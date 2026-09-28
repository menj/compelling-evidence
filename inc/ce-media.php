<?php
/**
 * Article media: figures, diagrams and tables.
 *
 * Articles carry three kinds of visual material:
 *
 *  - [ce_figure id="…"]Caption[/ce_figure]
 *      A photograph or scan from inc/articles/media.json. Each registry entry
 *      records its source (Wikimedia Commons, Pexels or Flickr), source page,
 *      author, licence and alt text. The credit line is built from the entry,
 *      so no figure can appear without its attribution.
 *
 *  - [ce_diagram id="…"]Caption[/ce_diagram]
 *      An SVG from assets/diagrams/, printed inline so it takes the theme's
 *      colour tokens and fonts, and follows light and dark schemes.
 *
 *  - Plain HTML <table> elements in the article body, wrapped at render time
 *    in a scrollable, focusable region so wide tables never break the layout.
 *
 * Images are imported into the Media Library in small batches on admin page
 * loads (Theme Options → Media), which gives them srcset, local caching and
 * independence from the source site. Until an image is imported, the figure
 * uses the source's own thumbnail URL.
 *
 * @package CE_Theme
 * @since   2.6.33
 */

defined( 'ABSPATH' ) || exit;

/* ═══════════════════════════════════════════════════════════════════════
   REGISTRY
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * All registered media, keyed by id.
 */
function ce_media_registry(): array {
	static $registry = null;
	if ( null !== $registry ) {
		return $registry;
	}
	$registry = [];
	$file     = get_stylesheet_directory() . '/inc/articles/media.json';
	if ( is_readable( $file ) ) {
		$data = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
		if ( is_array( $data ) && isset( $data['media'] ) && is_array( $data['media'] ) ) {
			$registry = $data['media'];
		}
	}
	return $registry;
}

/**
 * Attachment ids of imported media, keyed by registry id.
 */
function ce_media_attachments(): array {
	$map = get_option( 'ce_media_attachments', [] );
	return is_array( $map ) ? $map : [];
}

/**
 * Human-readable source name.
 */
function ce_media_source_label( string $source ): string {
	$labels = [
		'wikimedia-commons' => 'Wikimedia Commons',
		'pexels'            => 'Pexels',
		'flickr'            => 'Flickr',
	];
	return $labels[ $source ] ?? ucfirst( $source );
}

/**
 * Credit line for a registry entry, as safe HTML.
 */
function ce_media_credit_html( array $m ): string {
	$parts = [];
	if ( ! empty( $m['author'] ) ) {
		$parts[] = ! empty( $m['author_url'] )
			? '<a href="' . esc_url( $m['author_url'] ) . '" rel="noopener" target="_blank">' . esc_html( $m['author'] ) . '</a>'
			: esc_html( $m['author'] );
	}
	if ( ! empty( $m['license'] ) ) {
		$parts[] = ! empty( $m['license_url'] )
			? '<a href="' . esc_url( $m['license_url'] ) . '" rel="license noopener" target="_blank">' . esc_html( $m['license'] ) . '</a>'
			: esc_html( $m['license'] );
	}
	$via = ! empty( $m['page'] )
		? 'via <a href="' . esc_url( $m['page'] ) . '" rel="noopener" target="_blank">' . esc_html( ce_media_source_label( (string) ( $m['source'] ?? '' ) ) ) . '</a>'
		: 'via ' . esc_html( ce_media_source_label( (string) ( $m['source'] ?? '' ) ) );
	$parts[] = $via;
	return implode( ', ', $parts );
}

/* ═══════════════════════════════════════════════════════════════════════
   [ce_figure]
   ═══════════════════════════════════════════════════════════════════════ */

