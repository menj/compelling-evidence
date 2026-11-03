<?php
/**
 * Compelling Evidence: the page-level parts of the single JSON-LD graph.
 *
 * ce_schema_nodes() (inc/ce-seo.php) builds the site nodes and the Article,
 * then adds what this file builds for the page in hand:
 *
 *   - the page frame: WebPage (or its subtype), WPHeader, WPFooter, WPSideBar
 *     and one SiteNavigationElement per visible menu;
 *   - FAQPage (page-faq.php), DefinedTermSet (page-glossary.php), Quiz
 *     (page-quiz.php), LearningResource and the journey's own nodes
 *     (page-journey.php), QAPage (single-ce_question.php), ItemList (topic
 *     archives), ContactPage, AboutPage and SearchResultsPage;
 *   - article details: figures and diagrams as ImageObject with their credits,
 *     Qur'an and hadith quotations as Quotation nodes, glossary mentions.
 *
 * Every node has a stable @id (page URL + #fragment). ce_schema_finalize()
 * merges nodes that share an @id, so a page prints one graph. With Rank Math
 * active the same nodes go into Rank Math's graph through rank_math/json_ld
 * (ce_rankmath_json_ld()); nothing prints a second block.
 *
 * A type or property is added only when the page shows the thing it
 * describes. The owner keeps all schema.org markup (rule of 3 November 2026).
 *
 * @since 2.12.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ═══════════════════════════════════════════════════════════════════════
   IDS AND HELPERS
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Stable @id: a URL with a fragment.
 */
function ce_schema_id( string $url, string $fragment ): string {
    $url = preg_replace( '/#.*$/', '', $url );
    if ( false === strpos( $url, '?' ) ) {
        $url = trailingslashit( $url );
    }
    return $url . '#' . $fragment;
}

/** @id of the WebSite node. */
function ce_schema_website_id(): string {
    return ce_schema_id( home_url( '/' ), 'website' );
}

/** @id of the Organization node. */
function ce_schema_org_id(): string {
    return ce_schema_id( home_url( '/' ), 'organization' );
}

/** @id of the configured author Person. */
function ce_schema_author_id(): string {
    return ce_schema_id( home_url( '/' ), 'author' );
}

/** A reference to a node by @id. */
function ce_schema_ref( string $id ): array {
    return [ '@id' => $id ];
}

/**
 * Plain text from HTML: tags removed, entities decoded, spaces collapsed.
 */
