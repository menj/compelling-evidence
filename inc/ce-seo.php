<?php
/**
 * Compelling Evidence — SEO & Schema Markup
 *
 * - JSON-LD structured data (Article, FAQPage, WebSite, BreadcrumbList, Organization)
 * - Open Graph meta tags
 * - Twitter Card meta tags
 * - Canonical URLs
 * - Print stylesheet
 * - Meta description
 *
 * @since 1.9.1
 */

if ( ! defined( 'ABSPATH' ) ) exit;


/* ═══════════════════════════════════════════════════════════════════════
   IDENTITY HELPERS — Read author/organization data from theme options.
   Single source of truth for items 2 (Person schema), 3 (visible byline),
   4 (Organization schema), and 17 (twitter:site meta).
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Returns the configured author Person object for Article schema, or null
 * when no author name has been set. Reads from the Author & Identity tab
 * in CE Theme Options (ce_author_*).
 *
 * @return array|null Person schema array, or null if author identity is not configured.
 */
function ce_get_author_person_schema(): ?array {
    $name = trim( (string) get_option( 'ce_author_name', '' ) );
    if ( '' === $name ) {
        return null;
    }

    $person = [
        '@type' => 'Person',
        'name'  => $name,
    ];

    $url = trim( (string) get_option( 'ce_author_url', '' ) );
    if ( '' !== $url ) {
        $person['url'] = esc_url_raw( $url );
    }

    $title = trim( (string) get_option( 'ce_author_title', '' ) );
    if ( '' !== $title ) {
        $person['jobTitle'] = $title;
    }

    $bio = trim( (string) get_option( 'ce_author_bio', '' ) );
    if ( '' !== $bio ) {
        // Bio caps at 300 chars per the field description; enforce here too
        // in case the data was set before the cap or via WP-CLI.
        $person['description'] = mb_substr( wp_strip_all_tags( $bio ), 0, 300 );
    }

    $same_as = ce_get_author_same_as_urls();
    if ( ! empty( $same_as ) ) {
        $person['sameAs'] = $same_as;
    }

    // worksFor links the Person to the publishing Organization. Search
    // engines use this to attach the author's contributions to the site
    // entity in their knowledge graph.
    $person['worksFor'] = [
        '@type' => 'Organization',
        'name'  => 'Compelling Evidence',
        'url'   => home_url( '/' ),
    ];

    return $person;
}

/**
 * Collect all author social-profile URLs into a single deduplicated array
 * suitable for the Person schema's sameAs property.
 *
 * @return array<string>
 */
function ce_get_author_same_as_urls(): array {
    $urls = [];

    foreach ( [ 'ce_author_twitter', 'ce_author_youtube', 'ce_author_github', 'ce_author_linkedin' ] as $key ) {
        $val = trim( (string) get_option( $key, '' ) );
        if ( '' !== $val ) {
            $urls[] = esc_url_raw( $val );
        }
    }

    // Other URLs is a textarea — one URL per line. Trim, validate, dedupe.
    $other = (string) get_option( 'ce_author_other_urls', '' );
    if ( '' !== trim( $other ) ) {
        foreach ( preg_split( '/\r\n|\r|\n/', $other ) as $line ) {
            $line = trim( $line );
            if ( $line !== '' && filter_var( $line, FILTER_VALIDATE_URL ) ) {
                $urls[] = esc_url_raw( $line );
            }
        }
    }

    return array_values( array_unique( array_filter( $urls ) ) );
}

/**
 * Returns the URL of the appropriate share image for the current context.
 *
 * Prefers the dedicated 1200×630 'og-share' size registered in
 * ce_theme_setup(). Falls back to 'large' for posts uploaded before that
 * size existed, since add_image_size() only generates the new size for
 * subsequent uploads. Sites can run a regenerate-thumbnails plugin to
 * back-fill 'og-share' for older media.
 *
 * @param int|null $post_id Defaults to current post.
 * @return string Image URL or empty string if no thumbnail.
 */
function ce_get_share_image_url( $post_id = null ): string {
    if ( ! has_post_thumbnail( $post_id ) ) {
        return '';
    }

    $url = get_the_post_thumbnail_url( $post_id, 'og-share' );
    if ( ! $url ) {
        // og-share not yet generated for this attachment — fall back.
        $url = get_the_post_thumbnail_url( $post_id, 'large' );
    }

    return $url ?: '';
}


/**
 * Returns the Organization schema array used by the publisher field of
 * Article schema and as a standalone @graph entry on every page.
 *
 * @return array Organization schema.
 */
