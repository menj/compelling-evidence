<?php
/**
 * Security: remote-publishing lockdown, spam gate on new content, clean-up
 * tools and 410 Gone for removed spam. Settings are single options named
 * ce_sec_* (all on by default, stored as '1' or '0' like every other CE
 * option) and appear on the Security tab of Appearance > CE Theme Options.
 * Ported from the Abrahamic theme (2.87.0), built after the October 2026
 * attack on abrahamic-religions.com.
 *
 * Emergency switch: define( 'CE_SECURITY_LOCK', false ); in wp-config.php turns
 * the XML-RPC, application password and REST locks off without touching the options.
 *
 * @package CE_Theme
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/ce-security-detector.php';

define( 'CE_SEC_LOG', 'ce_security_log' );
define( 'CE_SEC_GONE', 'ce_security_gone' );
define( 'CE_SEC_LOG_MAX', 200 );
define( 'CE_SEC_GONE_MAX', 5000 );

/**
 * Whether a Security toggle is on.
 *
 * @param string $key Option key.
 * @return bool
 */
function ce_sec_on( $key ) {
	$locks = array( 'ce_sec_xmlrpc_off', 'ce_sec_app_passwords_off', 'ce_sec_rest_lock' );
	if ( in_array( $key, $locks, true ) && defined( 'CE_SECURITY_LOCK' ) && ! CE_SECURITY_LOCK ) {
		return false;
	}
	return '1' === (string) get_option( $key, '1' );
}

/**
 * Security tab address.
 *
 * @param array $args Extra query arguments.
 * @return string
 */
function ce_sec_url( $args = array() ) {
	return add_query_arg( array_merge( array( 'page' => 'ce-theme-options', 'tab' => 'security' ), $args ), admin_url( 'themes.php' ) );
}

/* -------------------------------------------------------------------------
 * 1. Remote-publishing lockdown
 * ---------------------------------------------------------------------- */

/**
 * XML-RPC off: no methods, no pingback header, xmlrpc.php answers 403.
 */
function ce_sec_xmlrpc() {
	if ( ! ce_sec_on( 'ce_sec_xmlrpc_off' ) ) {
		return;
	}
	add_filter( 'xmlrpc_enabled', '__return_false' );
	add_filter( 'xmlrpc_methods', '__return_empty_array', 999 );
	add_filter( 'pings_open', '__return_false', 999 );
	add_filter(
		'wp_headers',
		function ( $headers ) {
			unset( $headers['X-Pingback'], $headers['x-pingback'] );
			return $headers;
		}
	);
	remove_action( 'wp_head', 'rsd_link' );
	if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
		// Counted, not logged one by one: the October 2026 attack sent over
		// 17,000 requests in twelve days from 701 addresses.
		ce_sec_count( 'xmlrpc' );
		if ( ce_sec_fake_jetpack() ) {
			ce_sec_count( 'fake-jetpack' );
		}
		status_header( 403 );
		header( 'Content-Type: text/plain; charset=utf-8' );
		echo 'XML-RPC is disabled on this site.';
		exit;
	}
}
add_action( 'after_setup_theme', 'ce_sec_xmlrpc', 1 );

/**
 * Application passwords off.
 *
 * @param bool $available Core value.
 * @return bool
 */
function ce_sec_app_passwords( $available ) {
	return ce_sec_on( 'ce_sec_app_passwords_off' ) ? false : $available;
}
add_filter( 'wp_is_application_passwords_available', 'ce_sec_app_passwords', 99 );

/**
 * Whether this REST request comes from a logged-in browser session with a
 * valid wp_rest nonce. Core sets $wp_rest_auth_cookie when the logged-in cookie
 * validates, and rest_cookie_check_errors() has already logged the user out
 * if the cookie came without a valid nonce.
 *
 * @param WP_REST_Request $request Request.
 * @return bool
 */
function ce_sec_cookie_session( $request ) {
	if ( true !== ( isset( $GLOBALS['wp_rest_auth_cookie'] ) ? $GLOBALS['wp_rest_auth_cookie'] : null ) ) {
		return false;
	}
	if ( ! empty( $GLOBALS['wp_rest_application_password_uuid'] ) ) {
		return false;
	}
	$nonce = $request->get_header( 'X-WP-Nonce' );
	if ( ! $nonce && isset( $_REQUEST['_wpnonce'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- the nonce is checked here.
		$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	return $nonce && false !== wp_verify_nonce( $nonce, 'wp_rest' );
}

/**
 * REST lock: a request that writes (POST, PUT, PATCH, DELETE) as a logged-in
 * user must come from a cookie session with a valid nonce. Basic auth,
 * application passwords and any other header-based login are refused for
 * writes in every namespace. Reads and anonymous requests are unchanged, so
 * public GET output, contact forms and comment forms behave as before. The
 * block editor, Rank Math and the media modal send X-WP-Nonce with cookies
 * and pass.
 *
 * @param mixed           $result  Earlier result.
 * @param WP_REST_Server  $server  Server.
 * @param WP_REST_Request $request Request.
 * @return mixed
 */
function ce_sec_rest_guard( $result, $server, $request ) {
	if ( null !== $result || ! ce_sec_on( 'ce_sec_rest_lock' ) || ! ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return $result;
	}
	if ( in_array( strtoupper( $request->get_method() ), array( 'GET', 'HEAD', 'OPTIONS' ), true ) ) {
		return $result;
	}
	$route = (string) $request->get_route();
	/**
	 * Route prefixes that may write with non-cookie authentication, for
	 * example '/jetpack/'. Empty by default.
	 */
	foreach ( (array) apply_filters( 'ce_sec_rest_allow', array() ) as $prefix ) {
		if ( $prefix && 0 === strpos( $route, $prefix ) ) {
			return $result;
		}
	}
	$core = (bool) preg_match( '#^/(wp/v2|batch/v1)(/|$)#', $route ) && 0 !== strpos( $route, '/wp/v2/comments' );
	if ( ! is_user_logged_in() && ! $core ) {
		return $result;
	}
	if ( ! is_user_logged_in() ) {
		// Core refuses anonymous writes to these routes itself; nothing to add.
		return $result;
	}
	if ( ce_sec_cookie_session( $request ) ) {
		return $result;
	}
	ce_sec_log(
		array(
			'event' => 'rest-blocked',
			'title' => $request->get_method() . ' ' . $route,
		)
	);
	return new WP_Error( 'ce_rest_locked', __( 'Changes through the REST API need a logged-in browser session on this site.', 'compelling-evidence' ), array( 'status' => 403 ) );
}
add_filter( 'rest_pre_dispatch', 'ce_sec_rest_guard', 5, 3 );

/* -------------------------------------------------------------------------
 * 1b. Signals from the October 2026 attack
 *
 * The access logs showed the pattern: password guessing through xmlrpc.php
 * from 15 October, sent with forged "Jetpack by WordPress.com" user agents;
 * the first correct password on 20 October; from 22 October a post every 18
 * to 20 minutes, each one a login, a read of the site and a wp.newPost call,
 * every request from a different address. The login page itself was never
 * reached. These checks answer each step.
 * ---------------------------------------------------------------------- */

define( 'CE_SEC_COUNTS', 'ce_security_counts' );
define( 'CE_SEC_FAILED', 'ce_security_failed' );

/**
 * Add one to today's counter for an event. Thirty days are kept.
 *
 * @param string $key Event key.
 */
function ce_sec_count( $key ) {
	$counts = get_option( CE_SEC_COUNTS, array() );
	$counts = is_array( $counts ) ? $counts : array();
	$day    = gmdate( 'Y-m-d' );
	if ( ! isset( $counts[ $day ] ) ) {
		$counts[ $day ] = array();
		krsort( $counts );
		$counts = array_slice( $counts, 0, 30, true );
	}
	$counts[ $day ][ $key ] = ( isset( $counts[ $day ][ $key ] ) ? (int) $counts[ $day ][ $key ] : 0 ) + 1;
	update_option( CE_SEC_COUNTS, $counts, false );
}

/**
 * Whether the request claims to come from Jetpack or WordPress.com while the
 * Jetpack plugin is not active. Real Jetpack traffic only reaches sites that
 * run Jetpack, so on this site the claim is forged.
 *
 * @return bool
 */
function ce_sec_fake_jetpack() {
	$agent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) : '';
	if ( ! preg_match( '/jetpack|wordpress\.com/i', $agent ) ) {
		return false;
	}
	return ! class_exists( 'Jetpack' ) && ! defined( 'JETPACK__VERSION' );
}

/**
 * Whether this request is a remote, non-browser call: XML-RPC, or REST with
 * an application password or other header login.
 *
 * @return bool
 */
function ce_sec_is_remote() {
	if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
		return true;
	}
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST && is_user_logged_in() ) {
		return true !== ( isset( $GLOBALS['wp_rest_auth_cookie'] ) ? $GLOBALS['wp_rest_auth_cookie'] : null );
	}
	return false;
}

