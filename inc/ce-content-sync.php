<?php
/**
 * Compelling Evidence — Article Content Sync (JSON-Based)
 *
 * Syncs article content from secure JSON data files to the WordPress database.
 * Uses CE_Article_Loader for integrity-verified JSON loading instead of
 * PHP includes (which are vulnerable to code injection).
 *
 * Runs ONCE per theme version. The sync key is derived from the theme
 * version in style.css — every version bump automatically triggers a
 * fresh sync on the next admin page load.
 *
 * @since 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Topic migration map for retired/restructured categories
 * Articles assigned to old topics will be migrated to new topics during sync
 */
function ce_get_topic_migration_map(): array {
    return [
        // v2.3.1 retired topics → new homes (legacy safety net)
        'Examining the Quran'               => 'The Quran & Its Sources',
        'Examining the Sources'             => 'The Quran & Its Sources',
        'History & Context'                 => 'History, Context & Comparison',
        'The Bigger Picture'                => 'Revelation & Meaning',

        // v2.3.6 retired topics → new homes
        // 'Islamic Beliefs & Practice' historically mapped to 'Does God Exist?' here,
        // which contradicts the SSOT. The category was retired AND simultaneously a
        // new 'Islamic Practice & Ritual' category was introduced — articles in the
        // old name belong in the new ritual-specific category, not in the broad
        // theological one. SSOT § 5 (Deprecated topics) is authoritative.
        'Islamic Beliefs & Practice'        => 'Islamic Practice & Ritual',
        'Rights, Freedoms & Hard Questions' => 'Rights & Freedom',
    ];
}

/**
 * Slug rename map for articles that have been renamed
 * Maps old slugs → new slugs for in-place updates
 */
function ce_get_slug_rename_map(): array {
    return [
        // v2.3.6 slug rename
        'if-god-answers-prayer-why-cant-you-prove-it' => 'does-god-answer-prayer',
        // Add more renames here as needed
    ];
}

/**
 * Clean up deprecated topic terms that are no longer used
 */
function ce_cleanup_deprecated_topics() {
    $deprecated_topics = [
        'Islamic Beliefs & Practice',
        'Rights, Freedoms & Hard Questions',
    ];
    
    foreach ( $deprecated_topics as $topic_name ) {
        $term = term_exists( $topic_name, 'ce_topic' );
        if ( $term ) {
            $term_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
            $result = wp_delete_term( $term_id, 'ce_topic' );
            if ( is_wp_error( $result ) ) {
                error_log( 'CE: Failed to delete deprecated topic ' . $topic_name . ': ' . $result->get_error_message() );
            } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( 'CE: Deleted deprecated topic: ' . $topic_name );
            }
        }
    }
}

/**
 * Ensure all article topics exist in the ce_topic taxonomy
 * Auto-creates new topics for the restructured category system
 */
function ce_ensure_topics_exist() {
    $required_topics = [
        // Core topics
        'Does God Exist?'                   => 'does-god-exist',
        'Science & Evidence'                => 'science-evidence',
        'The Problem of Evil'               => 'the-problem-of-evil',
        'Ethics Without God?'               => 'ethics-without-god',
        'The Inner Journey'                 => 'the-inner-journey',
        'Rights & Freedom'                  => 'rights-freedom',
        
        // Extended topics
        'The Quran & Its Sources'           => 'quran-and-sources',
        'History, Context & Comparison'     => 'history-context-comparison',
        'Divine Justice & Fairness'         => 'divine-justice-fairness',
        'Islamic Practice & Ritual'         => 'islamic-practice-ritual',
        'Revelation & Meaning'                => 'revelation-meaning',
    ];
    
    foreach ( $required_topics as $name => $slug ) {
        $term = term_exists( $name, 'ce_topic' );
        if ( ! $term ) {
            $result = wp_insert_term( $name, 'ce_topic', [
                'slug'        => $slug,
                'description' => ''
            ]);
            
            if ( is_wp_error( $result ) ) {
                error_log( 'CE: Failed to create topic ' . $name . ': ' . $result->get_error_message() );
            } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( 'CE: Created topic: ' . $name );
            }
        }
    }
}

