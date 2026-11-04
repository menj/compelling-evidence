<?php
/**
 * Rank Math integration.
 *
 * Rule: where Rank Math and the theme cover the same ground, Rank Math wins.
 * The theme steps in only where Rank Math has no data for a field.
 *
 *  Front end
 *  - Meta description and canonical: Rank Math prints them. If its value
 *    comes back empty, the theme's own value is supplied through Rank Math's
 *    filters, so there is still exactly one tag.
 *  - Title (since 2.11.0): Rank Math prints the tag, and its text runs
 *    through the theme's builder (ce_title_build() in inc/ce-seo.php), so it
 *    reads "<page part> | Compelling Evidence" under 60 characters. Wording
 *    an editor typed into Rank Math's title field is kept as the page part.
 *  - JSON-LD: Rank Math's graph is the only block. Theme nodes (Article,
 *    BreadcrumbList, CollectionPage, WebSite, Organization) are added to it
 *    only when Rank Math's graph has no node of that type, for instance when
 *    an article's Rank Math schema type is set to None.
 *  - robots.txt, sitemaps and Open Graph stay with Rank Math entirely.
 *
 *  Editor (content analysis)
 *  - Rank Math scores the text in the editor. Articles show their figures,
 *    internal links and lead image only after rendering, so the analysis is
 *    given the published article body (assets/js/ce-rankmath-admin.js),
 *    the way Rank Math's own custom-fields integration feeds it content.
 *  - The theme builds a table of contents for every article with two or more
 *    H2 headings; Rank Math is told so through its metabox values.
 *
 *  Content sync
 *  - inc/articles/seo.json holds a focus keyword (with related keywords),
 *    short title and meta description for every article. Focus keyword and
 *    description are written to Rank Math's fields only when those fields
 *    are empty, so anything an editor sets in Rank Math is never
 *    overwritten. The short title is not written (since 2.11.0); the title
 *    filter supplies it. Bare titles that earlier versions wrote into
 *    rank_math_title are removed once (ce_rankmath_clear_legacy_titles()).
 *
 * @package CE_Theme
 * @since   2.6.36
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'ce_is_rankmath_active' ) ) {
	return;
}

/* ═══════════════════════════════════════════════════════════════════════
   FRONT END: FALLBACKS ONLY
   ═══════════════════════════════════════════════════════════════════════ */

function ce_rankmath_description( $description ) {
	if ( '' !== trim( (string) $description ) ) {
		return $description;
	}
	return function_exists( 'ce_get_meta_description' ) ? ce_get_meta_description() : $description;
}
add_filter( 'rank_math/frontend/description', 'ce_rankmath_description', 20 );

function ce_rankmath_canonical( $canonical ) {
	if ( '' !== trim( (string) $canonical ) ) {
		return $canonical;
	}
	return function_exists( 'ce_get_canonical_url' ) ? ce_get_canonical_url() : $canonical;
}
add_filter( 'rank_math/frontend/canonical', 'ce_rankmath_canonical', 20 );

/**
 * Titles that themes up to 2.10.1 wrote into rank_math_title, by slug.
 */
function ce_rankmath_legacy_titles(): array {
	static $titles = null;
	if ( null === $titles ) {
		$file   = get_stylesheet_directory() . '/inc/data/rankmath-legacy-titles.json';
		$data   = is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : []; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
		$titles = is_array( $data['titles'] ?? null ) ? $data['titles'] : [];
	}
	return $titles;
}

/**
 * Wording an editor typed into Rank Math's title field, with Rank Math's
 * variables resolved, or '' when the field is empty or holds a title the
 * theme wrote there itself.
 */
