<?php
/**
 * Template Name: Ask a Question
 * Description: Dedicated page for question submissions
 */
get_header(); ?>

<main class="ce-page ce-ask" id="main">

  <header class="page-hero ask-hero">
    <div class="page-hero-inner">
      <p class="section-label">Got something on your mind?</p>
      <h1>Ask a Question</h1>
      <p class="hero-sub">Can't find an answer on the site? Submit your question and we'll do our best to respond.</p>
    </div>
  </header>

  <?php /* Item 15 — Ask a Question editorial framing. */ ?>
  <aside class="page-intro-context" aria-label="<?php esc_attr_e( 'About this form', 'compelling-evidence' ); ?>">
    <div class="page-intro-inner">
      <p>
        <?php esc_html_e( 'Submit a question and the editorial team will read it. Some questions are answered privately by email. Some are answered publicly on the Q&A surface, where the response can serve future readers facing the same difficulty. A few are turned into full articles when the topic warrants the longer treatment. Submission does not commit you to any of these — your question can be answered privately even if it would also make a good public response.', 'compelling-evidence' ); ?>
      </p>
      <p>
        <?php esc_html_e( 'Response time varies. Simple factual queries are usually addressed within a few days. Substantive questions — the kind that require checking sources, weighing competing scholarly views, or working through an argument carefully — take longer, and we would rather take the time than reply with something half-considered. Personal or urgent questions get priority over the public-Q&A queue.', 'compelling-evidence' ); ?>
      </p>
      <p>
        <?php esc_html_e( 'Useful questions are specific. "Why does Islam permit X?" is harder to answer well than "I have read this article about X and I am stuck on this specific point — can you help me understand it?" The more context you give, the more useful the response can be. There is no question we treat as off-limits, but the more honestly the question is asked, the more honestly we can answer it.', 'compelling-evidence' ); ?>
      </p>
    </div>
  </aside>

  <div class="ask-body">

    <div class="ask-form-wrap">
      <?php if ( isset($_GET['sent']) && $_GET['sent'] === '1' ) : ?>
        <div class="ask-success">
          <svg width="48" height="48" fill="none" stroke="#d4a8ff" stroke-width="1.5" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          <h2>Question received!</h2>
          <p>Thank you for reaching out. We'll review your question and get back to you as soon as we can.</p>
          <a href="<?php echo esc_url(home_url('/')); ?>" class="btn-primary">Back to Home</a>
        </div>

      <?php else : ?>

        <?php
        // Surface a friendly error message when the handler redirects back
        // with ?ask_error=… rather than letting it die() with a wp_die screen.
        if ( isset( $_GET['ask_error'] ) ) : // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only status flag for display.
            $errs = [
                'security' => 'Your session expired. Please reload this page and try again.',
                'rate'     => 'You\'ve submitted several questions recently. Please wait a little before sending another.',
                'missing'  => 'Please fill in your name, email, and question before submitting.',
                'email'    => 'That email address doesn\'t look right. Please check and try again.',
                'short'    => 'Your question seems very short. Please add more detail so we can give a useful answer.',
                'server'   => 'Something went wrong on our end. Please try again in a moment.',
            ];
            $code = sanitize_key( wp_unslash( $_GET['ask_error'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only status flag. // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only status flag for display.
            $msg  = $errs[ $code ] ?? 'There was a problem submitting your question. Please try again.';
        ?>
          <div class="ask-error" role="alert" style="background:rgba(255,107,107,0.1);border:1px solid rgba(255,107,107,0.4);color:#ff8a8a;padding:1rem 1.2rem;border-radius:8px;margin-bottom:1.5rem;font-size:.95rem;">
            <strong>Couldn't send your question:</strong> <?php echo esc_html( $msg ); ?>
          </div>
        <?php endif; ?>

        <form class="ask-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
          <?php wp_nonce_field('ce_ask_question', 'ce_ask_nonce'); ?>
          <input type="hidden" name="action" value="ce_ask_question">
          <input type="hidden" name="ask_return_to" value="<?php echo esc_url( get_permalink() ); ?>">

          <?php /* Honeypot — invisible to humans, attractive to bots. Never display, never read in a screen reader. */ ?>
          <div aria-hidden="true" style="position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden;">
            <label for="ask-website">Website (leave blank)</label>
            <input type="text" id="ask-website" name="ask_website" tabindex="-1" autocomplete="off" value="">
          </div>

          <div class="form-row form-row--2">
            <div class="form-group">
              <label for="ask-name">Your Name <span aria-hidden="true">*</span></label>
              <input type="text" id="ask-name" name="ask_name" required placeholder="e.g. Ahmad" value="">
            </div>
            <div class="form-group">
              <label for="ask-email">Email Address <span aria-hidden="true">*</span></label>
              <input type="email" id="ask-email" name="ask_email" required placeholder="name@example.com" value="">
            </div>
          </div>

          <div class="form-group">
            <label for="ask-topic">Topic / Category</label>
            <select id="ask-topic" name="ask_topic">
              <option value="">— Select a topic (optional) —</option>
              <?php
              $terms = get_terms(['taxonomy' => 'ce_topic', 'hide_empty' => false]);
              if (!empty($terms) && !is_wp_error($terms)) :
                foreach ($terms as $term) :
              ?>
                <option value="<?php echo esc_attr($term->name); ?>"><?php echo esc_html($term->name); ?></option>
              <?php endforeach; endif; ?>
              <option value="Other">Other</option>
            </select>
          </div>

          <div class="form-group">
            <label for="ask-question">Your Question <span aria-hidden="true">*</span></label>
            <textarea id="ask-question" name="ask_question" required rows="6" placeholder="Type your question here…"></textarea>
          </div>

          <div class="form-group form-group--check">
            <label class="checkbox-label">
              <input type="checkbox" name="ask_consent" required>
              <span>I understand this question may be published on the site (anonymously) as an article.</span>
            </label>
          </div>

          <button type="submit" class="btn-primary ask-submit">
            Submit Question
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </button>
        </form>

      <?php endif; ?>
    </div>

    <aside class="ask-sidebar">
      <div class="sidebar-widget">
        <h3 class="widget-title">Before you ask…</h3>
        <p style="color:var(--muted);font-size:.9rem;line-height:1.7;margin-bottom:1rem;">Your question may already be answered. Try searching first:</p>
        <div class="ask-search">
          <input type="search" id="ask-search-input" placeholder="Search the site…" aria-label="Search articles">
          <a href="#" id="ask-search-go" class="btn-ghost" style="margin-top:.8rem;display:block;text-align:center;font-size:.85rem;">Search</a>
        </div>
      </div>

      <div class="sidebar-widget">
        <h3 class="widget-title">Popular Questions</h3>
        <?php
        $popular = get_posts([
          'post_type'      => ['ce_article', 'post'],
          'posts_per_page' => 5,
          'orderby'        => 'comment_count',
          'order'          => 'DESC',
        ]);
        if ($popular) : ?>
          <ul class="related-list">
            <?php foreach ($popular as $p) : ?>
              <li><a href="<?php echo esc_url(get_permalink($p)); ?>">
                <?php echo esc_html( get_the_title($p) ); ?>
                <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
              </a></li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </aside>

  </div>
</main>

<script>
  const askInput = document.getElementById('ask-search-input');
  const askGo    = document.getElementById('ask-search-go');
  if (askGo && askInput) {
    askGo.addEventListener('click', (e) => {
      e.preventDefault();
      const q = askInput.value.trim();
      if (q) window.location.href = '/?s=' + encodeURIComponent(q);
    });
    askInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') { e.preventDefault(); askGo.click(); }
    });
  }
</script>

<?php get_footer(); ?>
