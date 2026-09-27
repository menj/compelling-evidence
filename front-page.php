<?php get_header(); ?>

<main id="main" role="main" aria-label="Main content">

<!-- ── HERO ─────────────────────────────────────────────────────────────── -->
<section class="hero">

  <div class="hero-verse-img-wrap">
    <div class="hero-verse-text" lang="ar" dir="rtl" aria-label="فَبِأَيِّ آلَاءِ رَبِّكُمَا تُكَذِّبَانِ">
      فَبِأَيِّ آلَاءِ رَبِّكُمَا تُكَذِّبَانِ
      <span class="hero-verse-end" aria-hidden="true">﴿١٣﴾</span>
    </div>
    <img
      src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/al-rahman-transparent.png' ); ?>"
      alt=""
      class="hero-verse-img hero-verse-img--fallback"
      width="894"
      height="342"
      loading="eager"
      aria-hidden="true"
    >
    <p class="hero-translation">Then which of the favours of your Lord will you deny?</p>
    <div class="hero-citation-row">
      <span class="hero-citation-ornament" aria-hidden="true">
        <svg width="80" height="12" viewBox="0 0 80 12" fill="none" xmlns="http://www.w3.org/2000/svg">
          <line x1="0" y1="6" x2="28" y2="6" stroke="currentColor" stroke-opacity="0.35" stroke-width="0.75"/>
          <path d="M32 6 L35 3 L38 6 L35 9 Z" fill="currentColor" fill-opacity="0.4"/>
          <circle cx="35" cy="6" r="1.2" fill="currentColor" fill-opacity="0.7"/>
          <path d="M40 6 L42.5 3.5 L45 6 L42.5 8.5 Z" stroke="currentColor" stroke-opacity="0.3" stroke-width="0.6" fill="none"/>
          <line x1="48" y1="6" x2="52" y2="6" stroke="currentColor" stroke-opacity="0.2" stroke-width="0.75"/>
          <circle cx="54" cy="6" r="1" fill="currentColor" fill-opacity="0.2"/>
          <line x1="56" y1="6" x2="80" y2="6" stroke="currentColor" stroke-opacity="0.1" stroke-width="0.75"/>
        </svg>
      </span>
      <p class="hero-bismillah">Surah Ar-Rahman (55:13)</p>
      <span class="hero-citation-ornament hero-citation-ornament--right" aria-hidden="true">
        <svg width="80" height="12" viewBox="0 0 80 12" fill="none" xmlns="http://www.w3.org/2000/svg">
          <line x1="80" y1="6" x2="52" y2="6" stroke="currentColor" stroke-opacity="0.35" stroke-width="0.75"/>
          <path d="M48 6 L45 3 L42 6 L45 9 Z" fill="currentColor" fill-opacity="0.4"/>
          <circle cx="45" cy="6" r="1.2" fill="currentColor" fill-opacity="0.7"/>
          <path d="M40 6 L37.5 3.5 L35 6 L37.5 8.5 Z" stroke="currentColor" stroke-opacity="0.3" stroke-width="0.6" fill="none"/>
          <line x1="32" y1="6" x2="28" y2="6" stroke="currentColor" stroke-opacity="0.2" stroke-width="0.75"/>
          <circle cx="26" cy="6" r="1" fill="currentColor" fill-opacity="0.2"/>
          <line x1="24" y1="6" x2="0" y2="6" stroke="currentColor" stroke-opacity="0.1" stroke-width="0.75"/>
        </svg>
      </span>
    </div>
  </div>

  <h1 class="hero-sub">Honest, evidence-based inquiry into the questions that matter most — written for where you actually are.</h1>

  <div class="hero-search-wrap">
    <div class="hero-search" role="search">
      <input
        type="search"
        id="ce-search-input"
        placeholder="Search a question or topic…"
        aria-label="Search topics"
        autocomplete="off"
      >
      <button id="ce-search-btn" type="button">Search</button>
      <div id="ce-search-results" class="search-dropdown" aria-live="polite"></div>
    </div>

    <div class="hero-prompts" aria-label="Suggested topics">
      <p class="hero-prompts-label">Try asking —</p>
      <div class="hero-prompts-chips" id="hero-chips">
        <button class="hero-prompt-chip hero-prompt-chip--a" data-query="why does anything exist" type="button" style="display:none"><span class="chip-icon" aria-hidden="true">✦</span>why does anything exist</button>
        <button class="hero-prompt-chip hero-prompt-chip--b" data-query="problem of evil" type="button" style="display:none"><span class="chip-icon" aria-hidden="true">◈</span>problem of evil</button>
        <button class="hero-prompt-chip hero-prompt-chip--c" data-query="ethics without god" type="button" style="display:none"><span class="chip-icon" aria-hidden="true">◎</span>ethics without god</button>
        <button class="hero-prompt-chip hero-prompt-chip--a" data-query="fine tuning universe" type="button" style="display:none"><span class="chip-icon" aria-hidden="true">◇</span>fine tuning universe</button>
        <button class="hero-prompt-chip hero-prompt-chip--b" data-query="quran preserved" type="button" style="display:none"><span class="chip-icon" aria-hidden="true">✦</span>quran preserved</button>
        <button class="hero-prompt-chip hero-prompt-chip--c" data-query="leaving islam" type="button" style="display:none"><span class="chip-icon" aria-hidden="true">◈</span>leaving islam</button>
        <button class="hero-prompt-chip hero-prompt-chip--a" data-query="age of aisha" type="button" style="display:none"><span class="chip-icon" aria-hidden="true">◎</span>age of aisha</button>
        <button class="hero-prompt-chip hero-prompt-chip--b" data-query="science and religion" type="button" style="display:none"><span class="chip-icon" aria-hidden="true">◇</span>science and religion</button>
        <button class="hero-prompt-chip hero-prompt-chip--c" data-query="religious trauma" type="button" style="display:none"><span class="chip-icon" aria-hidden="true">✦</span>religious trauma</button>
        <button class="hero-prompt-chip hero-prompt-chip--a" data-query="women in islam" type="button" style="display:none"><span class="chip-icon" aria-hidden="true">◈</span>women in islam</button>
        <button class="hero-prompt-chip hero-prompt-chip--b" data-query="hadith reliability" type="button" style="display:none"><span class="chip-icon" aria-hidden="true">◎</span>hadith reliability</button>
        <button class="hero-prompt-chip hero-prompt-chip--c" data-query="purpose of life" type="button" style="display:none"><span class="chip-icon" aria-hidden="true">◇</span>purpose of life</button>
        <button class="hero-prompt-chip hero-prompt-chip--a" data-query="islam and violence" type="button" style="display:none"><span class="chip-icon" aria-hidden="true">✦</span>islam and violence</button>
        <button class="hero-prompt-chip hero-prompt-chip--b" data-query="why islam" type="button" style="display:none"><span class="chip-icon" aria-hidden="true">◈</span>why islam</button>
        <button class="hero-prompt-chip hero-prompt-chip--c" data-query="doubt and faith" type="button" style="display:none"><span class="chip-icon" aria-hidden="true">◎</span>doubt and faith</button>
      </div>
    </div>
    <script>
    (function(){
      var chips = document.querySelectorAll('#hero-chips .hero-prompt-chip');
      var all = Array.prototype.slice.call(chips);
      var anchors = ['why does anything exist','problem of evil'];
      var pick = [];
      var pool = [];

      // First visit: always include the two anchor phrases
      all.forEach(function(c){
        if(anchors.indexOf(c.getAttribute('data-query'))>=0) pick.push(c);
        else pool.push(c);
      });

      // Shuffle the pool
      for(var i=pool.length-1;i>0;i--){
        var j=Math.floor(Math.random()*(i+1));
        var t=pool[i];pool[i]=pool[j];pool[j]=t;
      }

      // Fill remaining slots from pool (6 total)
      var need = 6 - pick.length;
      for(var k=0;k<need && k<pool.length;k++) pick.push(pool[k]);

      // Shuffle the final 6 so anchors aren't always first
      for(var i=pick.length-1;i>0;i--){
        var j=Math.floor(Math.random()*(i+1));
        var t=pick[i];pick[i]=pick[j];pick[j]=t;
      }

      // Show them
      pick.forEach(function(c){ c.style.display=''; });
    })();
    </script>
  </div>

  <div class="scroll-hint" aria-hidden="true">
    <div class="scroll-hint-preview">
      <a href="<?php echo esc_url(home_url('/topic/does-god-exist/')); ?>" class="scroll-hint-tile" tabindex="-1">
        <span class="sht-label">Does God Exist?</span>
      </a>
      <a href="<?php echo esc_url(home_url('/topic/the-problem-of-evil/')); ?>" class="scroll-hint-tile">
        <span class="sht-label">The Problem of Evil</span>
      </a>
      <a href="<?php echo esc_url(home_url('/topic/the-inner-journey/')); ?>" class="scroll-hint-tile">
        <span class="sht-label">The Inner Journey</span>
      </a>
    </div>
    <span class="scroll-hint-label">Explore</span>
    <div class="scroll-hint-line"></div>
  </div>
