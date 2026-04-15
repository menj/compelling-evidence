<?php
/**
 * Compelling Evidence — functions.php
 */

// ── ENGAGEMENT SYSTEM (voting, resonance, share, widget) ─────────────────────
require_once get_stylesheet_directory() . '/inc/ce-engagement.php';

// ── SEO & SCHEMA MARKUP (JSON-LD, OG tags, print CSS) ───────────────────────
require_once get_stylesheet_directory() . '/inc/ce-seo.php';

// ── SECONDARY PAGES (About, Editorial, Privacy, Contact) ────────────────────
require_once get_stylesheet_directory() . '/inc/ce-secondary-pages.php';

// ── DATABASE MIGRATION (category restructure v2.2.0) ────────────────────────
require_once get_stylesheet_directory() . '/inc/ce-migration-2-2-0.php';
require_once get_stylesheet_directory() . '/inc/ce-migration-2-2-74.php';

// ── ANALYTICS (privacy-first, server-side tracking + admin dashboard) ────────
require_once get_stylesheet_directory() . '/inc/ce-analytics.php';

// ── THEME OPTIONS (tabbed admin settings page) ──────────────────────────────
require_once get_stylesheet_directory() . '/inc/ce-theme-options.php';

// ── CONTENT SYNC (updates existing articles from data files) ─────────────────
require_once get_stylesheet_directory() . '/inc/ce-content-sync.php';

// ── AUTO CROSS-LINKING (inline article-to-article links) ─────────────────────
require_once get_stylesheet_directory() . '/inc/ce-crosslinks.php';

// ── GLOSSARY TOOLTIP SYSTEM (auto-link Islamic/Arabic terms) ────────────────
require_once get_stylesheet_directory() . '/inc/ce-glossary.php';


// ── LEGACY URL REDIRECTS ──────────────────────────────────────────────────────

function ce_legacy_redirects() {
    if ( ! is_admin() ) {
        $request = trim( parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ), '/' );
        $map = [
            'what-is-the-purpose-of-life' => '/articles/purpose-of-life/',
            'does-god-exist'              => '/articles/does-god-exist/',
        ];
        if ( isset( $map[ $request ] ) ) {
            wp_redirect( home_url( $map[ $request ] ), 301 );
            exit;
        }
    }
}
add_action( 'template_redirect', 'ce_legacy_redirects' );


// ── ENQUEUE STYLES & SCRIPTS ──────────────────────────────────────────────────

function ce_enqueue_assets() {
    $jsdir = get_stylesheet_directory_uri() . '/assets/js/';
    $ver   = '1.0.0';

    // Fonts are self-hosted via @font-face in main.css (no external Google Fonts request)

    // Main stylesheet
    wp_enqueue_style(
        'ce-main',
        get_stylesheet_directory_uri() . '/assets/css/main.css',
        [],
        wp_get_theme()->get('Version')
    );

    // Template-specific styles (extracted from inline <style> blocks for browser caching)
    wp_enqueue_style(
        'ce-templates',
        get_stylesheet_directory_uri() . '/assets/css/templates.css',
        ['ce-main'],
        wp_get_theme()->get('Version')
    );

    // Main JS
    wp_enqueue_script(
        'ce-main',
        get_stylesheet_directory_uri() . '/assets/js/main.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );

    // Pass WP data to JS
    wp_localize_script('ce-main', 'CE', [
        'ajaxUrl'  => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('ce_nonce'),
        'homeUrl'  => home_url('/'),
    ]);

    // Analytics tracker (privacy-first, no cookies)
    wp_enqueue_script(
        'ce-analytics',
        get_stylesheet_directory_uri() . '/assets/js/ce-analytics.js',
        ['ce-main'],
        wp_get_theme()->get('Version'),
        true
    );
}
add_action('wp_enqueue_scripts', 'ce_enqueue_assets');


// ── ENQUEUE MASONRY + INFINITE SCROLL on archive/search pages ─────────────────



// ── THEME SUPPORTS ─────────────────────────────────────────────────────────────

function ce_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 240,
        'flex-width'  => true,
        'flex-height' => true,
    ]);

    // Menus
    register_nav_menus([
        'primary' => __('Primary Navigation', 'compelling-evidence'),
        'footer'  => __('Footer Navigation',  'compelling-evidence'),
    ]);
}
add_action('after_setup_theme', 'ce_theme_setup');

