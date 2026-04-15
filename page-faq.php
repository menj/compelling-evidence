<?php
/**
 * Template Name: FAQ
 * Description: Frequently Asked Questions with accordion UI and FAQPage schema markup
 */
get_header();

$faq_sections = [

    'About This Site' => [
        'What is Compelling Evidence?' =>
            'Compelling Evidence is an independent, evidence-based inquiry into the questions that matter most — the existence of God, the coherence of Islam, and the objections raised against both. It is written for the curious, the doubtful, and the unconvinced.',
        'Who runs this site?' =>
            'The site is maintained by an independent team of researchers, writers, and editors. It is not affiliated with any mosque, government, political movement, or religious organisation.',
        'Is this a dawah site?' =>
            'It is a site that presents the case for Islam — honestly, using evidence, and at full intellectual strength. If that constitutes dawah, then yes. But it does not use emotional manipulation, fear tactics, or social pressure. Every argument is built to survive scrutiny, not to bypass it.',
        'Why should I trust this site?' =>
            'You shouldn\'t — not on trust. The site asks you to evaluate the arguments on their merits. Every claim is sourced. Every major objection is presented at full strength before being addressed. The <a href="/editorial-policy">editorial policy</a> commits to intellectual honesty, and you are invited to hold the site to that standard.',
        'Is this site trying to convert me?' =>
            'The site presents the strongest case it can for Islam. What you do with that case is your decision. No article ends with a conversion pitch. No page collects your data for follow-up. The site respects your autonomy — because Islam itself requires that faith be freely chosen to have any value.',
    ],

    'The Quiz & Journeys' => [
        'What is the quiz?' =>
            'A 10-question assessment that identifies your starting point — atheist, agnostic, spiritual seeker, questioning Muslim, etc. — and routes you to a personalised reading journey designed for where you actually are, not where someone wishes you were.',
        'Is my quiz data stored on a server?' =>
            'No. Your quiz answers and journey progress are stored locally in your browser using localStorage. Nothing is sent to any server. If you clear your browser data, your progress is lost.',
        'What are journeys?' =>
            'Each journey is a curated sequence of articles ordered for a specific persona. A classical atheist starts with cosmological arguments. A questioning Muslim starts with doubt and inner struggle. The content is the same 100 articles — the sequencing is different.',
        'Can I read articles without taking the quiz?' =>
            'Absolutely. The <a href="/articles">articles page</a> gives you full access to all 100 articles, organised by topic. The quiz is a starting point, not a gate.',
        'How many personas are there?' =>
            'Fourteen — covering the full spectrum from hard atheism through agnosticism, deism, and spiritual seeking to the questioning Muslim and the committed Muslim.',
    ],

    'Common Objections' => [
        'Isn\'t this just preaching to the converted?' =>
            'The opposite. The site is written for people who do not believe, who have serious objections, and who require evidence before commitment. Every article addresses the strongest version of the opposing argument — not a strawman.',
        'Why Islam and not just generic theism?' =>
            'The site begins with generic theism — the existence of God is argued from philosophy, cosmology, and ethics without reference to any specific religion. Once the case for God is established, the site then examines which tradition best accounts for what reason has established. Islam is presented as the strongest candidate. The reader is invited to evaluate that claim.',
        'Don\'t you cherry-pick easy objections?' =>
            'No. The site addresses the hardest objections head-on: the age of Aisha, apostasy law, slavery in the sources, the problem of evil, Quranic cosmology, hadith reliability, the violence of early Islam, and more. Each is presented at full strength before the response is given.',
        'What if I read everything and still don\'t believe?' =>
            'Then you have engaged honestly with the evidence, and the site has done its job. Compelling Evidence does not define success as conversion. It defines success as honest engagement. A person who examines the evidence carefully and reaches a different conclusion has done something more valuable than a person who agrees without thinking.',
    ],

    'Technical & Privacy' => [
        'Does this site use cookies?' =>
            'The site uses essential cookies for basic functionality. It does not use tracking cookies, advertising cookies, or third-party analytics that follow you across the web.',
        'Is my data collected or shared?' =>
            'Quiz and journey progress are stored locally in your browser — never on our servers. If you submit a question through the <a href="/ask-a-question">Ask a Question</a> page, we receive only what you voluntarily provide. We do not sell, share, or monetise any data.',
        'How do I contact the site?' =>
            'Use the <a href="/contact-compelling-evidence">contact page</a> or the <a href="/ask-a-question">Ask a Question</a> form. We read every submission.',
        'Can I share or reprint articles?' =>
            'You may share links freely. For reprinting or republication, please <a href="/contact-compelling-evidence">contact us</a> first.',
    ],
];
?>

<main class="ce-page ce-faq" id="main">

  <header class="page-hero faq-hero">
    <div class="page-hero-inner">
      <p class="section-label">Got questions about the site?</p>
      <h1>Frequently Asked Questions</h1>
      <p class="hero-sub">Answers to the most common questions about Compelling Evidence — what it is, how it works, and what it asks of you.</p>
    </div>
  </header>

  <div class="faq-body">
    <?php foreach ( $faq_sections as $section_title => $questions ) : ?>
      <section class="faq-section">
        <h2 class="faq-section-title"><?php echo esc_html( $section_title ); ?></h2>
        <div class="faq-accordion">
          <?php foreach ( $questions as $question => $answer ) : ?>
            <div class="faq-item">
              <button class="faq-question" aria-expanded="false">
                <span class="faq-q-text"><?php echo esc_html( $question ); ?></span>
                <svg class="faq-chevron" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
              </button>
              <div class="faq-answer" aria-hidden="true">
                <div class="faq-answer-inner">
                  <p><?php echo wp_kses_post( $answer ); ?></p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  </div>

</main>

<?php
// ── FAQPage Schema (JSON-LD) ──
$schema_items = [];
foreach ( $faq_sections as $questions ) {
    foreach ( $questions as $q => $a ) {
        $schema_items[] = [
            '@type'          => 'Question',
            'name'           => $q,
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => wp_strip_all_tags( $a ),
            ],
        ];
    }
}

$schema = [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => $schema_items,
];
?>
<script type="application/ld+json"><?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ); ?></script>

<script>
(function() {
  document.querySelectorAll('.faq-question').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var expanded = this.getAttribute('aria-expanded') === 'true';
      // Close all others in same section
      this.closest('.faq-accordion').querySelectorAll('.faq-question').forEach(function(b) {
        b.setAttribute('aria-expanded', 'false');
        b.nextElementSibling.setAttribute('aria-hidden', 'true');
      });
      if (!expanded) {
        this.setAttribute('aria-expanded', 'true');
        this.nextElementSibling.setAttribute('aria-hidden', 'false');
      }
    });
  });
})();
</script>

<?php get_footer(); ?>
