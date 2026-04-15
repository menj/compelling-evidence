<?php
/**
 * Compelling Evidence — Analytics System
 *
 * Privacy-first, server-side analytics. No cookies, no PII, no third-party.
 * Tracks: page views, quiz funnel, journey progress, article engagement,
 * search queries, and content performance.
 *
 * Storage: Custom database table (ce_analytics).
 * Dashboard: Admin page under Compelling Evidence menu.
 *
 * @since 2.2.71
 */

if ( ! defined( 'ABSPATH' ) ) exit;


/* ═══════════════════════════════════════════════════════════════════════
   DATABASE
   ═══════════════════════════════════════════════════════════════════════ */

function ce_analytics_create_table() {
    global $wpdb;
    $table   = $wpdb->prefix . 'ce_analytics';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS {$table} (
        id           BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        event_type   VARCHAR(40) NOT NULL,
        event_data   TEXT DEFAULT NULL,
        visitor_hash VARCHAR(64) NOT NULL DEFAULT '',
        session_id   VARCHAR(32) NOT NULL DEFAULT '',
        page_url     VARCHAR(255) DEFAULT '',
        referrer     VARCHAR(255) DEFAULT '',
        device_type  VARCHAR(10) DEFAULT 'desktop',
        created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY idx_event_type (event_type),
        KEY idx_created (created_at),
        KEY idx_visitor (visitor_hash),
        KEY idx_session (session_id)
    ) {$charset};";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}
add_action( 'after_switch_theme', 'ce_analytics_create_table' );

// Run on admin init to ensure table exists
function ce_analytics_maybe_create_table() {
    if ( get_option( 'ce_analytics_table_version' ) === '1.0' ) return;
    ce_analytics_create_table();
    update_option( 'ce_analytics_table_version', '1.0' );
}
add_action( 'admin_init', 'ce_analytics_maybe_create_table' );


/* ═══════════════════════════════════════════════════════════════════════
   EVENT RECORDING
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Record an analytics event.
 *
 * @param string $type  Event type (e.g. 'pageview', 'quiz_complete', 'journey_screen')
 * @param array  $data  Event-specific data (stored as JSON)
 * @param string $session_id  Client session ID
 */
function ce_analytics_record( $type, $data = [], $session_id = '' ) {
    global $wpdb;
    $table = $wpdb->prefix . 'ce_analytics';

    $visitor_hash = function_exists( 'ce_get_ip_hash' ) ? ce_get_ip_hash() : '';

    // Detect device type from User-Agent
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $device = 'desktop';
    if ( preg_match( '/Mobile|Android|iPhone|iPad/i', $ua ) ) {
        $device = preg_match( '/iPad|Tablet/i', $ua ) ? 'tablet' : 'mobile';
    }

    $wpdb->insert( $table, [
        'event_type'   => sanitize_key( $type ),
        'event_data'   => wp_json_encode( $data ),
        'visitor_hash' => $visitor_hash,
        'session_id'   => sanitize_key( substr( $session_id, 0, 32 ) ),
        'page_url'     => esc_url_raw( substr( $_SERVER['HTTP_REFERER'] ?? '', 0, 255 ) ),
        'referrer'     => esc_url_raw( substr( $_SERVER['HTTP_REFERER'] ?? '', 0, 255 ) ),
        'device_type'  => $device,
    ], [ '%s', '%s', '%s', '%s', '%s', '%s', '%s' ] );
}


/* ═══════════════════════════════════════════════════════════════════════
   AJAX ENDPOINT — receives events from frontend JS
   ═══════════════════════════════════════════════════════════════════════ */

function ce_ajax_analytics() {
    check_ajax_referer( 'ce_nonce', 'nonce' );

    if ( ce_rate_limited( 'analytics', 30, 60 ) ) {
        wp_send_json_error( 'Rate limited' );
    }

    $type       = sanitize_key( $_POST['event_type'] ?? '' );
    $data       = json_decode( stripslashes( $_POST['event_data'] ?? '{}' ), true );
    $session_id = sanitize_key( $_POST['session_id'] ?? '' );

    if ( ! $type || ! is_array( $data ) ) {
        wp_send_json_error( 'Invalid event' );
    }

    // Whitelist event types
    $valid_types = [
        'pageview', 'quiz_start', 'quiz_answer', 'quiz_complete', 'quiz_abandon',
        'journey_start', 'journey_screen', 'journey_complete',
        'article_view', 'article_scroll', 'article_complete',
        'search_query', 'search_click',
    ];

    if ( ! in_array( $type, $valid_types, true ) ) {
        wp_send_json_error( 'Unknown event type' );
    }

    // Sanitize data values
    $clean_data = [];
    foreach ( $data as $k => $v ) {
        $key = sanitize_key( $k );
        $clean_data[ $key ] = is_numeric( $v ) ? $v : sanitize_text_field( (string) $v );
    }

    ce_analytics_record( $type, $clean_data, $session_id );
    wp_send_json_success();
}
add_action( 'wp_ajax_ce_analytics',        'ce_ajax_analytics' );
add_action( 'wp_ajax_nopriv_ce_analytics', 'ce_ajax_analytics' );


/* ═══════════════════════════════════════════════════════════════════════
   QUERY HELPERS — for the dashboard
   ═══════════════════════════════════════════════════════════════════════ */

function ce_analytics_count( $type, $days = 30, $extra_where = '' ) {
    global $wpdb;
    $table = $wpdb->prefix . 'ce_analytics';
    $since = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );
    return (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(*) FROM {$table} WHERE event_type = %s AND created_at >= %s {$extra_where}",
        $type, $since
    ) );
}

