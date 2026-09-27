<?php
/**
 * CE Theme: working alongside the Twenty Twenty-Five parent.
 *
 * CE Theme renders the front end with its own PHP templates. The parent is
 * a block theme, and WordPress gives a block template priority over a PHP
 * template at the same level of the template hierarchy. Without the filter
 * below, the parent's index, home, single, page, search, archive and 404
 * block templates would silently replace CE's templates for those views.
 *
 * Rules:
 *   1. On the front end, block templates that come from theme files are set
 *      aside, so CE's PHP templates render. CE ships no block templates of
 *      its own, so every theme-file block template here is the parent's.
 *   2. Templates saved in the Site Editor (source "custom") are kept. If a
 *      site owner deliberately customises a template there, that choice wins.
 *   3. The Site Editor, the block editor and the REST API are untouched, so
 *      the parent's templates, patterns and styles stay available there.
 *
 * Tested against Twenty Twenty-Five 1.5 (requires WordPress 6.7).
 *
 * @since 2.6.28
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Parent version CE Theme was last tested against. */
const CE_PARENT_TESTED_VERSION = '1.5';

/**
 * Let CE's PHP templates win over the parent's file-based block templates
 * on the front end.
 *
 * @param WP_Block_Template[] $templates     Templates found for the query.
 * @param array               $query         Query arguments.
 * @param string              $template_type wp_template or wp_template_part.
 * @return WP_Block_Template[]
 */
function ce_prefer_child_php_templates( $templates, $query, $template_type ) {
    if ( 'wp_template' !== $template_type || is_admin() || wp_is_serving_rest_request() ) {
        return $templates;
    }
    return array_values( array_filter( $templates, static function ( $template ) {
        return 'theme' !== $template->source;
    } ) );
}
add_filter( 'get_block_templates', 'ce_prefer_child_php_templates', 10, 3 );

/**
 * Admin notice when the parent is missing, is not Twenty Twenty-Five, or has
 * moved to a major version CE Theme has not been tested against.
 */
function ce_parent_theme_notice() {
    if ( ! current_user_can( 'switch_themes' ) ) {
        return;
    }
    $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
    if ( $screen && ! in_array( $screen->id, [ 'themes', 'dashboard', 'appearance_page_ce-theme-options' ], true ) ) {
        return;
    }
    $parent = wp_get_theme()->parent();
    if ( ! $parent || 'twentytwentyfive' !== $parent->get_stylesheet() ) {
        $message = 'CE Theme is a child of Twenty Twenty-Five. Install and keep Twenty Twenty-Five in wp-content/themes/twentytwentyfive.';
    } elseif ( (int) $parent->get( 'Version' ) > (int) CE_PARENT_TESTED_VERSION ) {
        $message = sprintf(
            'Twenty Twenty-Five %1$s is installed. CE Theme was last tested with %2$s. Check the front page, an article and a plain page before relying on this parent version.',
            $parent->get( 'Version' ),
            CE_PARENT_TESTED_VERSION
        );
    } else {
        return;
    }
    echo '<div class="notice notice-warning"><p>' . esc_html( $message ) . '</p></div>';
}
add_action( 'admin_notices', 'ce_parent_theme_notice' );
