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
   META DESCRIPTION
   ═══════════════════════════════════════════════════════════════════════ */

function ce_meta_description() {
    $desc = '';

    if ( is_singular( 'ce_article' ) || is_singular( 'post' ) ) {
        $desc = get_the_excerpt();
        if ( ! $desc ) {
            $desc = wp_trim_words( strip_tags( get_the_content() ), 30, '...' );
        }
    } elseif ( is_post_type_archive( 'ce_article' ) ) {
        $desc = 'Every serious objection. Every core argument. Written for the honest inquirer. Compelling Evidence articles explore the questions that matter most.';
    } elseif ( is_tax( 'ce_topic' ) ) {
        $term = get_queried_object();
        $desc = $term->description ?: 'Articles on ' . $term->name . ' — honest, evidence-based inquiry from Compelling Evidence.';
    } elseif ( is_front_page() || is_home() ) {
        $desc = 'Honest, evidence-based inquiry into the questions that matter most — written for the curious, the doubtful, and the unconvinced.';
    } elseif ( is_search() ) {
        $desc = 'Search results on Compelling Evidence — exploring questions about God, Islam, and existence.';
    } elseif ( is_page() ) {
        $desc = get_the_excerpt() ?: wp_trim_words( strip_tags( get_the_content() ), 30, '...' );
    }

    if ( $desc ) {
        $desc = esc_attr( wp_strip_all_tags( $desc ) );
        echo '<meta name="description" content="' . $desc . '">' . "\n";
    }
}
add_action( 'wp_head', 'ce_meta_description', 1 );


/* ═══════════════════════════════════════════════════════════════════════
   CANONICAL URL
   ═══════════════════════════════════════════════════════════════════════ */

function ce_canonical_url() {
    if ( is_singular() ) {
        $url = get_permalink();
    } elseif ( is_post_type_archive( 'ce_article' ) ) {
        $url = get_post_type_archive_link( 'ce_article' );
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
    $site_name = 'Compelling Evidence';
    $locale    = get_locale();
    $og_type   = 'website';
    $title     = '';
    $desc      = '';
    $url       = '';
    $image     = get_stylesheet_directory_uri() . '/screenshot.png';

    if ( is_singular( 'ce_article' ) || is_singular( 'post' ) ) {
        $og_type = 'article';
        $title   = get_the_title();
        $desc    = get_the_excerpt() ?: wp_trim_words( strip_tags( get_the_content() ), 30 );
        $url     = get_permalink();

        if ( has_post_thumbnail() ) {
            $image = get_the_post_thumbnail_url( null, 'large' );
        }

        // Article-specific OG
        $topics = wp_get_post_terms( get_the_ID(), 'ce_topic' );
        if ( $topics && ! is_wp_error( $topics ) ) {
            echo '<meta property="article:section" content="' . esc_attr( $topics[0]->name ) . '">' . "\n";
        }
        echo '<meta property="article:published_time" content="' . get_the_date( 'c' ) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . get_the_modified_date( 'c' ) . '">' . "\n";

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

    $title = esc_attr( wp_strip_all_tags( $title ) );
    $desc  = esc_attr( wp_strip_all_tags( $desc ) );
    ?>
    <!-- Open Graph -->
    <meta property="og:type" content="<?php echo $og_type; ?>">
    <meta property="og:title" content="<?php echo $title; ?>">
    <meta property="og:description" content="<?php echo $desc; ?>">
    <meta property="og:url" content="<?php echo esc_url( $url ); ?>">
    <meta property="og:site_name" content="<?php echo $site_name; ?>">
    <meta property="og:locale" content="<?php echo $locale; ?>">
    <meta property="og:image" content="<?php echo esc_url( $image ); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo $title; ?>">
    <meta name="twitter:description" content="<?php echo $desc; ?>">
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
        'potentialAction' => [
            '@type'       => 'SearchAction',
            'target'      => home_url( '/?s={search_term_string}' ),
            'query-input' => 'required name=search_term_string',
        ],
    ];

    // ── Organization schema ──
    $schemas[] = [
        '@type' => 'Organization',
        'name'  => 'Compelling Evidence',
        'url'   => home_url( '/' ),
        'logo'  => get_stylesheet_directory_uri() . '/screenshot.png',
    ];

    // ── Article schema (single articles) ──
    if ( is_singular( 'ce_article' ) || is_singular( 'post' ) ) {
        $word_count = str_word_count( strip_tags( get_the_content() ) );
        $topics     = wp_get_post_terms( get_the_ID(), 'ce_topic' );
        $topic_name = ( $topics && ! is_wp_error( $topics ) ) ? $topics[0]->name : 'General';

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
            'author'           => [
                '@type' => 'Organization',
                'name'  => 'Compelling Evidence',
            ],
            'publisher'        => [
                '@type' => 'Organization',
                'name'  => 'Compelling Evidence',
                'logo'  => [
                    '@type' => 'ImageObject',
                    'url'   => get_stylesheet_directory_uri() . '/screenshot.png',
                ],
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id'   => get_permalink(),
            ],
        ];

        if ( has_post_thumbnail() ) {
            $article['image'] = get_the_post_thumbnail_url( null, 'large' );
        }

        $schemas[] = $article;

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

    // ── FAQPage schema (front page — key questions section) ──
    if ( is_front_page() || is_home() ) {
        $faq_items = [];
        $faq_articles = get_posts([
            'post_type'      => 'ce_article',
            'posts_per_page' => 6,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
        ]);
        foreach ( $faq_articles as $p ) {
            $faq_items[] = [
                '@type'          => 'Question',
                'name'           => get_the_title( $p ),
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => wp_trim_words( strip_tags( $p->post_content ), 50 ),
                ],
            ];
        }
        if ( $faq_items ) {
            $schemas[] = [
                '@type'      => 'FAQPage',
                'mainEntity' => $faq_items,
            ];
        }
    }

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
function ce_custom_title( $title ) {
    if ( is_singular( 'ce_article' ) ) {
        $topics = wp_get_post_terms( get_the_ID(), 'ce_topic' );
        $topic  = ( $topics && ! is_wp_error( $topics ) ) ? $topics[0]->name : '';
        if ( $topic ) {
            $title['title'] = get_the_title() . ' — ' . $topic;
        }
    }
    return $title;
}
add_filter( 'document_title_parts', 'ce_custom_title' );

/**
 * Remove WordPress version from head for security.
 */
remove_action( 'wp_head', 'wp_generator' );
