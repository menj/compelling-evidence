<?php get_header(); ?>

<?php
// Calculate reading time
$word_count   = str_word_count( strip_tags( get_the_content() ) );
$reading_mins = max( 1, (int) ceil( $word_count / 200 ) );
$reading_secs = $word_count * 0.3; // approx seconds for progress tracking

// Structured data for articles lives in inc/ce-seo.php (Article + BreadcrumbList).
// The former ItemList/SiteNavigationElement table-of-contents markup and the
// Speakable block were removed in 2.6.31: Google supports neither for this
// kind of page (ItemList only inside carousels; Speakable only for news).
?>

<!-- ── READING PROGRESS BAR ───────────────────────────────────────────────── -->
<div id="ce-reading-progress" aria-hidden="true">
  <div id="ce-reading-progress-fill"></div>
</div>

<main class="ce-single" id="main" data-reading-article="1">
  <?php while (have_posts()) : the_post(); ?>

  <article id="post-<?php the_ID(); ?>" <?php post_class('single-article'); ?>>

    <!-- Article Header -->
    <header class="article-hero">
      <div class="article-hero-inner">
        <?php
        $topics = wp_get_post_terms(get_the_ID(), 'ce_topic');
        $topic_name = (!empty($topics) && !is_wp_error($topics)) ? $topics[0]->name : '';
        $topic_link = (!empty($topics) && !is_wp_error($topics)) ? get_term_link($topics[0]) : '';
        ?>

        <!-- Breadcrumbs -->
        <nav class="ce-breadcrumbs" aria-label="Breadcrumb">
          <ol>
            <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
            <li><a href="<?php echo esc_url(home_url('/articles')); ?>">Articles</a></li>
            <?php if ($topic_name && $topic_link) : ?>
              <li><a href="<?php echo esc_url($topic_link); ?>"><?php echo esc_html($topic_name); ?></a></li>
            <?php endif; ?>
            <li aria-current="page"><?php the_title(); ?></li>
          </ol>
        </nav>

        <?php if ($topic_name && $topic_link) : ?>
          <p class="section-label">
            <a href="<?php echo esc_url($topic_link); ?>">
              <?php echo esc_html($topic_name); ?>
            </a>
          </p>
        <?php endif; ?>

        <h1 class="article-title"><?php the_title(); ?></h1>

        <?php
        // Visible byline — only renders when an author has been configured in
        // CE Theme Options → Author & Identity. The link target falls back to
        // the WordPress author archive URL if no explicit profile URL is set,
        // which gives Google an internal author-page to crawl.
        $byline_name = trim( (string) get_option( 'ce_author_name', '' ) );
        if ( '' !== $byline_name ) :
            $byline_url = trim( (string) get_option( 'ce_author_url', '' ) );
            if ( '' === $byline_url ) {
                $byline_url = get_author_posts_url( get_the_author_meta( 'ID' ) );
            }
        ?>
            <p class="article-byline">
                <?php esc_html_e( 'By', 'compelling-evidence' ); ?>
                <a class="article-byline-link" rel="author" href="<?php echo esc_url( $byline_url ); ?>"><?php echo esc_html( $byline_name ); ?></a>
                <?php
                $byline_title = trim( (string) get_option( 'ce_author_title', '' ) );
                if ( '' !== $byline_title ) :
                ?>
                    <span class="article-byline-title">— <?php echo esc_html( $byline_title ); ?></span>
                <?php endif; ?>
            </p>
        <?php endif; ?>

        <div class="article-meta">
          <span class="meta-reading-time">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
            </svg>
            <?php echo (int) $reading_mins; ?> min read
          </span>
          <span class="meta-sep">·</span>
          <span><?php echo number_format($word_count); ?> words</span>
        </div>
      </div>
    </header>

    <!-- Content -->
    <div class="article-content-wrap">
      <div class="article-content" id="article-body">
        <?php the_content(); ?>
        <?php
        wp_link_pages([
          'before' => '<nav class="page-links"><span>Pages:</span>',
          'after'  => '</nav>',
        ]);
        ?>

        <!-- Engagement: Share + Vote + Resonance -->
        <div class="ce-engagement-footer">
          <div class="ce-share-vote-row">
            <?php ce_render_share_bar(); ?>
            <?php ce_render_vote_widget(); ?>
          </div>
          <?php ce_render_resonance(); ?>
        </div>

      </div>

      <!-- Sidebar -->
      <aside class="article-sidebar">

        <!-- Sticky group: stays in view for the whole article (TOC + reading progress) -->
        <div class="sidebar-sticky-track">
          <div class="sidebar-sticky">
          <!-- Table of Contents (auto-populated by JS) -->
          <nav class="sidebar-toc sidebar-widget" id="ce-toc" style="display:none;" role="doc-toc" aria-label="Table of contents">
            <h3 class="widget-title">In this article</h3>
            <ol class="toc-list" id="ce-toc-list"></ol>
          </nav>

          <!-- Sidebar reading progress -->
          <div class="sidebar-reading-progress" role="progressbar" aria-label="Reading progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" id="srp">
            <span class="srp-label">Reading progress</span>
            <div class="srp-track">
              <div class="srp-fill" id="srp-fill"></div>
            </div>
            <div class="srp-stats">
              <span><?php echo (int) $reading_mins; ?> min read</span>
              <span class="srp-pct" id="srp-pct">0%</span>
            </div>
          </div>
          </div>
        </div>

        <!-- ── ARTICLE → JOURNEY RETURN WIDGET ──────────────────────── -->
        <div class="sidebar-widget" id="ce-journey-return-widget" style="display:none;">
          <h3 class="widget-title" id="ce-journey-return-title">Your Journey</h3>
          <div id="ce-journey-return-body"></div>
        </div>

        <div class="sidebar-widget">
          <h3 class="widget-title"><?php esc_html_e('More Questions', 'compelling-evidence'); ?></h3>
          <?php
          // Get related by same topic first, then random
          $topic_ids = wp_get_post_terms(get_the_ID(), 'ce_topic', ['fields' => 'ids']);
          $related_args = [
            'post_type'      => get_post_type(),
            'posts_per_page' => 5,
            'post__not_in'   => [get_the_ID()],
            'orderby'        => 'rand',
          ];
          if (!empty($topic_ids) && !is_wp_error($topic_ids)) {
            $related_args['tax_query'] = [[
              'taxonomy' => 'ce_topic',
              'field'    => 'term_id',
              'terms'    => $topic_ids,
            ]];
          }
          $related = get_posts($related_args);
          if (empty($related)) {
            $related = get_posts(['post_type' => get_post_type(), 'posts_per_page' => 5, 'post__not_in' => [get_the_ID()], 'orderby' => 'rand']);
          }
          if ($related) : ?>
            <ul class="related-list">
              <?php foreach ($related as $r) : ?>
                <li>
                  <a href="<?php echo esc_url(get_permalink($r)); ?>">
                    <?php echo esc_html( get_the_title($r) ); ?>
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>

        <div class="sidebar-cta">
          <p><?php esc_html_e('Have a question that\'s not covered here?', 'compelling-evidence'); ?></p>
          <a href="<?php echo esc_url(home_url('/ask-a-question')); ?>" class="btn-primary">
            <?php esc_html_e('Ask a Question', 'compelling-evidence'); ?>
          </a>
        </div>
      </aside>
    </div>

  </article>

  <!-- ── RELATED ARTICLES (3-card grid) ───────────────────────────────────── -->
  <?php
  $related_cards_args = [
      'post_type'      => 'ce_article',
      'posts_per_page' => 3,
      'post__not_in'   => [get_the_ID()],
      'orderby'        => 'rand',
  ];
  if (!empty($topic_ids) && !is_wp_error($topic_ids)) {
      $related_cards_args['tax_query'] = [[
          'taxonomy' => 'ce_topic',
          'field'    => 'term_id',
          'terms'    => $topic_ids,
      ]];
  }
  $related_cards = get_posts($related_cards_args);
  if ($related_cards && count($related_cards) >= 2) : ?>
  <section class="ce-related-articles">
    <div class="related-articles-inner">
      <h2 class="related-articles-title">Continue Reading</h2>
      <div class="related-articles-grid">
        <?php foreach ($related_cards as $rc) :
            $rc_topics = wp_get_post_terms($rc->ID, 'ce_topic');
            $rc_topic  = (!empty($rc_topics) && !is_wp_error($rc_topics)) ? $rc_topics[0]->name : '';
            $rc_words  = str_word_count(strip_tags($rc->post_content));
            $rc_mins   = max(1, (int) ceil($rc_words / 200));
        ?>
        <a href="<?php echo esc_url(get_permalink($rc)); ?>" class="related-card">
          <?php if ($rc_topic) : ?>
            <span class="related-card-topic"><?php echo esc_html($rc_topic); ?></span>
          <?php endif; ?>
          <h3 class="related-card-title"><?php echo esc_html( get_the_title($rc) ); ?></h3>
          <span class="related-card-meta"><?php echo (int) $rc_mins; ?> min read</span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <?php endwhile; ?>
