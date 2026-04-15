<?php
/**
 * Compelling Evidence — Migration v2.2.74
 *
 * Fixes two layout regressions introduced by the first working install
 * of the theme on Twenty Twenty-Five (FSE block theme):
 *
 * 1. The parent /journey/ page was created with no _wp_page_template assigned,
 *    allowing TT25's block template to intercept /journey/* requests and
 *    constrain content to TT25's default ~650px contentSize.
 *    Fix: assign page-journey.php to the parent page.
 *
 * 2. The child theme lacked theme.json, so TT25's contentSize/wideSize
 *    values were inherited for any page that touched the block system.
 *    Fix: theme.json added to child theme (no migration action needed —
 *    it takes effect automatically on file deploy).
 *
 * Runs ONCE on first admin_init after update to 2.2.74+.
 * Flagged by option 'ce_migration_2_2_74'.
 *
 * @since 2.2.74
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function ce_migrate_2_2_74() {
    if ( get_option( 'ce_migration_2_2_74' ) ) return;

    // ── 1. Fix parent /journey/ page template ─────────────────────────────────
    $journey_parent = get_page_by_path( 'journey' );
    if ( $journey_parent ) {
        $current_template = get_post_meta( $journey_parent->ID, '_wp_page_template', true );
        if ( $current_template !== 'page-journey.php' ) {
            update_post_meta( $journey_parent->ID, '_wp_page_template', 'page-journey.php' );
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( 'CE migration 2.2.74: assigned page-journey.php to /journey/ parent page (ID ' . $journey_parent->ID . ')' );
            }
        }
    }

    // ── 2. Ensure all 14 child journey pages have correct template ────────────
    // Belt-and-braces: fix any that were created before this was enforced.
    $journey_slugs = [
        'new-atheist', 'agnostic', 'secular-humanist', 'antitheist',
        'materialist', 'muslim-doubts', 'apatheist', 'deist',
        'scientist', 'classical-atheist', 'ex-believer',
        'spiritual-seeker', 'freethinker', 'true-muslim',
    ];

    foreach ( $journey_slugs as $slug ) {
        $page = get_page_by_path( 'journey/' . $slug );
        if ( ! $page ) continue;

        $tpl = get_post_meta( $page->ID, '_wp_page_template', true );
        if ( $tpl !== 'page-journey.php' ) {
            update_post_meta( $page->ID, '_wp_page_template', 'page-journey.php' );
        }

        // Also ensure _ce_journey_key is set (required for correct HTML file routing)
        $key = get_post_meta( $page->ID, '_ce_journey_key', true );
        if ( empty( $key ) ) {
            update_post_meta( $page->ID, '_ce_journey_key', $slug );
        }
    }

    // ── Flag complete ──────────────────────────────────────────────────────────
    update_option( 'ce_migration_2_2_74', '1' );

    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( 'CE migration 2.2.74: complete' );
    }
}
add_action( 'admin_init', 'ce_migrate_2_2_74' );
