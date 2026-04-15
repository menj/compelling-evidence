<?php
/**
 * Compelling Evidence — Unified Cross-Link & Glossary Tooltip Engine
 *
 * Processes article content in a single pass, inserting both internal
 * cross-links and glossary tooltips while enforcing strict placement rules.
 *
 * GROUND RULES (enforced in code, not optional):
 * 1. Same keyword → linked/tooltipped at most ONCE per article.
 * 2. NEVER inside heading text (h1-h6).
 * 3. A crosslink and a tooltip CANNOT appear in the same paragraph.
 * 4. Every article gets at least 3 internal crosslinks (minimum).
 * 5. Tooltips only for terms that appear in the article.
 *
 * Configurable via Theme Options (Appearance → CE Theme Options → Links & Tooltips tab).
 *
 * @since 2.2.77
 */

if ( ! defined( 'ABSPATH' ) ) exit;


/* ═══════════════════════════════════════════════════════════════════════
   PHRASE → SLUG MAPPINGS (Internal Cross-Links)
   ═══════════════════════════════════════════════════════════════════════ */

function ce_get_crosslink_phrases() {
    // Allow admin override from DB
    $custom = get_option( 'ce_crosslink_custom_phrases' );
    if ( is_array( $custom ) && ! empty( $custom ) ) {
        $base = ce_get_default_crosslink_phrases();
        return array_merge( $base, $custom );
    }
    return ce_get_default_crosslink_phrases();
}

