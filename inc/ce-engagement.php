<?php
/**
 * Compelling Evidence — Engagement System
 *
 * Features:
 * - Up/down voting on articles
 * - Resonance feedback ("addressed my question" / "still have questions" / "want to discuss")
 * - Social share bar
 * - "Most Resonant" widget
 *
 * Storage: Transients (fast reads) + post meta (aggregate counts) + custom table (individual records)
 *
 * @since 1.7.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;


/* ═══════════════════════════════════════════════════════════════════════
   DATABASE
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Create custom table on theme activation.
 */
function ce_engagement_create_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'ce_engagement';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$table} (
        id          BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        post_id     BIGINT(20) UNSIGNED NOT NULL,
        user_id     BIGINT(20) UNSIGNED DEFAULT 0,
        ip_hash     VARCHAR(64) NOT NULL DEFAULT '',
        action_type VARCHAR(20) NOT NULL,
        created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_post_action (post_id, action_type),
        KEY idx_ip_post (ip_hash, post_id, action_type)
    ) {$charset};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );

    update_option( 'ce_engagement_db_version', '1.0' );
}
add_action( 'after_switch_theme', 'ce_engagement_create_table' );

// Also run on init if table doesn't exist yet (first load after theme update)
function ce_engagement_maybe_create_table() {
    if ( get_option( 'ce_engagement_db_version' ) !== '1.0' ) {
        ce_engagement_create_table();
    }
}
add_action( 'init', 'ce_engagement_maybe_create_table' );


/* ═══════════════════════════════════════════════════════════════════════
   HELPER FUNCTIONS
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Get hashed IP for anonymous duplicate prevention.
 */
function ce_get_ip_hash() {
    // Resolve real client IP behind CDN/reverse proxy.
    // Priority: Cloudflare > X-Real-IP > X-Forwarded-For > REMOTE_ADDR
    $ip = '0.0.0.0';
    if ( ! empty( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_REAL_IP'] ) ) {
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    } elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
        // X-Forwarded-For can contain multiple IPs — take the first (client)
        $parts = explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] );
        $ip = trim( $parts[0] );
    } elseif ( ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // Validate IP format to prevent header injection
    if ( ! filter_var( $ip, FILTER_VALIDATE_IP ) ) {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    return hash( 'sha256', $ip . wp_salt( 'auth' ) );
}

/**
 * Simple transient-based rate limiter.
 * Returns true if the request should be blocked.
 *
 * @param string $action   Action name (e.g. 'vote', 'search', 'resonance')
 * @param int    $limit    Max requests per window
 * @param int    $window   Window in seconds (default: 60)
 * @return bool  True if rate limit exceeded
 */
function ce_rate_limited( $action, $limit = 10, $window = 60 ) {
    $ip_hash = ce_get_ip_hash();
    $key     = 'ce_rl_' . $action . '_' . substr( $ip_hash, 0, 12 );
    $count   = (int) get_transient( $key );

    if ( $count >= $limit ) {
        return true;
    }

    set_transient( $key, $count + 1, $window );
    return false;
}

/**
 * Check if current visitor already performed an action on a post.
 */
function ce_has_acted( $post_id, $action_type ) {
    global $wpdb;
    $table   = $wpdb->prefix . 'ce_engagement';
    $user_id = get_current_user_id();
    $ip_hash = ce_get_ip_hash();

    if ( $user_id ) {
        $exists = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$table} WHERE post_id = %d AND user_id = %d AND action_type = %s LIMIT 1",
            $post_id, $user_id, $action_type
        ) );
    } else {
        $exists = $wpdb->get_var( $wpdb->prepare(
            "SELECT id FROM {$table} WHERE post_id = %d AND ip_hash = %s AND action_type = %s LIMIT 1",
            $post_id, $ip_hash, $action_type
        ) );
    }

    return (bool) $exists;
}

/**
 * Check if visitor has voted at all (up OR down) on a post.
 */
