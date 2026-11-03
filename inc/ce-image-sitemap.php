<?php
/**
 * Image sitemap at /image-sitemap.xml (since 2.11.0).
 *
 * WordPress's own sitemaps carry no image entries. This file lists every
 * published article, post and page with the images it shows: the featured
 * image and each registry image placed with [ce_figure]. robots.txt names it.
 * Rank Math and the other SEO plugins write their own image entries, so the
 * file stands down while one is active.
 *
 * @package CE_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether an SEO plugin that writes its own sitemaps is active.
 */
function ce_seo_plugin_active(): bool {
	return ( function_exists( 'ce_is_rankmath_active' ) && ce_is_rankmath_active() )
		|| defined( 'WPSEO_VERSION' )
		|| defined( 'AIOSEO_VERSION' )
		|| defined( 'SEOPRESS_VERSION' )
		|| defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/**
 * Whether the theme serves the image sitemap.
 */
function ce_image_sitemap_active(): bool {
	/**
	 * Filters whether the theme serves /image-sitemap.xml.
	 *
	 * @param bool $enabled True when no SEO plugin is active.
	 */
	return (bool) apply_filters( 'ce_image_sitemap_enabled', ! ce_seo_plugin_active() );
}

/**
 * Image addresses shown by one post.
 *
 * @param WP_Post $post Post.
 * @return string[]
 */
function ce_image_sitemap_images( WP_Post $post ): array {
	$images   = [];
	$thumb_id = (int) get_post_thumbnail_id( $post );
	if ( $thumb_id ) {
		$images[] = (string) wp_get_attachment_url( $thumb_id );
	}
	if ( false !== strpos( $post->post_content, '[ce_figure' ) && function_exists( 'ce_media_registry' ) ) {
		$registry = ce_media_registry();
		$attached = function_exists( 'ce_media_attachments' ) ? ce_media_attachments() : [];
		if ( preg_match_all( '/\[ce_figure\b[^\]]*\bid=["\']?([A-Za-z0-9_.:-]+)/', $post->post_content, $m ) ) {
			foreach ( array_unique( $m[1] ) as $id ) {
				$att = (int) ( $attached[ $id ] ?? 0 );
				if ( $att ) {
					$images[] = (string) wp_get_attachment_url( $att );
				} elseif ( isset( $registry[ $id ] ) ) {
					$images[] = (string) ( $registry[ $id ]['thumb'] ?? $registry[ $id ]['url'] ?? '' );
				}
			}
		}
	}
	return array_slice( array_values( array_unique( array_filter( $images ) ) ), 0, 1000 );
}

/**
 * Entries of the image sitemap: page address and image addresses.
 *
 * @return array[] Each with 'loc' and 'images'.
 */
function ce_image_sitemap_entries(): array {
	$entries = [];
	$paged   = 1;
	do {
		// Batches of 200, so every post is listed without one unbounded query.
		// Meta and thumbnails are primed per batch; terms are not needed.
		$posts = get_posts( [
			'post_type'              => [ 'ce_article', 'post', 'page' ],
			'post_status'            => 'publish',
			'posts_per_page'         => 200,
			'paged'                  => $paged,
			'has_password'           => false,
			'orderby'                => 'ID',
			'order'                  => 'ASC',
			'no_found_rows'          => true,
			'update_post_meta_cache' => true,
			'update_post_term_cache' => false,
			'suppress_filters'       => false,
		] );
		if ( $posts ) {
			update_post_thumbnail_cache( new WP_Query( [ 'posts' => $posts ] ) );
		}
		foreach ( $posts as $post ) {
			// Pages that only redirect elsewhere are left out.
			if ( 'page' === $post->post_type && false !== strpos( $post->post_content, '<!-- Redirected to' ) ) {
				continue;
			}
			$images = ce_image_sitemap_images( $post );
			if ( $images ) {
				$entries[] = [ 'loc' => get_permalink( $post ), 'images' => $images ];
			}
		}
		++$paged;
	} while ( count( $posts ) === 200 );
	return $entries;
}

/**
 * The image sitemap as XML.
 */
function ce_image_sitemap_xml(): string {
	$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
		. '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
	foreach ( ce_image_sitemap_entries() as $entry ) {
		$xml .= "\t<url>\n\t\t<loc>" . esc_url( $entry['loc'] ) . "</loc>\n";
		foreach ( $entry['images'] as $image ) {
			$xml .= "\t\t<image:image><image:loc>" . esc_url( $image ) . "</image:loc></image:image>\n";
		}
		$xml .= "\t</url>\n";
	}
	return $xml . '</urlset>' . "\n";
}

/**
 * Serve /image-sitemap.xml.
 *
 * @param WP $wp Request.
 */
function ce_image_sitemap_serve( $wp ): void {
	if ( 'image-sitemap.xml' !== $wp->request || ! ce_image_sitemap_active() || ! get_option( 'blog_public' ) ) {
		return;
	}
	status_header( 200 );
	header( 'Content-Type: application/xml; charset=UTF-8' );
	header( 'X-Robots-Tag: noindex, follow' );
	echo ce_image_sitemap_xml(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- every value is escaped above.
	exit;
}
add_action( 'parse_request', 'ce_image_sitemap_serve', 0 );

/**
 * robots.txt: name the image sitemap.
 *
 * @param string $output robots.txt.
 * @param bool   $public Whether the site is public.
 */
function ce_image_sitemap_robots_txt( $output, $public ) {
	if ( ! $public || ! ce_image_sitemap_active() || ! get_option( 'permalink_structure' ) ) {
		return $output;
	}
	return rtrim( (string) $output ) . "\nSitemap: " . esc_url( home_url( '/image-sitemap.xml' ) ) . "\n";
}
add_filter( 'robots_txt', 'ce_image_sitemap_robots_txt', 20, 2 );
