<?php
/**
 * Compelling Evidence: article anatomy (2.18.10).
 *
 * The pieces a single article or post adds to what its template already
 * prints (the breadcrumbs, the byline when one is set, reading time, the
 * sticky contents, the share row, the sidebar and the related cards):
 *
 *   ce_anatomy_dates()       Published and Updated in the meta row.
 *   ce_anatomy_breadcrumbs() The trail on a plain post (single.php).
 *   ce_anatomy_answer_box()  "In short": the excerpt and the key points.
 *   ce_anatomy_after_content() The visible FAQ from the FAQ Items box, then
 *                            the sources: every Qur'an and hadith card the
 *                            article quotes, linked to its place, and the
 *                            article's own Sources list.
 *
 * The FAQPage markup is emitted only when the FAQ shows (ce-seo.php), as the
 * owner decided on 2026-11-03. The sources feed the Article citation
 * (ce-schema.php). Nothing here adds a name to the page.
 *
 * @package CompellingEvidence
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The article a piece is about: the queried article or post, when it has no
 * password.
 *
 * @return WP_Post|null
 */
function ce_anatomy_post() {
    if ( ! is_singular( [ 'ce_article', 'post' ] ) ) {
        return null;
    }
    $post = get_queried_object();
    return ( $post instanceof WP_Post && ! post_password_required( $post ) ) ? $post : null;
}

/**
 * Plain text of a piece of HTML, on one line.
 *
 * @param string $html HTML.
 * @return string
 */
