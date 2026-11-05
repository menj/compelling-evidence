<?php
/**
 * Self-test for the "block direct file access" lines that inc/ce-security.php
 * writes into .htaccess. Run: php tests/htaccess-files-test.php
 *
 * Takes ce_sec_rewrite_prefix() and ce_sec_files_htaccess_lines() out of inc/ce-security.php, builds the lines
 * for a site at the domain root and for one in a subfolder, checks their
 * shape (balanced IfModule, each RewriteCond followed by a RewriteRule,
 * every pattern a valid regular expression) and runs them over request
 * paths the way mod_rewrite does in .htaccess: the pattern is matched
 * against the path below the .htaccess folder, a RewriteCond against the
 * full REQUEST_URI.
 *
 * @since 2.18.6
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}

define( 'ABSPATH', '/srv/site/' );
function wp_normalize_path( $p ) { return str_replace( '\\', '/', (string) $p ); } // phpcs:ignore
function trailingslashit( $p ) { return rtrim( (string) $p, '/\\' ) . '/'; } // phpcs:ignore
function untrailingslashit( $p ) { return rtrim( (string) $p, '/\\' ); } // phpcs:ignore
function get_stylesheet_directory() { return $GLOBALS['ce_theme_root'] . '/compelling-evidence'; } // phpcs:ignore
function get_theme_root() { return $GLOBALS['ce_theme_root']; } // phpcs:ignore

$ce_src = (string) file_get_contents( dirname( __DIR__ ) . '/inc/ce-security.php' );
foreach ( array( 'ce_sec_rewrite_prefix', 'ce_sec_files_htaccess_lines' ) as $ce_fn ) {
	if ( ! preg_match( '/^function ' . $ce_fn . '\(.*?^}$/ms', $ce_src, $ce_m ) ) {
		echo "FAIL $ce_fn not found in inc/ce-security.php\n";
		exit( 1 );
	}
	eval( $ce_m[0] ); // phpcs:ignore Squiz.PHP.Eval.Discouraged -- test only.
}

$ce_fail = 0;
function ce_check( $ok, $label ) {
	global $ce_fail;
	echo ( $ok ? 'ok   ' : 'FAIL ' ) . $label . "\n";
	$ce_fail += $ok ? 0 : 1;
}

/**
 * Whether the lines answer 403 for a request, as mod_rewrite would.
 *
 * @param string[] $lines Lines.
 * @param string   $uri   REQUEST_URI.
 * @param string   $base  URL path of the .htaccess folder, with slashes.
 * @return bool
 */
function ce_forbidden( $lines, $uri, $base ) {
	$path  = (string) substr( $uri, strlen( $base ) );
	$conds = array();
	foreach ( $lines as $line ) {
		if ( preg_match( '/^RewriteCond %\{REQUEST_URI\} (!?)(\S+)$/', $line, $m ) ) {
			$conds[] = array( '!' === $m[1], $m[2] );
			continue;
		}
		if ( preg_match( '/^RewriteRule (\S+) - \[F\]$/', $line, $m ) ) {
			$pass = 1 === preg_match( '#' . $m[1] . '#', $path );
			foreach ( $conds as $c ) {
				$hit  = 1 === preg_match( '#' . $c[1] . '#', $uri );
				$pass = $pass && ( $c[0] ? ! $hit : $hit );
			}
			$conds = array();
			if ( $pass ) {
				return true;
			}
		}
	}
	return false;
}

$ce_denied  = array( '.git/config', '.git/HEAD', '.gitignore', '.claude/CLAUDE.md', 'wp-content/themes/compelling-evidence/.git/config', 'wp-content/themes/compelling-evidence/.claude/FARUQI.md', 'wp-includes/version.php', 'wp-includes/blocks/archives.php', 'wp-includes/version.php/x', 'wp-includes/blocks/archives.php/wp-includes/js/tinymce/wp-tinymce.php', 'wp-includes/blocks/archives.php/wp-includes/ms-files.php', 'wp-settings.php/x', 'wp-admin/includes/admin.php', 'wp-content/themes/compelling-evidence/functions.php', 'wp-content/themes/twentytwentyfive/functions.php', 'wp-content/themes/compelling-evidence/tests/unlist-test.php', 'wp-content/themes/compelling-evidence/bin/build-error-pages.php', 'wp-content/themes/compelling-evidence/tools/x.py', 'wp-content/themes/compelling-evidence/tools/', 'wp-config.php', 'wp-settings.php', 'wp-config-sample.php', 'wp-admin/install.php', 'readme.html' );
$ce_allowed = array( '', 'index.php', 'wp-login.php', 'wp-cron.php', 'xmlrpc.php', 'wp-admin/', 'wp-admin/index.php', 'wp-admin/admin-ajax.php', 'wp-admin/post.php', 'wp-admin/load-scripts.php', 'wp-json/wp/v2/posts', 'wp-includes/js/tinymce/wp-tinymce.php', 'wp-includes/ms-files.php', 'wp-includes/js/dist/block-editor.min.js', 'wp-includes/css/dist/block-library/style.min.css', 'wp-content/themes/compelling-evidence/style.css', 'wp-content/themes/compelling-evidence/assets/js/theme.js', 'wp-content/themes/compelling-evidence/testsuite.css', 'wp-content/uploads/2026/11/photo.webp', 'what-is-the-qibla/', 'binary-choices/', 'documents/', 'articles/does-god-exist/', 'journeys/', 'quiz/', 'wp-content/themes/compelling-evidence/doc/readme.md' );

