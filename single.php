<?php get_header(); ?>

<?php
// Calculate reading time
$word_count   = str_word_count( strip_tags( get_the_content() ) );
$reading_mins = max( 1, (int) ceil( $word_count / 200 ) );
$reading_secs = $word_count * 0.3; // approx seconds for progress tracking

// Extract h2 headings for TOC schema
$toc_items = [];
$content = get_the_content();
if ( preg_match_all('/<h2[^>]*>(.*?)<\/h2>/i', $content, $matches, PREG_SET_ORDER ) ) {
    foreach ( $matches as $i => $match ) {
        $heading_text = wp_strip_all_tags( $match[1] );
        if ( ! empty( $heading_text ) ) {
            $toc_items[] = [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $heading_text,
                'url'      => get_permalink() . '#section-' . ( $i + 1 ),
            ];
        }
    }
}
if ( count( $toc_items ) >= 2 ) :
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "Table of Contents: <?php echo esc_js( get_the_title() ); ?>",
  "itemListElement": <?php echo wp_json_encode( $toc_items, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?>
}
</script>
<?php endif; ?>

<!-- Speakable schema for voice assistants -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "speakable": {
    "@type": "SpeakableSpecification",
    "cssSelector": [".article-title", ".article-content"]
  }
}
</script>

<!-- ── READING PROGRESS BAR ───────────────────────────────────────────────── -->
<div id="ce-reading-progress" aria-hidden="true">
  <div id="ce-reading-progress-fill"></div>
</div>

<style>
#ce-reading-progress {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  z-index: 9999;
  background: rgba(255,255,255,0.06);
}
#ce-reading-progress-fill {
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg, var(--teal, #0cd4e0), #09b8c4);
  transition: width 0.1s linear;
  border-radius: 0 2px 2px 0;
}

/* Article specific styles */
.ce-single { padding-top: var(--total-offset, 116px); }

