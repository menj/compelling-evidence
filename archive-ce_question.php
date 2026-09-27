<?php
/**
 * Template Name: Q&A Archive
 * Description: Public display of answered/published questions
 */
get_header(); ?>

<main class="ce-page ce-qa-archive" id="main">

  <header class="page-hero qa-hero">
    <div class="page-hero-inner">
      <p class="section-label">Community</p>
      <h1>Questions & Answers</h1>
      <p class="hero-sub">Thoughtful responses to questions from our readers.</p>
    </div>
  </header>

  <?php /* Item 14 — Q&A archive editorial framing.
            Explains how the Q&A surface works, what gets answered, the
            editorial process, and the response timeline. Sits between
            hero and the question list. */ ?>
  <aside class="qa-archive-intro" aria-label="<?php esc_attr_e( 'About this Q&A surface', 'compelling-evidence' ); ?>">
    <div class="qa-archive-intro-inner">
      <p>
        <?php esc_html_e( 'These are questions readers have submitted, with answers prepared by the editorial team. Some are factual queries with relatively settled answers. Others are harder — questions where the evidence is contested, where reasonable people disagree, or where the right response takes more than a paragraph. Both kinds appear here.', 'compelling-evidence' ); ?>
      </p>
      <p>
        <?php esc_html_e( 'Every published Q&A has been reviewed before going live. Questions are accepted from anyone, but the published surface is curated: we publish what we think will help future readers — questions that are either commonly asked, genuinely difficult, or based on a confusion worth addressing in detail. Submissions that overlap heavily with existing articles are usually pointed to those articles rather than answered separately.', 'compelling-evidence' ); ?>
      </p>
      <p>
        <?php esc_html_e( 'Response time varies. Simple factual clarifications usually go up within a few days. Substantive questions — the kind that require checking sources, weighing competing scholarly views, or working through an argument carefully — take longer, and we would rather take the time than publish something half-considered. If a question is urgent or personal, please reach out directly rather than waiting for the queue.', 'compelling-evidence' ); ?>
      </p>
      <p>
        <?php esc_html_e( 'Browse by topic in the sidebar, or use the form below to submit your own.', 'compelling-evidence' ); ?>
      </p>
    </div>
  </aside>

  <div class="qa-body">

    <div class="qa-list-wrap">
      <?php
      // Only show published questions (status = 'published')
      $published_term = get_term_by( 'slug', 'published', 'ce_qstatus' );

      $query = new WP_Query([
        'post_type' => 'ce_question',
        'posts_per_page' => 20,
        'paged' => get_query_var( 'paged' ) ?: 1,
        'tax_query' => [
          [
            'taxonomy' => 'ce_qstatus',
            'field'    => 'slug',
            'terms'    => 'published',
          ],
        ],
        'orderby' => 'date',
        'order' => 'DESC',
      ]);

      if ( $query->have_posts() ) :
        while ( $query->have_posts() ) : $query->the_post();
          $topic = get_post_meta( get_the_ID(), '_ce_question_topic', true );
          // Run the post content through the_content filter so shortcodes,
          // oEmbed, paragraph wrapping, and other content filters fire.
          // wp_kses_post() then ensures the output is whitelisted HTML —
          // protecting against any unfiltered junk that might exist in older
          // posts created before content filters were active.
          $raw_answer = get_the_content();
          if ( empty( trim( $raw_answer ) ) || trim( $raw_answer ) === trim( get_the_title() ) ) {
              $answer = '<p class="qa-pending">An answer is being prepared for this question.</p>';
          } else {
              $answer = wp_kses_post( apply_filters( 'the_content', $raw_answer ) );
          }
      ?>
        <article class="qa-item">
          <div class="qa-question">
            <span class="qa-label">Question</span>
            <h2 class="qa-title"><?php the_title(); ?></h2>
            <?php if ( $topic ) : ?>
              <span class="qa-topic"><?php echo esc_html( $topic ); ?></span>
            <?php endif; ?>
          </div>
          <div class="qa-answer">
            <span class="qa-label qa-label--answer">Answer</span>
            <div class="qa-content">
              <?php echo $answer; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post() applied above, or a static string. ?>
            </div>
          </div>
        </article>
      <?php
        endwhile;

        // Pagination
        if ( $query->max_num_pages > 1 ) :
      ?>
        <div class="qa-pagination">
          <?php
            echo wp_kses_post( paginate_links([
              'total'        => $query->max_num_pages,
              'current'      => max( 1, get_query_var( 'paged' ) ),
              'format'       => '?paged=%#%',
              'prev_text'    => '← Previous',
              'next_text'    => 'Next →',
            ] ) );
          ?>
        </div>
      <?php
        endif;

        wp_reset_postdata();

      else :
      ?>
        <div class="qa-empty">
          <svg width="64" height="64" fill="none" stroke="#0cd4e0" stroke-width="1.5" viewBox="0 0 24 24">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
          </svg>
          <h3>No published answers yet</h3>
          <p>We're working on responses to community questions. Check back soon!</p>
          <a href="<?php echo esc_url( home_url( '/ask-a-question/' ) ); ?>" class="btn-primary">Ask Your Question</a>
        </div>
      <?php endif; ?>
    </div>

    <aside class="qa-sidebar">
      <div class="sidebar-widget">
        <h3 class="widget-title">Have a question?</h3>
        <p style="color:var(--muted);font-size:.9rem;line-height:1.7;margin-bottom:1rem;">
          Can't find an answer on the site? Submit your question and we'll do our best to respond.
        </p>
        <a href="<?php echo esc_url( home_url( '/ask-a-question/' ) ); ?>" class="btn-primary" style="width:100%;text-align:center;">
          Ask a Question
        </a>
      </div>

      <div class="sidebar-widget">
        <h3 class="widget-title">Browse by Topic</h3>
        <ul class="qa-topic-list">
          <?php
          $canonical_topics = [
            'Does God Exist?', 'The Problem of Evil', 'Ethics Without God?',
            'Science & Evidence', 'The Quran & Its Sources',
            'History, Context & Comparison', 'Divine Justice & Fairness',
            'Islamic Practice & Ritual', 'Rights & Freedom',
            'The Inner Journey', 'Revelation & Meaning',
          ];
          $all_terms = get_terms(['taxonomy' => 'ce_topic', 'hide_empty' => false]);
          $term_map = [];
          foreach ( $all_terms as $t ) { $term_map[$t->name] = $t; }
          $topics = array_filter( array_map( fn($n) => $term_map[$n] ?? null, $canonical_topics ) );

          // Counts per topic name in one aggregated query rather than 11 WP_Query
          // instantiations. Joins ce_question posts to the published-status term
          // and groups by the _ce_question_topic post-meta value.
          global $wpdb;
          $counts_raw = $wpdb->get_results( "
              SELECT pm.meta_value AS topic_name, COUNT(*) AS qcount
              FROM {$wpdb->posts} p
              INNER JOIN {$wpdb->postmeta} pm
                  ON pm.post_id = p.ID AND pm.meta_key = '_ce_question_topic'
              INNER JOIN {$wpdb->term_relationships} tr
                  ON tr.object_id = p.ID
              INNER JOIN {$wpdb->term_taxonomy} tt
                  ON tt.term_taxonomy_id = tr.term_taxonomy_id AND tt.taxonomy = 'ce_qstatus'
              INNER JOIN {$wpdb->terms} t
                  ON t.term_id = tt.term_id AND t.slug = 'published'
              WHERE p.post_type = 'ce_question'
                AND p.post_status = 'publish'
              GROUP BY pm.meta_value
          ", ARRAY_A );
          $count_map = [];
          foreach ( $counts_raw as $row ) {
              $count_map[ $row['topic_name'] ] = (int) $row['qcount'];
          }

          foreach ( $topics as $topic ) :
            $count = $count_map[ $topic->name ] ?? 0;
            $term_link = get_term_link( $topic );
            $href = is_wp_error( $term_link ) ? '#' : $term_link;
          ?>
            <li<?php echo ( $count === 0 ) ? ' style="opacity:0.45"' : ''; ?>>
              <a href="<?php echo esc_url( $href ); ?>"
                 title="Browse <?php echo esc_attr( $topic->name ); ?> articles">
                <?php echo esc_html( $topic->name ); ?>
                <span class="qa-count"><?php echo intval( $count ); ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </aside>

  </div>

</main>

<?php get_footer(); ?>
