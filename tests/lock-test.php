<?php
/**
 * Stub test of inc/ce-lock.php against a real SQL table (SQLite in memory,
 * through a small stand-in for $wpdb): one taker wins, a fresh lock is
 * refused, a stale one is taken over once, and a release removes only the
 * releaser's own lock. Run: php tests/lock-test.php
 *
 * @since 2.18.7
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}

define( 'ABSPATH', __DIR__ . '/' );

/** Enough of $wpdb for inc/ce-lock.php. */
class CE_Test_Wpdb {
	public $options = 'wp_options';
	private $db;
	public function __construct( $file ) {
		// Each instance is its own connection to the same database file, as
		// two web requests are.
		$this->db = new PDO( 'sqlite:' . $file );
		$this->db->exec( 'CREATE TABLE IF NOT EXISTS wp_options (option_name TEXT PRIMARY KEY, option_value TEXT, autoload TEXT)' );
	}
	public function prepare( $sql, ...$args ) {
		$db = $this->db;
		return vsprintf( $sql, array_map( function ( $a ) use ( $db ) { return $db->quote( (string) $a ); }, $args ) );
	}
	public function query( $sql ) {
		return $this->db->exec( str_replace( 'INSERT IGNORE', 'INSERT OR IGNORE', $sql ) );
	}
	public function get_var( $sql ) {
		$v = $this->db->query( $sql )->fetchColumn();
		return false === $v ? null : $v;
	}
}
$ce_db_file     = tempnam( sys_get_temp_dir(), 'ce-lock-' );
$ce_request_a   = new CE_Test_Wpdb( $ce_db_file );
$ce_request_b   = new CE_Test_Wpdb( $ce_db_file );
$GLOBALS['wpdb'] = $ce_request_a;
function wp_generate_password( $n ) { return bin2hex( random_bytes( (int) ( $n / 2 ) ) ); }
function wp_cache_delete() { return true; }
require dirname( __DIR__ ) . '/inc/ce-lock.php';

$ce_fail = 0;
function ce_check( $ok, $label ) {
	global $ce_fail;
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . $label . "\n";
	$ce_fail += $ok ? 0 : 1;
}
$held = function () {
	return (string) $GLOBALS['wpdb']->get_var( "SELECT option_value FROM wp_options WHERE option_name = 'ce_t'" );
};

$a = ce_lock_take( 'ce_t', 60 );
ce_check( '' !== $a && '|' . $a === substr( $held(), -strlen( '|' . $a ) ), 'the first taker gets the lock' );
ce_check( '' === ce_lock_take( 'ce_t', 60 ), 'a fresh lock is refused' );
ce_lock_release( 'ce_t', 'someone-else' );
ce_check( ce_lock_holds( 'ce_t', $a ), 'a release with another token leaves the lock alone' );

$GLOBALS['wpdb']->query( "UPDATE wp_options SET option_value = '" . ( time() - 3600 ) . "|old' WHERE option_name = 'ce_t'" );
$b = ce_lock_take( 'ce_t', 60 );
ce_check( '' !== $b && ce_lock_holds( 'ce_t', $b ), 'a stale lock is taken over' );
ce_check( '' === ce_lock_take( 'ce_t', 60 ), 'and only once' );
ce_lock_release( 'ce_t', $a );
ce_check( ce_lock_holds( 'ce_t', $b ) && ! ce_lock_renew( 'ce_t', $a ), 'the old holder can neither release nor renew the new holder\'s lock' );
ce_lock_release( 'ce_t', $b );
ce_check( '' === $held(), 'the holder releases it' );
ce_check( '' !== ce_lock_take( 'ce_t', 60 ), 'and it can be taken again' );

// Renewal keeps a long-running holder from looking dead.
$GLOBALS['wpdb']->query( "DELETE FROM wp_options WHERE option_name = 'ce_r'" );
$r = ce_lock_take( 'ce_r', 60 );
$GLOBALS['wpdb']->query( "UPDATE wp_options SET option_value = '" . ( time() - 3600 ) . "|" . $r . "' WHERE option_name = 'ce_r'" );
ce_check( ce_lock_renew( 'ce_r', $r ) && '' === ce_lock_take( 'ce_r', 60 ), 'a renewed lock is not taken over' );

// Notes.
ce_check( ! ce_lock_note( 'ce_n' ) && ce_lock_note( 'ce_n', true ) && ce_lock_note( 'ce_n' ) && ! ce_lock_note( 'ce_n', false ) && ! ce_lock_note( 'ce_n' ), 'a note is set, read and cleared' );

// Two requests, two connections: while one holds the lock the other is
// refused, and it gets the lock once the first lets go.
$GLOBALS['wpdb'] = $ce_request_a;
$GLOBALS['wpdb']->query( "DELETE FROM wp_options WHERE option_name = 'ce_t2'" );
$first = ce_lock_take( 'ce_t2', 60 );
$GLOBALS['wpdb'] = $ce_request_b;
ce_check( '' !== $first && '' === ce_lock_take( 'ce_t2', 60 ), 'a second connection is refused while the first holds the lock' );
$GLOBALS['wpdb'] = $ce_request_a;
ce_lock_release( 'ce_t2', $first );
$GLOBALS['wpdb'] = $ce_request_b;
ce_check( '' !== ce_lock_take( 'ce_t2', 60 ), 'the second connection gets it after the release' );
@unlink( $ce_db_file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors

echo $ce_fail ? "$ce_fail failed\n" : "all passed\n";
exit( $ce_fail ? 1 : 0 );
