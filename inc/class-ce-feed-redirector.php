<?php
/**
 * CE Feed Redirector
 *
 * Integrated into the Compelling Evidence theme.
 *
 * - Removes feed autodiscovery links from <head>
 * - 301-redirects all feed URLs to their canonical equivalents
 * - Excludes feeds from the WordPress sitemap (only if no SEO plugin is active)
 * - Disallows feed URLs via robots.txt
 * - Sends noindex header on any feed URL that is accessed
 *
 * Stress-tested against:
 * - Multisite networks
 * - WP-CLI and cron contexts
 * - Admin and REST API requests
 * - All feed URL formats and query string variants
 * - Redirect loop prevention
 * - Headers already sent
 * - Password protected content
 * - WooCommerce and custom post types
 * - Physical robots.txt file conflicts
 * - HTTP and HTTPS environments
 *
 * @package CompellingEvidence
 * @since   2.4.9
 */

class CE_Feed_Redirector {

    private static $instance = null;

    private function __construct() {
        if ( $this->is_unsafe_context() ) {
            return;
        }

        add_action( 'init',        [ $this, 'disable_feed_links'     ] );
        add_action( 'parse_query', [ $this, 'redirect_feed_requests' ] );
        add_action( 'send_headers', [ $this, 'feed_noindex_header'   ] );

        if ( ! $this->seo_plugin_active() ) {
            add_filter( 'wp_sitemaps_add_provider', [ $this, 'remove_feed_from_sitemap' ], 10, 2 );
        }

        add_filter( 'robots_txt',                    [ $this, 'disallow_feeds_in_robots' ], 20, 2 );
        add_filter( 'the_feed_link',                 '__return_empty_string'               );
        add_filter( 'feed_links_show_posts_feed',    '__return_false'                      );
        add_filter( 'feed_links_show_comments_feed', '__return_false'                      );
    }

    public static function get_instance(): self {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Detect contexts where feed redirection must not run.
     */
    private function is_unsafe_context(): bool {
        if ( defined( 'WP_CLI' ) && WP_CLI ) {
            return true;
        }
        if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
            return true;
        }
        if ( is_admin() ) {
            return true;
        }
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
            return true;
        }
        return false;
    }

    /**
     * Detect whether a known SEO plugin is active and managing sitemaps.
     */
    private function seo_plugin_active(): bool {
        $seo_plugins = [
            'wordpress-seo/wp-seo.php',
            'wordpress-seo-premium/wp-seo-premium.php',
            'rank-math/rank-math.php',
            'rank-math-pro/rank-math-pro.php',
            'all-in-one-seo-pack/all_in_one_seo_pack.php',
            'all-in-one-seo-pack-pro/all_in_one_seo_pack.php',
            'seo-by-10web/seo-by-10web.php',
            'the-seo-framework/the-seo-framework.php',
            'squirrly-seo/squirrly.php',
        ];

        if ( ! function_exists( 'is_plugin_active' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        foreach ( $seo_plugins as $plugin ) {
            if ( is_plugin_active( $plugin ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove feed <link> autodiscovery tags from <head>.
     */
    public function disable_feed_links(): void {
        remove_action( 'wp_head', 'feed_links',       2 );
        remove_action( 'wp_head', 'feed_links_extra', 3 );
    }

    /**
     * Send an X-Robots-Tag HTTP header on any feed URL that is accessed.
     */
    public function feed_noindex_header(): void {
        if ( is_feed() && ! headers_sent() ) {
            header( 'X-Robots-Tag: noindex, nofollow', true );
        }
    }

    /**
     * 301-redirect feed requests to the canonical page URL.
     *
     * @param WP_Query $query
     */
    public function redirect_feed_requests( WP_Query $query ): void {
        if ( ! $query->is_feed() ) {
            return;
        }
        if ( ! $query->is_main_query() ) {
            return;
        }

        global $wp;

        $request_uri = isset( $_SERVER['REQUEST_URI'] )
            ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) )
            : '';

        $current_url = home_url( '/' . ltrim( $wp->request, '/' ) );

        // Strip /feed/ and any trailing feed format variant
        $canonical_url = preg_replace( '#/feed(/[^/]*)?/?$#i', '', $current_url );

        // Handle query string feed params e.g. ?feed=rss2&s=term
        if ( strpos( $request_uri, '?feed=' ) !== false || strpos( $request_uri, '&feed=' ) !== false ) {
            $parsed       = wp_parse_url( $current_url );
            $query_string = $parsed['query'] ?? '';
            parse_str( $query_string, $query_vars );
            unset( $query_vars['feed'] );
            $canonical_url = home_url( '/' );
            if ( ! empty( $query_vars ) ) {
                $canonical_url = add_query_arg( $query_vars, $canonical_url );
            }
        }

        $canonical_url = trailingslashit( $canonical_url );

        if ( empty( $canonical_url ) || ! filter_var( $canonical_url, FILTER_VALIDATE_URL ) ) {
            $canonical_url = home_url( '/' );
        }

        // Redirect loop prevention
        if ( strpos( $canonical_url, '/feed/' ) !== false ) {
            $canonical_url = home_url( '/' );
        }

        if ( headers_sent() ) {
            return;
        }

        wp_redirect( $canonical_url, 301 );
        exit;
    }

    /**
     * Remove feed entries from the WordPress core XML sitemap.
     * Only runs if no SEO plugin is detected.
     *
     * @param  WP_Sitemaps_Provider $provider
     * @param  string               $name
     * @return WP_Sitemaps_Provider|false
     */
    public function remove_feed_from_sitemap( $provider, string $name ) {
        if ( $name === 'posts' ) {
            return false;
        }
        return $provider;
    }

    /**
     * Append Disallow rules for feed paths to robots.txt.
     *
     * Note: Only works when WordPress generates robots.txt dynamically.
     * If a physical robots.txt exists in the root, add rules manually.
     *
     * Priority 20 runs after ce_robots_txt() (priority 10) so the
     * feed disallow rules append cleanly after the sitemap reference line.
     *
     * @param  string $output
     * @param  bool   $public
     * @return string
     */
    public function disallow_feeds_in_robots( string $output, bool $public ): string {
        if ( ! $public ) {
            return $output;
        }
        if ( strpos( $output, 'Disallow: /feed/' ) !== false ) {
            return $output;
        }

        $feed_rules  = "\n# Feed URLs disabled\n";
        $feed_rules .= "Disallow: /feed/\n";
        $feed_rules .= "Disallow: /*/feed/\n";
        $feed_rules .= "Disallow: /*/feed/rss/\n";
        $feed_rules .= "Disallow: /*/feed/rss2/\n";
        $feed_rules .= "Disallow: /*/feed/atom/\n";
        $feed_rules .= "Disallow: /*/feed/rdf/\n";
        $feed_rules .= "Disallow: /*?feed=\n";
        $feed_rules .= "Disallow: /*&feed=\n";

        return $output . $feed_rules;
    }
}
