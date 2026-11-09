<?php
/**
 * Stub test of the bundle review rules (inc/ce-sync-review.php): fingerprints, the decision for a changed,
 * edited, dropped or superseded article, and the removal guard.
 *
 * Run: php tests/sync-review-test.php
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}
define( 'ABSPATH', '/' );
function wp_json_encode( $v ) {
	return json_encode( $v, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
}
function add_action() {}
function add_filter() {}
require dirname( __DIR__ ) . '/inc/ce-sync-review.php';

$fail = 0;
function ce_check( $ok, $label ) {
	global $fail;
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . $label . "\n";
	$fail += $ok ? 0 : 1;
}

$a = [ 'title' => 'T', 'excerpt' => 'E', 'content' => '<p>C</p>', 'faq' => [] ];
$b = [ 'title' => 'T', 'excerpt' => 'E', 'content' => '<p>C2</p>', 'faq' => [] ];
$fa = ce_sync_bundle_fp( $a );
$fb = ce_sync_bundle_fp( $b );
ce_check( $fa !== $fb, 'a changed text changes the fingerprint' );
ce_check( ce_sync_fp_values( 'T', 'E', '<p>C</p>', '' ) === $fa, 'a stored post with no FAQ fingerprints like a bundled entry with none' );
ce_check( ce_sync_fp_values( 'T', 'E', '<p>C</p>', [ [ 'q' => 'Q', 'a' => 'A' ] ] ) !== $fa, 'FAQ items are part of the fingerprint' );

// decide( bundle, stored, recorded, last bundle, ack )
ce_check( 'current' === ce_sync_decide( $fb, $fb, $fb, $fb, '' ), 'equal text is current' );
ce_check( 'current' === ce_sync_decide( $fb, $fa, $fa, $fb, '' ), 'a bundle unchanged since the last write is current, even when the owner edited' );
ce_check( 'update' === ce_sync_decide( $fb, $fa, $fa, $fa, '' ), 'untouched and the bundle changed: update' );
ce_check( 'update' === ce_sync_decide( $fb, $fa, $fa, '', '' ), 'untouched, no bundle record: update' );
ce_check( 'newer' === ce_sync_decide( $fb, 'mine', $fa, $fa, '' ), 'edited and the bundle changed: newer, never overwritten' );
ce_check( 'newer' === ce_sync_decide( $fb, $fa, '', '', '' ), 'no recorded fingerprint counts as edited' );
ce_check( 'kept' === ce_sync_decide( $fb, 'mine', $fa, $fa, $fb ), 'the owner chose to keep against this bundle text' );
ce_check( 'newer' === ce_sync_decide( $fb, 'mine', $fa, $fa, $fa ), 'a keep against an older bundle text does not hold' );

// dropped from the bundle
ce_check( 'trash' === ce_sync_decide_removed( $fa, $fa, '' ), 'dropped and untouched: trash' );
ce_check( 'removed' === ce_sync_decide_removed( 'mine', $fa, '' ), 'dropped and edited: listed' );
ce_check( 'removed' === ce_sync_decide_removed( $fa, '', '' ), 'dropped with no record: listed, never trashed' );
ce_check( 'kept' === ce_sync_decide_removed( 'mine', $fa, 'removed' ), 'dropped, edited, owner chose to keep: quiet' );

// the guard
ce_check( '' === ce_sync_guard( false, true, 145, 145 ), 'the guard allows an ordinary sync' );
ce_check( '' !== ce_sync_guard( true, true, 145, 145 ), 'the kill switch stops removals' );
ce_check( '' !== ce_sync_guard( false, false, 145, 145 ), 'a failed checksum stops removals' );
ce_check( '' !== ce_sync_guard( false, true, 100, 145 ), 'a drop of more than 10 percent stops removals' );
ce_check( '' === ce_sync_guard( false, true, 132, 145 ), 'a drop of under 10 percent is allowed' );
ce_check( '' === ce_sync_guard( false, true, 5, 0 ), 'the first sync has no prior count' );
ce_check( '' !== ce_sync_guard( false, true, 130, 145 ), 'a drop of 10.3 percent is stopped, with no rounding' );
ce_check( '' === ce_sync_guard( false, true, 131, 145 ), 'a drop of exactly 9.7 percent is allowed' );

echo $fail ? "\n$fail check(s) failed\n" : "\nAll checks passed\n";
exit( $fail ? 1 : 0 );