/**
 * Password logins through XML-RPC are refused even when XML-RPC is switched
 * back on (for Jetpack, say). Jetpack signs its own requests and needs no
 * password, so it keeps working.
 *
 * @param WP_User|WP_Error|null $user Result so far.
 * @return WP_User|WP_Error|null
 */
function ce_sec_refuse_remote_password( $user ) {
	if ( ! ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) || ! ce_sec_on( 'ce_sec_xmlrpc_off' ) && ! ce_sec_on( 'ce_sec_rest_lock' ) ) {
		return $user;
	}
	if ( $user instanceof WP_User ) {
		ce_sec_count( 'remote-password' );
		ce_sec_log(
			array(
				'event' => 'remote-password-refused',
				'title' => $user->user_login . ': ' . __( 'the correct password was sent through XML-RPC. Change it now.', 'compelling-evidence' ),
				'user'  => $user->ID,
			)
		);
	}
	return new WP_Error( 'ce_remote_password', __( 'Password logins through XML-RPC are not accepted on this site.', 'compelling-evidence' ) );
}
add_filter( 'authenticate', 'ce_sec_refuse_remote_password', 100 );

/**
 * Count failed logins per account name over a rolling day. The attack came
 * from a different address every time, so counting by address shows nothing;
 * counting by account name shows which account is under attack.
 *
 * @param string $login Account name or email tried.
 */
function ce_sec_failed_login( $login ) {
	ce_sec_count( 'failed-login' );
	$login  = sanitize_user( (string) $login, true );
	$failed = get_option( CE_SEC_FAILED, array() );
	$failed = is_array( $failed ) ? $failed : array();
	$now    = time();
	foreach ( $failed as $name => $row ) {
		if ( $now - (int) $row['last'] > DAY_IN_SECONDS ) {
			unset( $failed[ $name ] );
		}
	}
	if ( '' === $login ) {
		$login = '(empty)';
	}
	if ( ! isset( $failed[ $login ] ) && count( $failed ) >= 50 ) {
		return;
	}
	$row = isset( $failed[ $login ] ) ? $failed[ $login ] : array( 'n' => 0, 'first' => $now, 'last' => $now, 'ips' => array() );
	$row['n']++;
	$row['last'] = $now;
	$ip          = ce_sec_ip();
	if ( $ip && ! in_array( $ip, $row['ips'], true ) && count( $row['ips'] ) < 1000 ) {
		$row['ips'][] = $ip;
	}
	$failed[ $login ] = $row;
	update_option( CE_SEC_FAILED, $failed, false );
}
add_action( 'wp_login_failed', 'ce_sec_failed_login' );

/**
 * Admin notice when an existing account is being guessed at: ten or more
 * failures in a day, or a correct password refused on the remote route.
 */
function ce_sec_attack_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$failed = get_option( CE_SEC_FAILED, array() );
	$hit    = array();
	foreach ( (array) $failed as $name => $row ) {
		if ( (int) $row['n'] >= 10 && ( username_exists( $name ) || email_exists( $name ) ) ) {
			$hit[] = sprintf( '%s (%d, %d)', $name, (int) $row['n'], count( (array) $row['ips'] ) );
		}
	}
	$counts = get_option( CE_SEC_COUNTS, array() );
	$today  = isset( $counts[ gmdate( 'Y-m-d' ) ]['remote-password'] ) ? (int) $counts[ gmdate( 'Y-m-d' ) ]['remote-password'] : 0;
	if ( ! $hit && ! $today ) {
		return;
	}
	$url = ce_sec_url();
	echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'Security:', 'compelling-evidence' ) . '</strong> ';
	if ( $today ) {
		esc_html_e( 'Someone sent a correct password through XML-RPC today. The login was refused, but the password is known: change it now.', 'compelling-evidence' );
		echo ' ';
	}
	if ( $hit ) {
		/* translators: %s: account names with failure and address counts. */
		echo esc_html( sprintf( __( 'Passwords are being guessed for these accounts in the last day (failures, addresses): %s.', 'compelling-evidence' ), implode( ', ', $hit ) ) ) . ' ';
	}
	echo '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Open the Security tab', 'compelling-evidence' ) . '</a></p></div>';
}
add_action( 'admin_notices', 'ce_sec_attack_notice' );

/* -------------------------------------------------------------------------
 * 2. Spam gate
 * ---------------------------------------------------------------------- */

/**
 * How this request reached WordPress.
 *
 * @return string
 */
function ce_sec_request_type() {
	if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) {
		return 'XML-RPC';
	}
	if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
		return ! empty( $GLOBALS['wp_rest_application_password_uuid'] ) ? 'REST (application password)' : 'REST';
	}
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return 'WP-CLI';
	}
	if ( wp_doing_cron() ) {
		return 'cron';
	}
	if ( wp_doing_ajax() ) {
		return 'admin AJAX';
	}
	return is_admin() ? 'admin' : 'front end';
}

/**
 * Visitor IP address, as the web server saw it.
 *
 * @return string
 */
function ce_sec_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
}

/**
 * Add a line to the capped log.
 *
 * @param array $entry Fields: event, title, post_id, reasons.
 */
