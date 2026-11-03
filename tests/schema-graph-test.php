<?php
if ( ! function_exists( 'array_is_list' ) ) {
	function array_is_list( array $a ): bool {
		return [] === $a || array_keys( $a ) === range( 0, count( $a ) - 1 );
	}
}
/**
 * Stub test of the single JSON-LD graph (inc/ce-seo.php, inc/ce-schema.php,
 * inc/ce-rankmath.php).
 *
 * Renders the graph for sample pages (home, every article, FAQ, glossary,
 * quiz, every journey, reader questions with and without an answer, a topic
 * archive, the ask page, search) with Rank Math off, and the merge into a
 * Rank Math graph for an article, a question and the FAQ. Checks that the
 * JSON parses, that every top-level @id is unique, that every reference
 * resolves, and that each type has the properties it needs.
 *
 * Optional: CE_SCHEMA_VOCAB=/path/schemaorg-current-https.jsonld also checks
 * every type and property against the schema.org vocabulary.
 *
 * CE_SCHEMA_DUMP=1 prints the graph of the detailed sample article.
 *
 * Run: php tests/schema-graph-test.php
 */
define( 'ABSPATH', '/' );
$ROOT = dirname( __DIR__ );

// ── WordPress stubs ─────────────────────────────────────────────────────
function add_action( ...$a ) {} function add_filter( ...$a ) {} function remove_action( ...$a ) {} function add_shortcode( ...$a ) {}
function get_stylesheet_directory() { return $GLOBALS['ROOT']; }
function get_stylesheet_directory_uri() { return 'https://ce.test/wp-content/themes/compelling-evidence'; }
function home_url( $p = '' ) { return 'https://ce.test' . ( '' === $p ? '' : '/' . ltrim( $p, '/' ) ); }
function trailingslashit( $s ) { return rtrim( $s, '/' ) . '/'; }
function untrailingslashit( $s ) { return rtrim( $s, '/' ); }
function wp_strip_all_tags( $s ) { return trim( strip_tags( preg_replace( '#<(script|style)[^>]*>.*?</\1>#s', '', (string) $s ) ) ); }
function apply_filters( $h, $v ) { return $v; }
function get_option( $k, $d = false ) { return $GLOBALS['ctx']['options'][ $k ] ?? $d; }
function get_locale() { return 'en_US'; }
function get_bloginfo( $k = '' ) { return 'Compelling Evidence'; }
function wp_json_encode( $d, $f = 0 ) { return json_encode( $d, $f ); }
function esc_url_raw( $u ) { return (string) $u; }
function esc_url( $u ) { return (string) $u; }
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_attr( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_attr__( $s ) { return $s; }
function sanitize_key( $s ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $s ) ); }
function sanitize_file_name( $s ) { return preg_replace( '/[^A-Za-z0-9_.\-]/', '', (string) $s ); }
function sanitize_html_class( $s ) { return preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $s ); }
function sanitize_title( $s ) { return trim( preg_replace( '/[^a-z0-9]+/', '-', strtolower( iconv( 'UTF-8', 'ASCII//TRANSLIT//IGNORE', (string) $s ) ) ), '-' ); }
function wp_kses_post( $s ) { return (string) $s; }
function wp_kses( $s, $allowed ) { return (string) $s; }
function absint( $n ) { return abs( (int) $n ); }
function is_email( $e ) { return false !== strpos( $e, '@' ); }
function is_wp_error( $x ) { return false; }
function wp_date( $f ) { return gmdate( $f ); }
function add_query_arg( $k, $v, $u ) { return $u . '?' . $k . '=' . $v; }
function current_user_can( $c ) { return false; }
function shortcode_parse_atts( $s ) { preg_match_all( '/(\w+)="([^"]*)"/', $s, $m ); return array_combine( $m[1], $m[2] ); }
function wp_trim_words( $t, $n = 55, $more = '…' ) { $w = preg_split( '/\s+/', trim( $t ) ); return implode( ' ', array_slice( $w, 0, $n ) ) . ( count( $w ) > $n ? $more : '' ); }
function wp_get_attachment_image_url( $a, $s ) { return ''; }
function get_nav_menu_locations() { return []; }
function ce_get_quiz_file() { return $GLOBALS['ROOT'] . '/journeys/quiz.html'; }
class WP_Post { public $ID = 1; public $post_type = 'post'; public $post_name = ''; public $post_title = ''; public $post_content = ''; public $post_date = '2026-01-01 10:00:00';
	public function __construct( array $a = [] ) { foreach ( $a as $k => $v ) { $this->$k = $v; } } }

