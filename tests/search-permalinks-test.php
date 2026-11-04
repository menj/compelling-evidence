<?php
/**
 * Stub test of pretty search URLs (inc/ce-search-permalinks.php): the 301
 * from ?s=, empty searches, pagination, the plugin fallback base and the
 * flush on a theme version change only.
 *
 * Run: php tests/search-permalinks-test.php
 */
define( 'ABSPATH', '/' );
$GLOBALS['opt'] = [];
$GLOBALS['qv']  = [];
$GLOBALS['redirect'] = null;
$GLOBALS['flushes']  = 0;
$GLOBALS['version']  = '2.16.0';
function add_action( ...$a ) {} function add_filter( ...$a ) {}
function get_option( $k, $d = false ) { return array_key_exists( $k, $GLOBALS['opt'] ) ? $GLOBALS['opt'][ $k ] : $d; }
function update_option( $k, $v ) { $GLOBALS['opt'][ $k ] = $v; return true; }
function delete_option( $k ) { unset( $GLOBALS['opt'][ $k ] ); }
function sanitize_title( $s ) { return trim( preg_replace( '/[^a-z0-9]+/', '-', strtolower( (string) $s ) ), '-' ); }
function get_query_var( $k ) { return $GLOBALS['qv'][ $k ] ?? ''; }
function is_search() { return isset( $GLOBALS['qv']['s'] ); }
function is_admin() { return false; }
function esc_url_raw( $u ) { return (string) $u; }
function wp_unslash( $v ) { return $v; }
function wp_parse_url( $u, $c = -1 ) { return parse_url( $u, $c ); }
function home_url( $p = '' ) { return 'https://ce.test' . $p; }
function flush_rewrite_rules( $hard = true ) { $GLOBALS['flushes']++; }
class Redirected extends Exception {}
function wp_safe_redirect( $url, $status ) { $GLOBALS['redirect'] = [ $url, $status ]; throw new Redirected(); }
class WP_Rewrite { public $search_base = 'search'; public function using_permalinks() { return true; } }
class Theme { public function get( $k ) { return $GLOBALS['version']; } }
function wp_get_theme() { return new Theme(); }
$GLOBALS['wp_rewrite'] = new WP_Rewrite();
require dirname( __DIR__ ) . '/inc/ce-search-permalinks.php';

$fail = 0;
function check( $label, $ok ) { global $fail; echo ( $ok ? 'ok   ' : 'FAIL ' ) . $label . "\n"; if ( ! $ok ) $fail++; }
function run( $uri, $qv ) {
	$_SERVER['REQUEST_URI'] = $uri; $GLOBALS['qv'] = $qv; $GLOBALS['redirect'] = null;
	try { ce_pretty_search_redirect(); } catch ( Redirected $e ) {}
	return $GLOBALS['redirect'];
}

check( 'query-string search gets a 301', [ 'https://ce.test/search/free%20will/', 301 ] === run( '/?s=free+will', [ 's' => 'free will' ] ) );
check( 'empty search is left alone', null === run( '/?s=', [ 's' => '' ] ) );
check( 'blank search is left alone', null === run( '/?s=+', [ 's' => ' ' ] ) );
check( 'pretty URL is not redirected', null === run( '/search/evil/', [ 's' => 'evil' ] ) );
check( 'paged search keeps its page', [ 'https://ce.test/search/evil/page/2/', 301 ] === run( '/?s=evil&paged=2', [ 's' => 'evil', 'paged' => 2 ] ) );
check( 'pretty paged URL is not redirected', null === run( '/search/evil/page/2/', [ 's' => 'evil', 'paged' => 2 ] ) );

check( 'plugin base is the fallback', ( $GLOBALS['opt']['wpseosearch_base'] = 'find' ) && 'find' === ce_search_base() );
$GLOBALS['opt']['ce_search_base'] = 'Look Up';
check( 'theme base wins and is sanitised', 'look-up' === ce_search_base() );
unset( $GLOBALS['opt']['ce_search_base'], $GLOBALS['opt']['wpseosearch_base'] );

ce_set_search_base();
check( 'search base set on WP_Rewrite', 'search' === $GLOBALS['wp_rewrite']->search_base );

ce_search_flush_on_version();
ce_search_flush_on_version();
check( 'one flush for a new version', 1 === $GLOBALS['flushes'] );
$GLOBALS['version'] = '2.16.1';
ce_search_flush_on_version();
ce_search_flush_on_version();
check( 'one more flush after the next version change', 2 === $GLOBALS['flushes'] );

echo $fail ? "\n$fail failed\n" : "\nAll passed\n";
exit( $fail ? 1 : 0 );