function ce_rankmath_custom_title_part( int $post_id ): string {
	$raw = trim( (string) get_post_meta( $post_id, 'rank_math_title', true ) );
	if ( '' === $raw ) {
		return '';
	}
	$legacy = ce_rankmath_legacy_titles();
	$slug   = (string) get_post_field( 'post_name', $post_id );
	if ( isset( $legacy[ $slug ] ) && sanitize_text_field( $legacy[ $slug ] ) === $raw ) {
		return '';
	}
	if ( false !== strpos( $raw, '%' ) && class_exists( 'RankMath\\Helper' ) && method_exists( 'RankMath\\Helper', 'replace_vars' ) ) {
		$raw = (string) \RankMath\Helper::replace_vars( $raw, get_post( $post_id ) );
	}
	return $raw;
}

/**
 * Run Rank Math's title through the theme's title builder.
 *
 * Page part: an editor's own Rank Math wording first, then the theme's
 * short title, then whatever Rank Math worked out. The site name and
 * separator always come from the builder.
 */
function ce_rankmath_title( $title ) {
	if ( ! function_exists( 'ce_title_build' ) ) {
		return $title;
	}
	$part = (string) $title;
	if ( '' === trim( $part ) && is_singular() ) {
		$part = wp_strip_all_tags( get_the_title() );
	}
	if ( is_singular() ) {
		$custom = ce_rankmath_custom_title_part( (int) get_queried_object_id() );
		$part   = '' !== $custom ? $custom : ce_title_page_part( $part );
	} else {
		$part = ce_title_page_part( $part );
	}
	return ce_title_build( $part );
}
add_filter( 'rank_math/frontend/title', 'ce_rankmath_title', 20 );

/**
 * Types already present anywhere in Rank Math's graph.
 */
function ce_rankmath_graph_types( array $data ): array {
	$types = [];
	array_walk_recursive( $data, static function ( $v, $k ) use ( &$types ) {
		if ( '@type' === $k ) {
			$types[] = (string) $v;
		}
	} );
	return array_unique( $types );
}

/**
 * Merge the theme graph into Rank Math's, so the page keeps one graph.
 *
 * - A theme node whose @id is already in Rank Math's graph fills that node's
 *   empty properties (and turns a plain WebPage into the page's subtype).
 * - For the families Rank Math builds itself (Article, Organization,
 *   WebSite, BreadcrumbList, WebPage and its subtypes), a theme node is not
 *   added when Rank Math has one: it fills that node's empty properties
 *   instead (citations, figures, mentions), and references to the theme @id
 *   are pointed at Rank Math's.
 * - Every other theme node is added under its own @id.
 *
 * @since 2.12.0 (merge by @id; before, one node per missing type).
 */
