<?php
/**
 * Compelling Evidence — Secondary Pages
 * About, Editorial Policy, Privacy Policy, Contact
 * Auto-created on theme activation.
 *
 * @since 2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function ce_create_secondary_pages() {
    $pages = [

        [
            'slug'    => 'about-compelling-evidence',
            'title'   => 'About This Site',
            'content' => '
<p class="article-lead">Compelling Evidence is a long-running attempt to take the strongest objections against Islam, and against belief in God in general, and weigh them honestly against what the available evidence supports. The work is written for the curious, the doubtful, and the unconvinced. It does not assume agreement, and it does not pretend to certainty that the evidence does not actually furnish.</p>

<h2>The premise</h2>

<p>Most material on contested religious questions falls into one of two camps. The first preaches to the convinced: comfortable, confident, allergic to genuine difficulty. The second weaponises difficulty against the other side without applying the same scrutiny to its own foundations. Neither serves the inquirer who actually wants to think a question through. The premise here is that careful argument, applied evenhandedly, has a better chance of arriving at something true than either confident assertion or rhetorical attack.</p>

<h2>The method</h2>

<p>Each article begins with a question, usually a hard one. The strongest version of the challenge is stated first, in the form a thoughtful sceptic would actually present it. Then the relevant evidence is laid out: historical sources, philosophical arguments, scientific findings, classical scholarly responses where they exist. Where the evidence cuts cleanly in one direction, that direction is named. Where the evidence is genuinely contested, the contestation is named. Where the evidence is thin, the thinness is named. Specific claims are tied to specific sources; rhetorical flourishes are kept to a minimum.</p>

<h2>What the work covers</h2>

<p>The articles are organised across eleven investigative topics. The existence of God: cosmological arguments, fine-tuning, ontological reasoning. The problem of evil: classical theodicies, modern objections, the evidential case from suffering. Ethics without God: whether the moral law can be grounded without theism, and what classical Islamic ethical thought brings to the question. Science and evidence: how religious claims interact with the empirical record, including the cosmological and biological details that recur in apologetic argument.</p>

<p>On the Islamic side specifically: the Quran and its sources, including its preservation history, literary form, internal structure, and engagement with prior scriptures. History, context, and comparison: situating early Islam within late antiquity, addressing the harder questions about the Prophet\'s life and the formation of the early community. Divine justice and fairness: the questions about hell, eternal punishment, predestination, and the moral architecture of the Islamic worldview. Islamic practice and ritual, examined for its rationale rather than presented as self-evident. Rights and freedom: the harder questions about religious liberty, apostasy, and how Islamic law has actually addressed these across the centuries. The inner journey, for readers who are wrestling with belief from the inside. Revelation and meaning, on the deeper question of why any of this should matter.</p>

<h2>Who this is for</h2>

<p>The audience is the honest inquirer, not the polemicist on either side. Atheists who want to know what the strongest version of the religious case actually looks like. Christians and other monotheists comparing claims across traditions. Muslims wrestling with hard questions and wanting to find them addressed seriously rather than waved away. Anyone who has noticed that the loudest voices in this discussion are rarely the most careful ones, and who would like an alternative.</p>

<p>Articles can be read individually, each is self-contained, or followed in their canonical sequence, which builds a cumulative argument across topics. New material is added regularly. The site is free, contains no advertising, and tracks no readers across sessions.</p>

<h2>A note on tone</h2>

<p>The work tries to be plainspoken without being casual, careful without being timid, and serious without being heavy-handed. Religious questions matter to people in ways that other questions do not, and that fact deserves respect on every side. Atheist readers should not feel patronised; Muslim readers should not feel that their tradition is being defended badly; readers from other backgrounds should be able to follow the arguments without prior commitment to any of them. Whether the work succeeds at this is for readers to judge, but the attempt, at least, is sincere.</p>

<p><a href="/articles">Browse all articles →</a></p>
',
        ],

        [
            'slug'    => 'editorial-policy',
            'title'   => 'Editorial Policy',
            'content' => '
<p class="article-lead">Every article on this site is held to the same standard: honest engagement with the strongest version of every objection, and a refusal to rely on arguments we would not find convincing ourselves.</p>

<h2>Intellectual honesty</h2>

<p>We present the strongest version of each objection before responding to it. If an argument against Islam is widely held by serious thinkers, we address it at full strength — not a weakened version that is easier to refute. Strawmanning is a failure of integrity, and we treat it as such.</p>

<h2>Sources and evidence</h2>

<p>Claims are supported by evidence from peer-reviewed research, established philosophical arguments, and primary textual sources. Quranic verses are cited in the original Arabic (Uthmanic rasm) with English translation and surah/verse reference. Hadith citations include the collection and grading. Scientific claims reference the relevant studies or established findings.</p>

<h2>What we do not do</h2>

<p>We do not use emotional manipulation, fear-based arguments, social pressure, or appeals to authority in place of evidence. We do not claim certainty where the evidence supports probability. We do not hide or minimise genuine difficulties in the Islamic tradition — we address them directly.</p>

<h2>Corrections</h2>

<p>If you find a factual error, a misrepresented source, or an argument that does not meet the standard described above, <a href="/contact-compelling-evidence">contact us</a>. We will investigate and correct it. Getting it right matters more than looking right.</p>
',
        ],

        [
            'slug'    => 'privacy-policy',
            'title'   => 'Privacy Policy',
            'content' => '
<p class="article-lead">Your privacy matters. This policy explains what data we collect, how we use it, and what we do not do.</p>

<h2>What we collect</h2>

<p><strong>Quiz and journey progress:</strong> Stored locally in your browser (localStorage). We do not send your quiz answers or journey progress to any server. Your data stays on your device. If you clear your browser data, your progress is lost.</p>

<p><strong>Engagement data:</strong> If you vote on an article or submit resonance feedback, we store an anonymised record (IP hash + article ID) to prevent duplicate submissions. We do not store your actual IP address.</p>

<p><strong>Contact form submissions:</strong> If you submit a question via the Ask a Question page, we receive your message and any contact information you voluntarily provide. We use this only to respond to your question.</p>

<h2>What we do not collect</h2>

<p>We do not use advertising trackers, social media pixels, or third-party analytics that track you across the web. We do not sell, share, or monetise any user data. We do not build profiles of our visitors.</p>

<h2>Cookies</h2>

<p>This site uses only essential cookies required for WordPress to function. No marketing cookies, no tracking cookies, no third-party cookies.</p>

<h2>Third parties</h2>

<p>This site loads fonts from Google Fonts. Google\'s privacy policy applies to those requests. No other third-party services receive your data.</p>

<h2>Your rights</h2>

<p>You can clear all locally stored data (quiz progress, journey state) by clearing your browser\'s localStorage for this domain. If you have submitted a question and want it deleted, <a href="/contact-compelling-evidence">contact us</a>.</p>

<p><em>Last updated: March 2026</em></p>
',
        ],

        [
            'slug'    => 'contact-compelling-evidence',
            'title'   => 'Contact',
            'content' => '
<p class="article-lead">Have a question, correction, or feedback? We read everything.</p>

<h2>Ask a question</h2>

<p>If you have a question about God, Islam, or any topic covered on this site, use the <a href="/ask-a-question">Ask a Question</a> page. Your question may be addressed in a future article.</p>

<h2>Report an error</h2>

<p>If you have found a factual error, a misrepresented source, or a broken link, please let us know. Getting it right matters more than looking right.</p>

<h2>General enquiries</h2>

<p>For all other enquiries, reach us at: <strong>hello@compelling-evidence.com</strong></p>

<p>We aim to respond within 48 hours. If your question requires research, it may take longer — but we will acknowledge receipt.</p>
',
        ],


        [
            'slug'    => 'ask-a-question',
            'title'   => 'Ask a Question',
            'content' => 'This page uses a custom template. Content is rendered by page-ask-a-question.php.',
            'template' => 'page-ask-a-question.php',
        ],

    
    ];

    foreach ( $pages as $page ) {
        // Skip if page already exists
        $existing = get_page_by_path( $page['slug'] );
        if ( $existing ) continue;

        $post_id = wp_insert_post([
            'post_title'   => $page['title'],
            'post_name'    => $page['slug'],
            'post_content' => $page['content'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ]);

        // Set page template if specified
        if ( $post_id && ! is_wp_error( $post_id ) && ! empty( $page['template'] ) ) {
            update_post_meta( $post_id, '_wp_page_template', $page['template'] );
        }
    }
}
/**
 * Run on every admin load, but only create pages once.
 * Uses an option flag so it doesn't re-run after pages exist.
 */
