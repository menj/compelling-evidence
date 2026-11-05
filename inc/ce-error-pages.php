<?php
/**
 * The three wp-content drop-ins and the "Install error pages" button
 * (Appearance > CE Theme Options > Tools & Sync).
 *
 * WordPress shows wp-content/maintenance.php while it updates (503),
 * php-error.php on a fatal PHP error (500) and db-error.php when the database
 * cannot be reached (503). Each file is built from its own entry in
 * ce_error_page_kinds(), so each says what happened. The theme writes them
 * on activation, again on an administrator's next visit when a file is
 * missing or outdated, and when the button is pressed. It replaces a copy
 * another theme left behind (marked "Written by the ... theme; safe to
 * delete"), never touches a file without such a mark, and removes its own
 * files when another theme is activated.
 *
 * @package compelling-evidence
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/ce-error-page-html.php';

/** The marker every drop-in the theme writes carries in its first line. */
const CE_ERR_MARKER = 'Compelling Evidence error page drop-in';

/**
 * Markers that identify a file as the theme's own, this version's and any
 * earlier one's.
 *
 * @return string[]
 */
function ce_error_markers() {
	return array( CE_ERR_MARKER, 'Written by the CE theme; safe to delete' );
}

/**
 * Drop-in file name => page kind.
 *
 * @return array<string,string>
 */
function ce_error_dropins() {
	return array(
		'maintenance.php' => 'maintenance',
		'php-error.php'   => 'php-error',
		'db-error.php'    => 'db-error',
	);
}

/** The folder in wp-content that holds the site's copies of the static pages. */
const CE_STATIC_ERRORS = 'compelling-evidence-errors';

/**
 * The site's copies of the static pages for the web server, relative to
 * wp-content => status code. errors/ in the theme assumes a site at the
 * domain root; these carry the site's own home address, so they also work
 * when WordPress sits in a subfolder.
 *
 * @return array<string,string>
 */
function ce_error_static_files() {
	$out = array();
	foreach ( array( '403', '500', '502', '503', '504' ) as $code ) {
		$out[ CE_STATIC_ERRORS . '/' . $code . '.html' ] = $code;
	}
	return $out;
}

/**
 * Every file the theme writes into wp-content: the drop-ins, then the static
 * copies. File => page kind.
 *
 * @return array<string,string>
 */
function ce_error_files() {
	return array_merge( ce_error_dropins(), ce_error_static_files() );
}

/**
 * The text of any file the theme writes into wp-content.
 *
 * @param string $name A key of ce_error_files().
 * @return string
 */
function ce_error_file_source( $name ) {
	$static = ce_error_static_files();
	if ( ! isset( $static[ $name ] ) ) {
		return ce_error_dropin_source( ce_error_dropins()[ $name ] );
	}
	$site = wp_specialchars_decode( (string) get_bloginfo( 'name' ), ENT_QUOTES );
	$home = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	return '<!-- ' . CE_ERR_MARKER . ' (' . $static[ $name ] . ') ' . (string) wp_get_theme()->get( 'Version' ) . ". Written by the CE theme; safe to delete. -->\n"
		. ce_error_page_html( $static[ $name ], '' !== $site ? $site : 'Compelling Evidence', '' !== $home ? $home : '/' ) . "\n";
}

/**
 * The source of one drop-in.
 *
 * @param string $kind Page kind.
 * @return string
 */
function ce_error_dropin_source( $kind ) {
	$site = wp_specialchars_decode( (string) get_bloginfo( 'name' ), ENT_QUOTES );
	$home = (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH );
	return ce_error_dropin_head( $kind, (string) wp_get_theme()->get( 'Version' ) )
		. ce_error_page_html( $kind, '' !== $site ? $site : 'Compelling Evidence', '' !== $home ? $home : '/' ) . "\n";
}

/**
 * Whether the theme wrote a file.
 *
 * @param string $path File path.
 * @return bool
 */
