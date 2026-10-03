<?php
/**
 * Compelling Evidence — functions.php
 */

// ── ENGAGEMENT SYSTEM (voting, resonance, share, widget) ─────────────────────
require_once get_stylesheet_directory() . '/inc/ce-db.php';
require_once get_stylesheet_directory() . '/inc/ce-gone.php';
require_once get_stylesheet_directory() . '/inc/ce-engagement.php';

// ── SEO & SCHEMA MARKUP (JSON-LD, OG tags, print CSS) ───────────────────────
require_once get_stylesheet_directory() . '/inc/ce-seo.php';

// ── AEO META BOXES & SCHEMA (FAQ items, about/mentions entities) ─────────────
require_once get_stylesheet_directory() . '/inc/ce-aeo.php';

// ── SECONDARY PAGES (About, Editorial, Privacy, Contact) ────────────────────
require_once get_stylesheet_directory() . '/inc/ce-secondary-pages.php';

// ── DATABASE MIGRATION (category restructure v2.2.0) ────────────────────────
require_once get_stylesheet_directory() . '/inc/ce-migration-2-2-0.php';
require_once get_stylesheet_directory() . '/inc/ce-migration-2-2-74.php';

// ── ANALYTICS (privacy-first, server-side tracking + admin dashboard) ────────
require_once get_stylesheet_directory() . '/inc/ce-analytics.php';

// ── THEME OPTIONS (tabbed admin settings page) ──────────────────────────────
require_once get_stylesheet_directory() . '/inc/ce-parent-compat.php';
require_once get_stylesheet_directory() . '/inc/ce-fonts.php';
require_once get_stylesheet_directory() . '/inc/ce-icons.php';
require_once get_stylesheet_directory() . '/inc/ce-404.php';
require_once get_stylesheet_directory() . '/inc/ce-media.php';
require_once get_stylesheet_directory() . '/inc/ce-media-auto.php';
require_once get_stylesheet_directory() . '/inc/ce-search-permalinks.php';
require_once get_stylesheet_directory() . '/inc/ce-plugin-compat.php';
require_once get_stylesheet_directory() . '/inc/ce-rankmath.php';
require_once get_stylesheet_directory() . '/inc/ce-theme-options.php';

// ── ARTICLE LOADER (secure JSON-based article loading with checksum verification)
require_once get_stylesheet_directory() . '/inc/class-ce-article-loader.php';
require_once get_stylesheet_directory() . '/inc/class-ce-feed-redirector.php';

// ── CONTENT SYNC (updates existing articles from data files) ─────────────────
require_once get_stylesheet_directory() . '/inc/ce-content-sync.php';

// ── AUTO CROSS-LINKING (inline article-to-article links) ─────────────────────
require_once get_stylesheet_directory() . '/inc/ce-crosslinks.php';

// ── GLOSSARY TOOLTIP SYSTEM (auto-link Islamic/Arabic terms) ────────────────
require_once get_stylesheet_directory() . '/inc/ce-glossary.php';

// ── Q&A SYSTEM (ticketing-style question management) ────────────────────────
require_once get_stylesheet_directory() . '/inc/ce-question-cpt.php';


// ── LEGACY URL REDIRECTS ──────────────────────────────────────────────────────

function ce_legacy_redirects() {
    if ( is_admin() || ! isset( $_SERVER['REQUEST_URI'] ) ) {
        return;
    }

    // Sanitise before parsing — REQUEST_URI is attacker-controllable and
    // can contain malformed input. wp_unslash() handles magic-quotes; the
    // path-only parse strips any query string before the lookup.
    $uri     = sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) );
    $request = trim( (string) parse_url( $uri, PHP_URL_PATH ), '/' );

    $map = [
        'what-is-the-purpose-of-life' => '/articles/purpose-of-life/',
        'does-god-exist'              => '/articles/does-god-exist/',
    ];
    if ( isset( $map[ $request ] ) ) {
        wp_safe_redirect( home_url( $map[ $request ] ), 301 );
        exit;
    }
}
add_action( 'template_redirect', 'ce_legacy_redirects' );


// ── ENQUEUE STYLES & SCRIPTS ──────────────────────────────────────────────────

