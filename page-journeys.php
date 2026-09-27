<?php
/**
 * Template Name: Journey Map
 * Description: 12 personalised paths — no persona labels shown publicly.
 *              The quiz reveals which path belongs to the reader.
 */
get_header();
?>

<main id="main" role="main">
<div class="journeys-page">

  <!-- Hero -->
  <div class="jy-hero">
    <span class="jy-hero-eyebrow">14 Paths · One Question</span>
    <h1>Where you start<br>is <em>everything.</em></h1>
    <p class="jy-hero-sub">Each path is written for a specific intellectual and emotional starting point. The argument, the tone, the texture — all calibrated for where you actually stand. Not a generic introduction. A case written for you.</p>
    <a href="<?php echo esc_url(home_url('/quiz')); ?>" class="jy-hero-cta">
      Find my path — 2 minutes
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
  </div>

  <!-- How it works -->
  <div class="jy-how">
    <div class="jy-how-step">
      <div class="jy-how-num">1</div>
      <div class="jy-how-title">Ten questions</div>
      <div class="jy-how-desc">No right answers. No trick questions. Just an honest picture of where you stand on the biggest question there is.</div>
    </div>
    <div class="jy-how-step">
      <div class="jy-how-num">2</div>
      <div class="jy-how-title">Your path opens</div>
      <div class="jy-how-desc">An argument written specifically for your starting point. The exact objections you carry. The exact language that reaches you.</div>
    </div>
    <div class="jy-how-step">
      <div class="jy-how-num">3</div>
      <div class="jy-how-title">Follow it honestly</div>
      <div class="jy-how-desc">Seven chapters of argument. Saved progress. Return whenever you want. No pressure. No performance.</div>
    </div>
  </div>

  <!-- Divider -->
  <div class="jy-divider">
    <div class="jy-divider-line"></div>
    <span class="jy-divider-text">Or browse all 14 paths directly</span>
    <div class="jy-divider-line"></div>
  </div>

  <!-- 12 path cards — no persona names, described by intellectual starting point -->
  <div class="jy-grid" id="jy-grid">
    <?php
    $paths = [
      [
        'key'      => 'new-atheist',
        'question' => 'You don\'t believe — and you\'ve done the reading.',
        'desc'     => 'Dawkins, Hitchens, Harris. The scientific picture of reality. A case against religion. This path meets the strongest form of that position.',
      ],
      [
        'key'      => 'agnostic',
        'question' => 'You hold the question genuinely open.',
        'desc'     => 'Not convinced either way. You want evidence, not assertion. This path is built for someone who actually wants to follow the argument.',
      ],
      [
        'key'      => 'secular-humanist',
        'question' => 'You\'ve built a coherent ethical life without God.',
        'desc'     => 'Values, community, meaning — all intact, all secular. The question is whether those goods have a foundation, or whether they float.',
      ],
      [
        'key'      => 'antitheist',
        'question' => 'It\'s not just that God doesn\'t exist — religion causes harm.',
        'desc'     => 'The moral case against religion is your starting point. This path takes that seriously and engages it directly.',
      ],
      [
        'key'      => 'materialist',
        'question' => 'The physical universe is all there is.',
        'desc'     => 'Consciousness, morality, meaning — everything emerges from matter. Nothing supernatural required. This path examines whether that holds.',
      ],
      [
        'key'      => 'muslim-doubts',
        'question' => 'You grew up inside the tradition.',
        'desc'     => 'Something specific in the sources, the history, or the practice created doubt. This path was written from inside, for people carrying it inside.',
      ],
      [
        'key'      => 'apatheist',
        'question' => 'God stopped being relevant.',
        'desc'     => 'Not disproved — just irrelevant to how you live. This path asks whether that indifference holds up when the question is actually pressed.',
      ],
      [
        'key'      => 'deist',
        'question' => 'Something made the universe. A personal God is the step too far.',
        'desc'     => 'A first cause makes sense. A God who intervenes, reveals, and demands — that\'s where the inference breaks down. Or does it?',
      ],
      [
        'key'      => 'scientist',
        'question' => 'You apply evidential standards consistently.',
        'desc'     => 'If the claim is real, the evidence should hold. This path applies exactly the rigour you\'d apply to any other serious claim about reality.',
      ],
      [
        'key'      => 'classical-atheist',
        'question' => 'The concept itself is philosophically incoherent.',
        'desc'     => 'This isn\'t primarily about evidence — it\'s a conceptual problem. This path engages the philosophical case in its strongest form.',
      ],
      [
        'key'      => 'ex-believer',
        'question' => 'You left deliberately.',
        'desc'     => 'Something specific broke the faith. You\'ve processed that. This path doesn\'t ask you to go back — it asks whether the foundational question is still open.',
      ],
      [
        'key'      => 'spiritual-seeker',
        'question' => 'You\'ve found something real in multiple traditions.',
        'desc'     => 'But no single container has held all of it. This path explores whether there is a ground that underlies what you\'ve genuinely found.',
      ],
      [
        'key'      => 'freethinker',
        'question' => 'You think for yourself — and you don\'t apologise for it.',
        'desc'     => 'No tribe. No authority. Just reason, applied honestly. This path asks whether your independence has been applied consistently — including to your own assumptions.',
      ],
      [
        'key'      => 'true-muslim',
        'question' => 'You already believe — and you\'re here to understand why.',
        'desc'     => 'Your faith is intact. This path grounds it in evidence, helps you understand what doubters experience, and equips you to engage with compassion and knowledge.',
      ],
    ];
    foreach ( $paths as $i => $p ) :
      $url = home_url('/journey/' . $p['key'] . '/');
      $num = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
    ?>
    <a href="<?php echo esc_url($url); ?>"
       class="jy-card"
       data-path="<?php echo esc_attr($p['key']); ?>">
      <span class="jy-card-badge" style="display:none;"></span>
      <span class="jy-card-num">Path <?php echo esc_html( $num ); ?></span>
      <div class="jy-card-question"><?php echo esc_html($p['question']); ?></div>
      <div class="jy-card-desc"><?php echo esc_html($p['desc']); ?></div>
      <span class="jy-card-action">
        Begin this path
        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </span>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Bottom CTA -->
  <div class="jy-bottom-cta" style="margin-left:auto;margin-right:auto;max-width:640px;padding:2.8rem 2rem;text-align:center;">
    <h2>Not sure which path is yours?</h2>
    <p>Ten questions. No wrong answers. Two minutes. The quiz routes you to the path that matches where you actually stand — not a generic introduction, but an argument built for your specific starting point.</p>
    <a href="<?php echo esc_url(home_url('/quiz')); ?>" class="jy-hero-cta">
      Take the quiz
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
  </div>

</div>

<!-- Mark started/completed paths from localStorage -->
<script>
(function() {
  try {
    var started   = JSON.parse(localStorage.getItem('ce_started_paths')   || '[]');
    var completed = JSON.parse(localStorage.getItem('ce_completed_paths') || '[]');
    if (!started.length && !completed.length) return;

    document.querySelectorAll('.jy-card[data-path]').forEach(function(card) {
      var key   = card.dataset.path;
      var badge = card.querySelector('.jy-card-badge');
      if (completed.indexOf(key) >= 0) {
        card.classList.add('jy-complete');
        if (badge) { badge.textContent = '✦ Complete'; badge.style.display = ''; }
        card.querySelector('.jy-card-action').innerHTML =
          'Revisit <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
      } else if (started.indexOf(key) >= 0) {
        card.classList.add('jy-started');
        if (badge) { badge.textContent = '→ In progress'; badge.style.display = ''; }
        card.querySelector('.jy-card-action').innerHTML =
          'Resume <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>';
      }
    });
  } catch(e) {}
})();
</script>

</main><!-- /#main -->

<?php get_footer(); ?>
