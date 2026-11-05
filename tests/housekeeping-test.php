<?php
/**
 * Stub test of inc/ce-housekeeping.php: a development environment and
 * CE_KEEP_DEV_FILES keep every file; production removes .git and every
 * other .git* entry, .claude, tests, bin and tools, keeps the theme, and
 * runs once per version. Run: php tests/housekeeping-test.php
 *
 * CE_KEEP_DEV_FILES needs a fresh process, so the test runs itself once
 * for that case.
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}

$ce_case = $argv[1] ?? '';
if ( '' === $ce_case ) {
	passthru( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __FILE__ ) . ' env', $ce_a );
	passthru( escapeshellarg( PHP_BINARY ) . ' ' . escapeshellarg( __FILE__ ) . ' keep', $ce_b );
	echo ( $ce_a || $ce_b ) ? "failed\n" : "all passed\n";
	exit( ( $ce_a || $ce_b ) ? 1 : 0 );
}

define( 'ABSPATH', __DIR__ . '/' );
if ( 'keep' === $ce_case ) {
	define( 'CE_KEEP_DEV_FILES', true );
}
$GLOBALS['ce_o']   = array();
$GLOBALS['ce_env'] = 'local';
function add_action() {}
function apply_filters( $h, $v ) { return 'ce_dev_paths' === $h ? array_merge( $v, $GLOBALS['ce_extra'] ?? array() ) : $v; }
function set_transient( $k, $v, $t = 0 ) { $GLOBALS['ce_t'][ $k ] = $v; return true; }
define( 'CE_LOCK_READY', true );
// inc/ce-lock.php is tested on its own (tests/lock-test.php); here the same
// rules over the option stub: a fresh lock is refused, a stale one taken over.
function add_option( $k, $v, $d = '', $a = true ) { if ( isset( $GLOBALS['ce_o'][ $k ] ) ) { return false; } $GLOBALS['ce_o'][ $k ] = $v; return true; }
function ce_lock_take( $k, $ttl ) { if ( isset( $GLOBALS['ce_o'][ $k ] ) && time() - (int) $GLOBALS['ce_o'][ $k ] < $ttl ) { return ''; } $GLOBALS['ce_o'][ $k ] = time() . '|t'; return $GLOBALS['ce_o'][ $k ]; }
function ce_lock_holds( $k, $token ) { return empty( $GLOBALS['ce_lost'] ) && ( $GLOBALS['ce_o'][ $k ] ?? null ) === $token; }
function ce_lock_renew( $k, $token ) { return ce_lock_holds( $k, $token ); }
function ce_lock_release( $k, $token ) { if ( ( $GLOBALS['ce_o'][ $k ] ?? null ) === $token ) { unset( $GLOBALS['ce_o'][ $k ] ); } }
define( 'HOUR_IN_SECONDS', 3600 );
define( 'MINUTE_IN_SECONDS', 60 );
function get_option( $k, $d = false ) { return $GLOBALS['ce_o'][ $k ] ?? $d; }
function update_option( $k, $v ) { $GLOBALS['ce_o'][ $k ] = $v; return true; }
function wp_get_environment_type() { return $GLOBALS['ce_env']; }
function get_stylesheet_directory() { return $GLOBALS['ce_root']; }
require dirname( __DIR__ ) . '/inc/ce-housekeeping.php';

$ce_root = sys_get_temp_dir() . '/ce-hk-' . getmypid() . '-' . bin2hex( random_bytes( 6 ) );
$GLOBALS['ce_root'] = $ce_root;
foreach ( array( '.git/objects', '.github/workflows', '.claude', 'tests', 'bin', 'tools', 'inc', 'assets' ) as $d ) {
	mkdir( "$ce_root/$d", 0700, true );
}
foreach ( array( '.git/HEAD', '.git/objects/ab', '.github/workflows/ci.yml', '.gitignore', '.gitattributes', '.claude/CLAUDE.md', 'tests/a-test.php', 'bin/build.php', 'tools/x.py', 'inc/keep.php', 'style.css', 'assets/.gitkeep' ) as $f ) {
	file_put_contents( "$ce_root/$f", 'x' );
}
$ce_outside = sys_get_temp_dir() . '/ce-hk-outside-' . getmypid();
@mkdir( $ce_outside );
file_put_contents( "$ce_outside/precious", 'x' );
symlink( $ce_outside, "$ce_root/tools/linked" );

$ce_fail = 0;
function ce_check( $ok, $label ) {
	global $ce_fail, $ce_case;
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . "[$ce_case] $label\n";
	$ce_fail += $ok ? 0 : 1;
}
function ce_left( $root ) {
	return array_values( array_filter( array( '.git', '.github', '.gitignore', '.gitattributes', '.claude', 'tests', 'bin', 'tools' ), function ( $p ) use ( $root ) {
		return file_exists( "$root/$p" );
	} ) );
}
$ce_theme = function () use ( $ce_root ) {
	return is_file( "$ce_root/inc/keep.php" ) && is_file( "$ce_root/style.css" ) && is_file( "$ce_root/assets/.gitkeep" );
};

if ( 'keep' === $ce_case ) {
	$GLOBALS['ce_env'] = 'production';
	ce_remove_dev_files( $ce_root, '9.9.9' );
	ce_check( 8 === count( ce_left( $ce_root ) ), 'CE_KEEP_DEV_FILES keeps every file on production' );
} else {
	foreach ( array( 'local', 'development' ) as $e ) {
		$GLOBALS['ce_env'] = $e;
		ce_remove_dev_files( $ce_root, '9.9.9' );
		ce_check( 8 === count( ce_left( $ce_root ) ) && false === get_option( 'ce_dev_files_removed' ), "a $e environment keeps every file" );
	}
	$GLOBALS['ce_env']  = 'production';
	$GLOBALS['ce_lost'] = true;
	ce_remove_dev_files( $ce_root, '9.9.9' );
	ce_check( false === get_option( 'ce_dev_files_removed' ), 'a holder that lost its lock part-way records nothing' );
	$GLOBALS['ce_lost'] = false;
	unset( $GLOBALS['ce_o']['ce_dev_files_lock'] );
	foreach ( array( '.git/objects', '.github/workflows', '.claude', 'tests', 'bin', 'tools' ) as $d ) {
		@mkdir( "$ce_root/$d", 0700, true ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	}
	foreach ( array( '.git/HEAD', '.git/objects/ab', '.github/workflows/ci.yml', '.gitignore', '.gitattributes', '.claude/CLAUDE.md', 'tests/a-test.php', 'bin/build.php', 'tools/x.py' ) as $f ) {
		file_put_contents( "$ce_root/$f", 'x' );
	}
	add_option( 'ce_dev_files_lock', time() );
	ce_remove_dev_files( $ce_root, '9.9.9' );
	ce_check( 8 === count( ce_left( $ce_root ) ) && false === get_option( 'ce_dev_files_removed' ), 'a fresh lock held by another request: nothing is deleted' );
	$GLOBALS['ce_o']['ce_dev_files_lock'] = time() - 3600;
	ce_remove_dev_files( $ce_root, '9.9.9' );
	ce_check( false === get_option( 'ce_dev_files_lock' ), 'a stale lock is taken over, and released after the run' );
	ce_check( array() === ce_left( $ce_root ) && $ce_theme() && '9.9.9' === get_option( 'ce_dev_files_removed' ), 'production removes the .git* entries, .claude, tests, bin and tools, and keeps the theme' );
	ce_check( is_file( "$ce_outside/precious" ), 'a symbolic link is removed without touching its target' );
	mkdir( "$ce_root/tests" );
	ce_remove_dev_files( $ce_root, '9.9.9' );
	ce_check( is_dir( "$ce_root/tests" ), 'the cleanup runs once per version' );

	// A path the remover refuses (it resolves outside the theme) stands for
	// one PHP cannot delete.
	$GLOBALS['ce_extra'] = array( '../' . basename( $ce_outside ) );
	ce_remove_dev_files( $ce_root, '9.9.10' );
	ce_check( '9.9.9' === get_option( 'ce_dev_files_removed' ) && array( '../' . basename( $ce_outside ) ) === ( $GLOBALS['ce_t']['ce_dev_files_retry'] ?? null ) && is_file( "$ce_outside/precious" ), 'a path left behind keeps the new version unrecorded and is queued for a retry' );
	$GLOBALS['ce_extra'] = array();
	ce_remove_dev_files( $ce_root, '9.9.10' );
	ce_check( '9.9.10' === get_option( 'ce_dev_files_removed' ), 'the retry records the version once every path is gone' );
}

exec( 'rm -rf ' . escapeshellarg( $ce_root ) . ' ' . escapeshellarg( $ce_outside ) );
exit( $ce_fail ? 1 : 0 );
