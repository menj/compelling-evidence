<?php
/**
 * Compelling Evidence — assets for the standalone journey and quiz pages
 *
 * page-journey.php and page-quiz.php echo complete HTML documents from
 * journeys/ and never call wp_head() or wp_footer(), so enqueued files would
 * not print. The shared CSS and JS are registered here like any other theme
 * asset and printed with wp_print_styles() / wp_print_scripts() in place of
 * the placeholders the HTML files carry:
 *
 *   <!-- ce:journey-css -->  journey.css             (head, before the page's own <style>)
 *   <!-- ce:journey-js -->   journey.js              (end of body, after window.CE_JOURNEY)
 *   <!-- ce:quiz-css -->     quiz.css
 *   <!-- ce:quiz-js -->      quiz.js                 (after the quiz data)
 *   </head>                  journey-base.css        (journeys only, last in the head)
 *
 * Each file's version is its modification time, so a changed file reaches
 * visitors on the next load without a theme version bump.
 *
 * @since 2.8.6
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Version string for a theme file: its mtime, or the theme version if the
 * file cannot be read.
 */
function ce_standalone_asset_version( string $rel ): string {
    $file = get_stylesheet_directory() . '/' . ltrim( $rel, '/' );
    $mtime = file_exists( $file ) ? filemtime( $file ) : false;
    return $mtime ? (string) $mtime : (string) wp_get_theme()->get( 'Version' );
}

/**
 * Register the journey and quiz assets. Safe to call more than once.
 */
function ce_register_standalone_assets(): void {
    static $done = false;
    if ( $done ) {
        return;
    }
    $done = true;
    $uri  = get_stylesheet_directory_uri();

    $styles = [
        'ce-journey'      => 'assets/css/journey.css',
        'ce-journey-base' => 'assets/css/journey-base.css',
        'ce-quiz'         => 'assets/css/quiz.css',
    ];
    foreach ( $styles as $handle => $rel ) {
        wp_register_style( $handle, $uri . '/' . $rel, [], ce_standalone_asset_version( $rel ) );
    }

    $scripts = [
        'ce-journey' => 'assets/js/journey.js',
        'ce-quiz'    => 'assets/js/quiz.js',
    ];
    foreach ( $scripts as $handle => $rel ) {
        // Printed where the inline scripts used to sit, so not deferred.
        wp_register_script( $handle, $uri . '/' . $rel, [], ce_standalone_asset_version( $rel ), false );
    }
    // Links to other journeys are built from the site root, as page-journey.php
    // used to do by rewriting "/journey/" inside the inline scripts.
    wp_add_inline_script( 'ce-journey', 'window.ceHomeUrl = ' . wp_json_encode( home_url( '/' ) ) . ';', 'before' );
}

/**
 * Printed markup for registered style or script handles.
 *
 * @param string   $type    'style' or 'script'.
 * @param string[] $handles Registered handles.
 */
function ce_standalone_asset_tags( string $type, array $handles ): string {
    ce_register_standalone_assets();
    ob_start();
    if ( 'style' === $type ) {
        wp_print_styles( $handles );
    } else {
        wp_print_scripts( $handles );
    }
    return (string) ob_get_clean();
}

/**
 * Swap the asset placeholders in a journey or quiz document for the printed
 * tags. A file without placeholders (an older translation) is left as it is.
 *
 * @param string $html Page HTML.
 * @param string $kind 'journey' or 'quiz'.
 */
function ce_standalone_assets( string $html, string $kind ): string {
    $map = 'quiz' === $kind
        ? [ '<!-- ce:quiz-css -->' => [ 'style', 'ce-quiz' ], '<!-- ce:quiz-js -->' => [ 'script', 'ce-quiz' ] ]
        : [ '<!-- ce:journey-css -->' => [ 'style', 'ce-journey' ], '<!-- ce:journey-js -->' => [ 'script', 'ce-journey' ] ];
    foreach ( $map as $marker => $asset ) {
        if ( false !== strpos( $html, $marker ) ) {
            $html = str_replace( $marker, rtrim( ce_standalone_asset_tags( $asset[0], [ $asset[1] ] ) ), $html );
        }
    }
    if ( 'journey' === $kind ) {
        // The responsive layer goes last in the head so it wins over the page's own <style>.
        $html = str_replace( '</head>', ce_standalone_asset_tags( 'style', [ 'ce-journey-base' ] ) . '</head>', $html );
    }
    return $html;
}
