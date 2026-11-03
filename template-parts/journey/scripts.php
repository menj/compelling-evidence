<?php
/**
 * Journey template: the page data for assets/js/journey.js, then the
 * marker ce_standalone_assets() swaps for the shared script.
 *
 * @since 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG;
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON with JSON_HEX_TAG.
?>
<script>window.PATH=<?php echo wp_json_encode( $j['key'], $flags ); ?>;
window.CE_JOURNEY = <?php echo ce_journey_json( ce_journey_config( $j ) ); ?>;</script>
<!-- ce:journey-js -->
