<?php
/**
 * Rank Math integration.
 *
 * Rule: where Rank Math and the theme cover the same ground, Rank Math wins.
 * The theme steps in only where Rank Math has no data for a field.
 *
 *  Front end
 *  - Title, meta description and canonical: Rank Math prints them. If its
 *    value comes back empty, the theme's own value is supplied through Rank
 *    Math's filters, so there is still exactly one tag.
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
 *    SEO title and meta description for every article. They are written to
 *    Rank Math's fields only when those fields are empty, so anything an
 *    editor sets in Rank Math is never overwritten.
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

function ce_rankmath_title( $title ) {
	if ( '' !== trim( (string) $title ) || ! is_singular() ) {
		return $title;
	}
	return wp_strip_all_tags( get_the_title() ) . ' – ' . get_bloginfo( 'name' );
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
 * Add theme schema nodes that Rank Math's graph lacks.
 */
function ce_rankmath_json_ld( $data, $jsonld = null ) {
	if ( ! is_array( $data ) || ! function_exists( 'ce_schema_nodes' ) ) {
		return $data;
	}
	$present = ce_rankmath_graph_types( $data );
	$family  = [
		'Article'        => [ 'Article', 'BlogPosting', 'NewsArticle', 'ScholarlyArticle', 'TechArticle', 'Report' ],
		'Organization'   => [ 'Organization', 'Person', 'NGO', 'EducationalOrganization' ],
		'BreadcrumbList' => [ 'BreadcrumbList' ],
		'CollectionPage' => [ 'CollectionPage' ],
		'WebSite'        => [ 'WebSite' ],
	];
	foreach ( ce_schema_nodes() as $node ) {
		$type = is_array( $node['@type'] ?? null ) ? reset( $node['@type'] ) : (string) ( $node['@type'] ?? '' );
		$fam  = $family[ $type ] ?? [ $type ];
		if ( '' === $type || array_intersect( $fam, $present ) ) {
			continue;
		}
		unset( $node['@context'] );
		$data[ 'ce_' . strtolower( $type ) ] = $node;
	}
	return $data;
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
 * Write focus keyword, SEO title and description into Rank Math's fields
 * where those fields are empty. Never overwrites a value already there.
 *
 * @return int Number of fields written.
 */
function ce_rankmath_fill_empty_fields(): int {
	$map     = [
		'focus_keyword' => 'rank_math_focus_keyword',
		'title'         => 'rank_math_title',
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
}
add_action( 'admin_init', 'ce_rankmath_maybe_fill', 50 );
add_action( 'ce_content_synced', 'ce_rankmath_fill_empty_fields' );
add_action( 'activated_plugin', static function ( $plugin ) {
	if ( 0 === strpos( (string) $plugin, 'seo-by-rank-math' ) ) {
		delete_option( 'ce_rankmath_seo_hash' );
	}
} );
