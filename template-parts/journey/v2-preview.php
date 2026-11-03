<?php
/**
 * Journey template: the Volume II preview chapter. Persona text and links
 * come from journey.json "v2_preview"; the chapter cards and labels from
 * the strings file.
 *
 * @since 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$v2 = $j['v2_preview'];
$arrow = '<svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- icon, title and arrow are theme markup.
?>
<div class="page">
  <div class="v2-preview">
    <div class="v2-preview-icon"><?php echo $v2['icon']; ?></div>
    <div class="v2-preview-title"><?php echo $v2['title']; ?></div>
    <div class="v2-preview-sub"><?php echo esc_html( $v2['sub'] ); ?></div>
    <div class="v2-preview-status"><?php echo esc_html( $t['v2_status'] ); ?></div>
    <div class="v2-chapters-grid">
<?php foreach ( $t['v2_chapters'] as $ch ) : ?>
      <div class="v2-ch-card"><div class="v2-ch-num"><?php echo esc_html( $ch[0] ); ?></div><div class="v2-ch-name"><?php echo esc_html( $ch[1] ); ?></div><div class="v2-ch-desc"><?php echo esc_html( $ch[2] ); ?></div></div>
<?php endforeach; ?>
    </div>
    <p style="font-size:0.88rem;color:var(--muted);line-height:1.7;max-width:480px;margin:0 auto 2rem;"><?php echo esc_html( $v2['intro'] ); ?></p>
    <div style="display:flex;flex-direction:column;gap:0.5rem;align-items:center;margin-bottom:2.5rem;">
<?php foreach ( $v2['links'] as $link ) : ?>
      <a class="nq-link" href="<?php echo esc_url( $link['href'] ); ?>" style="font-size:0.85rem;"><?php echo esc_html( $link['text'] ); ?> <?php echo $arrow; ?></a>
<?php endforeach; ?>
    </div>
    <button class="v2-back-link" onclick="goTo('conclusion')"><svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> <?php echo esc_html( $t['v2_back'] ); ?></button>
  </div>
</div>
