<?php
/**
 * 404 Not Found template — Compelling Evidence
 */
get_header();
?>

<section class="not-found-hero">

  <div class="not-found-code" aria-hidden="true">404</div>

  <h1 class="not-found-title">Page not found</h1>

  <p class="not-found-sub">
    The page you're looking for doesn't exist — but the questions that brought
    you here are worth pursuing.
  </p>

  <div class="not-found-actions">
    <a href="<?php echo esc_url( home_url('/') ); ?>" class="not-found-btn not-found-btn--primary">
      Go home
    </a>
    <a href="<?php echo esc_url( home_url('/quiz') ); ?>" class="not-found-btn not-found-btn--secondary">
      Find your path
    </a>
    <a href="<?php echo esc_url( home_url('/articles') ); ?>" class="not-found-btn not-found-btn--ghost">
      Browse articles
    </a>
  </div>

  <div class="not-found-search" role="search">
    <form method="get" action="<?php echo esc_url( home_url('/') ); ?>">
      <input
        type="search"
        name="s"
        placeholder="Search a question or topic…"
        aria-label="Search"
        autocomplete="off"
      >
      <button type="submit">Search</button>
    </form>
  </div>

</section>

<?php get_footer(); ?>