function ce_analytics_unique_visitors( $days = 30 ) {
    global $wpdb;
    $table = $wpdb->prefix . 'ce_analytics';
    $since = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );
    return (int) $wpdb->get_var( $wpdb->prepare(
        "SELECT COUNT(DISTINCT visitor_hash) FROM {$table} WHERE created_at >= %s",
        $since
    ) );
}

function ce_analytics_top_items( $type, $data_key, $days = 30, $limit = 15 ) {
    global $wpdb;
    $table = $wpdb->prefix . 'ce_analytics';
    $since = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );
    $json_path = '$.' . $data_key;
    return $wpdb->get_results( $wpdb->prepare(
        "SELECT JSON_UNQUOTE(JSON_EXTRACT(event_data, %s)) AS item, COUNT(*) AS total
         FROM {$table}
         WHERE event_type = %s AND created_at >= %s
         GROUP BY item
         ORDER BY total DESC
         LIMIT %d",
        $json_path, $type, $since, $limit
    ) );
}

function ce_analytics_daily_counts( $type, $days = 30 ) {
    global $wpdb;
    $table = $wpdb->prefix . 'ce_analytics';
    $since = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );
    return $wpdb->get_results( $wpdb->prepare(
        "SELECT DATE(created_at) AS day, COUNT(*) AS total
         FROM {$table}
         WHERE event_type = %s AND created_at >= %s
         GROUP BY day
         ORDER BY day ASC",
        $type, $since
    ) );
}

function ce_analytics_persona_distribution( $days = 30 ) {
    return ce_analytics_top_items( 'quiz_complete', 'persona', $days, 14 );
}

function ce_analytics_device_breakdown( $days = 30 ) {
    global $wpdb;
    $table = $wpdb->prefix . 'ce_analytics';
    $since = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );
    return $wpdb->get_results( $wpdb->prepare(
        "SELECT device_type, COUNT(*) AS total
         FROM {$table}
         WHERE event_type = 'pageview' AND created_at >= %s
         GROUP BY device_type
         ORDER BY total DESC",
        $since
    ) );
}

function ce_analytics_quiz_funnel( $days = 30 ) {
    return [
        'started'   => ce_analytics_count( 'quiz_start', $days ),
        'completed' => ce_analytics_count( 'quiz_complete', $days ),
        'abandoned' => ce_analytics_count( 'quiz_abandon', $days ),
    ];
}

function ce_analytics_journey_funnel( $days = 30 ) {
    return [
        'started'   => ce_analytics_count( 'journey_start', $days ),
        'completed' => ce_analytics_count( 'journey_complete', $days ),
    ];
}

function ce_analytics_search_queries( $days = 30, $limit = 20 ) {
    return ce_analytics_top_items( 'search_query', 'query', $days, $limit );
}

function ce_analytics_scroll_depth( $days = 30 ) {
    global $wpdb;
    $table = $wpdb->prefix . 'ce_analytics';
    $since = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );
    return $wpdb->get_results( $wpdb->prepare(
        "SELECT JSON_UNQUOTE(JSON_EXTRACT(event_data, '$.depth')) AS depth, COUNT(*) AS total
         FROM {$table}
         WHERE event_type = 'article_scroll' AND created_at >= %s
         GROUP BY depth
         ORDER BY CAST(depth AS UNSIGNED) ASC",
        $since
    ) );
}


/* ═══════════════════════════════════════════════════════════════════════
   ADMIN DASHBOARD
   ═══════════════════════════════════════════════════════════════════════ */

