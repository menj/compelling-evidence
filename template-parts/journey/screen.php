<?php
/**
 * Journey template: one chapter. Its body comes from
 * journeys/data/<key>/screens/<id>.html as written, or from a shared part
 * when journey.json names one ("template": "v2-preview").
 *
 * @since 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="screen<?php echo 0 === $i ? ' active' : ''; ?>" id="s-<?php echo esc_attr( $screen['id'] ); ?>">
<?php
if ( ! empty( $screen['template'] ) ) {
    ce_journey_part( sanitize_key( $screen['template'] ), compact( 'j', 't', 'screen' ) );
} else {
    echo $screen['body']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme content file.
}
?>
</div>
