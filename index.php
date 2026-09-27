<?php get_header(); ?>

<main class="ce-archive" id="main">
  <div class="archive-header reveal">
    <p class="section-label">
      <?php
      if (is_category())      echo esc_html(single_cat_title('', false));
      elseif (is_tax())       echo esc_html(single_term_title('', false));
      elseif (is_search())    esc_html_e('Search Results', 'compelling-evidence');
      elseif (is_archive())   esc_html_e('All Articles', 'compelling-evidence');
      else                    esc_html_e('Articles', 'compelling-evidence');
      ?>
    </p>
    <h1>
      <?php
      if (is_search())       echo esc_html__('Results for:', 'compelling-evidence') . ' <em>' . esc_html(get_search_query()) . '</em>';
      elseif (is_archive())  the_archive_title();
      else                   bloginfo('name');
      ?>
    </h1>
    <?php if (is_archive() && get_the_archive_description()) : ?>
      <p class="archive-desc"><?php echo wp_kses_post(get_the_archive_description()); ?></p>
    <?php endif; ?>
  </div>

  <?php if (have_posts()) : ?>
    <div class="grid">
      <?php
      $i = 0;
      while (have_posts()) : the_post();
        $topics = wp_get_post_terms(get_the_ID(), 'ce_topic');
        $topic_name = (!empty($topics) && !is_wp_error($topics)) ? $topics[0]->name : '';
      ?>
      <article class="card reveal">
        <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>" class="card-img-link" tabindex="-1" aria-hidden="true">
          <?php the_post_thumbnail('medium_large', ['class' => 'card-img', 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() )]); ?>
        </a>
        <?php else : ?>
          <?php echo ce_card_placeholder( $topic_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG built from constants in inc/ce-icons.php. ?>
        <?php endif; ?>
        <div class="card-body">
          <?php if ($topic_name) : ?>
            <p class="card-tag"><?php echo esc_html($topic_name); ?></p>
          <?php endif; ?>
          <h2 class="card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
          </h2>
          <p class="card-text"><?php the_excerpt(); ?></p>
          <a href="<?php the_permalink(); ?>" class="card-link">
            Read more<span class="screen-reader-text">: <?php echo esc_html( get_the_title() ); ?></span>
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
        </div>
      </article>
      <?php $i++; endwhile; ?>
    </div>

    <div class="pagination">
      <?php
      echo wp_kses_post( paginate_links([
        'prev_text' => '← Prev',
        'next_text' => 'Next →',
        'mid_size'  => 2,
      ] ) );
      ?>
    </div>

  <?php else : ?>
    <div class="no-results">
      <p>No articles found. <a href="<?php echo esc_url(home_url('/')); ?>">Go home</a></p>
    </div>
  <?php endif; ?>
</main>

<?php get_footer(); ?>