function ce_enqueue_assets() {
    $stylesheet_uri = get_stylesheet_directory_uri();
    $version        = wp_get_theme()->get( 'Version' );

    // Fonts are self-hosted via @font-face in main.css (no external Google Fonts request)

    // Main stylesheet
    wp_enqueue_style( 'ce-main', $stylesheet_uri . '/assets/css/main.css', [], $version );

    // Template-specific styles — skip on front page (saves 42 KiB unused CSS on homepage)
    if ( ! is_front_page() && ! is_home() ) {
        wp_enqueue_style( 'ce-templates', $stylesheet_uri . '/assets/css/templates.css', [ 'ce-main' ], $version );
    }

    // Main JS
    wp_enqueue_script( 'ce-main', $stylesheet_uri . '/assets/js/main.js', [], $version, true );

    // Pass WP data to JS
    wp_localize_script( 'ce-main', 'CE', [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'ce_nonce' ),
        'homeUrl' => home_url( '/' ),
    ] );

    // Analytics tracker (privacy-first, no cookies)
    wp_enqueue_script( 'ce-analytics', $stylesheet_uri . '/assets/js/ce-analytics.js', [ 'ce-main' ], $version, true );

    // Article table of contents (builds the sidebar TOC, scrolls to sections, scroll spy)
    if ( is_singular( [ 'ce_article', 'post' ] ) ) {
        wp_enqueue_script( 'ce-toc', $stylesheet_uri . '/assets/js/ce-toc.js', [], $version, true );
    }
}
add_action( 'wp_enqueue_scripts', 'ce_enqueue_assets' );


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

    // ── OG/Twitter share image size (Item 23) ──────────────────────────────
    // Facebook, X, LinkedIn, and most platforms expect 1200×630 for share
    // cards. The previous code pulled from 'large' (1024×1024 max), which
    // mismatches the og:image:width/height meta values (1200×630) and
    // forces auto-cropping by each consumer. Registering a dedicated size
    // ensures WordPress generates a proper 1200×630 thumbnail at upload
    // time. The hard crop (true) preserves aspect ratio across image
    // shapes by cropping rather than letterboxing.
    add_image_size( 'og-share', 1200, 630, true );

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
        'menu_icon'     => 'dashicons-book-alt', // Inherits CE branding from admin styles
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

    $term = sanitize_text_field( wp_unslash( $_GET['term'] ?? '' ) );
    if ( empty( $term ) ) {
        wp_send_json_error();
    }

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

    $rows = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- every fragment in $score_sql and $where_sql is built with $wpdb->prepare() above.

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
    check_ajax_referer( 'ce_nonce', 'nonce' );

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
    // ── Compute the return URL up front ──────────────────────────────────────
    // admin-post.php has no global $post, so get_permalink() returns falsy.
    // Use the form's hidden field if present, fall back to the referer, fall
    // back to the canonical /ask-a-question/ URL. All three are validated to
    // be same-host before we redirect to them.
    $home_host = wp_parse_url( home_url(), PHP_URL_HOST );

    $candidate = '';
    if ( ! empty( $_POST['ask_return_to'] ) ) {
        $candidate = esc_url_raw( wp_unslash( $_POST['ask_return_to'] ) );
    }
    if ( empty( $candidate ) ) {
        $candidate = wp_get_referer() ?: '';
    }
    if ( empty( $candidate ) || wp_parse_url( $candidate, PHP_URL_HOST ) !== $home_host ) {
        $candidate = home_url( '/ask-a-question/' );
    }
    $return_url = $candidate;

    $redirect_with = function( array $args ) use ( $return_url ) {
        wp_safe_redirect( add_query_arg( $args, $return_url ) );
        exit;
    };

    // ── Nonce ────────────────────────────────────────────────────────────────
    if ( ! isset( $_POST['ce_ask_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ce_ask_nonce'] ) ), 'ce_ask_question' ) ) {
        $redirect_with( [ 'ask_error' => 'security' ] );
    }

    // ── Honeypot — bots fill this hidden field; humans don't ─────────────────
    // The 'ask_website' field is invisible to humans (CSS-hidden + tabindex=-1).
    // Any non-empty value is a near-certain bot. Silently fake success so the
    // bot's success-detection logic moves on, but never persist the data.
    if ( ! empty( $_POST['ask_website'] ) ) {
        $redirect_with( [ 'sent' => '1' ] );
    }

    // ── Rate limit — 3 submissions per IP per hour ───────────────────────────
    if ( function_exists( 'ce_rate_limited' ) && ce_rate_limited( 'ask_question', 3, 3600 ) ) {
        $redirect_with( [ 'ask_error' => 'rate' ] );
    }

    // ── Sanitize and validate ────────────────────────────────────────────────
    $name     = sanitize_text_field( wp_unslash( $_POST['ask_name']     ?? '' ) );
    $email    = sanitize_email(      wp_unslash( $_POST['ask_email']    ?? '' ) );
    $topic    = sanitize_text_field( wp_unslash( $_POST['ask_topic']    ?? '' ) );
    $question = sanitize_textarea_field( wp_unslash( $_POST['ask_question'] ?? '' ) );

    if ( empty( $name ) || empty( $email ) || empty( $question ) ) {
        $redirect_with( [ 'ask_error' => 'missing' ] );
    }
    if ( ! is_email( $email ) ) {
        $redirect_with( [ 'ask_error' => 'email' ] );
    }
    if ( strlen( $question ) < 10 ) {
        $redirect_with( [ 'ask_error' => 'short' ] );
    }

    // ── Insert as ce_question CPT ────────────────────────────────────────────
    // post_status = 'pending' rather than 'publish' — the question is NOT
    // publicly accessible at /qa/{slug}/ until an admin reviews and publishes.
    // The ce_qstatus taxonomy ('new' | 'review' | 'answered' | 'published' |
    // 'archived') is the editorial workflow state; post_status is WordPress's
    // visibility gate. Both are used together: the admin moves the question
    // through ce_qstatus during review, then changes post_status to 'publish'
    // when the answer is ready to go live.
    $post_id = wp_insert_post( [
        'post_title'   => wp_trim_words( $question, 10, '…' ),
        'post_content' => $question,
        'post_status'  => 'pending',
        'post_type'    => 'ce_question',
        'meta_input'   => [
            '_ce_submitter_name'  => $name,
            '_ce_submitter_email' => $email,
            '_ce_question_topic'  => $topic,
            '_ce_submitted_date'  => current_time( 'mysql' ),
        ],
    ], true );

    if ( is_wp_error( $post_id ) ) {
        error_log( 'CE: Failed to save question — ' . $post_id->get_error_message() );
        $redirect_with( [ 'ask_error' => 'server' ] );
    }

    // Set ce_qstatus = 'new' so the dashboard widget picks it up
    $new_status = get_term_by( 'slug', 'new', 'ce_qstatus' );
    if ( $new_status && ! is_wp_error( $new_status ) ) {
        wp_set_object_terms( $post_id, [ $new_status->term_id ], 'ce_qstatus' );
    }

    // The admin notification email is sent by ce_notify_new_question() which
    // is hooked to wp_insert_post — no need to send it again here.

    $redirect_with( [ 'sent' => '1' ] );
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

