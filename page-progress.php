<?php
/**
 * Template Name: My Progress
 * Description: Personal journey progress dashboard — driven by localStorage
 *              and WP user meta (if logged in). No data sent without user action.
 */
get_header();
?>

<main id="main" role="main">
<div class="progress-page">

  <div class="progress-hero">
    <span style="font-size:0.68rem;letter-spacing:0.22em;text-transform:uppercase;color:rgba(12,212,224,0.5);display:block;margin-bottom:0.6rem;">
      <?php esc_html_e('Your Progress', 'compelling-evidence'); ?>
    </span>
    <h1 id="progress-greeting"><?php esc_html_e('Your Journeys', 'compelling-evidence'); ?></h1>
    <p><?php esc_html_e('Every path you have started or completed.', 'compelling-evidence'); ?></p>
  </div>

  <div class="progress-stats" id="progress-stats">
    <div class="ps-card" id="stat-started">
      <span class="ps-num" id="stat-started-num">—</span>
      <span class="ps-label"><?php esc_html_e('Started', 'compelling-evidence'); ?></span>
    </div>
    <div class="ps-card gold" id="stat-completed">
      <span class="ps-num" id="stat-completed-num">—</span>
      <span class="ps-label"><?php esc_html_e('Completed', 'compelling-evidence'); ?></span>
    </div>
    <div class="ps-card highlight" id="stat-quiz">
      <span class="ps-num" id="stat-quiz-val">—</span>
      <span class="ps-label"><?php esc_html_e('Your Path', 'compelling-evidence'); ?></span>
    </div>
  </div>

  <div class="journey-progress-grid" id="journey-grid">
    <!-- Populated by JS from localStorage -->
    <div style="color:rgba(242,238,255,0.3);font-size:0.85rem;grid-column:1/-1;text-align:center;padding:2rem;">
      <?php esc_html_e('Loading your progress...', 'compelling-evidence'); ?>
    </div>
  </div>

  <div class="progress-reset">
    <button onclick="clearProgress()"><?php esc_html_e('Clear all progress', 'compelling-evidence'); ?></button>
  </div>

</div>