function ce_error_is_ours( $path ) {
	$head = is_readable( $path ) ? (string) file_get_contents( $path, false, null, 0, 400 ) : ''; // phpcs:ignore WordPress.WP.AlternativeFunctions
	foreach ( ce_error_markers() as $marker ) {
		if ( '' !== $head && false !== strpos( $head, $marker ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Whether a file is a drop-in another theme left behind: it carries a
 * "Written by the ... theme; safe to delete" mark and is not the theme's own.
 *
 * @param string $path File path.
 * @return bool
 */
function ce_error_is_other_theme( $path ) {
	if ( ! is_readable( $path ) || ce_error_is_ours( $path ) ) {
		return false;
	}
	$head = (string) file_get_contents( $path, false, null, 0, 400 ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	// The mark must sit on a comment line of the file's header.
	return 1 === preg_match( '~^//[^\n]*Written by the [^\n]{1,80} theme; safe to delete~m', $head );
}

/**
 * The state of one drop-in in a directory.
 *
 * @param string $name File name.
 * @param string $dir  Directory, wp-content by default.
 * @return string missing | installed | outdated | theme | foreign
 */
function ce_error_state( $name, $dir = '' ) {
	$path = rtrim( '' !== $dir ? $dir : WP_CONTENT_DIR, '/' ) . '/' . $name;
	if ( ! file_exists( $path ) ) {
		return 'missing';
	}
	if ( ce_error_is_other_theme( $path ) ) {
		return 'theme';
	}
	if ( ! ce_error_is_ours( $path ) ) {
		return 'foreign';
	}
	return (string) file_get_contents( $path ) === ce_error_file_source( $name ) ? 'installed' : 'outdated'; // phpcs:ignore WordPress.WP.AlternativeFunctions
}

/**
 * Write the drop-ins into a directory. A missing file, one of the theme's
 * own or a copy another theme left behind is written; a file without a
 * theme's mark is left alone.
 *
 * @param string $dir Directory, wp-content by default.
 * @return array<string,string> File name => written | unchanged | kept | failed.
 */
function ce_error_install( $dir = '' ) {
	$dir    = rtrim( '' !== $dir ? $dir : WP_CONTENT_DIR, '/' );
	$result = array();
	foreach ( array_keys( ce_error_files() ) as $name ) {
		$state = ce_error_state( $name, $dir );
		if ( 'foreign' === $state || 'installed' === $state ) {
			$result[ $name ] = 'foreign' === $state ? 'kept' : 'unchanged';
			continue;
		}
		$target = $dir . '/' . $name;
		if ( ! is_dir( dirname( $target ) ) ) {
			wp_mkdir_p( dirname( $target ) );
		}
		// A temporary name, then a rename, so WordPress never reads half a file.
		$tmp = $target . '.' . uniqid( 'err-', true ) . '.tmp';
		$wrote = false !== @file_put_contents( $tmp, ce_error_file_source( $name ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions, WordPress.PHP.NoSilencedErrors
		if ( $wrote && 'missing' === $state ) {
			// A hard link fails when a file has appeared since the check, so a
			// file someone else wrote in the meantime is never replaced.
			$wrote = @link( $tmp, $target ) || ( ! file_exists( $target ) && @rename( $tmp, $target ) ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		} elseif ( $wrote ) {
			// Ours or another theme's: check again just before the swap.
			$wrote = in_array( ce_error_state( $name, $dir ), array( 'outdated', 'theme' ), true ) && @rename( $tmp, $target ); // phpcs:ignore WordPress.WP.AlternativeFunctions, WordPress.PHP.NoSilencedErrors
		}
		if ( $wrote ) {
			$result[ $name ] = 'written';
			if ( file_exists( $tmp ) ) {
				@unlink( $tmp ); // phpcs:ignore WordPress.WP.AlternativeFunctions, WordPress.PHP.NoSilencedErrors
			}
		} else {
			$result[ $name ] = 'failed';
			if ( file_exists( $tmp ) ) {
				@unlink( $tmp ); // phpcs:ignore WordPress.WP.AlternativeFunctions, WordPress.PHP.NoSilencedErrors
			}
		}
	}
	return $result;
}

/**
 * What the drop-ins embed: the theme version, the site name and the home
 * address. A change means the files are outdated.
 *
 * @return string
 */
function ce_error_fingerprint() {
	return md5( (string) wp_get_theme()->get( 'Version' ) . '|' . get_bloginfo( 'name' ) . '|' . home_url( '/' ) );
}

/**
 * Whether the drop-ins need writing: a file is missing or is another theme's
 * leftover, or the theme last wrote them for another version or site name.
 *
 * @return bool
 */
function ce_error_needs_install() {
	if ( get_option( 'ce_error_pages_fingerprint' ) !== ce_error_fingerprint() ) {
		return true;
	}
	foreach ( array_keys( ce_error_files() ) as $name ) {
		// Missing, another theme's leftover, or a copy of the theme's own that
		// differs from what it would write now (edited or half restored).
		if ( in_array( ce_error_state( $name ), array( 'missing', 'theme', 'outdated' ), true ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Write the drop-ins without the button: on activation, and on an
 * administrator's visit when they are missing or outdated. On a network only
 * the main site writes, since wp-content is shared. A clean run is
 * remembered; a failed one (wp-content not writable) is tried again on the
 * next visit.
 *
 * @return void
 */
function ce_error_auto_install() {
	if ( is_multisite() && ! is_main_site() ) {
		return;
	}
	if ( ! in_array( 'failed', ce_error_install(), true ) ) {
		update_option( 'ce_error_pages_fingerprint', ce_error_fingerprint(), false );
	}
}
add_action( 'after_switch_theme', 'ce_error_auto_install' );

/**
 * On an administrator's admin page load, write the drop-ins when they need it.
 *
 * @return void
 */
function ce_error_maybe_install() {
	if ( current_user_can( 'edit_theme_options' ) && ce_error_needs_install() ) {
		ce_error_auto_install();
	}
}
add_action( 'admin_init', 'ce_error_maybe_install' );

/**
 * Remove the theme's own drop-ins when another theme is activated. Only files
 * carrying one of the theme's markers are deleted. On a network the files
 * are left in place: wp-content is shared, and another site may still use
 * this theme.
 *
 * @return void
 */
function ce_error_remove() {
	if ( is_multisite() ) {
		return;
	}
	foreach ( array_keys( ce_error_files() ) as $name ) {
		$path = WP_CONTENT_DIR . '/' . $name;
		if ( ce_error_is_ours( $path ) ) {
			wp_delete_file( $path );
		}
	}
	if ( is_dir( WP_CONTENT_DIR . '/' . CE_STATIC_ERRORS ) ) {
		@rmdir( WP_CONTENT_DIR . '/' . CE_STATIC_ERRORS ); // phpcs:ignore WordPress.WP.AlternativeFunctions, WordPress.PHP.NoSilencedErrors -- only when empty.
	}
	delete_option( 'ce_error_pages_fingerprint' );
}
add_action( 'switch_theme', 'ce_error_remove' );

/**
 * The address the button returns to.
 *
 * @return string
 */
function ce_error_pages_url() {
	return admin_url( 'themes.php?page=ce-theme-options&tab=tools' );
}

/**
 * "Install error pages": nonce and manage_options. On a network only the main
 * site writes, since wp-content is shared.
 */
function ce_handle_install_error_pages() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You are not allowed to do that.', 'compelling-evidence' ), 403 );
	}
	check_admin_referer( 'ce_install_error_pages' );
	$result = ( is_multisite() && ! is_main_site() ) ? array() : ce_error_install();
	update_option( 'ce_error_pages_result', $result, false );
	if ( $result && ! in_array( 'failed', $result, true ) ) {
		update_option( 'ce_error_pages_fingerprint', ce_error_fingerprint(), false );
	}
	wp_safe_redirect( add_query_arg( 'ce_errors', '1', ce_error_pages_url() ) . '#ce_error-pages' );
	exit;
}
add_action( 'admin_post_ce_install_error_pages', 'ce_handle_install_error_pages' );

/**
 * The lines for .htaccess that point the web server at the site's copies of
 * the static pages in wp-content/compelling-evidence-errors/.
 *
 * @return string
 */
function ce_error_htaccess_snippet() {
	$base  = untrailingslashit( (string) wp_parse_url( content_url( '/' ), PHP_URL_PATH ) ) . '/' . CE_STATIC_ERRORS . '/';
	$lines = array( '# CE: pages for the errors the web server answers before WordPress runs' );
	foreach ( array( 403, 500, 502, 503, 504 ) as $code ) {
		$lines[] = 'ErrorDocument ' . $code . ' ' . $base . $code . '.html';
	}
	return implode( "\n", $lines );
}

/**
 * The error pages section: status table, button and .htaccess lines. Its own
 * form, so it must sit outside any settings form.
 */
function ce_render_error_pages_section() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$labels = array(
		'missing'   => __( 'Not installed', 'compelling-evidence' ),
		'installed' => __( 'Installed, current', 'compelling-evidence' ),
		'outdated'  => __( 'Installed by the theme, outdated', 'compelling-evidence' ),
		'theme'     => __( 'Left behind by another theme; replaced on the next install', 'compelling-evidence' ),
		'foreign'   => __( 'Another file is there; left alone', 'compelling-evidence' ),
	);
	$when   = array(
		'maintenance.php' => __( 'WordPress is updating (503, Retry-After)', 'compelling-evidence' ),
		'php-error.php'   => __( 'a fatal PHP error stops a page (500)', 'compelling-evidence' ),
		'db-error.php'    => __( 'the database cannot be reached (503, Retry-After)', 'compelling-evidence' ),
	);
	foreach ( ce_error_static_files() as $name => $code ) {
		/* translators: %s: HTTP status code */
		$when[ $name ] = sprintf( __( 'the web server answers %s, through the .htaccess lines below', 'compelling-evidence' ), $code );
	}
	$words  = array(
		'written'   => __( 'written', 'compelling-evidence' ),
		'unchanged' => __( 'already current', 'compelling-evidence' ),
		'kept'      => __( 'left alone, another file is there', 'compelling-evidence' ),
		'failed'    => __( 'could not be written, wp-content is not writable', 'compelling-evidence' ),
	);
	?>
	<div class="ce_error-pages" id="ce_error-pages">
	<h2><?php esc_html_e( 'Error pages', 'compelling-evidence' ); ?></h2>
	<?php
	$last = get_option( 'ce_error_pages_result' );
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display of the last result only.
	if ( isset( $_GET['ce_errors'] ) && is_array( $last ) && $last ) {
		$parts = array();
		foreach ( $last as $file => $what ) {
			$parts[] = $file . ': ' . ( $words[ $what ] ?? $what );
		}
		printf( '<div class="notice %s inline"><p>%s</p></div>', in_array( 'failed', $last, true ) ? 'notice-error' : 'notice-success', esc_html( implode( '; ', $parts ) ) );
	}
	?>
	<p><?php esc_html_e( 'WordPress shows these files from wp-content when it cannot build a page. Each one is self-contained, so it works with the theme half uploaded or the database down. The theme writes them when it is activated and keeps them current; the button writes them at once. A copy another theme left behind is replaced, a file without a theme’s mark is never overwritten, and the theme removes its own files when another theme is activated.', 'compelling-evidence' ); ?></p>
	<table class="widefat striped">
		<thead><tr><th scope="col"><?php esc_html_e( 'File', 'compelling-evidence' ); ?></th><th scope="col"><?php esc_html_e( 'Shown when', 'compelling-evidence' ); ?></th><th scope="col"><?php esc_html_e( 'Status', 'compelling-evidence' ); ?></th></tr></thead>
		<tbody>
		<?php foreach ( $when as $name => $label ) : ?>
			<tr><td><code>wp-content/<?php echo esc_html( $name ); ?></code></td><td><?php echo esc_html( $label ); ?></td><td><?php echo esc_html( $labels[ ce_error_state( $name ) ] ); ?></td></tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php if ( is_multisite() && ! is_main_site() ) : ?>
		<p><?php esc_html_e( 'The whole network shares wp-content, so the error pages are installed from the main site.', 'compelling-evidence' ); ?></p>
	<?php else : ?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:1em 0">
		<input type="hidden" name="action" value="ce_install_error_pages">
		<?php wp_nonce_field( 'ce_install_error_pages' ); ?>
		<?php submit_button( __( 'Install error pages', 'compelling-evidence' ), 'secondary', 'submit', false ); ?>
	</form>
	<?php endif; ?>
	<p><?php esc_html_e( 'For errors the web server answers before WordPress starts, the theme writes the static pages above into wp-content/compelling-evidence-errors, with this site’s home address. Add these lines to .htaccess to use them:', 'compelling-evidence' ); ?></p>
	<textarea readonly rows="6" class="large-text code" aria-label="<?php esc_attr_e( '.htaccess lines', 'compelling-evidence' ); ?>"><?php echo esc_textarea( ce_error_htaccess_snippet() ); ?></textarea>
	</div>
	<?php
}
