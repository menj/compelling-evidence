<?php
/**
 * Template Name: Persona Journey
 */
$journey_key = get_post_meta( get_the_ID(), '_ce_journey_key', true );
if ( empty($journey_key) ) {
    $journey_key = sanitize_key( basename( get_permalink() ) );
}
$valid = array('new-atheist','agnostic','secular-humanist','antitheist','materialist',
               'muslim-doubts','apatheist','deist','scientist','classical-atheist',
               'ex-believer','spiritual-seeker','freethinker','true-muslim');
$journey_file = '';
if ( in_array($journey_key, $valid, true) ) {
    $journey_file = ce_get_journey_file( $journey_key );
}
if ( ! $journey_file || ! file_exists($journey_file) ) {
    // If no valid slug at all (bare /journey/ URL), redirect to journeys overview
    if ( empty($journey_key) || $journey_key === 'journey' ) {
        wp_redirect( home_url('/journeys/'), 301 );
        exit;
    }
    // Invalid slug — show a styled error page
    status_header(404);
    get_header();
    ?>
    <main id="main" role="main">
<section class="not-found-hero">
      <div class="not-found-code" aria-hidden="true">404</div>
      <h1 class="not-found-title">Journey not found</h1>
      <p class="not-found-sub">
        The path <strong style="color:rgba(12,212,224,0.7)"><?php echo esc_html($journey_key); ?></strong>
        doesn't exist — but 14 others do. Take the quiz to find yours, or browse all paths.
      </p>
      <div class="not-found-actions">
        <a href="<?php echo esc_url(home_url('/quiz')); ?>" class="not-found-btn not-found-btn--primary">Take the quiz</a>
        <a href="<?php echo esc_url(home_url('/journeys')); ?>" class="not-found-btn not-found-btn--secondary">Browse all paths</a>
        <a href="<?php echo esc_url(home_url('/articles')); ?>" class="not-found-btn not-found-btn--ghost">Read articles</a>
      </div>
    </section>
    <?php
    get_footer();
    exit;
}
$html = file_get_contents( $journey_file );
// Make /journey/ and /articles/ paths absolute with the site domain
$html = str_replace( '"/journey/',  '"' . home_url('/journey/'),  $html );
$html = str_replace( "'/journey/",  "'" . home_url('/journey/'),  $html );
$html = str_replace( '"/articles/', '"' . home_url('/articles/'), $html );
$html = str_replace( "'/articles/", "'" . home_url('/articles/'), $html );
$html = str_replace( 'href="/"',    'href="' . home_url('/') . '"', $html );

// ── Inject shared journey base CSS ──
$base_css_url = get_stylesheet_directory_uri() . '/assets/css/journey-base.css?v=' . wp_get_theme()->get('Version');
$html = str_replace( '</head>', '<link rel="stylesheet" href="' . esc_url( $base_css_url ) . '">' . "\n" . '</head>', $html ); // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet -- standalone HTML document; wp_head() does not run here.

// ── Inject Auto Justify Content styles into self-contained journey HTML ──
// The AJC plugin cannot reach these pages (no wp_head fires), so we inject
// the justify + drop-cap CSS directly. Respects the plugin's saved settings.
$ajc_justify_css = '';
if ( get_option( 'ajc_enabled', true ) ) {
    $ajc_justify_css .= '.reading p, .reading li { text-align: justify !important; text-justify: inter-word !important; }';
    $ajc_justify_css .= '.reading p { text-align-last: left !important; }';
    if ( get_option( 'ajc_hyphen', true ) ) {
        $ajc_justify_css .= '.reading p, .reading li { -webkit-hyphens: auto !important; -ms-hyphens: auto !important; hyphens: auto !important; overflow-wrap: break-word !important; }';
    }
    // Exclude Quran/hadith citations
    $ajc_justify_css .= '.quran-citation, .quran-citation p, .hadith-citation, .hadith-citation p { text-align: center !important; text-justify: auto !important; hyphens: manual !important; }';
}
if ( get_option( 'ajc_dc_enabled', false ) ) {
    $dc_color = sanitize_hex_color( get_option( 'ajc_dc_color', '#1e293b' ) ) ?: '#1e293b';
    $ajc_justify_css .= '.chapter-num + .reading > p:first-child::first-letter { float: left; font-size: 4.0em; font-weight: 700; line-height: 0.83; margin: 0.04em 0.1em -0.05em 0; padding: 0 0.05em 0 0; color: ' . $dc_color . '; }';
    $ajc_justify_css .= '.chapter-num + .reading > p:first-child { overflow: hidden; }';
}
if ( $ajc_justify_css ) {
    $html = str_replace( '</head>', '<style>' . $ajc_justify_css . '</style></head>', $html );
}

// ── Inject analytics tracker into standalone journey HTML ──
$analytics_js = '<script>'
    . 'window.CE={ajaxUrl:"' . admin_url('admin-ajax.php') . '",nonce:"' . wp_create_nonce('ce_nonce') . '"};'
    . '(function(){var s=sessionStorage.getItem("ce_analytics_sid");if(!s){s=Math.random().toString(36).substr(2,12)+Date.now().toString(36);sessionStorage.setItem("ce_analytics_sid",s);}window.ceTrack=function(t,d){var b=new FormData();b.append("action","ce_analytics");b.append("nonce",CE.nonce);b.append("event_type",t);b.append("event_data",JSON.stringify(d||{}));b.append("session_id",s);fetch(CE.ajaxUrl,{method:"POST",body:b,credentials:"same-origin"}).catch(function(){});};'
    . 'ceTrack("pageview",{page:"journey",path:location.pathname});'
    . 'ceTrack("journey_start",{persona:"' . esc_js($journey_key) . '"});'
    . '})();'
    . '</script>';
$html = str_replace( '</body>', $analytics_js . '</body>', $html );

$html = ce_standalone_html_seo( $html, get_permalink(), $journey_key );

header('Content-Type: text/html; charset=UTF-8');
echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- complete static HTML document shipped with the theme.
exit;