/**
 * Main content sync function.
 * Triggered on admin_init when the theme version changes.
 *
 * @param bool $force_run Skip the version-key and lock checks entirely.
 *                        Passed as true by ce_manual_content_sync() to
 *                        guarantee execution regardless of option-cache state.
 *                        On hosts with a persistent object cache (Redis /
 *                        Memcached), delete_option() clears the DB row but the
 *                        cached value can survive for the remainder of the
 *                        request, making the manual button a no-op without
 *                        this flag. The ?ce_force_sync GET param also sets it
 *                        for direct-URL access with manage_options capability.
 */
function ce_sync_article_content( bool $force_run = false ) {
    $theme_version = wp_get_theme()->get( 'Version' );
    $version_key   = 'ce_content_sync_' . str_replace( '.', '_', $theme_version );

    // URL-based force: ?ce_force_sync=1&_wpnonce=… — admin capability + nonce required
    // The nonce defends against CSRF (an attacker tricking an admin into clicking a link
    // that triggers a 5-minute DB lock and full sync). Generate the nonce with
    // wp_create_nonce( 'ce_force_sync' ) when building any UI that links to this URL.
    $url_force = (
        isset( $_GET['ce_force_sync'], $_GET['_wpnonce'] )
        && current_user_can( 'manage_options' )
        && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'ce_force_sync' )
    );

    // Either trigger bypasses the version check
    $force_sync = $force_run || $url_force;

    // Check if already synced this version
    $already_synced = get_option( $version_key );
    if ( $already_synced && ! $force_sync ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'CE: Sync skipped — version ' . $theme_version . ' already synced at ' . date( 'Y-m-d H:i:s', $already_synced ) );
        }
        return;
    }

    if ( $force_sync && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        $trigger = $force_run ? 'ce_manual_content_sync()' : '?ce_force_sync GET param';
        error_log( 'CE: Force sync triggered via ' . $trigger . ' for version ' . $theme_version );
    }
    
    // Ensure all topics exist before syncing
    ce_ensure_topics_exist();

    // Prevent timeouts on resource-constrained hosting (120 articles)
    if ( function_exists( 'set_time_limit' ) ) {
        set_time_limit( 300 ); // 5 minutes
    }
    if ( function_exists( 'ini_set' ) ) {
        ini_set( 'memory_limit', '256M' );
    }

    // Lock to prevent concurrent sync runs
    $lock_key = 'ce_content_sync_lock';
    $lock_active = get_transient( $lock_key );
    if ( $lock_active && ! $force_sync ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'CE: Sync skipped — lock active (expires in ' . $lock_active . 's)' );
        }
        return;
    }
    
    // Clear any stuck lock if force syncing
    if ( $force_sync && $lock_active ) {
        delete_transient( $lock_key );
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'CE: Cleared stuck lock for force sync' );
        }
    }
    
    set_transient( $lock_key, '1', 300 ); // 5 minute lock
    
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( 'CE: Starting content sync for version ' . $theme_version );
    }

    // Load the secure JSON article loader
    $loader_path = get_stylesheet_directory() . '/inc/class-ce-article-loader.php';
    if ( ! file_exists( $loader_path ) ) {
        error_log( 'CE: Article loader not found: ' . $loader_path );
        delete_transient( $lock_key );
        return;
    }
    
    require_once $loader_path;
    
    if ( ! class_exists( 'CE_Article_Loader' ) ) {
        error_log( 'CE: CE_Article_Loader class not found' );
        delete_transient( $lock_key );
        return;
    }

    // Load articles from JSON with integrity verification
    $loader = new CE_Article_Loader();
    $articles = $loader->load_all_articles();
    
    if ( $loader->has_errors() ) {
        foreach ( $loader->get_errors() as $error ) {
            error_log( 'CE Article Loader: ' . $error );
        }
    }
    
    if ( empty( $articles ) ) {
        error_log( 'CE: No articles loaded from JSON files' );
        delete_transient( $lock_key );
        return;
    }

    $synced = 0;
    $created = 0;
    $failed = 0;

    foreach ( $articles as $article ) {
        if ( empty( $article['slug'] ) ) {
            $failed++;
            continue;
        }

        // Find existing post by slug
        $existing = get_posts([
            'name'        => $article['slug'],
            'post_type'   => ['ce_article', 'post'],
            'post_status' => 'any',
            'numberposts' => 1,
        ]);

        // If not found, check if this is a renamed article (lookup by old slug)
        $was_renamed = false;
        if ( empty( $existing ) ) {
            $slug_map = ce_get_slug_rename_map();
            $old_slug = array_search( $article['slug'], $slug_map, true );
            if ( $old_slug !== false ) {
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    error_log( 'CE: Looking for renamed article: ' . $old_slug . ' -> ' . $article['slug'] );
                }
                $existing = get_posts([
                    'name'        => $old_slug,
                    'post_type'   => ['ce_article', 'post'],
                    'post_status' => 'any',
                    'numberposts' => 1,
                ]);
                if ( ! empty( $existing ) ) {
                    $was_renamed = true;
                    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                        error_log( 'CE: Found renamed article ID ' . $existing[0]->ID . ', will update to new slug' );
                    }
                }
            }
        }

        if ( ! empty( $existing ) ) {
            // UPDATE existing article content
            $update = [ 'ID' => $existing[0]->ID ];
            
            // If slug was renamed, update post_name to new slug
            if ( $was_renamed ) {
                $update['post_name'] = $article['slug'];
            }

            if ( ! empty( $article['content'] ) ) {
                $update['post_content'] = $article['content'];
            }
            if ( ! empty( $article['title'] ) ) {
                $update['post_title'] = $article['title'];
            }
            if ( ! empty( $article['excerpt'] ) ) {
                $update['post_excerpt'] = $article['excerpt'];
            }

            $result = wp_update_post( $update, true );
            if ( is_wp_error( $result ) ) {
                error_log( 'CE: Failed to update article ' . $article['slug'] . ': ' . $result->get_error_message() );
                $failed++;
            } else {
                $synced++;
                
                // Update topic if it has changed (with migration support for retired topics)
                $topic_name = $article['topic'] ?? '';
                
                // Check if this topic needs migration (retired topic)
                $migration_map = ce_get_topic_migration_map();
                if ( $topic_name && isset( $migration_map[ $topic_name ] ) ) {
                    $topic_name = $migration_map[ $topic_name ]; // Migrate to new topic
                }
                
                if ( $topic_name ) {
                    $term = term_exists( $topic_name, 'ce_topic' );
                    $topic_id = 0;
                    if ( $term ) {
                        $topic_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
                    } else {
                        // Auto-create topic if it doesn't exist.
                        // term_exists() can have a cache miss for terms that already exist
                        // mid-request (e.g. after ce_ensure_topics_exist() runs). When that
                        // happens wp_insert_term() returns WP_Error('term_exists') and includes
                        // the existing term_id in the error data. Extract it rather than
                        // silently returning 0 and skipping wp_set_object_terms() entirely.
                        $slug   = sanitize_title( $topic_name );
                        $result = wp_insert_term( $topic_name, 'ce_topic', [
                            'slug'        => $slug,
                            'description' => '',
                        ] );
                        if ( ! is_wp_error( $result ) ) {
                            $topic_id = (int) $result['term_id'];
                        } elseif ( $result->get_error_code() === 'term_exists' ) {
                            $existing_id = (int) $result->get_error_data( 'term_exists' );
                            if ( $existing_id > 0 ) {
                                $topic_id = $existing_id;
                                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                                    error_log( 'CE: term_exists cache miss recovered (update): ' . $topic_name . ' (ID ' . $topic_id . ')' );
                                }
                            }
                        }
                    }
                    if ( $topic_id ) {
                        wp_set_object_terms( $existing[0]->ID, [ $topic_id ], 'ce_topic' );
                    }
                }
            }
        } else {
            // CREATE new article
            $topic_name = $article['topic'] ?? '';
            
            // Check if this topic needs migration (retired topic)
            $migration_map = ce_get_topic_migration_map();
            if ( $topic_name && isset( $migration_map[ $topic_name ] ) ) {
                $topic_name = $migration_map[ $topic_name ]; // Migrate to new topic
            }
            
            $topic_id = 0;
            if ( $topic_name ) {
                $term = term_exists( $topic_name, 'ce_topic' );
                if ( $term ) {
                    $topic_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
                } else {
                    // Same cache-miss recovery as the update branch above.
                    $slug   = sanitize_title( $topic_name );
                    $result = wp_insert_term( $topic_name, 'ce_topic', [
                        'slug'        => $slug,
                        'description' => '',
                    ] );
                    if ( ! is_wp_error( $result ) ) {
                        $topic_id = (int) $result['term_id'];
                        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                            error_log( 'CE: Auto-created topic during sync: ' . $topic_name );
                        }
                    } elseif ( $result->get_error_code() === 'term_exists' ) {
                        $existing_id = (int) $result->get_error_data( 'term_exists' );
                        if ( $existing_id > 0 ) {
                            $topic_id = $existing_id;
                            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                                error_log( 'CE: term_exists cache miss recovered (create): ' . $topic_name . ' (ID ' . $topic_id . ')' );
                            }
                        }
                    }
                }
            }

            $post_id = wp_insert_post([
                'post_title'   => $article['title'] ?? $article['slug'],
                'post_name'    => $article['slug'],
                'post_content' => $article['content'] ?? '',
                'post_excerpt' => $article['excerpt'] ?? '',
                'post_status'  => 'publish',
                'post_type'    => 'ce_article',
                'menu_order'   => $article['order'] ?? 0,
            ], true);

            if ( is_wp_error( $post_id ) ) {
                error_log( 'CE: Failed to create article ' . $article['slug'] . ': ' . $post_id->get_error_message() );
                $failed++;
                continue;
            }

            // Assign topic
            if ( $topic_id ) {
                wp_set_object_terms( $post_id, [$topic_id], 'ce_topic' );
            }

            $created++;
        }
    }
    
    // Orphan detection: trash any ce_article posts whose slugs are not in JSON
    // This cleans up duplicates created by slug renames before the rename map existed
    // Note: Only NEW slugs are valid. Old slugs should have been renamed; any remaining
    // old-slug articles are duplicates that need to be trashed.
    $valid_slugs = array_column( $articles, 'slug' );

    // Safety (2.6.36): never trash anything when the article set is incomplete.
    // Before this guard, one batch failing its checksum dropped every article
    // in that batch from $articles, and the orphan pass then trashed them all.
    $orphan_pass_allowed = ! $loader->has_errors();
    if ( ! $orphan_pass_allowed ) {
        error_log( 'CE: Orphan check skipped — the article loader reported errors, so the JSON set may be incomplete.' );
    }

    $orphans = ! $orphan_pass_allowed ? [] : get_posts([
        'post_type'      => 'ce_article',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);
    
    $orphans_trashed = 0;
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( 'CE: Orphan check — Found ' . count( $orphans ) . ' articles in DB, ' . count( $valid_slugs ) . ' valid slugs in JSON' );
    }
    foreach ( $orphans as $orphan_id ) {
        $orphan_slug = get_post_field( 'post_name', $orphan_id );
        if ( ! in_array( $orphan_slug, $valid_slugs, true ) ) {
            wp_trash_post( $orphan_id );
            $orphans_trashed++;
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( 'CE: Trashed orphan article: ' . $orphan_slug );
            }
        }
    }

    // Clean up deprecated topic terms (restructured categories)
    ce_cleanup_deprecated_topics();

    // Mark this version as synced
    update_option( $version_key, time() );
    
    // Release lock
    delete_transient( $lock_key );

    // Log results
    do_action( 'ce_content_synced' );

    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( "CE: Content sync complete — Synced: {$synced}, Created: {$created}, Orphans trashed: {$orphans_trashed}, Failed: {$failed}, Total: " . count( $articles ) );
    }

    // Peak memory logging for diagnostics
    if ( function_exists( 'memory_get_peak_usage' ) ) {
        $peak_mb = round( memory_get_peak_usage() / 1024 / 1024, 2 );
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( "CE: Sync peak memory usage: {$peak_mb} MB" );
        }
    }
}

/**
 * Legacy article loading from PHP files
 * DEPRECATED: Use ce_load_articles_from_json() instead
 * 
 * @deprecated 2.3.0 Use JSON-based loading via CE_Article_Loader
 * @return array Empty array - JSON system is now required
 */
function ce_get_articles_data(): array {
    _deprecated_function( __FUNCTION__, '2.3.0', 'ce_load_articles_from_json()' );
    return [];
}

function ce_get_articles_data_2(): array {
    _deprecated_function( __FUNCTION__, '2.3.0', 'ce_load_articles_from_json()' );
    return [];
}

function ce_get_articles_data_3(): array {
    _deprecated_function( __FUNCTION__, '2.3.0', 'ce_load_articles_from_json()' );
    return [];
}

function ce_get_articles_data_4(): array {
    _deprecated_function( __FUNCTION__, '2.3.0', 'ce_load_articles_from_json()' );
    return [];
}

function ce_get_next_articles_data(): array {
    _deprecated_function( __FUNCTION__, '2.3.0', 'ce_load_articles_from_json()' );
    return [];
}

// Hook into admin_init for automatic sync
add_action( 'admin_init', 'ce_sync_article_content' );
