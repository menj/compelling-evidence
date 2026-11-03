<?php
/**
 * Journey template: fixed header with the persona name, the current
 * chapter and one progress dot per entry in journey.json "nav.dots".
 *
 * @since 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$first   = $j['screens'][0];
$logo_ce = empty( $j['nav']['logo_ce_style'] ) ? '' : ' style="' . esc_attr( $j['nav']['logo_ce_style'] ) . '"';
$dots    = '';
foreach ( $j['nav']['dots'] as $dot ) {
    if ( ! empty( $dot['sep'] ) ) {
        $dots .= '<div class="np-vol-sep"></div>';
        continue;
    }
    $class = 'np-dot' . ( $dot['id'] === $first['id'] ? ' active' : '' ) . ( empty( $dot['v2'] ) ? '' : ' v2-dot' );
    $dots .= '<div class="' . $class . '" title="' . esc_attr( $dot['title'] ) . '" onclick="goTo(\'' . esc_attr( $dot['id'] ) . '\')"'
        . ( empty( $dot['style'] ) ? '' : ' style="' . esc_attr( $dot['style'] ) . '"' ) . '></div>';
}
$go_first = 'goTo(\'' . esc_attr( $first['id'] ) . '\')';
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- parts escaped above.
?>
<nav class="ce-nav"><span class="nav-logo" onclick="<?php echo $go_first; ?>" style="cursor:pointer;"><span class="nav-logo-text"><span class="nav-logo-ce"<?php echo $logo_ce; ?>><?php echo esc_html( $t['logo_first'] ); ?></span><span class="nav-logo-ev"><?php echo esc_html( $t['logo_second'] ); ?></span></span></span><div class="nav-path"><span class="nav-path-step" onclick="<?php echo $go_first; ?>"><?php echo esc_html( $j['nav']['path_label'] ); ?></span><span class="nav-path-sep">/</span><span class="nav-path-current" id="nav-current"><?php echo esc_html( $first['label'] ); ?></span></div><div class="nav-progress"><?php echo $dots; ?></div></nav>