function ce_analytics_admin_menu() {
    add_menu_page(
        'CE Analytics',
        'CE Analytics',
        'manage_options',
        'ce-analytics',
        'ce_analytics_dashboard_page',
        'dashicons-chart-area',
        30
    );
}
add_action( 'admin_menu', 'ce_analytics_admin_menu' );

function ce_analytics_dashboard_page() {
    $days = absint( $_GET['days'] ?? 30 );
    if ( ! in_array( $days, [ 7, 30, 90, 365 ], true ) ) $days = 30;

    $visitors       = ce_analytics_unique_visitors( $days );
    $pageviews      = ce_analytics_count( 'pageview', $days );
    $quiz_funnel    = ce_analytics_quiz_funnel( $days );
    $journey_funnel = ce_analytics_journey_funnel( $days );
    $personas       = ce_analytics_persona_distribution( $days );
    $top_articles   = ce_analytics_top_items( 'article_view', 'slug', $days, 15 );
    $searches       = ce_analytics_search_queries( $days, 15 );
    $devices        = ce_analytics_device_breakdown( $days );
    $scroll         = ce_analytics_scroll_depth( $days );
    $daily          = ce_analytics_daily_counts( 'pageview', $days );

    $quiz_rate = $quiz_funnel['started'] > 0
        ? round( $quiz_funnel['completed'] / $quiz_funnel['started'] * 100, 1 )
        : 0;
    $journey_rate = $journey_funnel['started'] > 0
        ? round( $journey_funnel['completed'] / $journey_funnel['started'] * 100, 1 )
        : 0;
    ?>
    <div class="wrap">
        <h1>Compelling Evidence — Analytics</h1>

        <div style="margin:1rem 0;">
            <?php foreach ( [7,30,90,365] as $d ) :
                $active = $d === $days ? 'font-weight:700;background:#2271b1;color:#fff;' : '';
            ?>
                <a href="<?php echo admin_url( 'admin.php?page=ce-analytics&days=' . $d ); ?>"
                   class="button" style="<?php echo $active; ?>">
                    <?php echo $d === 365 ? '1 Year' : $d . ' Days'; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- KPI Cards -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin:1.5rem 0;">
            <?php
            $cards = [
                [ 'Unique Visitors', number_format( $visitors ), '#2271b1' ],
                [ 'Page Views', number_format( $pageviews ), '#135e96' ],
                [ 'Quiz Starts', number_format( $quiz_funnel['started'] ), '#b32d2e' ],
                [ 'Quiz Completions', number_format( $quiz_funnel['completed'] ), '#d63638' ],
                [ 'Quiz Completion Rate', $quiz_rate . '%', '#e65054' ],
                [ 'Journey Starts', number_format( $journey_funnel['started'] ), '#00a32a' ],
                [ 'Journey Completions', number_format( $journey_funnel['completed'] ), '#00ba37' ],
                [ 'Journey Completion Rate', $journey_rate . '%', '#4ab866' ],
            ];
            foreach ( $cards as $card ) :
            ?>
            <div style="background:#fff;border:1px solid #ddd;border-left:4px solid <?php echo $card[2]; ?>;border-radius:4px;padding:1rem 1.2rem;">
                <div style="font-size:0.78rem;color:#666;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.4rem;"><?php echo esc_html( $card[0] ); ?></div>
                <div style="font-size:1.8rem;font-weight:700;color:#1d2327;"><?php echo esc_html( $card[1] ); ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-top:2rem;">

            <!-- Persona Distribution -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:4px;padding:1.5rem;">
                <h2 style="margin:0 0 1rem;font-size:1rem;">Persona Distribution</h2>
                <?php if ( $personas ) : ?>
                    <table class="widefat striped" style="margin:0;">
                        <thead><tr><th>Persona</th><th style="text-align:right;">Count</th></tr></thead>
                        <tbody>
                        <?php foreach ( $personas as $p ) : ?>
                            <tr>
                                <td><?php echo esc_html( $p->item ); ?></td>
                                <td style="text-align:right;font-weight:600;"><?php echo esc_html( $p->total ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p style="color:#666;">No quiz completions yet.</p>
                <?php endif; ?>
            </div>

            <!-- Top Articles -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:4px;padding:1.5rem;">
                <h2 style="margin:0 0 1rem;font-size:1rem;">Top Articles</h2>
                <?php if ( $top_articles ) : ?>
                    <table class="widefat striped" style="margin:0;">
                        <thead><tr><th>Article</th><th style="text-align:right;">Views</th></tr></thead>
                        <tbody>
                        <?php foreach ( $top_articles as $a ) : ?>
                            <tr>
                                <td><?php echo esc_html( $a->item ); ?></td>
                                <td style="text-align:right;font-weight:600;"><?php echo esc_html( $a->total ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p style="color:#666;">No article views yet.</p>
                <?php endif; ?>
            </div>

            <!-- Search Queries -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:4px;padding:1.5rem;">
                <h2 style="margin:0 0 1rem;font-size:1rem;">Top Search Queries</h2>
                <?php if ( $searches ) : ?>
                    <table class="widefat striped" style="margin:0;">
                        <thead><tr><th>Query</th><th style="text-align:right;">Count</th></tr></thead>
                        <tbody>
                        <?php foreach ( $searches as $s ) : ?>
                            <tr>
                                <td><?php echo esc_html( $s->item ); ?></td>
                                <td style="text-align:right;font-weight:600;"><?php echo esc_html( $s->total ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p style="color:#666;">No searches yet.</p>
                <?php endif; ?>
            </div>

            <!-- Device Breakdown -->
            <div style="background:#fff;border:1px solid #ddd;border-radius:4px;padding:1.5rem;">
                <h2 style="margin:0 0 1rem;font-size:1rem;">Devices</h2>
                <?php if ( $devices ) :
                    $total_devices = array_sum( wp_list_pluck( $devices, 'total' ) );
                    foreach ( $devices as $d ) :
                        $pct = $total_devices > 0 ? round( $d->total / $total_devices * 100, 1 ) : 0;
                ?>
                    <div style="margin-bottom:0.8rem;">
                        <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:0.3rem;">
                            <span style="text-transform:capitalize;"><?php echo esc_html( $d->device_type ); ?></span>
                            <span style="font-weight:600;"><?php echo esc_html( $d->total ); ?> (<?php echo $pct; ?>%)</span>
                        </div>
                        <div style="height:6px;background:#f0f0f1;border-radius:3px;overflow:hidden;">
                            <div style="height:100%;width:<?php echo $pct; ?>%;background:#2271b1;border-radius:3px;"></div>
                        </div>
                    </div>
                <?php endforeach; else : ?>
                    <p style="color:#666;">No data yet.</p>
                <?php endif; ?>

                <h2 style="margin:1.5rem 0 1rem;font-size:1rem;">Scroll Depth (Articles)</h2>
                <?php if ( $scroll ) :
                    $total_scroll = array_sum( wp_list_pluck( $scroll, 'total' ) );
                    foreach ( $scroll as $s ) :
                        $pct = $total_scroll > 0 ? round( $s->total / $total_scroll * 100, 1 ) : 0;
                ?>
                    <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:0.5rem;">
                        <span><?php echo esc_html( $s->depth ); ?>%</span>
                        <span style="font-weight:600;"><?php echo esc_html( $s->total ); ?> (<?php echo $pct; ?>%)</span>
                    </div>
                <?php endforeach; else : ?>
                    <p style="color:#666;">No scroll data yet.</p>
                <?php endif; ?>
            </div>

        </div>

        <!-- Data Purge -->
        <div style="margin-top:3rem;padding:1rem 1.5rem;background:#fff;border:1px solid #ddd;border-radius:4px;">
            <h2 style="margin:0 0 0.5rem;font-size:1rem;">Data Management</h2>
            <p style="color:#666;font-size:0.85rem;margin-bottom:1rem;">Analytics data is stored in your WordPress database. No data is sent to third parties.</p>
            <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
                <?php wp_nonce_field( 'ce_purge_analytics', 'ce_purge_nonce' ); ?>
                <input type="hidden" name="action" value="ce_purge_analytics">
                <label style="font-size:0.85rem;">Purge data older than
                    <select name="purge_days">
                        <option value="30">30 days</option>
                        <option value="90" selected>90 days</option>
                        <option value="180">6 months</option>
                        <option value="365">1 year</option>
                    </select>
                </label>
                <button type="submit" class="button" style="margin-left:0.5rem;">Purge</button>
            </form>
        </div>
    </div>
    <?php
}

// Handle purge
function ce_handle_purge_analytics() {
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Unauthorized' );
    check_admin_referer( 'ce_purge_analytics', 'ce_purge_nonce' );

    global $wpdb;
    $table = $wpdb->prefix . 'ce_analytics';
    $days  = absint( $_POST['purge_days'] ?? 90 );
    $before = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );

    $deleted = $wpdb->query( $wpdb->prepare(
        "DELETE FROM {$table} WHERE created_at < %s", $before
    ) );

    wp_redirect( admin_url( 'admin.php?page=ce-analytics&purged=' . $deleted ) );
    exit;
}
add_action( 'admin_post_ce_purge_analytics', 'ce_handle_purge_analytics' );