function ce_sec_log( $entry ) {
	$log   = get_option( CE_SEC_LOG, array() );
	$log   = is_array( $log ) ? $log : array();
	$log[] = array_merge(
		array(
			'time'    => time(),
			'event'   => '',
			'title'   => '',
			'post_id' => 0,
			'reasons' => array(),
			'user'    => get_current_user_id(),
			'type'    => ce_sec_request_type(),
			'ip'      => ce_sec_ip(),
		),
		$entry
	);
	update_option( CE_SEC_LOG, array_slice( $log, -CE_SEC_LOG_MAX ), false );
}

/**
 * What this theme seeds itself, from its own data files: every article slug
 * and title in inc/articles/batch-*.json (synced to ce_article by
 * inc/ce-content-sync.php), the topic names those articles carry, and the
 * pages the theme creates on activation (ce-secondary-pages.php and
 * ce_create_journey_pages()). Cached per theme version.
 *
 * @return array { slugs: array type => array slug => true, titles: array, terms: string[] }
 */
function ce_sec_seed_index() {
	static $index = null;
	if ( null !== $index ) {
		return $index;
	}
	$version = (string) wp_get_theme( get_stylesheet() )->get( 'Version' );
	$cached  = get_transient( 'ce_sec_seed_index' );
	if ( is_array( $cached ) && isset( $cached['version'] ) && $cached['version'] === $version ) {
		$index = $cached;
		return $index;
	}
	$index = array(
		'version' => $version,
		'slugs'   => array( 'article' => array(), 'page' => array() ),
		'titles'  => array(),
		'terms'   => array(),
	);
	$dir      = get_stylesheet_directory() . '/inc/articles/';
	$manifest = is_readable( $dir . 'manifest.json' ) ? json_decode( (string) file_get_contents( $dir . 'manifest.json' ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
	$files    = array();
	if ( is_array( $manifest ) && ! empty( $manifest['batches'] ) ) {
		foreach ( $manifest['batches'] as $batch ) {
			if ( ! empty( $batch['file'] ) ) {
				$files[] = basename( (string) $batch['file'] );
			}
		}
	}
	foreach ( $files as $file ) {
		$data = is_readable( $dir . $file ) ? json_decode( (string) file_get_contents( $dir . $file ), true ) : null; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme file.
		if ( empty( $data['articles'] ) || ! is_array( $data['articles'] ) ) {
			continue;
		}
		foreach ( $data['articles'] as $article ) {
			if ( empty( $article['slug'] ) ) {
				continue;
			}
			$slug                               = sanitize_title( $article['slug'] );
			$index['slugs']['article'][ $slug ] = true;
			if ( ! empty( $article['title'] ) ) {
				$index['titles'][ (string) $article['title'] ] = true;
			}
			if ( ! empty( $article['topic'] ) ) {
				$index['terms'][] = sanitize_title( $article['topic'] );
			}
		}
	}
	// The slug rename map keeps the old slugs of renamed articles seeded too.
	if ( function_exists( 'ce_get_slug_rename_map' ) ) {
		foreach ( (array) ce_get_slug_rename_map() as $old => $new ) {
			$index['slugs']['article'][ sanitize_title( $old ) ] = true;
			$index['slugs']['article'][ sanitize_title( $new ) ] = true;
		}
	}
	$pages = array( 'about-compelling-evidence', 'editorial-policy', 'privacy-policy', 'contact-compelling-evidence', 'ask-a-question', 'faq', 'glossary', 'quiz', 'my-progress', 'journeys', 'journey', 'rights-freedom' );
	if ( function_exists( 'ce_get_journey_paths' ) ) {
		$pages = array_merge( $pages, array_keys( ce_get_journey_paths() ) );
	}
	foreach ( $pages as $slug ) {
		$index['slugs']['page'][ $slug ] = true;
	}
	$index['terms'] = array_values( array_unique( array_merge( $index['terms'], array( 'does-god-exist', 'the-problem-of-evil', 'ethics-without-god', 'science-evidence', 'examining-the-quran', 'examining-the-sources', 'history-context', 'rights-freedom', 'the-inner-journey', 'the-bigger-picture' ) ) ) );
	set_transient( 'ce_sec_seed_index', $index, WEEK_IN_SECONDS );
	return $index;
}

/**
 * Whether a post is content this theme seeds: an article whose slug is in the
 * article batches, a page the theme creates (journey pages also carry
 * _ce_journey_key), or one of the rights-freedom-N copies of earlier versions.
 * Slugs are unique per type, so spam cannot borrow a seeded slug: WordPress
 * gives a new post with a taken slug a -2 suffix.
 *
 * @param int    $post_id Post ID, 0 for a new post.
 * @param string $title   Title.
 * @param string $slug    Slug.
 * @param string $type    Post type.
 * @return bool
 */
function ce_sec_is_seeded( $post_id, $title = '', $slug = '', $type = '' ) {
	if ( $post_id && '' !== (string) get_post_meta( $post_id, '_ce_journey_key', true ) ) {
		return true;
	}
	if ( '' === $type && $post_id ) {
		$type = (string) get_post_type( $post_id );
	}
	$slug  = strtolower( rawurldecode( (string) $slug ) );
	$index = ce_sec_seed_index();
	if ( in_array( $type, array( 'ce_article', 'post' ), true ) ) {
		if ( isset( $index['slugs']['article'][ $slug ] ) ) {
			return true;
		}
		// A new article from the sync before WordPress has set its slug.
		return '' === $slug && '' !== $title && isset( $index['titles'][ $title ] );
	}
	if ( 'page' === $type ) {
		return isset( $index['slugs']['page'][ $slug ] ) || (bool) preg_match( '/^rights-freedom-\d+$/', $slug );
	}
	return false;
}

/**
 * Post types the gate and the scan look at: posts, pages, the theme's own
 * articles (public, in REST, so spam could publish one) and questions (public
 * once published). Questions arrive from the Ask a Question form as Pending,
 * and the gate leaves that path exactly as it is: it acts on a question only
 * when someone makes it public. Topics, statuses, menus, attachments,
 * templates and blocks are never touched.
 *
 * @return string[]
 */
function ce_sec_types() {
	return array( 'post', 'page', 'ce_article', 'ce_question' );
}

/**
 * Hold suspected spam: any post going public whose title, slug or content
 * scores as gambling spam is saved as Pending instead.
 *
 * @param array $data    Sanitised post data.
 * @param array $postarr Raw post data.
 * @return array
 */
function ce_sec_gate( $data, $postarr ) {
	if ( ! ce_sec_on( 'ce_sec_spam_gate' ) || ! in_array( $data['post_type'], ce_sec_types(), true ) ) {
		return $data;
	}
	$public = in_array( $data['post_status'], array( 'publish', 'future', 'private' ), true );
	$id     = isset( $postarr['ID'] ) ? (int) $postarr['ID'] : 0;
	$title  = wp_unslash( $data['post_title'] );
	$slug   = $data['post_name'];
	if ( 'ce_question' === $data['post_type'] && ! $public ) {
		// The Ask a Question form saves Pending; that path stays unchanged.
		return $data;
	}
	if ( ce_sec_is_seeded( $id, $title, $slug, $data['post_type'] ) ) {
		return $data;
	}
	$verdict = ce_spam_score( $title, $slug, wp_unslash( $data['post_content'] ) );
	if ( ! $verdict['spam'] && $public && ce_sec_is_remote() ) {
		// The spam arrived through wp.newPost: anything published from
		// outside the editor waits for review, whatever it says.
		$verdict = array( 'spam' => true, 'reasons' => array( __( 'published from outside the editor', 'compelling-evidence' ) . ' (' . ce_sec_request_type() . ')' ) );
	}
	if ( ! $verdict['spam'] ) {
		return $data;
	}
	if ( $public ) {
		$data['post_status'] = 'pending';
	}
	$GLOBALS['ce_sec_held'] = array(
		'event'   => $public ? 'held' : 'flagged-draft',
		'title'   => $title,
		'reasons' => $verdict['reasons'],
	);
	return $data;
}
add_filter( 'wp_insert_post_data', 'ce_sec_gate', 99, 2 );

/**
 * Log what the gate held, now that the post has an ID.
 *
 * @param int $post_id Post ID.
 */
function ce_sec_gate_log( $post_id ) {
	if ( empty( $GLOBALS['ce_sec_held'] ) ) {
		return;
	}
	$entry                   = $GLOBALS['ce_sec_held'];
	$GLOBALS['ce_sec_held'] = null;
	$entry['post_id']        = (int) $post_id;
	update_post_meta( $post_id, '_ce_spam_held', time() );
	ce_sec_log( $entry );
}
add_action( 'wp_insert_post', 'ce_sec_gate_log', 1 );

/**
 * Keep held posts away from search engines while they wait: Rank Math and the
 * core sitemap only list published items, and a held post is never published,
 * so Instant Indexing has nothing to submit.
 */

/* -------------------------------------------------------------------------
 * 3. 410 Gone for removed spam
 * ---------------------------------------------------------------------- */

/**
 * Path of a URL, without slashes at either end.
 *
 * @param string $url URL.
 * @return string
 */
function ce_sec_path( $url ) {
	$path = (string) wp_parse_url( $url, PHP_URL_PATH );
	return trim( strtolower( rawurldecode( $path ) ), '/' );
}

/**
 * Remember the address of a post moved to the trash as spam.
 *
 * @param WP_Post $post Post.
 */
function ce_sec_remember_gone( $post ) {
	$gone = get_option( CE_SEC_GONE, array() );
	$gone = is_array( $gone ) ? $gone : array();
	foreach ( array( get_permalink( $post ), home_url( '/?p=' . $post->ID ) ) as $url ) {
		$path = ce_sec_path( $url );
		if ( '' !== $path ) {
			$gone[ $path ] = (int) $post->ID;
		}
	}
	if ( $post->post_name ) {
		$gone[ strtolower( rawurldecode( $post->post_name ) ) ] = (int) $post->ID;
	}
	update_option( CE_SEC_GONE, array_slice( $gone, -CE_SEC_GONE_MAX, null, true ), false );
}

/**
 * Answer 410 Gone, in place of 404, on the address of removed spam. A post
 * restored from the trash and published again is served as normal, since the
 * request is then no longer a 404.
 */
function ce_sec_send_gone() {
	if ( ! is_404() || ! ce_sec_on( 'ce_sec_gone' ) ) {
		return;
	}
	$gone = get_option( CE_SEC_GONE, array() );
	if ( ! $gone || ! is_array( $gone ) ) {
		return;
	}
	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
	$path = ce_sec_path( $uri );
	$last = basename( $path );
	if ( isset( $gone[ $path ] ) || ( '' !== $last && isset( $gone[ $last ] ) && false === strpos( $path, '/' ) ) ) {
		status_header( 410 );
		nocache_headers();
		header( 'X-Robots-Tag: noindex', true );
	}
}
add_action( 'template_redirect', 'ce_sec_send_gone', 0 );

/* -------------------------------------------------------------------------
 * 4. Login record for the report
 * ---------------------------------------------------------------------- */

/**
 * Last login time and IP, kept from this version on.
 *
 * @param string  $login Login name.
 * @param WP_User $user  User.
 */
function ce_sec_record_login( $login, $user ) {
	update_user_meta( $user->ID, '_ce_last_login', array( 'time' => time(), 'ip' => ce_sec_ip() ) );
}
add_action( 'wp_login', 'ce_sec_record_login', 10, 2 );

/* -------------------------------------------------------------------------
 * 5. Admin actions
 * ---------------------------------------------------------------------- */

/**
 * Stop unless the user may manage options and the nonce is valid.
 *
 * @param string $action Nonce action.
 */
function ce_sec_check( $action ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to do that.', 'compelling-evidence' ), 403 );
	}
	check_admin_referer( $action );
}

/**
 * Back to the Security tab with a notice.
 *
 * @param string $notice Notice code.
 * @param int    $count  Count.
 */
function ce_sec_back( $notice, $count = 0 ) {
	wp_safe_redirect( ce_sec_url( array( 'ce-sec' => $notice, 'ce-count' => (int) $count ) ) );
	exit;
}

/**
 * Revoke every application password of every user.
 */
function ce_sec_handle_revoke() {
	ce_sec_check( 'ce_sec_revoke' );
	$count = 0;
	if ( class_exists( 'WP_Application_Passwords' ) ) {
		foreach ( get_users( array( 'fields' => 'ID' ) ) as $user_id ) {
			$deleted = WP_Application_Passwords::delete_all_application_passwords( (int) $user_id );
			if ( ! is_wp_error( $deleted ) ) {
				$count += (int) $deleted;
			}
		}
	}
	ce_sec_log( array( 'event' => 'app-passwords-revoked', 'title' => (string) $count ) );
	ce_sec_back( 'revoked', $count );
}
add_action( 'admin_post_ce_sec_revoke', 'ce_sec_handle_revoke' );

/**
 * End every login session of every user, this one included.
 */
function ce_sec_handle_logout() {
	ce_sec_check( 'ce_sec_logout' );
	ce_sec_log( array( 'event' => 'sessions-ended', 'title' => '' ) );
	foreach ( get_users( array( 'fields' => 'ID' ) ) as $user_id ) {
		WP_Session_Tokens::get_instance( (int) $user_id )->destroy_all();
	}
	wp_clear_auth_cookie();
	wp_safe_redirect( wp_login_url( ce_sec_url() ) );
	exit;
}
add_action( 'admin_post_ce_sec_logout', 'ce_sec_handle_logout' );

/**
 * Term slugs the theme seeds: article topics and the 2.2.0 migration terms.
 *
 * @return string[]
 */
function ce_sec_seeded_terms() {
	$index = ce_sec_seed_index();
	return array_merge( $index['terms'], array( 'uncategorized' ) );
}

/**
 * Categories spam created: a spam-scoring name or one of the names seen in the
 * incident, never a seeded or default category, and only when nothing but
 * trashed posts uses it.
 *
 * @return WP_Term[]
 */
function ce_sec_spam_categories() {
	$known   = array( 'casino', 'casinos', 'review', 'reviews', 'betting', 'gambling', 'bonus', 'slots', 'sportsbook', 'kasyno', 'kazino' );
	$seeded  = ce_sec_seeded_terms();
	$default = (int) get_option( 'default_category' );
	$out     = array();
	foreach ( get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false ) ) as $term ) {
		if ( (int) $term->term_id === $default || in_array( $term->slug, $seeded, true ) || 'uncategorized' === $term->slug ) {
			continue;
		}
		$verdict = ce_spam_score( $term->name, $term->slug, '' );
		if ( ! in_array( $term->slug, $known, true ) && ! $verdict['spam'] ) {
			continue;
		}
		$used = get_posts(
			array(
				'post_type'      => 'any',
				'post_status'    => array( 'publish', 'pending', 'draft', 'future', 'private' ),
				'category'       => $term->term_id,
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);
		$term->ce_in_use = ! empty( $used );
		$out[]            = $term;
	}
	return $out;
}