/**
 * Returns the canonical map of journey-path slugs to display titles.
 *
 * Single source of truth for which journey paths exist. Used by:
 *   - ce_create_journey_pages() — page creation on theme activation
 *   - ce_save_quiz_result()    — validation of submitted primary path
 *   - ce_save_journey_progress() — implicit (via valid_screens lookup)
 *
 * @return array<string,string> [slug => title]
 */
/**
 * Build a fixed-character-length excerpt for listing surfaces.
 *
 * Truncates at the last word boundary at or below the given character limit
 * so we never break a word mid-character, then appends an ellipsis if the
 * source was longer than the limit. Reads from $post->post_excerpt first,
 * falling back to $post->post_content with all HTML stripped.
 *
 * Used on every article-listing surface for consistent UI rhythm — the
 * archive's three render branches and the search-results template all
 * call this helper rather than each computing their own truncation.
 *
 * @param WP_Post|int|null $post  Post object, ID, or null for current.
 * @param int              $limit Hard character ceiling. Default 120.
 * @return string Plain-text excerpt, length <= $limit + 1 (for the ellipsis).
 */
function ce_excerpt_chars( $post = null, int $limit = 120 ): string {
    $post = get_post( $post );
    if ( ! $post ) {
        return '';
    }

    $source = $post->post_excerpt;
    if ( '' === trim( (string) $source ) ) {
        $source = wp_strip_all_tags( $post->post_content );
    }

    // Collapse whitespace runs introduced by stripped HTML so truncation
    // counts visible characters rather than markup whitespace.
    $source = trim( preg_replace( '/\s+/u', ' ', (string) $source ) );

    if ( '' === $source ) {
        return '';
    }

    // mb_strlen handles UTF-8 multibyte characters correctly — Arabic, em-dashes,
    // and curly quotes shouldn't cost an extra character vs. their ASCII equivalents.
    if ( mb_strlen( $source, 'UTF-8' ) <= $limit ) {
        return $source;
    }

    // Cut to the limit, then walk back to the last space so we never break a word.
    $cut = mb_substr( $source, 0, $limit, 'UTF-8' );
    $last_space = mb_strrpos( $cut, ' ', 0, 'UTF-8' );
    if ( false !== $last_space && $last_space > 0 ) {
        $cut = mb_substr( $cut, 0, $last_space, 'UTF-8' );
    }
    return rtrim( $cut, " \t\n\r\0\x0B,;:.-" ) . '…';
}