// ── LOAD THEME TEXTDOMAIN ─────────────────────────────────────────────────────

function ce_load_textdomain() {
    load_theme_textdomain(
        'compelling-evidence',
        get_stylesheet_directory() . '/languages'
    );
}
add_action( 'after_setup_theme', 'ce_load_textdomain' );




// ── CUSTOM POST TYPE: ARTICLE ──────────────────────────────────────────────────

function ce_register_post_types() {
    register_post_type('ce_article', [
        'labels' => [
            'name'               => __('Articles',        'compelling-evidence'),
            'singular_name'      => __('Article',         'compelling-evidence'),
            'add_new_item'       => __('Add New Article', 'compelling-evidence'),
            'edit_item'          => __('Edit Article',    'compelling-evidence'),
            'new_item'           => __('New Article',     'compelling-evidence'),
            'view_item'          => __('View Article',    'compelling-evidence'),
            'search_items'       => __('Search Articles', 'compelling-evidence'),
            'not_found'          => __('No articles found', 'compelling-evidence'),
        ],
        'public'        => true,
        'menu_icon'     => 'dashicons-book-alt',
        'menu_position' => 5,
        'supports'      => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
        'rewrite'       => ['slug' => 'articles'],
        'has_archive'   => true,
        'show_in_rest'  => true,
    ]);
}
add_action('init', 'ce_register_post_types');


// ── CUSTOM TAXONOMY: TOPIC ─────────────────────────────────────────────────────

function ce_register_taxonomies() {
    register_taxonomy('ce_topic', ['ce_article', 'post'], [
        'labels' => [
            'name'          => __('Topics',    'compelling-evidence'),
            'singular_name' => __('Topic',     'compelling-evidence'),
            'add_new_item'  => __('Add Topic', 'compelling-evidence'),
        ],
        'hierarchical'  => true,
        'public'        => true,
        'show_in_rest'  => true,
        'rewrite'       => ['slug' => 'topic'],
    ]);
}
add_action('init', 'ce_register_taxonomies');


// ── SEARCH: INCLUDE ce_article IN DEFAULT SEARCH ──────────────────────────────

function ce_search_post_types( $query ) {
    if ( $query->is_search() && ! is_admin() && $query->is_main_query() ) {
        $query->set('post_type', ['post', 'ce_article']);
    }
    return $query;
}
add_filter('pre_get_posts', 'ce_search_post_types');


// ── EXCERPT LENGTH ─────────────────────────────────────────────────────────────

add_filter('excerpt_length', function() { return 22; });
add_filter('excerpt_more',   function() { return '…'; });


// ── REMOVE TWENTY TWENTY-FIVE BLOCK STYLES WE DON'T NEED ──────────────────────

function ce_dequeue_parent_styles() {
    // We load our own stylesheet; no need for block-specific ones on front end
    wp_dequeue_style('wp-block-library-theme');
}
add_action('wp_enqueue_scripts', 'ce_dequeue_parent_styles', 100);


// ── AJAX: LIVE SEARCH ──────────────────────────────────────────────────────────

