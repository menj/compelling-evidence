<?php
/**
 * Compelling Evidence: removes development files from a live theme folder.
 *
 * The owner uploads the theme over FTP, and on 4 November 2026 that upload
 * carried the repository (.git, 503 files) to the live site. A readable .git
 * hands out the theme's whole source history, and the tests, build scripts,
 * tools and agent notes serve no page. On a production server this deletes
 * them on activation and once per theme version.
 *
 * The files stay where they are worked on: wp_get_environment_type() is
 * "local" or "development", or CE_KEEP_DEV_FILES is defined true in
 * wp-config.php. Until the cleanup runs, and on a server where PHP cannot
 * delete them, the "Compelling Evidence: block direct file access" section
 * in .htaccess refuses them (inc/ce-security.php).
 *
 * @since 2.18.6
 */

defined( 'ABSPATH' ) || exit;

/**
 * Paths, relative to the theme root, that have no business on a server:
 * every entry whose name starts with .git (.git, .github, .githooks,
 * .gitattributes, .gitignore), .claude, tests, bin and tools.
 *
 * @param string $root Theme root.
 * @return string[]
 */
function ce_dev_paths( $root = '' ) {
	$root  = '' === $root ? get_stylesheet_directory() : $root;
	$paths = array( '.claude', 'tests', 'bin', 'tools' );
	$items = is_dir( $root ) ? scandir( $root ) : false;

	foreach ( false === $items ? array() : $items as $item ) {
		if ( 0 === strpos( $item, '.git' ) ) {
			$paths[] = $item;
		}
	}

	return (array) apply_filters( 'ce_dev_paths', $paths );
}

/**
 * Whether the development files must stay: a development environment, or
 * CE_KEEP_DEV_FILES defined true.
 *
 * @return bool
 */
function ce_keep_dev_files() {
	if ( defined( 'CE_KEEP_DEV_FILES' ) && CE_KEEP_DEV_FILES ) {
		return true;
	}

	$env = function_exists( 'wp_get_environment_type' ) ? wp_get_environment_type() : 'production';

	return in_array( $env, array( 'local', 'development' ), true );
}

/**
 * Deletes a file or a directory tree inside the theme root. Refuses
 * anything that resolves outside it, and follows no symbolic link out.
 *
 * @param string $path Absolute path.
 * @param string $root Theme root.
 * @return bool
 */
