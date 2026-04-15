<?php
/**
 * Compelling Evidence — Category Migration (v2.2.0)
 *
 * Migrates articles from old taxonomy terms to new ones.
 * Runs ONCE on first admin page load after theme update.
 * Flagged by option 'ce_category_migration_2_2_0'.
 *
 * What it does:
 * 1. Creates all new taxonomy terms if they don't exist
 * 2. Remaps every article from old term to new term
 * 3. Deletes old (now empty) terms
 * 4. Reassigns articles that had no topic (bridge articles) 
 * 5. Handles the slug rename: purpose-of-life-islam → purpose-of-life
 *
 * @since 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function ce_migrate_categories_2_2_0() {
    // Only run once
    if ( get_option( 'ce_category_migration_2_2_0' ) ) return;

    $taxonomy = 'ce_topic';

    // ═══════════════════════════════════════════════
    // 1. TERM MAPPING: old name → new name
    // ═══════════════════════════════════════════════
    $term_rename = [
        'Existence of God'    => 'Does God Exist?',
        'Evil & Suffering'    => 'The Problem of Evil',
        'Ethics & Morality'   => 'Ethics Without God?',
        'Science & Faith'     => 'Science & Evidence',
        'Consciousness'       => 'Science & Evidence',      // merged
        'The Quran'           => 'Examining the Quran',
        'Science & Islam'     => 'History & Context',        // merged
        'Purpose & Meaning'   => 'The Bigger Picture',       // merged
        'The Next Question'   => 'The Bigger Picture',       // merged
        'Doubts & Questions'  => null,                       // redistributed per-article
        'Objections'          => null,                       // redistributed per-article
        'Hard Questions'      => null,                       // redistributed per-article
    ];

    // ═══════════════════════════════════════════════
    // 2. NEW TERMS: create if they don't exist
    // ═══════════════════════════════════════════════
    $new_terms = [
        'Does God Exist?'          => 'does-god-exist',
        'The Problem of Evil'      => 'the-problem-of-evil',
        'Ethics Without God?'      => 'ethics-without-god',
        'Science & Evidence'       => 'science-evidence',
        'Examining the Quran'      => 'examining-the-quran',
        'Examining the Sources'    => 'examining-the-sources',
        'History & Context'        => 'history-context',
        'Rights & Freedom'         => 'rights-freedom',
        'The Inner Journey'        => 'the-inner-journey',
        'The Bigger Picture'       => 'the-bigger-picture',
    ];

    $term_ids = [];
    foreach ( $new_terms as $name => $slug ) {
        $existing = term_exists( $name, $taxonomy );
        if ( $existing ) {
            $term_ids[ $name ] = is_array( $existing ) ? (int) $existing['term_id'] : (int) $existing;
        } else {
            $result = wp_insert_term( $name, $taxonomy, [ 'slug' => $slug ] );
            if ( ! is_wp_error( $result ) ) {
                $term_ids[ $name ] = (int) $result['term_id'];
            }
        }
    }

    // ═══════════════════════════════════════════════
    // 3. PER-ARTICLE REMAP (for redistributed categories)
    // ═══════════════════════════════════════════════
    // Articles from "Objections", "Doubts & Questions", "Hard Questions"
    // need individual reassignment based on their slug.

    $article_remap = [
        // From Objections → various new cats
        'why-does-god-need-compelled-worship' => 'Does God Exist?',
        'divine-hiddenness'                   => 'Does God Exist?',
        'god-as-psychological-projection'     => 'Does God Exist?',
        'burden-of-proof'                     => 'Does God Exist?',
        'if-god-answers-prayer-why-cant-you-prove-it' => 'Does God Exist?',
        'why-hellfire'                        => 'The Problem of Evil',
        'finite-sins-infinite-punishment'     => 'The Problem of Evil',
        'unanswered-prayer'                   => 'The Problem of Evil',
        'universal-salvation'                 => 'The Problem of Evil',
        'why-create-knowing-suffering'        => 'The Problem of Evil',
        'euthyphro-dilemma'                   => 'Ethics Without God?',
        'religious-experience-neuroscience'   => 'Science & Evidence',
        'god-of-gaps'                         => 'Science & Evidence',
        'inconsistent-revelations'            => 'Examining the Sources',
        'did-islam-spread-by-the-sword'       => 'Examining the Sources',
        'religion-is-political-control'       => 'History & Context',
        'religion-cause-harm'                 => 'History & Context',
        'islam-and-enlightenment'             => 'History & Context',
        'honour-killings-culture-not-islam'   => 'History & Context',
        'apostasy-and-freedom'                => 'Rights & Freedom',
        'do-good-non-muslims-go-to-hell'      => 'Rights & Freedom',
        'islam-and-same-sex-attraction'       => 'Rights & Freedom',
        'islam-modernise'                     => 'Rights & Freedom',
        'why-islam-not-christianity'          => 'The Bigger Picture',
        'why-humans-believe-in-god'           => 'The Bigger Picture',

        // From Doubts & Questions → various new cats
        'free-will-predestination'            => 'Does God Exist?',
        'shirk-unforgivable'                  => 'The Bigger Picture',
        'mercy-harsh-passages'                => 'Examining the Quran',
        'meccan-medinan-abrogation'           => 'Examining the Quran',
        'reading-the-quran-for-the-first-time'=> 'Examining the Quran',
        'quran-in-arabic'                     => 'Examining the Quran',
        'hadith-reliability'                  => 'Examining the Sources',
        'kill-him-who-changes-religion'       => 'Examining the Sources',
        'aisha-age-marriage'                  => 'Examining the Sources',
        'banu-qurayza-early-violence'         => 'Examining the Sources',
        'gharaniq-satanic-verses'             => 'Examining the Sources',
        'the-islam-i-was-defending'           => 'History & Context',
        'the-freethinkers-islam-produced'     => 'History & Context',
        'no-compulsion-in-religion'           => 'Rights & Freedom',
        'women-in-islam'                      => 'Rights & Freedom',
        'slavery-in-islamic-sources'          => 'Rights & Freedom',
        'apostasy-political-history'          => 'Rights & Freedom',
        'apostasy-international-law'          => 'Rights & Freedom',
        'post-muslim-identity'                => 'Rights & Freedom',
        'doubt-permitted-in-islam'            => 'The Bigger Picture',

        // From Doubts & Questions → The Inner Journey
        'your-doubts-are-not-a-disease'       => 'The Inner Journey',
        'faith-was-just-conditioning'         => 'The Inner Journey',
        'the-good-muslim-paradox'             => 'The Inner Journey',
        'when-the-presence-fades'             => 'The Inner Journey',
        'left-because-of-specific-problems'   => 'The Inner Journey',
        'did-your-heart-leave-before-your-head'=> 'The Inner Journey',
        'the-dual-life'                       => 'The Inner Journey',
        'anger-at-religion'                   => 'The Inner Journey',
        'the-anger-is-real'                   => 'The Inner Journey',
        'religious-trauma'                    => 'The Inner Journey',
        'when-religion-was-imposed-not-discovered' => 'The Inner Journey',
        'social-cost-of-leaving'              => 'The Inner Journey',
        'how-muslims-leave-the-sociology'     => 'The Inner Journey',
        'scale-of-leaving'                    => 'The Inner Journey',
        'practising-without-belief'           => 'The Inner Journey',
        'the-algorithm-that-deconverted-you'  => 'The Inner Journey',

        // Bridge articles (from articles-data-next.php)
        'does-god-communicate-with-humanity'  => 'The Bigger Picture',
        'what-would-authentic-revelation-look-like' => 'The Bigger Picture',
        'how-do-we-evaluate-competing-claims-to-revelation' => 'The Bigger Picture',
    ];

    // ═══════════════════════════════════════════════
    // 4. EXECUTE: Simple renames first
    // ═══════════════════════════════════════════════

    foreach ( $term_rename as $old_name => $new_name ) {
        if ( $new_name === null ) continue; // redistributed per-article, handled below

        $old_term = term_exists( $old_name, $taxonomy );
        if ( ! $old_term ) continue;

        $old_id = is_array( $old_term ) ? (int) $old_term['term_id'] : (int) $old_term;
        $new_id = $term_ids[ $new_name ] ?? null;
        if ( ! $new_id ) continue;

        // Get all articles assigned to the old term
        $articles = get_posts([
            'post_type'      => [ 'ce_article', 'post' ],
            'posts_per_page' => -1,
            'tax_query'      => [[
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => [ $old_id ],
            ]],
        ]);

        foreach ( $articles as $article ) {
            // Check if this article has a per-article override
            if ( isset( $article_remap[ $article->post_name ] ) ) {
                $override_cat = $article_remap[ $article->post_name ];
                $override_id  = $term_ids[ $override_cat ] ?? null;
                if ( $override_id ) {
                    wp_set_post_terms( $article->ID, [ $override_id ], $taxonomy );
                    continue;
                }
            }
            // Default: move to the new term
            wp_set_post_terms( $article->ID, [ $new_id ], $taxonomy );
        }
    }

    // ═══════════════════════════════════════════════
    // 5. EXECUTE: Per-article remaps (for redistributed cats)
    // ═══════════════════════════════════════════════

    foreach ( $article_remap as $slug => $new_cat ) {
        $new_id = $term_ids[ $new_cat ] ?? null;
        if ( ! $new_id ) continue;

        $article = get_page_by_path( $slug, OBJECT, 'ce_article' );
        if ( ! $article ) {
            // Try regular post
            $article = get_page_by_path( $slug, OBJECT, 'post' );
        }
        if ( ! $article ) continue;

        wp_set_post_terms( $article->ID, [ $new_id ], $taxonomy );
    }

    // ═══════════════════════════════════════════════
    // 6. SLUG RENAME: purpose-of-life-islam → purpose-of-life
    // ═══════════════════════════════════════════════

    $old_article = get_page_by_path( 'purpose-of-life-islam', OBJECT, 'ce_article' );
    if ( $old_article ) {
        wp_update_post([
            'ID'        => $old_article->ID,
            'post_name' => 'purpose-of-life',
        ]);
    }

    // ═══════════════════════════════════════════════
    // 7. CLEANUP: Delete old empty terms
    // ═══════════════════════════════════════════════

    $old_terms_to_delete = [
        'Existence of God', 'Consciousness', 'Ethics & Morality',
        'Evil & Suffering', 'Science & Faith', 'Science & Islam',
        'The Quran', 'Purpose & Meaning', 'Objections',
        'Doubts & Questions', 'Hard Questions', 'The Next Question',
    ];

    foreach ( $old_terms_to_delete as $old_name ) {
        $term = term_exists( $old_name, $taxonomy );
        if ( $term ) {
            $tid = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
            // Only delete if no articles remain assigned
            $count = wp_count_terms( $taxonomy, [ 'parent' => 0 ] ); // just checking it exists
            $posts = get_posts([
                'post_type'      => [ 'ce_article', 'post' ],
                'posts_per_page' => 1,
                'tax_query'      => [[
                    'taxonomy' => $taxonomy,
                    'field'    => 'term_id',
                    'terms'    => [ $tid ],
                ]],
            ]);
            if ( empty( $posts ) ) {
                wp_delete_term( $tid, $taxonomy );
            }
        }
    }


    // ═══════════════════════════════════════════════
    // 8. FLAG: Mark migration as complete
    // ═══════════════════════════════════════════════

    update_option( 'ce_category_migration_2_2_0', '1' );

    // Log for debugging
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( 'CE: Category migration v2.2.0 complete — ' . count( $term_ids ) . ' new terms, ' . count( $article_remap ) . ' articles remapped' );
    }
}
add_action( 'admin_init', 'ce_migrate_categories_2_2_0' );
