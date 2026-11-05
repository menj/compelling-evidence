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
    return implode( "\n", ce_gone_default_list() );
}

/**
 * The default prefixes as a list. The first three are the spam injection's;
 * the rest are the previous owner's shop, which the server logs show
 * crawlers still request (2.18.6).
 *
 * @return string[]
 */
function ce_gone_default_list(): array {
    return [
        '/shop/manufacturer-site',
        '/product-similar-image',
        '/product/category/',
        '/f/',
        '/etc/',
        '/guide/knowledge/',
        '/odr/',
        '/ctg/',
        '/mbr/',
        '/iqr/',
    ];
}

/**
 * Adds default prefixes that are new since the list was last saved, once
 * each. A saved list keeps its own lines, and a default the owner deleted
 * after it was added is not brought back.
 */
function ce_gone_merge_defaults() {
    $seen = get_option( 'ce_gone_defaults_seen', null );
    $raw  = get_option( 'ce_gone_paths', null );
    if ( null === $raw ) {
        // Never saved: the defaults already apply.
        update_option( 'ce_gone_defaults_seen', ce_gone_default_list(), false );
        return;
    }
    // Before 2.18.6 nothing was recorded; the first three defaults were the list then.
    $seen  = is_array( $seen ) ? $seen : array_slice( ce_gone_default_list(), 0, 3 );
    $lines = array_map( 'strtolower', array_map( 'trim', preg_split( '/\R/', (string) $raw ) ) );
    $add   = [];
    foreach ( ce_gone_default_list() as $prefix ) {
        if ( ! in_array( $prefix, $seen, true ) && ! in_array( $prefix, $lines, true ) ) {
            $add[] = $prefix;
        }
    }
    if ( $add ) {
        update_option( 'ce_gone_paths', rtrim( (string) $raw ) . "\n" . implode( "\n", $add ) );
    }
    if ( $seen !== ce_gone_default_list() ) {
        update_option( 'ce_gone_defaults_seen', ce_gone_default_list(), false );
    }
}
add_action( 'admin_init', 'ce_gone_merge_defaults' );

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