function ce_get_journey_paths() {
    return [
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
}

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
    $journey_paths = ce_get_journey_paths();

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

    // Rights & Freedom placeholder page. The guard used to test a different
    // slug ('hard-questions') from the one inserted ('rights-freedom'), so every
    // theme activation added another copy (rights-freedom-2, -3, …), each a thin
    // duplicate of the topic page. Fixed in 2.6.31; see also
    // ce_redirect_topic_placeholder_pages().
    if ( ! get_page_by_path('rights-freedom') ) {
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

/**
 * Returns the allowed screen sequence for a given journey path.
 *
 * @param string $path Journey-path slug.
 * @return array<string> List of valid screen identifiers, or empty if unknown path.
 */
function ce_get_journey_screens( string $path ): array {
    if ( $path === 'true-muslim' ) {
        return [ 'foundation', 'understanding', 'honesty', 'equipping', 'compassion', 'knowledge', 'mission', 'conclusion' ];
    }
    return [ 'horizon', 'singularity', 'calibration', 'emergence', 'entropy', 'constant', 'signal', 'resonance', 'transmission', 'conclusion' ];
}

function ce_save_journey_progress() {
    check_ajax_referer( 'ce_nonce', 'nonce' );

    if ( ! is_user_logged_in() ) {
        wp_send_json_error( [ 'code' => 'not_logged_in' ] );
    }

    $path   = sanitize_key( $_POST['path']   ?? '' );
    $screen = sanitize_key( $_POST['screen'] ?? '' );

    if ( empty( $path ) || empty( $screen ) ) {
        wp_send_json_error( [ 'code' => 'missing_params' ] );
    }

    // Validate path against the canonical allowlist
    $allowed_paths = array_keys( ce_get_journey_paths() );
    if ( ! in_array( $path, $allowed_paths, true ) ) {
        wp_send_json_error( [ 'code' => 'invalid_path' ] );
    }

    // Validate screen against the per-path screen sequence
    $valid_screens = ce_get_journey_screens( $path );
    if ( ! in_array( $screen, $valid_screens, true ) ) {
        wp_send_json_error( [ 'code' => 'invalid_screen' ] );
    }

    $user_id  = get_current_user_id();
    $progress = get_user_meta( $user_id, '_ce_journey_progress', true ) ?: [];
    $progress[ $path ] = $screen;

    if ( $screen === 'conclusion' ) {
        $completed = get_user_meta( $user_id, '_ce_completed_journeys', true ) ?: [];
        if ( ! in_array( $path, $completed, true ) ) {
            $completed[] = $path;
            update_user_meta( $user_id, '_ce_completed_journeys', $completed );
        }
    }

    update_user_meta( $user_id, '_ce_journey_progress', $progress );
    wp_send_json_success( [ 'path' => $path, 'screen' => $screen ] );
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

    if ( empty( $primary ) ) {
        wp_send_json_error( [ 'code' => 'missing_primary' ] );
    }

    // Validate against the canonical journey-path allowlist. Without this, a
    // valid nonce lets any submitter store arbitrary text in user meta — not
    // a security disaster (the data is read by templates that fall back to
    // empty for unknown values), but a clean defence anyway.
    $allowed = array_keys( ce_get_journey_paths() );
    if ( ! in_array( $primary, $allowed, true ) ) {
        wp_send_json_error( [ 'code' => 'invalid_primary' ] );
    }
    $secondaries = array_values( array_intersect( $secondaries, $allowed ) );

    if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();
        update_user_meta( $user_id, '_ce_primary_path',    $primary );
        update_user_meta( $user_id, '_ce_secondary_paths', $secondaries );
        update_user_meta( $user_id, '_ce_quiz_taken',      current_time( 'mysql' ) );
    }
    wp_send_json_success( [ 'primary' => $primary, 'secondaries' => $secondaries ] );
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



// ── ARTICLES: JSON-BASED SYNC (v2.3.0+) ──────────────────────────────────────
// Legacy PHP article data files have been migrated to JSON format.
// Article creation is now handled by ce-content-sync.php using CE_Article_Loader.
// See: inc/articles/batch-*.json and inc/articles/manifest.json


// ── PERFORMANCE: BROWSER CACHE HEADERS ───────────────────────────────────────
// Sets aggressive cache lifetimes on static assets — fixes PageSpeed "Use efficient cache lifetimes"

function ce_browser_cache_headers() {
    if ( is_admin() ) return;

    // 1 year for fonts, images, versioned CSS/JS
    header( 'Cache-Control: public, max-age=31536000, immutable', false );
    header( 'Vary: Accept-Encoding', false );
}
add_action( 'send_headers', 'ce_browser_cache_headers' );

// Fine-grained cache per asset type via wp_headers filter
function ce_asset_cache_headers( $headers ) {
    if ( is_admin() ) return $headers;

    // Sanitise before regex match — preg_match itself is safe but the
    // value can be logged downstream by other filters, and consistency
    // with the rest of the theme matters.
    $uri = isset( $_SERVER['REQUEST_URI'] )
        ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) )
        : '';

    // Versioned assets (woff2, css?ver=, js?ver=, png, jpg)
    if ( preg_match( '/\.(woff2|woff|ttf|otf|eot)(\?|$)/', $uri ) ) {
        $headers['Cache-Control'] = 'public, max-age=31536000, immutable';
        $headers['Expires']       = gmdate( 'D, d M Y H:i:s', time() + 31536000 ) . ' GMT';
    } elseif ( preg_match( '/\.(css|js)\?ver=/', $uri ) ) {
        $headers['Cache-Control'] = 'public, max-age=31536000, immutable';
        $headers['Expires']       = gmdate( 'D, d M Y H:i:s', time() + 31536000 ) . ' GMT';
    } elseif ( preg_match( '/\.(png|jpg|jpeg|webp|svg|ico)(\?|$)/', $uri ) ) {
        $headers['Cache-Control'] = 'public, max-age=2592000'; // 30 days
        $headers['Expires']       = gmdate( 'D, d M Y H:i:s', time() + 2592000 ) . ' GMT';
    }

    return $headers;
}
add_filter( 'wp_headers', 'ce_asset_cache_headers' );


