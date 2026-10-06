<?php
/**
 * Network links — cross-site content links between the five sister sites.
 *
 * Best of Islam, Abrahamic, Compelling Evidence, Murtadd and Laylat al-Qadr
 * link to one another's content. Links are curated per post (meta box) and
 * site-wide (Appearance → Network Links), and only point at registered,
 * enabled sister sites. Scripts and styles live in the theme's own js/ and
 * css/ directories.
 *
 * @package compelling-evidence
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CE_NL_OPTION = 'ce_network';
const CE_NL_META   = '_' . 'ce_network_links';

/** The registry shared by all five themes: key => name, default URL, accent. */
function ce_nl_registry() {
	return array(
		'boi' => array( 'name' => 'Best of Islam',       'url' => 'https://bestofislam.org',           'color' => '#1F6FCC' ),
		'abr' => array( 'name' => 'Abrahamic',           'url' => 'https://abrahamic-religions.com',   'color' => '#B89555' ),
		'ce'  => array( 'name' => 'Compelling Evidence', 'url' => 'https://compelling-evidence.com',   'color' => '#E8455A' ),
		'mur' => array( 'name' => 'Murtadd',             'url' => 'https://murtadd.org',               'color' => '#0F6E56' ),
		'lq'  => array( 'name' => 'Laylat al-Qadr',      'url' => 'https://lailatulqadar.guide',       'color' => '#C9A85C' ),
		'pau' => array( 'name' => 'Apostle of Doom',      'url' => 'https://apostleofdoom.org',         'color' => '#7A4E2D' ),
	);
}

/** Post types that show the block (filterable). */
function ce_nl_post_types() {
	return (array) apply_filters( 'ce_nl_post_types', array( 'ce_article' ) );
}

/** Saved options merged over defaults. */
function ce_nl_options() {
	$defaults = array(
		'sites'   => array(),
		'links'   => '',
		'display' => array( 'enabled' => 1, 'heading' => 'Continue on our sister sites', 'max' => 4 ),
	);
	foreach ( ce_nl_registry() as $key => $site ) {
		$defaults['sites'][ $key ] = array( 'enabled' => 1, 'url' => $site['url'], 'color' => $site['color'] );
	}
	$saved = get_option( CE_NL_OPTION, array() );
	$saved = is_array( $saved ) ? $saved : array();
	$out   = $defaults;
	foreach ( $defaults['sites'] as $key => $row ) {
		if ( isset( $saved['sites'][ $key ] ) && is_array( $saved['sites'][ $key ] ) ) {
			$out['sites'][ $key ] = array_merge( $row, $saved['sites'][ $key ] );
		}
	}
	if ( isset( $saved['links'] ) ) {
		$out['links'] = (string) $saved['links'];
	}
	if ( isset( $saved['display'] ) && is_array( $saved['display'] ) ) {
		$out['display'] = array_merge( $defaults['display'], $saved['display'] );
	}
	return $out;
}

/** Which registered site a URL belongs to, or ''. Matches on host only. */
function ce_nl_site_for_url( $url, $opts ) {
	$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
	$host = preg_replace( '/^www\./', '', $host );
	foreach ( $opts['sites'] as $key => $row ) {
		$h = strtolower( (string) wp_parse_url( $row['url'], PHP_URL_HOST ) );
		if ( $host && $host === preg_replace( '/^www\./', '', $h ) ) {
			return $key;
		}
	}
	return '';
}

/** Parse "Title | URL" lines into validated links for other, enabled sites. */
function ce_nl_parse( $text, $opts ) {
	$links = array();
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $text ) as $line ) {
		$parts = array_map( 'trim', explode( '|', $line ) );
		if ( count( $parts ) < 2 || '' === $parts[0] ) {
			continue;
		}
		$url = esc_url_raw( $parts[1], array( 'http', 'https' ) );
		$key = $url ? ce_nl_site_for_url( $url, $opts ) : '';
		if ( '' === $key || 'ce' === $key || empty( $opts['sites'][ $key ]['enabled'] ) ) {
			continue;
		}
		$links[] = array( 'title' => $parts[0], 'url' => $url, 'site' => $key );
	}
	return $links;
}

/* ── Front end ─────────────────────────────────────────────────────── */