function ce_ajax_search() {
    check_ajax_referer('ce_nonce', 'nonce');

    if ( ce_rate_limited( 'search', 20, 60 ) ) {
        wp_send_json_error( [ 'message' => 'Too many searches. Please wait a moment.' ] );
    }

    $term = sanitize_text_field( $_GET['term'] ?? '' );
    if ( empty($term) ) wp_send_json_error();

    global $wpdb;

    // ── Relevance-ranked search ──
    // Score: title exact > title words > excerpt > content
    $like_term = '%' . $wpdb->esc_like( $term ) . '%';
    $words = array_filter( explode( ' ', $term ), function($w) { return strlen($w) > 2; } );

    // Build title word scoring
    $title_score_parts = [];
    // Exact phrase in title = highest score
    $title_score_parts[] = $wpdb->prepare( "(CASE WHEN p.post_title LIKE %s THEN 100 ELSE 0 END)", $like_term );

    // Individual word matches in title
    foreach ( $words as $word ) {
        $title_score_parts[] = $wpdb->prepare(
            "(CASE WHEN LOWER(p.post_title) LIKE %s THEN 10 ELSE 0 END)",
            '%' . $wpdb->esc_like( strtolower($word) ) . '%'
        );
    }

    // Excerpt match
    $title_score_parts[] = $wpdb->prepare( "(CASE WHEN p.post_excerpt LIKE %s THEN 5 ELSE 0 END)", $like_term );

    $score_sql = implode( ' + ', $title_score_parts );

    // Build WHERE: match any word in title OR content
    $where_parts = [ $wpdb->prepare( "p.post_title LIKE %s", $like_term ) ];
    $where_parts[] = $wpdb->prepare( "p.post_content LIKE %s", $like_term );
    foreach ( $words as $word ) {
        $w_like = '%' . $wpdb->esc_like( $word ) . '%';
        $where_parts[] = $wpdb->prepare( "p.post_title LIKE %s", $w_like );
    }

    $where_sql = implode( ' OR ', $where_parts );

    $sql = "SELECT p.ID, ({$score_sql}) AS relevance
            FROM {$wpdb->posts} p
            WHERE p.post_status = 'publish'
              AND p.post_type IN ('ce_article','post')
              AND ({$where_sql})
            ORDER BY relevance DESC, p.post_title ASC
            LIMIT 6";

    $rows = $wpdb->get_results( $sql );

    $data = [];
    foreach ( $rows as $row ) {
        $p = get_post( $row->ID );
        if ( ! $p ) continue;
        $terms = wp_get_post_terms( $p->ID, 'ce_topic' );
        $data[] = [
            'title'   => html_entity_decode( get_the_title($p), ENT_QUOTES, 'UTF-8' ),
            'url'     => get_permalink($p),
            'excerpt' => html_entity_decode( wp_trim_words( get_the_excerpt($p) ?: wp_trim_words( strip_tags($p->post_content), 12 ), 12 ), ENT_QUOTES, 'UTF-8' ),
            'topic'   => ( ! empty($terms) && ! is_wp_error($terms) ) ? html_entity_decode( $terms[0]->name, ENT_QUOTES, 'UTF-8' ) : '',
        ];
    }

    wp_send_json_success($data);
}
add_action('wp_ajax_nopriv_ce_search', 'ce_ajax_search');
add_action('wp_ajax_ce_search',        'ce_ajax_search');


// ── RANDOM ARTICLE ─────────────────────────────────────────────────────────────

function ce_ajax_random() {
    $post = get_posts([
        'post_type'      => ['post', 'ce_article'],
        'posts_per_page' => 1,
        'orderby'        => 'rand',
        'post_status'    => 'publish',
    ]);
    if ( ! empty($post) ) {
        wp_send_json_success(['url' => get_permalink($post[0])]);
    }
    wp_send_json_error();
}
add_action('wp_ajax_nopriv_ce_random', 'ce_ajax_random');
add_action('wp_ajax_ce_random',        'ce_ajax_random');


// ── ASK A QUESTION FORM HANDLER ────────────────────────────────────────────────

function ce_handle_ask_question() {
    if ( ! isset($_POST['ce_ask_nonce']) || ! wp_verify_nonce($_POST['ce_ask_nonce'], 'ce_ask_question') ) {
        wp_die('Security check failed.', 403);
    }
    $name     = sanitize_text_field($_POST['ask_name']         ?? '');
    $email    = sanitize_email($_POST['ask_email']             ?? '');
    $topic    = sanitize_text_field($_POST['ask_topic']        ?? '');
    $question = sanitize_textarea_field($_POST['ask_question'] ?? '');
    if ( empty($name) || empty($email) || empty($question) ) {
        wp_die('Please fill in all required fields.', 400);
    }
    $post_id = wp_insert_post([
        'post_title'   => wp_trim_words($question, 10),
        'post_content' => $question,
        'post_status'  => 'draft',
        'post_type'    => 'ce_article',
        'meta_input'   => [
            '_ce_submitter_name'  => $name,
            '_ce_submitter_email' => $email,
            '_ce_topic'           => $topic,
        ],
    ]);
    $admin_email = get_option('admin_email');
    $subject = sprintf('[%s] New Question Submitted', get_bloginfo('name'));
    $body    = "Name: {$name}\nEmail: {$email}\nTopic: {$topic}\n\nQuestion:\n{$question}\n\nReview: "
             . admin_url("post.php?post={$post_id}&action=edit");
    wp_mail($admin_email, $subject, $body);
    wp_redirect( add_query_arg('sent', '1', get_permalink()) );
    exit;
}
add_action('admin_post_nopriv_ce_ask_question', 'ce_handle_ask_question');
add_action('admin_post_ce_ask_question',        'ce_handle_ask_question');