// ── PERFORMANCE: REMOVE UNUSED PRECONNECT HINTS ──────────────────────────────
// PageSpeed flags fonts.googleapis.com and fonts.gstatic.com preconnects as unused
// because all fonts are self-hosted. The parent theme (Twenty Twenty-Five) adds these.

function ce_remove_unused_preconnects( $hints, $relation_type ) {
    if ( 'preconnect' !== $relation_type && 'dns-prefetch' !== $relation_type ) {
        return $hints;
    }
    $remove = [ 'fonts.googleapis.com', 'fonts.gstatic.com', 's.w.org' ];
    return array_filter( $hints, function( $hint ) use ( $remove ) {
        $url = is_array( $hint ) ? ( $hint['href'] ?? '' ) : $hint;
        foreach ( $remove as $domain ) {
            if ( strpos( $url, $domain ) !== false ) return false;
        }
        return true;
    });
}
add_filter( 'wp_resource_hints', 'ce_remove_unused_preconnects', 10, 2 );


// ── SEO: ROBOTS.TXT ──────────────────────────────────────────────────────────
// Fixes PageSpeed SEO "robots.txt is not valid" — Lighthouse was unable to download it.
// When Rank Math is active, defer entirely — Rank Math has its own admin-managed
// robots.txt with the correct sitemap_index.xml URL. Returning $output unchanged
// lets Rank Math's filter run (whichever order) without ours fighting it.

function ce_is_rankmath_active(): bool {
    // Rank Math's main class is named `RankMath` (not namespaced) per
    // its rank-math.php entry point. Check both this and the namespaced
    // helper class to cover edge cases.
    return class_exists( 'RankMath' ) || class_exists( 'RankMath\\Helper' );
}