function ce_anatomy_text( $html ) {
    return trim( (string) preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( (string) $html ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
}

/* ═══════════════════════════════════════════════════════════════════════
   META ROW AND BREADCRUMBS
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Prints the published date, and the updated date when the article changed a
 * day or more after it was published, for the .article-meta row.
 *
 * @param WP_Post|null $post Article; the current post when null.
 * @return void
 */
function ce_anatomy_dates( $post = null ) {
    $post = get_post( $post );
    if ( ! $post ) {
        return;
    }
    $format = (string) get_option( 'date_format' );
    printf(
        '<span class="meta-sep" aria-hidden="true">·</span><span class="meta-date">%1$s <time datetime="%2$s">%3$s</time></span>',
        esc_html__( 'Published', 'compelling-evidence' ),
        esc_attr( (string) get_post_time( 'c', true, $post ) ),
        esc_html( (string) get_the_date( $format, $post ) )
    );
    if ( (int) get_post_modified_time( 'U', true, $post ) - (int) get_post_time( 'U', true, $post ) >= DAY_IN_SECONDS ) {
        printf(
            '<span class="meta-sep" aria-hidden="true">·</span><span class="meta-date meta-date--updated">%1$s <time datetime="%2$s">%3$s</time></span>',
            esc_html__( 'Updated', 'compelling-evidence' ),
            esc_attr( (string) get_post_modified_time( 'c', true, $post ) ),
            esc_html( (string) get_the_modified_date( $format, $post ) )
        );
    }
}

/**
 * Prints the breadcrumb trail for a plain post, the same trail the
 * BreadcrumbList in ce-seo.php describes: Home, Articles, the topic, the post.
 *
 * @param WP_Post|null $post Post; the current post when null.
 * @return void
 */
function ce_anatomy_breadcrumbs( $post = null ) {
    $post = get_post( $post );
    if ( ! $post ) {
        return;
    }
    $topics  = wp_get_post_terms( $post->ID, 'ce_topic' );
    $archive = get_post_type_archive_link( 'ce_article' );
    echo '<nav class="ce-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'compelling-evidence' ) . '"><ol>';
    echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'compelling-evidence' ) . '</a></li>';
    if ( $archive ) {
        echo '<li><a href="' . esc_url( $archive ) . '">' . esc_html__( 'Articles', 'compelling-evidence' ) . '</a></li>';
    }
    if ( $topics && ! is_wp_error( $topics ) ) {
        $link = get_term_link( $topics[0] );
        if ( ! is_wp_error( $link ) ) {
            echo '<li><a href="' . esc_url( $link ) . '">' . esc_html( $topics[0]->name ) . '</a></li>';
        }
    }
    echo '<li aria-current="page">' . esc_html( get_the_title( $post ) ) . '</li></ol></nav>';
}

/* ═══════════════════════════════════════════════════════════════════════
   "IN SHORT"
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Key points from text: one a line, or separated by ";;".
 *
 * @param string $text Text.
 * @return string[]
 */
function ce_anatomy_points( $text ) {
    return array_values( array_filter( array_map( 'trim', (array) preg_split( '/\r\n|\r|\n|\s*;;\s*/', (string) $text ) ), 'strlen' ) );
}

/**
 * Whether the "In short" box shows: the article has an excerpt of its own or
 * key points.
 *
 * @param WP_Post|null $post Article.
 * @return bool
 */
function ce_anatomy_answer_shows( $post ) {
    // A post behind a password shows none of its text until the password is given.
    return $post instanceof WP_Post && ! post_password_required( $post ) && ( '' !== trim( (string) $post->post_excerpt ) || ce_anatomy_points( get_post_meta( $post->ID, '_ce_takeaways', true ) ) );
}

/**
 * Prints "In short": the excerpt as the summary, and the key points typed in
 * the Key points box. Nothing when the article has neither, or while its
 * password has not been given.
 *
 * @param WP_Post|null $post Article; the current post when null.
 * @return void
 */
function ce_anatomy_answer_box( $post = null ) {
    $post = get_post( $post );
    if ( ! ce_anatomy_answer_shows( $post ) ) {
        return;
    }
    $summary = ce_anatomy_text( $post->post_excerpt );
    $points  = ce_anatomy_points( get_post_meta( $post->ID, '_ce_takeaways', true ) );
    echo '<section class="ce-answer" aria-labelledby="ce-answer-title"><h2 id="ce-answer-title" class="ce-answer-title">' . esc_html__( 'In short', 'compelling-evidence' ) . '</h2>';
    if ( '' !== $summary ) {
        echo '<p class="ce-answer-summary">' . esc_html( $summary ) . '</p>';
    }
    if ( $points ) {
        echo '<ul class="ce-answer-points"><li>' . implode( '</li><li>', array_map( 'esc_html', $points ) ) . '</li></ul>';
    }
    echo '</section>';
}

/* ═══════════════════════════════════════════════════════════════════════
   SOURCES
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * The Qur'an and hadith cards of an article, in order: their kind and the
 * reference printed under each.
 *
 * @param string $content Stored or rendered text.
 * @return array List of array( kind, reference ).
 */
function ce_anatomy_passages( $content ) {
    if ( ! preg_match_all( '#<div class="(quran|hadith)-citation"[^>]*>.*?<div class="\1-ref">(.*?)</div>#s', (string) $content, $m, PREG_SET_ORDER ) ) {
        return [];
    }
    $out = [];
    foreach ( $m as $hit ) {
        $ref = ce_anatomy_text( $hit[2] );
        if ( '' !== $ref ) {
            $out[] = [ $hit[1], $ref ];
        }
    }
    return $out;
}

/**
 * The items of the article's own Sources list: the list that follows a
 * "Sources" heading, as plain text with the first web address in each.
 *
 * @param string $content Stored text.
 * @return array List of array( text, url ).
 */
function ce_anatomy_references( $content ) {
    // Block comments sit between the heading and the list in editor markup.
    $content = (string) preg_replace( '#<!--.*?-->#s', '', (string) $content );
    if ( ! preg_match( '#<h2[^>]*>\s*Sources\s*</h2>\s*<(ul|ol)[^>]*>(.*?)</\1>#is', (string) $content, $list ) ) {
        return [];
    }
    if ( ! preg_match_all( '#<li[^>]*>(.*?)</li>#is', $list[2], $items ) ) {
        return [];
    }
    $out = [];
    foreach ( $items[1] as $html ) {
        $text = ce_anatomy_text( $html );
        if ( '' !== $text ) {
            $out[] = [ $text, preg_match( '#href=["\'](https?://[^"\']+)["\']#i', $html, $u ) ? $u[1] : '' ];
        }
    }
    return $out;
}

/**
 * Whether the article's text carries a Sources heading of its own.
 *
 * @param string $content Stored text.
 * @return bool
 */
function ce_anatomy_has_own_sources( $content ) {
    return (bool) preg_match( '#<h2[^>]*>\s*Sources\s*</h2>#i', (string) preg_replace( '#<!--.*?-->#s', '', (string) $content ) );
}

/**
 * The article's own Sources list as CreativeWork citations, for the Article
 * node beside the Qur'an and hadith Quotations.
 *
 * @param string $content Stored text.
 * @return array[]
 */
function ce_anatomy_reference_citations( $content ) {
    $out = [];
    foreach ( ce_anatomy_references( $content ) as $ref ) {
        $node = [
            '@type' => 'CreativeWork',
            'name'  => $ref[0],
        ];
        if ( '' !== $ref[1] ) {
            $node['url'] = $ref[1];
        }
        $out[] = $node;
    }
    return $out;
}

/**
 * Gives each Qur'an and hadith card of the main text an id, passage-1 and on,
 * so the sources list can link to it.
 *
 * @param string $content Rendered text.
 * @return string
 */
function ce_anatomy_passage_ids( $content ) {
    $post = ce_anatomy_post();
    if ( ! $post || is_admin() || is_feed() || ! is_main_query() || doing_filter( 'get_the_excerpt' ) || (int) get_the_ID() !== $post->ID ) {
        return $content;
    }
    // A post split by <!--nextpage--> numbers its cards across all its pages,
    // as the sources list does, so each page starts after the cards before it.
    $n    = 0;
    $page = max( 1, (int) get_query_var( 'page' ) );
    if ( $page > 1 ) {
        $before = array_slice( explode( '<!--nextpage-->', (string) $post->post_content ), 0, $page - 1 );
        $n      = (int) preg_match_all( '#<div class="(?:quran|hadith)-citation"[^>]*>#', implode( '', $before ) );
    }
    return (string) preg_replace_callback(
        '#<div class="(quran|hadith)-citation"([^>]*)>#',
        static function ( $m ) use ( &$n ) {
            ++$n;
            // The card's other attributes stay; one that already has an id is replaced by the passage id.
            $rest = (string) preg_replace( '/\s+id\s*=\s*("[^"]*"|\'[^\']*\')/i', '', (string) $m[2] );
            return '<div class="' . $m[1] . '-citation" id="passage-' . $n . '"' . $rest . '>';
        },
        (string) $content
    );
}
add_filter( 'the_content', 'ce_anatomy_passage_ids', 20 );

/**
 * The address of page $page of a post split by <!--nextpage-->.
 *
 * @param WP_Post $post Post.
 * @param int     $page Page number, from 1.
 * @return string
 */
function ce_anatomy_page_url( $post, $page ) {
    $url = (string) get_permalink( $post );
    if ( $page < 2 ) {
        return $url;
    }
    return get_option( 'permalink_structure' ) ? trailingslashit( $url ) . user_trailingslashit( (string) $page, 'single_paged' ) : add_query_arg( 'page', $page, $url );
}

/**
 * The sources section: the passages quoted, each linked to its card. When the
 * article has its own Sources list, that list stays where the author put it
 * and this section is headed "Passages quoted".
 *
 * @param WP_Post $post Article.
 * @return string
 */
function ce_anatomy_sources_section( $post ) {
    $passages = ce_anatomy_passages( $post->post_content );
    if ( ! $passages ) {
        return '';
    }
    $title = ce_anatomy_has_own_sources( $post->post_content ) ? __( 'Passages quoted', 'compelling-evidence' ) : __( 'Sources', 'compelling-evidence' );
    // On a post split by <!--nextpage-->, a passage on another page is linked on that page.
    $pages   = explode( '<!--nextpage-->', (string) $post->post_content );
    $on_page = [];
    foreach ( $pages as $k => $chunk ) {
        $on_page = array_merge( $on_page, array_fill( 0, count( ce_anatomy_passages( $chunk ) ), $k + 1 ) );
    }
    $current = max( 1, (int) get_query_var( 'page' ) );
    $items   = '';
    foreach ( $passages as $i => $p ) {
        $page = $on_page[ $i ] ?? 1;
        $href = ( count( $pages ) > 1 && $page !== $current ? ce_anatomy_page_url( $post, $page ) : '' ) . '#passage-' . ( $i + 1 );
        // The reference names the work, so no label is added: a card styled as
        // hadith can carry a commentary, and a label would misname it.
        $items .= '<li><a href="' . esc_url( $href ) . '">' . esc_html( $p[1] ) . '</a></li>';
    }
    return '<section class="ce-sources" aria-labelledby="ce-sources-title"><h2 id="ce-sources-title">' . esc_html( $title ) . '</h2><p class="ce-sources-note">' . esc_html__( 'Every passage this article quotes, in the order it appears. Each link goes to the passage in the text.', 'compelling-evidence' ) . '</p><ol class="ce-sources-list">' . $items . '</ol></section>';
}

/* ═══════════════════════════════════════════════════════════════════════
   FAQ
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * The FAQ items of an article that are complete, as saved in the FAQ Items
 * box (inc/ce-aeo.php).
 *
 * @param WP_Post|null $post Article.
 * @return array List of array( q, a ).
 */
function ce_anatomy_faq_items( $post ) {
    if ( ! $post instanceof WP_Post ) {
        return [];
    }
    $items = get_post_meta( $post->ID, '_ce_faq_items', true );
    $out   = [];
    foreach ( is_array( $items ) ? $items : [] as $item ) {
        $q = trim( (string) ( $item['q'] ?? '' ) );
        $a = trim( (string) ( $item['a'] ?? '' ) );
        if ( '' !== $q && '' !== $a ) {
            $out[] = [ 'q' => $q, 'a' => $a ];
        }
    }
    return $out;
}

/**
 * Whether the FAQ shows on the queried article: the page then carries the
 * FAQPage markup for the same questions.
 *
 * @return bool
 */
function ce_anatomy_faq_shows() {
    return (bool) ce_anatomy_faq_items( ce_anatomy_post() );
}

/**
 * The visible FAQ: each saved question with its answer.
 *
 * @param WP_Post $post Article.
 * @return string
 */
function ce_anatomy_faq_section( $post ) {
    $items = ce_anatomy_faq_items( $post );
    if ( ! $items ) {
        return '';
    }
    $out = '<section class="ce-faq" id="faq" aria-labelledby="ce-faq-title"><h2 id="ce-faq-title">' . esc_html__( 'Questions readers ask', 'compelling-evidence' ) . '</h2>';
    foreach ( $items as $item ) {
        $out .= '<details class="ce-faq-item"><summary>' . esc_html( $item['q'] ) . '</summary><div class="ce-faq-answer">' . wpautop( esc_html( $item['a'] ) ) . '</div></details>';
    }
    return $out . '</section>';
}

/**
 * Prints what follows the text of an article: the FAQ, then the sources.
 *
 * @return void
 */
function ce_anatomy_after_content() {
    $post = ce_anatomy_post();
    if ( ! $post ) {
        return;
    }
    echo ce_anatomy_faq_section( $post ) . ce_anatomy_sources_section( $post ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped where built.
}

/* ═══════════════════════════════════════════════════════════════════════
   THE KEY POINTS BOX
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Adds the Key points box to the article and post screens.
 *
 * @return void
 */
function ce_anatomy_add_meta_box() {
    foreach ( [ 'ce_article', 'post' ] as $type ) {
        add_meta_box( 'ce_anatomy_points', __( 'Key points', 'compelling-evidence' ), 'ce_anatomy_meta_box', $type, 'normal', 'default' );
    }
}
add_action( 'add_meta_boxes', 'ce_anatomy_add_meta_box' );

/**
 * The Key points box.
 *
 * @param WP_Post $post Post.
 * @return void
 */
function ce_anatomy_meta_box( $post ) {
    wp_nonce_field( 'ce_anatomy_save', 'ce_anatomy_nonce' );
    ?>
    <p>
        <label for="ce-takeaways"><strong><?php esc_html_e( 'Key points, one a line', 'compelling-evidence' ); ?></strong></label>
        <textarea id="ce-takeaways" name="ce_takeaways" rows="4" class="widefat"><?php echo esc_textarea( (string) get_post_meta( $post->ID, '_ce_takeaways', true ) ); ?></textarea>
        <span class="description"><?php esc_html_e( 'Shown in the "In short" box at the top of the article, under the excerpt.', 'compelling-evidence' ); ?></span>
    </p>
    <?php
}

/**
 * Saves the Key points box.
 *
 * @param int $post_id Post.
 * @return void
 */
function ce_anatomy_save( $post_id ) {
    if ( ! isset( $_POST['ce_anatomy_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ce_anatomy_nonce'] ) ), 'ce_anatomy_save' ) ) {
        return;
    }
    if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    $value = implode( "\n", ce_anatomy_points( sanitize_textarea_field( wp_unslash( $_POST['ce_takeaways'] ?? '' ) ) ) );
    if ( '' !== $value ) {
        update_post_meta( $post_id, '_ce_takeaways', $value );
    } else {
        delete_post_meta( $post_id, '_ce_takeaways' );
    }
}
add_action( 'save_post_ce_article', 'ce_anatomy_save' );
add_action( 'save_post_post', 'ce_anatomy_save' );

/**
 * Loads the anatomy styles on single articles and posts.
 *
 * @return void
 */
function ce_anatomy_assets() {
    if ( is_singular( [ 'ce_article', 'post' ] ) ) {
        $file = get_stylesheet_directory() . '/assets/css/ce-article-anatomy.css';
        wp_enqueue_style( 'ce-article-anatomy', get_stylesheet_directory_uri() . '/assets/css/ce-article-anatomy.css', [ 'ce-main' ], is_readable( $file ) ? (string) filemtime( $file ) : null );
    }
}
add_action( 'wp_enqueue_scripts', 'ce_anatomy_assets', 20 );
