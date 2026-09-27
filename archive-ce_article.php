<?php
/**
 * Article Archive Template — /articles/
 * Organised by topic sections in logical reading order.
 *
 * @since 1.8.1
 */
get_header();

// Canonical topic display order — mirrors the intellectual journey.
// Topics in the DB that are NOT listed here are appended automatically
// so that articles created via WP Admin under new topics always appear.
$canonical_order = [
    'Does God Exist?',
    'The Problem of Evil',
    'Ethics Without God?',
    'Science & Evidence',
    'The Quran & Its Sources',
    'History, Context & Comparison',
    'Divine Justice & Fairness',
    'Islamic Practice & Ritual',
    'Rights & Freedom',
    'The Inner Journey',
    'Revelation & Meaning',
];

$all_terms = get_terms(['taxonomy' => 'ce_topic', 'hide_empty' => true]);
$term_map = [];
if ( $all_terms && ! is_wp_error( $all_terms ) ) {
    foreach ( $all_terms as $t ) {
        $term_map[ $t->name ] = $t;
    }
}

// Append any DB topics that are not in the canonical list so they are
// never silently swallowed — important for topics created directly in
// WP Admin → Articles → Topics.
$extra_topics = array_diff( array_keys( $term_map ), $canonical_order );
$topic_order  = array_merge( $canonical_order, array_values( $extra_topics ) );

$is_filtered = is_tax('ce_topic');
$active_term = $is_filtered ? get_queried_object() : null;
?>

<main id="main" role="main">

