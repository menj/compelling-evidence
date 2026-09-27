<footer class="ce-footer">
  <div class="footer-inner">

    <div class="footer-grid">

      <!-- Column 1: Brand -->
      <div class="footer-brand">
        <div class="footer-logo">
          <span class="footer-logo-ce">Compelling</span><span class="footer-logo-ev"> Evidence</span>
        </div>
        <p class="footer-tagline">Honest inquiry into the questions that matter most — for the curious, the doubtful, and the unconvinced.</p>
        <a href="<?php echo esc_url(home_url('/quiz')); ?>" class="footer-cta">
          Find my path
          <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>

      <!-- Column 2: Topics -->
      <div class="footer-col">
        <h3><?php esc_html_e('Topics', 'compelling-evidence'); ?></h3>
        <?php
        $topics = get_terms(['taxonomy' => 'ce_topic', 'hide_empty' => false, 'number' => 7, 'orderby' => 'count', 'order' => 'DESC']);
        if (!empty($topics) && !is_wp_error($topics)) : ?>
          <ul>
            <?php foreach ($topics as $t) : ?>
              <li><a href="<?php echo esc_url(get_term_link($t)); ?>"><?php echo esc_html($t->name); ?></a></li>
            <?php endforeach; ?>
          </ul>
        <?php else : ?>
          <ul>
            <li><a href="<?php echo esc_url(home_url('/articles')); ?>">All Articles</a></li>
            <li><a href="<?php echo esc_url(home_url('/topic/does-god-exist')); ?>">Does God Exist?</a></li>
            <li><a href="<?php echo esc_url(home_url('/topic/the-problem-of-evil')); ?>">The Problem of Evil</a></li>
            <li><a href="<?php echo esc_url(home_url('/topic/the-inner-journey')); ?>">The Inner Journey</a></li>
            <li><a href="<?php echo esc_url(home_url('/topic/rights-freedom')); ?>">Rights &amp; Freedom</a></li>
          </ul>
        <?php endif; ?>
      </div>

      <!-- Column 3: Navigate -->
      <div class="footer-col">
        <h3><?php esc_html_e('Navigate', 'compelling-evidence'); ?></h3>
        <ul>
          <li><a href="<?php echo esc_url(home_url('/quiz')); ?>"><?php esc_html_e('Take the Quiz', 'compelling-evidence'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/journeys')); ?>"><?php esc_html_e('All Paths', 'compelling-evidence'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/articles')); ?>"><?php esc_html_e('Articles', 'compelling-evidence'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/qa')); ?>"><?php esc_html_e('Q&amp;A', 'compelling-evidence'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/ask-a-question')); ?>"><?php esc_html_e('Ask a Question', 'compelling-evidence'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/faq')); ?>"><?php esc_html_e('FAQ', 'compelling-evidence'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/glossary')); ?>"><?php esc_html_e('Glossary', 'compelling-evidence'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/my-progress')); ?>"><?php esc_html_e('My Progress', 'compelling-evidence'); ?></a></li>
        </ul>
      </div>

      <!-- Column 4: About -->
      <div class="footer-col">
        <h3><?php esc_html_e('About', 'compelling-evidence'); ?></h3>
        <ul>
          <li><a href="<?php echo esc_url(home_url('/about-compelling-evidence')); ?>"><?php esc_html_e('About This Site', 'compelling-evidence'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/editorial-policy')); ?>"><?php esc_html_e('Editorial Policy', 'compelling-evidence'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php esc_html_e('Privacy Policy', 'compelling-evidence'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/contact-compelling-evidence')); ?>"><?php esc_html_e('Contact', 'compelling-evidence'); ?></a></li>
        </ul>
      </div>

    </div><!-- /footer-grid -->

    <div class="footer-bottom">
      <span>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?>. <?php esc_html_e('All rights reserved.', 'compelling-evidence'); ?></span>
      <span class="footer-bottom-links">
        <a href="<?php echo esc_url(home_url('/about-compelling-evidence')); ?>"><?php esc_html_e('About', 'compelling-evidence'); ?></a>
        <a href="<?php echo esc_url(home_url('/editorial-policy')); ?>"><?php esc_html_e('Editorial Policy', 'compelling-evidence'); ?></a>
        <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>"><?php esc_html_e('Privacy Policy', 'compelling-evidence'); ?></a>
        <a href="<?php echo esc_url(home_url('/contact-compelling-evidence')); ?>"><?php esc_html_e('Contact', 'compelling-evidence'); ?></a>
      </span>
    </div>

  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