function ce_has_voted( $post_id ) {
    return ce_has_acted( $post_id, 'vote_up' ) || ce_has_acted( $post_id, 'vote_down' );
}

/**
 * Check if visitor has given resonance feedback on a post.
 */
function ce_has_resonated( $post_id ) {
    return ce_has_acted( $post_id, 'res_addressed' )
        || ce_has_acted( $post_id, 'res_questions' )
        || ce_has_acted( $post_id, 'res_discuss' );
}

/**
 * Record an engagement action and update aggregates.
 */
function ce_record_action( $post_id, $action_type ) {
    global $wpdb;
    $table = $wpdb->prefix . 'ce_engagement';

    $wpdb->insert( $table, [
        'post_id'     => $post_id,
        'user_id'     => get_current_user_id(),
        'ip_hash'     => ce_get_ip_hash(),
        'action_type' => $action_type,
    ], [ '%d', '%d', '%s', '%s' ] );

    // Update post meta aggregate
    $meta_key = '_ce_' . $action_type;
    $current  = (int) get_post_meta( $post_id, $meta_key, true );
    update_post_meta( $post_id, $meta_key, $current + 1 );

    // Invalidate transient cache
    delete_transient( 'ce_engagement_' . $post_id );
    delete_transient( 'ce_most_resonant' );

    return $current + 1;
}

/**
 * Get all engagement counts for a post (uses transient cache).
 */
function ce_get_engagement( $post_id ) {
    $cached = get_transient( 'ce_engagement_' . $post_id );
    if ( $cached !== false ) {
        return $cached;
    }

    $data = [
        'vote_up'        => (int) get_post_meta( $post_id, '_ce_vote_up', true ),
        'vote_down'      => (int) get_post_meta( $post_id, '_ce_vote_down', true ),
        'res_addressed'  => (int) get_post_meta( $post_id, '_ce_res_addressed', true ),
        'res_questions'  => (int) get_post_meta( $post_id, '_ce_res_questions', true ),
        'res_discuss'    => (int) get_post_meta( $post_id, '_ce_res_discuss', true ),
    ];
    $data['vote_score']      = $data['vote_up'] - $data['vote_down'];
    $data['vote_total']      = $data['vote_up'] + $data['vote_down'];
    $data['resonance_total'] = $data['res_addressed'] + $data['res_questions'] + $data['res_discuss'];

    set_transient( 'ce_engagement_' . $post_id, $data, HOUR_IN_SECONDS );

    return $data;
}


/* ═══════════════════════════════════════════════════════════════════════
   AJAX HANDLERS
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Handle vote AJAX request.
 */
function ce_ajax_vote() {
    check_ajax_referer( 'ce_nonce', 'nonce' );

    if ( ce_rate_limited( 'vote', 15, 60 ) ) {
        wp_send_json_error( [ 'message' => 'Too many requests. Please wait a moment.' ] );
    }

    $post_id   = absint( $_POST['post_id'] ?? 0 );
    $direction = sanitize_key( $_POST['direction'] ?? '' );

    if ( ! $post_id || ! in_array( $direction, [ 'up', 'down' ], true ) ) {
        wp_send_json_error( [ 'message' => 'Invalid request.' ] );
    }

    if ( ! get_post( $post_id ) ) {
        wp_send_json_error( [ 'message' => 'Post not found.' ] );
    }

    $action_type = 'vote_' . $direction;

    if ( ce_has_voted( $post_id ) ) {
        wp_send_json_error( [ 'message' => 'Already voted.' ] );
    }

    $new_count = ce_record_action( $post_id, $action_type );
    $data      = ce_get_engagement( $post_id );

    wp_send_json_success( [
        'score'    => $data['vote_score'],
        'up'       => $data['vote_up'],
        'down'     => $data['vote_down'],
        'message'  => 'Vote recorded.',
    ] );
}
add_action( 'wp_ajax_ce_vote',        'ce_ajax_vote' );
add_action( 'wp_ajax_nopriv_ce_vote', 'ce_ajax_vote' );