function ce_figure_shortcode( $atts, $content = '' ): string {
	$atts = shortcode_atts( [ 'id' => '' ], $atts, 'ce_figure' );
	$id   = sanitize_key( $atts['id'] );
	$reg  = ce_media_registry();
	if ( ! $id || ! isset( $reg[ $id ] ) ) {
		return current_user_can( 'edit_posts' ) ? '<!-- ce_figure: unknown id "' . esc_html( $id ) . '" -->' : '';
	}
	$m       = $reg[ $id ];
	$alt     = (string) ( $m['alt'] ?? '' );
	$variant = sanitize_html_class( (string) ( $m['variant'] ?? 'photo' ) );
	$att     = (int) ( ce_media_attachments()[ $id ] ?? 0 );

	if ( $att && wp_attachment_is_image( $att ) ) {
		$img  = wp_get_attachment_image( $att, 'large', false, [
			'alt'      => $alt,
			'loading'  => 'lazy',
			'decoding' => 'async',
			'class'    => 'ce-figure-img',
			'sizes'    => '(max-width: 760px) 100vw, 720px',
		] );
		$full = (string) wp_get_attachment_image_url( $att, 'full' );
	} else {
		$src  = (string) ( $m['thumb'] ?? $m['url'] ?? '' );
		$w    = (int) ( $m['width'] ?? 0 );
		$h    = (int) ( $m['height'] ?? 0 );
		$img  = '<img class="ce-figure-img" src="' . esc_url( $src ) . '" alt="' . esc_attr( $alt ) . '" loading="lazy" decoding="async"'
			. ( $w && $h && empty( $m['crop'] ) ? ' width="' . $w . '" height="' . $h . '"' : '' ) . '>';
		$full = (string) ( $m['url'] ?? $src );
	}

	$caption = trim( wp_kses_post( $content ) );
	$link    = get_option( 'ce_lightbox_enabled', '1' ) === '1'
		? '<a class="ce-figure-link" href="' . esc_url( $full ) . '" data-ce-lightbox="article" aria-label="' . esc_attr__( 'Enlarge image', 'compelling-evidence' ) . '">' . $img . '</a>'
		: $img;

	return '<figure class="ce-figure ce-figure--' . $variant . '">'
		. $link
		. '<figcaption>'
		. ( $caption ? '<span class="ce-figure-caption">' . $caption . '</span> ' : '' )
		. '<span class="ce-figure-credit">' . ce_media_credit_html( $m ) . '</span>'
		. '</figcaption></figure>';
}
add_shortcode( 'ce_figure', 'ce_figure_shortcode' );

/* ═══════════════════════════════════════════════════════════════════════
   [ce_diagram]
   ═══════════════════════════════════════════════════════════════════════ */

function ce_diagram_shortcode( $atts, $content = '' ): string {
	$atts = shortcode_atts( [ 'id' => '' ], $atts, 'ce_diagram' );
	$id   = sanitize_file_name( $atts['id'] );
	$file = get_stylesheet_directory() . '/assets/diagrams/' . $id . '.svg';
	if ( ! $id || ! is_readable( $file ) ) {
		return current_user_can( 'edit_posts' ) ? '<!-- ce_diagram: missing ' . esc_html( $id ) . '.svg -->' : '';
	}
	$svg     = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
	$svg     = preg_replace( '/<\?xml[^>]*\?>\s*/', '', $svg );
	$caption = trim( wp_kses_post( $content ) );
	$zoom    = get_option( 'ce_lightbox_enabled', '1' ) === '1'
		? '<button type="button" class="ce-diagram-zoom" data-ce-lightbox-diagram aria-label="' . esc_attr__( 'Enlarge diagram', 'compelling-evidence' ) . '"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M15 3h6v6M9 21H3v-6M21 3l-7 7M3 21l7-7"/></svg></button>'
		: '';

	return '<figure class="ce-figure ce-diagram" data-diagram="' . esc_attr( $id ) . '">'
		. '<div class="ce-diagram-canvas">' . $svg . $zoom . '</div>' // phpcs:ignore -- SVG is a theme-owned file in assets/diagrams/.
		. ( $caption ? '<figcaption><span class="ce-figure-caption">' . $caption . '</span></figcaption>' : '' )
		. '</figure>';
}
add_shortcode( 'ce_diagram', 'ce_diagram_shortcode' );

/* ═══════════════════════════════════════════════════════════════════════
   TABLES
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Wrap article tables in a labelled, keyboard-scrollable region.
 */
function ce_wrap_content_tables( string $content ): string {
	if ( ! is_singular( [ 'ce_article', 'post' ] ) || false === stripos( $content, '<table' ) ) {
		return $content;
	}
	return preg_replace_callback( '#<table\b[^>]*>.*?</table>#si', static function ( $m ) {
		$label = 'Table';
		if ( preg_match( '#<caption[^>]*>(.*?)</caption>#si', $m[0], $c ) ) {
			$label = wp_strip_all_tags( $c[1] );
		}
		return '<div class="ce-table-wrap" role="region" tabindex="0" aria-label="' . esc_attr( $label ) . '">' . $m[0] . '</div>';
	}, $content );
}
add_filter( 'the_content', 'ce_wrap_content_tables', 12 );