// ── JOURNEY FILE HELPER: locale-aware ─────────────────────────────────────────

/**
 * Returns the filesystem path to the correct journey HTML file for the
 * current locale. Falls back to English (en) if no translation exists.
 *
 * File naming convention:
 *   /journeys/[slug]-journey.html          English (default)
 *   /journeys/ms-MY/[slug]-journey.html    Malay
 *
 * @param  string $journey_key  e.g. 'agnostic'
 * @return string               Absolute path to the file, or '' if not found
 */
function ce_get_journey_file( $journey_key ) {
    $base = get_stylesheet_directory() . '/journeys/';
    $locale = get_locale(); // e.g. 'ms_MY', 'en_US'

    // Normalise locale to folder name: ms_MY → ms-MY, en_US → en (fallback)
    $locale_folder = str_replace( '_', '-', $locale );

    // Try locale-specific file first
    $locale_file = $base . $locale_folder . '/' . $journey_key . '-journey.html';
    if ( file_exists( $locale_file ) ) {
        return $locale_file;
    }

    // Try language-only prefix (e.g. 'ms' from 'ms-MY')
    $lang = explode( '-', $locale_folder )[0];
    if ( $lang !== 'en' ) {
        $lang_file = $base . $lang . '/' . $journey_key . '-journey.html';
        if ( file_exists( $lang_file ) ) {
            return $lang_file;
        }
    }

    // Fall back to English default
    $default = $base . $journey_key . '-journey.html';
    return file_exists( $default ) ? $default : '';
}

/**
 * Same logic for quiz.html
 */
function ce_get_quiz_file() {
    $base = get_stylesheet_directory() . '/journeys/';
    $locale_folder = str_replace( '_', '-', get_locale() );

    $locale_file = $base . $locale_folder . '/quiz.html';
    if ( file_exists( $locale_file ) ) return $locale_file;

    $lang = explode( '-', $locale_folder )[0];
    if ( $lang !== 'en' ) {
        $lang_file = $base . $lang . '/quiz.html';
        if ( file_exists( $lang_file ) ) return $lang_file;
    }

    return $base . 'quiz.html';
}

// ── JOURNEY PAGES: AUTO-CREATE ON ACTIVATION ──────────────────────────────────

