<?php
/**
 * Social profile icons.
 *
 * Inline SVG links to the site's own profiles, in the footer and through the
 * [ce_social_icons] shortcode and the "Social icons" block pattern. The
 * glyphs are 10 files from the Minimalist Social Icons Pack 2.8 (GPL; the
 * ten used here come from CC0 sources), stored in assets/images/social/.
 *
 * Profile URLs are set in Theme Options > Author & Identity > Social icons.
 * X, Facebook and YouTube reuse the site fields that already fed the schema.
 *
 * @package CE_Theme
 * @since   2.16.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Networks in display order: key => [ label, option ].
 */
function ce_social_networks(): array {
	return [
		'x'         => [ 'X', 'ce_site_twitter_handle' ],
		'facebook'  => [ 'Facebook', 'ce_site_facebook_url' ],
		'youtube'   => [ 'YouTube', 'ce_site_youtube_url' ],
		'instagram' => [ 'Instagram', 'ce_social_instagram' ],
		'tiktok'    => [ 'TikTok', 'ce_social_tiktok' ],
		'threads'   => [ 'Threads', 'ce_social_threads' ],
		'bluesky'   => [ 'Bluesky', 'ce_social_bluesky' ],
		'mastodon'  => [ 'Mastodon', 'ce_social_mastodon' ],
		'telegram'  => [ 'Telegram', 'ce_social_telegram' ],
		'whatsapp'  => [ 'WhatsApp', 'ce_social_whatsapp' ],
	];
}

/**
 * The profile URL for one network, or '' when none is set. The X field holds
 * a handle (@name) for the twitter:site tag, so it becomes a profile URL here.
 */
function ce_social_url( string $key ): string {
	$networks = ce_social_networks();
	if ( ! isset( $networks[ $key ] ) ) {
		return '';
	}
	$raw = trim( (string) get_option( $networks[ $key ][1], '' ) );
	if ( '' === $raw ) {
		return '';
	}
	if ( 'x' === $key && ! preg_match( '#^https?://#i', $raw ) ) {
		$handle = ltrim( $raw, '@' );
		if ( ! preg_match( '/^[A-Za-z0-9_]{1,15}$/', $handle ) ) {
			return '';
		}
		$raw = 'https://x.com/' . $handle;
	}
	return (string) esc_url_raw( $raw, [ 'http', 'https' ] );
}

/**
 * Every network with a URL: key => [ label, url ].
 */
function ce_social_profiles(): array {
	$out = [];
	foreach ( ce_social_networks() as $key => $net ) {
		$url = ce_social_url( $key );
		if ( '' !== $url ) {
			$out[ $key ] = [ $net[0], $url ];
		}
	}
	return $out;
}

/**
 * The SVG markup of one icon, marked decorative.
 */
function ce_social_svg( string $key ): string {
	static $cache = [];
	if ( ! isset( $cache[ $key ] ) ) {
		$file          = get_stylesheet_directory() . '/assets/images/social/' . sanitize_key( $key ) . '.svg';
		$svg           = is_readable( $file ) ? trim( (string) file_get_contents( $file ) ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
		$cache[ $key ] = '' === $svg ? '' : preg_replace( '/^<svg\b/', '<svg class="ce-social__icon" width="20" height="20" fill="currentColor" aria-hidden="true" focusable="false"', $svg );
	}
	return $cache[ $key ];
}

/**
 * The icon list, or '' when no profile is set.
 *
 * @param string $context Extra class suffix, e.g. "footer".
 */
function ce_social_icons_html( string $context = '' ): string {
	$profiles = ce_social_profiles();
	if ( ! $profiles ) {
		return '';
	}
	ce_social_enqueue_style();
	$class = 'ce-social' . ( '' !== $context ? ' ce-social--' . sanitize_html_class( $context ) : '' );
	$html  = '<ul class="' . esc_attr( $class ) . '" aria-label="' . esc_attr__( 'Our profiles', 'compelling-evidence' ) . '">';
	foreach ( $profiles as $key => $p ) {
		$html .= '<li><a class="ce-social__link" href="' . esc_url( $p[1] ) . '" rel="me noopener"'
			/* translators: %s: network name, e.g. YouTube. */
			. ' aria-label="' . esc_attr( sprintf( __( '%s profile', 'compelling-evidence' ), $p[0] ) ) . '">'
			. ce_social_svg( $key ) . '</a></li>';
	}
	return $html . '</ul>';
}

function ce_social_enqueue_style(): void {
	if ( ! wp_style_is( 'ce-social-icons', 'enqueued' ) ) {
		wp_enqueue_style( 'ce-social-icons', get_stylesheet_directory_uri() . '/assets/css/ce-social-icons.css', [], wp_get_theme()->get( 'Version' ) );
	}
}

/**
 * Load the stylesheet in the head when the footer will print icons, so the
 * row never renders unstyled for a moment.
 */
function ce_social_maybe_enqueue(): void {
	if ( '1' === (string) get_option( 'ce_social_footer', '1' ) && ce_social_profiles() ) {
		ce_social_enqueue_style();
	}
}
add_action( 'wp_enqueue_scripts', 'ce_social_maybe_enqueue' );

/**
 * The footer row (footer.php).
 */
function ce_social_footer(): void {
	if ( '1' !== (string) get_option( 'ce_social_footer', '1' ) ) {
		return;
	}
	echo ce_social_icons_html( 'footer' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside the function.
}

/**
 * [ce_social_icons]
 */
function ce_social_icons_shortcode(): string {
	return ce_social_icons_html( 'inline' );
}
add_shortcode( 'ce_social_icons', 'ce_social_icons_shortcode' );

/**
 * Block pattern: a shortcode block holding [ce_social_icons].
 */
function ce_social_register_pattern(): void {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}
	register_block_pattern(
		'compelling-evidence/social-icons',
		[
			'title'       => __( 'Social icons', 'compelling-evidence' ),
			'description' => __( 'Links to the site profiles set in CE Theme Options.', 'compelling-evidence' ),
			'categories'  => [ 'footer', 'text' ],
			'content'     => "<!-- wp:shortcode -->\n[ce_social_icons]\n<!-- /wp:shortcode -->",
		]
	);
}
add_action( 'init', 'ce_social_register_pattern' );