function ce_robots_txt( $output, $public ) {
    if ( ce_is_rankmath_active() ) {
        return $output;
    }
    $home = home_url('/');
    $output  = "User-agent: *\n";
    $output .= "Allow: /\n";
    $output .= "Disallow: /wp-admin/\n";
    $output .= "Allow: /wp-admin/admin-ajax.php\n";
    // /wp-includes/ is no longer disallowed (2.6.31): it holds CSS and JS
    // that Google needs to render pages. Internal search results are
    // disallowed, per Google's starter guide on search-result-like pages.
    $output .= "Disallow: /?s=\n";
    $output .= "Disallow: /" . ( function_exists( 'ce_search_base' ) ? ce_search_base() : 'search' ) . "/\n";
    $output .= "Disallow: /wp-login.php\n";
    $output .= "Disallow: /xmlrpc.php\n";
    $output .= "\n";
    $output .= "Sitemap: " . $home . "wp-sitemap.xml\n";
    return $output;
}
add_filter( 'robots_txt', 'ce_robots_txt', 10, 2 );


// ── PERFORMANCE: DEFER GOOGLE TAG MANAGER ────────────────────────────────────
// GTM adds 153 KiB of JS with 63 KiB unused. Defer it until after page load.

function ce_defer_gtm_scripts( $tag, $handle, $src ) {
    // Only defer GTM, not any other scripts
    if ( strpos( $src, 'googletagmanager.com' ) !== false ) {
        // Replace synchronous script with defer
        $tag = str_replace( '<script ', '<script defer ', $tag );
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'ce_defer_gtm_scripts', 10, 3 );



// ── PERFORMANCE: CRITICAL INLINE FONT CSS FOR LCP ────────────────────────────
// The LCP element on the front page is the hero Arabic verse text.
// uthmani-quran.woff2 is discovered late through the CSS chain.
// Inlining the @font-face declaration in <head> moves discovery to the HTML parse.

function ce_critical_font_inline() {
    if ( ! is_front_page() && ! is_home() ) return;
    $font_url = get_stylesheet_directory_uri() . '/assets/fonts/uthmani-quran.woff2';
    $hadith_url = get_stylesheet_directory_uri() . '/assets/fonts/ce-hadith-400.woff2';
    echo '<style id="ce-critical-fonts">
@font-face{font-family:"Uthmani Quran";src:url("' . esc_url( $font_url ) . '") format("woff2");font-weight:400;font-style:normal;font-display:swap;unicode-range:U+0600-06FF}
@font-face{font-family:"CE Hadith";src:url("' . esc_url( $hadith_url ) . '") format("woff2");font-weight:400;font-style:normal;font-display:swap}
</style>' . "\n";
}
add_action( 'wp_head', 'ce_critical_font_inline', 0 );


// ── SEO: ENABLE WORDPRESS CORE SITEMAP ────────────────────────────────────────
// Activates /wp-sitemap.xml which includes all CPTs, taxonomies, and pages.
// The static sitemap.xml only had 16 URLs. This replaces it with full coverage.
//
// Skipped entirely when Rank Math is active — Rank Math owns sitemap generation
// (via its own includes/modules/sitemap/ subsystem at sitemap_index.xml) and
// its class-redirect-core-sitemaps.php intercepts core sitemaps anyway. These
// theme filters become inert with Rank Math active, but registering them is
// dead weight on every request, so guard at registration time.

if ( ! ce_is_rankmath_active() ) {

    add_filter( 'wp_sitemaps_enabled', '__return_true' );

    // Include ce_article CPT in the sitemap
    add_filter( 'wp_sitemaps_post_types', function( $post_types ) {
        if ( ! isset( $post_types['ce_article'] ) ) {
            $post_types['ce_article'] = get_post_type_object( 'ce_article' );
        }
        return $post_types;
    });

    // Include ce_topic taxonomy in the sitemap
    add_filter( 'wp_sitemaps_taxonomies', function( $taxonomies ) {
        if ( ! isset( $taxonomies['ce_topic'] ) ) {
            $taxonomies['ce_topic'] = get_taxonomy( 'ce_topic' );
        }
        return $taxonomies;
    });

}



// ── CE BRANDING: FAVICON + ADMIN ICON ────────────────────────────────────────

/**
 * Register favicon assets via WordPress site_icon filter as programmatic fallback.
 * The primary favicons are already in header.php <head>.
 * This ensures the WP admin also uses the CE icon.
 */
function ce_site_icon_url( $url, $size ) {
    $icon_dir = get_stylesheet_directory_uri() . '/assets/images/';
    if ( $size >= 180 ) return $icon_dir . 'apple-touch-icon.png';
    if ( $size >= 96 )  return $icon_dir . 'favicon-96x96.png';
    if ( $size >= 32 )  return $icon_dir . 'favicon-32x32.png';
    return $icon_dir . 'favicon-16x16.png';
}
add_filter( 'get_site_icon_url', 'ce_site_icon_url', 10, 2 );

/**
 * Inject CE icon into WordPress admin:
 * - Admin menu "CE Theme Options" entry gets the CE icon (data URI SVG)
 * - Admin bar gets CE icon alongside site name
 * - All CE admin pages show consistent branding
 */
function ce_admin_icon_styles() {
    $icon_url = get_stylesheet_directory_uri() . '/assets/images/ce-admin-icon.svg';
    $icon_url_2x = get_stylesheet_directory_uri() . '/assets/images/ce-admin-icon-40.png';
    ?>
    <style id="ce-admin-icon-styles">
        /* CE Theme Options menu entry — SVG icon */
        #adminmenu .toplevel_page_ce-theme-options .wp-menu-image::before,
        #adminmenu a[href*="ce-theme-options"] .wp-menu-image::before {
            background-image: url('<?php echo esc_url( $icon_url ); ?>') !important;
            background-size: 20px 20px !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
            content: '' !important;
            font-size: 0 !important;
        }

        /* Admin bar favicon */
        #wpadminbar #wp-admin-bar-site-name > .ab-item::before {
            content: '';
            display: inline-block;
            width: 16px;
            height: 16px;
            background: url('<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/favicon-32x32.png' ); ?>') center/contain no-repeat;
            vertical-align: middle;
            margin-right: 5px;
            border-radius: 3px;
        }

        /* CE options page — polished header logo */
        .ce-options-logo {
            background: #180d2e !important;
            border-radius: 8px !important;
            width: 32px !important;
            height: 32px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0 !important;
            background-image: url('<?php echo esc_url( $icon_url ); ?>') !important;
            background-size: 26px 26px !important;
            background-repeat: no-repeat !important;
            background-position: center !important;
        }

        /* CE admin notices — subtle left border accent */
        .ce-options-wrap .notice {
            border-left-color: #e8455a !important;
        }

        /* Tab active state matches CE teal */
        .ce-tab.active {
            color: #0cd4e0 !important;
            border-bottom-color: #0cd4e0 !important;
        }
    </style>
    <?php
}
add_action( 'admin_head', 'ce_admin_icon_styles' );

