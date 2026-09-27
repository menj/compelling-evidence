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
// Make /journey/ and /articles/ paths absolute with the site domain
$html = str_replace( '"/journey/',  '"' . home_url('/journey/'),  $html );
$html = str_replace( "'/journey/",  "'" . home_url('/journey/'),  $html );
$html = str_replace( '"/articles/', '"' . home_url('/articles/'), $html );
$html = str_replace( "'/articles/", "'" . home_url('/articles/'), $html );
$html = str_replace( 'href="/"',    'href="' . home_url('/') . '"', $html );

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
$analytics_js = '<script>'
    . 'window.CE={ajaxUrl:"' . admin_url('admin-ajax.php') . '",nonce:"' . wp_create_nonce('ce_nonce') . '"};'
    . '(function(){var s=sessionStorage.getItem("ce_analytics_sid");if(!s){s=Math.random().toString(36).substr(2,12)+Date.now().toString(36);sessionStorage.setItem("ce_analytics_sid",s);}window.ceTrack=function(t,d){var b=new FormData();b.append("action","ce_analytics");b.append("nonce",CE.nonce);b.append("event_type",t);b.append("event_data",JSON.stringify(d||{}));b.append("session_id",s);if(navigator.sendBeacon&&(t==="quiz_abandon")){var p=new URLSearchParams();p.append("action","ce_analytics");p.append("nonce",CE.nonce);p.append("event_type",t);p.append("event_data",JSON.stringify(d||{}));p.append("session_id",s);navigator.sendBeacon(CE.ajaxUrl,p);}else{fetch(CE.ajaxUrl,{method:"POST",body:b,credentials:"same-origin"}).catch(function(){});}};'
    . 'ceTrack("pageview",{page:"quiz",path:"/quiz/"});'
    . '})();'
    . '</script>';
// Inject before the first <script> in body (so ceTrack is defined before quiz JS calls it)
$html = preg_replace( '/(<script>)/', $analytics_js . '$1', $html, 1 );

header('Content-Type: text/html; charset=UTF-8');
$html = ce_standalone_html_seo( $html, get_permalink() );
echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- complete static HTML document shipped with the theme.
exit;