function ce_rankmath_json_ld( $data, $jsonld = null ) {
	if ( ! is_array( $data ) || ! function_exists( 'ce_schema_nodes' ) ) {
		return $data;
	}
	$family = [
		'Article'        => [ 'Article', 'BlogPosting', 'NewsArticle', 'ScholarlyArticle', 'TechArticle', 'Report' ],
		'Organization'   => [ 'Organization', 'NGO', 'EducationalOrganization' ],
		'BreadcrumbList' => [ 'BreadcrumbList' ],
		'WebSite'        => [ 'WebSite' ],
		'WebPage'        => [ 'WebPage', 'CollectionPage', 'QAPage', 'ContactPage', 'AboutPage', 'SearchResultsPage', 'ItemPage', 'ProfilePage', 'FAQPage' ],
	];
	$fam_of = static function ( $node ) use ( $family ) {
		$types = (array) ( $node['@type'] ?? [] );
		foreach ( $family as $name => $members ) {
			if ( array_intersect( $types, $members ) ) {
				return $name;
			}
		}
		return '';
	};

	// Rank Math's top-level nodes by @id and by family.
	$by_id  = [];
	$by_fam = [];
	foreach ( $data as $key => $node ) {
		if ( ! is_array( $node ) || empty( $node['@type'] ) ) {
			continue;
		}
		if ( ! empty( $node['@id'] ) ) {
			$by_id[ (string) $node['@id'] ] = $key;
		}
		$f = $fam_of( $node );
		// A page's FAQPage is not its WebPage node.
		if ( '' !== $f && ! isset( $by_fam[ $f ] ) && ! ( 'WebPage' === $f && in_array( 'FAQPage', (array) $node['@type'], true ) ) ) {
			$by_fam[ $f ] = $key;
		}
	}

	$theme = ce_schema_nodes();
	$remap = [];
	$fills = [];
	$add   = [];
	foreach ( $theme as $node ) {
		$id   = (string) ( $node['@id'] ?? '' );
		$f    = $fam_of( $node );
		$is_faq = in_array( 'FAQPage', (array) $node['@type'], true );
		if ( '' !== $id && isset( $by_id[ $id ] ) ) {
			$fills[] = [ $by_id[ $id ], $node ];
		} elseif ( '' !== $f && ! $is_faq && isset( $by_fam[ $f ] ) ) {
			$target = $by_fam[ $f ];
			if ( '' !== $id && ! empty( $data[ $target ]['@id'] ) ) {
				$remap[ $id ] = (string) $data[ $target ]['@id'];
			}
			$fills[] = [ $target, $node ];
		} else {
			$add[] = $node;
		}
	}

	$relink = static function ( $v ) use ( &$relink, $remap ) {
		if ( ! is_array( $v ) ) {
			return $v;
		}
		if ( 1 === count( $v ) && isset( $v['@id'] ) && isset( $remap[ $v['@id'] ] ) ) {
			return [ '@id' => $remap[ $v['@id'] ] ];
		}
		foreach ( $v as $k => $x ) {
			$v[ $k ] = $relink( $x );
		}
		return $v;
	};

	foreach ( $fills as [ $key, $node ] ) {
		$data[ $key ] = ce_schema_fill( $data[ $key ], $relink( $node ) );
	}
	foreach ( $add as $node ) {
		$node = $relink( $node );
		$key  = 'ce_' . substr( md5( (string) ( $node['@id'] ?? wp_json_encode( $node ) ) ), 0, 12 );
		$data[ $key ] = $node;
	}
	// An Article Rank Math prints where the theme has none (the quiz) gains
	// an image and the post's dates (2.15.1).
	$data = ce_schema_article_backfill( $data, ce_schema_article_image(), ce_schema_article_dates() );
	// One author Person, whatever Rank Math named (owner rule, 4 November 2026).
	return ce_schema_one_author( $data );
}
add_filter( 'rank_math/json_ld', 'ce_rankmath_json_ld', 99, 2 );

/* ═══════════════════════════════════════════════════════════════════════
   EDITOR: CONTENT ANALYSIS
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Tell Rank Math the theme provides a table of contents for this article.
 */
function ce_rankmath_metabox_values( $values ) {
	$post = get_post();
	if ( $post && in_array( $post->post_type, [ 'ce_article', 'post' ], true ) && preg_match_all( '/<h2\b/i', (string) $post->post_content ) >= 2 ) {
		$values['assessor']['hasTOCPlugin'] = [ 'compelling-evidence' => 'CE Theme table of contents' ];
	}
	return $values;
}
add_filter( 'rank_math/metabox/post/values', 'ce_rankmath_metabox_values', 20 );

/**
 * Give Rank Math's analysis the article as published.
 */
function ce_rankmath_admin_assets( $hook ): void {
	if ( ! ce_is_rankmath_active() || ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
		return;
	}
	$post = get_post();
	if ( ! $post || ! in_array( $post->post_type, [ 'ce_article', 'post' ], true ) || 'publish' !== $post->post_status ) {
		return;
	}
	wp_enqueue_script( 'ce-rankmath-admin', get_stylesheet_directory_uri() . '/assets/js/ce-rankmath-admin.js', [ 'wp-hooks' ], wp_get_theme()->get( 'Version' ), true );
	wp_localize_script( 'ce-rankmath-admin', 'ceRankMath', [
		'url' => add_query_arg( 'ce_rm', time(), get_permalink( $post ) ),
		'raw' => (string) $post->post_content,
	] );
}
add_action( 'admin_enqueue_scripts', 'ce_rankmath_admin_assets' );