function ce_get_organization_schema(): array {
    $org = [
        '@type' => 'Organization',
        'name'  => 'Compelling Evidence',
        'url'   => home_url( '/' ),
    ];

    // Logo as a proper ImageObject — Google rich-result eligibility requires
    // width/height. Falls back to the theme screenshot if the theme option
    // hasn't been populated yet, preserving prior behaviour.
    $logo_url    = trim( (string) get_option( 'ce_org_logo_url', '' ) );
    $logo_width  = absint( get_option( 'ce_org_logo_width',  512 ) );
    $logo_height = absint( get_option( 'ce_org_logo_height', 512 ) );

    if ( '' === $logo_url ) {
        $logo_url    = get_stylesheet_directory_uri() . '/screenshot.png';
        $logo_width  = 512;
        $logo_height = 512;
    }

    $org['logo'] = [
        '@type'  => 'ImageObject',
        'url'    => esc_url_raw( $logo_url ),
        'width'  => $logo_width,
        'height' => $logo_height,
    ];

    // sameAs aggregates the site-level social profiles. The site-level
    // Twitter is stored as a handle (@something); convert to URL for sameAs.
    $org_same_as = [];
    $tw_handle   = trim( (string) get_option( 'ce_site_twitter_handle', '' ) );
    if ( '' !== $tw_handle ) {
        $org_same_as[] = 'https://twitter.com/' . ltrim( $tw_handle, '@' );
    }
    foreach ( [ 'ce_site_youtube_url', 'ce_site_facebook_url' ] as $key ) {
        $val = trim( (string) get_option( $key, '' ) );
        if ( '' !== $val ) {
            $org_same_as[] = esc_url_raw( $val );
        }
    }
    if ( ! empty( $org_same_as ) ) {
        $org['sameAs'] = array_values( array_unique( array_filter( $org_same_as ) ) );
    }

    // contactPoint is the Google-recommended contact channel. Email-only
    // ContactPoint is the simplest valid form per schema.org.
    $email = trim( (string) get_option( 'ce_site_contact_email', '' ) );
    if ( '' !== $email && is_email( $email ) ) {
        $org['contactPoint'] = [
            '@type'       => 'ContactPoint',
            'contactType' => 'editorial',
            'email'       => $email,
        ];
    }

    // founder links the Organization to the Person. Only emit when an
    // author has been configured — otherwise we'd be claiming unsourced
    // identity data on the Organization.
    $author_name = trim( (string) get_option( 'ce_author_name', '' ) );
    if ( '' !== $author_name ) {
        $org['founder'] = [
            '@type' => 'Person',
            'name'  => $author_name,
        ];
    }

    return $org;
}


/* ═══════════════════════════════════════════════════════════════════════
   META DESCRIPTION
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Truncate a description to a hard 160-character ceiling at the last word
 * boundary, with mb-string handling for Arabic/UTF-8 content. Google's
 * meta-description snippet truncates around 155-160 chars, so we enforce
 * the cap before output rather than letting Google cut us off mid-word.
 *
 * Used by ce_meta_description to enforce the cap on every page-type branch.
 *
 * @param string $text  Source text (already plain — no HTML expected).
 * @param int    $limit Hard character ceiling (default 160).
 * @return string
 */
function ce_trim_meta_description( string $text, int $limit = 160 ): string {
    $text = trim( preg_replace( '/\s+/u', ' ', $text ) );
    if ( '' === $text ) {
        return '';
    }
    if ( mb_strlen( $text, 'UTF-8' ) <= $limit ) {
        return $text;
    }
    // Reserve 1 char for the ellipsis we'll append, so the visible output
    // never exceeds $limit even with the ellipsis.
    $cut = mb_substr( $text, 0, $limit - 1, 'UTF-8' );
    $last_space = mb_strrpos( $cut, ' ', 0, 'UTF-8' );
    if ( false !== $last_space && $last_space > 0 ) {
        $cut = mb_substr( $cut, 0, $last_space, 'UTF-8' );
    }
    return rtrim( $cut, " \t\n\r\0\x0B,;:.-" ) . '…';
}


/**
 * Fit a description and its call to action within the site's limit.
 *
 * House rule: meta descriptions are at most 130 characters, call to action
 * included. The summary is cut at a word boundary to leave room for the CTA.
 *
 * @since 2.6.31
 */
function ce_meta_description_with_cta( string $summary, string $cta, int $limit = 130 ): string {
    $summary = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( $summary ) ) );
    $cta     = trim( $cta );
    $room    = $limit - ( '' === $cta ? 0 : mb_strlen( $cta, 'UTF-8' ) + 1 );
    if ( mb_strlen( $summary, 'UTF-8' ) > $room ) {
        $cut  = mb_substr( $summary, 0, $room - 1, 'UTF-8' );
        $last = mb_strrpos( $cut, ' ', 0, 'UTF-8' );
        if ( false !== $last && $last > 0 ) {
            $cut = mb_substr( $cut, 0, $last, 'UTF-8' );
        }
        $summary = rtrim( $cut, " ,;:.-" ) . '…';
    } elseif ( '' !== $summary && ! preg_match( '/[.!?…]$/u', $summary ) ) {
        $summary .= '.';
    }
    return trim( $summary . ' ' . $cta );
}

