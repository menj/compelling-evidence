<?php
/**
 * Compelling Evidence — Article Content Sync
 *
 * Syncs article content from theme data files to the WordPress database.
 * Runs ONCE per theme version. The sync key is derived from the theme
 * version in style.css — every version bump automatically triggers a
 * fresh sync on the next admin page load. No manual key management needed.
 *
 * @since 2.2.8
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function ce_sync_article_content() {
    $theme_version = wp_get_theme()->get( 'Version' );
    $version_key   = 'ce_content_sync_' . str_replace( '.', '_', $theme_version );
    if ( get_option( $version_key ) ) return;

    $data_map = [
        'ce_get_articles_data'       => 'articles-data.php',
        'ce_get_articles_data_2'     => 'articles-data-2.php',
        'ce_get_articles_data_3'     => 'articles-data-3.php',
        'ce_get_articles_data_4'     => 'articles-data-4.php',
        'ce_get_next_articles_data'  => 'articles-data-next.php',
    ];

    $synced = 0;
    $created = 0;

    foreach ( $data_map as $func => $file ) {
        $path = get_stylesheet_directory() . '/inc/' . $file;
        if ( ! file_exists( $path ) ) continue;

        if ( ! function_exists( $func ) ) {
            require_once $path;
        }
        if ( ! function_exists( $func ) ) continue;

        $articles_data = $func();

        foreach ( $articles_data as $article ) {
            if ( empty( $article['slug'] ) ) continue;

            // Find existing post by slug
            $existing = get_posts([
                'name'        => $article['slug'],
                'post_type'   => ['ce_article', 'post'],
                'post_status' => 'any',
                'numberposts' => 1,
            ]);

            if ( ! empty( $existing ) ) {
                // UPDATE existing article content
                $update = [ 'ID' => $existing[0]->ID ];

                if ( ! empty( $article['content'] ) ) {
                    $update['post_content'] = $article['content'];
                }
                if ( ! empty( $article['title'] ) ) {
                    $update['post_title'] = $article['title'];
                }
                if ( ! empty( $article['excerpt'] ) ) {
                    $update['post_excerpt'] = $article['excerpt'];
                }

                wp_update_post( $update );
                $synced++;
            } else {
                // CREATE new article (for any articles added in this version)
                $topic_name = $article['topic'] ?? '';
                $topic_id = 0;
                if ( $topic_name ) {
                    $term = term_exists( $topic_name, 'ce_topic' );
                    if ( $term ) {
                        $topic_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
                    }
                }

                $post_id = wp_insert_post([
                    'post_title'   => $article['title'] ?? $article['slug'],
                    'post_name'    => $article['slug'],
                    'post_content' => $article['content'] ?? '',
                    'post_excerpt' => $article['excerpt'] ?? '',
                    'post_status'  => 'publish',
                    'post_type'    => 'ce_article',
                    'menu_order'   => $article['order'] ?? 99,
                ]);

                if ( $post_id && ! is_wp_error( $post_id ) && $topic_id ) {
                    wp_set_post_terms( $post_id, [ $topic_id ], 'ce_topic' );
                }
                $created++;
            }
        }
    }

    update_option( $version_key, '1' );

    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( "CE: Content sync complete — {$synced} updated, {$created} created" );
    }
}
add_action( 'admin_init', 'ce_sync_article_content' );
