<?php
/**
 * Compelling Evidence — Contextual links to the sister sites
 *
 * The first mention of a phrase in an article's running text links to the
 * inner page that treats it, on this site (through the unified engine in
 * ce-crosslinks.php) or on one of the six sister sites (this pass).
 *
 * The phrase map is inc/data/network-links.json, a shared file kept
 * identical in all seven themes. Each phrase belongs to exactly one page
 * across the network; this site's entries feed ce_get_crosslink_phrases(),
 * every other site's entries feed this pass.
 *
 * Rules for sister links: whole phrase, first mention, each address once a
 * page, at most one added link per paragraph or list item (a paragraph that
 * already carries a crosslink or a glossary tooltip gets none), never inside
 * headings, links, quotations, citations, Quran or hadith blocks, figures,
 * tables or any Arabic, Hebrew, Greek or Syriac text.
 *
 * Configurable via Theme Options → Links & Tooltips → Sister-Site Links.
 *
 * @since 2.6.39
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! defined( 'CE_SITE_KEY' ) ) {
    define( 'CE_SITE_KEY', 'ce' );
}


/* ═══════════════════════════════════════════════════════════════════════
   MAP
   ═══════════════════════════════════════════════════════════════════════ */

function ce_ctx_post_types() {
    return (array) apply_filters( 'ce_contextual_links_post_types', [ 'ce_article', 'post', 'ce_question', 'page' ] );
}

function ce_ctx_lower( $text ) {
    $text = str_replace( [ "\u{2019}", '&#8217;', '&#039;', '&#39;', '&rsquo;' ], "'", (string) $text );
    return function_exists( 'mb_strtolower' ) ? mb_strtolower( $text, 'UTF-8' ) : strtolower( $text );
}

/**
 * The shared network map, decoded once a request.
 *
 * @return array{sites:array<string,string>,links:array<int,array{site:string,path:string,phrases:string[]}>}
 */
function ce_ctx_network_map() {
    static $map = null;
    if ( null !== $map ) {
        return $map;
    }
    $file = get_stylesheet_directory() . '/inc/data/network-links.json';
    $data = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
    if ( ! is_array( $data ) || ! isset( $data['sites'], $data['links'] ) || ! is_array( $data['sites'] ) || ! is_array( $data['links'] ) ) {
        $data = [ 'sites' => [], 'links' => [] ];
    }
    return $map = (array) apply_filters( 'ce_contextual_links_map', $data );
}

/**
 * Hosts of the sister sites (this site excluded), keyed by host.
 *
 * @return array<string,string> host => site key
 */
function ce_ctx_sister_hosts() {
    $hosts = [];
    foreach ( ce_ctx_network_map()['sites'] as $key => $base ) {
        if ( CE_SITE_KEY === $key ) continue;
        $host = strtolower( (string) wp_parse_url( $base, PHP_URL_HOST ) );
        if ( $host ) $hosts[ $host ] = $key;
    }
    return $hosts;
}

/**
 * Admin lines from the Links & Tooltips tab: "phrase | /articles/slug/" for
 * this site, "phrase | https://sister/path/" for a sister site. Lines to any
 * other host or any other local path are ignored.
 *
 * @return array{own:array<string,string>,sister:array<string,array{phrase:string,url:string}>}
 */