function ce_create_journey_pages() {
    // Quiz page
    if ( ! get_page_by_path('quiz') ) {
        $quiz_id = wp_insert_post([
            'post_title'     => 'Find Your Path',
            'post_name'      => 'quiz',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'page_template'  => 'page-quiz.php',
            'comment_status' => 'closed',
        ]);
        update_post_meta( $quiz_id, '_wp_page_template', 'page-quiz.php' );
    }

    // My Progress page
    if ( ! get_page_by_path('my-progress') ) {
        $prog_id = wp_insert_post([
            'post_title'     => 'My Progress',
            'post_name'      => 'my-progress',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'page_template'  => 'page-progress.php',
            'comment_status' => 'closed',
        ]);
        update_post_meta( $prog_id, '_wp_page_template', 'page-progress.php' );
    }

    // Journeys overview page
    if ( ! get_page_by_path('journeys') ) {
        $map_id = wp_insert_post([
            'post_title'     => 'Your Journey',
            'post_name'      => 'journeys',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'page_template'  => 'page-journeys.php',
            'comment_status' => 'closed',
        ]);
        update_post_meta( $map_id, '_wp_page_template', 'page-journeys.php' );
    }

    // Individual journey pages
    $journey_paths = [
        'new-atheist'       => 'The New Atheist',
        'agnostic'          => 'The Agnostic',
        'secular-humanist'  => 'The Secular Humanist',
        'antitheist'        => 'The Antitheist',
        'materialist'       => 'The Materialist',
        'muslim-doubts'     => 'The Questioning Muslim',
        'apatheist'         => 'The Apatheist',
        'deist'             => 'The Deist',
        'scientist'         => 'The Scientist',
        'classical-atheist' => 'The Classical Atheist',
        'ex-believer'       => 'The Ex-Believer',
        'spiritual-seeker'  => 'The Spiritual Seeker',
        'freethinker'       => 'The Freethinker',
        'true-muslim'       => 'The Committed Muslim',
    ];

    foreach ( $journey_paths as $slug => $title ) {
        $path = 'journey/' . $slug;
        if ( ! get_page_by_path( $path ) ) {
            // Create parent /journey/ page if it doesn't exist
            $parent = get_page_by_path('journey');
            if ( ! $parent ) {
                $parent_id = wp_insert_post([
                    'post_title'     => 'Journey',
                    'post_name'      => 'journey',
                    'post_status'    => 'publish',
                    'post_type'      => 'page',
                    'post_content'   => '<!-- Redirected to /journeys/ by theme -->',
                    'comment_status' => 'closed',
                ]);
                // Assign the journey template so the FSE block theme doesn't constrain width
                update_post_meta( $parent_id, '_wp_page_template', 'page-journey.php' );
            } else {
                $parent_id = $parent->ID;
            }

            $page_id = wp_insert_post([
                'post_title'     => $title,
                'post_name'      => $slug,
                'post_parent'    => $parent_id,
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'page_template'  => 'page-journey.php',
                'comment_status' => 'closed',
            ]);
            update_post_meta( $page_id, '_wp_page_template', 'page-journey.php' );
            update_post_meta( $page_id, '_ce_journey_key', $slug );
        }
    }

    // Hard Questions dedicated page
    if ( ! get_page_by_path('hard-questions') ) {
        wp_insert_post([
            'post_title'     => 'Rights & Freedom',
            'post_name'      => 'rights-freedom',
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'post_content'   => '<!-- Redirected to /topic/hard-questions/ by theme -->',
            'comment_status' => 'closed',
        ]);
    }
}
add_action( 'after_switch_theme', 'ce_create_journey_pages' );


// ── JOURNEY PROGRESS: AJAX (logged-in users sync to user meta) ────────────────

function ce_save_journey_progress() {
    check_ajax_referer( 'ce_nonce', 'nonce' );
    if ( ! is_user_logged_in() ) {
        wp_send_json_error( ['code' => 'not_logged_in'] );
    }
    $path   = sanitize_key( $_POST['path']   ?? '' );
    $screen = sanitize_key( $_POST['screen'] ?? '' );
    if ( empty($path) || empty($screen) ) {
        wp_send_json_error( ['code' => 'missing_params'] );
    }
    // True Muslim has different screen names
    if ( $path === 'true-muslim' ) {
        $valid_screens = ['foundation','understanding','honesty','equipping','compassion','knowledge','mission','conclusion'];
    } else {
        $valid_screens = ['horizon','singularity','calibration','emergence','entropy','constant','signal','resonance','transmission','conclusion'];
    }
    if ( ! in_array($screen, $valid_screens, true) ) {
        wp_send_json_error( ['code' => 'invalid_screen'] );
    }
    $user_id = get_current_user_id();
    $progress = get_user_meta( $user_id, '_ce_journey_progress', true ) ?: [];
    $progress[$path] = $screen;
    if ( $screen === 'conclusion' ) {
        $completed = get_user_meta( $user_id, '_ce_completed_journeys', true ) ?: [];
        if ( ! in_array($path, $completed) ) {
            $completed[] = $path;
            update_user_meta( $user_id, '_ce_completed_journeys', $completed );
        }
    }
    update_user_meta( $user_id, '_ce_journey_progress', $progress );
    wp_send_json_success( ['path' => $path, 'screen' => $screen] );
}
add_action( 'wp_ajax_ce_save_progress', 'ce_save_journey_progress' );


function ce_get_journey_progress() {
    check_ajax_referer( 'ce_nonce', 'nonce' );
    if ( ! is_user_logged_in() ) {
        wp_send_json_success( ['progress' => [], 'completed' => []] );
    }
    $user_id   = get_current_user_id();
    $progress  = get_user_meta( $user_id, '_ce_journey_progress',   true ) ?: [];
    $completed = get_user_meta( $user_id, '_ce_completed_journeys', true ) ?: [];
    wp_send_json_success( compact('progress','completed') );
}
add_action( 'wp_ajax_nopriv_ce_get_progress', 'ce_get_journey_progress' );
add_action( 'wp_ajax_ce_get_progress',        'ce_get_journey_progress' );