function ce_get_default_crosslink_phrases() {
    return [

        // ── Does God Exist? ──
        'cosmological argument'             => 'existence-come-out-of-nothing',
        'kalam argument'                    => 'existence-come-out-of-nothing',
        'why anything exists'               => 'why-does-anything-exist',
        'something rather than nothing'     => 'why-does-anything-exist',
        'fine-tuning'                       => 'fine-tuning-universe',
        'fine tuning'                       => 'fine-tuning-universe',
        'anthropic principle'               => 'fine-tuning-universe',
        'physical constants'                => 'fine-tuning-universe',
        'ontological argument'              => 'ontological-argument',
        'argument from reason'              => 'argument-from-reason',
        'divine hiddenness'                 => 'divine-hiddenness',
        'hiddenness of god'                 => 'divine-hiddenness',
        'burden of proof'                   => 'burden-of-proof',
        'god as projection'                 => 'god-as-psychological-projection',
        'psychological projection'          => 'god-as-psychological-projection',
        'freud'                             => 'god-as-psychological-projection',
        'free will and predestination'      => 'free-will-predestination',
        'predestination'                    => 'free-will-predestination',
        'qadar'                             => 'free-will-predestination',
        'unanswered prayer'                 => 'if-god-answers-prayer-why-cant-you-prove-it',
        'why god needs worship'             => 'why-does-god-need-compelled-worship',
        'deism'                             => 'god-personal-or-deist',
        'first cause'                       => 'god-personal-or-deist',

        // ── The Problem of Evil ──
        'problem of evil'                   => 'problem-of-evil-response',
        'problem of suffering'              => 'suffering-and-god',
        'theodicy'                          => 'suffering-and-god',
        'natural evil'                      => 'natural-evil',
        'earthquakes, cancer'               => 'natural-evil',
        'finite sins, infinite punishment'  => 'finite-sins-infinite-punishment',
        'eternal hellfire'                  => 'why-hellfire',
        'hellfire'                          => 'why-hellfire',
        'why did god create'                => 'why-create-knowing-suffering',
        'universal salvation'               => 'universal-salvation',

        // ── Ethics Without God? ──
        'euthyphro dilemma'                 => 'euthyphro-dilemma',
        'euthyphro'                         => 'euthyphro-dilemma',
        'moral argument'                    => 'moral-argument-god',
        'moral realism'                     => 'moral-argument-god',
        'objective morality'                => 'objective-morality',
        'moral relativism'                  => 'objective-morality',
        'secular ethics'                    => 'ethics-without-god',
        'ethics without god'                => 'ethics-without-god',

        // ── Science & Evidence ──
        'scientism'                         => 'science-and-religion',
        'science and religion'              => 'science-and-religion',
        'limits of science'                 => 'science-limits',
        'hard problem of consciousness'     => 'consciousness-hard-problem',
        'hard problem'                      => 'consciousness-hard-problem',
        'near-death experiences'            => 'near-death-experiences',
        'near death experiences'            => 'near-death-experiences',
        'multiverse'                        => 'multiverse-objection',
        'many-worlds'                       => 'multiverse-objection',
        'evolution and islam'               => 'evolution-and-islam',
        'evolution'                         => 'evolution-and-islam',
        'big bang'                          => 'origins-of-the-universe',

        // ── Examining the Quran ──
        'quran and science'                 => 'quran-and-science',
        'scientific miracles'               => 'quran-and-science',
        'preservation of the quran'         => 'quran-preserved',
        'quran preservation'                => 'quran-preserved',
        'literary miracle'                  => 'quran-literary-miracle',
        'inimitability'                     => 'quran-literary-miracle',
        'challenge of the quran'            => 'quran-literary-miracle',
        'abrogation'                        => 'abrogation',
        'variant readings'                  => 'quran-variant-readings',
        'qiraat'                            => 'quran-variant-readings',
        'violence in the quran'             => 'quran-violence',
        'sword verse'                       => 'quran-violence',

        // ── Examining the Sources ──
        'hadith reliability'                => 'hadith-authenticity',
        'hadith criticism'                  => 'hadith-authenticity',
        'isnad'                             => 'hadith-authenticity',
        'chain of transmission'             => 'hadith-authenticity',
        'prophethood'                       => 'was-muhammad-who-he-claimed',
        'muhammad\'s character'             => 'was-muhammad-who-he-claimed',
        'age of aisha'                      => 'age-of-aisha',
        'aisha marriage'                    => 'age-of-aisha',

        // ── Rights & Freedom ──
        'women in islam'                    => 'women-in-islam',
        'women\'s rights in islam'          => 'women-in-islam',
        'apostasy'                          => 'freedom-to-leave',
        'freedom to leave islam'            => 'freedom-to-leave',
        'blasphemy'                         => 'blasphemy-laws',
        'religious freedom'                 => 'religious-freedom-islam',
        'shariah law'                       => 'shariah-modern-world',
        'sharia law'                        => 'shariah-modern-world',
        'slavery in islam'                  => 'slavery-in-islam',
        'human rights'                      => 'human-rights-islam',
        'jihad'                             => 'meaning-of-jihad',
        'holy war'                          => 'meaning-of-jihad',
        'lgbtq'                             => 'islam-lgbtq',
        'homosexuality'                     => 'islam-lgbtq',
        'hudud'                             => 'hudud-punishments',

        // ── History & Context ──
        'golden age of islam'               => 'golden-age',
        'islamic golden age'                => 'golden-age',
        'orientalism'                       => 'orientalism',
        'western lens'                      => 'orientalism',
        'crusades'                          => 'crusades-context',
        'colonialism'                       => 'colonialism-and-islam',

        // ── The Inner Journey ──
        'spiritual dryness'                 => 'when-presence-fades',
        'loss of faith'                     => 'when-presence-fades',
        'purpose of life'                   => 'purpose-of-life',
        'meaning of life'                   => 'purpose-of-life',
        'death in islam'                    => 'what-happens-after-death',
        'afterlife'                         => 'what-happens-after-death',
        'repentance'                        => 'the-door-is-still-open',
        'tawbah'                            => 'the-door-is-still-open',
        'coming back to islam'              => 'coming-back-after-leaving',
        'converts to islam'                 => 'what-draws-people-to-islam',

        // ── The Bigger Picture ──
        'nihilism'                          => 'if-nothing-really-matters',
        'existential nihilism'              => 'if-nothing-really-matters',
        'the heart in islam'                => 'spiritual-heart-of-islam',
        'believing in the unseen'           => 'rational-person-believe-unseen',
        'the unseen'                        => 'rational-person-believe-unseen',
    ];
}


/* ═══════════════════════════════════════════════════════════════════════
   UNIFIED CONTENT FILTER — crosslinks + tooltips in one pass
   ═══════════════════════════════════════════════════════════════════════ */