function ce_ctx_extra_lines() {
    static $cache = null;
    if ( null !== $cache ) {
        return $cache;
    }
    $own    = [];
    $sister = [];
    $hosts  = ce_ctx_sister_hosts();
    // No /u: on invalid UTF-8 a /u split returns false and every line is lost.
    foreach ( preg_split( '/\r\n|\r|\n/', (string) get_option( 'ce_contextual_links_extra', '' ) ) as $line ) {
        $parts = array_map( 'trim', explode( '|', $line, 2 ) );
        if ( count( $parts ) < 2 || '' === $parts[0] || '' === $parts[1] ) continue;
        [ $phrase, $target ] = $parts;
        $lc = ce_ctx_lower( $phrase );
        if ( preg_match( '#^/articles/([a-z0-9-]+)/?$#', $target, $m ) ) {
            if ( ! isset( $own[ $phrase ] ) && ! isset( $sister[ $lc ] ) ) $own[ $phrase ] = $m[1];
            continue;
        }
        if ( preg_match( '#^https://#i', $target ) ) {
            $host = strtolower( (string) wp_parse_url( $target, PHP_URL_HOST ) );
            if ( isset( $hosts[ $host ] ) && ! isset( $sister[ $lc ] ) ) {
                $sister[ $lc ] = [ 'phrase' => $phrase, 'url' => $target ];
            }
        }
    }
    return $cache = [ 'own' => $own, 'sister' => $sister ];
}

/**
 * This site's entries from the shared map, as phrase => article slug.
 *
 * @return array<string,string>
 */
function ce_ctx_own_map_phrases() {
    $out = [];
    foreach ( ce_ctx_network_map()['links'] as $link ) {
        if ( CE_SITE_KEY !== ( $link['site'] ?? '' ) ) continue;
        if ( ! preg_match( '#^/articles/([a-z0-9-]+)/$#', (string) ( $link['path'] ?? '' ), $m ) ) continue;
        foreach ( (array) ( $link['phrases'] ?? [] ) as $phrase ) {
            $out[ (string) $phrase ] = $m[1];
        }
    }
    return $out;
}

/**
 * Lower-cased phrases the shared map gives to a sister site.
 *
 * @return array<string,true>
 */
function ce_ctx_sister_owned_phrases() {
    $out = [];
    foreach ( ce_ctx_network_map()['links'] as $link ) {
        if ( CE_SITE_KEY === ( $link['site'] ?? '' ) ) continue;
        foreach ( (array) ( $link['phrases'] ?? [] ) as $phrase ) {
            $out[ ce_ctx_lower( $phrase ) ] = true;
        }
    }
    return $out;
}

/**
 * Sister links, keyed by lower-cased phrase. Admin lines first, so they win;
 * a phrase this site keeps (admin line or custom phrase option) never goes out.
 *
 * @return array<string,array{phrase:string,url:string}>
 */
function ce_ctx_sister_links() {
    static $cache = null;
    if ( null !== $cache ) {
        return $cache;
    }
    $extra = ce_ctx_extra_lines();
    $keep  = [];
    $custom = get_option( 'ce_crosslink_custom_phrases' );
    foreach ( array_merge( is_array( $custom ) ? array_keys( $custom ) : [], array_keys( $extra['own'] ) ) as $phrase ) {
        $keep[ ce_ctx_lower( $phrase ) ] = true;
    }
    $out   = array_diff_key( $extra['sister'], $keep );
    $map   = ce_ctx_network_map();
    foreach ( $map['links'] as $link ) {
        $site = (string) ( $link['site'] ?? '' );
        if ( CE_SITE_KEY === $site || empty( $map['sites'][ $site ] ) ) continue;
        $url = rtrim( (string) $map['sites'][ $site ], '/' ) . (string) ( $link['path'] ?? '/' );
        foreach ( (array) ( $link['phrases'] ?? [] ) as $phrase ) {
            $lc = ce_ctx_lower( $phrase );
            if ( '' === $lc || isset( $out[ $lc ] ) || isset( $keep[ $lc ] ) ) continue;
            $out[ $lc ] = [ 'phrase' => (string) $phrase, 'url' => $url ];
        }
    }
    return $cache = $out;
}

/**
 * One phrase as a pattern fragment: quoted, with every apostrophe form
 * (straight, curly, or the entity wptexturize writes) matching any other.
 * Shared by the sister pass and the unified engine.
 *
 * @param string $phrase Phrase.
 * @return string
 */
