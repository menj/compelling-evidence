<?php
/**
 * The error pages as plain HTML: the three wp-content drop-ins and the static
 * pages in errors/, from one builder.
 *
 * Pure PHP, no WordPress function: the drop-ins run while WordPress is half
 * loaded or has no database, and bin/build-error-pages.php runs this file on
 * the command line. The style is inline for the same reason, in the theme's
 * colours and fonts with its brandmark, light and dark, so no file of the
 * theme is needed to show a page.
 *
 * @package compelling-evidence
 */

defined( 'ABSPATH' ) || exit;

/**
 * Status, title and wording of each page.
 *
 * @return array<string,array> Kind => status, retry, eyebrow, title, lines, again.
 */
function ce_error_page_kinds() {
	return array(
		'maintenance' => array(
			'status' => 503,
			'retry' => 600,
			'eyebrow' => 'Maintenance',
			'title' => 'Back shortly',
			'lines' => array( 'The site is offline for a few minutes while it is updated.', 'Please try again shortly. Nothing you were reading has gone anywhere.' ),
			'again' => 'Try again',
		),
		'php-error' => array(
			'status' => 500,
			'retry' => 0,
			'eyebrow' => 'Error 500',
			'title' => 'Something went wrong',
			'lines' => array( 'A fault on our side stopped this page from loading. It is not something you did.', 'Please try again in a few minutes.' ),
			'again' => 'Try again',
		),
		'db-error' => array(
			'status' => 503,
			'retry' => 600,
			'eyebrow' => 'Database unavailable',
			'title' => 'We cannot reach our database',
			'lines' => array( 'The site cannot reach its database just now. This is temporary, and nothing you did caused it.', 'Please try again in a few minutes.' ),
			'again' => 'Try again',
		),
		'403' => array(
			'status' => 403,
			'retry' => 0,
			'eyebrow' => 'Error 403',
			'title' => 'Access denied',
			'lines' => array( 'This address is closed to visitors.', 'Every page of the site is open from the home page.' ),
			'again' => 'Go to the home page',
		),
		'500' => array(
			'status' => 500,
			'retry' => 0,
			'eyebrow' => 'Error 500',
			'title' => 'Something went wrong',
			'lines' => array( 'A fault on our side stopped this page from loading. It is not something you did.', 'Please try again in a few minutes.' ),
			'again' => 'Try again',
		),
		'502' => array(
			'status' => 502,
			'retry' => 0,
			'eyebrow' => 'Error 502',
			'title' => 'Bad gateway',
			'lines' => array( 'A server between you and the site gave a wrong answer.', 'Please try again in a few minutes.' ),
			'again' => 'Try again',
		),
		'504' => array(
			'status' => 504,
			'retry' => 0,
			'eyebrow' => 'Error 504',
			'title' => 'Gateway timeout',
			'lines' => array( 'A server between you and the site took too long to answer.', 'Please try again in a few minutes.' ),
			'again' => 'Try again',
		),
		'503' => array(
			'status' => 503,
			'retry' => 600,
			'eyebrow' => 'Error 503',
			'title' => 'Temporarily unavailable',
			'lines' => array( 'The site is busy or offline for a short while.', 'Please try again in a few minutes.' ),
			'again' => 'Try again',
		),
	);
}

/**
 * One page as a complete HTML document.
 *
 * @param string $kind A key of ce_error_page_kinds().
 * @param string $site Site name, plain text.
 * @param string $home Address of the home page.
 * @return string
 */
