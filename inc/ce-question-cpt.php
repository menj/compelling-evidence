<?php
/**
 * Compelling Evidence — Question Custom Post Type & Ticketing System
 *
 * A dedicated Q&A system separate from articles.
 * Status workflow: New → In Review → Answered → Published/Archived
 *
 * @since 2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// ── REGISTER CUSTOM POST TYPE: ce_question ────────────────────────────────────

function ce_register_question_cpt() {
    $labels = [
        'name'                  => 'Questions',
        'singular_name'         => 'Question',
        'menu_name'             => 'Q&A System',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Question',
        'edit_item'             => 'Edit Question',
        'new_item'              => 'New Question',
        'view_item'             => 'View Question',
        'search_items'          => 'Search Questions',
        'not_found'             => 'No questions found',
        'not_found_in_trash'    => 'No questions found in trash',
        'all_items'             => 'All Questions',
        'attributes'            => 'Question Attributes',
    ];

    $args = [
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 25,
        'menu_icon'          => 'dashicons-format-chat',
        'supports'           => ['title', 'editor', 'author', 'comments'],
        'has_archive'        => true,
        'rewrite'            => ['slug' => 'qa'],
        'show_in_rest'       => true,
    ];

    register_post_type( 'ce_question', $args );
}
add_action( 'init', 'ce_register_question_cpt' );


// ── REGISTER QUESTION STATUS TAXONOMY ──────────────────────────────────────────

function ce_register_question_status() {
    $labels = [
        'name'          => 'Question Status',
        'singular_name' => 'Status',
        'search_items'  => 'Search Statuses',
        'all_items'     => 'All Statuses',
        'edit_item'     => 'Edit Status',
        'add_new_item'  => 'Add New Status',
    ];

    $args = [
        'labels'        => $labels,
        'hierarchical'  => false,
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => false,
        'show_in_rest'  => true,
        'meta_box_cb'   => 'ce_question_status_meta_box',
    ];

    register_taxonomy( 'ce_qstatus', ['ce_question'], $args );
}
add_action( 'init', 'ce_register_question_status' );

/**
 * Create the default question statuses once.
 *
 * Previously ran inside ce_register_question_status() on every request,
 * issuing five term lookups (and inserts, when a lookup failed) on each
 * front-end page view. Now runs in admin only, and records a flag once all
 * five terms are confirmed.
 *
 * @since 2.6.21
 */
function ce_seed_question_statuses() {
    if ( get_option( 'ce_qstatus_seeded' ) === '1' ) return;
    if ( ! taxonomy_exists( 'ce_qstatus' ) ) return;

    $statuses = [
        'new'       => 'New',
        'review'    => 'In Review',
        'answered'  => 'Answered',
        'published' => 'Published',
        'archived'  => 'Archived',
    ];

    $complete = true;
    foreach ( $statuses as $slug => $name ) {
        if ( term_exists( $slug, 'ce_qstatus' ) ) continue;
        $result = wp_insert_term( $name, 'ce_qstatus', ['slug' => $slug] );
        if ( is_wp_error( $result ) ) {
            $complete = false;
        }
    }

    if ( $complete ) {
        update_option( 'ce_qstatus_seeded', '1', false );
    }
}
add_action( 'admin_init', 'ce_seed_question_statuses' );


// ── CUSTOM STATUS META BOX (dropdown instead of checkboxes) ─────────────────

function ce_question_status_meta_box( $post ) {
    $terms = get_terms( ['taxonomy' => 'ce_qstatus', 'hide_empty' => false] );
    $current = wp_get_object_terms( $post->ID, 'ce_qstatus', ['fields' => 'ids'] );
    $current_id = ! empty( $current ) ? $current[0] : 0;
    wp_nonce_field( 'ce_question_status', 'ce_question_status_nonce' );
    ?>
    <select name="ce_qstatus" id="ce_qstatus" style="width:100%;">
        <option value="">— Select Status —</option>
        <?php foreach ( $terms as $term ) : ?>
            <option value="<?php echo esc_attr( $term->term_id ); ?>" <?php selected( $current_id, $term->term_id ); ?>>
                <?php echo esc_html( $term->name ); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <p class="description">
        <strong>New</strong> = Just submitted<br>
        <strong>In Review</strong> = Being considered<br>
        <strong>Answered</strong> = Response drafted<br>
        <strong>Published</strong> = Public Q&A visible<br>
        <strong>Archived</strong> = No action needed
    </p>
    <?php
}

