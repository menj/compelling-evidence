<?php
/**
 * Template Name: Persona Quiz
 */
$quiz_file = ce_get_quiz_file();
if ( ! file_exists( $quiz_file ) ) {
    status_header(404);
    get_header();
    ?>
    <main id="main" role="main">
<section class="not-found-hero">
      <div class="not-found-code" aria-hidden="true">404</div>
      <h1 class="not-found-title">Quiz not available</h1>
      <p class="not-found-sub">The quiz could not be loaded. Browse all journey paths directly or read the articles.</p>
      <div class="not-found-actions">
        <a href="<?php echo esc_url(home_url('/journeys')); ?>" class="not-found-btn not-found-btn--primary">Browse all paths</a>
        <a href="<?php echo esc_url(home_url('/articles')); ?>" class="not-found-btn not-found-btn--secondary">Read articles</a>
      </div>
    </section>
    <?php
    get_footer();
    exit;
}
$html = file_get_contents( $quiz_file );
// Make /journey/, /journeys/ and /articles/ paths absolute with the site domain
$html = str_replace( '"/journeys/', '"' . home_url('/journeys/'), $html );
$html = str_replace( '"/journey/',  '"' . home_url('/journey/'),  $html );
$html = str_replace( "'/journey/",  "'" . home_url('/journey/'),  $html );
$html = str_replace( '"/articles/', '"' . home_url('/articles/'), $html );
$html = str_replace( "'/articles/", "'" . home_url('/articles/'), $html );
$html = str_replace( 'href="/"',    'href="' . home_url('/') . '"', $html );

// ── murtadd.org links for the "You are not alone" list ──
// Data: inc/data/quiz-murtadd-links.json, rebuilt with
// bin/build-quiz-murtadd-links.py. The base URL can be changed with the
// ce_murtadd_base_url filter.
$murtadd_base = 'https://murtadd.org';
if ( function_exists( 'apply_filters' ) ) {
    $murtadd_base = (string) apply_filters( 'ce_murtadd_base_url', $murtadd_base );
}
$murtadd_data = [];
$murtadd_file = get_stylesheet_directory() . '/inc/data/quiz-murtadd-links.json';
if ( is_readable( $murtadd_file ) ) {
    $murtadd_data = json_decode( (string) file_get_contents( $murtadd_file ), true );
}
if ( ! empty( $murtadd_data['items'] ) ) {
    $murtadd_data['base'] = untrailingslashit( esc_url_raw( $murtadd_base ) );
    $murtadd_js = '<script>var CE_MURTADD=' . wp_json_encode( $murtadd_data, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ) . ';</script>';
} else {
    $murtadd_js = '';
}
$html = str_replace( '<!-- ce:quiz-murtadd -->', $murtadd_js, $html );

// ── Visible FAQ, from the same data as the FAQPage node (inc/ce-standalone-faq.php) ──
$html = ce_standalone_faq_insert( $html, 'quiz' );

// ── Quiz CSS and JS (inc/ce-journey-assets.php) ──
$html = ce_standalone_assets( $html, 'quiz' );

// ── Inject SEO plugin meta tags (Rank Math, Yoast, etc.) ──────────────
// These plugins hook into wp_head(). We capture their output and inject it
// into the standalone quiz HTML, replacing any hardcoded SEO tags.
ob_start();
wp_head();
$wp_head_output = ob_get_clean();

// Extract only meta/link SEO tags (og:, twitter:, description, canonical, schema)
$seo_tags = '';
if ( preg_match_all( '/<meta\s+(?:name|property)=["\'](?:description|og:[^"\']+|twitter:[^"\']+|article:[^"\']+|robots)["\'][^>]*>/i', $wp_head_output, $matches ) ) {
    $seo_tags .= implode( "\n", $matches[0] ) . "\n";
}
if ( preg_match( '/<link\s+rel=["\']canonical["\'][^>]*>/i', $wp_head_output, $match ) ) {
    $seo_tags .= $match[0] . "\n";
}
// Rank Math JSON-LD schema
if ( preg_match( '/<script\s+type=["\']application\/ld\+json["\'][^>]*>.*?<\/script>/is', $wp_head_output, $match ) ) {
    $seo_tags .= $match[0] . "\n";
}

if ( $seo_tags ) {
    // Remove any hardcoded SEO tags from quiz.html (so Rank Math takes full control)
    $html = preg_replace( '/<meta\s+(?:name|property)=["\'](?:description|og:[^"\']+|twitter:[^"\']+)["\'][^>]*>\n?/i', '', $html );
    $html = preg_replace( '/<link\s+rel=["\']canonical["\'][^>]*>\n?/i', '', $html );
    // Inject Rank Math tags before </head>
    $html = str_replace( '</head>', $seo_tags . '</head>', $html );
}

// ── Inject analytics tracker into standalone quiz HTML ──
// quiz_abandon fires on pagehide: sendBeacon first, and when the browser
// refuses the beacon (it returns false), a keepalive fetch instead.
$analytics_js = '<script>'
    . 'window.CE={ajaxUrl:"' . admin_url('admin-ajax.php') . '",nonce:"' . wp_create_nonce('ce_nonce') . '"};'
    . '(function(){var s=null;try{s=sessionStorage.getItem("ce_analytics_sid");}catch(e){}if(!s){s=Math.random().toString(36).substr(2,12)+Date.now().toString(36);try{sessionStorage.setItem("ce_analytics_sid",s);}catch(e){}}window.ceTrack=function(t,d){var b=new FormData();b.append("action","ce_analytics");b.append("nonce",CE.nonce);b.append("event_type",t);b.append("event_data",JSON.stringify(d||{}));b.append("session_id",s);var q=t==="quiz_abandon";if(q&&navigator.sendBeacon){var p=new URLSearchParams();p.append("action","ce_analytics");p.append("nonce",CE.nonce);p.append("event_type",t);p.append("event_data",JSON.stringify(d||{}));p.append("session_id",s);try{if(navigator.sendBeacon(CE.ajaxUrl,p))return;}catch(e){}}fetch(CE.ajaxUrl,{method:"POST",body:b,credentials:"same-origin",keepalive:q}).catch(function(){});};'
    . 'ceTrack("pageview",{page:"quiz",path:"/quiz/"});'
    . '})();'
    . '</script>';
// Inject before the first <script> in body (so ceTrack is defined before quiz JS calls it)
$html = preg_replace( '/(<script>)/', $analytics_js . '$1', $html, 1 );

header('Content-Type: text/html; charset=UTF-8');
$html = ce_standalone_html_seo( $html, get_permalink() );
echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- complete static HTML document shipped with the theme.
exit;