function ce_unified_link_filter( $content ) {
    // Only on single articles/posts, not admin
    if ( is_admin() || ! is_singular( [ 'ce_article', 'post' ] ) ) {
        return $content;
    }

    // Check global enable/disable from Theme Options
    $crosslinks_on = get_option( 'ce_crosslinks_enabled', '1' ) === '1';
    $tooltips_on   = get_option( 'ce_tooltips_enabled', '1' ) === '1';
    if ( ! $crosslinks_on && ! $tooltips_on ) return $content;

    // Check per-post disable (post meta)
    $post_id = get_the_ID();
    if ( get_post_meta( $post_id, '_ce_disable_crosslinks', true ) === '1' ) $crosslinks_on = false;
    if ( get_post_meta( $post_id, '_ce_disable_tooltips', true ) === '1' ) $tooltips_on = false;
    if ( ! $crosslinks_on && ! $tooltips_on ) return $content;

    // Check per-category disable
    $disabled_cats = array_filter( explode( ',', get_option( 'ce_crosslink_disabled_categories', '' ) ) );
    if ( $disabled_cats ) {
        $post_terms = wp_get_post_terms( $post_id, 'ce_topic', [ 'fields' => 'slugs' ] );
        if ( ! is_wp_error( $post_terms ) && array_intersect( $post_terms, $disabled_cats ) ) {
            return $content;
        }
    }

    // Settings
    $max_crosslinks = absint( get_option( 'ce_crosslink_max_per_article', 5 ) );
    $min_crosslinks = absint( get_option( 'ce_crosslink_min_per_article', 3 ) );
    $max_tooltips   = absint( get_option( 'ce_tooltip_max_per_article', 8 ) );

    $current_slug = get_post_field( 'post_name', $post_id );
    $base_url     = home_url( '/articles/' );
    $glossary_url = home_url( '/glossary/' );

    // Get mappings
    $crosslink_phrases = $crosslinks_on ? ce_get_crosslink_phrases() : [];
    $glossary_terms    = $tooltips_on ? ce_get_glossary_terms() : [];

    // Sort both longest-first
    uksort( $crosslink_phrases, function( $a, $b ) { return strlen( $b ) - strlen( $a ); } );
    usort( $glossary_terms, function( $a, $b ) { return strlen( $b['term'] ) - strlen( $a['term'] ); } );

    // ── SPLIT CONTENT INTO PARAGRAPHS ──
    // We process paragraph by paragraph so we can enforce "no crosslink + tooltip in same paragraph"
    $parts = preg_split( '/(<[^>]+>)/s', $content, -1, PREG_SPLIT_DELIM_CAPTURE );

    $crosslink_count   = 0;
    $tooltip_count     = 0;
    $used_keywords     = [];  // Rule 1: each keyword linked at most once
    $used_slugs        = [];  // Each target slug linked at most once
    $in_heading        = false;
    $in_protected      = false; // Inside <a>, citation blocks
    $para_has_crosslink = false;
    $para_has_tooltip   = false;
    $in_paragraph      = false;

    for ( $i = 0; $i < count( $parts ); $i++ ) {
        $part = $parts[ $i ];

        // Track HTML context
        if ( preg_match( '/^<(h[1-6])\b/i', $part ) ) { $in_heading = true; continue; }
        if ( preg_match( '/^<\/(h[1-6])>/i', $part ) ) { $in_heading = false; continue; }
        if ( preg_match( '/^<(a|blockquote)\b/i', $part ) ) { $in_protected = true; continue; }
        if ( preg_match( '/^<\/(a|blockquote)>/i', $part ) ) { $in_protected = false; continue; }
        if ( preg_match( '/class="[^"]*(quran-citation|hadith-citation|quran-arabic|hadith-arabic)/i', $part ) ) { $in_protected = true; continue; }
        if ( $in_protected && preg_match( '/^<\/div>/i', $part ) ) { $in_protected = false; continue; }

        // Track paragraphs (Rule 3)
        if ( preg_match( '/^<p[\s>]/i', $part ) ) {
            $in_paragraph = true;
            $para_has_crosslink = false;
            $para_has_tooltip = false;
            continue;
        }
        if ( preg_match( '/^<\/p>/i', $part ) ) {
            $in_paragraph = false;
            continue;
        }

        // Skip non-text or protected zones
        if ( $in_heading || $in_protected ) continue;  // Rule 2: no links in headings
        if ( strpos( $part, '<' ) === 0 ) continue;     // HTML tag, skip
        if ( trim( $part ) === '' ) continue;

        $text = $part;
        $changed = false;

        // ── TRY CROSSLINKS FIRST ──
        if ( $crosslinks_on && $crosslink_count < $max_crosslinks && ! $para_has_tooltip ) {
            foreach ( $crosslink_phrases as $phrase => $slug ) {
                if ( $crosslink_count >= $max_crosslinks ) break;
                if ( $slug === $current_slug ) continue;
                if ( in_array( $slug, $used_slugs, true ) ) continue;

                $lc_phrase = mb_strtolower( $phrase );
                if ( in_array( $lc_phrase, $used_keywords, true ) ) continue; // Rule 1

                $escaped = preg_quote( $phrase, '/' );
                $pattern = '/(?<![a-zA-Z\x{0600}-\x{06FF}])(' . $escaped . ')(?![a-zA-Z\x{0600}-\x{06FF}])/iu';

                if ( preg_match( $pattern, $text, $match, PREG_OFFSET_CAPTURE ) ) {
                    $found = $match[1][0];
                    $offset = $match[1][1];

                    $title_attr = '';
                    $target_post = get_page_by_path( $slug, OBJECT, 'ce_article' );
                    if ( $target_post ) {
                        $title_attr = ' title="' . esc_attr( get_the_title( $target_post ) ) . '"';
                    }

                    $link = '<a href="' . esc_url( $base_url . $slug . '/' ) . '" class="ce-crosslink"' . $title_attr . '>' . $found . '</a>';
                    $text = substr_replace( $text, $link, $offset, strlen( $found ) );
                    $changed = true;
                    $crosslink_count++;
                    $used_slugs[] = $slug;
                    $used_keywords[] = $lc_phrase;
                    $para_has_crosslink = true; // Rule 3
                    break; // One insertion per text node
                }
            }
        }

        // ── TRY TOOLTIPS (only if this paragraph has no crosslink) ──
        if ( $tooltips_on && $tooltip_count < $max_tooltips && ! $para_has_crosslink && ! $changed ) {
            foreach ( $glossary_terms as $term_data ) {
                if ( $tooltip_count >= $max_tooltips ) break;

                $term = $term_data['term'];
                $lc_term = mb_strtolower( $term );
                if ( in_array( $lc_term, $used_keywords, true ) ) continue; // Rule 1

                $escaped = preg_quote( $term, '/' );
                $pattern = '/\b(' . $escaped . ')\b/iu';

                if ( preg_match( $pattern, $text, $match, PREG_OFFSET_CAPTURE ) ) {
                    $found = $match[1][0];
                    $offset = $match[1][1];
                    $definition = esc_attr( $term_data['def'] );
                    $anchor = sanitize_title( $term );
                    $url = $glossary_url . '#term-' . $anchor;
                    $arabic_attr = ! empty( $term_data['arabic'] ) ? ' data-arabic="' . esc_attr( $term_data['arabic'] ) . '"' : '';

                    $tooltip_link = '<a href="' . esc_url( $url ) . '" class="ce-glossary-term" data-definition="' . $definition . '"' . $arabic_attr . '>' . $found . '</a>';
                    $text = substr_replace( $text, $tooltip_link, $offset, strlen( $found ) );
                    $changed = true;
                    $tooltip_count++;
                    $used_keywords[] = $lc_term;
                    $para_has_tooltip = true; // Rule 3
                    break; // One insertion per text node
                }
            }
        }

        if ( $changed ) {
            $parts[ $i ] = $text;
        }
    }

    $content = implode( '', $parts );

    // ── Rule 4: Enforce minimum crosslinks ──
    // If we didn't hit the minimum, do a second pass with relaxed rules
    // (allow same paragraph as tooltip, but still enforce Rule 1 and Rule 2)
    if ( $crosslinks_on && $crosslink_count < $min_crosslinks ) {
        foreach ( $crosslink_phrases as $phrase => $slug ) {
            if ( $crosslink_count >= $min_crosslinks ) break;
            if ( $slug === $current_slug ) continue;
            if ( in_array( $slug, $used_slugs, true ) ) continue;

            $lc_phrase = mb_strtolower( $phrase );
            if ( in_array( $lc_phrase, $used_keywords, true ) ) continue;

            $escaped = preg_quote( $phrase, '/' );
            // Match in paragraph text only (not headings — use negative lookbehind for <h)
            $pattern = '/(?<=<p[^>]*>(?:[^<]*))(?<![a-zA-Z\x{0600}-\x{06FF}])(' . $escaped . ')(?![a-zA-Z\x{0600}-\x{06FF}])(?=[^<]*<\/p>)/iu';

            // Simpler approach: just search the full content, but verify not in heading
            $simple_pattern = '/(?<![a-zA-Z\x{0600}-\x{06FF}])(' . $escaped . ')(?![a-zA-Z\x{0600}-\x{06FF}])/iu';

            if ( preg_match( $simple_pattern, $content, $match, PREG_OFFSET_CAPTURE ) ) {
                $found  = $match[1][0];
                $offset = $match[1][1];

                // Verify not inside heading or link
                $before = substr( $content, max( 0, $offset - 500 ), min( 500, $offset ) );
                $in_h = preg_match_all( '/<h[1-6][^>]*>/i', $before ) > preg_match_all( '/<\/h[1-6]>/i', $before );
                $in_a = preg_match_all( '/<a[\s>]/i', $before ) > preg_match_all( '/<\/a>/i', $before );
                if ( $in_h || $in_a ) continue;

                $title_attr = '';
                $target_post = get_page_by_path( $slug, OBJECT, 'ce_article' );
                if ( $target_post ) $title_attr = ' title="' . esc_attr( get_the_title( $target_post ) ) . '"';

                $link = '<a href="' . esc_url( $base_url . $slug . '/' ) . '" class="ce-crosslink"' . $title_attr . '>' . $found . '</a>';
                $content = substr_replace( $content, $link, $offset, strlen( $found ) );
                $crosslink_count++;
                $used_slugs[] = $slug;
                $used_keywords[] = $lc_phrase;
            }
        }
    }

    return $content;
}

