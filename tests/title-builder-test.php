<?php
/**
 * Stub test of the title builder (inc/ce-seo.php, inc/ce-rankmath.php).
 * Run: php tests/title-builder-test.php
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}
define( 'ABSPATH', '/' );
$ROOT = dirname( __DIR__ );
function add_action(...$a) {} function add_filter(...$a) {} function remove_action(...$a) {} function remove_filter(...$a) {}
function get_stylesheet_directory() { return $GLOBALS['ROOT']; }
function wp_strip_all_tags( $s ) { return trim( strip_tags( (string) $s ) ); }
function apply_filters( $h, $v ) { return $v; }
$GLOBALS['ctx'] = [];
function is_front_page() { return ! empty( $GLOBALS['ctx']['front'] ); }
function is_singular( $t = '' ) { return ! empty( $GLOBALS['ctx']['post'] ); }
function get_queried_object_id() { return 1; }
function get_query_var( $k, $d = '' ) { return 'page' === $k ? ( $GLOBALS['ctx']['page'] ?? 0 ) : $d; }
function get_post( $p = null ) { return $GLOBALS['ctx']['post'] ?? null; }
function get_page_uri( $p ) { return $p->post_name; }
function ce_is_rankmath_active() { return ! empty( $GLOBALS['ctx']['rm'] ); }
function get_post_meta( $id, $k, $s ) { return $GLOBALS['ctx']['meta'][ $k ] ?? ''; }
function get_post_field( $f, $id ) { return $GLOBALS['ctx']['post']->post_name; }
function sanitize_text_field( $s ) { return trim( $s ); }
function get_the_title() { return $GLOBALS['ctx']['post']->post_title; }
require $ROOT . '/inc/ce-seo.php';
require $ROOT . '/inc/ce-rankmath.php';

$fail = 0; $n = 0; $before = 0; $max = 0; $rows = [];
function check( $t, $label ) {
	global $fail, $n, $max;
	$n++; $len = mb_strlen( $t, 'UTF-8' ); $max = max( $max, $len );
	if ( $len > 59 || ! str_ends_with( $t, ' | Compelling Evidence' ) || str_contains( $t, '|  ' ) ) { $fail++; echo "FAIL [$label] ($len) $t\n"; }
}
// Articles.
$seen = [];
foreach ( glob( $ROOT . '/inc/articles/batch-*.json' ) as $f ) {
	foreach ( json_decode( file_get_contents( $f ), true )['articles'] as $a ) {
		$p = (object) [ 'post_type' => 'ce_article', 'post_name' => $a['slug'], 'post_title' => $a['title'] ];
		if ( mb_strlen( $a['title'] . ' – Compelling Evidence' ) > 59 ) $before++;
		$GLOBALS['ctx'] = [ 'post' => $p ];
		$t = implode( ' | ', ce_custom_title( [ 'title' => $a['title'], 'site' => 'Compelling Evidence' ] ) );
		check( $t, $a['slug'] );
		if ( isset( $seen[ $t ] ) ) { $fail++; echo "DUP $t\n"; } $seen[ $t ] = 1;
		// Rank Math default output, legacy seeded meta, and an owner's own wording.
		$legacy = json_decode( file_get_contents( $ROOT . '/inc/data/rankmath-legacy-titles.json' ), true )['titles'][ $a['slug'] ];
		$GLOBALS['ctx'] = [ 'post' => $p, 'rm' => 1, 'meta' => [ 'rank_math_title' => $legacy ] ];
		$r = ce_rankmath_title( $legacy );
		if ( $r !== $t ) { $fail++; echo "RM MISMATCH $r / $t\n"; }
		check( $r, 'rm ' . $a['slug'] );
	}
}
$GLOBALS['ctx'] = [ 'post' => (object) [ 'post_type' => 'ce_article', 'post_name' => 'x', 'post_title' => 'X' ], 'rm' => 1, 'meta' => [ 'rank_math_title' => 'My Own Wording - Compelling Evidence' ] ];
$r = ce_rankmath_title( 'My Own Wording - Compelling Evidence' ); echo "owner custom: $r\n"; check( $r, 'owner' );
// Page 3 of a split article keeps its suffix within the limit.
$GLOBALS['ctx'] = [ 'post' => (object) [ 'post_type' => 'ce_article', 'post_name' => $a['slug'], 'post_title' => $a['title'] ], 'page' => 3 ];
$r3 = implode( ' | ', ce_custom_title( [ 'title' => $a['title'], 'site' => 'Compelling Evidence' ] ) ); echo "paged: $r3\n"; check( $r3, 'paged' );
if ( false === strpos( $r3, '(Page 3)' ) ) { $fail++; echo "PAGE SUFFIX LOST $r3\n"; }
if ( false === strpos( $r, 'My Own Wording' ) ) { $fail++; echo "OWNER WORDING LOST $r\n"; }
// Pages, journeys, quiz, topics, archives.
$seed = json_decode( file_get_contents( $ROOT . '/inc/data/seed-pages.json' ), true );
$pages = array_merge( $seed['pages'], $seed['pages_v2'], $seed['journey_pages'], $seed['placeholder_pages'] );
foreach ( $pages as $pg ) {
	$GLOBALS['ctx'] = [ 'post' => (object) [ 'post_type' => 'page', 'post_name' => $pg['slug'], 'post_title' => $pg['title'] ] ];
	$t = implode( ' | ', ce_custom_title( [ 'title' => $pg['title'], 'site' => 'Compelling Evidence' ] ) ); check( $t, 'page' ); $rows[] = $t;
}
foreach ( glob( $ROOT . '/journeys/data/*/journey.json' ) as $f ) {
	$t = ce_title_build( json_decode( file_get_contents( $f ), true )['seo']['title'] ); check( $t, 'journey' ); $rows[] = $t;
}
preg_match( '#<title>(.*?)</title>#s', file_get_contents( $ROOT . '/journeys/quiz.html' ), $m ); $t = ce_title_build( $m[1] ); check( $t, 'quiz' ); $rows[] = $t;
$topics = [];
foreach ( glob( $ROOT . '/inc/articles/batch-*.json' ) as $f ) foreach ( json_decode( file_get_contents( $f ), true )['articles'] as $a ) $topics[ $a['topic'] ] = 1;
foreach ( array_keys( $topics ) as $tp ) { $GLOBALS['ctx'] = []; $t = implode( ' | ', ce_custom_title( [ 'title' => $tp, 'page' => 'Page 2', 'site' => 'x' ] ) ); check( $t, 'topic' ); $rows[] = $t; }
$GLOBALS['ctx'] = [ 'front' => 1 ]; $t = implode( ' | ', ce_custom_title( [ 'title' => 'Compelling Evidence', 'tagline' => 'x' ] ) ); check( $t, 'front' ); $rows[] = $t;
$t = ce_title_build( 'A Very Long Title About Something That Goes On and On Without End' ); check( $t, 'cut' ); $rows[] = $t;
echo implode( "\n", $rows ), "\n";
echo "articles over 59 before (old builder, ' – ' with site): $before of 143\n";
echo "checked $n titles, longest $max, failures $fail\n";
exit( $fail ? 1 : 0 );
