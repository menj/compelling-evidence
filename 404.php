<?php
/**
 * 404 Not Found template: CE Theme.
 *
 * Opens on a random "excuse" from inc/ce-404.php. Each excuse riffs on an
 * argument the site treats seriously and links to that article.
 * "Hear another excuse" cycles them (assets/js/ce-404.js); without
 * JavaScript the same link reloads the page with ?excuse=N.
 */
get_header();

$ce_excuses = ce_404_excuses_resolved();
$ce_count   = count( $ce_excuses );
$ce_req     = isset( $_GET['excuse'] ) ? absint( wp_unslash( $_GET['excuse'] ) ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only display choice.
$ce_idx     = ( null !== $ce_req ) ? $ce_req % $ce_count : wp_rand( 0, $ce_count - 1 );
$ce_excuse  = $ce_excuses[ $ce_idx ];
$ce_next    = add_query_arg( 'excuse', ( $ce_idx + 1 ) % $ce_count );

$ce_recent = new WP_Query( [
    'post_type'           => 'ce_article',
    'posts_per_page'      => 3,
    'post_status'         => 'publish',
    'orderby'             => 'date',
    'order'               => 'DESC',
    'no_found_rows'       => true,
    'ignore_sticky_posts' => true,
] );
?>

<main id="main" role="main">
<section class="not-found-hero">

  <p class="not-found-kicker">Exhibit 404 <span aria-hidden="true">·</span> Evidence not found</p>

  <div class="not-found-code" aria-hidden="true">404</div>

  <div class="nf-excuse" id="nf-excuse" data-index="<?php echo esc_attr( $ce_idx ); ?>" aria-live="polite">
    <h1 class="not-found-title" id="nf-title"><?php echo esc_html( $ce_excuse['title'] ); ?></h1>
    <p class="not-found-sub" id="nf-body"><?php echo esc_html( $ce_excuse['body'] ); ?></p>
    <p class="nf-source">
      <span class="nf-source-label">Cf. <span id="nf-label"><?php echo esc_html( $ce_excuse['label'] ); ?></span></span>
      <a id="nf-link" href="<?php echo esc_url( $ce_excuse['url'] ); ?>"><span id="nf-cta"><?php echo esc_html( $ce_excuse['cta'] ); ?></span> <span aria-hidden="true">→</span></a>
    </p>
  </div>

  <a class="nf-another" id="nf-another" href="<?php echo esc_url( $ce_next ); ?>" rel="nofollow">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a9 9 0 1 1-3-6.7L21 8"/><path d="M21 3v5h-5"/></svg>
    Hear another excuse
  </a>

  <div class="not-found-actions">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="not-found-btn not-found-btn--primary">Go home</a>
    <a href="<?php echo esc_url( home_url( '/quiz' ) ); ?>" class="not-found-btn not-found-btn--secondary">Find your path</a>
    <a href="<?php echo esc_url( home_url( '/articles' ) ); ?>" class="not-found-btn not-found-btn--ghost">Browse articles</a>
  </div>

  <div class="not-found-search" role="search">
    <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
      <input type="search" name="s" placeholder="Search a question or topic…" aria-label="Search" autocomplete="off">
      <button type="submit">Search</button>
    </form>
  </div>

  <?php if ( $ce_recent->have_posts() ) : ?>
  <nav class="nf-recent" aria-label="Recent case files">
    <p class="nf-recent-label">Recent case files</p>
    <ul>
      <?php while ( $ce_recent->have_posts() ) : $ce_recent->the_post(); ?>
        <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
      <?php endwhile; wp_reset_postdata(); ?>
    </ul>
  </nav>
  <?php endif; ?>

  <script type="application/json" id="nf-excuses"><?php echo wp_json_encode( $ce_excuses, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ); ?></script>

</section>
</main><!-- /#main -->

<?php get_footer(); ?>