/* ═══════════════════════════════════════════════════════════════════════
   ASSETS
   ═══════════════════════════════════════════════════════════════════════ */

function ce_media_enqueue(): void {
	if ( ! is_singular( [ 'ce_article', 'post' ] ) ) {
		return;
	}
	$ver = wp_get_theme()->get( 'Version' );
	$dir = get_stylesheet_directory_uri();
	wp_enqueue_style( 'ce-media', $dir . '/assets/css/ce-media.css', [ 'ce-main' ], $ver );
	if ( get_option( 'ce_lightbox_enabled', '1' ) === '1' ) {
		wp_enqueue_script( 'ce-lightbox', $dir . '/assets/js/ce-lightbox.js', [], $ver, [ 'in_footer' => true, 'strategy' => 'defer' ] );
		wp_localize_script( 'ce-lightbox', 'ceLightbox', [
			'close' => __( 'Close', 'compelling-evidence' ),
			'prev'  => __( 'Previous image', 'compelling-evidence' ),
			'next'  => __( 'Next image', 'compelling-evidence' ),
			/* translators: 1: current image number, 2: total images. */
			'count' => __( '%1$s of %2$s', 'compelling-evidence' ),
		] );
	}
}
add_action( 'wp_enqueue_scripts', 'ce_media_enqueue', 20 );

/* ═══════════════════════════════════════════════════════════════════════
   IMPORT INTO THE MEDIA LIBRARY
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Import up to $limit registry images that are not yet in the Media Library.
 *
 * @return array{imported:int,failed:array,remaining:int}
 */