function ce_remove_path( $path, $root ) {
	// The cleanup renews its lock every 200 entries, and at least every 30
	// seconds, so a long run over a large .git tree on a slow disk is never
	// taken for a dead one, and a holder that lost its lock stops within
	// 30 seconds.
	static $seen = 0, $renewed = 0;
	if ( ! $renewed ) {
		$renewed = time();
	}
	// A holder that finds its lock gone (another request took it over after
	// a long pause) stops at once and leaves the rest to that request.
	if ( ! empty( $GLOBALS['ce_dev_files_lost'] ) ) {
		return false;
	}
	if ( ( 0 === ++$seen % 200 || time() - $renewed >= 30 ) && ! empty( $GLOBALS['ce_dev_files_token'] ) && defined( 'CE_LOCK_READY' ) ) {
		$renewed = time();
		if ( ! ce_lock_renew( 'ce_dev_files_lock', $GLOBALS['ce_dev_files_token'] ) ) {
			$GLOBALS['ce_dev_files_lost'] = true; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			return false;
		}
	}
	$real_root = realpath( $root );
	$real      = realpath( $path );

	if ( ! $real_root || ( ! $real && ! is_link( $path ) ) ) {
		return false;
	}

	if ( is_link( $path ) || is_file( $path ) ) {
		// A link is removed itself, never what it points to.
		if ( ! is_link( $path ) && 0 !== strpos( $real, $real_root . DIRECTORY_SEPARATOR ) ) {
			return false;
		}
		return @unlink( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
	}

	if ( 0 !== strpos( $real, $real_root . DIRECTORY_SEPARATOR ) ) {
		return false;
	}

	$items = scandir( $path );

	if ( false === $items ) {
		return false;
	}

	foreach ( array_diff( $items, array( '.', '..' ) ) as $item ) {
		ce_remove_path( $path . DIRECTORY_SEPARATOR . $item, $root );
	}

	// Lost the lock below, or while the last entry was going: leave this
	// folder, and every one above it, as it is. The lease is renewed (and so
	// checked) before every folder is removed.
	if ( ! empty( $GLOBALS['ce_dev_files_lost'] ) ) {
		return false;
	}
	if ( ! empty( $GLOBALS['ce_dev_files_token'] ) && defined( 'CE_LOCK_READY' ) && ! ce_lock_renew( 'ce_dev_files_lock', $GLOBALS['ce_dev_files_token'] ) ) {
		$GLOBALS['ce_dev_files_lost'] = true; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		return false;
	}

	return @rmdir( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors, WordPress.WP.AlternativeFunctions
}

/**
 * Removes the development files, once per theme version, on production.
 *
 * @param string $root    Theme root; the active theme's when empty.
 * @param string $version Theme version; the active theme's when empty.
 * @return void
 */
function ce_remove_dev_files( $root = '', $version = '' ) {
	$root    = '' === $root ? get_stylesheet_directory() : $root;
	$version = '' === $version ? (string) wp_get_theme()->get( 'Version' ) : $version;

	if ( get_option( 'ce_dev_files_removed' ) === $version ) {
		return;
	}

	// A development environment: nothing is deleted, and the version is not
	// recorded, so the files go once the site runs as production.
	if ( ce_keep_dev_files() ) {
		return;
	}

	// One cleanup at a time, whichever hook started it (inc/ce-lock.php); a
	// lock not renewed for ten minutes is from a request that died, and is
	// taken over. Until the lock code has arrived, nothing is deleted.
	$token = defined( 'CE_LOCK_READY' ) ? ce_lock_take( 'ce_dev_files_lock', 10 * MINUTE_IN_SECONDS ) : '';
	if ( '' === $token ) {
		return;
	}
	$GLOBALS['ce_dev_files_token'] = $token; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	$GLOBALS['ce_dev_files_lost']  = false; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

	$left = array();

	foreach ( ce_dev_paths( $root ) as $rel ) {
		$path = $root . DIRECTORY_SEPARATOR . ltrim( $rel, '/\\' );

		if ( file_exists( $path ) || is_link( $path ) ) {
			ce_remove_path( $path, $root );
		}
		clearstatcache();
		if ( file_exists( $path ) || is_link( $path ) ) {
			$left[] = $rel;
		}
	}

	// The version is recorded only once every path is gone. A path PHP could
	// not delete (file ownership, say) is tried again an hour later from the
	// front end, and on every admin page load.
	// Lost the lock part-way: record nothing, and leave the lock alone.
	if ( ! empty( $GLOBALS['ce_dev_files_lost'] ) || ! ce_lock_holds( 'ce_dev_files_lock', $token ) ) {
		unset( $GLOBALS['ce_dev_files_token'], $GLOBALS['ce_dev_files_lost'] );
		return;
	}

	if ( $left ) {
		set_transient( 'ce_dev_files_retry', $left, HOUR_IN_SECONDS );
	} else {
		update_option( 'ce_dev_files_removed', $version, true ); // Autoloaded: the front end reads it on every request.
	}

	ce_lock_release( 'ce_dev_files_lock', $token );
	unset( $GLOBALS['ce_dev_files_token'] );
}
// Both hooks pass arguments of their own (after_switch_theme the old
// theme's name), so neither may reach the $root parameter.
function ce_remove_dev_files_hook() {
	ce_remove_dev_files();
}
add_action( 'after_switch_theme', 'ce_remove_dev_files_hook', 10, 0 );
add_action( 'admin_init', 'ce_remove_dev_files_hook', 10, 0 );

/**
 * The same, on the first front-end request after an FTP update: such an
 * update fires no switch hook, and the site may see no admin visit for
 * days. ce_remove_dev_files() takes the lock that keeps two requests from
 * deleting at once.
 */
function ce_remove_dev_files_front() {
	// The autoloaded record first, so a finished cleanup costs no query.
	if ( is_admin() || get_option( 'ce_dev_files_removed' ) === (string) wp_get_theme()->get( 'Version' ) || ce_keep_dev_files() || get_transient( 'ce_dev_files_retry' ) ) {
		return;
	}
	ce_remove_dev_files();
}
add_action( 'init', 'ce_remove_dev_files_front', 99 );
