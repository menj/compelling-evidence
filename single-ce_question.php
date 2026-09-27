<?php
/**
 * Single Question Template
 * Shows a question with its answer
 */
get_header();

// ── QAPage schema (JSON-LD) ──────────────────────────────────────────────────
// Emit only when the question has been editorially published. The visibility
// gate in inc/ce-question-cpt.php already 404s non-published Q&As to non-admins,
// but the gate runs at template_redirect — by the time this template loads,
// we're guaranteed visibility OR an admin preview. Schema, however, should
// only emit for the public version, so we re-check ce_qstatus here.
if ( have_posts() ) {
    rewind_posts();
    the_post();
    $qa_post_id  = get_the_ID();
    $qa_statuses = wp_get_object_terms( $qa_post_id, 'ce_qstatus', [ 'fields' => 'slugs' ] );
    $qa_is_published = ! is_wp_error( $qa_statuses ) && in_array( 'published', $qa_statuses, true );

    if ( $qa_is_published ) {
        // Run the answer through the_content filter so shortcodes, oEmbed, and
        // paragraph wrapping resolve before we strip and trim. The schema's
        // Answer.text field expects readable plain text or basic HTML.
        $qa_answer_html = apply_filters( 'the_content', get_the_content() );
        $qa_answer_text = wp_strip_all_tags( $qa_answer_html );
        $qa_answer_text = trim( preg_replace( '/\s+/u', ' ', (string) $qa_answer_text ) );

        // Author of the answer — the editorial team, represented by the
        // configured Person if Identity tab is populated, else the Organization.
        $qa_answer_author = function_exists( 'ce_get_author_person_schema' )
            ? ce_get_author_person_schema()
            : null;
        if ( ! $qa_answer_author ) {
            $qa_answer_author = [
                '@type' => 'Organization',
                'name'  => 'Compelling Evidence',
                'url'   => home_url( '/' ),
            ];
        }

        // Question.text vs Question.name: schema.org docs say `name` is the short
        // form (the question title) and `text` is the full body. We have the
        // title already; the body is the original submitter's question stored as
        // post_content before the answer was added. If post_content has been
        // overwritten with the answer, fall back to using the title only.
        $qa_question_text = get_the_title();

        // Article markup (since 2.6.31). This page previously used QAPage, which
        // Google reserves for pages where users can submit their own answers
        // (forums, support communities). Here the site answers a reader's
        // question once, which Google lists as an invalid QAPage use case.
        $qa_schema = [
            '@context'         => 'https://schema.org',
            '@type'            => 'Article',
            '@id'              => trailingslashit( get_permalink() ) . '#article',
            'headline'         => wp_strip_all_tags( get_the_title() ),
            'description'      => wp_trim_words( $qa_answer_text, 30, '…' ),
            'datePublished'    => get_the_date( 'c' ),
            'dateModified'     => get_the_modified_date( 'c' ),
            'author'           => $qa_answer_author,
            'publisher'        => [
                '@type' => 'Organization',
                'name'  => 'Compelling Evidence',
                'url'   => home_url( '/' ),
            ],
            'mainEntityOfPage' => get_permalink(),
            'image'            => ( function_exists( 'ce_get_share_image_url' ) ? ce_get_share_image_url( get_the_ID() ) : '' ) ?: get_stylesheet_directory_uri() . '/screenshot.png',
        ];

        // JSON_HEX_TAG/_AMP/_APOS/_QUOT prevent any quote or angle-bracket from
        // breaking out of the <script type="application/ld+json"> container
        // even if a question or answer contains those characters verbatim.
        $qa_json = wp_json_encode(
            $qa_schema,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
        );

        if ( false !== $qa_json ) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encoded with JSON_HEX_TAG; script-tag breakout impossible.
            echo "\n<script type=\"application/ld+json\">" . $qa_json . "</script>\n";
        }
    }
    rewind_posts();
}
?>

<main class="ce-page ce-qa-single" id="main">

  <article class="qa-single">

    <header class="qa-single-header">
      <a href="<?php echo esc_url( home_url( '/qa/' ) ); ?>" class="qa-back-link">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back to Q&A
      </a>

      <div class="qa-question-wrap">
        <span class="qa-label">Question</span>
        <h1 class="qa-title"><?php the_title(); ?></h1>
        <?php
        $topic = get_post_meta( get_the_ID(), '_ce_question_topic', true );
        if ( $topic ) : ?>
          <span class="qa-topic"><?php echo esc_html( $topic ); ?></span>
        <?php endif; ?>
      </div>
    </header>

    <div class="qa-answer-wrap">
      <span class="qa-label qa-label--answer">Answer</span>
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
        <p>Have a follow-up question?</p>
        <a href="<?php echo esc_url( home_url( '/ask-a-question/' ) ); ?>" class="btn-ghost">
          Ask Another Question
        </a>
      </div>
    </footer>

  </article>

</main>

<?php get_footer(); ?>