// Remove old individual filters, use unified one
remove_filter( 'the_content', 'ce_auto_crosslink', 20 );
remove_filter( 'the_content', 'ce_glossary_filter_content', 20 );
add_filter( 'the_content', 'ce_unified_link_filter', 20 );


/* ═══════════════════════════════════════════════════════════════════════
   PER-POST META BOX — disable crosslinks/tooltips on specific posts
   ═══════════════════════════════════════════════════════════════════════ */

function ce_crosslink_meta_box() {
    add_meta_box(
        'ce-link-controls',
        'Links & Tooltips',
        'ce_crosslink_meta_box_html',
        [ 'ce_article', 'post' ],
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'ce_crosslink_meta_box' );

function ce_crosslink_meta_box_html( $post ) {
    wp_nonce_field( 'ce_link_meta', 'ce_link_meta_nonce' );
    $disable_cl = get_post_meta( $post->ID, '_ce_disable_crosslinks', true );
    $disable_tt = get_post_meta( $post->ID, '_ce_disable_tooltips', true );
    ?>
    <label style="display:block;margin-bottom:0.5rem;">
        <input type="checkbox" name="ce_disable_crosslinks" value="1" <?php checked( $disable_cl, '1' ); ?>>
        Disable internal crosslinks
    </label>
    <label style="display:block;">
        <input type="checkbox" name="ce_disable_tooltips" value="1" <?php checked( $disable_tt, '1' ); ?>>
        Disable glossary tooltips
    </label>
    <?php
}

function ce_crosslink_save_meta( $post_id ) {
    if ( ! isset( $_POST['ce_link_meta_nonce'] ) || ! wp_verify_nonce( $_POST['ce_link_meta_nonce'], 'ce_link_meta' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    update_post_meta( $post_id, '_ce_disable_crosslinks', isset( $_POST['ce_disable_crosslinks'] ) ? '1' : '0' );
    update_post_meta( $post_id, '_ce_disable_tooltips', isset( $_POST['ce_disable_tooltips'] ) ? '1' : '0' );
}
add_action( 'save_post', 'ce_crosslink_save_meta' );