function ce_schema_text( string $html ): string {
    $text = html_entity_decode( wp_strip_all_tags( $html ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
    return trim( (string) preg_replace( '/\s+/u', ' ', $text ) );
}

/**
 * The page template file of the queried page ('' when not a page).
 */
function ce_schema_template(): string {
    if ( ! is_page() ) {
        return '';
    }
    $slug = get_page_template_slug( get_queried_object_id() );
    return is_string( $slug ) ? $slug : '';
}

/**
 * The URL the current page is served from.
 */
function ce_schema_page_url(): string {
    $url = function_exists( 'ce_get_canonical_url' ) ? ce_get_canonical_url() : '';
    if ( '' === $url && is_search() ) {
        $url = add_query_arg( 's', rawurlencode( get_search_query() ), home_url( '/' ) );
        return $url;
    }
    return '' !== $url ? $url : home_url( '/' );
}

/**
 * The journey being rendered, set by template-parts/journey/document.php.
 *
 * @param array|null $j Journey data to set, or null to read.
 * @param bool       $clear Forget the journey.
 */
function ce_schema_journey( ?array $j = null, bool $clear = false ): ?array {
    static $current = null;
    if ( $clear ) {
        $current = null;
    } elseif ( null !== $j ) {
        $current = $j;
    }
    return $current;
}

/**
 * True for the quiz and journey pages, which are complete HTML documents
 * without the theme header, footer or wp_head() (the quiz captures it).
 */
function ce_schema_is_standalone(): bool {
    return null !== ce_schema_journey()
        || in_array( ce_schema_template(), [ 'page-quiz.php', 'page-journey.php' ], true );
}

/* ═══════════════════════════════════════════════════════════════════════
   PAGE NODES
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Nodes for the current page beyond the site nodes and the Article.
 *
 * @param array $base The nodes ce_schema_nodes() has built so far.
 * @return array[]
 */
function ce_schema_page_nodes( array $base ): array {
    if ( is_404() ) {
        return [];
    }
    $url   = ce_schema_page_url();
    $nodes = [];
    $page  = [
        '@type'      => 'WebPage',
        '@id'        => ce_schema_id( $url, 'webpage' ),
        'url'        => $url,
        'inLanguage' => get_locale(),
        'isPartOf'   => ce_schema_ref( ce_schema_website_id() ),
        'publisher'  => ce_schema_ref( ce_schema_org_id() ),
    ];
    $tpl = ce_schema_template();

    $journey = ce_schema_journey();
    if ( null !== $journey ) {
        return ce_schema_journey_nodes( $journey, $page );
    }

    if ( is_front_page() ) {
        $page['name'] = get_bloginfo( 'name' );
    } elseif ( is_singular() ) {
        $page['name'] = ce_schema_text( get_the_title() );
    }

    // Article pages: the Article is the main entity.
    foreach ( $base as $n ) {
        if ( ( $n['@type'] ?? '' ) === 'Article' && isset( $n['@id'] ) && ( $n['url'] ?? '' ) === $url ) {
            $page['mainEntity'] = ce_schema_ref( $n['@id'] );
            $page['datePublished'] = $n['datePublished'] ?? null;
            $page['dateModified']  = $n['dateModified'] ?? null;
            $page['speakable']     = [
                '@type'       => 'SpeakableSpecification',
                'cssSelector' => [ '.article-title', '.article-content > p:first-of-type' ],
            ];
        }
        if ( ( $n['@type'] ?? '' ) === 'BreadcrumbList' && isset( $n['@id'] ) ) {
            $page['breadcrumb'] = ce_schema_ref( $n['@id'] );
        }
        if ( ( $n['@type'] ?? '' ) === 'ImageObject' && ( $n['@id'] ?? '' ) === ce_schema_id( $url, 'primaryimage' ) ) {
            $page['primaryImageOfPage'] = ce_schema_ref( $n['@id'] );
        }
        if ( ( $n['@type'] ?? '' ) === 'CollectionPage' ) {
            $page['@type'] = 'CollectionPage';
        }
    }

    if ( 'page-faq.php' === $tpl ) {
        $faq = ce_schema_faq_node( $url );
        if ( $faq ) {
            $nodes[]            = $faq;
            $page['mainEntity'] = ce_schema_ref( $faq['@id'] );
        }
    } elseif ( 'page-glossary.php' === $tpl ) {
        $set                = ce_schema_glossary_node( $url );
        $nodes[]            = $set;
        $page['mainEntity'] = ce_schema_ref( $set['@id'] );
    } elseif ( 'page-quiz.php' === $tpl ) {
        $quiz = ce_schema_quiz_node( $url );
        if ( $quiz ) {
            $nodes[]            = $quiz;
            $page['mainEntity'] = ce_schema_ref( $quiz['@id'] );
        }
    } elseif ( 'page-ask-a-question.php' === $tpl ) {
        $page['@type'] = 'ContactPage';
    } elseif ( is_page( 'contact-compelling-evidence' ) ) {
        $page['@type'] = 'ContactPage';
    } elseif ( is_page( 'about-compelling-evidence' ) ) {
        $page['@type'] = 'AboutPage';
    } elseif ( is_search() ) {
        $page['@type'] = 'SearchResultsPage';
        $page['name']  = 'Search results for ' . get_search_query();
    } elseif ( is_post_type_archive( 'ce_question' ) ) {
        $page['@type'] = 'CollectionPage';
    }

    if ( is_tax( 'ce_topic' ) ) {
        $list = ce_schema_topic_list( $url );
        if ( $list ) {
            $nodes[]            = $list;
            $page['mainEntity'] = ce_schema_ref( $list['@id'] );
        }
    }

    if ( is_singular( 'ce_question' ) ) {
        $qa = ce_schema_question_node( $url );
        if ( $qa ) {
            $nodes[]            = $qa;
            $page['@type']      = 'QAPage';
            $page['mainEntity'] = ce_schema_ref( $qa['@id'] );
        }
    }

    if ( ! ce_schema_is_standalone() ) {
        foreach ( ce_schema_frame_nodes( $url ) as $f ) {
            $nodes[]           = $f;
            $page['hasPart'][] = ce_schema_ref( $f['@id'] );
        }
    }

    $nodes[] = array_filter( $page, static function ( $v ) {
        return null !== $v && '' !== $v && [] !== $v;
    } );
    return $nodes;
}

/* ═══════════════════════════════════════════════════════════════════════
   FRAME: HEADER, NAVIGATION, FOOTER, SIDEBAR
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * The primary menu as header.php shows it: the menu assigned to the
 * primary location, or the fallback list.
 *
 * @return array<int,array{name:string,url:string}>
 */
function ce_schema_primary_menu(): array {
    $items     = [];
    $locations = function_exists( 'get_nav_menu_locations' ) ? get_nav_menu_locations() : [];
    if ( ! empty( $locations['primary'] ) && function_exists( 'wp_get_nav_menu_items' ) ) {
        foreach ( (array) wp_get_nav_menu_items( $locations['primary'] ) as $item ) {
            // header.php sets no depth, so wp_nav_menu() prints every level.
            if ( is_object( $item ) ) {
                $items[] = [ 'name' => ce_schema_text( (string) $item->title ), 'url' => (string) $item->url ];
            }
        }
    }
    if ( ! $items ) {
        foreach ( [ 'journeys' => 'Journeys', 'articles' => 'Articles', 'qa' => 'Q&A', 'ask-a-question' => 'Ask a Question', 'faq' => 'FAQ' ] as $path => $name ) {
            $items[] = [ 'name' => $name, 'url' => home_url( '/' . $path ) ];
        }
    }
    return $items;
}

/**
 * The footer's Navigate and About columns (footer.php).
 *
 * @return array<int,array{name:string,url:string}>
 */
function ce_schema_footer_menu(): array {
    $links = [
        'quiz'                       => 'Take the Quiz',
        'journeys'                   => 'All Paths',
        'articles'                   => 'Articles',
        'qa'                         => 'Q&A',
        'ask-a-question'             => 'Ask a Question',
        'faq'                        => 'FAQ',
        'glossary'                   => 'Glossary',
        'my-progress'                => 'My Progress',
        'about-compelling-evidence'  => 'About This Site',
        'editorial-policy'           => 'Editorial Policy',
        'privacy-policy'             => 'Privacy Policy',
        'contact-compelling-evidence' => 'Contact',
    ];
    $items = [];
    foreach ( $links as $path => $name ) {
        $items[] = [ 'name' => $name, 'url' => home_url( '/' . $path ) ];
    }
    return $items;
}

/**
 * A SiteNavigationElement with its links as an ordered ItemList.
 */
function ce_schema_nav_node( string $id, string $name, array $items ): array {
    $list = [];
    foreach ( array_values( $items ) as $i => $it ) {
        $list[] = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => $it['name'],
            'url'      => $it['url'],
        ];
    }
    return [
        '@type'      => 'SiteNavigationElement',
        '@id'        => $id,
        'name'       => $name,
        'hasPart'    => array_map( static function ( $it ) {
            return [ '@type' => 'WebPage', 'name' => $it['name'], 'url' => $it['url'] ];
        }, array_values( $items ) ),
        'mainEntity' => [ '@type' => 'ItemList', 'itemListElement' => $list ],
    ];
}

/**
 * Header, footer, their menus and, on articles, the sidebar.
 *
 * The header and footer are the same on every page, so their @ids hang off
 * the home URL; the sidebar belongs to the article.
 *
 * @return array[]
 */
function ce_schema_frame_nodes( string $url ): array {
    $home  = home_url( '/' );
    $nodes = [
        [
            '@type' => 'WPHeader',
            '@id'   => ce_schema_id( $home, 'header' ),
            'name'  => 'Site header',
            'cssSelector' => '.ce-nav',
        ],
        ce_schema_nav_node( ce_schema_id( $home, 'primary-menu' ), 'Main menu', ce_schema_primary_menu() ),
        [
            '@type' => 'WPFooter',
            '@id'   => ce_schema_id( $home, 'footer' ),
            'name'  => 'Site footer',
            'cssSelector' => '.ce-footer',
            'copyrightHolder' => ce_schema_ref( ce_schema_org_id() ),
            'copyrightYear'   => (int) wp_date( 'Y' ),
        ],
        ce_schema_nav_node( ce_schema_id( $home, 'footer-menu' ), 'Footer menu', ce_schema_footer_menu() ),
    ];
    if ( is_singular( 'ce_article' ) ) {
        $nodes[] = [
            '@type'       => 'WPSideBar',
            '@id'         => ce_schema_id( $url, 'sidebar' ),
            'name'        => 'Contents and reading progress',
            'cssSelector' => '.article-sidebar',
        ];
    }
    return $nodes;
}

/* ═══════════════════════════════════════════════════════════════════════
   FAQ PAGE
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * The site FAQ as page-faq.php shows it: section title => question => answer
 * (answers are HTML).
 *
 * @return array<string,array<string,string>>
 */
function ce_faq_sections(): array {
    return [

        'About This Site' => [
            'What is Compelling Evidence?' =>
                'Compelling Evidence is an independent, evidence-based inquiry into the questions that matter most — the existence of God, the coherence of Islam, and the objections raised against both. It is written for the curious, the doubtful, and the unconvinced.',
            'Who runs this site?' =>
                'The site is maintained by an independent team of researchers, writers, and editors. It is not affiliated with any mosque, government, political movement, or religious organisation.',
            'Is this a dawah site?' =>
                'It is a site that presents the case for Islam — honestly, using evidence, and at full intellectual strength. If that constitutes dawah, then yes. But it does not use emotional manipulation, fear tactics, or social pressure. Every argument is built to survive scrutiny, not to bypass it.',
            'Why should I trust this site?' =>
                'You shouldn\'t — not on trust. The site asks you to evaluate the arguments on their merits. Every claim is sourced. Every major objection is presented at full strength before being addressed. The <a href="/editorial-policy">editorial policy</a> commits to intellectual honesty, and you are invited to hold the site to that standard.',
            'Is this site trying to convert me?' =>
                'The site presents the strongest case it can for Islam. What you do with that case is your decision. No article ends with a conversion pitch. No page collects your data for follow-up. The site respects your autonomy — because Islam itself requires that faith be freely chosen to have any value.',
        ],

        'The Quiz & Journeys' => [
            'What is the quiz?' =>
                'A 10-question assessment that identifies your starting point — atheist, agnostic, spiritual seeker, questioning Muslim, etc. — and routes you to a personalised reading journey designed for where you actually are, not where someone wishes you were.',
            'Is my quiz data stored on a server?' =>
                'Partly. Your quiz result and journey progress are saved in your browser (localStorage); if you clear your browser data, that progress is lost. Separately, the site records anonymous usage events on its own server, with no cookies and no third-party services: each answer you choose in the quiz, the persona the quiz assigns you, the journey screens you view, page and article views, scroll depth, and search terms. Each event is stored with a one-way hash of your IP address (the address itself is never stored), a random session ID, the page address, and whether you use a phone, tablet or desktop. The quiz never collects your name or email.',
            'What are journeys?' =>
                'Each journey is a curated sequence of articles ordered for a specific persona. A classical atheist starts with cosmological arguments. A questioning Muslim starts with doubt and inner struggle. The content is the same 100 articles — the sequencing is different.',
            'Can I read articles without taking the quiz?' =>
                'Absolutely. The <a href="/articles">articles page</a> gives you full access to all 100 articles, organised by topic. The quiz is a starting point, not a gate.',
            'How many personas are there?' =>
                'Fourteen — covering the full spectrum from hard atheism through agnosticism, deism, and spiritual seeking to the questioning Muslim and the committed Muslim.',
        ],

        'Common Objections' => [
            'Isn\'t this just preaching to the converted?' =>
                'The opposite. The site is written for people who do not believe, who have serious objections, and who require evidence before commitment. Every article addresses the strongest version of the opposing argument — not a strawman.',
            'Why Islam and not just generic theism?' =>
                'The site begins with generic theism — the existence of God is argued from philosophy, cosmology, and ethics without reference to any specific religion. Once the case for God is established, the site then examines which tradition best accounts for what reason has established. Islam is presented as the strongest candidate. The reader is invited to evaluate that claim.',
            'Don\'t you cherry-pick easy objections?' =>
                'No. The site addresses the hardest objections head-on: the age of Aisha, apostasy law, slavery in the sources, the problem of evil, Quranic cosmology, hadith reliability, the violence of early Islam, and more. Each is presented at full strength before the response is given.',
            'What if I read everything and still don\'t believe?' =>
                'Then you have engaged honestly with the evidence, and the site has done its job. Compelling Evidence does not define success as conversion. It defines success as honest engagement. A person who examines the evidence carefully and reaches a different conclusion has done something more valuable than a person who agrees without thinking.',
        ],

        'Technical & Privacy' => [
            'Does this site use cookies?' =>
                'The site uses essential cookies for basic functionality. It does not use tracking cookies, advertising cookies, or third-party analytics that follow you across the web.',
            'Is my data collected or shared?' =>
                'Quiz and journey progress are stored locally in your browser — never on our servers. If you submit a question through the <a href="/ask-a-question">Ask a Question</a> page, we receive only what you voluntarily provide. We do not sell, share, or monetise any data.',
            'How do I contact the site?' =>
                'Use the <a href="/contact-compelling-evidence">contact page</a> or the <a href="/ask-a-question">Ask a Question</a> form. We read every submission.',
            'Can I share or reprint articles?' =>
                'You may share links freely. For reprinting or republication, please <a href="/contact-compelling-evidence">contact us</a> first.',
        ],
    ];
}

/**
 * FAQPage for page-faq.php. Moved out of the template in 2.12.0 so the page
 * prints one graph.
 */
function ce_schema_faq_node( string $url ): ?array {
    $items = [];
    foreach ( ce_faq_sections() as $questions ) {
        foreach ( $questions as $q => $a ) {
            $items[] = [
                '@type'          => 'Question',
                'name'           => $q,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => wp_strip_all_tags( $a ),
                ],
            ];
        }
    }
    if ( ! $items ) {
        return null;
    }
    return [
        '@type'      => 'FAQPage',
        '@id'        => ce_schema_id( $url, 'faq' ),
        'url'        => $url,
        'name'       => 'Frequently Asked Questions',
        'mainEntity' => $items,
    ];
}