function ce_media_import_pending( int $limit = 3 ): array {
	$result = [ 'imported' => 0, 'failed' => [], 'remaining' => 0 ];
	$reg    = ce_media_registry();
	$map    = ce_media_attachments();
	$todo   = array_filter( $reg, static function ( $m, $id ) use ( $map ) {
		return empty( $map[ $id ] ) || ! wp_attachment_is_image( (int) $map[ $id ] );
	}, ARRAY_FILTER_USE_BOTH );

	if ( ! $todo ) {
		return $result;
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	foreach ( $todo as $id => $m ) {
		if ( $result['imported'] + count( $result['failed'] ) >= $limit ) {
			break;
		}
		$att = ce_media_import_one( (string) $id, $m );
		if ( is_wp_error( $att ) ) {
			$result['failed'][ $id ] = $att->get_error_message();
			continue;
		}
		$map[ $id ] = $att;
		update_option( 'ce_media_attachments', $map, false );
		$result['imported']++;
	}
	$result['remaining'] = max( 0, count( $todo ) - $result['imported'] );
	return $result;
}

/**
 * Download, optionally crop, and attach one registry image.
 *
 * @return int|WP_Error Attachment id.
 */
function ce_media_import_one( string $id, array $m ) {
	$url = (string) ( $m['url'] ?? '' );
	if ( ! $url ) {
		return new WP_Error( 'ce_media_no_url', 'No source URL.' );
	}
	$tmp = download_url( $url, 60 );
	if ( is_wp_error( $tmp ) ) {
		return $tmp;
	}

	// Registry crops are fractions of width and height: [left, top, right, bottom].
	if ( ! empty( $m['crop'] ) && is_array( $m['crop'] ) && 4 === count( $m['crop'] ) ) {
		$editor = wp_get_image_editor( $tmp );
		if ( ! is_wp_error( $editor ) ) {
			$size = $editor->get_size();
			[ $l, $t, $r, $b ] = array_map( 'floatval', $m['crop'] );
			$editor->crop( (int) round( $l * $size['width'] ), (int) round( $t * $size['height'] ), (int) round( ( $r - $l ) * $size['width'] ), (int) round( ( $b - $t ) * $size['height'] ) );
			$editor->save( $tmp );
		}
	}

	$ext  = strtolower( pathinfo( wp_parse_url( $url, PHP_URL_PATH ), PATHINFO_EXTENSION ) ) ?: 'jpg';
	$file = [
		'name'     => 'ce-' . $id . '.' . $ext,
		'tmp_name' => $tmp,
	];
	$att = media_handle_sideload( $file, 0, (string) ( $m['alt'] ?? $id ), [
		'post_excerpt' => wp_strip_all_tags( ce_media_credit_html( $m ) ),
	] );
	if ( is_wp_error( $att ) ) {
		wp_delete_file( $tmp );
		return $att;
	}
	update_post_meta( $att, '_wp_attachment_image_alt', sanitize_text_field( (string) ( $m['alt'] ?? '' ) ) );
	update_post_meta( $att, '_ce_media_key', $id );
	update_post_meta( $att, '_ce_media_source', esc_url_raw( (string) ( $m['page'] ?? $url ) ) );
	update_post_meta( $att, '_ce_media_license', sanitize_text_field( (string) ( $m['license'] ?? '' ) ) );
	ce_media_set_featured( (int) $att, $m );
	return (int) $att;
}

/**
 * Make an imported image the featured image of the article it belongs to,
 * unless that article already has one chosen by an editor.
 *
 * Featured images appear on article cards and as the social share image.
 */
function ce_media_set_featured( int $att, array $m ): void {
	if ( empty( $m['featured_for'] ) ) {
		return;
	}
	$post = get_page_by_path( sanitize_title( (string) $m['featured_for'] ), OBJECT, 'ce_article' );
	if ( $post && ! has_post_thumbnail( $post ) ) {
		set_post_thumbnail( $post, $att );
	}
}

/**
 * Assign featured images for media imported before featured_for existed,
 * once per registry change.
 */
function ce_media_assign_featured(): void {
	if ( wp_doing_ajax() || ! current_user_can( 'upload_files' ) ) {
		return;
	}
	$reg  = ce_media_registry();
	$hash = md5( (string) wp_json_encode( array_map( static function ( $m ) {
		return $m['featured_for'] ?? '';
	}, $reg ) ) . wp_json_encode( ce_media_attachments() ) );
	if ( get_option( 'ce_media_featured_hash' ) === $hash ) {
		return;
	}
	foreach ( ce_media_attachments() as $id => $att ) {
		if ( isset( $reg[ $id ] ) && wp_attachment_is_image( (int) $att ) ) {
			ce_media_set_featured( (int) $att, $reg[ $id ] );
		}
	}
	update_option( 'ce_media_featured_hash', $hash, false );
}
add_action( 'admin_init', 'ce_media_assign_featured', 41 );

/**
 * Trickle imports: a few images per admin page load, never during AJAX.
 */
function ce_media_auto_import(): void {
	if ( wp_doing_ajax() || ! current_user_can( 'upload_files' ) || get_option( 'ce_media_import', '1' ) !== '1' ) {
		return;
	}
	if ( get_transient( 'ce_media_import_lock' ) ) {
		return;
	}
	set_transient( 'ce_media_import_lock', 1, 2 * MINUTE_IN_SECONDS );
	$res = ce_media_import_pending( 3 );
	if ( $res['failed'] ) {
		update_option( 'ce_media_import_errors', $res['failed'], false );
	}
	delete_transient( 'ce_media_import_lock' );
}
add_action( 'admin_init', 'ce_media_auto_import', 40 );

/**
 * Status panel for Theme Options → Media.
 */
function ce_media_status_html(): string {
	$reg  = ce_media_registry();
	$map  = ce_media_attachments();
	$done = count( array_filter( array_keys( $reg ), static function ( $id ) use ( $map ) {
		return ! empty( $map[ $id ] ) && wp_attachment_is_image( (int) $map[ $id ] );
	} ) );
	$errs = get_option( 'ce_media_import_errors', [] );
	$html = '<p><strong>' . (int) $done . '</strong> of <strong>' . count( $reg ) . '</strong> registered images are in the Media Library.</p>';
	if ( is_array( $errs ) && $errs ) {
		$html .= '<p>Last import errors:</p><ul>';
		foreach ( $errs as $id => $msg ) {
			$html .= '<li><code>' . esc_html( $id ) . '</code>: ' . esc_html( $msg ) . '</li>';
		}
		$html .= '</ul>';
	}
	return $html;
}