// ── QUIZ RESULT: SAVE PERSONA TO USER META ─────────────────────────────────────

function ce_save_quiz_result() {
    check_ajax_referer( 'ce_nonce', 'nonce' );
    $primary     = sanitize_key( $_POST['primary']     ?? '' );
    $secondaries = array_map( 'sanitize_key', (array)( $_POST['secondaries'] ?? [] ) );
    if ( empty($primary) ) wp_send_json_error();
    if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();
        update_user_meta( $user_id, '_ce_primary_path',     $primary );
        update_user_meta( $user_id, '_ce_secondary_paths',  $secondaries );
        update_user_meta( $user_id, '_ce_quiz_taken',       current_time('mysql') );
    }
    wp_send_json_success( ['primary' => $primary, 'secondaries' => $secondaries] );
}
add_action( 'wp_ajax_nopriv_ce_save_quiz', 'ce_save_quiz_result' );
add_action( 'wp_ajax_ce_save_quiz',        'ce_save_quiz_result' );


// ── REWRITE RULES FOR /journey/[slug]/ ────────────────────────────────────────

function ce_journey_rewrite_rules() {
    $paths = [
        'new-atheist','agnostic','secular-humanist','antitheist','materialist',
        'muslim-doubts','apatheist','deist','scientist','classical-atheist',
        'ex-believer','spiritual-seeker',
    ];
    foreach ( $paths as $path ) {
        add_rewrite_rule(
            '^journey/' . $path . '/?$',
            'index.php?pagename=journey/' . $path,
            'top'
        );
    }
}
add_action( 'init', 'ce_journey_rewrite_rules' );



// ── NEXT ARTICLES: AUTO-CREATE ON ACTIVATION ──────────────────────────────────

function ce_create_next_articles() {
    if ( ! function_exists( 'ce_get_next_articles_data' ) ) {
        require_once get_stylesheet_directory() . '/inc/articles-data-next.php';
    }

    $articles  = ce_get_next_articles_data();
    $topic_map = [];

    $term = term_exists( 'The Bigger Picture', 'ce_topic' );
    if ( ! $term ) {
        $term = wp_insert_term( 'The Bigger Picture', 'ce_topic', [ 'slug' => 'the-bigger-picture' ] );
    }
    $topic_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;

    foreach ( $articles as $article ) {
        $existing = get_posts([
            'name'        => $article['slug'],
            'post_type'   => 'ce_article',
            'post_status' => 'publish',
            'numberposts' => 1,
        ]);
        if ( ! empty( $existing ) ) continue;

        $post_id = wp_insert_post([
            'post_title'     => $article['title'],
            'post_name'      => $article['slug'],
            'post_content'   => trim( $article['content'] ),
            'post_excerpt'   => $article['excerpt'],
            'post_status'    => 'publish',
            'post_type'      => 'ce_article',
            'comment_status' => 'closed',
        ]);

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            wp_set_post_terms( $post_id, [ $topic_id ], 'ce_topic' );
            update_post_meta( $post_id, '_ce_next_article', '1' );
        }
    }
}
add_action( 'after_switch_theme', 'ce_create_next_articles' );


// ── ARTICLES: AUTO-CREATE ON ACTIVATION ───────────────────────────────────────