/**
 * Override the admin menu "CE Theme Options" dashicon with the SVG icon
 * by registering a custom icon. WordPress supports data URIs or URLs for menu icons.
 */
function ce_set_admin_menu_icon() {
    global $menu;
    if ( ! is_array( $menu ) ) return;
    foreach ( $menu as $key => $item ) {
        if ( isset( $item[2] ) && $item[2] === 'themes.php' ) {
            // The CE options page lives under Appearance — already handled via CSS
            break;
        }
    }
}
add_action( 'admin_menu', 'ce_set_admin_menu_icon', 999 );


// ── MANUAL ARTICLE PROTECTION ──────────────────────────────────────────────────
// When an article is saved through WP Admin (not via the JSON sync process),
// stamp it with _ce_manual_article=1 so the orphan-detection step in
// ce-content-sync.php never trashes it.

function ce_stamp_manual_article( int $post_id, \WP_Post $post, bool $update ): void {
    // Skip auto-saves, revisions, and posts of the wrong type
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( wp_is_post_revision( $post_id ) )                return;
    if ( $post->post_type !== 'ce_article' )              return;

    // If the sync process is currently running, this save was triggered
    // programmatically — do NOT mark it as manual.
    if ( get_transient( 'ce_sync_in_progress' ) ) return;

    // Any other save path (admin form submission, REST API, Quick Edit…)
    // is treated as a manual / intentional edit.
    update_post_meta( $post_id, '_ce_manual_article', '1' );
}
add_action( 'save_post_ce_article', 'ce_stamp_manual_article', 10, 3 );


// ── FORCE RE-SYNC ──────────────────────────────────────────────────────────────
// Adds a small "Force Article Re-sync" tool to Tools → CE Sync so editors can
// trigger a fresh sync without needing to bump the theme version number.
// Useful after updating JSON batches on an already-deployed version.

function ce_register_sync_tool_page(): void {
    add_management_page(
        __( 'CE Article Sync', 'compelling-evidence' ),
        __( 'CE Article Sync', 'compelling-evidence' ),
        'manage_options',
        'ce-article-sync',
        'ce_render_sync_tool_page'
    );
}
add_action( 'admin_menu', 'ce_register_sync_tool_page' );