.article-hero {
  background: linear-gradient(180deg, rgba(107,47,160,0.12) 0%, transparent 100%);
  border-bottom: 1px solid rgba(107,47,160,0.12);
  padding: 4rem 2rem 3rem;
}
.article-hero-inner {
  max-width: 760px;
  margin: 0 auto;
}
.article-title {
  font-family: 'Playfair Display', serif;
  font-size: clamp(2rem, 4.5vw, 3.2rem);
  font-weight: 900;
  line-height: 1.1;
  letter-spacing: -0.025em;
  margin: 0.6rem 0 1.2rem;
  color: var(--white, #f2eeff);
}
.article-meta {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  font-size: 0.8rem;
  color: rgba(242,238,255,0.4);
  flex-wrap: wrap;
}
.meta-sep { opacity: 0.3; }

/* Reading time pill */
.meta-reading-time {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  background: rgba(12,212,224,0.08);
  border: 1px solid rgba(12,212,224,0.18);
  border-radius: 50px;
  padding: 0.2rem 0.65rem;
  color: rgba(12,212,224,0.7);
  font-size: 0.75rem;
  font-weight: 500;
}

/* Content layout */
.article-content-wrap {
  display: grid;
  grid-template-columns: 1fr 280px;
  gap: 3rem;
  max-width: 1060px;
  margin: 0 auto;
  padding: 3rem 2rem 4rem;
  align-items: start;
}
@media (max-width: 860px) {
  .article-content-wrap { grid-template-columns: 1fr; }
  .article-sidebar { order: 1; position: static; }
}

.article-content {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(1.05rem, 1.6vw, 1.15rem);
  line-height: 1.9;
  color: rgba(242,238,255,0.82);
}
.article-content h2 {
  font-family: 'Playfair Display', serif;
  font-size: clamp(1.3rem, 2.5vw, 1.7rem);
  font-weight: 700;
  color: var(--white, #f2eeff);
  margin: 2.5rem 0 0.8rem;
  line-height: 1.2;
}
.article-content p { margin-bottom: 1.4rem; }
.article-content p:last-child { margin-bottom: 0; }
.article-content .article-lead {
  font-size: clamp(1.15rem, 1.8vw, 1.28rem);
  color: rgba(242,238,255,0.9);
  line-height: 1.75;
  font-weight: 400;
}
.article-content blockquote {
  border-left: 3px solid rgba(12,212,224,0.3);
  padding: 0.8rem 1.4rem;
  margin: 1.8rem 0;
  color: rgba(242,238,255,0.6);
  font-style: italic;
}

/* Sidebar */
.article-sidebar::-webkit-scrollbar { display: none; }
@media (min-width: 901px) { .article-sidebar { position: sticky; top: calc(var(--total-offset, 116px) + 1rem); max-height: calc(100vh - var(--total-offset, 116px) - 2rem); overflow-y: auto; scrollbar-width: none; -ms-overflow-style: none; } }
.sidebar-widget {
  background: rgba(255,255,255,0.025);
  border: 1px solid rgba(107,47,160,0.18);
  border-radius: 14px;
  padding: 1.4rem;
  margin-bottom: 1.2rem;
}
.widget-title {
  font-family: 'Playfair Display', serif;
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--white, #f2eeff);
  margin-bottom: 0.9rem;
}
.related-list { list-style: none; }
.related-list li { border-bottom: 1px solid rgba(255,255,255,0.05); }
.related-list li:last-child { border-bottom: none; }
.related-list a {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.5rem;
  padding: 0.6rem 0;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.82rem;
  color: rgba(242,238,255,0.6);
  text-decoration: none;
  transition: color 0.15s;
}
.related-list a:hover { color: var(--teal, #0cd4e0); }
.related-list a svg { flex-shrink: 0; opacity: 0.4; }

/* Sidebar reading progress widget */
.sidebar-reading-progress {
  background: rgba(12,212,224,0.04);
  border: 1px solid rgba(12,212,224,0.12);
  border-radius: 14px;
  padding: 1.2rem 1.4rem;
  margin-bottom: 1.2rem;
}
.srp-label {
  font-size: 0.65rem;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: rgba(12,212,224,0.5);
  margin-bottom: 0.7rem;
  display: block;
}
.srp-track {
  height: 4px;
  background: rgba(255,255,255,0.06);
  border-radius: 50px;
  overflow: hidden;
  margin-bottom: 0.5rem;
}
.srp-fill {
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg, var(--teal, #0cd4e0), #09b8c4);
  border-radius: 50px;
  transition: width 0.2s;
}
.srp-stats {
  display: flex;
  justify-content: space-between;
  font-size: 0.75rem;
  color: rgba(242,238,255,0.35);
}
.srp-pct { color: rgba(12,212,224,0.6); font-weight: 600; }

/* CTA */
.sidebar-cta {
  background: rgba(107,47,160,0.08);
  border: 1px solid rgba(107,47,160,0.18);
  border-radius: 14px;
  padding: 1.4rem;
  text-align: center;
}
.sidebar-cta p {
  font-size: 0.82rem;
  color: rgba(242,238,255,0.5);
  margin-bottom: 0.8rem;
  line-height: 1.5;
}



/* ── MOBILE RESPONSIVE ─────────────────────────────────────────── */
@media (max-width: 600px) {
  .article-hero { padding: 3rem 1.2rem 2rem; }
  .article-content-wrap { padding: 2rem 1.2rem 3rem; gap: 2rem; }
  .sidebar-widget { padding: 1.2rem; }
}

</style>

<main class="ce-single" id="main" data-reading-article="1">
  <?php while (have_posts()) : the_post(); ?>

  <article id="post-<?php the_ID(); ?>" <?php post_class('single-article'); ?>>

    <!-- Article Header -->
    <header class="article-hero">
      <div class="article-hero-inner">
        <?php
        $topics = wp_get_post_terms(get_the_ID(), 'ce_topic');
        if (!empty($topics) && !is_wp_error($topics)) : ?>
          <p class="section-label">
            <a href="<?php echo esc_url(get_term_link($topics[0])); ?>">
              <?php echo esc_html($topics[0]->name); ?>
            </a>
          </p>
        <?php endif; ?>

        <h1 class="article-title"><?php the_title(); ?></h1>

        <div class="article-meta">
          <span class="meta-reading-time">
            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
              <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
            </svg>
            <?php echo $reading_mins; ?> min read
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

        <!-- Table of Contents (auto-populated by JS) -->
        <nav class="sidebar-toc sidebar-widget" id="ce-toc" style="display:none;" aria-label="Table of contents">
          <h3 class="widget-title">In this article</h3>
          <ol class="toc-list" id="ce-toc-list"></ol>
        </nav>

        <!-- Sidebar reading progress -->
        <div class="sidebar-reading-progress">
          <span class="srp-label">Reading progress</span>
          <div class="srp-track">
            <div class="srp-fill" id="srp-fill"></div>
          </div>
          <div class="srp-stats">
            <span><?php echo $reading_mins; ?> min read</span>
            <span class="srp-pct" id="srp-pct">0%</span>
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
                    <?php echo get_the_title($r); ?>
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
  }

  window.addEventListener('scroll', update, { passive: true });
  window.addEventListener('resize', update, { passive: true });
  update();
})();

// ── TABLE OF CONTENTS ────────────────────────────────────────────────────
(function() {
  var body = document.getElementById('article-body');
  var toc  = document.getElementById('ce-toc');
  var list = document.getElementById('ce-toc-list');
  if (!body || !toc || !list) return;

  var headings = body.querySelectorAll('h2');
  if (headings.length < 2) return; // Don't show TOC for 0-1 headings

  headings.forEach(function(h, i) {
    // Add ID to heading
    var id = 'section-' + (i + 1);
    h.id = id;

    // Create TOC link
    var li = document.createElement('li');
    var a  = document.createElement('a');
    a.href = '#' + id;
    a.textContent = h.textContent;
    a.addEventListener('click', function(e) {
      e.preventDefault();
      var target = document.getElementById(id);
      if (target) {
        var offset = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--total-offset') || '116');
        window.scrollTo({ top: target.offsetTop - offset - 20, behavior: 'smooth' });
      }
    });
    li.appendChild(a);
    list.appendChild(li);
  });

  toc.style.display = 'block';

  // Scroll spy
  var links = list.querySelectorAll('a');
  function updateActive() {
    var offset = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--total-offset') || '116') + 40;
    var current = '';
    headings.forEach(function(h) {
      if (h.getBoundingClientRect().top <= offset) {
        current = h.id;
      }
    });
    links.forEach(function(a) {
      a.classList.toggle('toc-active', a.getAttribute('href') === '#' + current);
    });
  }
  window.addEventListener('scroll', updateActive, { passive: true });
  updateActive();
})();

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