/* ═══════════════════════════════════════════════════════════════════════
   GLOSSARY
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * URL of the glossary page, where every term's anchor lives.
 */
function ce_schema_glossary_url(): string {
    return home_url( '/glossary/' );
}

/**
 * @id of one glossary term: its anchor on the glossary page.
 */
function ce_schema_term_id( string $term ): string {
    return ce_schema_glossary_url() . '#term-' . sanitize_title( $term );
}

/**
 * DefinedTermSet for page-glossary.php. Moved out of the template in 2.12.0.
 */
function ce_schema_glossary_node( string $url ): array {
    $set_id = ce_schema_id( ce_schema_glossary_url(), 'glossary' );
    $terms  = [];
    foreach ( ( function_exists( 'ce_get_glossary_terms' ) ? ce_get_glossary_terms() : [] ) as $t ) {
        $node = [
            '@type'            => 'DefinedTerm',
            '@id'              => ce_schema_term_id( $t['term'] ),
            'name'             => $t['term'],
            'description'      => wp_strip_all_tags( $t['def_long'] ?? $t['def'] ),
            'url'              => ce_schema_term_id( $t['term'] ),
            'inDefinedTermSet' => ce_schema_ref( $set_id ),
        ];
        if ( ! empty( $t['arabic'] ) ) {
            $node['termCode'] = $t['arabic'];
        }
        $terms[] = $node;
    }
    return [
        '@type'          => 'DefinedTermSet',
        '@id'            => $set_id,
        'name'           => 'Compelling Evidence Glossary of Islamic Terms',
        'description'    => 'Key concepts from the Islamic intellectual tradition used throughout this site — defined clearly and without jargon.',
        'inLanguage'     => 'en',
        'url'            => $url,
        'publisher'      => ce_schema_ref( ce_schema_org_id() ),
        'hasDefinedTerm' => $terms,
    ];
}