/**
 * Delete spam categories that are empty.
 */
function ce_sec_handle_delete_categories() {
	ce_sec_check( 'ce_sec_categories' );
	$count = 0;
	foreach ( ce_sec_spam_categories() as $term ) {
		if ( ! $term->ce_in_use && ! is_wp_error( wp_delete_term( $term->term_id, 'category' ) ) ) {
			++$count;
		}
	}
	ce_sec_log( array( 'event' => 'categories-deleted', 'title' => (string) $count ) );
	ce_sec_back( 'categories', $count );
}
add_action( 'admin_post_ce_sec_categories', 'ce_sec_handle_delete_categories' );

/**
 * Clear the gate log.
 */
function ce_sec_handle_clear_log() {
	ce_sec_check( 'ce_sec_clear_log' );
	delete_option( CE_SEC_LOG );
	ce_sec_back( 'log-cleared' );
}
add_action( 'admin_post_ce_sec_clear_log', 'ce_sec_handle_clear_log' );

/**
 * AJAX: one batch of the scan.
 */
function ce_sec_ajax_scan() {
	check_ajax_referer( 'ce_sec_ajax', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to do that.', 'compelling-evidence' ) ), 403 );
	}
	$offset = isset( $_POST['offset'] ) ? absint( $_POST['offset'] ) : 0;
	$batch  = 100;
	$query  = new WP_Query(
		array(
			'post_type'              => ce_sec_types(),
			'post_status'            => array( 'publish', 'pending', 'draft', 'future', 'private' ),
			'posts_per_page'         => $batch,
			'offset'                 => $offset,
			'orderby'                => 'ID',
			'order'                  => 'DESC',
			'no_found_rows'          => false,
			'update_post_term_cache' => false,
			'suppress_filters'       => true,
		)
	);
	$rows = array();
	foreach ( $query->posts as $post ) {
		if ( ce_sec_is_seeded( $post->ID, $post->post_title, $post->post_name, $post->post_type ) ) {
			continue;
		}
		$verdict = ce_spam_score( $post->post_title, $post->post_name, $post->post_content );
		if ( ! $verdict['spam'] ) {
			continue;
		}
		$author = get_userdata( (int) $post->post_author );
		$rows[] = array(
			'id'      => (int) $post->ID,
			'title'   => '' !== $post->post_title ? wp_strip_all_tags( $post->post_title ) : __( '(no title)', 'compelling-evidence' ),
			'date'    => get_the_date( 'Y-m-d H:i', $post ),
			'status'  => $post->post_status,
			'author'  => $author ? $author->user_login : '',
			'reasons' => $verdict['reasons'],
			'edit'    => (string) get_edit_post_link( $post->ID, 'raw' ),
			'view'    => 'publish' === $post->post_status ? (string) get_permalink( $post ) : '',
		);
	}
	$next = $offset + count( $query->posts );
	wp_send_json_success(
		array(
			'rows'  => $rows,
			'next'  => $next,
			'total' => (int) $query->found_posts,
			'done'  => count( $query->posts ) < $batch,
		)
	);
}
add_action( 'wp_ajax_ce_sec_scan', 'ce_sec_ajax_scan' );

