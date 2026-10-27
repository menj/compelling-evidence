<?php
/**
 * "Read this on murtadd.org": the return half of the murtadd counterpart link.
 *
 * murtadd.org rebuttals and doubts name the Compelling Evidence article that
 * takes the same question at length (their ce_slug). This card closes the
 * loop: an article lists the murtadd entries that answer the charge itself.
 *
 * The mapping is data only, inc/data/murtadd-counterparts.json, generated from
 * murtadd's seed data by bin/build-murtadd-counterparts.py. Rerun the script
 * when murtadd's ce_slug values change; never edit the JSON by hand.
 *
 * Theme Options → Links & Tooltips → "Link to murtadd.org counterparts"
 * (ce_murtadd_counterparts_enabled, on by default) switches the card off.
 *
 * @package Compelling_Evidence
 */

defined( 'ABSPATH' ) || exit;

/**
 * murtadd entries for one CE article slug.
 *
 * @param string $slug CE article slug.
 * @return array<int,array{type:string,title:string,url:string}>
 */
function ce_murtadd_counterparts( $slug ) {
    static $map = null;
    if ( null === $map ) {
        $file = get_stylesheet_directory() . '/inc/data/murtadd-counterparts.json';
        $raw  = is_readable( $file ) ? file_get_contents( $file ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme data file.
        $map  = $raw ? json_decode( $raw, true ) : array();
        $map  = is_array( $map ) ? $map : array();
    }
    $items = isset( $map[ $slug ] ) && is_array( $map[ $slug ] ) ? $map[ $slug ] : array();
    return array_values(
        array_filter(
            $items,
            static function ( $i ) {
                return is_array( $i ) && ! empty( $i['title'] ) && ! empty( $i['url'] )
                    && 0 === strpos( (string) $i['url'], 'https://murtadd.org/' );
            }
        )
    );
}

/**
 * Print the card for the current article, if it has counterparts.
 *
 * @return void
 */
function ce_render_murtadd_counterparts() {
    if ( '1' !== get_option( 'ce_murtadd_counterparts_enabled', '1' ) || 'ce_article' !== get_post_type() ) {
        return;
    }
    $items = ce_murtadd_counterparts( (string) get_post_field( 'post_name', get_the_ID() ) );
    if ( ! $items ) {
        return;
    }
    $labels = array(
        'rebuttal' => __( 'Rebuttal', 'compelling-evidence' ),
        'doubt'    => __( 'Doubt', 'compelling-evidence' ),
    );
    ?>
    <aside class="ce-murtadd-card" aria-labelledby="ce-murtadd-card-title">
      <p class="ce-murtadd-card-kicker"><?php esc_html_e( 'The charge, answered', 'compelling-evidence' ); ?></p>
      <h2 class="ce-murtadd-card-title" id="ce-murtadd-card-title"><?php esc_html_e( 'Read this on murtadd.org', 'compelling-evidence' ); ?></h2>
      <p class="ce-murtadd-card-body"><?php esc_html_e( 'This article works the question through at length. Murtadd takes the specific objection and answers it directly, with its sources.', 'compelling-evidence' ); ?></p>
      <ul class="ce-murtadd-card-list">
        <?php foreach ( $items as $item ) :
            $type = isset( $labels[ $item['type'] ?? '' ] ) ? $item['type'] : 'rebuttal';
            ?>
          <li>
            <a class="ce-murtadd-card-link" href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener">
              <span class="ce-murtadd-card-type ce-murtadd-card-type--<?php echo esc_attr( $type ); ?>"><?php echo esc_html( $labels[ $type ] ); ?></span>
              <span class="ce-murtadd-card-name"><?php echo esc_html( $item['title'] ); ?></span>
              <span class="ce-murtadd-card-arrow" aria-hidden="true">↗</span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </aside>
    <?php
}