/* ═══════════════════════════════════════════════════════════════════════
   CONTENT SYNC: FILL EMPTY RANK MATH FIELDS
   ═══════════════════════════════════════════════════════════════════════ */

function ce_rankmath_seo_data(): array {
	$file = get_stylesheet_directory() . '/inc/articles/seo.json';
	if ( ! is_readable( $file ) ) {
		return [];
	}
	$data = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
	return is_array( $data['articles'] ?? null ) ? $data['articles'] : [];
}

/**
 * Remove the bare titles that themes up to 2.10.1 wrote into
 * rank_math_title. A field is cleared only when it still holds exactly the
 * text the theme wrote; an editor's own title is left alone.
 *
 * @return int Number of fields cleared.
 */
function ce_rankmath_clear_legacy_titles(): int {
	$cleared = 0;
	foreach ( ce_rankmath_legacy_titles() as $slug => $legacy ) {
		$posts = get_posts( [ 'post_type' => 'ce_article', 'name' => sanitize_title( $slug ), 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ] );
		if ( $posts && sanitize_text_field( (string) $legacy ) === trim( (string) get_post_meta( $posts[0], 'rank_math_title', true ) ) ) {
			delete_post_meta( $posts[0], 'rank_math_title' );
			$cleared++;
		}
	}
	return $cleared;
}

/**
 * Write focus keyword and description into Rank Math's fields
 * where those fields are empty. Never overwrites a value already there.
 *
 * @return int Number of fields written.
 */
function ce_rankmath_fill_empty_fields(): int {
	$map     = [
		'focus_keyword' => 'rank_math_focus_keyword',
		'description'   => 'rank_math_description',
	];
	$written = 0;
	foreach ( ce_rankmath_seo_data() as $slug => $seo ) {
		$posts = get_posts( [ 'post_type' => 'ce_article', 'name' => sanitize_title( $slug ), 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ] );
		if ( ! $posts ) {
			continue;
		}
		foreach ( $map as $field => $meta ) {
			if ( empty( $seo[ $field ] ) ) {
				continue;
			}
			if ( '' === trim( (string) get_post_meta( $posts[0], $meta, true ) ) ) {
				update_post_meta( $posts[0], $meta, sanitize_text_field( $seo[ $field ] ) );
				$written++;
			}
		}
	}
	return $written;
}

/**
 * Run after a content sync, and once whenever seo.json changes.
 */
function ce_rankmath_maybe_fill(): void {
	if ( ! ce_is_rankmath_active() || ! current_user_can( 'edit_posts' ) || wp_doing_ajax() ) {
		return;
	}
	$file = get_stylesheet_directory() . '/inc/articles/seo.json';
	$hash = is_readable( $file ) ? md5_file( $file ) : '';
	if ( '' === $hash || get_option( 'ce_rankmath_seo_hash' ) === $hash ) {
		return;
	}
	ce_rankmath_fill_empty_fields();
	update_option( 'ce_rankmath_seo_hash', $hash, false );
	if ( '1' !== get_option( 'ce_rankmath_legacy_titles_cleared' ) ) {
		ce_rankmath_clear_legacy_titles();
		update_option( 'ce_rankmath_legacy_titles_cleared', '1', false );
	}
}
add_action( 'admin_init', 'ce_rankmath_maybe_fill', 50 );
add_action( 'ce_content_synced', 'ce_rankmath_fill_empty_fields' );
add_action( 'activated_plugin', static function ( $plugin ) {
	if ( 0 === strpos( (string) $plugin, 'seo-by-rank-math' ) ) {
		delete_option( 'ce_rankmath_seo_hash' );
	}
} );