function ce_render_sync_tool_page(): void {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $message = '';
    if ( isset( $_POST['ce_force_sync'] ) && check_admin_referer( 'ce_force_sync_action' ) ) {
        // Clear the version lock so the sync will fire again on the next admin_init
        $theme_version = wp_get_theme()->get( 'Version' );
        $version_key   = 'ce_content_sync_' . str_replace( '.', '_', $theme_version );
        delete_option( $version_key );
        delete_transient( 'ce_content_sync_lock' );
        $message = '<div class="notice notice-success is-dismissible"><p>'
            . esc_html__( 'Sync lock cleared. The article sync will run on your next admin page load.', 'compelling-evidence' )
            . '</p></div>';
    }

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__( 'CE Article Sync', 'compelling-evidence' ) . '</h1>';
    echo wp_kses_post( $message );
    echo '<p>' . esc_html__( 'Use this tool after updating the JSON article batch files on the server. It clears the version lock so the sync runs again on the next admin page load.', 'compelling-evidence' ) . '</p>';
    echo '<form method="post">';
    wp_nonce_field( 'ce_force_sync_action' );
    submit_button( __( 'Force Re-sync on Next Load', 'compelling-evidence' ), 'primary', 'ce_force_sync' );
    echo '</form>';
    echo '</div>';
}

// ── CE FEED REDIRECTOR ───────────────────────────────────────────────────────
// Initialise the feed redirector singleton on theme load. A named function is
// used rather than a closure so other code can remove_action() it if needed
// (e.g. plugins that want to manage feeds directly).
function ce_init_feed_redirector() {
    if ( class_exists( 'CE_Feed_Redirector' ) ) {
        CE_Feed_Redirector::get_instance();
    }
}
add_action( 'after_setup_theme', 'ce_init_feed_redirector' );


/**
 * 301 the Rights & Freedom placeholder pages to the topic they stand for.
 *
 * /rights-freedom/ and its accidental duplicates (/rights-freedom-2/ …) were
 * empty pages competing with /topic/rights-freedom/ under the same title.
 * One URL per piece of content, per Google's starter guide.
 *
 * @since 2.6.31
 */
function ce_redirect_topic_placeholder_pages() {
    if ( ! is_page() ) {
        return;
    }
    $slug = get_post_field( 'post_name', get_queried_object_id() );
    if ( preg_match( '/^rights-freedom(-\d+)?$/', (string) $slug ) ) {
        $target = get_term_link( 'rights-freedom', 'ce_topic' );
        if ( ! is_wp_error( $target ) ) {
            wp_safe_redirect( $target, 301 );
            exit;
        }
    }
}
add_action( 'template_redirect', 'ce_redirect_topic_placeholder_pages', 1 );

/**
 * Keep reader-specific pages out of the index.
 *
 * My Progress renders from the visitor's own browser storage: to a crawler it
 * is an empty shell, and to readers it is private.
 *
 * @since 2.6.31
 */
function ce_noindex_private_pages( array $robots ): array {
    if ( is_page( 'my-progress' ) ) {
        $robots['noindex'] = true;
        $robots['follow']  = true;
    }
    return $robots;
}
add_filter( 'wp_robots', 'ce_noindex_private_pages' );


/**
 * Keep redirecting and private pages out of the core XML sitemap.
 *
 * The /journey/ parent page redirects to /journeys/, the Rights & Freedom
 * placeholders redirect to their topic, and My Progress is noindexed.
 * A sitemap should list only the canonical URLs meant for search.
 *
 * @since 2.6.31
 */
function ce_sitemap_exclude_pages( array $args, string $post_type ): array {
    if ( 'page' !== $post_type ) {
        return $args;
    }
    $exclude = [];
    foreach ( [ 'journey', 'my-progress', 'rights-freedom' ] as $path ) {
        $p = get_page_by_path( $path );
        if ( $p ) {
            $exclude[] = $p->ID;
        }
    }
    global $wpdb;
    $dupes = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND post_name LIKE %s", $wpdb->esc_like( 'rights-freedom-' ) . '%' ) );
    $exclude = array_merge( $exclude, array_map( 'intval', $dupes ) );
    if ( $exclude ) {
        $args['post__not_in'] = array_merge( $args['post__not_in'] ?? [], $exclude );
    }
    return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'ce_sitemap_exclude_pages', 10, 2 );
