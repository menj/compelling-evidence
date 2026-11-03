<?php
/**
 * Journey template: the whole document. Variables: $j (journey data from
 * ce_journey_load()), $t (strings from ce_journey_strings()).
 *
 * The asset markers are swapped for the shared CSS and JS by
 * ce_standalone_assets(); page-journey.php then runs the same steps as for
 * a stand-alone HTML journey.
 *
 * @since 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$seo = $j['seo'];
?>
<!DOCTYPE html>
<html lang="<?php echo esc_attr( $t['lang'] ); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo esc_html( $seo['title'] ); ?></title>
<meta name="description" content="<?php echo esc_attr( $seo['description'] ); ?>">
<?php foreach ( [ 'og:title', 'og:description', 'og:type', 'og:url', 'og:site_name' ] as $p ) : ?>
<meta property="<?php echo esc_attr( $p ); ?>" content="<?php echo esc_attr( $seo[ $p ] ); ?>">
<?php endforeach; ?>
<?php foreach ( [ 'twitter:card', 'twitter:title', 'twitter:description' ] as $p ) : ?>
<meta name="<?php echo esc_attr( $p ); ?>" content="<?php echo esc_attr( $seo[ $p ] ); ?>">
<?php endforeach; ?>
<link rel="canonical" href="<?php echo esc_url( $seo['canonical'] ); ?>">
<?php foreach ( (array) ( $j['schema'] ?? [] ) as $node ) : ?>
<script type="application/ld+json">
<?php echo ce_journey_json( $node ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON with JSON_HEX_TAG. ?>

</script>
<?php endforeach; ?>
<!-- ce:journey-css -->
<style>
<?php echo $j['css']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme file. ?>
</style>
</head>
<body>
<?php ce_journey_part( 'nav', compact( 'j', 't' ) ); ?>

<?php
foreach ( $j['screens'] as $i => $screen ) {
    ce_journey_part( 'screen', compact( 'j', 't', 'screen', 'i' ) );
}
?>

<?php ce_journey_part( 'scripts', compact( 'j', 't' ) ); ?>
</body>
</html>
