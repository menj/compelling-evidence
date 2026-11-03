<?php
/**
 * Compelling Evidence — FAQ for the journey pages and the quiz
 *
 * One source, inc/data/standalone-faq.json, feeds both the visible FAQ
 * section near the end of each page and the page's FAQPage node, so the
 * markup describes exactly what the reader sees (owner, 2026-11-03).
 *
 * @since 2.13.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * The decoded data file, read once per request.
 */
function ce_standalone_faq_data(): array {
    static $data = null;
    if ( null === $data ) {
        $file = get_stylesheet_directory() . '/inc/data/standalone-faq.json';
        $data = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : [];
        $data = is_array( $data ) ? $data : [];
    }
    return $data;
}

/**
 * Question/answer pairs for a page: a journey key, or 'quiz'.
 *
 * @return array[] Each [ 'q' => string, 'a' => string (may hold <a href>) ].
 */
function ce_standalone_faq_items( string $key ): array {
    $items = ce_standalone_faq_data()['pages'][ $key ] ?? [];
    $out   = [];
    foreach ( (array) $items as $it ) {
        if ( ! empty( $it['q'] ) && ! empty( $it['a'] ) ) {
            $out[] = [ 'q' => (string) $it['q'], 'a' => ce_standalone_faq_answer( (string) $it['a'] ) ];
        }
    }
    return $out;
}

/**
 * An answer with only links allowed.
 */
function ce_standalone_faq_answer( string $a ): string {
    return wp_kses( $a, [ 'a' => [ 'href' => true ] ] );
}

/**
 * FAQPage node for the page at $url, or null when the page has no FAQ.
 */
function ce_standalone_faq_node( string $url, string $key ): ?array {
    $items = ce_standalone_faq_items( $key );
    if ( ! $items ) {
        return null;
    }
    $data = ce_standalone_faq_data();
    $main = [];
    foreach ( $items as $it ) {
        $main[] = [
            '@type'          => 'Question',
            'name'           => $it['q'],
            'acceptedAnswer' => [ '@type' => 'Answer', 'text' => $it['a'] ],
        ];
    }
    return [
        '@type'      => 'FAQPage',
        '@id'        => ce_schema_id( $url, 'faq' ),
        'url'        => $url,
        'name'       => (string) ( 'quiz' === $key ? ( $data['quiz_heading'] ?? 'FAQ' ) : ( $data['heading'] ?? 'FAQ' ) ),
        'isPartOf'   => ce_schema_ref( ce_schema_id( $url, 'webpage' ) ),
        'mainEntity' => $main,
    ];
}

/**
 * The visible FAQ section, or '' when the page has no FAQ.
 */
function ce_standalone_faq_html( string $key ): string {
    $items = ce_standalone_faq_items( $key );
    if ( ! $items ) {
        return '';
    }
    $data    = ce_standalone_faq_data();
    $heading = (string) ( 'quiz' === $key ? ( $data['quiz_heading'] ?? 'FAQ' ) : ( $data['heading'] ?? 'FAQ' ) );
    $out     = '<section class="ce-sfaq" id="faq" aria-labelledby="ce-sfaq-title">'
        . '<h2 class="ce-sfaq-title" id="ce-sfaq-title">' . esc_html( $heading ) . '</h2>';
    foreach ( $items as $it ) {
        $out .= '<details class="ce-sfaq-item"><summary class="ce-sfaq-q">' . esc_html( $it['q'] ) . '</summary>'
            . '<p class="ce-sfaq-a">' . $it['a'] . '</p></details>';
    }
    return $out . '</section>';
}

/**
 * Put the visible FAQ section at the end of a quiz document's <main>.
 */
function ce_standalone_faq_insert( string $html, string $key ): string {
    $faq = ce_standalone_faq_html( $key );
    return '' === $faq ? $html : (string) preg_replace( '#</main>#', addcslashes( $faq, '\\$' ) . '</main>', $html, 1 );
}

/**
 * FAQ for a journey served from a stand-alone HTML file (a locale with no
 * data folder yet), which skips template-parts/journey/document.php.
 *
 * Any FAQPage the file carries is dropped, since its questions are not on
 * the page. When the data file has questions for $key, the visible section
 * goes in before </body> and its FAQPage joins the file's first @graph (or
 * a block of its own when the file has none), so markup and page match.
 *
 * @param string $html Journey document.
 * @param string $key  Journey key.
 * @param string $url  URL the page is served from.
 */
function ce_standalone_faq_html_source( string $html, string $key, string $url ): string {
    $node   = ce_standalone_faq_node( $url, $key );
    $placed = false;
    $html   = (string) preg_replace_callback( '#<script type="application/ld\+json">(.*?)</script>#su', static function ( $m ) use ( $node, &$placed ) {
        $data = json_decode( $m[1], true );
        if ( ! is_array( $data ) ) {
            return $m[0];
        }
        $is_faq = static function ( $n ) {
            return is_array( $n ) && in_array( 'FAQPage', (array) ( $n['@type'] ?? [] ), true );
        };
        if ( $is_faq( $data ) ) {
            return '';
        }
        if ( isset( $data['@graph'] ) && is_array( $data['@graph'] ) ) {
            $data['@graph'] = array_values( array_filter( $data['@graph'], static function ( $n ) use ( $is_faq ) { return ! $is_faq( $n ); } ) );
            if ( $node && ! $placed ) {
                $data['@graph'][] = $node;
                $placed           = true;
            }
        }
        return '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ) . '</script>';
    }, $html );

    if ( ! $node ) {
        return $html;
    }
    if ( ! $placed ) {
        $block = '<script type="application/ld+json">' . wp_json_encode( [ '@context' => 'https://schema.org' ] + $node, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ) . '</script>';
        $html  = (string) preg_replace( '#</head>#', addcslashes( $block, '\\$' ) . '</head>', $html, 1 );
    }
    return (string) preg_replace( '#</body>#', addcslashes( ce_standalone_faq_html( $key ), '\\$' ) . '</body>', $html, 1 );
}