</main>

<script>
(function() {
  var topBar    = document.getElementById('ce-reading-progress-fill');
  var srpFill   = document.getElementById('srp-fill');
  var srpPct    = document.getElementById('srp-pct');
  var body      = document.getElementById('article-body');

  if (!topBar || !body) return;

  function getScrollPct() {
    var rect   = body.getBoundingClientRect();
    var total  = body.offsetHeight;
    var viewH  = window.innerHeight;
    // How far through the article content we've scrolled
    var scrolled = Math.max(0, -rect.top + viewH * 0.15);
    var readable = total - viewH * 0.15;
    return Math.min(100, Math.max(0, Math.round((scrolled / readable) * 100)));
  }

  function update() {
    var pct = getScrollPct();
    var pctStr = pct + '%';
    topBar.style.width = pctStr;
    if (srpFill)  srpFill.style.width   = pctStr;
    if (srpPct)   srpPct.textContent    = pctStr;
    var srp = document.getElementById('srp');
    if (srp) srp.setAttribute('aria-valuenow', pct);
  }

  window.addEventListener('scroll', update, { passive: true });
  window.addEventListener('resize', update, { passive: true });
  update();
})();

// Table of contents: assets/js/ce-toc.js (enqueued in functions.php).

// ── ARTICLE → JOURNEY RETURN WIDGET ─────────────────────────────────────
(function() {
  var PATH_NAMES = {
    'new-atheist':'The New Atheist','agnostic':'The Agnostic',
    'secular-humanist':'The Secular Humanist','antitheist':'The Antitheist',
    'materialist':'The Materialist','muslim-doubts':'The Questioning Muslim',
    'apatheist':'The Apatheist','deist':'The Deist','scientist':'The Scientist',
    'classical-atheist':'The Classical Atheist','ex-believer':'The Ex-Believer',
    'spiritual-seeker':'The Spiritual Seeker',
    'freethinker':'The Freethinker','true-muslim':'The Committed Muslim',
  };
  var SCREENS = ['horizon','singularity','calibration','emergence','entropy','constant','signal','resonance','transmission','conclusion'];
  var SCREEN_LABELS = ['Horizon','Singularity','Calibration','Emergence','Entropy','Constant','Signal','Resonance','Transmission','Conclusion'];

  window.addEventListener('load', function() {
    var widget  = document.getElementById('ce-journey-return-widget');
    var title   = document.getElementById('ce-journey-return-title');
    var body    = document.getElementById('ce-journey-return-body');
    if (!widget || !body) return;

    try {
      var primary    = localStorage.getItem('ce_primary_path');
      var quizTaken  = localStorage.getItem('ce_quiz_taken');
      var savedScreen = primary ? localStorage.getItem('ce_progress_' + primary) : null;
      var completed   = JSON.parse(localStorage.getItem('ce_completed_paths') || '[]');
      var isComplete  = primary && completed.indexOf(primary) >= 0;

      if (primary && quizTaken && PATH_NAMES[primary]) {
        var name = PATH_NAMES[primary];
        var screenLabel = savedScreen ? (SCREEN_LABELS[SCREENS.indexOf(savedScreen)] || 'Horizon') : 'Horizon';
        var href = '/journey/' + primary + '/';

        if (isComplete) {
          title.textContent = 'You completed a journey';
          body.innerHTML =
            '<p style="font-size:0.8rem;color:rgba(242,238,255,0.5);margin-bottom:0.8rem;line-height:1.5;">' + name + ' — finished</p>' +
            '<a href="' + href + '" style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.8rem;color:var(--teal,#0cd4e0);border-bottom:1px solid rgba(12,212,224,0.2);padding-bottom:1px;text-decoration:none;">Revisit journey →</a>' +
            '<div style="margin-top:0.8rem;"><a href="/journeys" style="font-size:0.75rem;color:rgba(242,238,255,0.3);text-decoration:none;">Browse all 14 paths →</a></div>';
        } else if (savedScreen && savedScreen !== 'horizon') {
          title.textContent = 'Resume your journey';
          body.innerHTML =
            '<p style="font-size:0.8rem;color:rgba(242,238,255,0.5);margin-bottom:0.8rem;line-height:1.5;">' + name + '<br><span style="color:rgba(12,212,224,0.5);">At: ' + screenLabel + '</span></p>' +
            '<a href="' + href + '" style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.8rem;color:var(--teal,#0cd4e0);border-bottom:1px solid rgba(12,212,224,0.2);padding-bottom:1px;text-decoration:none;">Continue reading →</a>';
        } else {
          title.textContent = 'Your path';
          body.innerHTML =
            '<p style="font-size:0.8rem;color:rgba(242,238,255,0.5);margin-bottom:0.8rem;line-height:1.5;">' + name + '</p>' +
            '<a href="' + href + '" style="display:inline-flex;align-items:center;gap:0.4rem;font-size:0.8rem;color:var(--teal,#0cd4e0);border-bottom:1px solid rgba(12,212,224,0.2);padding-bottom:1px;text-decoration:none;">Begin journey →</a>';
        }
        widget.style.display = 'block';
      } else {
        // No quiz taken — show quiz CTA
        title.textContent = 'Find your path';
        body.innerHTML =
          '<p style="font-size:0.8rem;color:rgba(242,238,255,0.5);margin-bottom:0.8rem;line-height:1.5;">The arguments in this article are part of a personalised journey written specifically for your starting point.</p>' +
          '<a href="' + (window.CE && CE.homeUrl ? CE.homeUrl : '/') + 'quiz" style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.55rem 1.1rem;border-radius:50px;background:rgba(12,212,224,0.12);border:1px solid rgba(12,212,224,0.25);color:var(--teal,#0cd4e0);font-size:0.8rem;font-weight:600;text-decoration:none;">Take the quiz →</a>';
        widget.style.display = 'block';
      }
    } catch(e) {
      // localStorage blocked — show quiz CTA silently
      var widget = document.getElementById('ce-journey-return-widget');
      if (widget) {
        document.getElementById('ce-journey-return-title').textContent = 'Find your path';
        document.getElementById('ce-journey-return-body').innerHTML =
          '<a href="' + (window.CE && CE.homeUrl ? CE.homeUrl : '/') + 'quiz" style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.55rem 1.1rem;border-radius:50px;background:rgba(12,212,224,0.12);border:1px solid rgba(12,212,224,0.25);color:var(--teal,#0cd4e0);font-size:0.8rem;font-weight:600;text-decoration:none;">Take the quiz →</a>';
        widget.style.display = 'block';
      }
    }
  });
})();

</script>

<?php get_footer(); ?>