function ce_maybe_create_secondary_pages() {
    if ( get_option( 'ce_secondary_pages_created' ) ) return;
    ce_create_secondary_pages();
    update_option( 'ce_secondary_pages_created', '1' );
}
add_action( 'admin_init', 'ce_maybe_create_secondary_pages' );

/**
 * Create FAQ and Glossary pages (added in v2.2.51).
 * Uses its own flag so it runs once on upgrade.
 */
function ce_create_v2_pages() {
    if ( get_option( 'ce_v2_pages_created' ) ) return;

    $pages = [
        [
            'slug'     => 'faq',
            'title'    => 'Frequently Asked Questions',
            'content'  => '',
            'template' => 'page-faq.php',
        ],
        [
            'slug'     => 'glossary',
            'title'    => 'Glossary of Terms',
            'content'  => '',
            'template' => 'page-glossary.php',
        ],
    ];

    foreach ( $pages as $page ) {
        $existing = get_page_by_path( $page['slug'] );
        if ( $existing ) continue;

        $post_id = wp_insert_post([
            'post_title'   => $page['title'],
            'post_name'    => $page['slug'],
            'post_content' => $page['content'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
        ]);

        if ( $post_id && ! is_wp_error( $post_id ) && ! empty( $page['template'] ) ) {
            update_post_meta( $post_id, '_wp_page_template', $page['template'] );
        }
    }

    update_option( 'ce_v2_pages_created', '1' );
}
add_action( 'admin_init', 'ce_create_v2_pages' );

/**
 * Refresh the About page content on existing installs (added in v2.6.10).
 *
 * The auto-creation function above runs once and sets a flag. On sites where
 * the theme was activated before v2.6.10, the About page already exists in
 * the database with the older content, and updating this file alone does not
 * propagate to the existing post.
 *
 * This function uses its own flag (ce_about_v2_updated) so it runs exactly
 * once per site, locates the existing About page by slug, and refreshes its
 * content via wp_update_post. The flag prevents repeated overwrites if the
 * site administrator has further customised the About page after this
 * upgrade has run.
 */
function ce_update_about_page_v2() {
    if ( get_option( 'ce_about_v2_updated' ) ) return;

    $about = get_page_by_path( 'about-compelling-evidence' );
    if ( ! $about ) {
        // No existing About page — the auto-create function will handle it
        // on the next admin load with the new content.
        update_option( 'ce_about_v2_updated', '1' );
        return;
    }

    $new_content = '
<p class="article-lead">Compelling Evidence is a long-running attempt to take the strongest objections against Islam, and against belief in God in general, and weigh them honestly against what the available evidence supports. The work is written for the curious, the doubtful, and the unconvinced. It does not assume agreement, and it does not pretend to certainty that the evidence does not actually furnish.</p>

<h2>The premise</h2>

<p>Most material on contested religious questions falls into one of two camps. The first preaches to the convinced: comfortable, confident, allergic to genuine difficulty. The second weaponises difficulty against the other side without applying the same scrutiny to its own foundations. Neither serves the inquirer who actually wants to think a question through. The premise here is that careful argument, applied evenhandedly, has a better chance of arriving at something true than either confident assertion or rhetorical attack.</p>

<h2>The method</h2>

<p>Each article begins with a question, usually a hard one. The strongest version of the challenge is stated first, in the form a thoughtful sceptic would actually present it. Then the relevant evidence is laid out: historical sources, philosophical arguments, scientific findings, classical scholarly responses where they exist. Where the evidence cuts cleanly in one direction, that direction is named. Where the evidence is genuinely contested, the contestation is named. Where the evidence is thin, the thinness is named. Specific claims are tied to specific sources; rhetorical flourishes are kept to a minimum.</p>

<h2>What the work covers</h2>

<p>The articles are organised across eleven investigative topics. The existence of God: cosmological arguments, fine-tuning, ontological reasoning. The problem of evil: classical theodicies, modern objections, the evidential case from suffering. Ethics without God: whether the moral law can be grounded without theism, and what classical Islamic ethical thought brings to the question. Science and evidence: how religious claims interact with the empirical record, including the cosmological and biological details that recur in apologetic argument.</p>

<p>On the Islamic side specifically: the Quran and its sources, including its preservation history, literary form, internal structure, and engagement with prior scriptures. History, context, and comparison: situating early Islam within late antiquity, addressing the harder questions about the Prophet\'s life and the formation of the early community. Divine justice and fairness: the questions about hell, eternal punishment, predestination, and the moral architecture of the Islamic worldview. Islamic practice and ritual, examined for its rationale rather than presented as self-evident. Rights and freedom: the harder questions about religious liberty, apostasy, and how Islamic law has actually addressed these across the centuries. The inner journey, for readers who are wrestling with belief from the inside. Revelation and meaning, on the deeper question of why any of this should matter.</p>

<h2>Who this is for</h2>

<p>The audience is the honest inquirer, not the polemicist on either side. Atheists who want to know what the strongest version of the religious case actually looks like. Christians and other monotheists comparing claims across traditions. Muslims wrestling with hard questions and wanting to find them addressed seriously rather than waved away. Anyone who has noticed that the loudest voices in this discussion are rarely the most careful ones, and who would like an alternative.</p>

<p>Articles can be read individually, each is self-contained, or followed in their canonical sequence, which builds a cumulative argument across topics. New material is added regularly. The site is free, contains no advertising, and tracks no readers across sessions.</p>

<h2>A note on tone</h2>

<p>The work tries to be plainspoken without being casual, careful without being timid, and serious without being heavy-handed. Religious questions matter to people in ways that other questions do not, and that fact deserves respect on every side. Atheist readers should not feel patronised; Muslim readers should not feel that their tradition is being defended badly; readers from other backgrounds should be able to follow the arguments without prior commitment to any of them. Whether the work succeeds at this is for readers to judge, but the attempt, at least, is sincere.</p>

<p><a href="/articles">Browse all articles →</a></p>
';

    wp_update_post([
        'ID'           => $about->ID,
        'post_content' => $new_content,
    ]);

    update_option( 'ce_about_v2_updated', '1' );
}
add_action( 'admin_init', 'ce_update_about_page_v2' );