function ce_ctx_phrase_alt( $phrase ) {
    $phrase = str_replace( [ "\u{2019}", '&#8217;', '&#039;', '&#39;', '&rsquo;' ], "'", (string) $phrase );
    return str_replace( "'", "(?:'|\u{2019}|&\#8217;|&\#0?39;|&rsquo;)", preg_quote( $phrase, '/' ) );
}

/**
 * The sister phrases as one or more patterns, longest phrase first. One
 * pattern normally; when the list is too large for PCRE to compile (many
 * extra phrases on the settings tab), it is split until each part compiles,
 * and a phrase that cannot compile alone is dropped.
 *
 * @return string[]
 */
function ce_ctx_sister_patterns() {
    static $patterns = null;
    if ( null !== $patterns ) {
        return $patterns;
    }
    $phrases = array_column( ce_ctx_sister_links(), 'phrase' );
    if ( ! $phrases ) {
        return $patterns = [];
    }
    $len = function_exists( 'mb_strlen' ) ? 'mb_strlen' : 'strlen';
    usort( $phrases, static function ( $a, $b ) use ( $len ) { return $len( $b ) <=> $len( $a ); } );
    $alts     = array_map( 'ce_ctx_phrase_alt', $phrases );
    $wrap     = static function ( $alts ) { return '/(?<![\p{L}\p{N}])(?:' . implode( '|', $alts ) . ')(?![\p{L}\p{N}])/iu'; };
    $queue    = [ $alts ];
    $patterns = [];
    while ( $queue ) {
        $chunk   = array_shift( $queue );
        $pattern = $wrap( $chunk );
        if ( false !== @preg_match( $pattern, '' ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- the compile test is the point.
            $patterns[] = $pattern;
        } elseif ( count( $chunk ) > 1 ) {
            $half = (int) ceil( count( $chunk ) / 2 );
            array_unshift( $queue, array_slice( $chunk, 0, $half ), array_slice( $chunk, $half ) );
        }
    }
    return $patterns;
}

/**
 * The sister link for a matched phrase. Without mbstring, ce_ctx_lower()
 * lowers ASCII only, so a match in accented capitals ("IBN ISḤĀQ") misses
 * its key and is compared phrase by phrase instead.
 *
 * @param array  $links Sister links keyed by lower-cased phrase.
 * @param string $text  Matched text.
 * @return string Address, or '' when none.
 */
function ce_ctx_find_sister_url( $links, $text ) {
    $key = ce_ctx_lower( $text );
    if ( isset( $links[ $key ] ) ) {
        return $links[ $key ]['url'];
    }
    if ( ! function_exists( 'mb_strtolower' ) ) {
        foreach ( $links as $link ) {
            if ( preg_match( '/^' . ce_ctx_phrase_alt( $link['phrase'] ) . '$/iu', $text ) ) {
                return $link['url'];
            }
        }
    }
    return '';
}

/**
 * Replace the matches of several patterns in one text as one pattern would:
 * leftmost first, the longest at the same place, never overlapping.
 *
 * @param string   $text     Plain text.
 * @param string[] $patterns Patterns.
 * @param callable $callback Receives the matched text, returns the replacement.
 * @return string The text, unchanged when a pattern fails (invalid UTF-8).
 */
function ce_ctx_replace_matches( $text, $patterns, $callback ) {
    $found = [];
    foreach ( $patterns as $pattern ) {
        if ( false === preg_match_all( $pattern, $text, $sets, PREG_OFFSET_CAPTURE ) ) {
            return $text;
        }
        foreach ( $sets[0] as $m ) {
            $found[] = $m;
        }
    }
    if ( ! $found ) {
        return $text;
    }
    usort( $found, static function ( $a, $b ) { return $a[1] <=> $b[1] ?: strlen( $b[0] ) <=> strlen( $a[0] ); } );
    $out    = '';
    $cursor = 0;
    foreach ( $found as [ $match, $offset ] ) {
        if ( $offset < $cursor ) continue;
        $out   .= substr( $text, $cursor, $offset - $cursor ) . $callback( $match );
        $cursor = $offset + strlen( $match );
    }
    return $out . substr( $text, $cursor );
}


/* ═══════════════════════════════════════════════════════════════════════
   SHARED GUARDS
   ═══════════════════════════════════════════════════════════════════════ */

/** Excluded slugs and the escape hatch, for both passes. */
function ce_ctx_enabled_for( $post ) {
    if ( ! $post ) return false;
    $excluded = preg_split( '/[\s,]+/', strtolower( (string) get_option( 'ce_contextual_links_excluded', '' ) ), -1, PREG_SPLIT_NO_EMPTY );
    $on       = ! in_array( strtolower( (string) $post->post_name ), $excluded, true );
    return (bool) apply_filters( 'ce_contextual_links_enabled', $on, $post );
}

/**
 * True when the post sits in a topic listed under "Disable crosslinks for
 * categories". Questions carry their topic as a term name in
 * `_ce_question_topic`, not as a `ce_topic` term, so that name is resolved too.
 */
function ce_ctx_in_disabled_topic( $post_id ) {
    $disabled = array_filter( array_map( 'trim', explode( ',', (string) get_option( 'ce_crosslink_disabled_categories', '' ) ) ) );
    if ( ! $disabled ) return false;
    $terms = wp_get_post_terms( $post_id, 'ce_topic', [ 'fields' => 'slugs' ] );
    $terms = is_wp_error( $terms ) ? [] : (array) $terms;
    $name  = (string) get_post_meta( $post_id, '_ce_question_topic', true );
    if ( '' !== $name ) {
        $term    = get_term_by( 'name', $name, 'ce_topic' );
        $terms[] = $term ? $term->slug : sanitize_title( $name );
    }
    return (bool) array_intersect( $terms, $disabled );
}


/* ═══════════════════════════════════════════════════════════════════════
   SISTER-SITE PASS
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Runs at 21, after the unified engine (20): its crosslinks and tooltips are
 * already in place, so their paragraphs are known and their anchors are
 * skipped like any other link.
 */
function ce_sister_link_filter( $content ) {
    if ( '1' !== get_option( 'ce_sister_links_enabled', '1' ) ) return $content;
    if ( is_admin() || is_feed() || is_front_page() || ! is_singular( ce_ctx_post_types() ) || ! is_main_query() || doing_filter( 'get_the_excerpt' ) ) {
        return $content;
    }
    if ( false === strpos( $content, '<' ) ) return $content;

    $post = get_post();
    if ( ! ce_ctx_enabled_for( $post ) ) return $content;
    if ( '1' === get_post_meta( $post->ID, '_ce_disable_crosslinks', true ) ) return $content;
    if ( ce_ctx_in_disabled_topic( $post->ID ) ) return $content;

    $max = min( 10, absint( get_option( 'ce_sister_links_max', 3 ) ) );
    if ( $max < 1 ) return $content;

    return ce_ctx_apply_sister_links( $content, $max, (string) get_permalink( $post ) );
}
add_filter( 'the_content', 'ce_sister_link_filter', 21 );

/**
 * The text walk, separate from the guards so it can be tested alone.
 *
 * @param string $content Rendered content.
 * @param int    $max     Sister links on the page, at most.
 * @param string $here    The current page's address.
 */
function ce_ctx_apply_sister_links( $content, $max, $here ) {
    $links    = ce_ctx_sister_links();
    $patterns = ce_ctx_sister_patterns();
    if ( ! $links || ! $patterns ) return $content;

    $pieces = preg_split( '/(<[^>]+>)/', $content, -1, PREG_SPLIT_DELIM_CAPTURE );
    if ( false === $pieces ) return $content;

    $skip_tags  = [ 'a', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote', 'q', 'figure', 'figcaption', 'cite', 'code', 'pre', 'sup', 'sub', 'nav', 'table', 'button', 'label', 'select', 'textarea', 'summary', 'script', 'style', 'svg' ];
    $skip_class = '/\bclass\s*=\s*"[^"]*\b(?:quran-citation|hadith-citation|quran-arabic|hadith-arabic|quran-translation|hadith-translation|quran-ref|hadith-ref|ce-toc|footnotes?|qa-pending)\b/i';
    $skip_lang  = '/\blang\s*=\s*"(?:ar|he|el|syr)(?:-[^"]*)?"/i';
    $void       = [ 'br', 'img', 'hr', 'wbr', 'input', 'source', 'meta', 'link', 'area', 'col', 'embed', 'param', 'track' ];

    // First walk: give each piece the paragraph or list item it sits in, and
    // note the ones that already hold a crosslink or tooltip.
    $stack      = []; // [ tag, skip ] for every open element.
    $blocks     = []; // open p/li ids.
    $next_block = 0;
    $owner      = [];
    $skip_at    = [];
    $used       = [];
    foreach ( $pieces as $i => $piece ) {
        if ( '' !== $piece && '<' === $piece[0] ) {
            if ( preg_match( '#^<(/?)([a-zA-Z][a-zA-Z0-9-]*)#', $piece, $m ) ) {
                $tag = strtolower( $m[2] );
                if ( '/' === $m[1] ) {
                    for ( $j = count( $stack ) - 1; $j >= 0; $j-- ) {
                        if ( $stack[ $j ][0] === $tag ) {
                            array_splice( $stack, $j );
                            break;
                        }
                    }
                    if ( ( 'p' === $tag || 'li' === $tag ) && $blocks ) array_pop( $blocks );
                } elseif ( ! in_array( $tag, $void, true ) && '/' !== substr( $piece, -2, 1 ) ) {
                    // A paragraph inside a list item shares the item's id, so
                    // the item still takes one link however many paragraphs.
                    if ( 'li' === $tag ) $blocks[] = ++$next_block;
                    if ( 'p' === $tag ) $blocks[] = $blocks ? end( $blocks ) : ++$next_block;
                    $stack[] = [ $tag, in_array( $tag, $skip_tags, true ) || preg_match( $skip_class, $piece ) || preg_match( $skip_lang, $piece ) ];
                    if ( 'a' === $tag && $blocks && preg_match( '/\bclass\s*=\s*"[^"]*\b(?:ce-crosslink|ce-glossary-term)\b/i', $piece ) ) {
                        $used[ end( $blocks ) ] = true;
                    }
                }
            }
            continue;
        }
        $owner[ $i ]   = $blocks ? end( $blocks ) : 0;
        $skip_at[ $i ] = (bool) array_filter( array_column( $stack, 1 ) );
    }

    // Second walk: link the first mention of each address, one a paragraph.
    $here_key = untrailingslashit( strtok( $here, '?#' ) );
    $done     = [];
    $made     = 0;
    foreach ( $pieces as $i => $piece ) {
        if ( $made >= $max ) break;
        if ( ! isset( $owner[ $i ] ) || ! $owner[ $i ] || $skip_at[ $i ] || isset( $used[ $owner[ $i ] ] ) || '' === trim( $piece ) ) continue;
        $block = $owner[ $i ];
        $pieces[ $i ] = ce_ctx_replace_matches(
            $piece,
            $patterns,
            static function ( $match ) use ( $links, $here_key, $block, $max, &$done, &$made, &$used ) {
                if ( $made >= $max || isset( $used[ $block ] ) ) return $match;
                $url = ce_ctx_find_sister_url( $links, $match );
                if ( '' === $url || isset( $done[ $url ] ) || untrailingslashit( $url ) === $here_key ) return $match;
                $done[ $url ]    = true;
                $used[ $block ]  = true;
                ++$made;
                return '<a class="ce-crosslink ce-ctx-link ce-ctx-link--sister" href="' . esc_url( $url ) . '" rel="noopener">' . $match . '</a>';
            }
        );
    }
    return implode( '', $pieces );
}