<div class="articles-archive">

  <div class="archive-hero">
    <?php if ( $is_filtered && $active_term ) : /* Topic pages get their own heading (2.6.31). */ ?>
    <span class="archive-hero-eyebrow"><?php esc_html_e('Topic', 'compelling-evidence'); ?></span>
    <h1><?php echo esc_html( $active_term->name ); ?></h1>
    <p><?php echo ce_topic_description( $active_term ) ? esc_html( ce_topic_description( $active_term ) ) : esc_html( sprintf( _n( '%d article on this question, weighing the strongest objections against the evidence.', '%d articles on this question, weighing the strongest objections against the evidence.', (int) $active_term->count, 'compelling-evidence' ), (int) $active_term->count ) ); ?></p>
    <?php else : ?>
    <span class="archive-hero-eyebrow"><?php esc_html_e('Articles', 'compelling-evidence'); ?></span>
    <h1><?php esc_html_e('The Evidence, Examined', 'compelling-evidence'); ?></h1>
    <p><?php esc_html_e('Every serious objection. Every core argument. Written for the honest inquirer.', 'compelling-evidence'); ?></p>
    <?php endif; ?>
    <?php
    $total_count = wp_count_posts('ce_article');
    $total_pub   = isset($total_count->publish) ? $total_count->publish : 0;
    ?>
    <span class="ah-count"><?php echo (int) $total_pub; ?> articles across <?php echo (int) count($term_map); ?> topics</span>
  </div>

  <?php /* Item 6 — Editorial framing for crawlers, AI overviews, and curious readers.
            ~250 words placing the collection in context: what it covers, the editorial
            method, and who it's for. Hidden on smaller viewports via CSS to preserve
            the existing scannable archive layout for mobile users — the indexable
            text remains in the DOM regardless of viewport. */ ?>
  <aside class="archive-intro-context" aria-label="<?php esc_attr_e( 'About this archive', 'compelling-evidence' ); ?>">
    <p>
      <?php esc_html_e( 'These articles examine the questions that actually drive doubt and inquiry — about God, evidence, religion, and what the available evidence supports. Each one is an attempt to take a serious objection seriously: to state the strongest version of the challenge, then weigh it against the evidence in plain prose, without polemic or theatrical certainty.', 'compelling-evidence' ); ?>
    </p>
    <p>
      <?php esc_html_e( 'The collection is organised across eleven investigative topics: from the existence of God and the problem of evil through to the historical record of early Islam, the preservation of the Quran, classical responses to apostasy, the science of fine-tuning, and questions of justice and freedom. Each article is self-contained — read in any order — and built around a single clear question that the headline names directly.', 'compelling-evidence' ); ?>
    </p>
    <p>
      <?php esc_html_e( 'The audience is the honest inquirer rather than the already-convinced. That has consequences for the writing: assumptions are stated rather than smuggled, contested points are flagged as contested, and arguments aim for what a careful sceptic would accept rather than what a sympathetic reader would let pass. Where the case is strong it is named strong. Where it is weak it is named weak. Where it is genuinely undecided, that too is named.', 'compelling-evidence' ); ?>
    </p>
    <p>
      <?php esc_html_e( 'New articles appear regularly. Use the topic filter below to browse by theme, or follow the canonical reading order — the articles are sequenced for cumulative argument, not by publication date.', 'compelling-evidence' ); ?>
    </p>
  </aside>

  <!-- Topic filter -->
  <div class="topic-filter">
    <a href="<?php echo esc_url(get_post_type_archive_link('ce_article')); ?>"
       class="tf-link<?php echo !$is_filtered ? ' active' : ''; ?>">
      <?php esc_html_e('All', 'compelling-evidence'); ?>
    </a>
    <?php foreach ( $topic_order as $topic_name ):
      if ( ! isset( $term_map[ $topic_name ] ) ) continue;
      $t = $term_map[ $topic_name ];
      $active = ($active_term && $active_term->term_id === $t->term_id) ? ' active' : '';
      // Use anchor links on main archive, term links on filtered pages
      $tf_link = $is_filtered ? esc_url(get_term_link($t)) : '#topic-' . esc_attr($t->slug);
    ?>
      <a href="<?php echo esc_url( $tf_link ); ?>" class="tf-link<?php echo esc_attr( $active ); ?>">
        <?php echo esc_html($t->name); ?><span class="tf-count"><?php echo (int) $t->count; ?></span>
      </a>
    <?php endforeach; ?>
  </div>

  <?php
  $topic_descs = [
      'Does God Exist?'                => 'Cosmological, fine-tuning, and ontological arguments — examined from the ground up.',
      'The Problem of Evil'            => 'The strongest objection to theism, taken at full strength.',
      'Ethics Without God?'            => 'Where moral facts come from — and whether they need a foundation.',
      'Science & Evidence'             => 'Evolution, neuroscience, cosmology — what the evidence actually shows.',
      'The Quran & Its Sources'        => 'Preservation, classical sources, and literary claims — critically examined.',
      'History, Context & Comparison'  => 'Historical events, institutions, and competing claims — examined honestly.',
      'Divine Justice & Fairness'      => 'Divine justice, punishment, and fairness in the scheme of existence.',
      'Islamic Practice & Ritual'      => 'Prayer, fasting, ritual — the practices that look strange from the outside, explained.',
      'Rights & Freedom'               => 'Apostasy, gender, sexuality, slavery — the hardest questions about justice and freedom.',
      'The Inner Journey'              => 'For those carrying doubt — the emotional and intellectual reality of questioning.',
      'Revelation & Meaning'           => 'Purpose, meaning, competing claims — where the evidence points.',
  ];

  // One-line orientation for each subcategory header. Optional — any
  // subcategory whose name does not appear here renders without a description.
  $subcategory_descs = [
      'Does God Exist?' => [
          'The Core Arguments'              => 'Why the universe, reason, and reality point toward a Creator.',
          'Common Objections'               => 'Answering the standard sceptical and atheist replies.',
      ],
      'The Quran & Its Sources' => [
          'The Text & Its Transmission'     => 'How the Quran reached us, and what its literary form claims.',
          'Authorship & Prophethood'        => 'Who the Prophet was, and where the Quran came from.',
          'Difficult Passages & Doctrines'  => 'The verses that look harshest, and what they actually say.',
          'History & Hadith'                => 'Apostasy, early violence, and how the prophetic record was preserved.',
      ],
      'Science & Evidence' => [
          'What Science Can and Can\'t Do'   => 'The reach and the limits of empirical method.',
          'Cosmology & Origins'             => 'The Big Bang, fine-tuning, and the questions evolution does not answer.',
          'Mind & Experience'               => 'Consciousness, religious experience, and what the brain explains.',
      ],
      'Rights & Freedom' => [
          'Leaving Islam'                   => 'What the tradition actually says about apostasy and conscience.',
          'Gender, Body & Sexuality'        => 'The hardest questions about women, the body, and same-sex attraction.',
          'The Harder Accusations'          => 'Slavery, hellfire, and the charge that Islam is a system of fear.',
      ],
      'The Inner Journey' => [
          'Understanding Doubt'             => 'What doubt is, where it comes from, and what it asks of you.',
          'The Emotional Cost'              => 'Anger, hidden disbelief, religious trauma, and the weight of leaving.',
          'What Happens Next'               => 'The social aftermath, the algorithmic environment, and the path home.',
      ],
      'Revelation & Meaning' => [
          'Does God Speak?'                 => 'Whether God communicates, and how to evaluate competing claims.',
          'Why Islam?'                      => 'What draws people to Islam, and what it offers that others do not.',
          'Questions From Within'           => 'Doubt, belief, and the things only the believer asks.',
      ],
      'History, Context & Comparison' => [
          'Religion in History'             => 'What religion has done in the world, and what that proves.',
          "Islam's Intellectual Tradition"  => 'The reason, reform, and self-criticism Islam already produced.',
          'The Specific Charges'            => 'Concrete accusations against Muhammad, Muslims, and the way Islam spreads.',
      ],
      'The Problem of Evil' => [
          'The Problem in Theory'           => 'The classical objection: how a good God permits suffering.',
          'Hell, Punishment & Mercy'        => 'Eternal punishment, universal salvation, and the limits of divine mercy.',
          'Living With Suffering'           => 'Unanswered prayer and the personal experience of pain.',
      ],
      'Islamic Practice & Ritual' => [
          'Worship & Ritual'                => 'Prayer, fasting, the Kaaba, and the rules that look strange from outside.',
          'The Unseen World'                => 'Angels, jinn, the evil eye, and the realities Islam affirms beyond the senses.',
      ],
  ];

  // Subcategory structure for topics with enough articles to warrant grouping.
  // Slugs within each group are displayed in array order (matching menu_order).
  // Any article whose slug does not appear in any group falls through to the
  // flat list rendered below, so nothing is ever silently dropped.
  $subcategories = [
      'Does God Exist?' => [
          'The Core Arguments' => [
              'does-god-exist',
              'why-does-anything-exist',
              'existence-come-out-of-nothing',
              'fine-tuning-universe',
              'ontological-argument',
              'argument-from-reason',
              'god-personal-or-deist',
          ],
          'Common Objections' => [
              'divine-hiddenness',
              'burden-of-proof',
              'god-as-psychological-projection',
              'if-nothing-really-matters',
              'free-will-predestination',
          ],
      ],
      'The Quran & Its Sources' => [
          'The Text & Its Transmission' => [
              'quran-historical-reliability',
              'quran-literary-argument',
              'quran-variant-readings-qiraat',
              'quran-bible-stories',
              'islamic-dilemma-quran-and-bible',
              'reading-the-quran-for-the-first-time',
              'quran-in-arabic',
          ],
          'Authorship & Prophethood' => [
              'quran-written-by-humans',
              'was-muhammad-who-he-claimed-to-be',
              'inconsistent-revelations',
              'gharaniq-satanic-verses',
          ],
          'Difficult Passages & Doctrines' => [
              'mercy-harsh-passages',
              'sword-verse-jizya-9-5-9-29',
              'meccan-medinan-abrogation',
              'scientific-miracles-quran',
              'quran-cosmology-flat-earth-seven-heavens',
              'quran-creation-accounts',
              'moon-splitting',
          ],
          'History & Hadith' => [
              'hadith-authenticity',
              'hadith-reliability',
              'kill-him-who-changes-religion',
              'banu-qurayza-early-violence',
              'did-islam-spread-by-the-sword',
              'aisha-age-marriage',
          ],
      ],
      'Science & Evidence' => [
          'What Science Can and Can\'t Do' => [
              'science-limits',
              'science-and-religion',
              'god-of-gaps',
              'how-can-a-rational-person-believe-in-the-unseen',
          ],
          'Cosmology & Origins' => [
              'big-bang-creation',
              'multiverse-objection',
              'evolution-explains-design',
              'evolution-and-islam',
          ],
          'Mind & Experience' => [
              'consciousness-hard-problem',
              'religious-experience-neuroscience',
              'near-death-experiences',
          ],
      ],
      'Rights & Freedom' => [
          'Leaving Islam' => [
              'apostasy-and-freedom',
              'no-compulsion-in-religion',
              'apostasy-political-history',
              'apostasy-international-law',
              'post-muslim-identity',
          ],
          'Gender, Body & Sexuality' => [
              'women-in-islam',
              'quran-4-34-wife-beating',
              'islam-and-same-sex-attraction',
              'ritual-purity-wudu-menstruation',
          ],
          'The Harder Accusations' => [
              'slavery-in-islamic-sources',
              'do-good-non-muslims-go-to-hell',
              'islam-built-on-fear',
              'is-islam-a-cult',
              'is-islam-compatible-with-western-democracy',
          ],
      ],
      'The Inner Journey' => [
          'Understanding Doubt' => [
              'your-doubts-are-not-a-disease', 'faith-was-just-conditioning',
              'the-good-muslim-paradox', 'when-the-presence-fades',
              'left-because-of-specific-problems', 'did-your-heart-leave-before-your-head',
          ],
          'The Emotional Cost' => [
              'the-dual-life', 'anger-at-religion', 'the-anger-is-real',
              'religious-trauma', 'when-religion-was-imposed-not-discovered',
          ],
          'What Happens Next' => [
              'social-cost-of-leaving', 'how-muslims-leave-the-sociology',
              'scale-of-leaving', 'practising-without-belief',
              'the-algorithm-that-deconverted-you', 'coming-back-after-leaving',
          ],
      ],
      'Revelation & Meaning' => [
          'Does God Speak?' => [
              'does-god-communicate-with-humanity',
              'what-would-authentic-revelation-look-like',
              'how-do-we-evaluate-competing-claims-to-revelation',
          ],
          'Why Islam?' => [
              'why-islam-not-christianity',
              'what-draws-people-to-islam-today',
              'the-spiritual-heart-of-islam',
              'purpose-of-life',
              'what-does-islam-say-happens-after-death',
          ],
          'Questions From Within' => [
              'doubt-permitted-in-islam',
              'why-humans-believe-in-god',
              'shirk-unforgivable',
          ],
      ],
      'History, Context & Comparison' => [
          'Religion in History' => [
              'religion-cause-harm',
              'religion-is-political-control',
              'religion-of-your-birth',
          ],
          "Islam's Intellectual Tradition" => [
              'islam-and-science',
              'islam-and-enlightenment',
              'the-freethinkers-islam-produced',
              'the-islam-i-was-defending',
          ],
          'The Specific Charges' => [
              'muhammad-and-warfare',
              'honour-killings-culture-not-islam',
              'islam-prison-conversion',
              'is-islam-a-religion-of-peace-terrorism-data',
              'does-the-quran-teach-hatred-of-jews',
          ],
      ],
      'The Problem of Evil' => [
          'The Problem in Theory' => [
              'problem-of-evil',
              'natural-evil',
              'problem-of-evil-response',
              'why-create-knowing-suffering',
          ],
          'Hell, Punishment & Mercy' => [
              'why-hellfire',
              'universal-salvation',
              'finite-sins-infinite-punishment',
          ],
          'Living With Suffering' => [
              'unanswered-prayer',
              'suffering-and-god',
          ],
      ],
      'Islamic Practice & Ritual' => [
          'Worship & Ritual' => [
              'why-arabic-prayer',
              'does-god-need-our-prayers',
              'kaaba-idol-worship',
              'ramadan-fasting-purpose',
              'islam-too-many-rules',
          ],
          'The Unseen World' => [
              'angels-and-the-unseen',
              'islam-jinn-mental-illness',
              'evil-eye-islamic-view',
          ],
      ],
  ];

  if ( $is_filtered && $active_term ) {
      $show_topics = [ $active_term->name ];
  } else {
      $show_topics = $topic_order;
  }

  $article_counter = 0;
  $shown_ids = []; // prevent duplicates across sections

  foreach ( $show_topics as $topic_name ):
      if ( ! isset( $term_map[ $topic_name ] ) ) continue;
      $t = $term_map[ $topic_name ];

      $articles = get_posts([
          'post_type'      => 'ce_article',
          'post_status'    => 'publish',
          'posts_per_page' => -1,
          'orderby'        => 'menu_order',
          'order'          => 'ASC',
          'tax_query'      => [[
              'taxonomy' => 'ce_topic',
              'field'    => 'term_id',
              'terms'    => [ $t->term_id ],
          ]],
      ]);

      // Filter out articles already shown in a previous section (multi-topic articles)
      if ( ! $is_filtered ) {
          $articles = array_filter($articles, function($p) use (&$shown_ids) {
              return ! in_array($p->ID, $shown_ids, true);
          });
      }

      if ( empty($articles) ) continue;

      $desc = $topic_descs[ $topic_name ] ?? '';
      $count = count($articles);
      $show_initially = 8;
      $has_more = $count > $show_initially;
  ?>
    <section class="topic-section" id="topic-<?php echo esc_attr($t->slug); ?>">
      <div class="ts-header open" onclick="this.classList.toggle('open');this.nextElementSibling.classList.toggle('open');">
        <h2 class="ts-name"><?php echo esc_html($topic_name); ?></h2>
        <span class="ts-count"><?php echo (int) $count; ?> article<?php echo (int) $count > 1 ? 's' : ''; ?></span>
        <span class="ts-chevron"><svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9l6 6 6-6"/></svg></span>
      </div>
      <div class="ts-body open">
      <?php if ($desc): ?>
        <p class="ts-desc"><?php echo esc_html($desc); ?></p>
      <?php endif; ?>

      <?php
      $has_subs = isset($subcategories[$topic_name]);
      
      if ($has_subs) :
          // Collect all slugs explicitly listed in subgroups so we can catch
          // any articles that aren't assigned to a subgroup yet.
          $sub_slugs = $subcategories[$topic_name];
          $all_subcatted_slugs = array_merge(...array_values($sub_slugs));

          foreach ($sub_slugs as $sub_name => $sub_slug_list) :
      ?>
          <div class="ts-subcat">
            <h3 class="ts-subcat-name"><?php echo esc_html($sub_name); ?></h3>
            <?php
            $sub_desc = $subcategory_descs[$topic_name][$sub_name] ?? '';
            if ($sub_desc) : ?>
              <p class="ts-subcat-desc"><?php echo esc_html($sub_desc); ?></p>
            <?php endif; ?>
            <div class="ts-articles">
              <?php
              foreach ($articles as $post) :
                  if ( ! in_array($post->post_name, $sub_slug_list, true) ) continue;
                  setup_postdata($post);
                  $shown_ids[] = $post->ID;
                  $article_counter++;
                  $wc   = str_word_count( strip_tags( $post->post_content ) );
                  $mins = max(1, (int) ceil($wc / 200));
              ?>
                <a href="<?php echo esc_url(get_permalink($post)); ?>" class="ts-article">
                  <span class="ts-article-num"><?php echo (int) $article_counter; ?></span>
                  <div class="ts-article-body">
                    <div class="ts-article-title"><?php echo esc_html( get_the_title( $post ) ); ?></div>
                    <div class="ts-article-excerpt"><?php echo esc_html( ce_excerpt_chars( $post, 120 ) ); ?></div>
                  </div>
                  <span class="ts-article-meta"><?php echo (int) $mins; ?> min</span>
                </a>
              <?php endforeach; wp_reset_postdata(); ?>
            </div>
          </div>
      <?php endforeach; ?>

      <?php
      // Catch-all: render any articles not listed in any subgroup so nothing
      // is ever silently dropped when new articles are added to the topic.
      $uncategorised = array_filter($articles, function($p) use ($all_subcatted_slugs) {
          return ! in_array($p->post_name, $all_subcatted_slugs, true);
      });
      if ( ! empty($uncategorised) ) : ?>
          <div class="ts-subcat">
            <h3 class="ts-subcat-name"><?php esc_html_e('Further Reading', 'compelling-evidence'); ?></h3>
            <div class="ts-articles">
              <?php foreach ($uncategorised as $post) :
                  setup_postdata($post);
                  $shown_ids[] = $post->ID;
                  $article_counter++;
                  $wc   = str_word_count( strip_tags( $post->post_content ) );
                  $mins = max(1, (int) ceil($wc / 200));
              ?>
                <a href="<?php echo esc_url(get_permalink($post)); ?>" class="ts-article">
                  <span class="ts-article-num"><?php echo (int) $article_counter; ?></span>
                  <div class="ts-article-body">
                    <div class="ts-article-title"><?php echo esc_html( get_the_title( $post ) ); ?></div>
                    <div class="ts-article-excerpt"><?php echo esc_html( ce_excerpt_chars( $post, 120 ) ); ?></div>
                  </div>
                  <span class="ts-article-meta"><?php echo (int) $mins; ?> min</span>
                </a>
              <?php endforeach; wp_reset_postdata(); ?>
            </div>
          </div>
      <?php endif; ?>
      
      <?php else : ?>
      
      <div class="ts-articles">
        <?php
        $i = 0;
        foreach ( $articles as $post ):
            setup_postdata($post);
            $shown_ids[] = $post->ID;
            $i++;
            $article_counter++;
            $wc   = str_word_count( strip_tags( $post->post_content ) );
            $mins = max(1, (int) ceil($wc / 200));
            $extra_class = ($has_more && $i > $show_initially) ? ' ts-overflow' : '';
            $extra_style = ($has_more && $i > $show_initially) ? ' style="display:none;"' : '';
        ?>
          <a href="<?php echo esc_url( get_permalink( $post ) ); ?>" class="ts-article<?php echo esc_attr( $extra_class ); ?>"<?php echo $extra_style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- constant attribute string, hardcoded literal: ' style="display:none;"' or empty ?>>
            <span class="ts-article-num"><?php echo (int) $article_counter; ?></span>
            <div class="ts-article-body">
              <div class="ts-article-title"><?php echo esc_html( get_the_title( $post ) ); ?></div>
              <div class="ts-article-excerpt"><?php echo esc_html( ce_excerpt_chars( $post, 120 ) ); ?></div>
            </div>
            <span class="ts-article-meta"><?php echo (int) $mins; ?> min</span>
          </a>
        <?php endforeach; wp_reset_postdata(); ?>
      </div>
      
      <?php endif; ?>

      <?php if ($has_more): ?>
        <button class="ts-toggle-btn" onclick="this.previousElementSibling.querySelectorAll('.ts-overflow').forEach(function(el){el.style.display='';});this.style.display='none';">
          Show all <?php echo (int) $count; ?> articles
        </button>
      <?php endif; ?>
      </div><!-- /ts-body -->
    </section>
  <?php endforeach; ?>

  <div class="archive-footer">
    <p><?php esc_html_e('These arguments are developed in full in the personalised journey paths — tailored to where you actually are.', 'compelling-evidence'); ?></p>
    <a href="<?php echo esc_url(home_url('/quiz')); ?>" class="btn-teal">
      <?php esc_html_e('Find my path', 'compelling-evidence'); ?>
      <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
  </div>

</div>

</main><!-- /#main -->

<?php get_footer(); ?>
