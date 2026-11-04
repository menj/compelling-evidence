<?php
/**
 * Reading progress bar.
 *
 * The bar under the header on articles and posts existed since 2.6 as markup
 * in single.php and single-ce_article.php. Since 2.16.0 it is printed here,
 * on wp_body_open, and set from Theme Options > Engagement > Reading
 * progress bar: on or off, colour, height, top or bottom, and the post types
 * it shows on. Native replacement for the Reading Progress Bar plugin; the
 * theme bar steps aside while that plugin is active.
 *
 * The bar is aria-hidden. It repeats the scroll position the browser already
 * gives every reader, and a progressbar that changes on each scroll would
 * flood a screen reader. Articles keep the sidebar card, which is a labelled
 * role="progressbar" with aria values.
 *
 * @package CE_Theme
 * @since   2.16.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Post types the bar can show on: post type => default.
 */
function ce_rpb_post_types(): array {
	return [
		'ce_article'  => '1',
		'post'        => '1',
		'page'        => '1',
		'ce_question' => '0',
	];
}

/**
 * Whether the bar shows on the current request.
 */
function ce_rpb_applies(): bool {
	if ( '1' !== (string) get_option( 'ce_rpb_enabled', '1' ) || function_exists( 'rp_show_it' ) ) {
		return false;
	}
	if ( ! is_singular() || is_front_page() ) {
		return false;
	}
	$type  = get_post_type();
	$types = ce_rpb_post_types();
	if ( ! isset( $types[ $type ] ) || '1' !== (string) get_option( 'ce_rpb_on_' . $type, $types[ $type ] ) ) {
		return false;
	}
	// Quiz, journeys and other full-screen templates draw their own progress.
	if ( 'page' === $type && '' !== (string) get_page_template_slug() ) {
		return false;
	}
	return (bool) apply_filters( 'ce_rpb_applies', true );
}

function ce_rpb_enqueue(): void {
	if ( ! ce_rpb_applies() ) {
		return;
	}
	$uri = get_stylesheet_directory_uri();
	$ver = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'ce-reading-progress', $uri . '/assets/css/ce-reading-progress.css', [], $ver );
	wp_enqueue_script( 'ce-reading-progress', $uri . '/assets/js/ce-reading-progress.js', [], $ver, [ 'in_footer' => true, 'strategy' => 'defer' ] );
}
add_action( 'wp_enqueue_scripts', 'ce_rpb_enqueue', 20 );

/**
 * Print the bar. Colour and height go in as custom properties; a colour left
 * at its default keeps the teal gradient.
 */
function ce_rpb_render(): void {
	if ( ! ce_rpb_applies() ) {
		return;
	}
	$style  = '';
	$color  = sanitize_hex_color( (string) get_option( 'ce_rpb_color', '#0cd4e0' ) );
	$height = min( 8, max( 2, absint( get_option( 'ce_rpb_height', '3' ) ) ) );
	if ( $color && '#0cd4e0' !== strtolower( $color ) ) {
		$style .= '--ce-rpb-color:' . $color . ';';
	}
	if ( 3 !== $height ) {
		$style .= '--ce-rpb-height:' . $height . 'px;';
	}
	$pos = 'bottom' === get_option( 'ce_rpb_position', 'top' ) ? 'bottom' : 'top';
	echo '<div id="ce-reading-progress" class="ce-rpb ce-rpb--' . esc_attr( $pos ) . '" aria-hidden="true"' . ( $style ? ' style="' . esc_attr( $style ) . '"' : '' ) . '><div id="ce-reading-progress-fill"></div></div>';
}
add_action( 'wp_body_open', 'ce_rpb_render', 5 );
