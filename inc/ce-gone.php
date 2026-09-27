<?php
/**
 * Compelling Evidence — 410 Gone for retired URL patterns.
 *
 * Search engines keep requesting URLs that were injected into the site by a
 * past SEO-spam compromise (e.g. /shop/manufacturer-site, /product/category/…,
 * /product-similar-image/). Each request rendered the full 404 template, and
 * each 404 fired Rank Math's 404 monitor. A 410 response, sent at init before
 * the main query runs, is cheaper and tells crawlers to drop the URL.
 *
 * Patterns are path prefixes, one per line, set under
 * Theme Options → Performance → Retired URLs.
 *
 * @since 2.6.21
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Default retired path prefixes.
 */
function ce_gone_default_paths(): string {
    return "/shop/manufacturer-site\n/product-similar-image\n/product/category/";
}

/**
 * Parse the saved option into a list of normalised path prefixes.
 *
 * @return string[]
 */
function ce_gone_paths(): array {
    $raw   = (string) get_option( 'ce_gone_paths', ce_gone_default_paths() );
    $paths = [];
    foreach ( preg_split( '/\R/', $raw ) as $line ) {
        $line = strtolower( trim( $line ) );
        if ( $line === '' || $line === '/' ) continue; // Never retire the whole site.
        $paths[] = '/' . ltrim( $line, '/' );
    }
    return $paths;
}

/**
 * Send 410 Gone for requests whose path starts with a retired prefix.
 */
function ce_maybe_send_gone() {
    if ( get_option( 'ce_gone_enabled', '1' ) !== '1' ) return;
    if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) return;
    if ( defined( 'REST_REQUEST' ) || defined( 'WP_CLI' ) ) return;

    $uri  = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
    $path = strtolower( (string) wp_parse_url( $uri, PHP_URL_PATH ) );
    if ( $path === '' ) return;

    foreach ( ce_gone_paths() as $prefix ) {
        if ( strpos( $path, $prefix ) === 0 ) {
            status_header( 410 );
            nocache_headers();
            header( 'X-Robots-Tag: noindex, nofollow', true );
            header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
            echo '<!doctype html><title>410 Gone</title><p>This page has been permanently removed.</p>';
            exit;
        }
    }
}
add_action( 'init', 'ce_maybe_send_gone', 0 );
