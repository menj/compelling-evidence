<?php
/**
 * Compelling Evidence: the login screen.
 *
 * Dresses every screen wp-login.php serves (log in, lost password, reset
 * password, registration, the interim login shown when a session expires)
 * in the site's night palette and type, through WordPress's own login
 * hooks. It builds no login link and never reads the login address, so the
 * WPS Hide Login plugin keeps moving the address and the design follows it.
 *
 * Theme Options → Security → Login screen: a switch (ce_login_design, on by
 * default), an optional logo URL (ce_login_logo_url) and an optional line
 * above the form (ce_login_message). While a plugin that styles the login
 * screen is active (LoginPress, Custom Login, Login Logo, Branda and the
 * like; see ce_login_plugin_active()), the theme steps aside.
 *
 * Colours follow Theme Options → Colors; fonts follow Theme Options →
 * Typography. The stylesheet is assets/css/ce-login.css.
 *
 * @since 2.17.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Whether a plugin that styles the login screen is active.
 * Filter: ce_login_plugin_active.
 */
function ce_login_plugin_active(): bool {
    $active = defined( 'LOGINPRESS_VERSION' ) || class_exists( 'LoginPress' )
        || defined( 'CUSTOM_LOGIN_VERSION' ) || class_exists( 'CWS_Login_Logo_Plugin' )
        || defined( 'BRANDA_VERSION' ) || class_exists( 'Branda' )
        || defined( 'ACL_PLUGIN_URL' ) || class_exists( 'Login_Designer' )
        || defined( 'ERIDENT_CUSTOM_LOGIN_VERSION' ) || function_exists( 'er_login_logo' );
    return (bool) apply_filters( 'ce_login_plugin_active', $active );
}

/** Whether the theme dresses the login screen. */
function ce_login_design_on(): bool {
    return '1' === (string) get_option( 'ce_login_design', '1' ) && ! ce_login_plugin_active();
}

/** Whether this request is the interim (session expired) login. */
function ce_login_is_interim(): bool {
    return isset( $_REQUEST['interim-login'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read only.
}

/**
 * The image above the form: the Theme Options URL, else the custom logo,
 * else the theme's CE mark.
 */
function ce_login_logo_url(): string {
    $url = trim( (string) get_option( 'ce_login_logo_url', '' ) );
    if ( '' !== $url ) {
        return $url;
    }
    $logo_id = (int) get_theme_mod( 'custom_logo' );
    if ( $logo_id ) {
        $src = wp_get_attachment_image_url( $logo_id, 'medium' );
        if ( $src ) {
            return (string) $src;
        }
    }
    return get_stylesheet_directory_uri() . '/assets/images/ce-icon.svg';
}

/**
 * Custom properties for the login stylesheet: the Colors tab's changes,
 * the Typography tab's families and the logo.
 */
function ce_login_inline_css(): string {
    $map = [
        'ce_color_accent'     => [ '--accent', '#e8455a' ],
        'ce_color_accent2'    => [ '--accent2', '#ff6b35' ],
        'ce_color_teal'       => [ '--teal', '#0cd4e0' ],
        'ce_color_purple'     => [ '--purple', '#6b2fa0' ],
        'ce_color_gold'       => [ '--gold', '#f5c518' ],
        'ce_color_bg_deep'    => [ '--deep', '#1a0a2e' ],
        'ce_color_bg_darkest' => [ '--bg-deepest', '#0d0820' ],
    ];
    $vars = '';
    foreach ( $map as $option => $pair ) {
        $hex = sanitize_hex_color( (string) get_option( $option, $pair[1] ) );
        if ( $hex && $hex !== $pair[1] ) {
            $vars .= $pair[0] . ':' . $hex . ';';
        }
    }
    if ( function_exists( 'ce_font_registry' ) && function_exists( 'ce_font_roles' ) ) {
        $fonts = ce_font_registry();
        foreach ( ce_font_roles() as $role => $cfg ) {
            $key = ce_font_choice( $role );
            if ( $key !== $cfg['default'] ) {
                $vars .= $cfg['var'] . ':' . $fonts[ $key ]['stack'] . ';';
            }
        }
    }
    $vars .= '--ce-login-logo:url("' . esc_url_raw( ce_login_logo_url() ) . '");';
    return ':root{' . $vars . '}';
}

/** Stylesheet, on the login screen only. */
function ce_login_assets(): void {
    if ( ! ce_login_design_on() ) {
        return;
    }
    $ver = (string) wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'ce-login', get_stylesheet_directory_uri() . '/assets/css/ce-login.css', [ 'login' ], $ver );
    wp_add_inline_style( 'ce-login', ce_login_inline_css() );
}
add_action( 'login_enqueue_scripts', 'ce_login_assets' );

/** The mark links to the site. */
function ce_login_header_url( $url ) {
    return ce_login_design_on() ? home_url( '/' ) : $url;
}
add_filter( 'login_headerurl', 'ce_login_header_url' );

/**
 * The link text: the site name as the nav wordmark sets it, first word in
 * the accent red. Core prints this inside the h1 link unescaped, so every
 * part is escaped here.
 */
function ce_login_header_text( $text ) {
    if ( ! ce_login_design_on() ) {
        return $text;
    }
    $name  = trim( wp_strip_all_tags( (string) get_bloginfo( 'name' ) ) );
    $parts = preg_split( '/\s+/u', $name, 2 );
    $first = (string) ( $parts[0] ?? '' );
    $rest  = (string) ( $parts[1] ?? '' );
    return '<span class="ce-login-word"><span class="ce-login-word__first">' . esc_html( $first ) . '</span>'
        . ( '' !== $rest ? ' <span class="ce-login-word__rest">' . esc_html( $rest ) . '</span>' : '' ) . '</span>';
}
add_filter( 'login_headertext', 'ce_login_header_text' );

/** Body classes that scope the stylesheet. */
function ce_login_body_class( $classes ) {
    if ( ! ce_login_design_on() ) {
        return $classes;
    }
    $classes   = (array) $classes;
    $classes[] = 'ce-login';
    if ( ce_login_is_interim() ) {
        $classes[] = 'ce-login--interim';
    }
    return $classes;
}
add_filter( 'login_body_class', 'ce_login_body_class' );

/**
 * The title tag: "<screen> | Compelling Evidence", under 60 characters,
 * through the same builder as every other page.
 *
 * @param string $login_title Core's title.
 * @param string $title       The screen's own title ("Log In", "Lost Password").
 */
function ce_login_title( $login_title, $title = '' ) {
    if ( ! function_exists( 'ce_title_build' ) ) {
        return $login_title;
    }
    $part = '' !== trim( (string) $title ) ? (string) $title : __( 'Log In', 'compelling-evidence' );
    return ce_title_build( $part );
}
add_filter( 'login_title', 'ce_login_title', 10, 2 );

/** The line from Theme Options above the log-in form, never in the interim login. */
function ce_login_message( $message ) {
    $note   = trim( (string) get_option( 'ce_login_message', '' ) );
    $action = isset( $_REQUEST['action'] ) && is_string( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : 'login'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read only.
    if ( '' === $note || 'login' !== $action || ce_login_is_interim() || ! ce_login_design_on() ) {
        return $message;
    }
    return $message . '<p class="message ce-login-note">' . esc_html( $note ) . '</p>';
}
add_filter( 'login_message', 'ce_login_message' );