/**
 * AJAX: move a batch of posts to the trash. Each one is scored again here, so
 * only posts that still read as spam and are not seeded content go.
 */
function ce_sec_ajax_trash() {
	check_ajax_referer( 'ce_sec_ajax', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to do that.', 'compelling-evidence' ) ), 403 );
	}
	$ids     = isset( $_POST['ids'] ) ? array_slice( array_map( 'absint', (array) wp_unslash( $_POST['ids'] ) ), 0, 100 ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- absint on each.
	$trashed = array();
	$skipped = array();
	foreach ( array_filter( $ids ) as $id ) {
		$post = get_post( $id );
		if ( ! $post || ! in_array( $post->post_type, ce_sec_types(), true ) || 'trash' === $post->post_status || ! current_user_can( 'delete_post', $id ) || ce_sec_is_seeded( $id, $post->post_title, $post->post_name, $post->post_type ) ) {
			$skipped[] = $id;
			continue;
		}
		$verdict = ce_spam_score( $post->post_title, $post->post_name, $post->post_content );
		if ( ! $verdict['spam'] ) {
			$skipped[] = $id;
			continue;
		}
		if ( 'publish' === $post->post_status ) {
			ce_sec_remember_gone( $post );
		}
		if ( wp_trash_post( $id ) ) {
			$trashed[] = $id;
		} else {
			$skipped[] = $id;
		}
	}
	if ( $trashed ) {
		ce_sec_log( array( 'event' => 'trashed', 'title' => (string) count( $trashed ) ) );
	}
	wp_send_json_success( array( 'trashed' => $trashed, 'skipped' => $skipped ) );
}
add_action( 'wp_ajax_ce_sec_trash', 'ce_sec_ajax_trash' );

/* -------------------------------------------------------------------------
 * 6. Screen
 * ---------------------------------------------------------------------- */

/**
 * Assets for the Security tab.
 *
 * @param string $hook Screen hook.
 */
function ce_sec_assets( $hook ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- tab selection only.
	$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';
	if ( 'appearance_page_ce-theme-options' !== $hook || 'security' !== $tab || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$uri     = get_stylesheet_directory_uri();
	$version = wp_get_theme()->get( 'Version' );
	wp_enqueue_style( 'ce-security', $uri . '/assets/css/ce-security.css', array( 'ce-theme-options-admin' ), $version );
	wp_enqueue_script( 'ce-security', $uri . '/assets/js/ce-security.js', array(), $version, true );
	wp_localize_script(
		'ce-security',
		'ceSecurity',
		array(
			'ajax'    => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'ce_sec_ajax' ),
			'i18n'    => array(
				'scanning'     => __( 'Scanning: %1$d of %2$d checked, %3$d found.', 'compelling-evidence' ),
				'done'         => __( 'Scan finished: %1$d checked, %2$d found.', 'compelling-evidence' ),
				'none'         => __( 'No suspected spam found.', 'compelling-evidence' ),
				'trashing'     => __( 'Moving to the trash: %1$d of %2$d.', 'compelling-evidence' ),
				'trashed'      => __( '%1$d moved to the trash, %2$d skipped. The Trash of each content type can restore them.', 'compelling-evidence' ),
				'confirmTrash' => __( 'Move the selected items to the trash? They can be restored from the Trash.', 'compelling-evidence' ),
				'error'        => __( 'The request failed. Reload the page and try again.', 'compelling-evidence' ),
				'edit'         => __( 'Edit', 'compelling-evidence' ),
				'view'         => __( 'View', 'compelling-evidence' ),
				'select'       => __( 'Select', 'compelling-evidence' ),
			),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'ce_sec_assets', 20 );

/**
 * Notices after Security actions.
 */
function ce_sec_notice() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
	$code = isset( $_GET['ce-sec'] ) ? sanitize_key( wp_unslash( $_GET['ce-sec'] ) ) : '';
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
	$n    = isset( $_GET['ce-count'] ) ? absint( $_GET['ce-count'] ) : 0;
	$map  = array(
		/* translators: %d: number of application passwords. */
		'revoked'     => sprintf( __( 'Application passwords revoked: %d.', 'compelling-evidence' ), $n ),
		/* translators: %d: number of categories. */
		'categories'  => sprintf( __( 'Empty spam categories deleted: %d.', 'compelling-evidence' ), $n ),
		'log-cleared' => __( 'Security log cleared.', 'compelling-evidence' ),
	);
	if ( isset( $map[ $code ] ) ) {
		printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( $map[ $code ] ) );
	}
}

/**
 * Small action form posting to admin-post.php.
 *
 * @param string $action  Action and nonce name.
 * @param string $label   Button label.
 * @param string $confirm Confirmation text.
 * @param string $class   Button class.
 */
function ce_sec_button( $action, $label, $confirm, $class = 'secondary' ) {
	?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="ce-sec-form" data-ce-confirm="<?php echo esc_attr( $confirm ); ?>">
		<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">
		<?php wp_nonce_field( $action ); ?>
		<?php submit_button( $label, $class, 'submit', false ); ?>
	</form>
	<?php
}

/**
 * Administrators with last login and application passwords.
 *
 * @return array
 */
function ce_sec_admin_rows() {
	$rows = array();
	foreach ( get_users( array( 'role' => 'administrator' ) ) as $user ) {
		$last     = get_user_meta( $user->ID, '_ce_last_login', true );
		$sessions = get_user_meta( $user->ID, 'session_tokens', true );
		$time     = is_array( $last ) && ! empty( $last['time'] ) ? (int) $last['time'] : 0;
		$ip       = is_array( $last ) && ! empty( $last['ip'] ) ? $last['ip'] : '';
		if ( is_array( $sessions ) ) {
			foreach ( $sessions as $session ) {
				if ( ! empty( $session['login'] ) && (int) $session['login'] > $time ) {
					$time = (int) $session['login'];
					$ip   = isset( $session['ip'] ) ? $session['ip'] : $ip;
				}
			}
		}
		$apps = class_exists( 'WP_Application_Passwords' ) ? WP_Application_Passwords::get_user_application_passwords( $user->ID ) : array();
		$used = 0;
		foreach ( $apps as $app ) {
			$used = max( $used, (int) $app['last_used'] );
		}
		$rows[] = array(
			'user'     => $user,
			'login'    => $time,
			'ip'       => $ip,
			'sessions' => is_array( $sessions ) ? count( $sessions ) : 0,
			'apps'     => $apps,
			'app_used' => $used,
		);
	}
	return $rows;
}

/**
 * Posts per day and author over the last 30 days, and hours with bursts.
 *
 * @return array { days: array date => array login => count, bursts: array }
 */
function ce_sec_activity() {
	global $wpdb;
	$since = gmdate( 'Y-m-d H:i:s', time() - 30 * DAY_IN_SECONDS );
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery -- report query, read only.
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT post_author, DATE(post_date) AS d, HOUR(post_date) AS h, COUNT(*) AS n FROM {$wpdb->posts} WHERE post_type IN ('post','ce_article') AND post_status NOT IN ('auto-draft','inherit') AND post_date_gmt >= %s GROUP BY post_author, d, h",
			$since
		)
	);
	$days   = array();
	$bursts = array();
	foreach ( (array) $rows as $row ) {
		$user  = get_userdata( (int) $row->post_author );
		$login = $user ? $user->user_login : '#' . (int) $row->post_author;
		$days[ $row->d ][ $login ] = ( isset( $days[ $row->d ][ $login ] ) ? $days[ $row->d ][ $login ] : 0 ) + (int) $row->n;
		if ( (int) $row->n > 2 ) {
			$bursts[] = array( 'login' => $login, 'when' => sprintf( '%s %02d:00', $row->d, (int) $row->h ), 'n' => (int) $row->n );
		}
	}
	krsort( $days );
	return array( 'days' => $days, 'bursts' => $bursts );
}

