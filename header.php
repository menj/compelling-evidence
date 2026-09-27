<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="profile" href="https://gmpg.org/xfn/11">
  <!-- Favicons -->
  <link rel="icon" type="image/svg+xml" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/ce-icon.svg' ); ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/favicon-32x32.png' ); ?>">
  <link rel="icon" type="image/png" sizes="16x16" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/favicon-16x16.png' ); ?>">
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/apple-touch-icon.png' ); ?>">
  <link rel="shortcut icon" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/favicon.ico' ); ?>">

  <!-- Preload critical fonts (heading + UI families; see inc/ce-fonts.php) -->
  <?php ce_fonts_preload_tags(); ?>
  <?php if ( is_front_page() || is_home() ) : ?>
  <!-- Preload Arabic fonts — LCP element on homepage is hero-verse-text -->
  <link rel="preload" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/fonts/uthmani-quran.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
  <link rel="preload" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/fonts/ce-hadith-400.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
  <?php endif; ?>
  <?php wp_head(); ?>
<script>
(function() {
  try {
    if (localStorage.getItem('ce_quiz_taken')) {
      var btn = document.getElementById('ce-nav-progress-btn');
      if (btn) btn.style.display = 'inline-flex';
    }
  } catch(e) {}
})();
</script>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<nav class="ce-nav" id="ce-nav">
  <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-logo">
    <?php if (has_custom_logo()) : ?>
      <?php the_custom_logo(); ?>
    <?php else : ?>
      <span class="nav-logo-text">
        <span class="nav-logo-ce">Compelling</span><span class="nav-logo-ev"> Evidence</span>
      </span>
    <?php endif; ?>
  </a>

  <button class="nav-toggle" aria-label="Toggle menu" aria-expanded="false">
    <span></span><span></span><span></span>
  </button>

  <?php
  wp_nav_menu([
    'theme_location' => 'primary',
    'container'      => false,
    'menu_class'     => 'nav-links',
    'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
    'fallback_cb'    => function() { ?>
      <ul class="nav-links">
        <li><a href="<?php echo esc_url(home_url('/journeys')); ?>"><?php esc_html_e('Journeys', 'compelling-evidence'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/articles')); ?>"><?php esc_html_e('Articles', 'compelling-evidence'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/qa')); ?>"><?php esc_html_e('Q&amp;A', 'compelling-evidence'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/ask-a-question')); ?>"><?php esc_html_e('Ask a Question', 'compelling-evidence'); ?></a></li>
        <li><a href="<?php echo esc_url(home_url('/faq')); ?>"><?php esc_html_e('FAQ', 'compelling-evidence'); ?></a></li>
      </ul>
    <?php },
  ]);
  ?>

  <div class="nav-actions">
    <a href="<?php echo esc_url(home_url('/my-progress')); ?>" id="ce-nav-progress-btn" class="nav-progress-link" title="<?php esc_attr_e('My Progress', 'compelling-evidence'); ?>" style="display:none;">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </a>
    <a href="#" class="nav-random" id="ce-random-btn" title="Random article">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M2 18h1.4c1.3 0 2.5-.6 3.3-1.7l6.1-8.6c.8-1.1 2-1.7 3.3-1.7H22"/><path d="m18 2 4 4-4 4"/><path d="M2 6h1.9c1 0 1.8.4 2.5 1"/><path d="m22 18-4 4-4-4"/><path d="M17.5 17c.7.7 1.6 1 2.5 1H22"/></svg>
      Random
    </a>
  </div>
</nav>

<!-- ── FEATURED STRIP ─────────────────────────────────────────────────────── -->
<div class="featured-strip" id="featured-strip">
  <div class="featured-strip-inner">
    <span class="featured-strip-label">
      <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
      Read first
    </span>
    <div class="featured-strip-links">
      <a href="<?php echo esc_url(home_url('/articles/purpose-of-life/')); ?>" class="featured-link">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        What is the Purpose of Life?
      </a>
      <a href="<?php echo esc_url(home_url('/articles/does-god-exist/')); ?>" class="featured-link">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        Does God Exist?
      </a>
      <a href="<?php echo esc_url(home_url('/articles/why-does-anything-exist/')); ?>" class="featured-link">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        Why Does Anything Exist?
      </a>
      <a href="<?php echo esc_url(home_url('/articles/fine-tuning-universe/')); ?>" class="featured-link">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        The Universe Is Absurdly Specific
      </a>
      <a href="<?php echo esc_url(home_url('/articles/problem-of-evil/')); ?>" class="featured-link">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        If God Is Good, Why Suffering?
      </a>
    </div>
  </div>
</div>