<script>
(function() {
  var PATHS = [
    {key:'new-atheist',       name:'The New Atheist'},
    {key:'agnostic',          name:'The Agnostic'},
    {key:'secular-humanist',  name:'The Secular Humanist'},
    {key:'antitheist',        name:'The Antitheist'},
    {key:'materialist',       name:'The Materialist'},
    {key:'muslim-doubts',     name:'The Questioning Muslim'},
    {key:'apatheist',         name:'The Apatheist'},
    {key:'deist',             name:'The Deist'},
    {key:'scientist',         name:'The Scientist'},
    {key:'classical-atheist', name:'The Classical Atheist'},
    {key:'ex-believer',       name:'The Ex-Believer'},
    {key:'spiritual-seeker',  name:'The Spiritual Seeker'},
    {key:'freethinker',       name:'The Freethinker'},
    {key:'true-muslim',       name:'The Committed Muslim'},
  ];
  var SCREENS = ['horizon','singularity','calibration','emergence','entropy','constant','signal','resonance','transmission','conclusion'];
  var TM_SCREENS = ['foundation','understanding','honesty','equipping','compassion','knowledge','mission','conclusion'];
  var SCREEN_LABELS = {
    horizon:'Horizon', singularity:'Singularity', calibration:'Calibration',
    emergence:'Emergence', entropy:'Entropy', constant:'Constant',
    signal:'Signal', resonance:'Resonance', transmission:'Transmission', conclusion:'Conclusion',
    foundation:'Foundation', understanding:'Understanding', honesty:'Honesty',
    equipping:'Equipping', compassion:'Compassion', knowledge:'Knowledge', mission:'Mission', 'v2-preview':'Volume II'
  };
  var BASE = '<?php echo esc_js(home_url("/")); ?>';

  function getProgress(key) {
    try { return localStorage.getItem('ce_progress_' + key); } catch(e) { return null; }
  }
  function getList(k) {
    try { return JSON.parse(localStorage.getItem(k) || '[]'); } catch(e) { return []; }
  }

  function screenPct(screenId, pathKey) {
    var screens = (pathKey === 'true-muslim') ? TM_SCREENS : SCREENS;
    var idx = screens.indexOf(screenId);
    if (idx < 0) return 0;
    return Math.round(((idx + 1) / screens.length) * 100);
  }

  function renderGrid() {
    var started   = getList('ce_started_paths');
    var completed = getList('ce_completed_paths');
    var primary   = '';
    try { primary = localStorage.getItem('ce_primary_path') || ''; } catch(e) {}

    // If no progress at all, redirect to quiz
    if (started.length === 0 && completed.length === 0) {
      var grid = document.getElementById('journey-grid');
      grid.innerHTML =
        '<div class="progress-quiz-cta" style="grid-column:1/-1;">' +
        '<h2>You haven\'t started a journey yet</h2>' +
        '<p>Take the quiz to get a personalised path written specifically for where you stand.</p>' +
        '<a href="' + BASE + 'quiz" style="display:inline-flex;align-items:center;gap:0.4rem;padding:0.85rem 1.8rem;border-radius:50px;background:linear-gradient(135deg,#0cd4e0,#09b8c4);color:#0d0820;font-size:0.9rem;font-weight:700;text-decoration:none;">Take the quiz →</a>' +
        '</div>';
      document.getElementById('stat-started-num').textContent = '0';
      document.getElementById('stat-completed-num').textContent = '0';
      document.getElementById('stat-quiz-val').textContent = '—';
      document.getElementById('progress-greeting').textContent = 'No journeys yet';
      return;
    }

    // Stats
    document.getElementById('stat-started-num').textContent  = started.length;
    document.getElementById('stat-completed-num').textContent = completed.length;

    var primaryPath = PATHS.find(function(p){ return p.key === primary; });
    document.getElementById('stat-quiz-val').textContent = primaryPath ? primaryPath.name : '—';

    // Greeting
    var greeting = document.getElementById('progress-greeting');
    greeting.textContent = completed.length >= 14 ? 'All paths completed' : 'Your Journeys';

    // Grid — only show paths the user has started or completed
    var grid = document.getElementById('journey-grid');
    grid.innerHTML = '';

    var activePaths = PATHS.filter(function(p) {
      return started.indexOf(p.key) >= 0 || completed.indexOf(p.key) >= 0;
    });

    activePaths.forEach(function(p) {
      var savedScreen = getProgress(p.key);
      var isDone      = completed.indexOf(p.key) >= 0;
      var isStarted   = started.indexOf(p.key) >= 0;
      var isPrimary   = p.key === primary;

      var card = document.createElement('div');
      var stateClass = isDone ? 'completed' : (isStarted ? 'in-progress' : 'not-started');
      card.className = 'jpc ' + stateClass;

      var pct = isDone ? 100 : (savedScreen ? screenPct(savedScreen, p.key) : 0);
      var screenLabel = savedScreen ? (SCREEN_LABELS[savedScreen] || savedScreen) : '—';
      var statusIcon  = isDone ? '✦' : (isStarted ? '→' : '·');
      var statusText  = isDone ? 'Completed' : (isStarted ? 'In progress' : 'Not started');
      var statusCls   = isDone ? 'done' : (isStarted ? 'going' : '');
      var primaryTag  = isPrimary ? ' <span style="font-size:0.6rem;background:rgba(12,212,224,0.12);border:1px solid rgba(12,212,224,0.2);padding:0.1rem 0.4rem;border-radius:50px;color:rgba(12,212,224,0.6);">Your path</span>' : '';

      var actionHref = BASE + 'journey/' + p.key + '/';
      var actionLabel = isDone ? 'Revisit' : (isStarted ? 'Continue' : 'Begin');

      card.innerHTML =
        (isDone ? '<span class="jpc-badge">✦</span>' : '') +
        '<div class="jpc-name">' + p.name + primaryTag + '</div>' +
        '<div class="jpc-status ' + statusCls + '">' + statusIcon + ' ' + statusText + '</div>' +
        '<div class="jpc-bar"><div class="jpc-bar-fill" style="width:' + pct + '%;"></div></div>' +
        '<div class="jpc-screen">' + (isStarted || isDone ? 'At: ' + screenLabel : 'Ready to begin') + '</div>' +
        '<div class="jpc-actions">' +
        '<a href="' + actionHref + '" class="jpc-btn jpc-btn-primary">' + actionLabel + ' journey →</a>' +
        '</div>';

      grid.appendChild(card);
    });
  }

  window.clearProgress = function() {
    if (!confirm('Clear all journey progress? This cannot be undone.')) return;
    try {
      ['ce_started_paths','ce_completed_paths','ce_primary_path','ce_secondary_paths','ce_quiz_taken'].forEach(function(k){ localStorage.removeItem(k); });
      ['new-atheist','agnostic','secular-humanist','antitheist','materialist','muslim-doubts','apatheist','deist','scientist','classical-atheist','ex-believer','spiritual-seeker','freethinker','true-muslim'].forEach(function(k){ localStorage.removeItem('ce_progress_' + k); });
      renderGrid();
    } catch(e) {}
  };

  window.addEventListener('load', renderGrid);
})();
</script>

</main><!-- /#main -->

<?php get_footer(); ?>