/* ═══════════════════════════════════════════════════════════════════════
   QUIZ
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Quiz for page-quiz.php, read from the QUESTIONS object in quiz.html.
 *
 * Every question and its options show on the page. The quiz states that it
 * has no right answers, and the page never reveals one, so no question has
 * an acceptedAnswer. The options are suggestedAnswer.
 */
function ce_schema_quiz_node( string $url ): ?array {
    $file = function_exists( 'ce_get_quiz_file' ) ? ce_get_quiz_file() : '';
    if ( ! $file || ! is_readable( $file ) ) {
        return null;
    }
    $html = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- theme file.
    if ( ! preg_match( '/var QUESTIONS = (\{.*?\});\s*$/m', $html, $m ) ) {
        return null;
    }
    $questions = json_decode( $m[1], true );
    if ( ! is_array( $questions ) ) {
        return null;
    }
    $parts = [];
    $pos   = 0;
    foreach ( $questions as $key => $q ) {
        if ( empty( $q['text'] ) || empty( $q['options'] ) ) {
            continue;
        }
        $answers = [];
        foreach ( $q['options'] as $o ) {
            $answers[] = [
                '@type' => 'Answer',
                'text'  => trim( $o['text'] . ( ! empty( $o['sub'] ) ? '. ' . $o['sub'] : '' ) ),
            ];
        }
        $parts[] = array_filter( [
            '@type'           => 'Question',
            '@id'             => ce_schema_id( $url, 'question-' . sanitize_key( (string) $key ) ),
            'position'        => ++$pos,
            'name'            => $q['text'],
            'text'            => $q['text'],
            'about'           => ! empty( $q['topic'] ) ? [ '@type' => 'Thing', 'name' => (string) $q['topic'] ] : null,
            'eduQuestionType' => ! empty( $q['single'] ) ? 'Multiple choice' : 'Checkbox',
            'answerCount'     => count( $answers ),
            'suggestedAnswer' => $answers,
        ] );
    }
    if ( ! $parts ) {
        return null;
    }
    $title = preg_match( '#<title>(.*?)</title>#s', $html, $t ) ? ce_title_strip_site( ce_schema_text( $t[1] ) ) : 'Find Your Path';
    $desc  = preg_match( '#<meta name="description" content="([^"]*)"#', $html, $d ) ? html_entity_decode( $d[1], ENT_QUOTES ) : '';
    return array_filter( [
        '@type'                => 'Quiz',
        '@id'                  => ce_schema_id( $url, 'quiz' ),
        'name'                 => $title,
        'description'          => $desc,
        'url'                  => $url,
        'inLanguage'           => get_locale(),
        'learningResourceType' => 'Self-assessment quiz',
        'educationalUse'       => 'Self-assessment',
        'isAccessibleForFree'  => true,
        'publisher'            => ce_schema_ref( ce_schema_org_id() ),
        'hasPart'              => $parts,
    ] );
}

/* ═══════════════════════════════════════════════════════════════════════
   JOURNEYS
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * The journey page's graph: the journey's own nodes from journey.json
 * (Organization, Article, FAQPage, BreadcrumbList, WebPage with speakable),
 * with stable @ids, plus a LearningResource whose steps are the chapters in
 * reading order.
 *
 * The page has no theme header or footer, so it has no frame nodes. Its
 * FAQPage is removed later by ce_standalone_html_seo(), because those
 * questions are not shown on the page.
 *
 * @param array $j    Journey data (ce_journey_load()).
 * @param array $page The WebPage node so far.
 * @return array[]
 */