function ce_create_articles() {
    if ( ! function_exists( 'ce_get_articles_data' ) ) {
        require_once get_stylesheet_directory() . '/inc/articles-data.php';
    }
    $articles = ce_get_articles_data();

    // Ensure topic terms exist
    $topic_map = [];
    $topics = [
        'Does God Exist?'  => 'does-god-exist',
        'Science & Evidence'     => 'science-evidence',
        'Ethics Without God?' => 'ethics-without-god',
        'The Problem of Evil'  => 'the-problem-of-evil',
        'Science & Evidence'   => 'science-evidence',
        'Examining the Quran'         => 'examining-the-quran',
        'History & Context'   => 'history-context',
        'The Bigger Picture' => 'the-bigger-picture',
        'Does God Exist?'        => 'does-god-exist',
        'The Bigger Picture' => 'the-bigger-picture',
    ];
    foreach ( $topics as $name => $slug ) {
        $term = term_exists( $name, 'ce_topic' );
        if ( ! $term ) {
            $term = wp_insert_term( $name, 'ce_topic', [ 'slug' => $slug ] );
        }
        $topic_map[ $name ] = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
    }

    foreach ( $articles as $article ) {
        // Skip if already exists
        $existing = get_posts([
            'name'        => $article['slug'],
            'post_type'   => 'ce_article',
            'post_status' => 'publish',
            'numberposts' => 1,
        ]);
        if ( ! empty( $existing ) ) continue;

        $post_id = wp_insert_post([
            'post_title'     => $article['title'],
            'post_name'      => $article['slug'],
            'post_content'   => trim( $article['content'] ),
            'post_excerpt'   => $article['excerpt'],
            'post_status'    => 'publish',
            'post_type'      => 'ce_article',
            'menu_order'     => (int) $article['order'],
            'comment_status' => 'closed',
        ]);

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            $topic_name = $article['topic'] ?? '';
            if ( isset( $topic_map[ $topic_name ] ) ) {
                wp_set_post_terms( $post_id, [ $topic_map[ $topic_name ] ], 'ce_topic' );
            }
            update_post_meta( $post_id, '_ce_article_order', (int) $article['order'] );
        }
    }
}
add_action( 'after_switch_theme', 'ce_create_articles' );


// ── ARTICLES SET 2: AUTO-CREATE ON ACTIVATION ─────────────────────────────────

function ce_create_articles_2() {
    if ( ! function_exists( 'ce_get_articles_data_2' ) ) {
        require_once get_stylesheet_directory() . '/inc/articles-data-2.php';
    }

    $articles = ce_get_articles_data_2();

    // Ensure topic taxonomy terms exist
    $topic_map = [];
    $topics = [
        'Does God Exist?'  => 'does-god-exist',
        'Science & Evidence'     => 'science-evidence',
        'Ethics Without God?' => 'ethics-without-god',
        'The Problem of Evil'  => 'the-problem-of-evil',
        'Science & Evidence'   => 'science-evidence',
        'Examining the Quran'         => 'examining-the-quran',
        'History & Context'   => 'history-context',
        'The Bigger Picture' => 'the-bigger-picture',
        'Does God Exist?'        => 'does-god-exist',
        'The Inner Journey'=> 'the-inner-journey',
        'The Bigger Picture' => 'the-bigger-picture',
    ];
    foreach ( $topics as $name => $slug ) {
        $term = term_exists( $name, 'ce_topic' );
        if ( ! $term ) {
            $term = wp_insert_term( $name, 'ce_topic', [ 'slug' => $slug ] );
        }
        $topic_map[ $name ] = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
    }

    foreach ( $articles as $article ) {
        $existing = get_posts([
            'name'        => $article['slug'],
            'post_type'   => 'ce_article',
            'post_status' => 'publish',
            'numberposts' => 1,
        ]);
        if ( ! empty( $existing ) ) continue;

        $post_id = wp_insert_post([
            'post_title'     => $article['title'],
            'post_name'      => $article['slug'],
            'post_content'   => trim( $article['content'] ),
            'post_excerpt'   => $article['excerpt'],
            'post_status'    => 'publish',
            'post_type'      => 'ce_article',
            'menu_order'     => (int) $article['order'],
            'comment_status' => 'closed',
        ]);

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            $topic_name = $article['topic'] ?? '';
            if ( isset( $topic_map[ $topic_name ] ) ) {
                wp_set_post_terms( $post_id, [ $topic_map[ $topic_name ] ], 'ce_topic' );
            }
            update_post_meta( $post_id, '_ce_article_order', (int) $article['order'] );
        }
    }
}
add_action( 'after_switch_theme', 'ce_create_articles_2' );


// ── ARTICLES SET 3: AUTO-CREATE ON ACTIVATION ─────────────────────────────────

