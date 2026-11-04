<?php
/**
 * Pretty search URLs: /?s=term becomes /search/term/.
 *
 * Native replacement for the "Pretty Search Permalinks" plugin (wp-seo-search
 * 1.3), with three corrections: terms are encoded with rawurlencode(), so a
 * space becomes %20 and never a literal plus sign inside a path; empty
 * searches are left alone instead of redirecting to a bare /search/; and the
 * redirect is a 301 so browsers and crawlers settle on one URL.
 *
 * Since 2.16.0: the base can also be set in Settings > Permalinks, as the
 * plugin allowed; ?s=term&paged=2 keeps its page (/search/term/page/2/);
 * and rewrite rules are flushed once when the theme version changes.
 *
 * Steps aside when the plugin is active, so the two never double-redirect.
 *
 * @package CE_Theme
 * @since   2.6.33
 */

defined( 'ABSPATH' ) || exit;

function ce_pretty_search_active(): bool {
	return get_option( 'ce_pretty_search', '1' ) === '1' && ! function_exists( 'wpseosearch_rewrite' );
}

/**
 * Slugs the theme's own rewrite rules use; a search base may not take one.
 */
function ce_search_reserved_bases(): array {
	return (array) apply_filters( 'ce_search_reserved_bases', [ 'articles', 'topic', 'qa', 'journey', 'journeys', 'quiz', 'my-progress', 'page', 'feed', 'comments', 'wp-json', 'wp-admin', 'wp-content', 'wp-includes', 'wp-sitemap', 'image-sitemap', 'category', 'tag', 'author', 'embed', 'trackback' ] );
}

/**
 * A usable base from raw input: sanitize_key() (lowercase letters, digits,
 * hyphens and underscores), and never a reserved slug.
 */
function ce_search_clean_base( $raw ): string {
	$base = sanitize_key( is_scalar( $raw ) ? (string) $raw : '' );
	return ( '' === $base || in_array( $base, ce_search_reserved_bases(), true ) ) ? '' : $base;
}

function ce_search_base(): string {
	// A base saved by the plugin is the fallback, so its URLs survive the switch.
	$base = ce_search_clean_base( get_option( 'ce_search_base', get_option( 'wpseosearch_base', 'search' ) ) );
	return '' !== $base ? $base : 'search';
}

/**
 * Tell WordPress which segment carries search terms.
 */
function ce_set_search_base(): void {
	if ( ! ce_pretty_search_active() ) {
		return;
	}
	global $wp_rewrite;
	if ( $wp_rewrite instanceof WP_Rewrite ) {
		$wp_rewrite->search_base = ce_search_base();
	}
}
add_action( 'init', 'ce_set_search_base', 1 );

/**
 * 301 from the query-string form to the path form.
 */
function ce_pretty_search_redirect(): void {
	global $wp_rewrite;
	if ( ! ce_pretty_search_active() || ! is_search() || is_admin() || ! $wp_rewrite->using_permalinks() ) {
		return;
	}
	$term = get_query_var( 's' );
	if ( ! is_string( $term ) || '' === trim( $term ) ) {
		return;
	}
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path = (string) wp_parse_url( $uri, PHP_URL_PATH );
	if ( false !== strpos( $path, '/' . ce_search_base() . '/' ) ) {
		return;
	}
	$paged = max( 1, (int) get_query_var( 'paged' ) );
	$tail  = $paged > 1 ? 'page/' . $paged . '/' : '';
	$url   = home_url( '/' . ce_search_base() . '/' . rawurlencode( $term ) . '/' . $tail );
	// Every other argument stays, arrays such as post_type[] included.
	$args = wp_unslash( $_GET ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- carried over to the new address, encoded below.
	unset( $args['s'], $args['paged'] );
	if ( $args ) {
		$url .= '?' . http_build_query( $args, '', '&', PHP_QUERY_RFC3986 );
	}
	wp_safe_redirect( $url, 301 );
	exit;
}
add_action( 'template_redirect', 'ce_pretty_search_redirect', 5 );

/**
 * Flush rewrite rules once when the base changes.
 */
function ce_search_base_changed( $old, $new ): void {
	if ( $old !== $new ) {
		update_option( 'ce_flush_rewrites', 1 );
	}
}
add_action( 'update_option_ce_search_base', 'ce_search_base_changed', 10, 2 );
add_action( 'update_option_ce_pretty_search', 'ce_search_base_changed', 10, 2 );

function ce_maybe_flush_rewrites(): void {
	if ( get_option( 'ce_flush_rewrites' ) ) {
		delete_option( 'ce_flush_rewrites' );
		flush_rewrite_rules( false );
	}
}
add_action( 'init', 'ce_maybe_flush_rewrites', 99 );

/**
 * Flush once per theme version, so the search rules exist after an update.
 * Never on an ordinary load.
 *
 * @since 2.16.0
 */
function ce_search_flush_on_version(): void {
	global $wp_rewrite;
	$version = (string) wp_get_theme()->get( 'Version' );
	if ( get_option( 'ce_rewrite_version' ) === $version ) {
		return;
	}
	// Plain permalinks keep no rules, so there is nothing to wait for.
	if ( ! ( $wp_rewrite instanceof WP_Rewrite ) || ! $wp_rewrite->using_permalinks() ) {
		update_option( 'ce_rewrite_version', $version );
		return;
	}
	flush_rewrite_rules( false );
	// The marker is written only once the rules hold the search rule, so a
	// flush that failed is tried again on the next request.
	if ( ce_search_rules_ready() ) {
		update_option( 'ce_rewrite_version', $version );
	}
}
add_action( 'init', 'ce_search_flush_on_version', 99 );

/**
 * Whether the stored rules contain the search rule for the current base
 * (or pretty search is off, when no rule is needed).
 */
function ce_search_rules_ready(): bool {
	if ( ! ce_pretty_search_active() ) {
		return true;
	}
	$prefix = ce_search_base() . '/(.+)';
	foreach ( array_keys( (array) get_option( 'rewrite_rules', [] ) ) as $rule ) {
		if ( 0 === strpos( (string) $rule, $prefix ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Search base field in Settings > Permalinks > Optional, as the plugin had.
 * It edits the same option as Theme Options > Links & Tooltips.
 *
 * @since 2.16.0
 */
function ce_search_permalink_settings(): void {
	if ( ! current_user_can( 'manage_options' ) || function_exists( 'wpseosearch_rewrite' ) ) {
		return;
	}
	if ( isset( $_POST['ce_search_base'] ) ) {
		check_admin_referer( 'update-permalink' );
		$base = ce_search_clean_base( wp_unslash( $_POST['ce_search_base'] ) );
		update_option( 'ce_search_base', '' !== $base ? $base : 'search' );
		ce_set_search_base();
	}
	add_settings_field( 'ce_search_base', __( 'Search base', 'compelling-evidence' ), 'ce_search_permalink_input', 'permalink', 'optional' );
}
add_action( 'load-options-permalink.php', 'ce_search_permalink_settings' );

function ce_search_permalink_input(): void {
	echo '<input type="text" name="ce_search_base" id="ce_search_base" class="regular-text code" value="' . esc_attr( ce_search_base() ) . '">';
	echo '<p class="description">' . esc_html__( 'Search results open at /search-base/term/. Lowercase letters, digits, hyphens and underscores; a slug the theme already uses, such as articles or topic, is refused. Also set in CE Theme Options, Links & Tooltips.', 'compelling-evidence' ) . '</p>';
}
