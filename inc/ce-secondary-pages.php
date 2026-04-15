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
<p class="article-lead">Compelling Evidence is an independent, evidence-based inquiry into the questions that matter most — written for the curious, the doubtful, and the unconvinced.</p>

<h2>What this site is</h2>

<p>This site presents the case for God\'s existence and for Islam as the most rationally compelling answer to the questions that follow from that existence. It is written primarily for atheists, agnostics, and sceptics — people who start from zero and demand evidence before belief.</p>

<p>Every argument is built on evidence from philosophy, physics, cosmology, consciousness studies, and ethics — not on scripture, tradition, or emotional appeals. The Quran and hadith are cited as supporting evidence where relevant, but no argument depends on accepting their authority in advance.</p>

<h2>What this site is not</h2>

<p>This is not a comparative religion site. It does not attack other traditions. It is not affiliated with any mosque, organisation, political movement, or government. It does not collect personal data for marketing. It does not track you across the web.</p>

<h2>Who it is for</h2>

<p>If you are a convinced atheist who thinks religion is intellectually bankrupt — this is written for you. If you are an agnostic who has never found a case strong enough to commit — this is written for you. If you are a scientist who demands empirical rigour — this is written for you. If you are a Muslim carrying doubts you have never been able to voice — this is written for you.</p>

<p>The only thing required is honesty. Not agreement. Not belief. Just the willingness to follow the evidence wherever it leads.</p>

<h2>How to engage</h2>

<p>Start with the <a href="/quiz">quiz</a> — ten questions that route you to a personalised journey written for your specific starting point. Or browse the <a href="/articles">articles</a> directly. Or <a href="/ask-a-question">ask a question</a> we haven\'t addressed yet.</p>
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
