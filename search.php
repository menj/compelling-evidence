<?php
/**
 * Search Results Template
 * Handles /?s= queries — shows ce_article + post results
 * with reading time, topic tags, and an empty-state CTA.
 */
get_header();

$search_term = get_search_query();
$paged       = get_query_var('paged') ?: 1;

// Re-run the query to include ce_article alongside posts
$args = [
    's'              => $search_term,
    'post_type'      => ['post', 'ce_article'],
    'post_status'    => 'publish',
    'posts_per_page' => 12,
    'paged'          => $paged,
];
$results = new WP_Query($args);
?>

<div class="search-page">

  <!-- Hero / Search bar -->
  <div class="search-hero">
    <span class="search-hero-eyebrow">
      <?php esc_html_e('Search', 'compelling-evidence'); ?>
    </span>

    <?php if ($search_term) : ?>
      <h1><?php esc_html_e('Results for:', 'compelling-evidence'); ?> <em><?php echo esc_html($search_term); ?></em></h1>
      <p class="search-count">
        <?php
        $found = $results->found_posts;
        if ($found === 0) {
            echo esc_html__('No results found', 'compelling-evidence');
        } elseif ($found === 1) {
            echo '1 ' . esc_html__('article', 'compelling-evidence');
        } else {
            echo $found . ' ' . esc_html__('articles', 'compelling-evidence');
        }
        ?>
      </p>
    <?php else : ?>
      <h1><?php esc_html_e('Search the inquiry', 'compelling-evidence'); ?></h1>
    <?php endif; ?>

    <!-- Search form -->
    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="search-bar-wrap">
      <input
        type="search"
        class="search-bar-input"
        placeholder="<?php esc_attr_e('Search a question or topic…', 'compelling-evidence'); ?>"
        value="<?php echo esc_attr($search_term); ?>"
        name="s"
        autocomplete="off"
        autofocus
      >
      <button type="submit" class="search-bar-btn">
        <?php esc_html_e('Search', 'compelling-evidence'); ?>
      </button>
    </form>
  </div>

  <!-- Results -->
  <?php if ($results->have_posts()) : ?>

    <div class="search-results-list">
      <?php
      $idx = ($paged - 1) * 12;
      while ($results->have_posts()) : $results->the_post();
        $idx++;
        $topics   = wp_get_post_terms(get_the_ID(), 'ce_topic');
        $topic    = (!empty($topics) && !is_wp_error($topics)) ? $topics[0]->name : '';
        $wc       = str_word_count(strip_tags(get_the_content()));
        $mins     = max(1, (int) ceil($wc / 200));
        $num_str  = str_pad($idx, 2, '0', STR_PAD_LEFT);
      ?>
      <a href="<?php the_permalink(); ?>" class="search-result-card">
        <span class="src-num"><?php echo $num_str; ?></span>
        <div class="src-body">
          <div class="src-meta">
            <?php if ($topic) : ?>
              <span class="src-topic"><?php echo esc_html($topic); ?></span>
            <?php endif; ?>
            <span class="src-reading"><?php echo $mins; ?> min read</span>
          </div>
          <div class="src-title"><?php the_title(); ?></div>
          <div class="src-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 24); ?></div>
        </div>
      </a>
      <?php endwhile; wp_reset_postdata(); ?>
    </div>

    <!-- Pagination -->
    <div class="pagination">
      <?php
      echo paginate_links([
        'base'      => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
        'format'    => '?paged=%#%',
        'current'   => $paged,
        'total'     => $results->max_num_pages,
        'prev_text' => '← ' . __('Prev', 'compelling-evidence'),
        'next_text' => __('Next', 'compelling-evidence') . ' →',
        'mid_size'  => 2,
      ]);
      ?>
    </div>

  <?php else : ?>

    <!-- Empty state -->
    <div class="search-empty">
      <div class="search-empty-icon">◎</div>
      <h2>
        <?php if ($search_term) : ?>
          <?php printf(esc_html__('Nothing found for "%s"', 'compelling-evidence'), esc_html($search_term)); ?>
        <?php else : ?>
          <?php esc_html_e('What are you looking for?', 'compelling-evidence'); ?>
        <?php endif; ?>
      </h2>
      <p>
        <?php if ($search_term) : ?>
          <?php esc_html_e('Try a different term — or browse by topic below. You can also take the quiz to get an argument written for your specific starting point.', 'compelling-evidence'); ?>
        <?php else : ?>
          <?php esc_html_e('Search for any question, topic, or concept covered in the inquiry.', 'compelling-evidence'); ?>
        <?php endif; ?>
      </p>

      <!-- Suggested topics -->
      <div class="search-suggestions">
        <?php
        $suggest = ['Does God Exist', 'Problem of Evil', 'Ethics Without God', 'Fine-Tuning', 'Quran Preservation', 'Hadith Reliability', 'Apostasy', 'Women in Islam', 'Doubt', 'Purpose of Life'];
        foreach ($suggest as $s) :
        ?>
          <a href="<?php echo esc_url(home_url('/?s=' . urlencode($s))); ?>"
             class="search-suggestion-pill">
            <?php echo esc_html($s); ?>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="search-cta-row">
        <a href="<?php echo esc_url(home_url('/quiz')); ?>" class="search-btn-primary">
          Find my path
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="<?php echo esc_url(home_url('/articles')); ?>" class="search-btn-ghost">
          Browse all articles
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>

  <?php endif; ?>

</div>

<script>
(function() {
  var list = document.querySelector('.search-results-list');
  if (!list) return;

  var pagination = document.querySelector('.pagination');
  var nextLink = pagination ? pagination.querySelector('.next') : null;
  if (!nextLink) return;

  var status = document.getElementById('inf-scroll-status-search');
  var loading = false;
  var done = false;

  // Hide pagination (replaced by infinite scroll)
  pagination.style.display = 'none';

  // Sentinel element at the bottom
  var sentinel = document.createElement('div');
  sentinel.style.height = '1px';
  list.parentNode.insertBefore(sentinel, status);

  var observer = new IntersectionObserver(function(entries) {
    if (entries[0].isIntersecting && !loading && !done) {
      loadMore();
    }
  }, { rootMargin: '500px' });

  observer.observe(sentinel);

  function loadMore() {
    if (!nextLink || done) return;
    loading = true;
    if (status) status.querySelector('.inf-scroll-request').style.display = 'block';

    fetch(nextLink.href)
      .then(function(r) { return r.text(); })
      .then(function(html) {
        var parser = new DOMParser();
        var doc = parser.parseFromString(html, 'text/html');
        var newCards = doc.querySelectorAll('.search-result-card');
        var newNext = doc.querySelector('.pagination .next');

        newCards.forEach(function(card) {
          list.appendChild(document.importNode(card, true));
        });

        if (status) status.querySelector('.inf-scroll-request').style.display = 'none';

        if (newNext) {
          nextLink = newNext;
        } else {
          done = true;
          observer.disconnect();
          if (status) status.querySelector('.inf-scroll-last').style.display = 'block';
        }
        loading = false;
      })
      .catch(function() {
        loading = false;
        if (status) status.querySelector('.inf-scroll-request').style.display = 'none';
      });
  }
})();
</script>
<div id="inf-scroll-status-search" style="text-align:center;padding:1rem 0;">
  <p class="inf-scroll-request" style="display:none;color:rgba(242,238,255,0.3);font-size:0.8rem;">Loading more…</p>
  <p class="inf-scroll-last"    style="display:none;color:rgba(242,238,255,0.2);font-size:0.75rem;">All results loaded.</p>
</div>

<?php get_footer(); ?>
