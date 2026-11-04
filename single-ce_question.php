<?php
/**
 * Single Question Template
 * Shows a question with its answer
 */
get_header();

// Article markup for a published question is built in ce_question_article_node()
// (inc/ce-seo.php) and printed in the theme graph, or added to Rank Math's graph
// when Rank Math has no Article of its own (since 2.11.0).
?>

<main class="ce-page ce-qa-single" id="main" tabindex="-1">

  <article id="post-<?php the_ID(); ?>" <?php post_class( 'qa-single' ); ?>>

    <header class="qa-single-header">
      <a href="<?php echo esc_url( home_url( '/qa/' ) ); ?>" class="qa-back-link">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back to Q&A
      </a>

      <div class="qa-question-wrap">
        <span class="qa-label"><?php esc_html_e( 'Question', 'compelling-evidence' ); ?></span>
        <h1 class="qa-title"><?php the_title(); ?></h1>
        <?php
        $topic = get_post_meta( get_the_ID(), '_ce_question_topic', true );
        if ( $topic ) : ?>
          <span class="qa-topic"><?php echo esc_html( $topic ); ?></span>
        <?php endif; ?>
      </div>
    </header>

    <div class="qa-answer-wrap">
      <span class="qa-label qa-label--answer"><?php esc_html_e( 'Answer', 'compelling-evidence' ); ?></span>
      <div class="qa-content">
        <?php
        $answer_raw = get_the_content();
        if ( '' === trim( $answer_raw ) || trim( $answer_raw ) === trim( get_the_title() ) ) {
            echo '<p class="qa-pending">An answer is being prepared for this question.</p>';
        } else {
            // the_content() applies the filter chain — paragraphs, oEmbed,
            // shortcodes, and any registered content filters. Same as what
            // the schema captures above for consistency.
            the_content();
        }
        ?>
      </div>
    </div>

    <footer class="qa-single-footer">
      <div class="qa-meta">
        <span>Asked <?php echo esc_html( human_time_diff( get_the_time( 'U' ) ) ); ?> ago</span>
      </div>

      <div class="qa-actions">
        <p><?php esc_html_e( 'Have a follow-up question?', 'compelling-evidence' ); ?></p>
        <a href="<?php echo esc_url( home_url( '/ask-a-question/' ) ); ?>" class="btn-ghost">
          Ask Another Question
        </a>
      </div>
    </footer>

  </article>

</main>

<?php get_footer(); ?>
