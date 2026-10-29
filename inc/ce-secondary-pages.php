<?php
/**
 * Compelling Evidence — Secondary Pages
 * About, Editorial Policy, Privacy Policy, Contact
 * Auto-created on theme activation, from inc/data/seed-pages.json.
 *
 * @since 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Seed data for the pages the theme creates, from inc/data/seed-pages.json.
 *
 * Keys: pages (About, Editorial Policy, Privacy Policy, Contact, Ask a
 * Question), pages_v2 (FAQ, Glossary), journeys (path slug => title),
 * journey_pages, journey_parent (whose template the journey pages share),
 * placeholder_pages and question_statuses. The file is data: read with json_decode(), never
 * executed. A missing or malformed file gives empty lists, so nothing is
 * created from it.
 *
 * @param string $part Key in the file.
 * @return array
 */
function ce_seed_pages_data( $part ) {
    static $data = null;
    if ( null === $data ) {
        $file = get_stylesheet_directory() . '/inc/data/seed-pages.json';
        $data = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
        $data = is_array( $data ) ? $data : array();
    }
    return isset( $data[ $part ] ) && is_array( $data[ $part ] ) ? $data[ $part ] : array();
}

function ce_create_secondary_pages() {
    $pages = ce_seed_pages_data( 'pages' );

    foreach ( $pages as $page ) {
        // Skip if page already exists
        $existing = get_page_by_path( $page['slug'] );
        if ( $existing ) continue;

        $post_id = wp_insert_post([
            'post_title'   => $page['title'],
            'post_name'    => $page['slug'],
            'post_content' => $page['content'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ]);

        // Set page template if specified
        if ( $post_id && ! is_wp_error( $post_id ) && ! empty( $page['template'] ) ) {
            update_post_meta( $post_id, '_wp_page_template', $page['template'] );
        }
    }
}
/**
 * Run on every admin load, but only create pages once.
 * Uses an option flag so it doesn't re-run after pages exist.
 */
function ce_maybe_create_secondary_pages() {
    if ( get_option( 'ce_secondary_pages_created' ) ) return;
    if ( ! ce_seed_pages_data( 'pages' ) ) return; // Seed file unreadable: try again on the next load.
    ce_create_secondary_pages();
    update_option( 'ce_secondary_pages_created', '1' );
}
add_action( 'admin_init', 'ce_maybe_create_secondary_pages' );

/**
 * Create FAQ and Glossary pages (added in v2.2.51).
 * Uses its own flag so it runs once on upgrade.
 */
function ce_create_v2_pages() {
    if ( get_option( 'ce_v2_pages_created' ) ) return;

    $pages = ce_seed_pages_data( 'pages_v2' );
    if ( ! $pages ) return; // Seed file unreadable: try again on the next load.

    foreach ( $pages as $page ) {
        $existing = get_page_by_path( $page['slug'] );
        if ( $existing ) continue;

        $post_id = wp_insert_post([
            'post_title'   => $page['title'],
            'post_name'    => $page['slug'],
            'post_content' => $page['content'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ]);

        if ( $post_id && ! is_wp_error( $post_id ) && ! empty( $page['template'] ) ) {
            update_post_meta( $post_id, '_wp_page_template', $page['template'] );
        }
    }

    update_option( 'ce_v2_pages_created', '1' );
}
add_action( 'admin_init', 'ce_create_v2_pages' );

/**
 * Refresh the About page content on existing installs (added in v2.6.10).
 *
 * The auto-creation function above runs once and sets a flag. On sites where
 * the theme was activated before v2.6.10, the About page already exists in
 * the database with the older content, and updating this file alone does not
 * propagate to the existing post.
 *
 * This function uses its own flag (ce_about_v2_updated) so it runs exactly
 * once per site, locates the existing About page by slug, and refreshes its
 * content via wp_update_post. The flag prevents repeated overwrites if the
 * site administrator has further customised the About page after this
 * upgrade has run.
 */
function ce_update_about_page_v2() {
    if ( get_option( 'ce_about_v2_updated' ) ) return;

    $about = get_page_by_path( 'about-compelling-evidence' );
    if ( ! $about ) {
        // No existing About page — the auto-create function will handle it
        // on the next admin load with the new content.
        update_option( 'ce_about_v2_updated', '1' );
        return;
    }

    $new_content = '';
    foreach ( ce_seed_pages_data( 'pages' ) as $page ) {
        if ( 'about-compelling-evidence' === ( $page['slug'] ?? '' ) ) {
            $new_content = (string) $page['content'];
        }
    }
    if ( '' === $new_content ) {
        return; // The seed file is missing or damaged: try again on the next load.
    }

    wp_update_post([
        'ID'           => $about->ID,
        'post_content' => $new_content,
    ]);

    update_option( 'ce_about_v2_updated', '1' );
}
add_action( 'admin_init', 'ce_update_about_page_v2' );