// Request context.
$GLOBALS['ctx'] = [];
function ctx( $k, $d = null ) { return $GLOBALS['ctx'][ $k ] ?? $d; }
function is_front_page() { return 'front' === ctx( 'view' ); }
function is_home() { return false; }
function is_404() { return '404' === ctx( 'view' ); }
function is_search() { return 'search' === ctx( 'view' ); }
function get_search_query() { return 'mercy'; }
function is_page( $slug = '' ) { $p = ctx( 'post' ); return $p && 'page' === $p->post_type && ( '' === $slug || $slug === $p->post_name ); }
function is_singular( $t = '' ) { $p = ctx( 'post' ); if ( ! $p ) return false; return '' === $t || in_array( $p->post_type, (array) $t, true ); }
function is_tax( $t = '' ) { return 'tax' === ctx( 'view' ); }
function is_post_type_archive( $t = '' ) { return 'archive' === ctx( 'view' ) && $t === ctx( 'archive' ); }
function get_queried_object() { return ctx( 'term' ) ?: ctx( 'post' ); }
function get_queried_object_id() { return 1; }
function get_page_template_slug( $id ) { return ctx( 'template', '' ); }
function get_post( $p = null ) { return is_object( $p ) ? $p : ctx( 'post' ); }
function get_the_ID() { return 1; }
function get_permalink( $p = null ) { $p = is_object( $p ) ? $p : ctx( 'post' ); if ( 'ce_article' === $p->post_type ) return home_url( '/articles/' . $p->post_name . '/' ); if ( 'ce_question' === $p->post_type ) return home_url( '/qa/' . $p->post_name . '/' ); return home_url( '/' . $p->post_name . '/' ); }
function get_the_title( $p = null ) { $p = is_object( $p ) ? $p : ctx( 'post' ); return $p->post_title; }
function get_the_content() { return ctx( 'post' )->post_content; }
function get_the_excerpt() { return 'Excerpt.'; }
function get_the_date( $f = '', $p = null ) { return 'Y' === $f ? '2026' : '2026-01-01T10:00:00+00:00'; }
function get_the_modified_date( $f = '', $p = null ) { return '2026-02-01T10:00:00+00:00'; }
function has_post_thumbnail() { return (bool) ctx( 'thumb' ); }
function get_the_post_thumbnail_url( $p = null, $s = '' ) { return ctx( 'thumb' ) ? 'https://ce.test/wp-content/uploads/hero.jpg' : ''; }
function get_post_thumbnail_id( $p = null ) { return ctx( 'thumb' ) ? 9 : 0; }
function wp_get_attachment_image_src( $id, $s = '' ) { return [ 'https://ce.test/wp-content/uploads/hero.jpg', 1200, 630 ]; }
function get_post_meta( $id, $k, $s = false ) { return ctx( 'meta' )[ $k ] ?? ''; }
function wp_get_post_terms( $id, $tax ) { return 'ce_topic' === $tax ? [ (object) [ 'term_id' => 3, 'name' => 'The Quran', 'slug' => 'the-quran' ] ] : []; }
function wp_get_object_terms( $id, $tax, $a = [] ) { return ctx( 'qstatus', [] ); }
function get_term_link( $t ) { return home_url( '/topic/' . $t->slug . '/' ); }
function get_post_type_archive_link( $t ) { return home_url( 'ce_article' === $t ? '/articles/' : '/qa/' ); }
function get_posts( $a ) { return ctx( 'list', [] ); }
function ce_is_rankmath_active() { return (bool) ctx( 'rm' ); }

require $ROOT . '/inc/ce-seo.php';
require $ROOT . '/inc/ce-schema.php';
require $ROOT . '/inc/ce-rankmath.php';
require $ROOT . '/inc/ce-glossary.php';
require $ROOT . '/inc/ce-media.php';
require $ROOT . '/inc/ce-journey-template.php';
require $ROOT . '/inc/ce-standalone-faq.php';

