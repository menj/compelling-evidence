<?php
/**
 * Presentation-level integration with plugins that should stay plugins.
 *
 * Contact Form 7 and its companion Contact Form CFDB7 hold forms and stored
 * messages; WPS Hide Login guards the login URL. Moving any of them into the
 * theme would tie data or site security to the active theme, so they remain
 * plugins. The theme makes them look and load as if they were native:
 *
 *  - CF7 scripts and styles load only on pages that contain a form
 *    (the plugin loads them on every page by default).
 *  - CF7 forms take the theme's form design, colour tokens and fonts
 *    from assets/css/ce-cf7.css.
 *
 * @package CE_Theme
 * @since   2.6.33
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the current singular content contains a Contact Form 7 form.
 */
function ce_page_has_cf7(): bool {
	if ( ! is_singular() ) {
		return false;
	}
	$post = get_post();
	return $post && ( has_shortcode( $post->post_content, 'contact-form-7' ) || has_shortcode( $post->post_content, 'contact-form' ) || has_block( 'contact-form-7/contact-form-selector', $post ) );
}

/**
 * Load CF7 assets only where a form appears.
 */
function ce_cf7_conditional_assets(): void {
	if ( ! defined( 'WPCF7_VERSION' ) ) {
		return;
	}
	add_filter( 'wpcf7_load_js', 'ce_page_has_cf7' );
	add_filter( 'wpcf7_load_css', '__return_false' ); // The theme stylesheet below replaces it.
}
add_action( 'wp', 'ce_cf7_conditional_assets' );

function ce_cf7_styles(): void {
	if ( defined( 'WPCF7_VERSION' ) && ce_page_has_cf7() ) {
		wp_enqueue_style( 'ce-cf7', get_stylesheet_directory_uri() . '/assets/css/ce-cf7.css', [ 'ce-main' ], wp_get_theme()->get( 'Version' ) );
	}
}
add_action( 'wp_enqueue_scripts', 'ce_cf7_styles', 20 );
