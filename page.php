<?php get_header(); ?>

<main class="ce-page" id="main">
  <?php while (have_posts()) : the_post(); ?>
  <article id="page-<?php the_ID(); ?>" <?php post_class('static-page'); ?>>
    <header class="page-hero">
      <div class="page-hero-inner">
        <h1><?php the_title(); ?></h1>
      </div>
    </header>
    <?php if (has_post_thumbnail()) : ?>
      <div class="page-featured-img"><?php the_post_thumbnail('full'); ?></div>
    <?php endif; ?>
    <div class="page-content">
      <?php the_content(); ?>
    </div>
  </article>
  <?php endwhile; ?>
</main>

<?php get_footer(); ?>
