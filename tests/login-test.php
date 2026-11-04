<?php
/**
 * Stub test of the login screen (inc/ce-login.php).
 * Run: php tests/login-test.php
 *
 * Checks the switch and the plugin step-aside, the link, wordmark and body
 * classes, the note (login screen only, never the interim login), the
 * stylesheet hook, and every title tag: "<screen> | Compelling Evidence",
 * under 60 characters.
 */
define( 'ABSPATH', '/' );
$ROOT = dirname( __DIR__ );
$GLOBALS['opt']     = [];
$GLOBALS['plugin']  = false;
$GLOBALS['enq']     = [];
$GLOBALS['hooks']   = [];
function add_action( $h, $f ) { $GLOBALS['hooks'][ $h ][] = $f; }
function add_filter( $h, $f ) { $GLOBALS['hooks'][ $h ][] = $f; }
function remove_action( ...$a ) {} function remove_filter( ...$a ) {}
function apply_filters( $h, $v ) { return 'ce_login_plugin_active' === $h ? ( $v || $GLOBALS['plugin'] ) : $v; }
function get_option( $k, $d = false ) { return $GLOBALS['opt'][ $k ] ?? $d; }
function get_stylesheet_directory() { return $GLOBALS['ROOT']; }
function get_stylesheet_directory_uri() { return 'https://ce.test/t'; }
function get_theme_mod( $k ) { return 0; }
function home_url( $p = '' ) { return 'https://ce.test' . $p; }
function get_bloginfo( $k ) { return 'Compelling Evidence'; }
function wp_strip_all_tags( $s ) { return trim( strip_tags( (string) $s ) ); }
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_url_raw( $s ) { return (string) $s; }
function sanitize_hex_color( $c ) { return preg_match( '/^#[0-9a-f]{6}$/i', (string) $c ) ? $c : null; }
function sanitize_key( $s ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/i', '', (string) $s ) ); }
function wp_unslash( $s ) { return $s; }
function __( $s ) { return $s; }
function wp_enqueue_style( $h ) { $GLOBALS['enq'][] = $h; }
function wp_add_inline_style( $h, $css ) { $GLOBALS['inline'] = $css; }
function wp_get_theme() { return new class { function get( $k ) { return '9.9.9'; } }; }
function is_front_page() { return false; } function is_singular( $t = '' ) { return false; }
function get_queried_object_id() { return 0; } function get_query_var( $k, $d = '' ) { return $d; }
function get_post( $p = null ) { return null; } function ce_is_rankmath_active() { return false; }
function sanitize_text_field( $s ) { return trim( $s ); }
require $ROOT . '/inc/ce-seo.php';
require $ROOT . '/inc/ce-fonts.php';
require $ROOT . '/inc/ce-login.php';

$fail = 0;
function ok( $c, $what ) { global $fail; echo ( $c ? 'ok   ' : 'FAIL ' ) . $what . "\n"; $fail += $c ? 0 : 1; }

ok( ce_login_design_on(), 'on by default' );
ok( in_array( 'ce_login_assets', $GLOBALS['hooks']['login_enqueue_scripts'] ?? [], true ), 'stylesheet on login_enqueue_scripts' );
ok( 'https://ce.test/' === ce_login_header_url( 'https://wordpress.org/' ), 'mark links home' );
$t = ce_login_header_text( 'Powered by WordPress' );
ok( str_contains( $t, 'ce-login-word__first">Compelling<' ) && str_contains( $t, '>Evidence<' ), 'wordmark' );
ok( in_array( 'ce-login', ce_login_body_class( [ 'login' ] ), true ), 'body class' );
ce_login_assets();
ok( [ 'ce-login' ] === $GLOBALS['enq'], 'stylesheet enqueued' );
ok( str_contains( $GLOBALS['inline'], '--ce-login-logo:url("https://ce.test/t/assets/images/ce-icon.svg")' ), 'CE mark by default' );
$GLOBALS['opt'] = [ 'ce_color_accent' => '#123456', 'ce_heading_font' => 'eb-garamond', 'ce_login_logo_url' => 'https://ce.test/l.png' ];
ce_login_assets();
ok( str_contains( $GLOBALS['inline'], '--accent:#123456;' ), 'Colors tab followed' );
ok( str_contains( $GLOBALS['inline'], "--font-heading:'EB Garamond'" ), 'Typography tab followed' );
ok( str_contains( $GLOBALS['inline'], 'l.png' ), 'logo option used' );
ok( ! str_contains( $GLOBALS['inline'], '--teal' ), 'unchanged colours print nothing' );

$GLOBALS['opt'] = [ 'ce_login_message' => 'Editors <b>only</b>' ];
ok( str_contains( ce_login_message( '' ), 'Editors &lt;b&gt;only' ), 'note printed, escaped' );
$_REQUEST = [ 'action' => 'register' ];
ok( '' === ce_login_message( '' ), 'note absent on register' );
$_REQUEST = [ 'interim-login' => '1' ];
ok( '' === ce_login_message( '' ), 'note absent in the interim login' );
ok( in_array( 'ce-login--interim', ce_login_body_class( [] ), true ), 'interim class' );
$_REQUEST = [];

foreach ( [ 'Log In', 'Lost Password', 'Reset Password', 'Registration Form', 'Check your email', 'Password Reset', '' ] as $screen ) {
	$title = ce_login_title( 'x', $screen );
	ok( mb_strlen( $title ) < 60 && str_ends_with( $title, ' | Compelling Evidence' ) && 1 === substr_count( $title, ' | ' ), "title: $title" );
}

$GLOBALS['opt'] = [ 'ce_login_design' => '0' ];
ok( ! ce_login_design_on(), 'switch off' );
ok( 'https://wordpress.org/' === ce_login_header_url( 'https://wordpress.org/' ), 'off: core link' );
ok( [ 'login' ] === ce_login_body_class( [ 'login' ] ), 'off: no class' );
$GLOBALS['enq'] = [];
ce_login_assets();
ok( [] === $GLOBALS['enq'], 'off: no stylesheet' );

$GLOBALS['opt']    = [];
$GLOBALS['plugin'] = true;
ok( ! ce_login_design_on(), 'login plugin: theme steps aside' );
ok( 'Powered by WordPress' === ce_login_header_text( 'Powered by WordPress' ), 'login plugin: core text kept' );

$src = file_get_contents( $ROOT . '/inc/ce-login.php' );
ok( ! preg_match( '/login_url|site_url\(|wp_login_url/', $src ), 'builds no login URL (WPS Hide Login keeps working)' );

echo $fail ? "\n$fail failed\n" : "\nAll passed\n";
exit( $fail ? 1 : 0 );
