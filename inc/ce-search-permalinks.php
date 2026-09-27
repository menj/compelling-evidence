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
 * Steps aside when the plugin is active, so the two never double-redirect.
 *
 * @package CE_Theme
 * @since   2.6.33
 */

defined( 'ABSPATH' ) || exit;

function ce_pretty_search_active(): bool {
	return get_option( 'ce_pretty_search', '1' ) === '1' && ! function_exists( 'wpseosearch_rewrite' );
}

function ce_search_base(): string {
	$base = sanitize_title( (string) get_option( 'ce_search_base', 'search' ) );
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
	wp_safe_redirect( home_url( '/' . ce_search_base() . '/' . rawurlencode( $term ) . '/' ), 301 );
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