function ce_schema_journey_nodes( array $j, array $page ): array {
    $url      = $page['url'];
    $nodes    = [];
    $title    = ce_title_strip_site( (string) ( $j['seo']['og:title'] ?? $j['seo']['title'] ?? '' ) );
    $title    = preg_replace( '/^Compelling Evidence:\s*/', '', $title );
    $art_id   = ce_schema_id( $url, 'article' );
    $learn_id = ce_schema_id( $url, 'learning' );

    foreach ( (array) ( $j['schema'] ?? [] ) as $node ) {
        unset( $node['@context'] );
        $type = (string) ( $node['@type'] ?? '' );
        switch ( $type ) {
            case 'Organization':
                // The site Organization node stands for it.
                break;
            case 'Article':
                $node['@id']              = $art_id;
                $node['url']              = $url;
                $node['author']           = ce_schema_ref( ce_schema_org_id() );
                $node['publisher']        = ce_schema_ref( ce_schema_org_id() );
                $node['mainEntityOfPage'] = ce_schema_ref( $page['@id'] );
                $node['isPartOf']         = ce_schema_ref( $page['@id'] );
                $nodes[]                  = $node;
                break;
            case 'FAQPage':
                $node['@id'] = ce_schema_id( $url, 'faq' );
                $nodes[]     = $node;
                break;
            case 'BreadcrumbList':
                $node['@id']        = ce_schema_id( $url, 'breadcrumb' );
                $page['breadcrumb'] = ce_schema_ref( $node['@id'] );
                $nodes[]            = $node;
                break;
            case 'WebPage':
                unset( $node['@type'], $node['@id'] );
                $page = array_merge( $page, $node );
                break;
            default:
                if ( '' !== $type ) {
                    $nodes[] = $node;
                }
        }
    }

    $steps = [];
    $pos   = 0;
    foreach ( (array) $j['screens'] as $screen ) {
        if ( ! empty( $screen['template'] ) || empty( $screen['id'] ) ) {
            continue; // The Volume II preview is a teaser, not a step.
        }
        $item = [
            '@type' => 'CreativeWork',
            '@id'   => ce_schema_id( $url, 's-' . sanitize_key( $screen['id'] ) ),
            'name'  => (string) ( $screen['label'] ?? $screen['id'] ),
            'url'   => preg_replace( '/#.*$/', '', $url ) . '#s-' . $screen['id'],
        ];
        $reading = [];
        foreach ( (array) ( $j['journey']['articles'][ $screen['id'] ] ?? [] ) as $a ) {
            if ( ! empty( $a['href'] ) && ! empty( $a['title'] ) ) {
                $reading[] = [
                    '@type'    => 'Article',
                    'headline' => (string) $a['title'],
                    'name'     => (string) $a['title'],
                    'url'   => home_url( (string) $a['href'] ),
                ];
            }
        }
        if ( $reading ) {
            $item['isBasedOn'] = $reading;
        }
        $steps[] = [
            '@type'    => 'ListItem',
            'position' => ++$pos,
            'item'     => $item,
        ];
    }

    $article = null;
    foreach ( $nodes as $n ) {
        if ( 'Article' === ( $n['@type'] ?? '' ) ) {
            $article = $n;
        }
    }
    $learning = array_filter( [
        '@type'                => 'LearningResource',
        '@id'                  => $learn_id,
        'name'                 => $title,
        'description'          => (string) ( $j['seo']['description'] ?? '' ),
        'url'                  => $url,
        'inLanguage'           => (string) ( $article['inLanguage'] ?? get_locale() ),
        'learningResourceType' => 'Guided reading path',
        'educationalUse'       => 'Self-study',
        'isAccessibleForFree'  => true,
        'about'                => $article['about'] ?? null,
        'keywords'             => $article['keywords'] ?? null,
        'publisher'            => ce_schema_ref( ce_schema_org_id() ),
        'mainEntityOfPage'     => ce_schema_ref( $page['@id'] ),
        'hasPart'              => $steps ? [
            '@type'           => 'ItemList',
            '@id'             => ce_schema_id( $url, 'steps' ),
            'name'            => 'Chapters',
            'itemListOrder'   => 'https://schema.org/ItemListOrderAscending',
            'numberOfItems'   => count( $steps ),
            'itemListElement' => $steps,
        ] : null,
    ] );
    $nodes[] = $learning;

    $page['name']       = $title;
    $page['mainEntity'] = ce_schema_ref( $learn_id );
    $nodes[]            = $page;
    return $nodes;
}

/* ═══════════════════════════════════════════════════════════════════════
   READER QUESTIONS
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Question node for a published reader question (QAPage main entity).
 *
 * The answer is marked only when the page shows one: content that is empty
 * or repeats the title shows the pending-answer line, and then the Question
 * has answerCount 0 and no Answer. The asker is never named on the page, so
 * the Question has no author.
 */
function ce_schema_question_node( string $url ): ?array {
    $post = get_queried_object();
    if ( ! ( $post instanceof WP_Post ) || 'ce_question' !== $post->post_type ) {
        return null;
    }
    $statuses = wp_get_object_terms( $post->ID, 'ce_qstatus', [ 'fields' => 'slugs' ] );
    if ( is_wp_error( $statuses ) || ! in_array( 'published', (array) $statuses, true ) ) {
        return null;
    }
    $title = ce_schema_text( get_the_title( $post ) );
    $raw   = trim( (string) $post->post_content );
    $node  = [
        '@type'       => 'Question',
        '@id'         => ce_schema_id( $url, 'question' ),
        'name'        => $title,
        'text'        => $title,
        'url'         => $url,
        'dateCreated' => get_the_date( 'c', $post ),
        'answerCount' => 0,
    ];
    $topic = (string) get_post_meta( $post->ID, '_ce_question_topic', true );
    if ( '' !== $topic ) {
        $node['about'] = [ '@type' => 'Thing', 'name' => $topic ];
    }
    $answer = '' !== $raw ? ce_schema_text( apply_filters( 'the_content', $post->post_content ) ) : '';
    if ( '' !== $answer && $raw !== trim( get_the_title( $post ) ) ) {
        $node['answerCount']    = 1;
        $node['acceptedAnswer'] = [
            '@type'       => 'Answer',
            '@id'         => ce_schema_id( $url, 'answer' ),
            'text'        => $answer,
            'url'         => $url,
            'dateCreated' => get_the_modified_date( 'c', $post ),
            'author'      => ce_schema_ref( ce_schema_org_id() ),
        ];
    }
    return $node;
}

/* ═══════════════════════════════════════════════════════════════════════
   TOPIC ARCHIVES
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * ItemList of the articles a topic page lists, in the order shown
 * (archive-ce_article.php: menu_order ascending).
 */
function ce_schema_topic_list( string $url ): ?array {
    $term = get_queried_object();
    if ( ! is_object( $term ) || empty( $term->term_id ) ) {
        return null;
    }
    $posts = get_posts( [
        'post_type'      => 'ce_article',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'menu_order',
        'order'          => 'ASC',
        'tax_query'      => [ [ 'taxonomy' => 'ce_topic', 'field' => 'term_id', 'terms' => [ $term->term_id ] ] ], // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
    ] );
    if ( ! $posts ) {
        return null;
    }
    $items = [];
    foreach ( array_values( $posts ) as $i => $p ) {
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $i + 1,
            'name'     => ce_schema_text( get_the_title( $p ) ),
            'url'      => get_permalink( $p ),
        ];
    }
    return [
        '@type'           => 'ItemList',
        '@id'             => ce_schema_id( $url, 'articles' ),
        'name'            => $term->name . ' articles',
        'itemListOrder'   => 'https://schema.org/ItemListOrderAscending',
        'numberOfItems'   => count( $items ),
        'itemListElement' => $items,
    ];
}

/* ═══════════════════════════════════════════════════════════════════════
   ARTICLE DETAILS: FIGURES, DIAGRAMS, SCRIPTURE, GLOSSARY
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Media type from a file name or URL.
 */