/**
 * Handle resonance feedback AJAX request.
 */
function ce_ajax_resonance() {
    check_ajax_referer( 'ce_nonce', 'nonce' );

    if ( ce_rate_limited( 'resonance', 15, 60 ) ) {
        wp_send_json_error( [ 'message' => 'Too many requests. Please wait a moment.' ] );
    }

    $post_id = absint( $_POST['post_id'] ?? 0 );
    $type    = sanitize_key( $_POST['type'] ?? '' );

    if ( ! $post_id || ! in_array( $type, [ 'addressed', 'questions', 'discuss' ], true ) ) {
        wp_send_json_error( [ 'message' => 'Invalid request.' ] );
    }

    if ( ! get_post( $post_id ) ) {
        wp_send_json_error( [ 'message' => 'Post not found.' ] );
    }

    $action_type = 'res_' . $type;

    if ( ce_has_resonated( $post_id ) ) {
        wp_send_json_error( [ 'message' => 'Already submitted feedback.' ] );
    }

    $new_count = ce_record_action( $post_id, $action_type );
    $data      = ce_get_engagement( $post_id );

    wp_send_json_success( [
        'addressed' => $data['res_addressed'],
        'questions' => $data['res_questions'],
        'discuss'   => $data['res_discuss'],
        'message'   => 'Feedback recorded.',
    ] );
}
add_action( 'wp_ajax_ce_resonance',        'ce_ajax_resonance' );
add_action( 'wp_ajax_nopriv_ce_resonance', 'ce_ajax_resonance' );


/* ═══════════════════════════════════════════════════════════════════════
   RENDER FUNCTIONS
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Render the social share bar.
 * 8 platforms (icon-only) + native share on mobile.
 */