function ce_nl_render( $post_id = 0 ) {
	$opts = ce_nl_options();
	if ( empty( $opts['display']['enabled'] ) ) {
		return '';
	}
	$post_id  = $post_id ? $post_id : get_the_ID();
	$links    = array_merge(
		ce_nl_parse( get_post_meta( $post_id, CE_NL_META, true ), $opts ),
		ce_nl_parse( $opts['links'], $opts )
	);
	$seen = array();
	$max  = max( 1, (int) $opts['display']['max'] );
	$html = '';
	foreach ( $links as $link ) {
		if ( isset( $seen[ $link['url'] ] ) || count( $seen ) >= $max ) {
			continue;
		}
		$seen[ $link['url'] ] = true;
		$site  = ce_nl_registry()[ $link['site'] ];
		$color = sanitize_hex_color( $opts['sites'][ $link['site'] ]['color'] );
		$html .= sprintf(
			'<li class="nl__item" style="--nl-site:%s"><a class="nl__link" href="%s"><span class="nl__site">%s</span><span class="nl__title">%s</span></a></li>',
			esc_attr( $color ? $color : $site['color'] ),
			esc_url( $link['url'] ),
			esc_html( $site['name'] ),
			esc_html( $link['title'] )
		);
	}
	if ( '' === $html ) {
		return '';
	}
	wp_enqueue_style( 'ce_network-links' );
	return '<aside class="nl" aria-label="' . esc_attr( $opts['display']['heading'] ) . '"><h2 class="nl__heading">'
		. esc_html( $opts['display']['heading'] ) . '</h2><ul class="nl__list">' . $html . '</ul></aside>';
}

function ce_nl_register_assets() {
	wp_register_style( 'ce_network-links', get_theme_file_uri( 'assets/css/network-links.css' ), array(), filemtime( get_theme_file_path( 'assets/css/network-links.css' ) ) );
}
add_action( 'wp_enqueue_scripts', 'ce_nl_register_assets' );

function ce_nl_append( $content ) {
	if ( is_singular( ce_nl_post_types() ) && in_the_loop() && is_main_query() ) {
		$content .= ce_nl_render();
	}
	return $content;
}
add_filter( 'the_content', 'ce_nl_append', 20 );

add_shortcode( 'network_links', function () {
	return ce_nl_render();
} );

/* ── Per-post meta box ─────────────────────────────────────────────── */

add_action( 'add_meta_boxes', function () {
	add_meta_box( 'ce_network', __( 'Sister-site links', 'compelling-evidence' ), function ( $post ) {
		wp_nonce_field( 'ce_nl_meta', 'ce_nl_nonce' );
		echo '<p class="description">' . esc_html__( 'One per line: Title | URL. Only registered sister sites are shown.', 'compelling-evidence' ) . '</p>';
		printf( '<textarea class="widefat" rows="5" name="ce_nl_links">%s</textarea>', esc_textarea( (string) get_post_meta( $post->ID, CE_NL_META, true ) ) );
	}, ce_nl_post_types(), 'side' );
} );