function ce_schema_media_type( string $file ): string {
    $ext = strtolower( pathinfo( (string) parse_url( $file, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
    $map = [ 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml', 'tif' => 'image/tiff', 'tiff' => 'image/tiff' ];
    return $map[ $ext ] ?? '';
}

/**
 * ImageObject for a media registry entry, with the credit the figure shows.
 */
function ce_schema_media_image( string $id, array $m, string $node_id, string $caption = '' ): array {
    $att  = (int) ( function_exists( 'ce_media_attachments' ) ? ( ce_media_attachments()[ $id ] ?? 0 ) : 0 );
    $full = $att ? (string) wp_get_attachment_image_url( $att, 'full' ) : '';
    if ( '' === $full ) {
        $full = (string) ( $m['url'] ?? $m['thumb'] ?? '' );
    }
    $credit = function_exists( 'ce_media_credit_html' ) ? ce_schema_text( ce_media_credit_html( $m ) ) : '';
    $license = '';
    if ( ! empty( $m['license_url'] ) ) {
        $license = (string) $m['license_url'];
    } elseif ( ! empty( $m['license'] ) ) {
        $license = [ '@type' => 'CreativeWork', 'name' => (string) $m['license'] ];
    }
    $img = [
        '@type'              => 'ImageObject',
        '@id'                => $node_id,
        'contentUrl'         => $full,
        'url'                => $full,
        'width'              => (int) ( $m['width'] ?? 0 ) ?: null,
        'height'             => (int) ( $m['height'] ?? 0 ) ?: null,
        'encodingFormat'     => ce_schema_media_type( (string) ( $m['file'] ?? $full ) ),
        'description'        => (string) ( $m['alt'] ?? '' ),
        'caption'            => $caption,
        'creditText'         => $credit,
        'creator'            => ! empty( $m['author'] ) ? array_filter( [ '@type' => 'Thing', 'name' => (string) $m['author'], 'url' => (string) ( $m['author_url'] ?? '' ) ] ) : null,
        'copyrightNotice'    => trim( ( (string) ( $m['author'] ?? '' ) ) . ( ! empty( $m['license'] ) ? ', ' . $m['license'] : '' ), ', ' ),
        'license'            => $license,
        'acquireLicensePage' => (string) ( $m['page'] ?? '' ),
    ];
    return array_filter( $img, static function ( $v ) {
        return null !== $v && '' !== $v && [] !== $v;
    } );
}

/**
 * Figures and diagrams in an article body, in order.
 *
 * @return array[] ImageObject nodes.
 */
function ce_schema_figures( string $content, string $url ): array {
    $out = [];
    if ( ! preg_match_all( '/\[(ce_figure|ce_diagram)\b([^\]]*)\](?:(.*?)\[\/\1\])?/s', $content, $m, PREG_SET_ORDER ) ) {
        return $out;
    }
    $reg = function_exists( 'ce_media_registry' ) ? ce_media_registry() : [];
    $n   = 0;
    foreach ( $m as $hit ) {
        $atts    = shortcode_parse_atts( $hit[2] );
        $caption = ce_schema_text( (string) ( $hit[3] ?? '' ) );
        if ( 'ce_figure' === $hit[1] ) {
            $id = sanitize_key( (string) ( $atts['id'] ?? '' ) );
            if ( ! $id || ! isset( $reg[ $id ] ) ) {
                continue;
            }
            $out[] = ce_schema_media_image( $id, $reg[ $id ], ce_schema_id( $url, 'figure-' . ( ++$n ) ), $caption );
        } else {
            $id   = sanitize_file_name( (string) ( $atts['id'] ?? '' ) );
            $file = get_stylesheet_directory() . '/assets/diagrams/' . $id . '.svg';
            if ( ! $id || ! is_readable( $file ) ) {
                continue;
            }
            $src   = get_stylesheet_directory_uri() . '/assets/diagrams/' . $id . '.svg';
            $out[] = array_filter( [
                '@type'           => 'ImageObject',
                '@id'             => ce_schema_id( $url, 'figure-' . ( ++$n ) ),
                'contentUrl'      => $src,
                'url'             => $src,
                'encodingFormat'  => 'image/svg+xml',
                'caption'         => $caption,
                'creator'         => ce_schema_ref( ce_schema_org_id() ),
                'copyrightHolder' => ce_schema_ref( ce_schema_org_id() ),
            ] );
        }
    }
    return $out;
}

/**
 * The hero image as an ImageObject: the registry entry when the featured
 * image is one (featured_for names the article), else the share image.
 */
function ce_schema_hero_image( WP_Post $post, string $url, string $share_url ): array {
    $id = ce_schema_id( $url, 'primaryimage' );
    if ( function_exists( 'ce_media_registry' ) ) {
        foreach ( ce_media_registry() as $mid => $m ) {
            if ( ( $m['featured_for'] ?? '' ) === $post->post_name ) {
                $img = ce_schema_media_image( (string) $mid, $m, $id );
                if ( $share_url ) {
                    $img['url'] = $share_url;
                }
                $img['representativeOfPage'] = true;
                return $img;
            }
        }
    }
    return [
        '@type'                => 'ImageObject',
        '@id'                  => $id,
        'url'                  => $share_url,
        'contentUrl'           => $share_url,
        'representativeOfPage' => true,
    ];
}

/**
 * The Qur'an as a Book. Wikidata Q428 is the Qur'an.
 */
function ce_schema_quran_book(): array {
    return [
        '@type'      => 'Book',
        '@id'        => ce_schema_id( home_url( '/' ), 'quran' ),
        'name'       => 'The Qur\'an',
        'genre'      => 'Scripture',
        'inLanguage' => 'ar',
        'sameAs'     => 'https://www.wikidata.org/wiki/Q428',
    ];
}

/**
 * Hadith collections the references name, by pattern on the reference.
 *
 * @return array<string,string> key => collection name.
 */
function ce_schema_hadith_collections(): array {
    return [
        'bukhari'   => 'Ṣaḥīḥ al-Bukhārī',
        'muslim'    => 'Ṣaḥīḥ Muslim',
        'abu-dawud' => 'Sunan Abī Dāwūd',
        'tirmidhi'  => 'Sunan al-Tirmidhī',
        'nasai'     => 'Sunan al-Nasāʾī',
        'ibn-majah' => 'Sunan Ibn Mājah',
        'ahmad'     => 'Musnad Aḥmad',
        'muwatta'   => 'al-Muwaṭṭaʾ of Mālik',
    ];
}

/**
 * The collection key for one part of a hadith reference, or ''.
 */
function ce_schema_hadith_key( string $part ): string {
    $p = strtr( ce_schema_text( $part ), [
        'ā' => 'a', 'ī' => 'i', 'ū' => 'u', 'Ā' => 'A', 'Ī' => 'I', 'Ū' => 'U',
        'ḥ' => 'h', 'Ḥ' => 'H', 'ṣ' => 's', 'Ṣ' => 'S', 'ḍ' => 'd', 'Ḍ' => 'D',
        'ṭ' => 't', 'Ṭ' => 'T', 'ẓ' => 'z', 'Ẓ' => 'Z', 'ʿ' => '', 'ʾ' => '', "'" => '',
    ] );
    $p = strtolower( $p );
    $patterns = [
        'adab'      => '/adab al-mufrad/',
        'bukhari'   => '/sahih al-bukhari|^al-bukhari|^bukhari/',
        'muslim'    => '/sahih muslim/',
        'abu-dawud' => '/ab[iu] dawud/',
        'tirmidhi'  => '/tirmidhi/',
        'nasai'     => '/nasa[i]/',
        'ibn-majah' => '/ibn majah/',
        'ahmad'     => '/musnad ahmad/',
        'muwatta'   => '/muwatta/',
    ];
    foreach ( $patterns as $key => $re ) {
        if ( preg_match( $re, $p ) ) {
            return 'adab' === $key ? '' : $key;
        }
    }
    return '';
}

/**
 * Qur'an and hadith quotations in an article body (the quran-citation and
 * hadith-citation blocks). Each block gives two Quotations: the Arabic text
 * and the English translation shown under it (translationOfWork). Qur'an
 * passages sit in their surah (Chapter) in the Qur'an (Book); hadith sit in
 * the collections the reference names, with the hadith number as
 * identifier. A grading is marked only where the reference states one.
 *
 * @return array{nodes:array[],cited:string[]}
 */
function ce_schema_scripture( string $content, string $url ): array {
    $nodes = [];
    $cited = [];
    $books = [];
    $re    = '#<div class="(quran|hadith)-citation">\s*<div class="\1-arabic"[^>]*>(.*?)</div>\s*<div class="\1-translation">(.*?)</div>\s*<div class="\1-ref">(.*?)</div>\s*</div>#s';
    if ( ! preg_match_all( $re, $content, $m, PREG_SET_ORDER ) ) {
        return [ 'nodes' => [], 'cited' => [] ];
    }
    $n = 0;
    foreach ( $m as $hit ) {
        $kind  = $hit[1];
        $ar    = ce_schema_text( $hit[2] );
        $en    = trim( ce_schema_text( $hit[3] ), " \"“”" );
        $ref   = ce_schema_text( $hit[4] );
        $n++;
        $ar_id = ce_schema_id( $url, $kind . '-' . $n . '-ar' );
        $en_id = ce_schema_id( $url, $kind . '-' . $n );
        $arabic = [
            '@type'      => 'Quotation',
            '@id'        => $ar_id,
            'text'       => $ar,
            'inLanguage' => 'ar',
            'name'       => $ref,
        ];
        $english = [
            '@type'             => 'Quotation',
            '@id'               => $en_id,
            'text'              => $en,
            'inLanguage'        => 'en',
            'name'              => $ref,
            'translationOfWork' => ce_schema_ref( $ar_id ),
        ];
        $arabic['workTranslation'] = ce_schema_ref( $en_id );

        if ( 'quran' === $kind ) {
            $quran = ce_schema_quran_book();
            $books[ $quran['@id'] ] = $quran;
            if ( preg_match( '/^(.*?)\s*\(?(\d{1,3}):(\d{1,3}(?:[–-]\d{1,3})?)\)?/u', $ref, $r ) ) {
                $surah = (int) $r[2];
                $name  = trim( preg_replace( '/^(Sūrat|Surah)\s+/u', '', trim( $r[1], ' ,' ) ) );
                $ch_id = ce_schema_id( home_url( '/' ), 'quran-' . $surah );
                $chapter = array_filter( [
                    '@type'    => 'Chapter',
                    '@id'      => $ch_id,
                    'name'     => '' !== $name ? 'Sūrat ' . preg_replace( '/^Al-/u', 'al-', $name ) : null,
                    'position' => $surah,
                    'isPartOf' => ce_schema_ref( $quran['@id'] ),
                ] );
                $books[ $ch_id ]     = $chapter;
                $arabic['isPartOf']  = ce_schema_ref( $ch_id );
                $arabic['identifier'] = $surah . ':' . $r[3];
                $english['identifier'] = $surah . ':' . $r[3];
            } else {
                $arabic['isPartOf'] = ce_schema_ref( $quran['@id'] );
            }
        } else {
            $parts = [];
            $ids   = [];
            foreach ( preg_split( '/[;,]\s*(?=[^\d])/u', $ref ) as $piece ) {
                $key = ce_schema_hadith_key( $piece );
                if ( '' === $key ) {
                    continue;
                }
                $bid = ce_schema_id( home_url( '/' ), 'hadith-' . $key );
                $books[ $bid ] = [
                    '@type'      => 'Book',
                    '@id'        => $bid,
                    'name'       => ce_schema_hadith_collections()[ $key ],
                    'genre'      => 'Hadith collection',
                    'inLanguage' => 'ar',
                ];
                $parts[] = ce_schema_ref( $bid );
                if ( preg_match( '/(\d+(?::\d+)?)\s*(?:\(|$)/u', trim( ce_schema_text( $piece ) ), $num ) ) {
                    $ids[] = ce_schema_hadith_collections()[ $key ] . ' ' . $num[1];
                }
            }
            if ( $parts ) {
                $arabic['isPartOf'] = 1 === count( $parts ) ? $parts[0] : $parts;
            }
            if ( $ids ) {
                $arabic['identifier'] = 1 === count( $ids ) ? $ids[0] : $ids;
            }
            if ( preg_match( '/(?:graded|classified|chain)\s+(?:<em>)?([\p{L}ʿʾ\-]+)/u', strip_tags( $hit[4], '<em>' ), $g ) ) {
                $arabic['additionalProperty'] = [
                    '@type' => 'PropertyValue',
                    'name'  => 'grading',
                    'value' => $g[1],
                ];
            }
        }
        $nodes[] = $arabic;
        $nodes[] = $english;
        $cited[] = $en_id;
    }
    return [ 'nodes' => array_merge( array_values( $books ), $nodes ), 'cited' => $cited ];
}

/**
 * Glossary terms an article links to: the [glossary] shortcode, and the
 * tooltip links the content filter adds (ce_unified_link_filter() in
 * inc/ce-crosslinks.php, with its own limits and placement rules). Both
 * link to the term's anchor on the glossary page, where the DefinedTerm is
 * defined; the article refers to it by @id.
 *
 * @return array[] DefinedTerm nodes (id, name, url) for the graph.
 */
function ce_schema_glossary_mentions( string $content ): array {
    if ( ! function_exists( 'ce_get_glossary_terms' ) ) {
        return [];
    }
    $names = preg_match_all( '/\[glossary\s+term="([^"]+)"\s*\]/', $content, $m ) ? $m[1] : [];
    $linked = function_exists( 'ce_unified_link_filter' ) ? (string) ce_unified_link_filter( $content ) : '';
    $slugs  = preg_match_all( '#class="ce-glossary-term"#', $linked ) && preg_match_all( '#/glossary/\#term-([a-z0-9\-]+)"#', $linked, $l ) ? $l[1] : [];
    if ( ! $names && ! $slugs ) {
        return [];
    }
    $known = [];
    foreach ( ce_get_glossary_terms() as $t ) {
        $known[ strtolower( $t['term'] ) ] = $t;
    }
    foreach ( ce_get_glossary_terms() as $t ) {
        if ( in_array( sanitize_title( $t['term'] ), $slugs, true ) ) {
            $names[] = $t['term'];
        }
    }
    $out = [];
    foreach ( array_unique( $names ) as $name ) {
        $t = $known[ strtolower( $name ) ] ?? null;
        if ( ! $t ) {
            continue;
        }
        $out[ $t['term'] ] = [
            '@type'            => 'DefinedTerm',
            '@id'              => ce_schema_term_id( $t['term'] ),
            'name'             => $t['term'],
            'description'      => (string) $t['def'],
            'url'              => ce_schema_term_id( $t['term'] ),
            'inDefinedTermSet' => [
                '@type' => 'DefinedTermSet',
                '@id'   => ce_schema_id( ce_schema_glossary_url(), 'glossary' ),
                'name'  => 'Compelling Evidence Glossary of Islamic Terms',
                'url'   => ce_schema_glossary_url(),
            ],
        ];
    }
    return array_values( $out );
}

/**
 * Add figures, scripture and glossary mentions to an Article and return the
 * extra nodes they need.
 *
 * @param array   $article Article node (changed in place).
 * @param WP_Post $post    The article.
 * @return array[] Extra nodes.
 */
function ce_schema_article_details( array &$article, WP_Post $post ): array {
    $content = (string) $post->post_content;
    $url     = (string) $article['url'];
    $extra   = [];

    $figures = ce_schema_figures( $content, $url );
    if ( $figures ) {
        $extra                       = array_merge( $extra, $figures );
        $article['associatedMedia'] = array_map( static function ( $f ) {
            return ce_schema_ref( $f['@id'] );
        }, $figures );
    }

    $scripture = ce_schema_scripture( $content, $url );
    if ( $scripture['cited'] ) {
        $extra               = array_merge( $extra, $scripture['nodes'] );
        $article['citation'] = array_map( 'ce_schema_ref', $scripture['cited'] );
    }

    $terms = ce_schema_glossary_mentions( $content );
    if ( $terms ) {
        $extra                = array_merge( $extra, $terms );
        $article['mentions']  = array_merge(
            (array) ( $article['mentions'] ?? [] ),
            array_map( static function ( $t ) {
                return ce_schema_ref( $t['@id'] );
            }, $terms )
        );
    }
    return $extra;
}

/* ═══════════════════════════════════════════════════════════════════════
   ONE GRAPH
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * True for a list (0-based sequential keys).
 */
function ce_schema_is_list( array $a ): bool {
    return array_keys( $a ) === range( 0, count( $a ) - 1 );
}

/**
 * Fill the empty properties of $into from $from. Lists of references are
 * joined without repeats.
 */
function ce_schema_fill( array $into, array $from ): array {
    foreach ( $from as $k => $v ) {
        if ( '@id' === $k || '@context' === $k ) {
            continue;
        }
        if ( '@type' === $k ) {
            // A plain WebPage takes the page's own subtype (QAPage, ...).
            if ( ( $into['@type'] ?? '' ) === 'WebPage' && is_string( $v ) && 'WebPage' !== $v ) {
                $into['@type'] = $v;
            }
            continue;
        }
        if ( ! array_key_exists( $k, $into ) || null === $into[ $k ] || '' === $into[ $k ] || [] === $into[ $k ] ) {
            $into[ $k ] = $v;
        } elseif ( 'hasPart' === $k && is_array( $into[ $k ] ) && is_array( $v ) && ce_schema_is_list( $into[ $k ] ) && ce_schema_is_list( $v ) ) {
            $into[ $k ] = array_values( array_unique( array_merge( $into[ $k ], $v ), SORT_REGULAR ) );
        }
    }
    return $into;
}

/**
 * Drop @context from nodes and merge nodes that share an @id.
 *
 * @param array[] $nodes Nodes.
 * @return array[]
 */
function ce_schema_finalize( array $nodes ): array {
    $out = [];
    $pos = [];
    foreach ( $nodes as $node ) {
        if ( ! is_array( $node ) || empty( $node['@type'] ) ) {
            continue;
        }
        unset( $node['@context'] );
        $id = (string) ( $node['@id'] ?? '' );
        if ( '' !== $id && isset( $pos[ $id ] ) ) {
            $out[ $pos[ $id ] ] = ce_schema_fill( $out[ $pos[ $id ] ], $node );
            continue;
        }
        if ( '' !== $id ) {
            $pos[ $id ] = count( $out );
        }
        $out[] = $node;
    }
    return $out;
}

/**
 * The whole graph as a JSON-LD document.
 */
function ce_schema_graph(): array {
    return [
        '@context' => 'https://schema.org',
        '@graph'   => ce_schema_nodes(),
    ];
}

/**
 * The graph as one <script> block.
 */
function ce_schema_script(): string {
    $graph = ce_schema_graph();
    if ( empty( $graph['@graph'] ) ) {
        return '';
    }
    return '<script type="application/ld+json">' . "\n"
        . wp_json_encode( $graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_HEX_TAG )
        . "\n</script>\n";
}
