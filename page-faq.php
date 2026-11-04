<?php
/**
 * Template Name: FAQ
 * Description: Frequently Asked Questions with accordion UI. Its FAQPage node is
 * built in ce_schema_faq_node() (inc/ce-schema.php) and printed in the theme graph.
 */
get_header();

// The questions and answers live in ce_faq_sections() (inc/ce-schema.php),
// which also builds this page's FAQPage node in the one theme graph.
$faq_sections = ce_faq_sections();
?>

<main class="ce-page ce-faq" id="main" tabindex="-1">

  <header class="page-hero faq-hero">
    <div class="page-hero-inner">
      <p class="section-label">Got questions about the site?</p>
      <h1>Frequently Asked Questions</h1>
      <p class="hero-sub">Answers to the most common questions about Compelling Evidence — what it is, how it works, and what it asks of you.</p>
    </div>
  </header>

  <?php /* Item 15 — FAQ editorial framing. */ ?>
  <aside class="page-intro-context" aria-label="<?php esc_attr_e( 'About these FAQs', 'compelling-evidence' ); ?>">
    <div class="page-intro-inner">
      <p>
        <?php esc_html_e( 'These are the questions readers actually ask about the site itself: what it is, who runs it, what its editorial commitments are, what it does and does not ask of its readers. The answers are short by design — long-form treatment of substantive questions belongs in the articles, not here.', 'compelling-evidence' ); ?>
      </p>
      <p>
        <?php esc_html_e( 'Three principles guide the answers below. First, honesty about what the site is — it presents the case for Islam at full intellectual strength, but it does not pretend to be neutral or to lack a point of view. Second, respect for the reader\'s autonomy — no manipulation, no data harvesting, no obligation, and no follow-up unless the reader initiates it. Third, accountability — the site\'s claims are sourced, the editorial process is documented, and corrections are issued when errors are found.', 'compelling-evidence' ); ?>
      </p>
      <p>
        <?php esc_html_e( 'For substantive questions about Islam itself — about God, prophethood, the Quran, ethics, or any of the topics the articles address — see the articles directly or use the search. For questions about reader-submitted Q&A, see the dedicated Q&A surface. The form below answers structural questions about the site as a whole.', 'compelling-evidence' ); ?>
      </p>
    </div>
  </aside>

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