/**
 * Seeded articles and pages whose content now carries gambling links.
 *
 * @return WP_Post[]
 */
function ce_sec_tainted_seeds() {
	$out   = array();
	$posts = get_posts(
		array(
			'post_type'      => array( 'ce_article', 'post', 'page' ),
			'post_status'    => array( 'publish', 'pending', 'draft', 'future', 'private' ),
			'posts_per_page' => 1000,
			'no_found_rows'  => true,
		)
	);
	foreach ( $posts as $post ) {
		if ( ! ce_sec_is_seeded( $post->ID, $post->post_title, $post->post_name, $post->post_type ) ) {
			continue;
		}
		$hosts = ce_spam_links( $post->post_content );
		if ( $hosts ) {
			$post->ce_hosts = $hosts;
			$out[]          = $post;
		}
	}
	return $out;
}

/**
 * Whether Rank Math Instant Indexing is on.
 *
 * @return bool
 */
function ce_sec_instant_indexing() {
	return class_exists( '\RankMath\Helper' ) && method_exists( '\RankMath\Helper', 'is_module_active' ) && \RankMath\Helper::is_module_active( 'instant-indexing' );
}

/**
 * Tools and reports, shown under the form while the Security tab is open.
 */
function ce_sec_render_tools() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
	$admins = ce_sec_admin_rows();
	$total  = 0;
	foreach ( $admins as $a ) {
		$total += count( $a['apps'] );
	}
	?>
	<section class="ce-sec" id="ce-security-tools" aria-label="<?php esc_attr_e( 'Security tools and reports', 'compelling-evidence' ); ?>">
		<?php ce_sec_notice(); ?>
		<?php if ( defined( 'CE_SECURITY_LOCK' ) && ! CE_SECURITY_LOCK ) : ?>
			<div class="notice notice-warning inline"><p><?php esc_html_e( 'CE_SECURITY_LOCK is false in wp-config.php, so the three remote-publishing switches above are off whatever they show.', 'compelling-evidence' ); ?></p></div>
		<?php endif; ?>

		<h3 class="ce-section-title"><?php esc_html_e( 'Clean up spam', 'compelling-evidence' ); ?></h3>
		<p class="ce-field-desc"><?php esc_html_e( 'The scan reads every post, page, article and question that is published, pending, scheduled, private or a draft, 100 at a time, and lists those that read as gambling spam. Content the theme seeds (its articles and pages) is never listed. Nothing changes until you move posts to the trash, and the trash can restore them.', 'compelling-evidence' ); ?></p>
		<div class="ce-sec-bar">
			<button type="button" class="button button-primary" id="ce-sec-scan"><?php esc_html_e( 'Scan for spam', 'compelling-evidence' ); ?></button>
			<button type="button" class="button" id="ce-sec-trash" disabled><?php esc_html_e( 'Move selected to Trash', 'compelling-evidence' ); ?></button>
			<span class="ce-sec-status" id="ce-sec-status" role="status" aria-live="polite"></span>
		</div>
		<div class="ce-sec-progress" hidden><span id="ce-sec-progress"></span></div>
		<div class="ce-sec-scroll">
			<table class="widefat striped ce-sec-table" id="ce-sec-results" hidden>
				<thead>
					<tr>
						<td class="check-column"><input type="checkbox" id="ce-sec-all" aria-label="<?php esc_attr_e( 'Select all', 'compelling-evidence' ); ?>"></td>
						<th scope="col"><?php esc_html_e( 'Title', 'compelling-evidence' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Date', 'compelling-evidence' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Status', 'compelling-evidence' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Author', 'compelling-evidence' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Reasons', 'compelling-evidence' ); ?></th>
					</tr>
				</thead>
				<tbody></tbody>
			</table>
		</div>
		<p class="ce-field-desc">
			<?php
			esc_html_e( 'A trashed post answers 404, or 410 Gone when that switch is on. Resubmit the sitemap in Google Search Console afterwards.', 'compelling-evidence' );
			echo ' ';
			echo ce_sec_instant_indexing()
				? esc_html__( 'Rank Math Instant Indexing is on. Held spam is never published, so it is never submitted.', 'compelling-evidence' )
				: esc_html__( 'Rank Math Instant Indexing is off.', 'compelling-evidence' );
			?>
		</p>

		<h3 class="ce-section-title"><?php esc_html_e( 'Spam categories', 'compelling-evidence' ); ?></h3>
		<?php $cats = ce_sec_spam_categories(); ?>
		<?php if ( $cats ) : ?>
			<ul class="ce-sec-list">
				<?php foreach ( $cats as $term ) : ?>
					<li>
						<?php
						echo esc_html( $term->name . ' (' . $term->slug . ')' );
						echo ' ';
						echo $term->ce_in_use ? esc_html__( 'still used by posts outside the trash, kept', 'compelling-evidence' ) : esc_html__( 'empty, can be deleted', 'compelling-evidence' );
						?>
					</li>
				<?php endforeach; ?>
			</ul>
			<?php ce_sec_button( 'ce_sec_categories', __( 'Delete empty spam categories', 'compelling-evidence' ), __( 'Delete the empty spam categories listed? Categories the theme seeds are never deleted.', 'compelling-evidence' ) ); ?>
		<?php else : ?>
			<p class="ce-field-desc"><?php esc_html_e( 'No spam categories found.', 'compelling-evidence' ); ?></p>
		<?php endif; ?>

		<h3 class="ce-section-title"><?php esc_html_e( 'Passwords and sessions', 'compelling-evidence' ); ?></h3>
		<p class="ce-field-desc">
			<?php
			/* translators: %d: number of application passwords. */
			echo esc_html( sprintf( __( 'Application passwords stored on administrator accounts: %d.', 'compelling-evidence' ), $total ) );
			?>
		</p>
		<div class="ce-sec-bar">
			<?php
			ce_sec_button( 'ce_sec_revoke', __( 'Revoke all application passwords', 'compelling-evidence' ), __( 'Revoke every application password of every user? Tools that use them stop working.', 'compelling-evidence' ), 'delete' );
			ce_sec_button( 'ce_sec_logout', __( 'Log out all sessions', 'compelling-evidence' ), __( 'End every login session of every user, yours included? You will need to log in again.', 'compelling-evidence' ), 'delete' );
			?>
		</div>

		<h4 class="ce-sec-sub"><?php esc_html_e( 'Administrator accounts', 'compelling-evidence' ); ?></h4>
		<div class="ce-sec-scroll">
			<table class="widefat striped ce-sec-table">
				<thead><tr><th scope="col"><?php esc_html_e( 'Account', 'compelling-evidence' ); ?></th><th scope="col"><?php esc_html_e( 'Last login', 'compelling-evidence' ); ?></th><th scope="col"><?php esc_html_e( 'Sessions', 'compelling-evidence' ); ?></th><th scope="col"><?php esc_html_e( 'Application passwords', 'compelling-evidence' ); ?></th></tr></thead>
				<tbody>
					<?php foreach ( $admins as $a ) : ?>
						<tr>
							<td><a href="<?php echo esc_url( get_edit_user_link( $a['user']->ID ) ); ?>"><?php echo esc_html( $a['user']->user_login ); ?></a><br><span class="ce-field-desc"><?php echo esc_html( $a['user']->user_email ); ?></span></td>
							<td><?php echo $a['login'] ? esc_html( wp_date( $format, $a['login'] ) . ( $a['ip'] ? ' (' . $a['ip'] . ')' : '' ) ) : esc_html__( 'Not recorded', 'compelling-evidence' ); ?></td>
							<td><?php echo esc_html( (string) $a['sessions'] ); ?></td>
							<td>
								<?php
								echo esc_html( (string) count( $a['apps'] ) );
								foreach ( $a['apps'] as $app ) {
									echo '<br><span class="ce-field-desc">' . esc_html( $app['name'] . ': ' . ( $app['last_used'] ? wp_date( $format, (int) $app['last_used'] ) . ( $app['last_ip'] ? ' (' . $app['last_ip'] . ')' : '' ) : __( 'never used', 'compelling-evidence' ) ) ) . '</span>';
								}
								?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<h3 class="ce-section-title"><?php esc_html_e( 'Attacks seen', 'compelling-evidence' ); ?></h3>
		<?php
		$counts = get_option( CE_SEC_COUNTS, array() );
		$counts = is_array( $counts ) ? $counts : array();
		krsort( $counts );
		$labels = array(
			'xmlrpc'          => __( 'XML-RPC requests refused', 'compelling-evidence' ),
			'fake-jetpack'    => __( 'of them posing as Jetpack', 'compelling-evidence' ),
			'remote-password' => __( 'correct passwords refused on XML-RPC', 'compelling-evidence' ),
			'failed-login'    => __( 'failed logins', 'compelling-evidence' ),
		);
		?>
		<p class="ce-field-desc"><?php esc_html_e( 'Counted per day, in UTC, for the last 30 days. A correct password refused on XML-RPC means the password is known to someone else: change it.', 'compelling-evidence' ); ?></p>
		<?php if ( $counts ) : ?>
			<div class="ce-sec-scroll">
				<table class="widefat striped ce-sec-table">
					<thead><tr><th scope="col"><?php esc_html_e( 'Day', 'compelling-evidence' ); ?></th><?php foreach ( $labels as $label ) : ?><th scope="col"><?php echo esc_html( $label ); ?></th><?php endforeach; ?></tr></thead>
					<tbody>
						<?php foreach ( $counts as $day => $row ) : ?>
							<tr><td><?php echo esc_html( $day ); ?></td><?php foreach ( array_keys( $labels ) as $key ) : ?><td><?php echo esc_html( (string) ( isset( $row[ $key ] ) ? (int) $row[ $key ] : 0 ) ); ?></td><?php endforeach; ?></tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php else : ?>
			<p class="ce-field-desc"><?php esc_html_e( 'Nothing counted yet.', 'compelling-evidence' ); ?></p>
		<?php endif; ?>
		<h4 class="ce-sec-sub"><?php esc_html_e( 'Accounts being guessed, last 24 hours', 'compelling-evidence' ); ?></h4>
		<p class="ce-field-desc"><?php esc_html_e( 'Attackers change address on every try, so failures are counted by account name. An account of yours in this list should get a new, long password, and ideally a new name that is not the site name.', 'compelling-evidence' ); ?></p>
		<?php
		$failed = get_option( CE_SEC_FAILED, array() );
		$failed = is_array( $failed ) ? $failed : array();
		uasort(
			$failed,
			function ( $x, $y ) {
				return (int) $y['n'] - (int) $x['n'];
			}
		);
		?>
		<?php if ( $failed ) : ?>
			<ul class="ce-sec-list">
				<?php foreach ( array_slice( $failed, 0, 20, true ) as $name => $row ) : ?>
					<li>
						<?php
						$real = username_exists( $name ) || email_exists( $name );
						/* translators: 1: account name, 2: failures, 3: addresses, 4: whether the account exists. */
						echo esc_html( sprintf( __( '%1$s: %2$d failures from %3$d addresses%4$s', 'compelling-evidence' ), $name, (int) $row['n'], count( (array) $row['ips'] ), $real ? __( ', a real account on this site', 'compelling-evidence' ) : '' ) );
						?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p class="ce-field-desc"><?php esc_html_e( 'None.', 'compelling-evidence' ); ?></p>
		<?php endif; ?>

		<h3 class="ce-section-title"><?php esc_html_e( 'Activity', 'compelling-evidence' ); ?></h3>
		<?php $activity = ce_sec_activity(); ?>
		<h4 class="ce-sec-sub"><?php esc_html_e( 'Posts per day, last 30 days', 'compelling-evidence' ); ?></h4>
		<?php if ( $activity['days'] ) : ?>
			<div class="ce-sec-scroll">
				<table class="widefat striped ce-sec-table ce-sec-days">
					<thead><tr><th scope="col"><?php esc_html_e( 'Day', 'compelling-evidence' ); ?></th><th scope="col"><?php esc_html_e( 'Account and count', 'compelling-evidence' ); ?></th></tr></thead>
					<tbody>
						<?php foreach ( $activity['days'] as $day => $counts ) : ?>
							<tr>
								<td><?php echo esc_html( $day ); ?></td>
								<td>
									<?php
									$parts = array();
									foreach ( $counts as $login => $n ) {
										$parts[] = $login . ': ' . $n;
									}
									echo esc_html( implode( ', ', $parts ) );
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php else : ?>
			<p class="ce-field-desc"><?php esc_html_e( 'No posts in the last 30 days.', 'compelling-evidence' ); ?></p>
		<?php endif; ?>
		<h4 class="ce-sec-sub"><?php esc_html_e( 'Burst publishing', 'compelling-evidence' ); ?></h4>
		<p class="ce-field-desc"><?php esc_html_e( 'Hours in which one account added more than two posts. A report only: nothing is held or trashed for this.', 'compelling-evidence' ); ?></p>
		<?php if ( $activity['bursts'] ) : ?>
			<ul class="ce-sec-list">
				<?php foreach ( array_slice( $activity['bursts'], 0, 50 ) as $b ) : ?>
					<li><?php echo esc_html( sprintf( '%s, %s: %d', $b['login'], $b['when'], $b['n'] ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p class="ce-field-desc"><?php esc_html_e( 'None.', 'compelling-evidence' ); ?></p>
		<?php endif; ?>

		<h4 class="ce-sec-sub"><?php esc_html_e( 'Seeded content with gambling links', 'compelling-evidence' ); ?></h4>
		<?php $tainted = ce_sec_tainted_seeds(); ?>
		<?php if ( $tainted ) : ?>
			<p class="ce-field-desc"><?php esc_html_e( 'These seeded articles or pages link to gambling sites. Nothing is changed here: open each one and remove the links by hand.', 'compelling-evidence' ); ?></p>
			<ul class="ce-sec-list">
				<?php foreach ( $tainted as $post ) : ?>
					<li><a href="<?php echo esc_url( (string) get_edit_post_link( $post->ID ) ); ?>"><?php echo esc_html( $post->post_title ); ?></a> <span class="ce-field-desc"><?php echo esc_html( implode( ', ', $post->ce_hosts ) ); ?></span></li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<p class="ce-field-desc"><?php esc_html_e( 'None: no seeded article or page links to a gambling site.', 'compelling-evidence' ); ?></p>
		<?php endif; ?>

		<h3 class="ce-section-title"><?php esc_html_e( 'Security log', 'compelling-evidence' ); ?></h3>
		<?php
		$log = get_option( CE_SEC_LOG, array() );
		$log = is_array( $log ) ? array_reverse( $log ) : array();
		?>
		<?php if ( $log ) : ?>
			<div class="ce-sec-scroll ce-sec-scroll--tall">
				<table class="widefat striped ce-sec-table">
					<thead><tr><th scope="col"><?php esc_html_e( 'Time', 'compelling-evidence' ); ?></th><th scope="col"><?php esc_html_e( 'Event', 'compelling-evidence' ); ?></th><th scope="col"><?php esc_html_e( 'Detail', 'compelling-evidence' ); ?></th><th scope="col"><?php esc_html_e( 'Account', 'compelling-evidence' ); ?></th><th scope="col"><?php esc_html_e( 'Route', 'compelling-evidence' ); ?></th><th scope="col"><?php esc_html_e( 'IP', 'compelling-evidence' ); ?></th></tr></thead>
					<tbody>
						<?php foreach ( $log as $e ) : ?>
							<?php $u = ! empty( $e['user'] ) ? get_userdata( (int) $e['user'] ) : false; ?>
							<tr>
								<td><?php echo esc_html( wp_date( $format, (int) $e['time'] ) ); ?></td>
								<td><?php echo esc_html( $e['event'] ); ?></td>
								<td>
									<?php
									if ( ! empty( $e['post_id'] ) && get_post( (int) $e['post_id'] ) ) {
										echo '<a href="' . esc_url( (string) get_edit_post_link( (int) $e['post_id'] ) ) . '">' . esc_html( $e['title'] ) . '</a>';
									} else {
										echo esc_html( $e['title'] );
									}
									if ( ! empty( $e['reasons'] ) ) {
										echo '<br><span class="ce-field-desc">' . esc_html( implode( '; ', (array) $e['reasons'] ) ) . '</span>';
									}
									?>
								</td>
								<td><?php echo esc_html( $u ? $u->user_login : '' ); ?></td>
								<td><?php echo esc_html( $e['type'] ); ?></td>
								<td><?php echo esc_html( $e['ip'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php ce_sec_button( 'ce_sec_clear_log', __( 'Clear the log', 'compelling-evidence' ), __( 'Clear the security log?', 'compelling-evidence' ) ); ?>
		<?php else : ?>
			<p class="ce-field-desc"><?php esc_html_e( 'Nothing logged yet.', 'compelling-evidence' ); ?></p>
		<?php endif; ?>
	</section>
	<?php
}