function ce_create_articles_3() {
    if ( ! function_exists( 'ce_get_articles_data_3' ) ) {
        require_once get_stylesheet_directory() . '/inc/articles-data-3.php';
    }
    $articles = ce_get_articles_data_3();

    $topic_map = [];
    $topics = [
        'Does God Exist?'  => 'does-god-exist',
        'Science & Evidence'     => 'science-evidence',
        'Ethics Without God?' => 'ethics-without-god',
        'The Problem of Evil'  => 'the-problem-of-evil',
        'Science & Evidence'   => 'science-evidence',
        'Examining the Quran'         => 'examining-the-quran',
        'History & Context'   => 'history-context',
        'The Bigger Picture' => 'the-bigger-picture',
        'Does God Exist?'        => 'does-god-exist',
        'The Inner Journey'=> 'the-inner-journey',
        'The Bigger Picture' => 'the-bigger-picture',
    ];
    foreach ( $topics as $name => $slug ) {
        $term = term_exists( $name, 'ce_topic' );
        if ( ! $term ) {
            $term = wp_insert_term( $name, 'ce_topic', [ 'slug' => $slug ] );
        }
        $topic_map[ $name ] = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
    }

    foreach ( $articles as $article ) {
        $existing = get_posts([
            'name'        => $article['slug'],
            'post_type'   => 'ce_article',
            'post_status' => 'publish',
            'numberposts' => 1,
        ]);
        if ( ! empty( $existing ) ) continue;

        $post_id = wp_insert_post([
            'post_title'     => $article['title'],
            'post_name'      => $article['slug'],
            'post_content'   => trim( $article['content'] ),
            'post_excerpt'   => $article['excerpt'],
            'post_status'    => 'publish',
            'post_type'      => 'ce_article',
            'menu_order'     => (int) $article['order'],
            'comment_status' => 'closed',
        ]);

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            $topic_name = $article['topic'] ?? '';
            $term_ids   = [];
            if ( isset( $topic_map[ $topic_name ] ) ) {
                $term_ids[] = $topic_map[ $topic_name ];
            }
            // Tag all Set 3 articles as "Hard Questions"
            $hq_term = term_exists( 'Rights & Freedom', 'ce_topic' );
            if ( ! $hq_term ) {
                $hq_term = wp_insert_term( 'Rights & Freedom', 'ce_topic', [ 'slug' => 'rights-freedom' ] );
            }
            $hq_id = is_array( $hq_term ) ? (int) $hq_term['term_id'] : (int) $hq_term;
            if ( $hq_id ) $term_ids[] = $hq_id;

            if ( ! empty( $term_ids ) ) {
                wp_set_post_terms( $post_id, $term_ids, 'ce_topic' );
            }
            update_post_meta( $post_id, '_ce_article_order', (int) $article['order'] );
            update_post_meta( $post_id, '_ce_hard_question', '1' );
        }
    }
}
add_action( 'after_switch_theme', 'ce_create_articles_3' );


// ── CREATE ARTICLES — Set 4 (Book-informed, v1.7.0) ─────────────────────────

function ce_create_articles_4() {
    if ( ! function_exists( 'ce_get_articles_data_4' ) ) {
        require_once get_stylesheet_directory() . '/inc/articles-data-4.php';
    }
    $articles = ce_get_articles_data_4();

    $topic_map = [];
    $topics = [
        'The Inner Journey'=> 'the-inner-journey',
    ];
    foreach ( $topics as $name => $slug ) {
        $term = term_exists( $name, 'ce_topic' );
        if ( ! $term ) {
            $term = wp_insert_term( $name, 'ce_topic', [ 'slug' => $slug ] );
        }
        $topic_map[ $name ] = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
    }

    foreach ( $articles as $article ) {
        $existing = get_posts([
            'name'        => $article['slug'],
            'post_type'   => 'ce_article',
            'post_status' => 'publish',
            'numberposts' => 1,
        ]);
        if ( ! empty( $existing ) ) continue;

        $post_id = wp_insert_post([
            'post_title'     => $article['title'],
            'post_name'      => $article['slug'],
            'post_content'   => trim( $article['content'] ),
            'post_excerpt'   => $article['excerpt'],
            'post_status'    => 'publish',
            'post_type'      => 'ce_article',
            'menu_order'     => (int) $article['order'],
            'comment_status' => 'closed',
        ]);

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            $topic_name = $article['topic'] ?? '';
            $term_ids   = [];
            if ( isset( $topic_map[ $topic_name ] ) ) {
                $term_ids[] = $topic_map[ $topic_name ];
            }
            if ( ! empty( $term_ids ) ) {
                wp_set_post_terms( $post_id, $term_ids, 'ce_topic' );
            }
            update_post_meta( $post_id, '_ce_article_order', (int) $article['order'] );
        }
    }
}
add_action( 'after_switch_theme', 'ce_create_articles_4' );