function ce_save_question_status( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    // CSRF protection: verify the nonce rendered by ce_question_status_meta_box.
    // Without this, a logged-in editor visiting a crafted page could be coerced
    // into changing a question's editorial workflow state.
    if ( ! isset( $_POST['ce_question_status_nonce'] ) ) return;
    $nonce = sanitize_text_field( wp_unslash( $_POST['ce_question_status_nonce'] ) );
    if ( ! wp_verify_nonce( $nonce, 'ce_question_status' ) ) return;

    if ( ! current_user_can( 'edit_post', $post_id ) ) return;
    if ( ! isset( $_POST['ce_qstatus'] ) ) return;

    $term_id = intval( $_POST['ce_qstatus'] );
    if ( $term_id ) {
        wp_set_object_terms( $post_id, [$term_id], 'ce_qstatus' );
    }
}
add_action( 'save_post_ce_question', 'ce_save_question_status' );


// ── KEEP post_status IN SYNC WITH ce_qstatus ─────────────────────────────────
// Submitted questions land with post_status='pending'. The editorial workflow
// state is tracked by the ce_qstatus taxonomy. This sync ensures admins only
// have to flip ce_qstatus to 'published' — the post_status follows automatically
// so the post becomes publicly visible at /qa/{slug}/ at the same moment it's
// editorially published. Reverting ce_qstatus to anything else (archived, in
// review, new) demotes post_status back to 'pending', removing it from public
// view immediately.

function ce_sync_question_post_status( $object_id, $tt_ids, $taxonomy ) {
    if ( $taxonomy !== 'ce_qstatus' ) return;
    if ( get_post_type( $object_id ) !== 'ce_question' ) return;

    $current = get_post_status( $object_id );
    $statuses = wp_get_object_terms( $object_id, 'ce_qstatus', [ 'fields' => 'slugs' ] );
    if ( is_wp_error( $statuses ) ) return;

    $is_published = in_array( 'published', $statuses, true );
    $target = $is_published ? 'publish' : 'pending';

    if ( $current === $target ) return;
    if ( ! in_array( $current, [ 'pending', 'publish', 'draft' ], true ) ) return;

    // Avoid recursion: wp_update_post triggers save_post hooks which can re-fire taxonomy hooks
    remove_action( 'set_object_terms', 'ce_sync_question_post_status', 10 );
    wp_update_post( [ 'ID' => $object_id, 'post_status' => $target ] );
    add_action( 'set_object_terms', 'ce_sync_question_post_status', 10, 3 );
}
add_action( 'set_object_terms', 'ce_sync_question_post_status', 10, 3 );


// ── ADD META BOXES FOR SUBMITTER INFO ─────────────────────────────────────────