function ce_error_page_html( $kind, $site, $home = '/' ) {
	$kinds = ce_error_page_kinds();
	$k     = isset( $kinds[ $kind ] ) ? $kinds[ $kind ] : $kinds['500'];
	$e     = static function ( $s ) {
		return htmlspecialchars( (string) $s, ENT_QUOTES, 'UTF-8' );
	};
	$lines = '';
	foreach ( $k['lines'] as $line ) {
		$lines .= '<p>' . $e( $line ) . "</p>\n";
	}
	if ( ! empty( $k['alt'] ) ) {
		$lines .= '<p class="alt" lang="en">' . $e( $k['alt'] ) . "</p>\n";
	}
	$css = <<<'CSS'
:root{--ground:#1a0a2e;--card:#2a1050;--ink:#f5f0ff;--muted:#cfc5df;--coral:#e8455a;--cyan:#0cd4e0;--line:#3d1f6b;--on-cyan:#1a0a2e}
@media (prefers-color-scheme:light){:root{--ground:#f5f0ff;--card:#ffffff;--ink:#1a0a2e;--muted:#4b3d63;--coral:#b4203a;--cyan:#6b2fa0;--line:#e2d8f2;--on-cyan:#ffffff}}
*{box-sizing:border-box}
html,body{height:100%;margin:0}
body{display:flex;align-items:center;justify-content:center;padding:24px 16px;background:var(--ground) radial-gradient(circle at 15% 0,rgba(232,69,90,.18),transparent 55%);color:var(--ink);font:1.05rem/1.65 "DM Sans",system-ui,-apple-system,"Segoe UI",Roboto,sans-serif}
main{width:min(36rem,100%);padding:2.6rem clamp(1.25rem,6vw,2.4rem) 2.4rem;background:var(--card);border:1px solid var(--line);border-top:4px solid var(--coral);border-radius:20px;text-align:center;box-shadow:0 24px 60px -30px rgba(0,0,0,.6)}
.mark{display:inline-block;width:4.25rem;height:4.25rem}
.mark svg{display:block;width:100%;height:100%}
.name{margin:.8rem 0 0;font-size:.82rem;font-weight:600;letter-spacing:.24em;text-transform:uppercase;color:var(--muted)}
.code{margin:1.5rem 0 .2rem;font-size:.78rem;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:var(--cyan)}
h1{margin:.3rem 0 1rem;font:700 clamp(1.7rem,5vw,2.3rem)/1.15 "Playfair Display",Georgia,serif}
p{margin:0 auto .6rem;max-width:30rem;color:var(--muted)}
.again{display:inline-block;margin-top:1.4rem;padding:.75rem 1.8rem;border-radius:999px;background:var(--cyan);color:var(--on-cyan);font-weight:700;text-decoration:none}
.again:hover{filter:brightness(1.1)}
.again:focus-visible{outline:3px solid var(--coral);outline-offset:3px}
CSS;
	// The mark is the page's tab icon too, as a data address: an error page
	// answers at any path, in a subfolder install too, and asks for no file.
	$mark = <<<'MARK'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" focusable="false"><rect width="100" height="100" rx="22" fill="#180d2e"/><text x="50" y="68" font-family="'DM Sans', 'Helvetica Neue', Arial, sans-serif" font-weight="900" font-size="54" text-anchor="middle" letter-spacing="-3" fill="#f5f0ff"><tspan fill="#e8455a">C</tspan>E</text></svg>
MARK;
	return '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex">
<title>' . $e( $k['title'] ) . ' | ' . $e( $site ) . '</title>
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,' . $e( rawurlencode( $mark ) ) . '">
<style>
' . $css . '
</style>
</head>
<body>
<main role="main">
<div class="mark" aria-hidden="true">' . $mark . '</div>
<p class="name">' . $e( $site ) . '</p>
<p class="code">' . $e( $k['eyebrow'] ) . '</p>
<h1>' . $e( $k['title'] ) . '</h1>
' . $lines . '<a class="again" href="' . $e( $home ) . '">' . $e( $k['again'] ) . '</a>
</main>
</body>
</html>';
}

/**
 * The PHP header of a drop-in: the marker line, then the status headers.
 *
 * @param string $kind    Drop-in kind.
 * @param string $version Theme version.
 * @return string
 */
function ce_error_dropin_head( $kind, $version ) {
	$k      = ce_error_page_kinds()[ $kind ];
	$reason = array(
		500 => 'Internal Server Error',
		503 => 'Service Unavailable',
	);
	return "<?php\n// Compelling Evidence error page drop-in (" . $kind . ') ' . $version . ". Written by the CE theme; safe to delete.\n"
		. "// Self-contained: no theme file, no WordPress function and no database.\n"
		. "if ( ! headers_sent() ) {\n"
		. "\theader( 'HTTP/1.1 " . $k['status'] . ' ' . $reason[ $k['status'] ] . "', true, " . $k['status'] . " );\n"
		. "\theader( 'Content-Type: text/html; charset=utf-8' );\n"
		. ( $k['retry'] ? "\theader( 'Retry-After: " . (int) $k['retry'] . "' );\n" : '' )
		. "\theader( 'X-Robots-Tag: noindex' );\n"
		. "\theader( 'Cache-Control: no-store, max-age=0' );\n"
		. "}\n?>\n";
}
