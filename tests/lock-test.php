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
	public function __construct() {
		$this->db = new PDO( 'sqlite::memory:' );
		$this->db->exec( 'CREATE TABLE wp_options (option_name TEXT PRIMARY KEY, option_value TEXT, autoload TEXT)' );
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
$GLOBALS['wpdb'] = new CE_Test_Wpdb();
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
ce_check( '' !== $a && $held() === $a, 'the first taker gets the lock' );
ce_check( '' === ce_lock_take( 'ce_t', 60 ), 'a fresh lock is refused' );
ce_lock_release( 'ce_t', 'someone-else' );
ce_check( $held() === $a, 'a release with another token leaves the lock alone' );

$GLOBALS['wpdb']->query( "UPDATE wp_options SET option_value = '" . ( time() - 3600 ) . "|old' WHERE option_name = 'ce_t'" );
$b = ce_lock_take( 'ce_t', 60 );
ce_check( '' !== $b && $held() === $b, 'a stale lock is taken over' );
ce_check( '' === ce_lock_take( 'ce_t', 60 ), 'and only once' );
ce_lock_release( 'ce_t', $a );
ce_check( $held() === $b, 'the old holder cannot release the new holder\'s lock' );
ce_lock_release( 'ce_t', $b );
ce_check( '' === $held(), 'the holder releases it' );
ce_check( '' !== ce_lock_take( 'ce_t', 60 ), 'and it can be taken again' );

echo $ce_fail ? "$ce_fail failed\n" : "all passed\n";
exit( $ce_fail ? 1 : 0 );