add_action( 'save_post', function ( $post_id ) {
	if ( ! isset( $_POST['ce_nl_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['ce_nl_nonce'] ) ), 'ce_nl_meta' ) || ! current_user_can( 'edit_post', $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}
	update_post_meta( $post_id, CE_NL_META, sanitize_textarea_field( wp_unslash( isset( $_POST['ce_nl_links'] ) ? $_POST['ce_nl_links'] : '' ) ) );
} );

/* ── Settings page (tabbed) ────────────────────────────────────────── */

add_action( 'admin_menu', function () {
	add_theme_page( __( 'Network Links', 'compelling-evidence' ), __( 'Network Links', 'compelling-evidence' ), 'manage_options', 'ce_network', 'ce_nl_page' );
} );

add_action( 'admin_init', function () {
	register_setting( 'ce_network', CE_NL_OPTION, array( 'sanitize_callback' => 'ce_nl_sanitize' ) );
} );

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( 'appearance_page_ce_network' !== $hook ) {
		return;
	}
	wp_enqueue_style( 'ce_network-admin', get_theme_file_uri( 'assets/css/network-links-admin.css' ), array(), filemtime( get_theme_file_path( 'assets/css/network-links-admin.css' ) ) );
	wp_enqueue_script( 'ce_network-admin', get_theme_file_uri( 'assets/js/network-links-admin.js' ), array(), filemtime( get_theme_file_path( 'assets/js/network-links-admin.js' ) ), true );
} );

function ce_nl_sanitize( $in ) {
	$in  = is_array( $in ) ? $in : array();
	$out = array( 'sites' => array(), 'display' => array() );
	foreach ( ce_nl_registry() as $key => $site ) {
		$row                  = isset( $in['sites'][ $key ] ) ? (array) $in['sites'][ $key ] : array();
		$url                  = isset( $row['url'] ) ? esc_url_raw( trim( $row['url'] ), array( 'http', 'https' ) ) : '';
		$color                = isset( $row['color'] ) ? sanitize_hex_color( $row['color'] ) : '';
		$out['sites'][ $key ] = array(
			'enabled' => empty( $row['enabled'] ) ? 0 : 1,
			'url'     => $url ? untrailingslashit( $url ) : '',
			'color'   => $color ? $color : $site['color'],
		);
	}
	$out['links']   = sanitize_textarea_field( isset( $in['links'] ) ? $in['links'] : '' );
	$d              = isset( $in['display'] ) ? (array) $in['display'] : array();
	$out['display'] = array(
		'enabled' => empty( $d['enabled'] ) ? 0 : 1,
		'heading' => isset( $d['heading'] ) ? sanitize_text_field( $d['heading'] ) : '',
		'max'     => isset( $d['max'] ) ? min( 12, max( 1, (int) $d['max'] ) ) : 4,
	);
	return $out;
}

function ce_nl_page() {
	$o = ce_nl_options();
	$n = CE_NL_OPTION;
	?>
	<div class="wrap nl-admin">
		<h1><?php esc_html_e( 'Network Links', 'compelling-evidence' ); ?></h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'ce_network' ); ?>
			<nav class="nl-admin__tabs" role="tablist">
				<button type="button" class="nl-admin__tab" role="tab" data-tab="sites"><?php esc_html_e( 'Sites', 'compelling-evidence' ); ?></button>
				<button type="button" class="nl-admin__tab" role="tab" data-tab="links"><?php esc_html_e( 'Links', 'compelling-evidence' ); ?></button>
				<button type="button" class="nl-admin__tab" role="tab" data-tab="display"><?php esc_html_e( 'Display', 'compelling-evidence' ); ?></button>
			</nav>

			<section class="nl-admin__panel" data-panel="sites">
				<?php foreach ( ce_nl_registry() as $key => $site ) : ?>
					<div class="nl-admin__row">
						<label><input type="checkbox" name="<?php echo esc_attr( $n ); ?>[sites][<?php echo esc_attr( $key ); ?>][enabled]" value="1" <?php checked( $o['sites'][ $key ]['enabled'] ); ?>> <strong><?php echo esc_html( $site['name'] ); ?></strong><?php echo 'ce' === $key ? ' <em>' . esc_html__( '(this site)', 'compelling-evidence' ) . '</em>' : ''; ?></label>
						<input type="url" class="regular-text" name="<?php echo esc_attr( $n ); ?>[sites][<?php echo esc_attr( $key ); ?>][url]" value="<?php echo esc_attr( $o['sites'][ $key ]['url'] ); ?>">
						<input type="color" name="<?php echo esc_attr( $n ); ?>[sites][<?php echo esc_attr( $key ); ?>][color]" value="<?php echo esc_attr( $o['sites'][ $key ]['color'] ); ?>">
					</div>
				<?php endforeach; ?>
			</section>

			<section class="nl-admin__panel" data-panel="links">
				<p class="description"><?php esc_html_e( 'Site-wide links, one per line: Title | URL. Each URL must belong to an enabled sister site.', 'compelling-evidence' ); ?></p>
				<textarea class="large-text code" rows="12" name="<?php echo esc_attr( $n ); ?>[links]"><?php echo esc_textarea( $o['links'] ); ?></textarea>
			</section>

			<section class="nl-admin__panel" data-panel="display">
				<div class="nl-admin__row"><label><input type="checkbox" name="<?php echo esc_attr( $n ); ?>[display][enabled]" value="1" <?php checked( $o['display']['enabled'] ); ?>> <?php esc_html_e( 'Show the block after content', 'compelling-evidence' ); ?></label></div>
				<div class="nl-admin__row"><label><?php esc_html_e( 'Heading', 'compelling-evidence' ); ?> <input type="text" class="regular-text" name="<?php echo esc_attr( $n ); ?>[display][heading]" value="<?php echo esc_attr( $o['display']['heading'] ); ?>"></label></div>
				<div class="nl-admin__row"><label><?php esc_html_e( 'Maximum links', 'compelling-evidence' ); ?> <input type="number" min="1" max="12" name="<?php echo esc_attr( $n ); ?>[display][max]" value="<?php echo esc_attr( $o['display']['max'] ); ?>"></label></div>
			</section>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