function ce_question_meta_boxes() {
    add_meta_box(
        'ce_question_submitter',
        'Submitter Information',
        'ce_question_submitter_meta_box',
        'ce_question',
        'side',
        'high'
    );

    add_meta_box(
        'ce_question_admin_notes',
        'Admin Notes (Internal)',
        'ce_question_admin_notes_meta_box',
        'ce_question',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'ce_question_meta_boxes' );

function ce_question_submitter_meta_box( $post ) {
    $name  = get_post_meta( $post->ID, '_ce_submitter_name', true );
    $email = get_post_meta( $post->ID, '_ce_submitter_email', true );
    $topic = get_post_meta( $post->ID, '_ce_question_topic', true );
    $submitted = get_post_meta( $post->ID, '_ce_submitted_date', true ) ?: $post->post_date;
    wp_nonce_field( 'ce_question_meta', 'ce_question_meta_nonce' );
    ?>
    <p>
        <label><strong>Name:</strong></label><br>
        <input type="text" name="ce_submitter_name" value="<?php echo esc_attr( $name ); ?>" style="width:100%;">
    </p>
    <p>
        <label><strong>Email:</strong></label><br>
        <input type="email" name="ce_submitter_email" value="<?php echo esc_attr( $email ); ?>" style="width:100%;">
    </p>
    <p>
        <label><strong>Topic:</strong></label><br>
        <input type="text" name="ce_question_topic" value="<?php echo esc_attr( $topic ); ?>" style="width:100%;">
    </p>
    <p>
        <label><strong>Submitted:</strong></label><br>
        <?php echo esc_html( mysql2date( 'M j, Y g:i a', $submitted ) ); ?>
    </p>
    <hr>
    <p>
        <a href="mailto:<?php echo esc_attr( $email ); ?>?subject=Re: Your Question" class="button">
            Reply via Email
        </a>
    </p>
    <?php
}

function ce_question_admin_notes_meta_box( $post ) {
    $notes = get_post_meta( $post->ID, '_ce_admin_notes', true );
    ?>
    <textarea name="ce_admin_notes" rows="5" style="width:100%;"><?php echo esc_textarea( $notes ); ?></textarea>
    <p class="description">Internal notes — not visible to public.</p>
    <?php
}

function ce_save_question_meta( $post_id ) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! isset( $_POST['ce_question_meta_nonce'] ) ) return;

    $nonce = sanitize_text_field( wp_unslash( $_POST['ce_question_meta_nonce'] ) );
    if ( ! wp_verify_nonce( $nonce, 'ce_question_meta' ) ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Per-field sanitisers — text-field for short strings, email validation for
    // submitter email, textarea-field for multi-line admin notes.
    $sanitisers = [
        'ce_submitter_name'  => 'sanitize_text_field',
        'ce_submitter_email' => 'sanitize_email',
        'ce_question_topic'  => 'sanitize_text_field',
        'ce_admin_notes'     => 'sanitize_textarea_field',
    ];

    foreach ( $sanitisers as $field => $callback ) {
        if ( isset( $_POST[ $field ] ) ) {
            $value = call_user_func( $callback, wp_unslash( $_POST[ $field ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitised by the per-field callback.
            update_post_meta( $post_id, '_' . $field, $value );
        }
    }
}
add_action( 'save_post_ce_question', 'ce_save_question_meta' );


// ── ADMIN LIST VIEW CUSTOMIZATIONS ────────────────────────────────────────────

function ce_question_columns( $columns ) {
    $new = [];
    foreach ( $columns as $key => $label ) {
        if ( $key === 'title' ) {
            $new[$key] = 'Question';
            $new['status'] = 'Status';
            $new['submitter'] = 'Submitter';
            $new['topic'] = 'Topic';
            $new['date'] = 'Submitted';
        } else {
            $new[$key] = $label;
        }
    }
    return $new;
}
add_filter( 'manage_ce_question_posts_columns', 'ce_question_columns' );

function ce_question_column_content( $column, $post_id ) {
    switch ( $column ) {
        case 'status':
            $statuses = get_the_terms( $post_id, 'ce_qstatus' );
            if ( $statuses && ! is_wp_error( $statuses ) ) {
                $status = $statuses[0];
                $colors = [
                    'new'       => '#ff6b6b',
                    'review'    => '#f5c518',
                    'answered'  => '#4ecdc4',
                    'published' => '#2ecc71',
                    'archived'  => '#95a5a6',
                ];
                $color = $colors[$status->slug] ?? '#ccc';
                echo '<span style="background:' . esc_attr( $color ) . ';color:#000;padding:3px 8px;border-radius:3px;font-size:11px;font-weight:bold;">' . esc_html( $status->name ) . '</span>';
            } else {
                echo '<span style="background:#ff6b6b;color:#fff;padding:3px 8px;border-radius:3px;font-size:11px;">New</span>';
            }
            break;

        case 'submitter':
            $name = get_post_meta( $post_id, '_ce_submitter_name', true );
            $email = get_post_meta( $post_id, '_ce_submitter_email', true );
            echo esc_html( $name ) . '<br><small>' . esc_html( $email ) . '</small>';
            break;

        case 'topic':
            $topic = get_post_meta( $post_id, '_ce_question_topic', true );
            echo esc_html( $topic ?: '—' );
            break;
    }
}
add_action( 'manage_ce_question_posts_custom_column', 'ce_question_column_content', 10, 2 );

function ce_question_sortable_columns( $columns ) {
    $columns['status'] = 'status';
    $columns['submitter'] = 'submitter';
    return $columns;
}
add_filter( 'manage_edit-ce_question_sortable_columns', 'ce_question_sortable_columns' );


// ── DASHBOARD WIDGET: New Questions ─────────────────────────────────────────

function ce_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'ce_questions_widget',
        'New Questions',
        'ce_dashboard_questions_widget'
    );
}
add_action( 'wp_dashboard_setup', 'ce_add_dashboard_widget' );

function ce_dashboard_questions_widget() {
    $new_term = get_term_by( 'slug', 'new', 'ce_qstatus' );
    $query = new WP_Query([
        'post_type' => 'ce_question',
        'posts_per_page' => 5,
        'tax_query' => [
            [
                'taxonomy' => 'ce_qstatus',
                'field' => 'slug',
                'terms' => 'new',
            ],
        ],
    ]);

    if ( ! $query->have_posts() ) {
        echo '<p>No new questions. All caught up!</p>';
        return;
    }

    echo '<ul style="margin:0;">';
    while ( $query->have_posts() ) {
        $query->the_post();
        $name = get_post_meta( get_the_ID(), '_ce_submitter_name', true );
        echo '<li style="margin-bottom:10px;padding-bottom:10px;border-bottom:1px solid #eee;">';
        echo '<a href="' . esc_url( get_edit_post_link() ) . '" style="font-weight:bold;">' . esc_html( wp_trim_words( get_the_title(), 8 ) ) . '</a>';
        echo '<br><small>by ' . esc_html( $name ) . ' — ' . esc_html( human_time_diff( get_the_time('U') ) ) . ' ago</small>';
        echo '</li>';
    }
    echo '</ul>';
    echo '<p style="margin-top:10px;"><a href="' . esc_url( admin_url('edit.php?post_type=ce_question') ) . '" class="button">View All Questions</a></p>';
    wp_reset_postdata();
}


// ── NOTIFICATION EMAIL TO ADMIN ────────────────────────────────────────────────

function ce_notify_new_question( $post_id, $post, $update ) {
    if ( $update ) return; // Only on new questions
    if ( $post->post_type !== 'ce_question' ) return;
    if ( defined( 'CE_IMPORTING' ) && CE_IMPORTING ) return;

    $name = get_post_meta( $post_id, '_ce_submitter_name', true );
    $email = get_post_meta( $post_id, '_ce_submitter_email', true );
    $topic = get_post_meta( $post_id, '_ce_question_topic', true );

    $admin_email = get_option( 'admin_email' );
    $subject = sprintf( '[%s] New Question Submitted', get_bloginfo( 'name' ) );
    $body = "A new question has been submitted:\n\n";
    $body .= "From: {$name} <{$email}>\n";
    $body .= "Topic: {$topic}\n";
    $body .= "Question:\n" . $post->post_content . "\n\n";
    $body .= "Review and respond:\n" . admin_url( "post.php?post={$post_id}&action=edit" ) . "\n\n";
    $body .= "View all questions:\n" . admin_url( "edit.php?post_type=ce_question" );

    wp_mail( $admin_email, $subject, $body );
}
add_action( 'wp_insert_post', 'ce_notify_new_question', 10, 3 );


// ── PUBLIC VISIBILITY GATE ────────────────────────────────────────────────────
// A submitted question is created with post_status='pending' and ce_qstatus='new'
// — neither WordPress visibility nor editorial workflow exposes it publicly until
// an admin reviews and publishes it. This gate is defence-in-depth in case any
// future code path inadvertently sets post_status='publish' before review:
// even then, the qstatus term must be 'published' for non-admins to see the page.

function ce_question_visibility_gate() {
    if ( ! is_singular( 'ce_question' ) ) return;

    // Editors and above can preview at any workflow stage
    if ( current_user_can( 'edit_posts' ) ) return;

    $post_id  = get_queried_object_id();
    if ( ! $post_id ) return;

    $statuses = wp_get_object_terms( $post_id, 'ce_qstatus', [ 'fields' => 'slugs' ] );
    if ( is_wp_error( $statuses ) || ! in_array( 'published', $statuses, true ) ) {
        global $wp_query;
        $wp_query->set_404();
        status_header( 404 );
        nocache_headers();
        $template = get_query_template( '404' );
        if ( $template ) include $template;
        exit;
    }
}
add_action( 'template_redirect', 'ce_question_visibility_gate' );

// Belt-and-suspenders: also filter the public archive query so only ce_qstatus='published'
// items appear in /qa/ — the archive template already does this in its own WP_Query, but
// applying it at pre_get_posts protects feeds, REST queries, and main-query renders too.

function ce_question_archive_filter( $query ) {
    if ( is_admin() || ! $query->is_main_query() ) return;
    if ( ! $query->is_post_type_archive( 'ce_question' ) ) return;

    $existing = (array) $query->get( 'tax_query' );
    $existing[] = [
        'taxonomy' => 'ce_qstatus',
        'field'    => 'slug',
        'terms'    => 'published',
    ];
    $query->set( 'tax_query', $existing );
}
add_action( 'pre_get_posts', 'ce_question_archive_filter' );


// ── FLUSH REWRITE RULES FOR NEW CPT ───────────────────────────────────────────

function ce_question_flush_rewrites() {
    ce_register_question_cpt();
    ce_register_question_status();
    ce_seed_question_statuses();
    flush_rewrite_rules();
}
add_action( 'after_switch_theme', 'ce_question_flush_rewrites' );