</section>

<!-- ── QUICK ACCESS CARDS ─────────────────────────────────────────────────── -->
<section class="quick-access">
  <a href="<?php echo esc_url(home_url('/articles?orderby=popular')); ?>" class="qa-card qa-card--questions">
    <div class="qa-card-icon">
      <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>
    </div>
    <h2>Top Questions</h2>
    <p>The most commonly asked questions about Islam, God, and existence.</p>
    <span class="qa-card-cta">Browse questions →</span>
  </a>

  <a href="<?php echo esc_url(home_url('/qa')); ?>" class="qa-card qa-card--qa">
    <div class="qa-card-icon">
      <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2z"/><path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1"/></svg>
    </div>
    <h2>Reader Q&amp;A</h2>
    <p>Real questions submitted by readers, answered with care.</p>
    <span class="qa-card-cta">Read answers →</span>
  </a>

  <a href="<?php echo esc_url(home_url('/articles')); ?>" class="qa-card qa-card--new">
    <div class="qa-card-icon">
      <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
    </div>
    <h2>What's New</h2>
    <p>Our most recently published articles and answers.</p>
    <span class="qa-card-cta">See latest →</span>
  </a>

  <a href="<?php echo esc_url(home_url('/ask-a-question')); ?>" class="qa-card qa-card--ask">
    <div class="qa-card-icon">
      <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M12 7v6M9 10h6"/></svg>
    </div>
    <h2>Ask a Question</h2>
    <p>Can't find what you're looking for? Submit your question directly.</p>
    <span class="qa-card-cta">Ask now →</span>
  </a>