function ce_render_share_bar( $post_id = null ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $url   = esc_url( get_permalink( $post_id ) );
    $title = esc_attr( get_the_title( $post_id ) );
    $text  = esc_attr( wp_trim_words( get_the_excerpt( $post_id ), 20, '...' ) );
    ?>
    <div class="ce-share-bar" data-url="<?php echo $url; ?>" data-title="<?php echo $title; ?>">
        <span class="ce-share-label">Share</span>
        <div class="ce-share-buttons">
            <!-- WhatsApp -->
            <button class="ce-share-btn ce-share-wa" title="WhatsApp"
                onclick="window.open('https://wa.me/?text='+encodeURIComponent('<?php echo $title; ?> — <?php echo $url; ?>'),'_blank')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
            </button>
            <!-- Telegram -->
            <button class="ce-share-btn ce-share-tg" title="Telegram"
                onclick="window.open('https://t.me/share/url?url='+encodeURIComponent('<?php echo $url; ?>')+'&text='+encodeURIComponent('<?php echo $title; ?>'),'_blank','width=550,height=420')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.96 6.504-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
            </button>
            <!-- Facebook -->
            <button class="ce-share-btn ce-share-fb" title="Facebook"
                onclick="window.open('https://www.facebook.com/sharer/sharer.php?u='+encodeURIComponent('<?php echo $url; ?>'),'_blank','width=550,height=420')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
            </button>
            <!-- X / Twitter -->
            <button class="ce-share-btn ce-share-x" title="X / Twitter"
                onclick="window.open('https://twitter.com/intent/tweet?text='+encodeURIComponent('<?php echo $title; ?>')+' '+encodeURIComponent('<?php echo $url; ?>'),'_blank','width=550,height=420')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
            </button>
            <!-- Reddit -->
            <button class="ce-share-btn ce-share-reddit" title="Reddit"
                onclick="window.open('https://www.reddit.com/submit?url='+encodeURIComponent('<?php echo $url; ?>')+'&title='+encodeURIComponent('<?php echo $title; ?>'),'_blank','width=550,height=600')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0zm5.01 4.744c.688 0 1.25.561 1.25 1.249a1.25 1.25 0 0 1-2.498.056l-2.597-.547-.8 3.747c1.824.07 3.48.632 4.674 1.488.308-.309.73-.491 1.207-.491.968 0 1.754.786 1.754 1.754 0 .716-.435 1.333-1.01 1.614a3.111 3.111 0 0 1 .042.52c0 2.694-3.13 4.87-7.004 4.87-3.874 0-7.004-2.176-7.004-4.87 0-.183.015-.366.043-.534A1.748 1.748 0 0 1 4.028 12c0-.968.786-1.754 1.754-1.754.463 0 .898.196 1.207.49 1.207-.883 2.878-1.43 4.744-1.487l.885-4.182a.342.342 0 0 1 .14-.197.35.35 0 0 1 .238-.042l2.906.617a1.214 1.214 0 0 1 1.108-.701zM9.25 12C8.561 12 8 12.562 8 13.25c0 .687.561 1.248 1.25 1.248.687 0 1.248-.561 1.248-1.249 0-.688-.561-1.249-1.249-1.249zm5.5 0c-.687 0-1.248.561-1.248 1.25 0 .687.561 1.248 1.249 1.248.688 0 1.249-.561 1.249-1.249 0-.687-.562-1.249-1.25-1.249zm-5.466 3.99a.327.327 0 0 0-.231.094.33.33 0 0 0 0 .463c.842.842 2.484.913 2.961.913.477 0 2.105-.056 2.961-.913a.361.361 0 0 0 .029-.463.33.33 0 0 0-.464 0c-.547.533-1.684.73-2.512.73-.828 0-1.979-.196-2.512-.73a.326.326 0 0 0-.232-.095z"/></svg>
            </button>
            <!-- Threads -->
            <button class="ce-share-btn ce-share-threads" title="Threads"
                onclick="window.open('https://www.threads.net/intent/post?text='+encodeURIComponent('<?php echo $title; ?> <?php echo $url; ?>'),'_blank','width=550,height=420')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.186 24h-.007c-3.581-.024-6.334-1.205-8.184-3.509C2.35 18.44 1.5 15.586 1.472 12.01v-.017c.03-3.579.879-6.43 2.525-8.482C5.845 1.205 8.6.024 12.18 0h.014c2.746.02 5.043.725 6.826 2.098 1.677 1.29 2.858 3.13 3.509 5.467l-2.04.569c-1.104-3.96-3.898-5.984-8.304-6.015-2.91.022-5.11.936-6.54 2.717C4.307 6.504 3.616 8.914 3.589 12c.027 3.086.718 5.496 2.057 7.164 1.43 1.783 3.631 2.698 6.54 2.717 2.623-.02 4.358-.631 5.8-2.045 1.647-1.613 1.618-3.593 1.09-4.798-.31-.71-.873-1.3-1.634-1.75-.192 1.352-.622 2.446-1.284 3.272-.886 1.102-2.14 1.704-3.73 1.79-1.202.065-2.361-.218-3.259-.801-1.063-.689-1.685-1.74-1.752-2.96-.065-1.187.408-2.26 1.33-3.017.88-.724 2.104-1.126 3.449-1.13h.036c1.16.006 2.136.283 2.907.823.43.3.78.674 1.05 1.107.165-.404.262-.858.282-1.37l2.104.079c-.058 1.394-.515 2.545-1.233 3.428.466.391.86.862 1.165 1.41.743 1.333.932 3.052.55 4.607-.595 2.432-2.574 4.41-5.42 5.43C15.584 23.676 13.927 24 12.186 24zm.068-8.27h-.023c-1.548.006-2.636.707-2.583 1.665.024.428.253.822.685 1.078.529.312 1.25.45 2.03.405 1.1-.06 1.955-.452 2.542-1.17.407-.498.69-1.132.835-1.882-.556-.277-1.19-.45-1.887-.483a8.417 8.417 0 0 0-.347-.008l-.046.001-.054-.001a9.467 9.467 0 0 0-.152-.005z"/></svg>
            </button>
            <!-- Email -->
            <button class="ce-share-btn ce-share-email" title="Email"
                onclick="window.location.href='mailto:?subject='+encodeURIComponent('<?php echo $title; ?>')+'&body='+encodeURIComponent('<?php echo $title; ?> — <?php echo $url; ?>')">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            </button>
            <!-- Copy Link -->
            <button class="ce-share-btn ce-share-copy" title="Copy link" data-url="<?php echo $url; ?>">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            </button>
            <!-- Native Share (mobile) -->
            <button class="ce-share-btn ce-share-native" title="More">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg>
            </button>
        </div>
    </div>
    <?php
}

