<?php
/**
 * Compelling Evidence — font roles.
 *
 * One registry for every self-hosted Latin family. Theme Options →
 * Typography assigns a family to each role (heading, reading body,
 * labels); this file turns those choices into CSS custom properties
 * (--font-heading, --font-reading, --font-accent) and into the font
 * preload tags in header.php. The @font-face rules themselves live in
 * assets/css/main.css. Arabic stacks are fixed and not part of this system.
 *
 * @since 2.6.23
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registered families.
 *
 * roles:   which Typography selects may offer the family.
 * preload: file in assets/fonts/ to preload when the family sets headings.
 */
function ce_font_registry(): array {
    return [
        'playfair' => [
            'label'   => 'Playfair Display (serif, editorial)',
            'stack'   => "'Playfair Display', Georgia, serif",
            'roles'   => [ 'heading', 'reading' ],
            'preload' => 'playfair-display-900.woff2',
        ],
        'cormorant' => [
            'label'   => 'Cormorant Garamond (serif, literary)',
            'stack'   => "'Cormorant Garamond', Georgia, serif",
            'roles'   => [ 'heading', 'reading' ],
            'preload' => 'cormorant-garamond-600.woff2',
        ],
        'eb-garamond' => [
            'label'   => 'EB Garamond (serif, classical)',
            'stack'   => "'EB Garamond', Georgia, serif",
            'roles'   => [ 'heading', 'reading' ],
            'preload' => 'eb-garamond-var.woff2',
        ],
        'sabon' => [
            'label'   => 'Sabon Next LT (serif, book face)',
            'stack'   => "'Sabon Next LT', 'EB Garamond', Georgia, serif",
            'roles'   => [ 'heading', 'reading' ],
            'preload' => 'sabon-next-lt-700.woff2',
        ],
        'dm-sans' => [
            'label'   => 'DM Sans (sans-serif, modern)',
            'stack'   => "'DM Sans', system-ui, sans-serif",
            'roles'   => [ 'reading', 'accent' ],
            'preload' => 'dm-sans-400.woff2',
        ],
        'special-elite' => [
            'label'   => 'Special Elite (typewriter)',
            'stack'   => "'Special Elite', 'Courier New', monospace",
            'roles'   => [ 'accent' ],
            'preload' => 'special-elite-400.woff2',
        ],
    ];
}

/** Option name and default family for each role. */
function ce_font_roles(): array {
    return [
        'heading' => [ 'option' => 'ce_heading_font', 'default' => 'playfair',  'var' => '--font-heading' ],
        'reading' => [ 'option' => 'ce_reading_font', 'default' => 'cormorant', 'var' => '--font-reading' ],
        'accent'  => [ 'option' => 'ce_accent_font',  'default' => 'dm-sans',   'var' => '--font-accent' ],
    ];
}

/** Select options for one role, for the Typography tab. */
function ce_font_options( string $role ): array {
    $out = [];
    foreach ( ce_font_registry() as $key => $font ) {
        if ( in_array( $role, $font['roles'], true ) ) {
            $out[ $key ] = $font['label'];
        }
    }
    return $out;
}

/** The family key chosen for a role, falling back to the default when invalid. */
function ce_font_choice( string $role ): string {
    $roles = ce_font_roles();
    $fonts = ce_font_registry();
    $key   = (string) get_option( $roles[ $role ]['option'], $roles[ $role ]['default'] );
    if ( ! isset( $fonts[ $key ] ) || ! in_array( $role, $fonts[ $key ]['roles'], true ) ) {
        $key = $roles[ $role ]['default'];
    }
    return $key;
}

/**
 * Print role variables for any role that differs from its default.
 * Defaults are already declared in main.css, so an untouched site
 * prints nothing.
 */
function ce_fonts_inject_css() {
    $fonts = ce_font_registry();
    $vars  = [];
    foreach ( ce_font_roles() as $role => $cfg ) {
        $key = ce_font_choice( $role );
        if ( $key !== $cfg['default'] ) {
            $vars[] = $cfg['var'] . ': ' . $fonts[ $key ]['stack'] . ';';
        }
    }
    if ( $vars ) {
        echo '<style id="ce-font-roles">:root { ' . implode( ' ', $vars ) . " }</style>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- font stacks are constants from ce_font_registry().
    }
}
add_action( 'wp_head', 'ce_fonts_inject_css', 6 );

/** Body class that lets main.css adjust labels for the single-weight typewriter face. */
function ce_fonts_body_class( array $classes ): array {
    if ( ce_font_choice( 'accent' ) === 'special-elite' ) {
        $classes[] = 'ce-accent-typewriter';
    }
    return $classes;
}
add_filter( 'body_class', 'ce_fonts_body_class' );

/**
 * Preload tags for the fonts needed on first paint: the heading family
 * and the UI family. Honours Theme Options → Performance → Preload
 * critical fonts, which earlier versions ignored.
 */
function ce_fonts_preload_tags() {
    if ( get_option( 'ce_preload_fonts', '1' ) !== '1' ) {
        return;
    }
    $fonts = ce_font_registry();
    $files = array_unique( [
        $fonts[ ce_font_choice( 'heading' ) ]['preload'],
        $fonts['dm-sans']['preload'],
    ] );
    $base = get_stylesheet_directory_uri() . '/assets/fonts/';
    foreach ( $files as $file ) {
        printf(
            '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
            esc_url( $base . $file )
        );
    }
}