$ce_sites = array(
	'at the domain root' => array( '/srv/site/', '/' ),
	'in a subfolder'     => array( '/srv/site/', '/blog/' ),
);
foreach ( $ce_sites as $ce_label => $ce_site ) {
	$GLOBALS['ce_theme_root'] = '/srv/site/wp-content/themes';
	$ce_lines = ce_sec_files_htaccess_lines( $ce_site[0] );

	ce_check( '<IfModule mod_rewrite.c>' === $ce_lines[0] && '</IfModule>' === end( $ce_lines ) && 2 === count( preg_grep( '/IfModule/', $ce_lines ) ), "$ce_label: one balanced IfModule guard" );
	$ce_shape = true;
	foreach ( $ce_lines as $ce_i => $ce_line ) {
		if ( 0 === strpos( $ce_line, 'RewriteCond' ) && 0 !== strpos( $ce_lines[ $ce_i + 1 ] ?? '', 'RewriteRule' ) ) {
			$ce_shape = false;
		}
		if ( preg_match( '/^Rewrite(?:Rule|Cond %\{REQUEST_URI\} !?)\s*(\S+)/', $ce_line, $ce_m ) && false === @preg_match( '#' . $ce_m[1] . '#', '' ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors
			$ce_shape = false;
		}
		if ( ! preg_match( '/^(<IfModule mod_rewrite\.c>|<\/IfModule>|RewriteEngine On|RewriteCond %\{REQUEST_URI\} \S+|RewriteRule \S+ - \[F\])$/', $ce_line ) ) {
			$ce_shape = false;
		}
	}
	ce_check( $ce_shape, "$ce_label: every line is a known directive with a valid pattern" );

	foreach ( $ce_denied as $ce_p ) {
		ce_check( ce_forbidden( $ce_lines, $ce_site[1] . $ce_p, $ce_site[1] ), "$ce_label: 403 for /$ce_p" );
	}
	foreach ( $ce_allowed as $ce_p ) {
		ce_check( ! ce_forbidden( $ce_lines, $ce_site[1] . $ce_p, $ce_site[1] ), "$ce_label: open /$ce_p" );
	}
}

// A folder name with a regex metacharacter is quoted, so its own paths match.
$ce_pfx = ce_sec_rewrite_prefix( '/srv/wp.6/', '/srv/' );
ce_check( 'wp\\.6/' === $ce_pfx && 1 === preg_match( '#^' . $ce_pfx . 'wp-includes/.+\\.php$#', 'wp.6/wp-includes/version.php' ) && 0 === preg_match( '#^' . $ce_pfx . 'wp-includes/.+\\.php$#', 'wpx6/wp-includes/version.php' ), 'a dot in a folder name is escaped, and the prefix matches that folder only' );

// WordPress in its own folder below the .htaccess (wp/), themes beside it.
$GLOBALS['ce_theme_root'] = '/srv/site/wp-content/themes';
$ce_split = ce_sec_files_htaccess_lines( '/srv/' );
ce_check( (bool) preg_grep( '#\^site/wp-includes/#', $ce_split ) && (bool) preg_grep( '#\^site/wp-content/themes/compelling-evidence/#', $ce_split ), 'core and the theme below the .htaccess folder get their prefix' );
ce_check( ce_forbidden( $ce_split, '/wp-config.php', '/' ) && ce_forbidden( $ce_split, '/site/wp-config.php', '/' ), 'wp-config.php is refused in the core folder and in the folder above it' );
ce_check( ce_forbidden( $ce_split, '/site/wp-includes/version.php', '/' ) && ! ce_forbidden( $ce_split, '/site/wp-includes/js/tinymce/wp-tinymce.php', '/' ), 'the prefixed rules refuse and allow as at the root' );

// The theme folder's own .htaccess, which Apache reads before WordPress runs.
$ce_own = array();
foreach ( file( dirname( __DIR__ ) . '/.htaccess', FILE_IGNORE_NEW_LINES ) as $ce_line ) {
	if ( preg_match( '/^RedirectMatch 403 (\S+)$/', $ce_line, $ce_m ) ) {
		$ce_own[] = $ce_m[1];
	}
}
$ce_own_hit = function ( $uri ) use ( $ce_own ) {
	foreach ( $ce_own as $p ) {
		if ( preg_match( '#' . $p . '#', $uri ) ) {
			return true;
		}
	}
	return false;
};
ce_check( 3 === count( $ce_own ), "the theme's .htaccess has its 3 RedirectMatch lines" );
foreach ( array( '.git/config', '.github/workflows/ci.yml', '.gitignore', '.claude/CLAUDE.md', 'tests/unlist-test.php', 'bin/build-error-pages.php', 'tools/x.py', 'functions.php', 'inc/ce-seo.php/x' ) as $ce_p ) {
	ce_check( $ce_own_hit( '/wp-content/themes/compelling-evidence/' . $ce_p ), "theme .htaccess: 403 for $ce_p" );
}
foreach ( array( 'style.css', 'screenshot.png', 'assets/js/main.js', 'assets/css/main.css', 'journeys/quiz.html', 'journeys/data/agnostic/screens/conclusion.html', 'doc/readme.md' ) as $ce_p ) {
	ce_check( ! $ce_own_hit( '/wp-content/themes/compelling-evidence/' . $ce_p ), "theme .htaccess: open $ce_p" );
}

echo $ce_fail ? "$ce_fail failed\n" : "all passed\n";
exit( $ce_fail ? 1 : 0 );
