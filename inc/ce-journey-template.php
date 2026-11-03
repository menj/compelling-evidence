<?php
/**
 * Compelling Evidence: persona journeys built from one template.
 *
 * Each journey is a folder of data:
 *
 *   journeys/data/<key>/journey.json   SEO fields, JSON-LD, nav, chapter list,
 *                                      Go deeper links, badge name, Volume II preview
 *   journeys/data/<key>/style.css      the rules only that persona uses
 *   journeys/data/<key>/screens/<id>.html  each chapter's body, kept as written
 *
 * The page shell (head, nav, chapter wrappers, Volume II preview, scripts)
 * is in template-parts/journey/. Interface text the template and
 * assets/js/journey.js print is in journeys/i18n/<locale>.php, so a
 * translation needs a strings file and translated data, and no code.
 *
 * A translation lives in journeys/<locale>/data/<key>/. Where there is none,
 * page-journey.php falls back to a hand-made journeys/<locale>/<key>-journey.html,
 * then to the English data.
 *
 * @since 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Locale folders to try, most specific first: 'ms-MY', 'ms'. English data
 * sits at the top of journeys/ and is tried by the callers last.
 *
 * @return string[]
 */
function ce_journey_locale_folders(): array {
    $folder = str_replace( '_', '-', (string) get_locale() );
    $lang   = explode( '-', $folder )[0];
    if ( 'en' === $lang ) {
        return [];
    }
    return array_values( array_unique( [ $folder, $lang ] ) );
}

/**
 * Where a journey comes from: its data folder or a stand-alone HTML file.
 *
 * Order: data for the locale, HTML for the locale (both for 'ms-MY' and then
 * 'ms'), English data, English HTML.
 *
 * @param string $key Journey key.
 * @return array{type:string,path:string}|null
 */
function ce_journey_source( string $key ): ?array {
    $base = get_stylesheet_directory() . '/journeys/';
    foreach ( ce_journey_locale_folders() as $loc ) {
        if ( is_readable( $base . $loc . '/data/' . $key . '/journey.json' ) ) {
            return [ 'type' => 'data', 'path' => $base . $loc . '/data/' . $key . '/' ];
        }
        if ( is_readable( $base . $loc . '/' . $key . '-journey.html' ) ) {
            return [ 'type' => 'html', 'path' => $base . $loc . '/' . $key . '-journey.html' ];
        }
    }
    if ( is_readable( $base . 'data/' . $key . '/journey.json' ) ) {
        return [ 'type' => 'data', 'path' => $base . 'data/' . $key . '/' ];
    }
    if ( is_readable( $base . $key . '-journey.html' ) ) {
        return [ 'type' => 'html', 'path' => $base . $key . '-journey.html' ];
    }
    return null;
}

/**
 * Interface strings for the current locale, English underneath.
 *
 * @return array<string,mixed>
 */
function ce_journey_strings(): array {
    static $cache = null;
    if ( null !== $cache ) {
        return $cache;
    }
    $dir     = get_stylesheet_directory() . '/journeys/i18n/';
    $strings = (array) require $dir . 'en.php';
    foreach ( array_reverse( ce_journey_locale_folders() ) as $loc ) {
        if ( is_readable( $dir . $loc . '.php' ) ) {
            $strings = array_replace_recursive( $strings, (array) require $dir . $loc . '.php' );
        }
    }
    $cache = $strings;
    return $cache;
}

/**
 * Read a journey's data folder.
 *
 * @param string $dir Folder path with a trailing slash.
 * @return array|null Null when journey.json is missing or invalid.
 */
function ce_journey_load( string $dir ): ?array {
    $data = json_decode( (string) file_get_contents( $dir . 'journey.json' ), true );
    if ( ! is_array( $data ) || empty( $data['screens'] ) ) {
        return null;
    }
    $data['css'] = is_readable( $dir . 'style.css' ) ? (string) file_get_contents( $dir . 'style.css' ) : '';
    foreach ( $data['screens'] as $i => $screen ) {
        if ( empty( $screen['template'] ) ) {
            $file = $dir . 'screens/' . $screen['id'] . '.html';
            $data['screens'][ $i ]['body'] = is_readable( $file ) ? (string) file_get_contents( $file ) : '';
        }
    }
    return $data;
}

/**
 * Include one part of the journey template with $j (journey data) and
 * $t (strings) in scope.
 *
 * @param string $part Part name in template-parts/journey/.
 * @param array  $vars Variables for the part.
 */
function ce_journey_part( string $part, array $vars ): void {
    extract( $vars, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
    include get_stylesheet_directory() . '/template-parts/journey/' . $part . '.php';
}

/**
 * JSON for a <script> block, indented by two spaces like the hand-written
 * pages were.
 *
 * @param mixed $data Data.
 */
function ce_journey_json( $data ): string {
    $json = (string) wp_json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG );
    return (string) preg_replace_callback( '/^( {4})+/m', static function ( $m ) {
        return str_repeat( ' ', strlen( $m[0] ) / 2 );
    }, $json );
}

/**
 * The window.CE_JOURNEY object journey.js reads.
 *
 * @param array $j Journey data.
 */
function ce_journey_config( array $j ): array {
    $cfg = [
        'screens' => array_column( $j['screens'], 'id' ),
        'labels'  => array_column( $j['screens'], 'label' ),
    ];
    return $cfg + (array) ( $j['journey'] ?? [] );
}

/**
 * Render a journey's complete HTML document from its data folder. The
 * result goes through the same steps in page-journey.php as a stand-alone
 * HTML file (asset markers, link rewriting, analytics, ce_standalone_html_seo()).
 *
 * @param string $dir Data folder with a trailing slash.
 * @return string Empty when the data cannot be read.
 */
function ce_journey_render( string $dir ): string {
    $j = ce_journey_load( $dir );
    if ( ! $j ) {
        return '';
    }
    $vars = [ 'j' => $j, 't' => ce_journey_strings() ];
    ob_start();
    ce_journey_part( 'document', $vars );
    return (string) ob_get_clean();
}