/**
 * Render the vote widget.
 */
function ce_render_vote_widget( $post_id = null ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $data     = ce_get_engagement( $post_id );
    $voted    = ce_has_voted( $post_id );
    $cls_vote = $voted ? ' ce-voted' : '';
    ?>
    <div class="ce-vote-widget<?php echo $cls_vote; ?>" data-post-id="<?php echo $post_id; ?>">
        <button class="ce-vote-btn ce-vote-up<?php echo ce_has_acted($post_id,'vote_up') ? ' active' : ''; ?>"
                data-direction="up" <?php echo $voted ? 'disabled' : ''; ?>>
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
        </button>
        <span class="ce-vote-score" id="ce-vote-score-<?php echo $post_id; ?>">
            <?php echo $data['vote_score']; ?>
        </span>
        <button class="ce-vote-btn ce-vote-down<?php echo ce_has_acted($post_id,'vote_down') ? ' active' : ''; ?>"
                data-direction="down" <?php echo $voted ? 'disabled' : ''; ?>>
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
        </button>
    </div>
    <?php
}

/**
 * Render the resonance feedback panel.
 */
function ce_render_resonance( $post_id = null ) {
    if ( ! $post_id ) $post_id = get_the_ID();
    $data      = ce_get_engagement( $post_id );
    $responded = ce_has_resonated( $post_id );
    $cls       = $responded ? ' ce-resonated' : '';
    ?>
    <div class="ce-resonance<?php echo $cls; ?>" data-post-id="<?php echo $post_id; ?>">
        <h4 class="ce-resonance-title">Did this article help?</h4>
        <?php if ( $responded ) : ?>
            <p class="ce-resonance-thanks">Thank you for your feedback.</p>
            <div class="ce-resonance-results">
                <div class="ce-res-stat">
                    <span class="ce-res-count"><?php echo $data['res_addressed']; ?></span>
                    <span class="ce-res-label">found it helpful</span>
                </div>
                <div class="ce-res-stat">
                    <span class="ce-res-count"><?php echo $data['res_questions']; ?></span>
                    <span class="ce-res-label">still have questions</span>
                </div>
                <div class="ce-res-stat">
                    <span class="ce-res-count"><?php echo $data['res_discuss']; ?></span>
                    <span class="ce-res-label">want to discuss</span>
                </div>
            </div>
        <?php else : ?>
            <div class="ce-resonance-options">
                <button class="ce-res-btn" data-type="addressed">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                    <span>This addressed my question</span>
                </button>
                <button class="ce-res-btn" data-type="questions">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span>I still have questions</span>
                </button>
                <button class="ce-res-btn" data-type="discuss">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <span>I want to discuss this</span>
                </button>
            </div>
        <?php endif; ?>
    </div>
    <?php
}


/* ═══════════════════════════════════════════════════════════════════════
   MOST RESONANT WIDGET
   ═══════════════════════════════════════════════════════════════════════ */

