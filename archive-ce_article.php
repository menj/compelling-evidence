<?php
/**
 * Article Archive Template — /articles/
 * Organised by topic sections in logical reading order.
 *
 * @since 1.8.1
 */
get_header();

// Topic display order — mirrors the intellectual journey
$topic_order = [
    'Does God Exist?',
    'The Problem of Evil',
    'Ethics Without God?',
    'Science & Evidence',
    'Examining the Quran',
    'Examining the Sources',
    'History & Context',
    'Rights & Freedom',
    'The Inner Journey',
    'The Bigger Picture',
];

$all_terms = get_terms(['taxonomy' => 'ce_topic', 'hide_empty' => true]);
$term_map = [];
if ( $all_terms && ! is_wp_error( $all_terms ) ) {
    foreach ( $all_terms as $t ) {
        $term_map[ $t->name ] = $t;
    }
}

$is_filtered = is_tax('ce_topic');
$active_term = $is_filtered ? get_queried_object() : null;
?>

<div class="articles-archive">

  <div class="archive-hero">
    <span class="archive-hero-eyebrow"><?php esc_html_e('Articles', 'compelling-evidence'); ?></span>
    <h1><?php esc_html_e('The Evidence, Examined', 'compelling-evidence'); ?></h1>
    <p><?php esc_html_e('Every serious objection. Every core argument. Written for the honest inquirer.', 'compelling-evidence'); ?></p>
    <?php
    $total_count = wp_count_posts('ce_article');
    $total_pub   = isset($total_count->publish) ? $total_count->publish : 0;
    ?>
    <span class="ah-count"><?php echo $total_pub; ?> articles across <?php echo count($term_map); ?> topics</span>
  </div>

  <!-- Topic filter -->
  <div class="topic-filter">
    <a href="<?php echo esc_url(get_post_type_archive_link('ce_article')); ?>"
       class="tf-link<?php echo !$is_filtered ? ' active' : ''; ?>">
      <?php esc_html_e('All', 'compelling-evidence'); ?>
    </a>
    <?php foreach ( $topic_order as $topic_name ):
      if ( ! isset( $term_map[ $topic_name ] ) ) continue;
      $t = $term_map[ $topic_name ];
      $active = ($active_term && $active_term->term_id === $t->term_id) ? ' active' : '';
      // Use anchor links on main archive, term links on filtered pages
      $tf_link = $is_filtered ? esc_url(get_term_link($t)) : '#topic-' . esc_attr($t->slug);
    ?>
      <a href="<?php echo $tf_link; ?>" class="tf-link<?php echo $active; ?>">
        <?php echo esc_html($t->name); ?><span class="tf-count"><?php echo $t->count; ?></span>
      </a>
    <?php endforeach; ?>
  </div>

  <?php
  $topic_descs = [
      'Does God Exist?'          => 'Cosmological, fine-tuning, and ontological arguments — examined from the ground up.',
      'The Problem of Evil'      => 'The strongest objection to theism, taken at full strength.',
      'Ethics Without God?'      => 'Where moral facts come from — and whether they need a foundation.',
      'Science & Evidence'       => 'Evolution, neuroscience, cosmology — what the evidence actually shows.',
      'Examining the Quran'      => 'Preservation, variant readings, literary claims — source criticism applied.',
      'Examining the Sources'    => 'Hadith methodology, prophetic biography, historical events — scrutinised.',
      'History & Context'        => 'Institutions, civilisations, and the record — examined honestly.',
      'Rights & Freedom'         => 'Apostasy, gender, sexuality, law — the hardest questions about human rights.',
      'The Inner Journey'        => 'For those carrying doubt — the emotional and intellectual reality of questioning.',
      'The Bigger Picture'       => 'Purpose, meaning, competing claims — where the evidence points.',
  ];

  // Subcategory structure for The Inner Journey
  $subcategories = [
      'The Inner Journey' => [
          'Understanding Doubt' => [
              'your-doubts-are-not-a-disease', 'faith-was-just-conditioning',
              'the-good-muslim-paradox', 'when-the-presence-fades',
              'left-because-of-specific-problems', 'did-your-heart-leave-before-your-head',
          ],
          'The Emotional Cost' => [
              'the-dual-life', 'anger-at-religion', 'the-anger-is-real',
              'religious-trauma', 'when-religion-was-imposed-not-discovered',
          ],
          'What Happens Next' => [
              'social-cost-of-leaving', 'how-muslims-leave-the-sociology',
              'scale-of-leaving', 'practising-without-belief',
              'the-algorithm-that-deconverted-you',
          ],
      ],
  ];

  if ( $is_filtered && $active_term ) {
      $show_topics = [ $active_term->name ];
  } else {
      $show_topics = $topic_order;
  }

  $article_counter = 0;
  $shown_ids = []; // prevent duplicates across sections

  foreach ( $show_topics as $topic_name ):
      if ( ! isset( $term_map[ $topic_name ] ) ) continue;
      $t = $term_map[ $topic_name ];

      $articles = get_posts([
          'post_type'      => 'ce_article',
          'post_status'    => 'publish',
          'posts_per_page' => -1,
          'orderby'        => 'menu_order',
          'order'          => 'ASC',
          'tax_query'      => [[
              'taxonomy' => 'ce_topic',
              'field'    => 'term_id',
              'terms'    => [ $t->term_id ],
          ]],
      ]);

      // Filter out articles already shown in a previous section (multi-topic articles)
      if ( ! $is_filtered ) {
          $articles = array_filter($articles, function($p) use (&$shown_ids) {
              return ! in_array($p->ID, $shown_ids);
          });
      }

      if ( empty($articles) ) continue;

      $desc = $topic_descs[ $topic_name ] ?? '';
      $count = count($articles);
      $show_initially = 8;
      $has_more = $count > $show_initially;
  ?>
    <section class="topic-section" id="topic-<?php echo esc_attr($t->slug); ?>">
      <div class="ts-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open');">
        <span class="ts-name"><?php echo esc_html($topic_name); ?></span>
        <span class="ts-count"><?php echo $count; ?> article<?php echo $count > 1 ? 's' : ''; ?></span>
        <span class="ts-chevron"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></span>
      </div>
      <div class="ts-body open">
      <?php if ($desc): ?>
        <p class="ts-desc"><?php echo esc_html($desc); ?></p>
      <?php endif; ?>

      <?php
      $has_subs = isset($subcategories[$topic_name]);
      
      if ($has_subs) :
          // Render with subcategory headings
          $sub_slugs = $subcategories[$topic_name];
          foreach ($sub_slugs as $sub_name => $sub_slug_list) :
      ?>
          <div class="ts-subcat">
            <h3 class="ts-subcat-name"><?php echo esc_html($sub_name); ?></h3>
            <div class="ts-articles">
              <?php
              foreach ($articles as $post) :
                  if ( ! in_array($post->post_name, $sub_slug_list) ) continue;
                  setup_postdata($post);
                  $shown_ids[] = $post->ID;
                  $article_counter++;
                  $wc   = str_word_count( strip_tags( $post->post_content ) );
                  $mins = max(1, (int) ceil($wc / 200));
              ?>
                <a href="<?php echo esc_url(get_permalink($post)); ?>" class="ts-article">
                  <span class="ts-article-num"><?php echo $article_counter; ?></span>
                  <div class="ts-article-body">
                    <div class="ts-article-title"><?php echo get_the_title($post); ?></div>
                    <div class="ts-article-excerpt"><?php echo wp_trim_words($post->post_excerpt ?: wp_trim_words(strip_tags($post->post_content), 25), 25); ?></div>
                  </div>
                  <span class="ts-article-meta"><?php echo $mins; ?> min</span>
                </a>
              <?php endforeach; wp_reset_postdata(); ?>
            </div>
          </div>
      <?php endforeach; ?>
      
      <?php else : ?>
      
      <div class="ts-articles">
        <?php
        $i = 0;
        foreach ( $articles as $post ):
            setup_postdata($post);
            $shown_ids[] = $post->ID;
            $i++;
            $article_counter++;
            $wc   = str_word_count( strip_tags( $post->post_content ) );
            $mins = max(1, (int) ceil($wc / 200));
            $extra_class = ($has_more && $i > $show_initially) ? ' ts-overflow' : '';
            $extra_style = ($has_more && $i > $show_initially) ? ' style="display:none;"' : '';
        ?>
          <a href="<?php echo esc_url(get_permalink($post)); ?>" class="ts-article<?php echo $extra_class; ?>"<?php echo $extra_style; ?>>
            <span class="ts-article-num"><?php echo $article_counter; ?></span>
            <div class="ts-article-body">
              <div class="ts-article-title"><?php echo get_the_title($post); ?></div>
              <div class="ts-article-excerpt"><?php echo wp_trim_words($post->post_excerpt ?: wp_trim_words(strip_tags($post->post_content), 25), 25); ?></div>
            </div>
            <span class="ts-article-meta"><?php echo $mins; ?> min</span>
          </a>
        <?php endforeach; wp_reset_postdata(); ?>
      </div>
      
      <?php endif; ?>

      <?php if ($has_more): ?>
        <button class="ts-toggle-btn" onclick="this.previousElementSibling.querySelectorAll('.ts-overflow').forEach(function(el){el.style.display='';});this.style.display='none';">
          Show all <?php echo $count; ?> articles
        </button>
      <?php endif; ?>
      </div><!-- /ts-body -->
    </section>
  <?php endforeach; ?>

  <div class="archive-footer">
    <p><?php esc_html_e('These arguments are developed in full in the personalised journey paths — tailored to where you actually are.', 'compelling-evidence'); ?></p>
    <a href="<?php echo esc_url(home_url('/quiz')); ?>" class="btn-teal">
      <?php esc_html_e('Find my path', 'compelling-evidence'); ?>
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
  </div>

</div>

<?php get_footer(); ?>