// ── Checks ──────────────────────────────────────────────────────────────
$fail = 0;
$runs = 0;
function bad( $label, $msg ) { global $fail; $fail++; echo "FAIL [$label] $msg\n"; }

$VOCAB = null;
if ( getenv( 'CE_SCHEMA_VOCAB' ) && is_readable( getenv( 'CE_SCHEMA_VOCAB' ) ) ) {
	$VOCAB = [];
	foreach ( json_decode( file_get_contents( getenv( 'CE_SCHEMA_VOCAB' ) ), true )['@graph'] as $t ) {
		$VOCAB[ preg_replace( '/^schema:/', '', (string) $t['@id'] ) ] = 1;
	}
}

$REQUIRED = [
	'WebSite' => [ 'name', 'url' ], 'Organization' => [ 'name', 'url' ], 'Person' => [ 'name' ],
	'Article' => [ 'headline' ], 'Article@id' => [ 'headline', 'author', 'publisher' ], 'WebPage' => [ 'url' ], 'WebPage@id' => [ 'url', 'isPartOf' ],
	'CollectionPage' => [ 'url' ], 'QAPage' => [ 'url', 'mainEntity' ], 'ContactPage' => [ 'url' ], 'AboutPage' => [ 'url' ], 'SearchResultsPage' => [ 'url' ],
	'FAQPage' => [ 'mainEntity' ], 'Question' => [ 'name' ], 'Answer' => [ 'text' ],
	'DefinedTermSet' => [ 'name' ], 'DefinedTerm' => [ 'name', 'inDefinedTermSet' ],
	'Quiz' => [ 'name', 'hasPart' ], 'LearningResource' => [ 'name', 'hasPart' ],
	'ItemList' => [ 'itemListElement' ], 'ListItem' => [ 'position' ], 'BreadcrumbList' => [ 'itemListElement' ],
	'ImageObject' => [ 'url' ], 'Quotation' => [ 'text', 'inLanguage' ], 'Chapter' => [ 'position', 'isPartOf' ], 'Book' => [ 'name' ],
	'SiteNavigationElement' => [ 'name', 'hasPart' ], 'WPHeader' => [ 'name' ], 'WPFooter' => [ 'name' ], 'WPSideBar' => [ 'name' ],
	'SearchAction' => [ 'target', 'query-input' ], 'PropertyValue' => [ 'name', 'value' ],
];

/** Walk a value, calling $fn on every object (assoc array). */
function walk( $v, callable $fn ) {
	if ( ! is_array( $v ) ) return;
	if ( isset( $v['@type'] ) || isset( $v['@id'] ) ) $fn( $v );
	foreach ( $v as $x ) walk( $x, $fn );
}

/**
 * Check one graph (list of top-level nodes). Returns types seen.
 */