</section>

<!-- ── ARTICLES ──────────────────────────────────────────────────────────── -->
<section class="articles" id="articles">
  <div class="articles-header reveal">
    <div>
      <p class="section-label">Key Questions</p>
      <h2>What are you searching for?</h2>
    </div>
    <p>Honest engagement with the strongest objections to belief — approached with reason, evidence, and intellectual integrity.</p>
  </div>

  <div class="grid">
    <?php
    // Featured strip slugs — exclude from grid to avoid duplication
    $featured_slugs = [
      'purpose-of-life',
      'does-god-exist',
      'why-does-anything-exist',
      'fine-tuning-universe',
      'problem-of-evil',
    ];
    $exclude_ids = [];
    foreach ($featured_slugs as $slug) {
      $p = get_page_by_path($slug, OBJECT, 'ce_article');
      if ($p) $exclude_ids[] = $p->ID;
    }

    // First try custom post type
    $args = [
      'post_type'      => 'ce_article',
      'posts_per_page' => 6,
      'post_status'    => 'publish',
      'orderby'        => 'menu_order',
      'order'          => 'ASC',
      'post__not_in'   => $exclude_ids,
    ];
    $articles = new WP_Query($args);

    // Fallback to regular posts
    if (! $articles->have_posts()) {
      $args['post_type'] = 'post';
      $articles = new WP_Query($args);
    }

    $i = 0;

    if ($articles->have_posts()) :
      while ($articles->have_posts()) : $articles->the_post();
        $topics = wp_get_post_terms(get_the_ID(), 'ce_topic');
        $topic_name = (!empty($topics) && !is_wp_error($topics)) ? $topics[0]->name : get_post_type_object(get_post_type())->labels->singular_name;
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
        <h3 class="card-title">
          <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <p class="card-text"><?php echo esc_html( get_the_excerpt() ); ?></p>
        <a href="<?php the_permalink(); ?>" class="card-link">
          Read more<span class="screen-reader-text">: <?php echo esc_html( get_the_title() ); ?></span>
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </article>
    <?php
        $i++;
      endwhile;
      wp_reset_postdata();
    else :
    ?>
      <!-- Static fallback cards when no posts exist yet -->
      <?php
      $static_cards = [
        ['tag' => 'Existence',    'icon' => '🌱', 'title' => 'What Is The Purpose of Life?',                    'text' => 'Is there an objective reason we exist, or are we left to invent our own meaning in a purposeless universe?'],
        ['tag' => 'Cosmology',    'icon' => '✨', 'title' => 'Can Existence Come Out Of Nothing?',             'text' => 'Physics and philosophy both wrestle with this. Can "nothing" ever produce something — or does every effect demand a cause?'],
        ['tag' => 'Theology',     'icon' => '🔭', 'title' => 'Does A Higher Power Exist?',                     'text' => 'From the fine-tuning of the cosmos to the existence of consciousness — what does the evidence actually point toward?'],
        ['tag' => 'Theodicy',     'icon' => '⚖️', 'title' => 'But What About The Problem of Evil?',            'text' => 'If God is all-powerful and all-good, why does suffering exist? The most emotionally charged objection to belief in God.'],
        ['tag' => 'Science',      'icon' => '🔬', 'title' => 'Does Science Provide All The Answers?',          'text' => 'Science is one of humanity\'s greatest tools. But does it answer questions of meaning, consciousness, and morality?'],
        ['tag' => 'Ethics',       'icon' => '🧭', 'title' => 'What About Ethics &amp; Morality Without Religion?', 'text' => 'Can a secular worldview ground objective morality? Or does ethics without God ultimately collapse into relativism?'],
      ];
      foreach ($static_cards as $card) : ?>
        <article class="card reveal">
          <?php echo ce_card_placeholder( $card['tag'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG built from constants in inc/ce-icons.php. ?>
          <div class="card-body">
            <p class="card-tag"><?php echo esc_html($card['tag']); ?></p>
            <h3 class="card-title"><?php echo wp_kses_post( $card['title'] ); ?></h3>
            <p class="card-text"><?php echo esc_html($card['text']); ?></p>
            <a href="#" class="card-link" aria-label="<?php echo esc_attr( $card['title'] ); ?>">
              Read more
              <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>


<!-- ── PERSONA JOURNEY ENTRY ─────────────────────────────────────────────── -->
<section class="journey-entry">
  <div class="je-inner">
    <div class="je-text">
      <p class="je-eyebrow">Your personalised path</p>
      <h2 class="je-title">Where are you <em>starting from?</em></h2>
      <p class="je-sub">Not a generic introduction — an argument written for someone exactly like you. Ten questions. Two minutes.</p>
      <div id="je-stats" class="je-stats" style="display:none;"></div>
      <div class="je-actions">
        <a href="<?php echo esc_url( home_url('/quiz') ); ?>" class="btn-journey-cta" id="ce-quiz-btn">
          Find my path
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
        <a href="<?php echo esc_url( home_url('/journeys') ); ?>" class="je-browse">
          Browse all 14 paths →
        </a>
      </div>
    </div>

  </div>
</section>

<div class="ce-divider"></div>

<!-- ── CTA ───────────────────────────────────────────────────────────────── -->
<section class="cta" id="contact">
  <div class="cta-left reveal">
    <p class="section-label">Still Searching?</p>
    <h2>Want to ask <span>more questions?</span></h2>
    <p>Whether you are a convinced atheist, an agnostic, a scientist, a sceptic, or someone carrying doubts you have never voiced — this is for you. Every question deserves a serious answer, not a rehearsed one.</p>
    <div class="cta-buttons">
      <a href="<?php echo esc_url(home_url('/contact-compelling-evidence')); ?>" class="btn-primary">Get In Touch</a>
      <a href="#articles" class="btn-ghost">Browse Topics</a>
    </div>
  </div>

  <div class="cta-right reveal">
    <div class="cta-stat">
      <span class="cta-stat-num">400+</span>
      <span class="cta-stat-label">Verses in the Quran addressing disbelievers directly</span>
    </div>
    <div class="cta-stat">
      <span class="cta-stat-num">10<sup style="font-size:0.5em;vertical-align:super;">80</sup></span>
      <span class="cta-stat-label">Atoms in the observable universe</span>
    </div>
    <div class="cta-stat">
      <span class="cta-stat-num">1.9B</span>
      <span class="cta-stat-label">Muslims asking the same questions</span>
    </div>
    <div class="cta-stat">
      <span class="cta-stat-num">∞</span>
      <span class="cta-stat-label">Reasons to keep seeking truth</span>
    </div>
  </div>
</section>


<!-- ── RETURNING VISITOR STATE ─────────────────────────────────────────────── -->
<script>
(function() {
  var PATH_NAMES = {
    'new-atheist':'The New Atheist','agnostic':'The Agnostic',
    'secular-humanist':'The Secular Humanist','antitheist':'The Antitheist',
    'materialist':'The Materialist','muslim-doubts':'The Questioning Muslim',
    'apatheist':'The Apatheist','deist':'The Deist','scientist':'The Scientist',
    'classical-atheist':'The Classical Atheist','ex-believer':'The Ex-Believer',
    'spiritual-seeker':'The Spiritual Seeker','freethinker':'The Freethinker',
    'true-muslim':'The Committed Muslim',
  };
  var SCREENS = ['horizon','singularity','calibration','emergence','entropy','constant','signal','resonance','transmission','conclusion'];
  var LABELS  = ['Horizon','Singularity','Calibration','Emergence','Entropy','Constant','Signal','Resonance','Transmission','Conclusion'];

  window.addEventListener('load', function() {
    try {
      var primary   = localStorage.getItem('ce_primary_path');
      var quizTaken = localStorage.getItem('ce_quiz_taken');
      var completed = JSON.parse(localStorage.getItem('ce_completed_paths') || '[]');
      var started   = JSON.parse(localStorage.getItem('ce_started_paths')   || '[]');

      if (!primary || !quizTaken) return;

      var name    = PATH_NAMES[primary] || primary;
      var saved   = localStorage.getItem('ce_progress_' + primary);
      var isDone  = completed.indexOf(primary) >= 0;
      var label   = saved ? LABELS[SCREENS.indexOf(saved)] || saved : 'Horizon';
      var href    = '/journey/' + primary + '/';

      // Update hero CTA area
      var quizBtn = document.getElementById('ce-quiz-btn');
      if (quizBtn) {
        if (isDone) {
          quizBtn.textContent = '✦  Journey complete — revisit';
          quizBtn.href = href;
          quizBtn.style.background = 'linear-gradient(135deg,rgba(245,197,24,0.2),rgba(212,160,23,0.2))';
          quizBtn.style.borderColor = 'rgba(245,197,24,0.3)';
          quizBtn.style.color = 'rgba(245,197,24,0.9)';
        } else if (saved && saved !== 'horizon') {
          quizBtn.innerHTML = 'Resume your path · ' + label + ' <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
          quizBtn.href = href;
        } else {
          quizBtn.innerHTML = 'Begin your path <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
          quizBtn.href = href;
        }
      }

      // Show progress indicator in journey entry section
      var statsRow = document.getElementById('je-stats');
      if (statsRow) {
        statsRow.innerHTML =
          '<span>' + started.length + ' started</span>' +
          '<span class="je-stats-sep"></span>' +
          '<span>' + completed.length + ' completed</span>' +
          '<a href="/my-progress">View all →</a>';
        statsRow.style.display = 'flex';
      }
    } catch(e) {}
  });
})();
</script>

<!-- ── HERO PARALLAX ────────────────────────────────────────────────────── -->
<script>
(function() {
  // Respect reduced motion preference
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  // Skip on mobile / narrow screens
  if (window.innerWidth < 768) return;

  var hero = document.querySelector('.hero');
  if (!hero) return;

  var verse = hero.querySelector('.hero-verse-img-wrap');
  var heroSub = hero.querySelector('.hero-sub');
  var searchWrap = hero.querySelector('.hero-search-wrap');
  var ticking = false;
  var lastY = 0;

  function onScroll() {
    lastY = window.pageYOffset || window.scrollY;
    if (!ticking) {
      requestAnimationFrame(update);
      ticking = true;
    }
  }

  // Cache heroH once to avoid forced reflow on every scroll event
  var heroH = hero.offsetHeight;

  function update() {
    ticking = false;

    // Early exit once scrolled past hero
    if (lastY > heroH + 200) return;

    var ratio = Math.min(lastY / heroH, 1); // 0 → 1 as hero scrolls away

    // Layer 1: dot grid — drifts upward at 0.5x (background depth)
    hero.style.setProperty('--plx-dots', 'translate3d(0,' + (lastY * 0.5) + 'px,0)');

    // Layer 2: glow orb — drifts at 0.35x + slight scale
    hero.style.setProperty('--plx-orb', 'translate3d(0,' + (lastY * 0.35) + 'px,0) scale(' + (1 + ratio * 0.15) + ')');

    // Layer 3: Arabic verse — rises slower than content (creates float effect)
    if (verse) {
      verse.style.transform = 'translate3d(0,' + (lastY * -0.15) + 'px,0)';
      verse.style.opacity = Math.max(0, 1 - ratio * 1.4);
    }

    // Layer 4: h1 subtitle — fades and drifts slightly
    if (heroSub) {
      heroSub.style.transform = 'translate3d(0,' + (lastY * -0.08) + 'px,0)';
      heroSub.style.opacity = Math.max(0, 1 - ratio * 1.6);
    }

    // Layer 5: search area — fades last
    if (searchWrap) {
      searchWrap.style.opacity = Math.max(0, 1 - ratio * 1.8);
    }
  }

  // Apply CSS custom props to pseudo-elements
  var style = document.createElement('style');
  style.textContent =
    '.hero::before { transform: var(--plx-dots, none); }' +
    '.hero::after { transform: var(--plx-orb, none); }';
  document.head.appendChild(style);

  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll(); // initial position
})();
</script>

</main><!-- /#main -->

<?php get_footer(); ?>