/**
 * Descriptions for theme-built pages whose editor content is empty or a
 * template placeholder (the Ask a Question page used to publish
 * "This page uses a custom template…" as its description).
 */
function ce_page_meta_descriptions(): array {
    return [
        'faq'                         => [ 'Straight answers about this site: who writes it, how articles are sourced and checked, and what it sets out to do.', 'Read the FAQ.' ],
        'glossary'                    => [ 'Plain definitions of the Arabic and Islamic terms used across the site, from ayah and hadith to tawhid.', 'Browse the glossary.' ],
        'quiz'                        => [ 'Ten questions that place you on one of fourteen reading paths, matched to where your doubts begin.', 'Take the quiz.' ],
        'journeys'                    => [ 'Fourteen guided reading paths, each written for a different starting point: atheist, agnostic, deist and more.', 'Choose a path.' ],
        'ask-a-question'              => [ 'Send a question about God, Islam or this site. Selected questions are answered in full and published.', 'Ask yours.' ],
        'my-progress'                 => [ 'Your reading progress across the journeys and articles on this site, stored in your own browser.', 'See your progress.' ],
    ];
}

function ce_meta_description() {
    // Rank Math owns meta descriptions when active; printing ours as well
    // puts two description tags on every page.
    if ( function_exists( 'ce_is_rankmath_active' ) && ce_is_rankmath_active() ) {
        return;
    }

    $summary = '';
    $cta     = '';

    if ( is_singular( 'ce_article' ) || is_singular( 'post' ) ) {
        $summary = get_the_excerpt() ?: wp_trim_words( strip_tags( get_the_content() ), 25, '' );
        $cta     = 'Read the argument.';
    } elseif ( is_singular( 'ce_question' ) ) {
        $summary = get_the_title() . ' ' . wp_trim_words( strip_tags( get_the_content() ), 18, '' );
        $cta     = 'Read the answer.';
    } elseif ( is_post_type_archive( 'ce_article' ) ) {
        $summary = 'Every serious objection to belief in God and Islam, with the strongest case on both sides, in 11 topics.';
        $cta     = 'Start reading.';
    } elseif ( is_post_type_archive( 'ce_question' ) ) {
        $summary = 'Questions sent in by readers about God, Islam and this site, each answered in full.';
        $cta     = 'Read the answers.';
    } elseif ( is_tax( 'ce_topic' ) ) {
        $term    = get_queried_object();
        $sep     = preg_match( '/[?!.]$/u', $term->name ) ? ' ' : ': ';
        $summary = ce_topic_description( $term ) ?: sprintf( '%s%s%d articles weighing the strongest objections against the evidence.', $term->name, $sep, (int) $term->count );
        $cta     = 'Explore the topic.';
    } elseif ( is_front_page() || is_home() ) {
        $summary = 'Honest, evidence-based inquiry into God, Islam and meaning, written for the curious and the unconvinced.';
        $cta     = 'Start here.';
    } elseif ( is_page() ) {
        $map  = ce_page_meta_descriptions();
        $slug = get_post_field( 'post_name', get_queried_object_id() );
        if ( isset( $map[ $slug ] ) ) {
            [ $summary, $cta ] = $map[ $slug ];
        } else {
            $raw = get_the_excerpt() ?: wp_trim_words( strip_tags( get_the_content() ), 25, '' );
            // Never publish template placeholders or HTML comments as a description.
            if ( ! preg_match( '/custom template|rendered by|redirected to/i', $raw ) ) {
                $summary = $raw;
                $cta     = 'Read more.';
            }
        }
    }
    // Search results and 404s carry no description: search pages are
    // noindexed and 404s are not indexed.

    if ( $summary ) {
        $desc = ce_meta_description_with_cta( $summary, $cta );
        echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'ce_meta_description', 1 );


/* ═══════════════════════════════════════════════════════════════════════
   CANONICAL URL
   ═══════════════════════════════════════════════════════════════════════ */

function ce_canonical_url() {
    // Rank Math prints its own canonical; two canonical tags cancel each other out.
    if ( function_exists( 'ce_is_rankmath_active' ) && ce_is_rankmath_active() ) {
        return;
    }
    if ( is_singular() ) {
        $url = get_permalink();
    } elseif ( is_post_type_archive( 'ce_article' ) ) {
        $url = get_post_type_archive_link( 'ce_article' );
    } elseif ( is_post_type_archive( 'ce_question' ) ) {
        $url = get_post_type_archive_link( 'ce_question' );
    } elseif ( is_tax( 'ce_topic' ) ) {
        $url = get_term_link( get_queried_object() );
    } elseif ( is_front_page() || is_home() ) {
        $url = home_url( '/' );
    } else {
        return;
    }

    if ( $url && ! is_wp_error( $url ) ) {
        echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'ce_canonical_url', 1 );


/* ═══════════════════════════════════════════════════════════════════════
   OPEN GRAPH + TWITTER CARDS
   ═══════════════════════════════════════════════════════════════════════ */

function ce_og_twitter_meta() {
    // Rank Math prints Open Graph and Twitter tags when active.
    if ( function_exists( 'ce_is_rankmath_active' ) && ce_is_rankmath_active() ) {
        return;
    }
    $site_name = 'Compelling Evidence';
    $locale    = get_locale();
    $og_type   = 'website';
    $title     = '';
    $desc      = '';
    $url       = '';
    $image     = get_stylesheet_directory_uri() . '/screenshot.png';
    // Track whether the OG image is a real per-post hero image vs. the
    // sitewide screenshot.png fallback. Used by the twitter:card type
    // selector below — Twitter expects summary_large_image only when
    // there's a substantive image to display, not a logo placeholder.
    $has_real_image = false;

    if ( is_singular( 'ce_article' ) || is_singular( 'post' ) ) {
        $og_type = 'article';
        $title   = get_the_title();
        $desc    = get_the_excerpt() ?: wp_trim_words( strip_tags( get_the_content() ), 30 );
        $url     = get_permalink();

        if ( has_post_thumbnail() ) {
            $image          = ce_get_share_image_url();
            $has_real_image = true;
        }

        // Article-specific OG
        $topics = wp_get_post_terms( get_the_ID(), 'ce_topic' );
        if ( $topics && ! is_wp_error( $topics ) ) {
            echo '<meta property="article:section" content="' . esc_attr( $topics[0]->name ) . '">' . "\n";
        }
        echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . "\n";

    } elseif ( is_post_type_archive( 'ce_article' ) ) {
        $title = 'Articles — The Evidence, Examined';
        $desc  = 'Every serious objection. Every core argument. Written for the honest inquirer.';
        $url   = get_post_type_archive_link( 'ce_article' );

    } elseif ( is_tax( 'ce_topic' ) ) {
        $term  = get_queried_object();
        $title = $term->name . ' — Compelling Evidence';
        $desc  = $term->description ?: 'Articles on ' . $term->name;
        $url   = get_term_link( $term );

    } elseif ( is_front_page() || is_home() ) {
        $title = 'Facts. Figures. God. — Compelling Evidence';
        $desc  = 'Honest, evidence-based inquiry into the questions that matter most.';
        $url   = home_url( '/' );

    } elseif ( is_page() ) {
        $title = get_the_title();
        $desc  = get_the_excerpt() ?: wp_trim_words( strip_tags( get_the_content() ), 30 );
        $url   = get_permalink();

    } else {
        return;
    }

    $title = wp_strip_all_tags( $title );
    $desc  = wp_strip_all_tags( $desc );
    ?>
    <!-- Open Graph -->
    <meta property="og:type" content="<?php echo esc_attr( $og_type ); ?>">
    <meta property="og:title" content="<?php echo esc_attr( $title ); ?>">
    <meta property="og:description" content="<?php echo esc_attr( $desc ); ?>">
    <meta property="og:url" content="<?php echo esc_url( $url ); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>">
    <meta property="og:locale" content="<?php echo esc_attr( $locale ); ?>">
    <meta property="og:image" content="<?php echo esc_url( $image ); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Card -->
    <?php
    // Item 18 — Conditional twitter:card type. Only declare summary_large_image
    // when the page actually has a per-post hero image worth displaying as a
    // large card. Pages without a featured image (using only the sitewide
    // screenshot.png fallback) declare the small-thumbnail summary card so
    // Twitter doesn't render an awkwardly-stretched logo as a hero banner.
    $tw_card_type = $has_real_image ? 'summary_large_image' : 'summary';
    ?>
    <meta name="twitter:card" content="<?php echo esc_attr( $tw_card_type ); ?>">
    <?php
    // twitter:site identifies the publisher account; twitter:creator identifies
    // the article author when their handle is configured. Both are optional but
    // strongly recommended for proper Twitter Card attribution.
    $tw_site = trim( (string) get_option( 'ce_site_twitter_handle', '' ) );
    if ( '' !== $tw_site ) {
        $tw_site_handle = '@' . ltrim( $tw_site, '@' );
        echo '<meta name="twitter:site" content="' . esc_attr( $tw_site_handle ) . '">' . "\n    ";
    }

    // Author Twitter URL → handle for twitter:creator. Only emit on article
    // singular views where there's an actual author to credit.
    if ( is_singular( [ 'ce_article', 'post' ] ) ) {
        $author_tw_url = trim( (string) get_option( 'ce_author_twitter', '' ) );
        if ( '' !== $author_tw_url ) {
            // Extract handle from URL like https://twitter.com/handle or https://x.com/handle
            if ( preg_match( '#(?:twitter\.com|x\.com)/(@?[A-Za-z0-9_]{1,15})#i', $author_tw_url, $m ) ) {
                $tw_creator = '@' . ltrim( $m[1], '@' );
                echo '<meta name="twitter:creator" content="' . esc_attr( $tw_creator ) . '">' . "\n    ";
            }
        }
    }
    ?>
    <meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>">
    <meta name="twitter:description" content="<?php echo esc_attr( $desc ); ?>">
    <meta name="twitter:image" content="<?php echo esc_url( $image ); ?>">
    <?php
}
add_action( 'wp_head', 'ce_og_twitter_meta', 2 );


/* ═══════════════════════════════════════════════════════════════════════
   JSON-LD STRUCTURED DATA
   ═══════════════════════════════════════════════════════════════════════ */

function ce_schema_jsonld() {
    $schemas = [];

    // ── WebSite schema (every page) ──
    $schemas[] = [
        '@type'           => 'WebSite',
        'name'            => 'Compelling Evidence',
        'url'             => home_url( '/' ),
        'description'     => 'Honest, evidence-based inquiry into the questions that matter most.',
        'inLanguage'      => get_locale(),
        // SearchAction removed in 2.6.31: Google retired the sitelinks search
        // box on 21 November 2024. WebSite itself stays (site names use it).
    ];

    // ── Organization schema (sitewide) ──
    // Single source: ce_get_organization_schema() builds the full Organization
    // object with logo ImageObject, sameAs profiles, contactPoint, and founder.
    $schemas[] = ce_get_organization_schema();

    // ── Article schema (single articles) ──
    // Skipped when Rank Math is active — Rank Math's class-jsonld.php emits
    // its own Article/BlogPosting schema on every singular post. Two
    // competing Article blocks confuses Search Console and dilutes the
    // canonical signal. Theme Article schema is the fallback for sites
    // running without an SEO plugin.
    //
    // The WebSite, Organization, BreadcrumbList, FAQPage, and TOC schemas
    // above remain — they're either complementary (BreadcrumbList,
    // FAQPage) or sitewide (WebSite, Organization) and Rank Math doesn't
    // duplicate them. Per-article schema is the single overlap point.
    $skip_article_schema = function_exists( 'ce_is_rankmath_active' ) && ce_is_rankmath_active();

    if ( ! $skip_article_schema && ( is_singular( 'ce_article' ) || is_singular( 'post' ) ) ) {
        $word_count = str_word_count( strip_tags( get_the_content() ) );
        $topics     = wp_get_post_terms( get_the_ID(), 'ce_topic' );
        $topic_name = ( $topics && ! is_wp_error( $topics ) ) ? $topics[0]->name : 'General';

        // Author: Person if Identity tab is configured, otherwise fall back
        // to Organization-as-author for backwards compatibility with sites
        // that haven't yet populated the new Author & Identity tab.
        $author_person = ce_get_author_person_schema();
        $article_author = $author_person ?: [
            '@type' => 'Organization',
            'name'  => 'Compelling Evidence',
            'url'   => home_url( '/' ),
        ];

        $article = [
            '@type'            => 'Article',
            'headline'         => get_the_title(),
            'description'      => get_the_excerpt() ?: wp_trim_words( strip_tags( get_the_content() ), 30 ),
            'url'              => get_permalink(),
            'datePublished'    => get_the_date( 'c' ),
            'dateModified'     => get_the_modified_date( 'c' ),
            'wordCount'        => $word_count,
            'articleSection'   => $topic_name,
            'inLanguage'       => get_locale(),
            'author'           => $article_author,
            'publisher'        => ce_get_organization_schema(),
            'mainEntityOfPage' => [
                '@type'      => 'WebPage',
                '@id'        => get_permalink(),
                'url'        => get_permalink(),
                'inLanguage' => get_locale(),
            ],
            // Item 8 — accessibility & access semantics. These help search
            // engines and answer engines understand the article is freely
            // readable text content, supports structural navigation, and
            // surfaces alt text on its imagery — all factors in inclusive
            // ranking and reader-mode rendering.
            'isAccessibleForFree'   => true,
            'accessMode'            => [ 'textual', 'visual' ],
            'accessibilityFeature'  => [
                'readingOrder',
                'structuralNavigation',
                'tableOfContents',
                'alternativeText',
            ],
        ];

        // Item 8 — keywords assembled from the article's ce_topic taxonomy
        // and any post tags. Schema.org expects either a single string or
        // an array; we use array form for clarity. Empty when neither
        // taxonomy yields terms.
        $kw = [];
        if ( $topics && ! is_wp_error( $topics ) ) {
            foreach ( $topics as $term ) {
                $kw[] = $term->name;
            }
        }
        $tags = wp_get_post_terms( get_the_ID(), 'post_tag' );
        if ( $tags && ! is_wp_error( $tags ) ) {
            foreach ( $tags as $tag ) {
                $kw[] = $tag->name;
            }
        }
        $kw = array_values( array_unique( array_filter( $kw ) ) );
        if ( ! empty( $kw ) ) {
            $article['keywords'] = $kw;
        }

        // Item 8 — timeRequired in ISO 8601 duration format (PT{n}M).
        // Reading speed of 200 wpm matches the visible reading-time UI
        // shown in single.php and single-ce_article.php.
        if ( $word_count > 0 ) {
            $reading_mins = max( 1, (int) ceil( $word_count / 200 ) );
            $article['timeRequired'] = 'PT' . $reading_mins . 'M';
        }

        if ( has_post_thumbnail() ) {
            $share_url = ce_get_share_image_url();
            // Item 9 — primaryImageOfPage links the WebPage entry to the
            // article's hero image as a proper ImageObject. The WebPage
            // schema is referenced by mainEntityOfPage, so search engines
            // resolve the chain: Article → WebPage → primaryImageOfPage.
            $article['image'] = $share_url;
            $article['mainEntityOfPage']['primaryImageOfPage'] = [
                '@type' => 'ImageObject',
                'url'   => $share_url,
            ];
        }

        // ── about / mentions entities (Item 12) ──
        // Read the comma-separated entity meta and convert to schema.org Thing
        // arrays. Empty meta produces empty arrays which we don't include.
        if ( function_exists( 'ce_aeo_parse_entities' ) ) {
            $about_csv    = (string) get_post_meta( get_the_ID(), '_ce_about',    true );
            $mentions_csv = (string) get_post_meta( get_the_ID(), '_ce_mentions', true );

            $about_entities    = ce_aeo_parse_entities( $about_csv );
            $mentions_entities = ce_aeo_parse_entities( $mentions_csv );

            if ( ! empty( $about_entities ) ) {
                $article['about'] = $about_entities;
            }
            if ( ! empty( $mentions_entities ) ) {
                $article['mentions'] = $mentions_entities;
            }
        }

        // Google recommends an image for Article; fall back to the share image.
        if ( empty( $article['image'] ) ) {
            $article['image'] = [ ce_get_share_image_url( get_the_ID() ) ?: get_stylesheet_directory_uri() . '/screenshot.png' ];
        }

        $schemas[] = $article;

        // Per-article FAQPage removed in 2.6.31: the _ce_faq_items answers are
        // stored in meta but never shown on the page, and structured data must
        // describe visible content.

        // ── BreadcrumbList ──
        $breadcrumbs = [
            ['name' => 'Home',     'url' => home_url( '/' )],
            ['name' => 'Articles', 'url' => get_post_type_archive_link( 'ce_article' )],
        ];
        if ( $topics && ! is_wp_error( $topics ) ) {
            $breadcrumbs[] = [
                'name' => $topics[0]->name,
                'url'  => get_term_link( $topics[0] ),
            ];
        }
        $breadcrumbs[] = ['name' => get_the_title(), 'url' => get_permalink()];

        $items = [];
        foreach ( $breadcrumbs as $i => $bc ) {
            $items[] = [
                '@type'    => 'ListItem',
                'position' => $i + 1,
                'name'     => $bc['name'],
                'item'     => $bc['url'],
            ];
        }
        $schemas[] = [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    // ── CollectionPage schema (article archive) ──
    if ( is_post_type_archive( 'ce_article' ) || is_tax( 'ce_topic' ) ) {
        $schemas[] = [
            '@type'       => 'CollectionPage',
            'name'        => is_tax() ? get_queried_object()->name . ' — Articles' : 'The Evidence, Examined',
            'description' => 'Every serious objection. Every core argument. Written for the honest inquirer.',
            'url'         => is_tax() ? get_term_link( get_queried_object() ) : get_post_type_archive_link( 'ce_article' ),
        ];
    }

    // Front-page FAQPage removed in 2.6.31. It marked up article titles as
    // questions and article excerpts as answers, none of which is visible as
    // Q&A on the page; Google requires marked-up content to be visible, and
    // FAQ rich results are limited to government and health sites.

    // ── Output ──
    if ( ! empty( $schemas ) ) {
        $output = [
            '@context' => 'https://schema.org',
            '@graph'   => $schemas,
        ];
        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode( $output, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
        echo "\n</script>\n";
    }
}
add_action( 'wp_head', 'ce_schema_jsonld', 3 );


/* ═══════════════════════════════════════════════════════════════════════
   PRINT STYLESHEET
   ═══════════════════════════════════════════════════════════════════════ */

function ce_print_styles() {
    $css_dir = get_stylesheet_directory_uri() . '/assets/css/';
    $ver     = wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'ce-print', $css_dir . 'print.css', [], $ver, 'print' );
}
add_action( 'wp_enqueue_scripts', 'ce_print_styles' );


/* ═══════════════════════════════════════════════════════════════════════
   ADDITIONAL SEO HOOKS
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Add hreflang for potential Malay translation.
 */
function ce_hreflang() {
    echo '<link rel="alternate" hreflang="en" href="' . esc_url( home_url( '/' ) ) . '">' . "\n";
    echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( home_url( '/' ) ) . '">' . "\n";
}
add_action( 'wp_head', 'ce_hreflang', 1 );

/**
 * Optimise document title.
 */
/**
 * Optimise document title for ce_article posts.
 *
 * Appends the article's primary topic to the title (e.g. "Title | Topic")
 * for clearer SERP listings on long-tail queries that match topic terms.
 *
 * Yields entirely to Rank Math when it's active. Rank Math owns the title
 * chain via its own `document_title_parts` filter and has its own
 * configurable post-title-with-taxonomy formatting; running both filters
 * on the same hook produces non-deterministic output (whichever filter
 * runs last wins) and causes silent override conflicts.
 */
function ce_custom_title( $title ) {
    // Defer to Rank Math when active. It registers as the class
    // `RankMath\Helper` (the namespace prefix matters), so a class_exists
    // check on either the namespace root or the helper covers both
    // installation modes.
    if ( class_exists( 'RankMath' ) || class_exists( 'RankMath\\Helper' ) ) {
        return $title;
    }
    if ( is_singular( 'ce_article' ) ) {
        $topics = wp_get_post_terms( get_the_ID(), 'ce_topic' );
        $topic  = ( $topics && ! is_wp_error( $topics ) ) ? $topics[0]->name : '';
        // Add the topic only while the full title stays brief (Google shows
        // roughly 60 characters). Long article titles stand alone.
        $with_topic = get_the_title() . ' | ' . $topic;
        if ( $topic && mb_strlen( $with_topic . ' – Compelling Evidence', 'UTF-8' ) <= 65 ) {
            $title['title'] = $with_topic;
        } elseif ( mb_strlen( get_the_title() . ' – Compelling Evidence', 'UTF-8' ) > 70 ) {
            // A long article title already fills the result line; the site
            // name would only be cut off, so leave it out.
            unset( $title['site'] );
        }
    }
    return $title;
}
add_filter( 'document_title_parts', 'ce_custom_title' );

/**
 * Remove WordPress version from head for security.
 */
remove_action( 'wp_head', 'wp_generator' );


/* ═══════════════════════════════════════════════════════════════════════
   ABSTRACT BOX PLUGIN COORDINATION (Item 24)

   When the Abstract Box plugin is installed (the [abstract] shortcode and
   matching Gutenberg block), it emits its own JSON-LD schema with the
   schema.org `abstract` property. We coordinate three things to keep the
   site's schema graph consistent:

   1. abstract_box_schema_type — match the schema @type the theme uses
      for Article schema (`Article`), so search engines see one consistent
      type rather than e.g. ScholarlyArticle from Abstract Box and Article
      from the theme.

   2. abstract_box_schema_payload — inject the same Person author the
      theme uses (from Day 1's ce_get_author_person_schema), so author
      identity is consistent across both schemas.

   3. The plugin's own filter — abstract_box_output_schema — could be
      used to suppress the plugin's emission entirely on posts where the
      theme's Article schema already includes the abstract via Day 2's
      `description` field. We don't suppress here, because the plugin's
      schema includes the abstract specifically (which the theme's
      Article.description field doesn't replicate verbatim) and Google
      treats them as compatible block types in the @graph.

   All filters degrade gracefully — if Abstract Box isn't installed, none
   of these filters fire and there's no overhead.
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Force Abstract Box to emit Article schema type so it matches the
 * theme's Article schema. Default Abstract Box type is configurable via
 * its admin UI; this filter overrides at runtime to keep both schemas
 * in alignment without requiring admin configuration.
 */
add_filter( 'abstract_box_schema_type', function ( $type, $post ) {
    return 'Article';
}, 10, 2 );

/**
 * Inject the theme's configured Person author into Abstract Box's
 * schema payload, replacing the plugin's default (which uses the WP
 * post_author display_name as a Person without sameAs/jobTitle/url).
 *
 * If no author is configured in the Identity tab, leave the plugin's
 * default in place — the WP author display_name is still better than
 * nothing.
 */
add_filter( 'abstract_box_schema_payload', function ( $schema, $post, $attrs, $shortcode_content ) {
    $author = function_exists( 'ce_get_author_person_schema' )
        ? ce_get_author_person_schema()
        : null;

    if ( $author ) {
        $schema['author'] = $author;
    }

    return $schema;
}, 10, 4 );


/* ═══════════════════════════════════════════════════════════════════════
   STANDALONE HTML (journeys, quiz): SEARCH-GUIDELINE CLEAN-UP (2.6.31)
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Bring a self-contained journey or quiz page in line with Google's search
 * guidelines before it is served.
 *
 *  - Canonical, og:url and JSON-LD pointed at /journeys/{key}/, a URL that
 *    301-redirects back to /journey/{key}/. A canonical that redirects is
 *    ignored; point it at the URL actually served.
 *  - Titles read "Compelling Evidence — X"; lead with the page's topic.
 *  - Each journey marks every screen title as <h1> (8 to 10 per page); keep
 *    the first, demote the rest to <h2> with the same classes.
 *  - JSON-LD FAQPage blocks (questions not shown as Q&A on the page) and
 *    Speakable (news publishers only) are removed; everything else stays.
 *
 * @param string $html      Page HTML.
 * @param string $permalink URL the page is served from.
 * @param string $key       Journey key (empty for the quiz).
 */
function ce_standalone_html_seo( string $html, string $permalink, string $key = '' ): string {
    $home = untrailingslashit( home_url() );

    if ( $key ) {
        $html = str_replace( 'https://compelling-evidence.com/journeys/' . $key . '/', $permalink, $html );
    }
    $html = str_replace( 'https://compelling-evidence.com', $home, $html );

    // Title: topic first, brand last.
    $html = preg_replace_callback( '#<title>\s*Compelling Evidence\s*[—–-]\s*(.*?)</title>#su', static function ( $m ) {
        return '<title>' . trim( $m[1] ) . ' – Compelling Evidence</title>';
    }, $html, 1 );

    // Meta description: house limit of 130 characters, call to action included.
    $html = preg_replace_callback( '#<meta name="description" content="([^"]*)"\s*/?>#u', static function ( $m ) use ( $key ) {
        $desc = ce_meta_description_with_cta( html_entity_decode( $m[1], ENT_QUOTES ), $key ? 'Start the path.' : 'Take the quiz.' );
        return '<meta name="description" content="' . esc_attr( $desc ) . '">';
    }, $html, 1 );

    // One <h1> per page.
    $seen = false;
    $html = preg_replace_callback( '#<h1(\b[^>]*)>(.*?)</h1>#su', static function ( $m ) use ( &$seen ) {
        if ( ! $seen ) {
            $seen = true;
            return $m[0];
        }
        return '<h2' . $m[1] . '>' . $m[2] . '</h2>';
    }, $html );

    // JSON-LD: drop FAQPage nodes and speakable properties.
    $html = preg_replace_callback( '#<script type="application/ld\+json">(.*?)</script>#su', static function ( $m ) {
        $data = json_decode( $m[1], true );
        if ( ! is_array( $data ) ) {
            return $m[0];
        }
        $clean = static function ( $node ) use ( &$clean ) {
            if ( ! is_array( $node ) ) {
                return $node;
            }
            unset( $node['speakable'] );
            $out = [];
            foreach ( $node as $k => $v ) {
                if ( is_array( $v ) && isset( $v['@type'] ) && in_array( $v['@type'], [ 'FAQPage', 'SpeakableSpecification' ], true ) ) {
                    continue;
                }
                $out[ $k ] = $clean( $v );
            }
            $is_list = array_keys( $node ) === range( 0, count( $node ) - 1 );
            return $is_list ? array_values( $out ) : $out;
        };
        if ( isset( $data['@type'] ) && in_array( $data['@type'], [ 'FAQPage' ], true ) ) {
            return '';
        }
        $data = $clean( $data );
        if ( isset( $data['@type'] ) && 'WebPage' === $data['@type'] && count( array_diff( array_keys( $data ), [ '@context', '@type' ] ) ) === 0 ) {
            return ''; // A WebPage block that only carried speakable.
        }
        return '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG ) . '</script>';
    }, $html );

    return $html;
}


/**
 * A topic's own description, ignoring the placeholder that content sync used
 * to write to every term ("Compelling Evidence article topic"), which made
 * all eleven topic pages share one meta description.
 *
 * @since 2.6.31
 */
function ce_topic_description( $term ): string {
    $d = trim( wp_strip_all_tags( (string) ( $term->description ?? '' ) ) );
    return ( '' === $d || 'Compelling Evidence article topic' === $d ) ? '' : $d;
}