function check_graph( array $nodes, string $label ): array {
	global $REQUIRED, $VOCAB, $runs;
	$runs++;
	$json = json_encode( [ '@context' => 'https://schema.org', '@graph' => array_values( $nodes ) ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	$back = json_decode( (string) $json, true );
	if ( ! is_array( $back ) ) { bad( $label, 'JSON does not parse: ' . json_last_error_msg() ); return []; }
	$ids = []; $defined = []; $types = [];
	foreach ( $back['@graph'] as $n ) {
		if ( empty( $n['@type'] ) ) bad( $label, 'node without @type' );
		if ( empty( $n['@id'] ) ) bad( $label, 'top-level node without @id: ' . json_encode( $n['@type'] ) );
		else {
			if ( isset( $ids[ $n['@id'] ] ) ) bad( $label, 'duplicate @id ' . $n['@id'] );
			$ids[ $n['@id'] ] = 1;
			if ( ! preg_match( '#^https?://[^\s]+\#[a-z0-9\-]+$#', $n['@id'] ) && ! preg_match( '#^https?://#', $n['@id'] ) ) bad( $label, 'odd @id ' . $n['@id'] );
		}
	}
	walk( $back['@graph'], static function ( $o ) use ( &$defined, &$types, $label, $REQUIRED, $VOCAB ) {
		if ( isset( $o['@id'] ) && count( $o ) > 1 ) $defined[ $o['@id'] ] = 1;
		if ( ! isset( $o['@type'] ) ) return;
		foreach ( (array) $o['@type'] as $t ) {
			$types[ $t ] = 1;
			foreach ( array_merge( $REQUIRED[ $t ] ?? [], isset( $o['@id'] ) ? ( $REQUIRED[ $t . '@id' ] ?? [] ) : [] ) as $p ) {
				if ( ! isset( $o[ $p ] ) || '' === $o[ $p ] || [] === $o[ $p ] ) bad( $label, "$t without $p" . ( isset( $o['@id'] ) ? ' (' . $o['@id'] . ')' : '' ) );
			}
			if ( $VOCAB && ! isset( $VOCAB[ $t ] ) ) bad( $label, "type $t not in schema.org" );
		}
		if ( $VOCAB ) foreach ( array_keys( $o ) as $p ) {
			if ( '@' !== $p[0] && 'query-input' !== $p && ! isset( $VOCAB[ $p ] ) ) bad( $label, "property $p not in schema.org" );
		}
		foreach ( $o as $k => $v ) {
			if ( null === $v || '' === $v ) bad( $label, "empty $k on " . json_encode( $o['@type'] ) );
		}
	} );
	walk( $back['@graph'], static function ( $o ) use ( $defined, $label ) {
		foreach ( $o as $k => $v ) {
			foreach ( ( is_array( $v ) && array_is_list( $v ) ) ? $v : [ $v ] as $r ) {
				if ( is_array( $r ) && 1 === count( $r ) && isset( $r['@id'] ) && ! isset( $defined[ $r['@id'] ] ) ) bad( $label, "dangling reference $k -> " . $r['@id'] );
			}
		}
	} );
	return array_keys( $types );
}
function expect_types( array $have, array $want, string $label ) {
	foreach ( $want as $t ) if ( ! in_array( $t, $have, true ) ) bad( $label, "missing type $t" );
}
function find( array $nodes, string $type ): ?array {
	foreach ( $nodes as $n ) if ( in_array( $type, (array) $n['@type'], true ) ) return $n;
	return null;
}

$page = static function ( $slug, $title, $tpl = '' ) {
	return [ 'post' => new WP_Post( [ 'post_type' => 'page', 'post_name' => $slug, 'post_title' => $title ] ), 'template' => $tpl ];
};

// Home.
$GLOBALS['ctx'] = [ 'view' => 'front' ];
$g = ce_schema_nodes();
expect_types( check_graph( $g, 'front' ), [ 'WebSite', 'Organization', 'WebPage', 'WPHeader', 'WPFooter', 'SiteNavigationElement', 'SearchAction' ], 'front' );

// Every article.
$articles = [];
foreach ( glob( $ROOT . '/inc/articles/batch-*.json' ) as $f ) {
	foreach ( json_decode( file_get_contents( $f ), true )['articles'] as $a ) $articles[ $a['slug'] ] = $a;
}
$quotes = 0; $figs = 0;
foreach ( $articles as $slug => $a ) {
	$GLOBALS['ctx'] = [ 'view' => 'single', 'thumb' => 1, 'post' => new WP_Post( [ 'post_type' => 'ce_article', 'post_name' => $slug, 'post_title' => $a['title'], 'post_content' => $a['content'] ] ) ];
	$g = ce_schema_nodes();
	expect_types( check_graph( $g, $slug ), [ 'Article', 'WebPage', 'BreadcrumbList', 'ImageObject', 'WPSideBar' ], $slug );
	$art = find( $g, 'Article' );
	if ( empty( $art['datePublished'] ) || empty( $art['author'] ) ) bad( $slug, 'Article without date or author' );
	$want = substr_count( $a['content'], 'class="quran-citation"' ) + substr_count( $a['content'], 'class="hadith-citation"' );
	if ( count( $art['citation'] ?? [] ) !== $want ) bad( $slug, 'citations ' . count( $art['citation'] ?? [] ) . ", blocks $want" );
	$quotes += $want;
	$figs   += count( $art['associatedMedia'] ?? [] );
}
echo 'articles: ' . count( $articles ) . ", quotations $quotes, figures $figs\n";

// One article in detail.
$a = $articles['quran-wife-beating'];
$GLOBALS['ctx'] = [ 'view' => 'single', 'thumb' => 1, 'post' => new WP_Post( [ 'post_type' => 'ce_article', 'post_name' => 'quran-wife-beating', 'post_title' => $a['title'], 'post_content' => $a['content'] ] ) ];
$g = ce_schema_nodes();
$types = check_graph( $g, 'detail' );
expect_types( $types, [ 'Quotation', 'Chapter', 'Book' ], 'detail' );
$gm = ce_schema_glossary_mentions( 'Text [glossary term="Tawhid"] and [glossary term="No Such Term"].' );
if ( 1 !== count( $gm ) || 'https://ce.test/glossary/#term-tawhid' !== $gm[0]['@id'] ) bad( 'glossary mention', json_encode( $gm ) );
$hero = null; foreach ( $g as $n ) if ( str_ends_with( $n['@id'], '#primaryimage' ) ) $hero = $n;
if ( ! $hero || empty( $hero['creditText'] ) || empty( $hero['license'] ) ) bad( 'detail', 'hero image without credit or licence' );
$en = null; foreach ( $g as $n ) if ( 'Quotation' === $n['@type'] && 'en' === $n['inLanguage'] ) { $en = $n; break; }
if ( ! $en || empty( $en['translationOfWork'] ) ) bad( 'detail', 'translated quotation without translationOfWork' );
$book = find( $g, 'Book' );
if ( getenv( 'CE_SCHEMA_DUMP' ) ) echo json_encode( $g, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ), "\n";
echo "detail: " . count( $g ) . " nodes; first book {$book['name']}\n";

// With an author set in Theme Options: a Person node, referred to by @id.
$GLOBALS['ctx']['options'] = [ 'ce_author_name' => 'A. Writer', 'ce_author_url' => 'https://ce.test/about/' ];
$g = ce_schema_nodes(); expect_types( check_graph( $g, 'author' ), [ 'Person' ], 'author' );
if ( ( find( $g, 'Article' )['author']['@id'] ?? '' ) !== 'https://ce.test/#author' ) bad( 'author', 'Article author is not the Person' );

// Hadith grading only where the reference states it.
$h = ce_schema_scripture( '<div class="hadith-citation"><div class="hadith-arabic">x</div><div class="hadith-translation">"y"</div><div class="hadith-ref">Sunan al-Tirmidhī 2459 (graded ḥasan)</div></div><div class="hadith-citation"><div class="hadith-arabic">x</div><div class="hadith-translation">"y"</div><div class="hadith-ref">Ṣaḥīḥ al-Bukhārī 6018; Ṣaḥīḥ Muslim 47</div></div>', 'https://ce.test/a/' );
$q = array_values( array_filter( $h['nodes'], static function ( $n ) { return 'Quotation' === $n['@type'] && 'ar' === $n['inLanguage']; } ) );
if ( ( $q[0]['additionalProperty']['value'] ?? '' ) !== 'ḥasan' ) bad( 'hadith', 'grading not read' );
if ( isset( $q[1]['additionalProperty'] ) ) bad( 'hadith', 'grading invented' );
if ( ( $q[1]['identifier'] ?? [] ) !== [ 'Ṣaḥīḥ al-Bukhārī 6018', 'Ṣaḥīḥ Muslim 47' ] ) bad( 'hadith', 'identifiers ' . json_encode( $q[1]['identifier'] ?? null, JSON_UNESCAPED_UNICODE ) );

// FAQ, glossary, quiz, ask, about, search.
$GLOBALS['ctx'] = $page( 'faq', 'FAQ', 'page-faq.php' );
$g = ce_schema_nodes(); expect_types( check_graph( $g, 'faq' ), [ 'FAQPage', 'Question', 'Answer', 'WebPage' ], 'faq' );
if ( count( find( $g, 'FAQPage' )['mainEntity'] ) !== 18 ) bad( 'faq', 'expected 18 questions' );
if ( false !== strpos( file_get_contents( $ROOT . '/page-faq.php' ), 'ld+json' ) ) bad( 'faq', 'template still prints its own block' );

$GLOBALS['ctx'] = $page( 'glossary', 'Glossary', 'page-glossary.php' );
$g = ce_schema_nodes(); expect_types( check_graph( $g, 'glossary' ), [ 'DefinedTermSet', 'DefinedTerm' ], 'glossary' );
if ( count( find( $g, 'DefinedTermSet' )['hasDefinedTerm'] ?? [] ) !== count( ce_get_glossary_terms() ) ) bad( 'glossary', 'term count' );
if ( false !== strpos( file_get_contents( $ROOT . '/page-glossary.php' ), 'ld+json' ) ) bad( 'glossary', 'template still prints its own block' );

$GLOBALS['ctx'] = $page( 'quiz', 'Quiz', 'page-quiz.php' );
$g = ce_schema_nodes(); $t = check_graph( $g, 'quiz' ); expect_types( $t, [ 'Quiz', 'Question', 'Answer', 'FAQPage' ], 'quiz' );
if ( in_array( 'WPHeader', $t, true ) ) bad( 'quiz', 'frame nodes on a standalone page' );
foreach ( find( $g, 'Quiz' )['hasPart'] as $qq ) if ( isset( $qq['acceptedAnswer'] ) ) bad( 'quiz', 'acceptedAnswer on a quiz with no right answers' );
$qhtml = ce_standalone_faq_insert( file_get_contents( ce_get_quiz_file() ), 'quiz' );
faq_visible( $g, $qhtml, 'quiz' );
echo 'quiz questions: ' . count( find( $g, 'Quiz' )['hasPart'] ) . "\n";

$GLOBALS['ctx'] = $page( 'ask-a-question', 'Ask', 'page-ask-a-question.php' );
expect_types( check_graph( ce_schema_nodes(), 'ask' ), [ 'ContactPage' ], 'ask' );
$GLOBALS['ctx'] = $page( 'about-compelling-evidence', 'About' );
expect_types( check_graph( ce_schema_nodes(), 'about' ), [ 'AboutPage' ], 'about' );
$GLOBALS['ctx'] = [ 'view' => 'search' ];
expect_types( check_graph( ce_schema_nodes(), 'search' ), [ 'SearchResultsPage' ], 'search' );
$GLOBALS['ctx'] = [ 'view' => '404' ];
check_graph( ce_schema_nodes(), '404' );

// Topic archive.
$list = [];
foreach ( array_slice( $articles, 0, 4 ) as $slug => $a ) $list[] = new WP_Post( [ 'post_type' => 'ce_article', 'post_name' => $slug, 'post_title' => $a['title'] ] );
$GLOBALS['ctx'] = [ 'view' => 'tax', 'term' => (object) [ 'term_id' => 3, 'name' => 'The Quran', 'slug' => 'the-quran' ], 'list' => $list ];
$g = ce_schema_nodes(); expect_types( check_graph( $g, 'topic' ), [ 'CollectionPage', 'ItemList', 'ListItem' ], 'topic' );
if ( find( $g, 'WebPage' ) ) bad( 'topic', 'plain WebPage beside the CollectionPage' );

// Reader questions.
$qpost = static function ( $content ) { return new WP_Post( [ 'post_type' => 'ce_question', 'post_name' => 'why', 'post_title' => 'Why pray five times?', 'post_content' => $content ] ); };
$GLOBALS['ctx'] = [ 'view' => 'single', 'post' => $qpost( '<p>Because the day has five turning points.</p>' ), 'qstatus' => [ 'published' ] ];
$g = ce_schema_nodes(); expect_types( check_graph( $g, 'question' ), [ 'QAPage', 'Question', 'Answer', 'Article' ], 'question' );
if ( 1 !== find( $g, 'Question' )['answerCount'] ) bad( 'question', 'answerCount' );
$GLOBALS['ctx'] = [ 'view' => 'single', 'post' => $qpost( '' ), 'qstatus' => [ 'published' ] ];
$g = ce_schema_nodes(); check_graph( $g, 'question-pending' );
$qn = find( $g, 'Question' );
if ( ! $qn || isset( $qn['acceptedAnswer'] ) || 0 !== $qn['answerCount'] ) bad( 'question-pending', 'answer marked before it exists' );
$GLOBALS['ctx'] = [ 'view' => 'single', 'post' => $qpost( 'x' ), 'qstatus' => [ 'new' ] ];
if ( find( ce_schema_nodes(), 'QAPage' ) ) bad( 'question-new', 'QAPage on an unpublished question' );

// Each FAQPage question must be on the page itself, outside the JSON-LD.
function faq_visible( array $graph, string $html, string $label ): void {
	$faq = find( $graph, 'FAQPage' );
	if ( ! $faq || count( $faq['mainEntity'] ) < 4 ) { bad( $label, 'no FAQPage with 4+ questions' ); return; }
	$visible = html_entity_decode( strip_tags( preg_replace( '#<(script|style)[^>]*>.*?</\1>#s', '', $html ) ), ENT_QUOTES | ENT_HTML5 );
	foreach ( $faq['mainEntity'] as $q ) {
		if ( 'Question' !== $q['@type'] || empty( $q['acceptedAnswer']['text'] ) ) bad( $label, 'Question without an acceptedAnswer' );
		if ( false === strpos( $visible, $q['name'] ) ) bad( $label, 'question not on the page: ' . $q['name'] );
		if ( false === strpos( $visible, html_entity_decode( strip_tags( $q['acceptedAnswer']['text'] ), ENT_QUOTES | ENT_HTML5 ) ) ) bad( $label, 'answer not on the page: ' . $q['name'] );
	}
}

// Journeys: one graph per page, FAQPage from the same data as the visible FAQ.
if ( false !== strpos( file_get_contents( $ROOT . '/template-parts/journey/document.php' ), "\$j['schema']" ) ) bad( 'journey', 'document.php still prints journey.json blocks' );
foreach ( glob( $ROOT . '/journeys/data/*/journey.json' ) as $f ) {
	$key = basename( dirname( $f ) );
	$j   = ce_journey_load( dirname( $f ) . '/' );
	$GLOBALS['ctx'] = $page( 'journey/' . $key, $key, 'page-journey.php' );
	$GLOBALS['ctx']['post']->post_name = 'journeys/' . $key;
	ce_schema_journey( $j );
	$g = ce_schema_nodes();
	$before = array_unique( array_merge( ...array_map( static function ( $n ) { return (array) $n['@type']; }, $j['schema'] ) ) );
	$t = check_graph( $g, "journey $key" );
	expect_types( $t, array_merge( $before, [ 'LearningResource', 'ItemList' ] ), "journey $key" );
	if ( in_array( 'WPHeader', $t, true ) ) bad( "journey $key", 'frame nodes on a standalone page' );
	$steps = find( $g, 'LearningResource' )['hasPart']['itemListElement'];
	$want  = count( array_filter( $j['screens'], static function ( $s ) { return empty( $s['template'] ); } ) );
	if ( count( $steps ) !== $want ) bad( "journey $key", 'steps ' . count( $steps ) . " of $want" );
	$html = '<html><head><title>' . $j['seo']['title'] . '</title>' . ce_schema_script() . '</head><body><h1>x</h1></body></html>';
	ce_schema_journey( null, true );
	$out = ce_standalone_html_seo( $html, home_url( '/journeys/' . $key . '/' ), $key );
	if ( 1 !== substr_count( $out, 'application/ld+json' ) ) bad( "journey $key", 'not one block' );
	preg_match( '#<script type="application/ld\+json">(.*?)</script>#s', $out, $m );
	$data = json_decode( $m[1], true );
	$t2   = check_graph( $data['@graph'], "journey $key standalone" );
	if ( 1 !== count( array_keys( $t2, 'FAQPage', true ) ) ) bad( "journey $key", 'expected one FAQPage' );
	$full = ce_standalone_html_seo( ce_journey_render( dirname( $f ) . '/' ), home_url( '/journeys/' . $key . '/' ), $key );
	preg_match( '#<script type="application/ld\+json">(.*?)</script>#s', $full, $m );
	faq_visible( json_decode( $m[1], true )['@graph'], $full, "journey $key page" );
	expect_types( $t2, [ 'LearningResource', 'Article', 'WebPage', 'Organization', 'FAQPage', 'Question', 'Answer' ], "journey $key standalone" );
	if ( empty( find( $data['@graph'], 'WebPage' )['speakable'] ) ) bad( "journey $key", 'speakable lost' );
}

// Rank Math: merge into its graph, no second block, one node per family.
function rm_graph( string $url ): array {
	$h = 'https://ce.test/';
	return [
		'WebSite'      => [ '@type' => 'WebSite', '@id' => $h . '#website', 'name' => 'Compelling Evidence', 'url' => $h ],
		'publisher'    => [ '@type' => 'Organization', '@id' => $h . '#organization', 'name' => 'Compelling Evidence', 'url' => $h ],
		'WebPage'      => [ '@type' => 'WebPage', '@id' => $url . '#webpage', 'url' => $url, 'name' => 'x', 'isPartOf' => [ '@id' => $h . '#website' ] ],
		'richSnippet'  => [ '@type' => 'BlogPosting', '@id' => $url . '#richSnippet', 'headline' => 'x', 'author' => [ '@id' => $h . '#author' ], 'datePublished' => '2026-01-01', 'publisher' => [ '@id' => $h . '#organization' ] ],
		'author'       => [ '@type' => 'Person', '@id' => $h . '#author', 'name' => 'Editor' ],
	];
}
foreach ( [ 'article', 'question', 'faq' ] as $which ) {
	if ( 'article' === $which ) {
		$a = $articles['quran-wife-beating'];
		$GLOBALS['ctx'] = [ 'rm' => 1, 'view' => 'single', 'thumb' => 1, 'post' => new WP_Post( [ 'post_type' => 'ce_article', 'post_name' => 'quran-wife-beating', 'post_title' => $a['title'], 'post_content' => $a['content'] ] ) ];
	} elseif ( 'question' === $which ) {
		$GLOBALS['ctx'] = [ 'rm' => 1, 'view' => 'single', 'post' => $qpost( '<p>Answer.</p>' ), 'qstatus' => [ 'published' ] ];
	} else {
		$GLOBALS['ctx'] = $page( 'faq', 'FAQ', 'page-faq.php' ) + [ 'rm' => 1 ];
	}
	ob_start(); ce_schema_jsonld(); $printed = ob_get_clean();
	if ( '' !== $printed ) bad( "rm $which", 'theme printed its own block beside Rank Math' );
	$url    = get_permalink();
	$merged = ce_rankmath_json_ld( rm_graph( $url ) );
	$nodes  = array_values( array_filter( $merged, 'is_array' ) );
	$t      = check_graph( $nodes, "rm $which" );
	$count  = [];
	foreach ( $nodes as $n ) foreach ( (array) $n['@type'] as $ty ) $count[ $ty ] = ( $count[ $ty ] ?? 0 ) + 1;
	foreach ( [ 'WebSite', 'Organization', 'BreadcrumbList' ] as $ty ) if ( ( $count[ $ty ] ?? 0 ) > 1 ) bad( "rm $which", "two $ty nodes" );
	$pages = 0; foreach ( [ 'WebPage', 'QAPage', 'CollectionPage', 'ContactPage' ] as $ty ) $pages += $count[ $ty ] ?? 0;
	if ( 1 !== $pages ) bad( "rm $which", "$pages page nodes" );
	if ( ( $count['Article'] ?? 0 ) + ( $count['BlogPosting'] ?? 0 ) > 1 ) bad( "rm $which", 'two article nodes' );
	if ( 'article' === $which ) {
		if ( empty( $merged['richSnippet']['citation'] ) ) bad( 'rm article', "Rank Math's article did not take the citations" );
		expect_types( $t, [ 'Quotation', 'ImageObject', 'WPHeader' ], 'rm article' );
	}
	if ( 'question' === $which ) {
		if ( 'QAPage' !== $merged['WebPage']['@type'] ) bad( 'rm question', 'page not typed QAPage' );
		expect_types( $t, [ 'Question', 'Answer' ], 'rm question' );
	}
	if ( 'faq' === $which ) expect_types( $t, [ 'FAQPage' ], 'rm faq' );
}

echo "graphs checked: $runs, failures: $fail\n";
exit( $fail ? 1 : 0 );