class CE_Most_Resonant_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'ce_most_resonant',
            'CE: Most Resonant Articles',
            [ 'description' => 'Shows articles with the most positive engagement.' ]
        );
    }

    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] ?? 'Most Helpful Articles' );
        $count = (int) ( $instance['count'] ?? 5 );

        $cached = get_transient( 'ce_most_resonant' );
        if ( $cached === false ) {
            // Query posts with highest vote_up + res_addressed
            $query = new WP_Query( [
                'post_type'      => 'ce_article',
                'posts_per_page' => $count,
                'meta_key'       => '_ce_vote_up',
                'orderby'        => 'meta_value_num',
                'order'          => 'DESC',
                'meta_query'     => [
                    [
                        'key'     => '_ce_vote_up',
                        'value'   => '0',
                        'compare' => '>',
                        'type'    => 'NUMERIC',
                    ],
                ],
            ] );

            $cached = [];
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $data = ce_get_engagement( get_the_ID() );
                    $cached[] = [
                        'id'    => get_the_ID(),
                        'title' => get_the_title(),
                        'url'   => get_permalink(),
                        'score' => $data['vote_score'],
                        'helped' => $data['res_addressed'],
                    ];
                }
                wp_reset_postdata();
            }
            set_transient( 'ce_most_resonant', $cached, 2 * HOUR_IN_SECONDS );
        }

        if ( empty( $cached ) ) return;

        echo $args['before_widget'];
        if ( $title ) echo $args['before_title'] . $title . $args['after_title'];
        ?>
        <ul class="ce-most-resonant-list">
            <?php foreach ( array_slice( $cached, 0, $count ) as $item ) : ?>
                <li class="ce-mr-item">
                    <a href="<?php echo esc_url( $item['url'] ); ?>">
                        <span class="ce-mr-title"><?php echo esc_html( $item['title'] ); ?></span>
                        <span class="ce-mr-meta">
                            <?php if ( $item['score'] > 0 ) : ?>
                                <span class="ce-mr-score">+<?php echo $item['score']; ?></span>
                            <?php endif; ?>
                            <?php if ( $item['helped'] > 0 ) : ?>
                                <span class="ce-mr-helped"><?php echo $item['helped']; ?> found helpful</span>
                            <?php endif; ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $title = $instance['title'] ?? 'Most Helpful Articles';
        $count = $instance['count'] ?? 5;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>"
                   value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>">Number of articles:</label>
            <input type="number" class="tiny-text" id="<?php echo $this->get_field_id('count'); ?>"
                   name="<?php echo $this->get_field_name('count'); ?>"
                   value="<?php echo esc_attr( $count ); ?>" min="1" max="20" />
        </p>
        <?php
    }

    public function update( $new, $old ) {
        return [
            'title' => sanitize_text_field( $new['title'] ),
            'count' => absint( $new['count'] ),
        ];
    }
}

function ce_register_engagement_widget() {
    register_widget( 'CE_Most_Resonant_Widget' );
}
add_action( 'widgets_init', 'ce_register_engagement_widget' );


/* ═══════════════════════════════════════════════════════════════════════
   ENQUEUE ASSETS
   ═══════════════════════════════════════════════════════════════════════ */

function ce_enqueue_engagement_assets() {
    if ( ! is_singular( 'ce_article' ) && ! is_singular( 'post' ) ) return;

    $dir = get_stylesheet_directory_uri();
    $ver = wp_get_theme()->get( 'Version' );

    wp_enqueue_style(
        'ce-engagement',
        $dir . '/assets/css/ce-engagement.css',
        [ 'ce-main' ],
        $ver
    );

    wp_enqueue_script(
        'ce-engagement',
        $dir . '/assets/js/ce-engagement.js',
        [],
        $ver,
        true
    );

    wp_localize_script( 'ce-engagement', 'CE_Engage', [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'ce_nonce' ),
        'postId'  => get_the_ID(),
    ] );
}
add_action( 'wp_enqueue_scripts', 'ce_enqueue_engagement_assets' );
