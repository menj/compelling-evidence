<?php
/**
 * Compelling Evidence — Theme Options
 *
 * Tabbed settings interface for site-wide configuration.
 * Admin menu: Appearance → CE Theme Options
 *
 * @since 2.2.75
 */

if ( ! defined( 'ABSPATH' ) ) exit;


/* ═══════════════════════════════════════════════════════════════════════
   REGISTER SETTINGS
   ═══════════════════════════════════════════════════════════════════════ */

function ce_options_register() {
    // ── General ──
    register_setting( 'ce_options_general', 'ce_hero_title', [ 'default' => 'What if the evidence pointed somewhere you didn\'t expect?' ] );
    register_setting( 'ce_options_general', 'ce_hero_subtitle', [ 'default' => 'Challenging what you think you know — with evidence, not assertion.' ] );
    register_setting( 'ce_options_general', 'ce_hero_verse_arabic', [ 'default' => 'فَبِأَيِّ آلَاءِ رَبِّكُمَا تُكَذِّبَانِ' ] );
    register_setting( 'ce_options_general', 'ce_hero_verse_translation', [ 'default' => 'Then which of the favours of your Lord will you deny?' ] );
    register_setting( 'ce_options_general', 'ce_hero_verse_ref', [ 'default' => 'Surah Ar-Rahman (55:13)' ] );
    register_setting( 'ce_options_general', 'ce_hero_verse_number', [ 'default' => '١٣' ] );
    register_setting( 'ce_options_general', 'ce_footer_text', [ 'default' => '© Compelling Evidence. All rights reserved.' ] );

    // ── Colors ──
    register_setting( 'ce_options_colors', 'ce_color_accent', [ 'default' => '#e8455a' ] );
    register_setting( 'ce_options_colors', 'ce_color_accent2', [ 'default' => '#ff6b35' ] );
    register_setting( 'ce_options_colors', 'ce_color_teal', [ 'default' => '#0cd4e0' ] );
    register_setting( 'ce_options_colors', 'ce_color_purple', [ 'default' => '#6b2fa0' ] );
    register_setting( 'ce_options_colors', 'ce_color_gold', [ 'default' => '#f5c518' ] );
    register_setting( 'ce_options_colors', 'ce_color_bg_deep', [ 'default' => '#1a0a2e' ] );
    register_setting( 'ce_options_colors', 'ce_color_bg_darkest', [ 'default' => '#0d0820' ] );

    // ── Typography ──
    register_setting( 'ce_options_typography', 'ce_font_scale', [ 'default' => '1.0' ] );
    register_setting( 'ce_options_typography', 'ce_reading_font', [ 'default' => 'cormorant' ] );
    register_setting( 'ce_options_typography', 'ce_heading_font', [ 'default' => 'playfair' ] );
    register_setting( 'ce_options_typography', 'ce_accent_font', [ 'default' => 'dm-sans' ] );

    // ── Quiz & Journeys ──
    register_setting( 'ce_options_quiz', 'ce_quiz_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_quiz', 'ce_quiz_max_selections', [ 'default' => '3' ] );
    register_setting( 'ce_options_quiz', 'ce_journeys_per_row', [ 'default' => '3' ] );
    register_setting( 'ce_options_quiz', 'ce_show_journey_progress', [ 'default' => '1' ] );

    // ── Engagement ──
    register_setting( 'ce_options_engagement', 'ce_voting_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_engagement', 'ce_resonance_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_engagement', 'ce_crosslinks_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_engagement', 'ce_search_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_engagement', 'ce_articles_per_page', [ 'default' => '12' ] );

    // ── Links & Tooltips ──
    register_setting( 'ce_options_links', 'ce_crosslinks_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_links', 'ce_crosslink_max_per_article', [ 'default' => '5' ] );
    register_setting( 'ce_options_links', 'ce_crosslink_min_per_article', [ 'default' => '3' ] );
    register_setting( 'ce_options_links', 'ce_crosslink_style', [ 'default' => 'underline' ] );
    register_setting( 'ce_options_links', 'ce_crosslink_color', [ 'default' => '#e8455a' ] );
    register_setting( 'ce_options_links', 'ce_crosslink_disabled_categories', [ 'default' => '' ] );
    register_setting( 'ce_options_links', 'ce_crosslink_nofollow', [ 'default' => '0' ] );
    register_setting( 'ce_options_links', 'ce_tooltips_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_links', 'ce_tooltip_max_per_article', [ 'default' => '8' ] );
    register_setting( 'ce_options_links', 'ce_tooltip_style', [ 'default' => 'dashed' ] );
    register_setting( 'ce_options_links', 'ce_tooltip_color', [ 'default' => '#0cd4e0' ] );
    register_setting( 'ce_options_links', 'ce_tooltip_show_arabic', [ 'default' => '1' ] );
    register_setting( 'ce_options_links', 'ce_tooltip_trigger', [ 'default' => 'hover' ] );
    register_setting( 'ce_options_links', 'ce_sister_links_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_links', 'ce_murtadd_counterparts_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_links', 'ce_sister_links_max', [ 'default' => '3' ] );
    register_setting( 'ce_options_links', 'ce_contextual_links_extra', [ 'default' => '' ] );
    register_setting( 'ce_options_links', 'ce_contextual_links_excluded', [ 'default' => '' ] );

    // ── Reading progress bar (inc/ce-reading-progress.php) ──
    register_setting( 'ce_options_engagement', 'ce_rpb_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_engagement', 'ce_rpb_color', [ 'default' => '#0cd4e0' ] );
    register_setting( 'ce_options_engagement', 'ce_rpb_height', [ 'default' => '3' ] );
    register_setting( 'ce_options_engagement', 'ce_rpb_position', [ 'default' => 'top' ] );
    foreach ( ce_rpb_post_types() as $ce_rpb_type => $ce_rpb_default ) {
        register_setting( 'ce_options_engagement', 'ce_rpb_on_' . $ce_rpb_type, [ 'default' => $ce_rpb_default ] );
    }

    // ── Social icons (inc/ce-social-icons.php) ──
    register_setting( 'ce_options_identity', 'ce_social_footer', [ 'default' => '1' ] );
    foreach ( [ 'instagram', 'tiktok', 'threads', 'bluesky', 'mastodon', 'telegram', 'whatsapp' ] as $ce_social_key ) {
        register_setting( 'ce_options_identity', 'ce_social_' . $ce_social_key, [ 'default' => '', 'sanitize_callback' => 'esc_url_raw' ] );
    }

    // ── Analytics ──
    register_setting( 'ce_options_analytics', 'ce_analytics_enabled', [ 'default' => '0' ] );
    register_setting( 'ce_options_analytics', 'ce_analytics_retention_days', [ 'default' => '90' ] );
    register_setting( 'ce_options_analytics', 'ce_analytics_track_scroll', [ 'default' => '1' ] );
    register_setting( 'ce_options_analytics', 'ce_analytics_track_search', [ 'default' => '1' ] );

    // ── Performance ──
    register_setting( 'ce_options_performance', 'ce_parallax_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_performance', 'ce_preload_fonts', [ 'default' => '1' ] );
    register_setting( 'ce_options_performance', 'ce_minify_inline', [ 'default' => '0' ] );
    register_setting( 'ce_options_performance', 'ce_gone_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_performance', 'ce_gone_paths', [ 'default' => ce_gone_default_paths() ] );

    // Security (inc/ce-security.php)
    foreach ( [ 'ce_sec_xmlrpc_off', 'ce_sec_app_passwords_off', 'ce_sec_rest_lock', 'ce_sec_spam_gate', 'ce_sec_gone' ] as $ce_sec_key ) {
        register_setting( 'ce_options_security', $ce_sec_key, [ 'default' => '1' ] );
    }
}
add_action( 'admin_init', 'ce_options_register' );


/* ═══════════════════════════════════════════════════════════════════════
   ADMIN MENU
   ═══════════════════════════════════════════════════════════════════════ */

function ce_options_menu() {
    add_theme_page(
        __( 'CE Theme Options', 'compelling-evidence' ),
        __( 'CE Theme Options', 'compelling-evidence' ),
        'manage_options',
        'ce-theme-options',
        'ce_options_page'
    );
}
add_action( 'admin_menu', 'ce_options_menu' );

function ce_options_admin_assets( $hook ) {
    if ( 'appearance_page_ce-theme-options' !== $hook ) return;
    $uri     = get_stylesheet_directory_uri();
    $version = wp_get_theme()->get( 'Version' );
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_style( 'ce-theme-options-admin', $uri . '/assets/css/ce-theme-options-admin.css', [ 'wp-color-picker', 'dashicons' ], $version );
    wp_enqueue_script( 'ce-theme-options-admin', $uri . '/assets/js/ce-theme-options-admin.js', [ 'jquery', 'wp-color-picker' ], $version, true );
}
add_action( 'admin_enqueue_scripts', 'ce_options_admin_assets' );


/* ═══════════════════════════════════════════════════════════════════════
   TABS AND USAGE TEXT
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * The tabs of the Theme Options page, in order, each with its usage text.
 *
 * @since 2.15.0 usage text added; labels translatable.
 */
function ce_theme_opts_tabs(): array {
    return [
        'general'     => [ 'label' => __( 'General', 'compelling-evidence' ),           'icon' => 'dashicons-admin-home',
            'usage' => __( 'The words on the home page hero and the footer line. The Arabic verse, its ayah number, translation and reference print together above the search box; change all four when you change the verse. Save, then reload the home page.', 'compelling-evidence' ) ],
        'identity'    => [ 'label' => __( 'Author & Identity', 'compelling-evidence' ), 'icon' => 'dashicons-businessperson',
            'usage' => __( 'Who wrote the site, for the byline and for the Person and Organization schema. Fill the author name first: with it empty, articles carry Organization authorship only. Profile and social fields take full URLs; the site Twitter field takes the handle with its @. Every site profile filled in, X, Facebook and YouTube included, shows as an icon in the footer; paste [ce_social_icons] or the Social icons pattern to show the same row elsewhere.', 'compelling-evidence' ) ],
        'colors'      => [ 'label' => __( 'Colors', 'compelling-evidence' ),            'icon' => 'dashicons-art',
            'usage' => __( 'Seven brand colours, printed as CSS custom properties in the page head. A colour left at its default prints nothing. Pick a colour or type a hex value; Default inside each picker puts the shipped colour back. Check text contrast on the dark background after any change.', 'compelling-evidence' ) ],
        'typography'  => [ 'label' => __( 'Typography', 'compelling-evidence' ),        'icon' => 'dashicons-editor-textcolor',
            'usage' => __( 'Click a card to choose the face for headings, for reading text and for small labels. Every face is self-hosted, so no font is fetched from another server. The size scale changes body text only; headings and controls keep their sizes.', 'compelling-evidence' ) ],
        'links'       => [ 'label' => __( 'Links & Tooltips', 'compelling-evidence' ),  'icon' => 'dashicons-admin-links',
            'usage' => __( 'Automatic links inside article and answer text: crosslinks to this site, links to the other sites, murtadd.org counterparts and glossary tooltips. Drag a slider to set a limit; the number beside it is the value that will be saved. Links never go into headings, quotations, Quran or hadith blocks.', 'compelling-evidence' ) ],
        'media'       => [ 'label' => __( 'Media', 'compelling-evidence' ),             'icon' => 'dashicons-format-image',
            'usage' => __( 'The lightbox, automatic lead images from Pexels and the import of registered article images into the Media Library. Automatic images stay off until you switch them on and add a Pexels key. Use the buttons below the settings to import or place images now.', 'compelling-evidence' ) ],
        'quiz'        => [ 'label' => __( 'Quiz & Journeys', 'compelling-evidence' ),   'icon' => 'dashicons-forms',
            'usage' => __( 'Turn the persona quiz on or off, set how many answers a reader may pick per question, and lay out the journeys overview. With the quiz off, /quiz/ redirects to /journeys/. The scoring and the 14 journeys themselves are not changed here.', 'compelling-evidence' ) ],
        'engagement'  => [ 'label' => __( 'Engagement', 'compelling-evidence' ),        'icon' => 'dashicons-thumbs-up',
            'usage' => __( 'Reader feedback under each article (votes and resonance buttons), the home page search, the archive page size and the reading progress bar. Votes and resonance are stored per article with a hashed address; no account is needed.', 'compelling-evidence' ) ],
        'analytics'   => [ 'label' => __( 'Analytics', 'compelling-evidence' ),         'icon' => 'dashicons-chart-area',
            'usage' => __( 'First-party analytics is off by default. Switch it on to record page views, quiz and journey progress, article reads and searches in this database; the report is under CE Analytics. Rows older than the retention period are deleted every day, whether tracking is on or off.', 'compelling-evidence' ) ],
        'performance' => [ 'label' => __( 'Performance', 'compelling-evidence' ),       'icon' => 'dashicons-performance',
            'usage' => __( 'Front-end effects, font preloading and the 410 Gone answer for retired paths. List one path prefix per line, each starting with a slash; never list a prefix that real content uses.', 'compelling-evidence' ) ],
        'security'    => [ 'label' => __( 'Security', 'compelling-evidence' ),          'icon' => 'dashicons-shield',
            'usage' => __( 'Switches that close the remote-publishing routes spam tools use, the spam gate for new content, the design of the login screen, and the tools below them: scan and trash spam, revoke application passwords, end sessions and read the security log. The block editor is not affected.', 'compelling-evidence' ) ],
        'tools'       => [ 'label' => __( 'Tools & Sync', 'compelling-evidence' ),      'icon' => 'dashicons-admin-tools',
            'usage' => __( 'Articles live in JSON batch files in the theme and sync to the database when the theme version changes. Run the sync by hand after editing a batch file, and use the diagnostics to check the manifest and checksums when articles look empty.', 'compelling-evidence' ) ],
    ];
}


/* ═══════════════════════════════════════════════════════════════════════
   SANITISING
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Sanitise one submitted value for its field definition.
 *
 * @since 2.15.0
 *
 * @param array $field Field definition from ce_get_tab_fields().
 * @param mixed $raw   Unslashed raw value, or null when absent.
 * @return string
 */
function ce_theme_opts_sanitize( array $field, $raw ): string {
    $type    = $field['type'];
    $default = (string) ( $field['default'] ?? '' );
    if ( 'checkbox' === $type ) {
        return null !== $raw ? '1' : '0';
    }
    $value = is_scalar( $raw ) ? (string) $raw : '';
    switch ( $type ) {
        case 'color':
            return sanitize_hex_color( $value ) ?: $default;
        case 'number':
        case 'range':
            $n = absint( $value );
            if ( isset( $field['min'] ) ) $n = max( (int) $field['min'], $n );
            if ( isset( $field['max'] ) ) $n = min( (int) $field['max'], $n );
            return (string) $n;
        case 'select':
        case 'choice':
            $value = sanitize_text_field( $value );
            return array_key_exists( $value, (array) ( $field['options'] ?? [] ) ) ? $value : $default;
        case 'search_base':
            return function_exists( 'ce_search_clean_base' ) && '' !== ce_search_clean_base( $value ) ? ce_search_clean_base( $value ) : 'search';
        case 'url':
            return (string) esc_url_raw( trim( $value ), [ 'http', 'https' ] );
        case 'textarea':
            return 'ce_contextual_links_extra' === $field['id'] && function_exists( 'ce_ctx_sanitize_extra_lines' )
                ? ce_ctx_sanitize_extra_lines( $value )
                : sanitize_textarea_field( $value );
        default:
            return sanitize_text_field( $value );
    }
}

/**
 * Save the fields of one tab from $_POST. The caller has checked the
 * capability and the nonce.
 *
 * @since 2.15.0
 */
function ce_theme_opts_save_tab( string $tab ): void {
    foreach ( ce_get_tab_fields( $tab ) as $field ) {
        if ( 'info' === $field['type'] ) continue;
        $key = $field['id'];
        $raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Missing -- sanitised by ce_theme_opts_sanitize(); nonce checked by the caller.
        update_option( $key, ce_theme_opts_sanitize( $field, $raw ) );
    }
}


/* ═══════════════════════════════════════════════════════════════════════
   FIELD RENDERING
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Small sample shown inside a visual selector card, keyed by field.
 */
function ce_theme_opts_choice_preview( string $field_id, string $value ): string {
    switch ( $field_id ) {
        case 'ce_heading_font':
        case 'ce_reading_font':
        case 'ce_accent_font':
            return '<span class="ce-theme-opts-choice__sample ce-theme-opts-font--' . esc_attr( $value ) . '">' . esc_html__( 'Evidence, then belief', 'compelling-evidence' ) . '</span>';
        case 'ce_font_scale':
            return '<span class="ce-theme-opts-choice__sample ce-theme-opts-scale--' . esc_attr( str_replace( '.', '', $value ) ) . '">Aa</span>';
        case 'ce_crosslink_style':
            return '<span class="ce-theme-opts-choice__sample">' . esc_html__( 'Read the', 'compelling-evidence' ) . ' <span class="ce-theme-opts-link ce-theme-opts-link--' . esc_attr( $value ) . '">' . esc_html__( 'linked phrase', 'compelling-evidence' ) . '</span></span>';
        case 'ce_tooltip_style':
            return '<span class="ce-theme-opts-choice__sample">' . esc_html__( 'The word', 'compelling-evidence' ) . ' <span class="ce-theme-opts-term ce-theme-opts-term--' . esc_attr( $value ) . '">tawhid</span></span>';
        case 'ce_journeys_per_row':
            return '<span class="ce-theme-opts-choice__sample ce-theme-opts-grid" aria-hidden="true">' . str_repeat( '<i></i>', (int) $value ) . '</span>';
        case 'ce_quiz_max_selections':
            return '<span class="ce-theme-opts-choice__sample ce-theme-opts-dots" aria-hidden="true">' . str_repeat( '<i></i>', (int) $value ) . '</span>';
        case 'ce_tooltip_trigger':
            return '<span class="dashicons ' . ( 'hover' === $value ? 'dashicons-visibility' : 'dashicons-admin-generic' ) . '" aria-hidden="true"></span>';
    }
    return '';
}

/**
 * Print one field row: label and description on the left, control on the right.
 */
function ce_theme_opts_render_field( string $tab, array $field ): void {
    $key   = $field['id'];
    $id    = 'ce-opt-' . $tab . '-' . str_replace( '_', '-', $key );
    $value = (string) get_option( $key, $field['default'] ?? '' );
    $desc  = $field['desc'] ?? '';
    $did   = $desc ? $id . '-desc' : '';
    $aria  = $did ? ' aria-describedby="' . esc_attr( $did ) . '"' : '';
    $type  = $field['type'];

    if ( 'info' === $type ) {
        echo '<div class="ce-theme-opts-callout"><strong>' . esc_html( $field['label'] ) . '</strong></div>';
        return;
    }

    $is_group = 'choice' === $type;
    echo '<div class="ce-theme-opts-row ce-theme-opts-row--' . esc_attr( $type ) . '">';

    // Label column.
    echo '<div class="ce-theme-opts-row__label">';
    if ( $is_group ) {
        echo '<span class="ce-theme-opts-label" id="' . esc_attr( $id . '-label' ) . '">' . esc_html( $field['label'] ) . '</span>';
    } else {
        echo '<label class="ce-theme-opts-label" for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ) . '</label>';
    }
    if ( $desc ) {
        echo '<p class="ce-theme-opts-desc" id="' . esc_attr( $did ) . '">' . esc_html( $desc ) . '</p>';
    }
    echo '</div>';

    // Control column.
    echo '<div class="ce-theme-opts-row__control">';
    switch ( $type ) {
        case 'checkbox':
            echo '<span class="ce-theme-opts-switch">';
            echo '<input type="checkbox" role="switch" class="ce-theme-opts-switch__input" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="1"' . checked( $value, '1', false ) . $aria . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- checked() returns a fixed attribute; $aria is built from esc_attr().
            echo '<span class="ce-theme-opts-switch__track" aria-hidden="true"><span class="ce-theme-opts-switch__thumb"></span></span>';
            echo '<span class="ce-theme-opts-switch__state" aria-hidden="true"><span class="is-on">' . esc_html__( 'On', 'compelling-evidence' ) . '</span><span class="is-off">' . esc_html__( 'Off', 'compelling-evidence' ) . '</span></span>';
            echo '</span>';
            break;

        case 'range':
            $min  = (int) ( $field['min'] ?? 0 );
            $max  = (int) ( $field['max'] ?? 100 );
            $step = (int) ( $field['step'] ?? 1 );
            echo '<span class="ce-theme-opts-range">';
            echo '<input type="range" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" min="' . esc_attr( (string) $min ) . '" max="' . esc_attr( (string) $max ) . '" step="' . esc_attr( (string) max( 1, $step ) ) . '" value="' . esc_attr( $value ) . '"' . $aria . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $aria is built from esc_attr().
            echo '<output class="ce-theme-opts-range__value" id="' . esc_attr( $id . '-value' ) . '" for="' . esc_attr( $id ) . '" aria-live="polite">' . esc_html( $value ) . '</output>';
            echo '<span class="ce-theme-opts-range__bounds" aria-hidden="true"><span>' . esc_html( (string) $min ) . '</span><span>' . esc_html( (string) $max ) . '</span></span>';
            echo '</span>';
            break;

        case 'color':
            echo '<input type="text" class="ce-theme-opts-color" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( (string) $field['default'] ) . '" maxlength="7" spellcheck="false"' . $aria . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $aria is built from esc_attr().
            break;

        case 'choice':
            echo '<div class="ce-theme-opts-choices" role="radiogroup" aria-labelledby="' . esc_attr( $id . '-label' ) . '"' . $aria . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $aria is built from esc_attr().
            $i = 0;
            foreach ( (array) $field['options'] as $opt_val => $opt_label ) {
                $opt_id = $id . '-' . $i++;
                echo '<label class="ce-theme-opts-choice" for="' . esc_attr( $opt_id ) . '">';
                echo '<input type="radio" class="ce-theme-opts-choice__input" id="' . esc_attr( $opt_id ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( (string) $opt_val ) . '"' . checked( $value, (string) $opt_val, false ) . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- checked() returns a fixed attribute.
                echo '<span class="ce-theme-opts-choice__card">';
                echo ce_theme_opts_choice_preview( $key, (string) $opt_val ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- every part is escaped inside the function.
                echo '<span class="ce-theme-opts-choice__label">' . esc_html( $opt_label ) . '</span>';
                echo '</span></label>';
            }
            echo '</div>';
            break;

        case 'select':
            echo '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '"' . $aria . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $aria is built from esc_attr().
            foreach ( (array) $field['options'] as $opt_val => $opt_label ) {
                echo '<option value="' . esc_attr( (string) $opt_val ) . '"' . selected( $value, (string) $opt_val, false ) . '>' . esc_html( $opt_label ) . '</option>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- selected() returns a fixed attribute.
            }
            echo '</select>';
            break;

        case 'textarea':
            echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" rows="5"' . $aria . '>' . esc_textarea( $value ) . '</textarea>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $aria is built from esc_attr().
            break;

        default: // text, password, number, url
            $input_type = in_array( $type, [ 'password', 'number', 'url' ], true ) ? $type : 'text';
            $extra = '';
            if ( 'password' === $type ) $extra .= ' autocomplete="off" spellcheck="false"';
            foreach ( [ 'min', 'max', 'step' ] as $attr ) {
                if ( isset( $field[ $attr ] ) ) $extra .= ' ' . $attr . '="' . esc_attr( (string) $field[ $attr ] ) . '"';
            }
            echo '<input type="' . esc_attr( $input_type ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '"' . $extra . $aria . '>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $extra and $aria are built from esc_attr().
    }
    echo '</div></div>';
}

/**
 * Print the fields of one tab as cards, one card per section.
 */
function ce_theme_opts_render_cards( string $tab ): void {
    $fields = ce_get_tab_fields( $tab );
    if ( ! $fields ) return;
    $open      = false;
    $is_colors = 'colors' === $tab;
    foreach ( $fields as $field ) {
        if ( ! $open || ( ! empty( $field['section'] ) ) ) {
            if ( $open ) echo '</div></div>';
            echo '<div class="ce-theme-opts-card' . ( $is_colors ? ' ce-theme-opts-card--colors' : '' ) . '">';
            if ( ! empty( $field['section'] ) ) {
                echo '<h2 class="ce-theme-opts-card__title">' . esc_html( $field['section'] ) . '</h2>';
            }
            echo '<div class="ce-theme-opts-card__body">';
            $open = true;
        }
        ce_theme_opts_render_field( $tab, $field );
    }
    if ( $open ) echo '</div></div>';
}


/* ═══════════════════════════════════════════════════════════════════════
   ADMIN PAGE
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Placeholder for the heavy part of a tab that is not the one opened: the
 * scan tools, image status and article diagnostics run their queries only
 * when their own tab loads (…&tab=<key>).
 *
 * @since 2.15.0
 */
function ce_theme_opts_load_note( string $key ): void {
    $labels = [
        'media'    => __( 'Load image status and tools', 'compelling-evidence' ),
        'security' => __( 'Load the security tools and reports', 'compelling-evidence' ),
        'tools'    => __( 'Load content sync and diagnostics', 'compelling-evidence' ),
    ];
    echo '<div class="ce-theme-opts-card"><div class="ce-theme-opts-card__body ce-theme-opts-card__body--prose"><p>';
    esc_html_e( 'This part reads the database, so it loads only when this tab is opened on its own.', 'compelling-evidence' );
    echo '</p><p><a class="button" href="' . esc_url( admin_url( 'themes.php?page=ce-theme-options&tab=' . $key ) ) . '">' . esc_html( $labels[ $key ] ?? __( 'Load this tab', 'compelling-evidence' ) ) . '</a></p></div></div>';
}

function ce_options_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $tabs   = ce_theme_opts_tabs();
    $active = sanitize_key( wp_unslash( $_GET['tab'] ?? 'general' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- tab selection only.
    if ( ! isset( $tabs[ $active ] ) ) $active = 'general';

    // Handle save. Each tab posts to its own URL (…&tab=<key>) with its own nonce.
    $saved        = false;
    $sync_result  = null;
    $media_result = null;

    if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['ce_options_tab'] ) ) {
        check_admin_referer( 'ce_options_' . $active );
        if ( $active === 'tools' && isset( $_POST['ce_manual_sync'] ) ) {
            $sync_result = ce_manual_content_sync();
        } elseif ( $active === 'media' && isset( $_POST['ce_media_auto_action'] ) ) {
            // Handled by ce_media_auto_panel_html() below; settings are left as they are.
        } elseif ( $active === 'media' && isset( $_POST['ce_media_import_now'] ) && function_exists( 'ce_media_import_pending' ) ) {
            if ( function_exists( 'set_time_limit' ) ) {
                @set_time_limit( 300 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- may be disabled on the host.
            }
            $media_result = ce_media_import_pending( 20 );
            if ( $media_result['failed'] ) {
                update_option( 'ce_media_import_errors', $media_result['failed'], false );
            } else {
                delete_option( 'ce_media_import_errors' );
            }
        } elseif ( 'tools' !== $active ) {
            ce_theme_opts_save_tab( $active );
            $saved = true;
        }
    }

    $theme   = wp_get_theme();
    $repo    = (string) $theme->get( 'ThemeURI' );
    ?>
    <div class="wrap ce-theme-opts">
        <header class="ce-theme-opts__header">
            <img class="ce-theme-opts__logo" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/ce-icon.svg' ); ?>" alt="" width="40" height="40">
            <div class="ce-theme-opts__intro">
                <h1 class="ce-theme-opts__title"><?php esc_html_e( 'CE Theme Options', 'compelling-evidence' ); ?></h1>
                <p class="ce-theme-opts__lede"><?php esc_html_e( 'Site-wide settings for Compelling Evidence. Each tab saves on its own.', 'compelling-evidence' ); ?></p>
            </div>
            <span class="ce-theme-opts__badge">
                <?php
                /* translators: %s: theme version. */
                echo esc_html( sprintf( __( 'Version %s', 'compelling-evidence' ), $theme->get( 'Version' ) ) );
                ?>
            </span>
        </header>
        <hr class="wp-header-end">

        <?php if ( $saved ) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'compelling-evidence' ); ?></p></div>
        <?php endif; ?>

        <?php if ( ! empty( $sync_result ) ) : ?>
            <?php if ( $sync_result['success'] ) : ?>
                <div class="notice notice-success is-dismissible">
                    <p><strong><?php esc_html_e( 'Sync finished.', 'compelling-evidence' ); ?></strong></p>
                    <ul class="ce-sync-list">
                        <?php /* translators: %d: number of articles. */ ?>
                        <li><?php echo esc_html( sprintf( __( 'Articles synced: %d', 'compelling-evidence' ), (int) $sync_result['synced'] ) ); ?></li>
                        <?php /* translators: %d: number of topic terms. */ ?>
                        <li><?php echo esc_html( sprintf( __( 'Topics created: %d', 'compelling-evidence' ), (int) $sync_result['topics_created'] ) ); ?></li>
                        <?php /* translators: %d: number of topic terms. */ ?>
                        <li><?php echo esc_html( sprintf( __( 'Topics cleaned: %d', 'compelling-evidence' ), (int) $sync_result['topics_cleaned'] ) ); ?></li>
                        <?php if ( ! empty( $sync_result['duplicates_found'] ) ) : ?>
                            <?php /* translators: %d: number of duplicate articles. */ ?>
                            <li><?php echo esc_html( sprintf( __( 'Duplicates detected and skipped: %d', 'compelling-evidence' ), (int) $sync_result['duplicates_found'] ) ); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php else : ?>
                <div class="notice notice-error is-dismissible">
                    <p><strong><?php esc_html_e( 'Sync failed:', 'compelling-evidence' ); ?></strong> <?php echo esc_html( $sync_result['error'] ); ?></p>
                    <?php if ( ! empty( $sync_result['fallback'] ) ) : ?>
                        <?php /* translators: %d: number of articles. */ ?>
                        <p><em><?php echo esc_html( sprintf( __( 'Fallback mode ran. %d articles synced with the basic method.', 'compelling-evidence' ), (int) $sync_result['fallback_synced'] ) ); ?></em></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <nav class="ce-theme-opts__tabs" role="tablist" aria-label="<?php esc_attr_e( 'Theme option groups', 'compelling-evidence' ); ?>">
            <?php foreach ( $tabs as $key => $tab ) :
                $on = $active === $key; ?>
                <a href="<?php echo esc_url( admin_url( 'themes.php?page=ce-theme-options&tab=' . $key ) ); ?>"
                   class="ce-theme-opts__tab<?php echo $on ? ' is-active' : ''; ?>"
                   role="tab" id="ce-theme-opts-tab-<?php echo esc_attr( $key ); ?>"
                   aria-controls="ce-theme-opts-panel-<?php echo esc_attr( $key ); ?>"
                   aria-selected="<?php echo $on ? 'true' : 'false'; ?>"
                   tabindex="<?php echo $on ? '0' : '-1'; ?>"
                   data-tab="<?php echo esc_attr( $key ); ?>">
                    <span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>" aria-hidden="true"></span>
                    <span class="ce-theme-opts__tab-label"><?php echo esc_html( $tab['label'] ); ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <?php foreach ( $tabs as $key => $tab ) : ?>
            <section class="ce-theme-opts__panel" role="tabpanel" tabindex="0"
                     id="ce-theme-opts-panel-<?php echo esc_attr( $key ); ?>"
                     aria-labelledby="ce-theme-opts-tab-<?php echo esc_attr( $key ); ?>"<?php echo $active === $key ? '' : ' hidden'; ?>>

                <div class="ce-theme-opts-card ce-theme-opts-card--usage">
                    <div class="ce-theme-opts-card__body">
                        <h2 class="ce-theme-opts-card__title"><span class="dashicons dashicons-info-outline" aria-hidden="true"></span> <?php esc_html_e( 'How to use this tab', 'compelling-evidence' ); ?></h2>
                        <p><?php echo esc_html( $tab['usage'] ); ?></p>
                    </div>
                </div>

                <form method="post" action="<?php echo esc_url( admin_url( 'themes.php?page=ce-theme-options&tab=' . $key ) ); ?>" class="ce-theme-opts__form">
                    <?php // A nonce field without an id: twelve forms share this page. ?>
                    <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'ce_options_' . $key ) ); ?>">
                    <?php wp_referer_field(); ?>
                    <input type="hidden" name="ce_options_tab" value="<?php echo esc_attr( $key ); ?>">
                    <?php if ( 'tools' !== $key ) ce_theme_opts_render_cards( $key ); ?>

                    <?php if ( 'media' === $key && $key !== $active ) : ce_theme_opts_load_note( $key ); elseif ( 'media' === $key && function_exists( 'ce_media_status_html' ) ) : ?>
                        <div class="ce-theme-opts-card">
                            <h2 class="ce-theme-opts-card__title"><?php esc_html_e( 'Article images', 'compelling-evidence' ); ?></h2>
                            <div class="ce-theme-opts-card__body ce-theme-opts-card__body--prose">
                                <?php if ( $media_result ) : ?>
                                    <?php /* translators: 1: images imported, 2: images remaining. */ ?>
                                    <div class="notice notice-success inline"><p><?php echo esc_html( sprintf( __( 'Imported %1$d image(s); %2$d remaining.', 'compelling-evidence' ), (int) $media_result['imported'], (int) $media_result['remaining'] ) ); ?></p></div>
                                <?php endif; ?>
                                <?php echo wp_kses_post( ce_media_status_html() ); ?>
                                <p><?php echo wp_kses( __( 'Figures use images registered in <code>inc/articles/media.json</code>, each with its source page and licence. Images from Wikimedia Commons, Pexels and Flickr are supported; the credit line is printed under every figure.', 'compelling-evidence' ), [ 'code' => [] ] ); ?></p>
                                <p><button type="submit" name="ce_media_import_now" value="1" class="button button-secondary"><?php esc_html_e( 'Import next 20 images now', 'compelling-evidence' ); ?></button></p>
                            </div>
                        </div>
                        <?php if ( function_exists( 'ce_media_auto_panel_html' ) ) { echo ce_media_auto_panel_html(); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- every value is escaped inside the function. ?>
                    <?php endif; ?>

                    <?php if ( 'tools' === $key && $key !== $active ) : ?>
                        <?php ce_theme_opts_load_note( $key ); ?>
                    <?php elseif ( 'tools' === $key ) : ?>
                        <?php
                        $last_sync  = get_option( 'ce_content_sync_last_run', __( 'Never', 'compelling-evidence' ) );
                        $theme_ver  = $theme->get( 'Version' );
                        $sync_key   = 'ce_content_sync_' . str_replace( '.', '_', $theme_ver );
                        $has_synced = get_option( $sync_key ) ? __( 'Yes', 'compelling-evidence' ) : __( 'No (sync pending)', 'compelling-evidence' );
                        ?>
                        <div class="ce-theme-opts-card">
                            <h2 class="ce-theme-opts-card__title"><?php esc_html_e( 'Manual content synchronisation', 'compelling-evidence' ); ?></h2>
                            <div class="ce-theme-opts-card__body ce-theme-opts-card__body--prose">
                                <p><?php esc_html_e( 'Copies the articles from the JSON batch files into the database, creates missing topic terms and removes retired categories.', 'compelling-evidence' ); ?></p>
                                <dl class="ce-theme-opts-facts">
                                    <dt><?php esc_html_e( 'Theme version', 'compelling-evidence' ); ?></dt><dd><?php echo esc_html( $theme_ver ); ?></dd>
                                    <dt><?php esc_html_e( 'Synced for this version', 'compelling-evidence' ); ?></dt><dd><?php echo esc_html( $has_synced ); ?></dd>
                                    <dt><?php esc_html_e( 'Last manual sync', 'compelling-evidence' ); ?></dt><dd><?php echo esc_html( (string) $last_sync ); ?></dd>
                                </dl>
                                <div class="ce-theme-opts-actions">
                                    <button type="submit" name="ce_manual_sync" value="1" class="button button-primary ce-theme-opts-button" data-ce-confirm="<?php esc_attr_e( 'This syncs every article from the batch files. Continue?', 'compelling-evidence' ); ?>">
                                        <span class="dashicons dashicons-update" aria-hidden="true"></span>
                                        <?php esc_html_e( 'Run content sync now', 'compelling-evidence' ); ?>
                                    </button>
                                    <span class="ce-theme-opts-switch ce-theme-opts-switch--inline">
                                        <input type="checkbox" role="switch" class="ce-theme-opts-switch__input" id="ce-opt-tools-sync-force" name="ce_sync_force" value="1">
                                        <span class="ce-theme-opts-switch__track" aria-hidden="true"><span class="ce-theme-opts-switch__thumb"></span></span>
                                        <label for="ce-opt-tools-sync-force"><?php esc_html_e( 'Force re-sync (ignore the version check)', 'compelling-evidence' ); ?></label>
                                    </span>
                                </div>
                                <h3><?php esc_html_e( 'Safeguards', 'compelling-evidence' ); ?></h3>
                                <ul class="ce-theme-opts-list">
                                    <li><?php esc_html_e( 'Articles with a duplicate slug are skipped.', 'compelling-evidence' ); ?></li>
                                    <li><?php esc_html_e( 'If the JSON files fail to load, a basic sync runs instead.', 'compelling-evidence' ); ?></li>
                                    <li><?php esc_html_e( 'The memory limit rises to 256M for the sync.', 'compelling-evidence' ); ?></li>
                                    <li><?php esc_html_e( 'A 5-minute time limit stops a sync from hanging half-done.', 'compelling-evidence' ); ?></li>
                                </ul>
                            </div>
                        </div>

                        <div class="ce-theme-opts-card">
                            <h2 class="ce-theme-opts-card__title"><?php esc_html_e( 'Article data diagnostics', 'compelling-evidence' ); ?></h2>
                            <div class="ce-theme-opts-card__body ce-theme-opts-card__body--prose">
                                <p><?php esc_html_e( 'Checks the manifest, the batch file checksums and the loader. Run it when articles look empty or the sync does not fire.', 'compelling-evidence' ); ?></p>
                                <?php ce_render_article_diagnostics(); ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <div class="ce-theme-opts-savebar">
                            <button type="submit" class="button button-primary ce-theme-opts-button"><?php esc_html_e( 'Save changes', 'compelling-evidence' ); ?></button>
                        </div>
                    <?php endif; ?>
                </form>

                <?php if ( 'tools' === $key && $key === $active && function_exists( 'ce_render_error_pages_section' ) ) : ?>
                    <div class="ce-theme-opts-card">
                        <div class="ce-theme-opts-card__body ce-theme-opts-card__body--prose">
                            <?php ce_render_error_pages_section(); ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ( 'security' === $key && $key !== $active ) : ?>
                    <?php ce_theme_opts_load_note( $key ); ?>
                <?php elseif ( 'security' === $key && function_exists( 'ce_sec_render_tools' ) ) : ?>
                    <div class="ce-theme-opts-card">
                        <div class="ce-theme-opts-card__body ce-theme-opts-card__body--prose">
                            <?php ce_sec_render_tools(); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>

        <footer class="ce-theme-opts__footer">
            <?php if ( $repo ) : ?>
                <a href="<?php echo esc_url( trailingslashit( $repo ) . 'blob/main/readme.txt' ); ?>"><span class="dashicons dashicons-book" aria-hidden="true"></span> <?php esc_html_e( 'Documentation', 'compelling-evidence' ); ?></a>
                <a href="<?php echo esc_url( trailingslashit( $repo ) . 'issues' ); ?>"><span class="dashicons dashicons-sos" aria-hidden="true"></span> <?php esc_html_e( 'Support and bug reports', 'compelling-evidence' ); ?></a>
            <?php endif; ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=ce-analytics' ) ); ?>"><span class="dashicons dashicons-chart-area" aria-hidden="true"></span> <?php esc_html_e( 'CE Analytics report', 'compelling-evidence' ); ?></a>
        </footer>
    </div>
    <?php
}


/* ═══════════════════════════════════════════════════════════════════════
   FIELD DEFINITIONS
   ═══════════════════════════════════════════════════════════════════════ */

function ce_get_tab_fields( $tab ) {
    $fields = [
        'general' => [
            [ 'id' => 'ce_hero_title',             'label' => __( 'Hero Title', 'compelling-evidence' ),               'type' => 'text',     'default' => 'What if the evidence pointed somewhere you didn\'t expect?', 'section' => __( 'Hero Section', 'compelling-evidence' ) ],
            [ 'id' => 'ce_hero_subtitle',           'label' => __( 'Hero Subtitle', 'compelling-evidence' ),            'type' => 'text',     'default' => 'Challenging what you think you know — with evidence, not assertion.', 'desc' => __( 'Appears below the Arabic verse on the homepage.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_hero_verse_arabic',       'label' => __( 'Arabic Verse', 'compelling-evidence' ),             'type' => 'text',     'default' => 'فَبِأَيِّ آلَاءِ رَبِّكُمَا تُكَذِّبَانِ' ],
            [ 'id' => 'ce_hero_verse_number',       'label' => __( 'Verse Number (Arabic)', 'compelling-evidence' ),     'type' => 'text',     'default' => '١٣', 'desc' => __( 'Ayah number after the verse, in Arabic-Indic digits (١٣); the Quran face draws it inside the ayah medallion, so no brackets.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_hero_verse_translation',  'label' => __( 'Verse Translation', 'compelling-evidence' ),        'type' => 'text',     'default' => 'Then which of the favours of your Lord will you deny?' ],
            [ 'id' => 'ce_hero_verse_ref',          'label' => __( 'Verse Reference', 'compelling-evidence' ),          'type' => 'text',     'default' => 'Surah Ar-Rahman (55:13)' ],
            [ 'id' => 'ce_footer_text',             'label' => __( 'Footer Text', 'compelling-evidence' ),              'type' => 'text',     'default' => '© Compelling Evidence. All rights reserved.', 'section' => __( 'Footer', 'compelling-evidence' ) ],
        ],

        'identity' => [
            // ── Author (Person schema) ─────────────────────────────────────
            [ 'id' => 'ce_author_name',     'label' => __( 'Author Name', 'compelling-evidence' ),     'type' => 'text', 'default' => '', 'section' => __( 'Author', 'compelling-evidence' ), 'desc' => __( 'The visible byline on every article. The structured data names Mohd Elfie Nieshaem Juferi, at https://menj.bio, as the author whatever this field holds.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_author_title',    'label' => __( 'Author Job Title', 'compelling-evidence' ), 'type' => 'text', 'default' => '', 'desc' => __( 'e.g. "Editor", "Writer", "Founder". Used in Person schema (jobTitle).', 'compelling-evidence' ) ],
            [ 'id' => 'ce_author_bio',      'label' => __( 'Author Bio', 'compelling-evidence' ),      'type' => 'textarea', 'default' => '', 'desc' => __( 'Up to 300 characters. Used in Person schema (description) and the author archive page.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_author_url',      'label' => __( 'Author Profile URL', 'compelling-evidence' ), 'type' => 'text', 'default' => '', 'desc' => __( 'Where the byline links to. Defaults to the WordPress author archive if empty. The structured data lists it in sameAs for the author, ahead of https://menj.bio.', 'compelling-evidence' ) ],

            // ── Author social profiles (sameAs schema) ─────────────────────
            [ 'id' => 'ce_author_twitter',  'label' => __( 'Author Twitter / X', 'compelling-evidence' ), 'type' => 'text', 'default' => '', 'section' => __( 'Author Social Profiles', 'compelling-evidence' ), 'desc' => __( 'Full URL, e.g. https://twitter.com/handle', 'compelling-evidence' ) ],
            [ 'id' => 'ce_author_youtube',  'label' => __( 'Author YouTube', 'compelling-evidence' ),  'type' => 'text', 'default' => '', 'desc' => __( 'Full URL to channel.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_author_github',   'label' => __( 'Author GitHub', 'compelling-evidence' ),   'type' => 'text', 'default' => '', 'desc' => __( 'Full URL to profile.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_author_linkedin', 'label' => __( 'Author LinkedIn', 'compelling-evidence' ), 'type' => 'text', 'default' => '', 'desc' => __( 'Full URL to profile.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_author_other_urls', 'label' => __( 'Other Author URLs', 'compelling-evidence' ), 'type' => 'textarea', 'default' => '', 'desc' => __( 'One URL per line. Wikipedia entry, Scholar profile, ORCID, etc. All added to the Person schema sameAs array.', 'compelling-evidence' ) ],

            // ── Site-level social and contact (Organization schema) ────────
            [ 'id' => 'ce_site_twitter_handle', 'label' => __( 'Site Twitter Handle', 'compelling-evidence' ), 'type' => 'text', 'default' => '', 'section' => __( 'Site Social & Contact', 'compelling-evidence' ), 'desc' => __( 'Handle only, including the @, e.g. @CompellingEv. Used for twitter:site meta tag.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_site_youtube_url',    'label' => __( 'Site YouTube Channel', 'compelling-evidence' ), 'type' => 'text', 'default' => '', 'desc' => __( 'Full URL.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_site_facebook_url',   'label' => __( 'Site Facebook Page', 'compelling-evidence' ), 'type' => 'text', 'default' => '' ],
            [ 'id' => 'ce_social_footer',    'label' => __( 'Show social icons in the footer', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '1', 'section' => __( 'Social icons', 'compelling-evidence' ), 'desc' => __( 'One icon for each profile with a URL: the X handle, Facebook and YouTube above and the fields below. With none filled in, nothing prints.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_social_instagram', 'label' => __( 'Instagram', 'compelling-evidence' ), 'type' => 'url', 'default' => '', 'desc' => __( 'Full URL, e.g. https://www.instagram.com/name/', 'compelling-evidence' ) ],
            [ 'id' => 'ce_social_tiktok',    'label' => __( 'TikTok', 'compelling-evidence' ),    'type' => 'url', 'default' => '' ],
            [ 'id' => 'ce_social_threads',   'label' => __( 'Threads', 'compelling-evidence' ),   'type' => 'url', 'default' => '' ],
            [ 'id' => 'ce_social_bluesky',   'label' => __( 'Bluesky', 'compelling-evidence' ),   'type' => 'url', 'default' => '' ],
            [ 'id' => 'ce_social_mastodon',  'label' => __( 'Mastodon', 'compelling-evidence' ),  'type' => 'url', 'default' => '', 'desc' => __( 'The link carries rel="me", so Mastodon can verify the site.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_social_telegram',  'label' => __( 'Telegram', 'compelling-evidence' ),  'type' => 'url', 'default' => '' ],
            [ 'id' => 'ce_social_whatsapp',  'label' => __( 'WhatsApp channel', 'compelling-evidence' ), 'type' => 'url', 'default' => '' ],
            [ 'id' => 'ce_site_contact_email',  'label' => __( 'Site Contact Email', 'compelling-evidence' ), 'type' => 'text', 'default' => '', 'desc' => __( 'Used in Organization schema contactPoint.', 'compelling-evidence' ) ],

            // ── Logo (Organization schema, ImageObject) ────────────────────
            [ 'id' => 'ce_org_logo_url',    'label' => __( 'Logo URL', 'compelling-evidence' ),        'type' => 'text',   'default' => '', 'section' => __( 'Logo (Organization Schema)', 'compelling-evidence' ), 'desc' => __( 'Direct URL to logo image. Recommended 1:1 aspect ratio, ≥ 112×112 px for Google rich-result eligibility.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_org_logo_width',  'label' => __( 'Logo Width (px)', 'compelling-evidence' ), 'type' => 'number', 'default' => '512', 'min' => 64, 'max' => 4096 ],
            [ 'id' => 'ce_org_logo_height', 'label' => __( 'Logo Height (px)', 'compelling-evidence' ), 'type' => 'number', 'default' => '512', 'min' => 64, 'max' => 4096 ],
        ],

        'colors' => [
            [ 'id' => 'ce_color_accent',      'label' => __( 'Primary Accent', 'compelling-evidence' ),     'type' => 'color', 'default' => '#e8455a', 'desc' => __( 'Buttons, links, category labels.', 'compelling-evidence' ), 'section' => __( 'Brand Colors', 'compelling-evidence' ) ],
            [ 'id' => 'ce_color_accent2',     'label' => __( 'Secondary Accent', 'compelling-evidence' ),   'type' => 'color', 'default' => '#ff6b35', 'desc' => __( 'Gradient endpoints, hover states.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_color_teal',        'label' => __( 'Teal', 'compelling-evidence' ),               'type' => 'color', 'default' => '#0cd4e0', 'desc' => __( 'Quiz CTAs, journey navigation, search highlights.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_color_purple',      'label' => __( 'Purple', 'compelling-evidence' ),             'type' => 'color', 'default' => '#6b2fa0', 'desc' => __( 'Article cards, Quran citation backgrounds.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_color_gold',        'label' => __( 'Gold', 'compelling-evidence' ),               'type' => 'color', 'default' => '#f5c518', 'desc' => __( 'Result CTA, premium elements.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_color_bg_deep',     'label' => __( 'Background Deep', 'compelling-evidence' ),    'type' => 'color', 'default' => '#1a0a2e', 'desc' => __( 'Primary background tone.', 'compelling-evidence' ), 'section' => __( 'Background', 'compelling-evidence' ) ],
            [ 'id' => 'ce_color_bg_darkest',  'label' => __( 'Background Darkest', 'compelling-evidence' ), 'type' => 'color', 'default' => '#0d0820', 'desc' => __( 'Deepest background (hero, nav).', 'compelling-evidence' ) ],
        ],

        'typography' => [
            [ 'id' => 'ce_heading_font', 'label' => __( 'Heading Font', 'compelling-evidence' ), 'type' => 'choice', 'default' => 'playfair', 'section' => __( 'Font Families', 'compelling-evidence' ),
              'options' => ce_font_options( 'heading' ),
              'desc' => __( 'Titles, section headings and display numbers across the site.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_reading_font', 'label' => __( 'Reading Body Font', 'compelling-evidence' ), 'type' => 'choice', 'default' => 'cormorant',
              'options' => ce_font_options( 'reading' ),
              'desc' => __( 'Article text, excerpts and quotations. EB Garamond has the closest x-height to Cormorant, so switching between the two keeps line lengths stable.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_accent_font',  'label' => __( 'Label & Eyebrow Font', 'compelling-evidence' ), 'type' => 'choice', 'default' => 'dm-sans',
              'options' => ce_font_options( 'accent' ),
              'desc' => __( 'Uppercase section labels, topic tags and page eyebrows. Special Elite gives a typewriter case-file look; it covers English text only, so it is never applied to transliterated Arabic.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_font_scale',   'label' => __( 'Font Size Scale', 'compelling-evidence' ), 'type' => 'choice', 'default' => '1.0', 'section' => __( 'Sizing', 'compelling-evidence' ),
              'options' => [ '0.9' => __( 'Compact (90%)', 'compelling-evidence' ), '1.0' => __( 'Default (100%)', 'compelling-evidence' ), '1.1' => __( 'Large (110%)', 'compelling-evidence' ), '1.2' => __( 'Extra Large (120%)', 'compelling-evidence' ) ],
              'desc' => __( 'Scales all body text proportionally. Headings and UI elements remain fixed.', 'compelling-evidence' ) ],
        ],

        'quiz' => [
            [ 'id' => 'ce_quiz_enabled',         'label' => __( 'Enable persona quiz', 'compelling-evidence' ),           'type' => 'checkbox', 'default' => '1', 'section' => __( 'Quiz', 'compelling-evidence' ), 'desc' => __( 'When disabled, /quiz/ redirects to /journeys/ overview.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_quiz_max_selections',   'label' => __( 'Max selections per question', 'compelling-evidence' ),   'type' => 'choice',   'default' => '3',
              'options' => [ '1' => __( '1 (single select)', 'compelling-evidence' ), '2' => __( '2', 'compelling-evidence' ), '3' => __( '3 (default)', 'compelling-evidence' ) ] ],
            [ 'id' => 'ce_show_journey_progress', 'label' => __( 'Show journey progress badges', 'compelling-evidence' ),  'type' => 'checkbox', 'default' => '1', 'section' => __( 'Journeys', 'compelling-evidence' ), 'desc' => __( 'Display started/completed badges on the journeys overview page.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_journeys_per_row',      'label' => __( 'Journey cards per row', 'compelling-evidence' ),         'type' => 'choice',   'default' => '3',
              'options' => [ '2' => __( '2 cards', 'compelling-evidence' ), '3' => __( '3 cards (default)', 'compelling-evidence' ), '4' => __( '4 cards', 'compelling-evidence' ) ] ],
        ],

        'engagement' => [
            [ 'id' => 'ce_voting_enabled',     'label' => __( 'Enable article voting (up/down)', 'compelling-evidence' ),     'type' => 'checkbox', 'default' => '1', 'section' => __( 'Features', 'compelling-evidence' ) ],
            [ 'id' => 'ce_resonance_enabled',  'label' => __( 'Enable resonance feedback', 'compelling-evidence' ),           'type' => 'checkbox', 'default' => '1', 'desc' => __( '"Addressed my question" / "Still have questions" / "Want to discuss" buttons.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_crosslinks_enabled', 'label' => __( 'Enable automatic cross-linking', 'compelling-evidence' ),      'type' => 'checkbox', 'default' => '1', 'desc' => __( 'Auto-links key phrases in articles to related articles (161 phrase mappings).', 'compelling-evidence' ) ],
            [ 'id' => 'ce_search_enabled',     'label' => __( 'Enable homepage search', 'compelling-evidence' ),              'type' => 'checkbox', 'default' => '1' ],
            [ 'id' => 'ce_articles_per_page',  'label' => __( 'Articles per page (archive)', 'compelling-evidence' ),          'type' => 'range',   'default' => '12', 'min' => 4, 'max' => 48, 'section' => __( 'Content', 'compelling-evidence' ) ],
            [ 'id' => 'ce_rpb_enabled',  'label' => __( 'Show the reading progress bar', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '1', 'section' => __( 'Reading progress bar', 'compelling-evidence' ),
              'desc' => __( 'A thin bar that fills as the reader scrolls through the text. Screen readers skip it; articles keep the labelled progress card in the sidebar. It turns itself off while the Reading Progress Bar plugin is active.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_rpb_color',    'label' => __( 'Bar colour', 'compelling-evidence' ), 'type' => 'color', 'default' => '#0cd4e0', 'desc' => __( 'Left at the default, the bar keeps its teal gradient.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_rpb_height',   'label' => __( 'Bar height (px)', 'compelling-evidence' ), 'type' => 'range', 'default' => '3', 'min' => 2, 'max' => 8 ],
            [ 'id' => 'ce_rpb_position', 'label' => __( 'Position', 'compelling-evidence' ), 'type' => 'select', 'default' => 'top',
              'options' => [ 'top' => __( 'Top, under the header', 'compelling-evidence' ), 'bottom' => __( 'Bottom of the window', 'compelling-evidence' ) ] ],
            [ 'id' => 'ce_rpb_on_ce_article',  'label' => __( 'On articles', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '1' ],
            [ 'id' => 'ce_rpb_on_post',        'label' => __( 'On posts', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '1' ],
            [ 'id' => 'ce_rpb_on_page',        'label' => __( 'On pages', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '1', 'desc' => __( 'Pages on the default template only. The quiz, the journeys and other full-screen pages show their own progress.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_rpb_on_ce_question', 'label' => __( 'On reader questions', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '0' ],
        ],

        'analytics' => [
            [ 'id' => 'ce_analytics_enabled',        'label' => __( 'Enable analytics tracking', 'compelling-evidence' ),         'type' => 'checkbox', 'default' => '0', 'section' => __( 'Tracking', 'compelling-evidence' ), 'desc' => __( 'Off until you switch it on. Events go to a table in this database and nowhere else: no cookies, no raw IP addresses, no third party. While it is off the tracker is not loaded and the quiz and the journeys send nothing.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_analytics_track_scroll',    'label' => __( 'Track article scroll depth', 'compelling-evidence' ),        'type' => 'checkbox', 'default' => '1', 'desc' => __( 'Records 25%, 50%, 75%, 100% scroll milestones per article.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_analytics_track_search',    'label' => __( 'Track search queries', 'compelling-evidence' ),              'type' => 'checkbox', 'default' => '1', 'desc' => __( 'Logs search terms (debounced 1.5s) for content gap analysis.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_analytics_retention_days',  'label' => __( 'Data retention (days)', 'compelling-evidence' ),             'type' => 'select',   'default' => '90', 'section' => __( 'Data Management', 'compelling-evidence' ),
              'options' => [ '30' => __( '30 days', 'compelling-evidence' ), '60' => __( '60 days', 'compelling-evidence' ), '90' => __( '90 days (default)', 'compelling-evidence' ), '180' => __( '6 months', 'compelling-evidence' ), '365' => __( '1 year', 'compelling-evidence' ) ],
              'desc' => __( 'Auto-purge analytics data older than this. Purge runs on daily cron.', 'compelling-evidence' ) ],
        ],

        'links' => [
            [ 'id' => 'ce_crosslinks_enabled',     'label' => __( 'Enable automatic internal crosslinks', 'compelling-evidence' ),  'type' => 'checkbox', 'default' => '1', 'section' => __( 'Internal Cross-Links', 'compelling-evidence' ),
              'desc' => __( 'Auto-links key phrases in article and answer text to related articles: the curated list plus this site\'s entries in the shared network map. Obeys all ground rules.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_crosslink_max_per_article', 'label' => __( 'Maximum crosslinks per article', 'compelling-evidence' ), 'type' => 'range', 'default' => '5', 'min' => 1, 'max' => 15,
              'desc' => __( 'Upper limit. Actual count may be lower if fewer phrases match.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_crosslink_min_per_article', 'label' => __( 'Minimum crosslinks per article', 'compelling-evidence' ), 'type' => 'range', 'default' => '3', 'min' => 0, 'max' => 10,
              'desc' => __( 'If the first pass finds fewer than this, a second pass runs with relaxed paragraph rules to reach the minimum. Set to 0 to disable minimum.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_crosslink_style',   'label' => __( 'Crosslink style', 'compelling-evidence' ),  'type' => 'choice', 'default' => 'underline',
              'options' => [ 'underline' => __( 'Underline (default)', 'compelling-evidence' ), 'dotted' => __( 'Dotted underline', 'compelling-evidence' ), 'bold' => __( 'Bold text, no underline', 'compelling-evidence' ), 'subtle' => __( 'Color only, no underline', 'compelling-evidence' ) ] ],
            [ 'id' => 'ce_crosslink_color',   'label' => __( 'Crosslink color', 'compelling-evidence' ),  'type' => 'color',  'default' => '#e8455a', 'desc' => __( 'Text color for internal crosslinks.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_crosslink_nofollow', 'label' => __( 'Add rel="nofollow" to crosslinks', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '0',
              'desc' => __( 'Not recommended — internal links should pass link equity. Only enable if you have a specific SEO reason.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_crosslink_disabled_categories', 'label' => __( 'Disable crosslinks for categories', 'compelling-evidence' ), 'type' => 'text', 'default' => '',
              'desc' => __( 'Comma-separated topic slugs (e.g. "the-bigger-picture,the-inner-journey"). Articles in these categories will have no auto-crosslinks.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_sister_links_enabled', 'label' => __( 'Link to the other sites', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '1', 'section' => __( 'Links to the Other Sites', 'compelling-evidence' ),
              'desc' => __( 'The first mention of a phrase another of the seven sites treats (Abrahamic Religions, Best of Islam, The Injil, Apostle of Doom, Murtadd, Lailatulqadar) links to that inner page. Phrases come from inc/data/network-links.json, the same file in all seven themes. Never in headings, quotations, Quran or hadith blocks, figures or tables; one added link a paragraph; never in a paragraph that already has a crosslink or tooltip.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_sister_links_max', 'label' => __( 'Maximum links to other sites per article', 'compelling-evidence' ), 'type' => 'range', 'default' => '3', 'min' => 0, 'max' => 10,
              'desc' => __( 'Counted apart from the internal crosslinks above. 0 turns them off.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_murtadd_counterparts_enabled', 'label' => __( 'Link to murtadd.org counterparts', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '1',
              'desc' => __( 'A "Read this on murtadd.org" card after the article text, listing the murtadd rebuttals and doubts that name this article as their counterpart. Data: inc/data/murtadd-counterparts.json, rebuilt with bin/build-murtadd-counterparts.py.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_contextual_links_extra', 'label' => __( 'Extra phrases', 'compelling-evidence' ), 'type' => 'textarea', 'default' => '',
              'desc' => __( 'One per line: phrase | /articles/slug/ for this site, or phrase | https://injil.help/paul/james/ for another site. These win over the shared map. Other addresses are ignored.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_contextual_links_excluded', 'label' => __( 'Pages without crosslinks', 'compelling-evidence' ), 'type' => 'textarea', 'default' => '',
              'desc' => __( 'Article or question slugs, one per line. These get neither internal nor other-site links; glossary tooltips are unaffected.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_tooltips_enabled',        'label' => __( 'Enable glossary tooltips', 'compelling-evidence' ),              'type' => 'checkbox', 'default' => '1', 'section' => __( 'Glossary Tooltips', 'compelling-evidence' ),
              'desc' => __( 'Auto-links Islamic/Arabic terms and shows definition tooltips on hover. 53 terms defined.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_tooltip_max_per_article',  'label' => __( 'Maximum tooltips per article', 'compelling-evidence' ),          'type' => 'range',   'default' => '8', 'min' => 1, 'max' => 20,
              'desc' => __( 'Upper limit. Only terms that actually appear in the article are linked.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_tooltip_style',   'label' => __( 'Tooltip link style', 'compelling-evidence' ), 'type' => 'choice', 'default' => 'dashed',
              'options' => [ 'dashed' => __( 'Dashed underline (default)', 'compelling-evidence' ), 'dotted' => __( 'Dotted underline', 'compelling-evidence' ), 'solid' => __( 'Solid underline', 'compelling-evidence' ), 'none' => __( 'Color only, cursor: help', 'compelling-evidence' ) ] ],
            [ 'id' => 'ce_tooltip_color',   'label' => __( 'Tooltip link color', 'compelling-evidence' ), 'type' => 'color',  'default' => '#0cd4e0', 'desc' => __( 'Text color for glossary tooltip terms.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_tooltip_show_arabic', 'label' => __( 'Show Arabic script in tooltips', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '1', 'desc' => __( 'Display Arabic transliteration alongside the definition.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_tooltip_trigger',  'label' => __( 'Tooltip trigger', 'compelling-evidence' ),   'type' => 'choice', 'default' => 'hover',
              'options' => [ 'hover' => __( 'Hover (desktop) / Tap (mobile)', 'compelling-evidence' ), 'click' => __( 'Click/tap only', 'compelling-evidence' ) ],
              'desc' => __( 'When set to hover, tooltip appears on mouseover. On mobile, first tap shows tooltip, second tap follows link.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_pretty_search',  'label' => __( 'Pretty search URLs', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '1', 'section' => __( 'Search URLs', 'compelling-evidence' ),
              'desc' => __( 'Redirects /?s=term to /search/term/. Built in; replaces the Pretty Search Permalinks plugin. Search results stay noindexed and disallowed in robots.txt.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_search_base',    'label' => __( 'Search base', 'compelling-evidence' ), 'type' => 'search_base', 'default' => 'search',
              'desc' => __( 'The path segment before the search term. Lowercase letters, digits, hyphens and underscores; a slug the theme already uses, such as articles or topic, falls back to search. Save Settings → Permalinks after changing it.', 'compelling-evidence' ) ],
        ],

        'media' => [
            [ 'id' => 'ce_lightbox_enabled', 'label' => __( 'Open article images and diagrams in a lightbox', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '1', 'section' => __( 'Lightbox', 'compelling-evidence' ),
              'desc' => __( 'Built into the theme (no jQuery, no plugin). Keyboard, swipe and screen-reader accessible. Replaces the Lightbox2 plugin.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_media_auto', 'label' => __( 'Find and publish lead images for new articles automatically', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '0', 'section' => __( 'Automatic images', 'compelling-evidence' ),
              'desc' => __( 'Every hour, articles with no featured image and no figure get a Pexels photograph chosen from their title, imported, credited, set as the featured image and shown after the first paragraph. Published at once; correct any choice below with Replace or Remove.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_pexels_key', 'label' => __( 'Pexels API key', 'compelling-evidence' ), 'type' => 'password', 'default' => '',
              'desc' => __( 'From pexels.com/api. Stored in the database only, never in the theme files.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_media_import', 'label' => __( 'Import article images into the Media Library', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '1', 'section' => __( 'Image import', 'compelling-evidence' ),
              'desc' => __( 'Copies each registered image into the Media Library, a few per admin page load, so pages serve local, responsive images. Until an image is imported, figures load it from its source.', 'compelling-evidence' ) ],
        ],

        'performance' => [
            [ 'id' => 'ce_parallax_enabled', 'label' => __( 'Enable homepage parallax effect', 'compelling-evidence' ),  'type' => 'checkbox', 'default' => '1', 'section' => __( 'Visual Effects', 'compelling-evidence' ), 'desc' => __( 'Dot grid, glow orb, and Arabic verse parallax layers. Automatically disabled on mobile and prefers-reduced-motion.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_preload_fonts',    'label' => __( 'Preload critical fonts', 'compelling-evidence' ),           'type' => 'checkbox', 'default' => '1', 'desc' => __( 'Adds <link rel="preload"> for the selected heading font and DM Sans 400 to eliminate flash of invisible text.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_minify_inline',    'label' => __( 'Minify inline script output', 'compelling-evidence' ),      'type' => 'checkbox', 'default' => '0', 'desc' => __( 'Strips whitespace from wp_localize_script output. Minor savings.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_gone_enabled',     'label' => __( 'Answer retired URLs with 410 Gone', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '1', 'section' => __( 'Retired URLs', 'compelling-evidence' ), 'desc' => __( 'Returns a lightweight 410 response, before the main query runs, for paths that never belonged to this site (left behind by a past spam injection). Crawlers drop 410 URLs faster than 404 URLs.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_gone_paths',       'label' => __( 'Retired path prefixes', 'compelling-evidence' ),            'type' => 'textarea', 'default' => ce_gone_default_paths(), 'desc' => __( 'One path prefix per line, starting with a slash. Any request whose path begins with a listed prefix receives 410. Never list a prefix used by real content.', 'compelling-evidence' ) ],
        ],

        'security' => [
            [ 'id' => 'ce_sec_xmlrpc_off',        'label' => __( 'Turn off XML-RPC', 'compelling-evidence' ),                                  'type' => 'checkbox', 'default' => '1', 'section' => __( 'Remote publishing', 'compelling-evidence' ), 'desc' => __( 'Spam tools publish from outside the site with a stored password; these switches close the doors they use, and writing in the editor is not affected. xmlrpc.php answers 403, counted per day, and the pingback header goes. On Apache the theme also blocks xmlrpc.php in .htaccess, and removes that block when this is switched off. The Jetpack plugin and old mobile apps need XML-RPC.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_sec_app_passwords_off', 'label' => __( 'Turn off application passwords', 'compelling-evidence' ),                    'type' => 'checkbox', 'default' => '1', 'desc' => __( 'Stored application passwords stop working at once. Revoke them as well, below, so they cannot return if this is switched off.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_sec_rest_lock',         'label' => __( 'Allow REST API changes only from a logged-in browser', 'compelling-evidence' ), 'type' => 'checkbox', 'default' => '1', 'desc' => __( 'A logged-in request that creates, changes or deletes content must come from a browser session on this site with a valid nonce. Basic authentication and passwords sent in a header are refused. Reading through the API, the block editor, Rank Math, contact forms and the quiz work as before, and visitors who are not logged in are not affected.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_sec_spam_gate',         'label' => __( 'Hold suspected gambling spam as Pending', 'compelling-evidence' ),           'type' => 'checkbox', 'default' => '1', 'section' => __( 'New content', 'compelling-evidence' ), 'desc' => __( 'A post, page, article or question going public whose title, address or text reads as casino or betting spam is saved as Pending and logged below. So is anything published from outside the editor. The articles and pages the theme seeds are exempt, and questions sent through Ask a Question arrive as Pending as before.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_sec_gone',              'label' => __( 'Answer 410 Gone on the address of removed spam', 'compelling-evidence' ),    'type' => 'checkbox', 'default' => '1', 'desc' => __( 'Search engines drop a 410 address faster than a 404 one. Only addresses of spam moved to the trash from this tab are affected. Retired URL prefixes stay under Performance.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_login_design',          'label' => __( 'Dress the login screen in the site design', 'compelling-evidence' ),          'type' => 'checkbox', 'default' => '1', 'section' => __( 'Login screen', 'compelling-evidence' ), 'desc' => __( 'The CE mark, the wordmark, the night palette and the Colors and Typography choices on wp-login.php, the password and registration screens included. A plugin that styles the login screen, such as LoginPress, takes over while it is active. WPS Hide Login keeps working: it moves the address and the design follows.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_login_logo_url',        'label' => __( 'Login logo URL', 'compelling-evidence' ),                                     'type' => 'url',      'default' => '', 'desc' => __( 'Optional. A square image shown above the form at 64px. Empty uses the custom logo, then the CE mark.', 'compelling-evidence' ) ],
            [ 'id' => 'ce_login_message',         'label' => __( 'Line above the login form', 'compelling-evidence' ),                         'type' => 'text',     'default' => '', 'desc' => __( 'Optional plain text, for example who may sign in. Shown on the login screen only, not on the password screens or the session-expired box.', 'compelling-evidence' ) ],
        ],

        'tools' => [
            [ 'id' => 'ce_sync_info', 'label' => __( 'Content Sync', 'compelling-evidence' ), 'type' => 'info', 'section' => __( 'Manual Article Sync', 'compelling-evidence' ) ],
        ],
    ];

    return $fields[ $tab ] ?? [];
}


/* ═══════════════════════════════════════════════════════════════════════
   AUTO-PURGE CRON — delete old analytics data
   ═══════════════════════════════════════════════════════════════════════ */

function ce_analytics_cron_purge() {
    // Runs whether or not tracking is on, so rows left from an earlier
    // opt-in still leave on schedule.
    global $wpdb;
    $table = $wpdb->prefix . 'ce_analytics';
    $days  = absint( get_option( 'ce_analytics_retention_days', 90 ) );
    $before = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );
    $wpdb->query( $wpdb->prepare( "DELETE FROM %i WHERE created_at < %s", $table, $before ) );
}
add_action( 'ce_daily_cleanup', 'ce_analytics_cron_purge' );

// Schedule cron if not already scheduled (wrapped in function for proper hook timing)
function ce_schedule_analytics_cron() {
    if ( ! wp_next_scheduled( 'ce_daily_cleanup' ) ) {
        wp_schedule_event( time(), 'daily', 'ce_daily_cleanup' );
    }
}
add_action( 'init', 'ce_schedule_analytics_cron' );


/* ═══════════════════════════════════════════════════════════════════════
   INJECT CUSTOM CSS VARIABLES
   Overrides :root values in main.css based on Theme Options settings.
   ═══════════════════════════════════════════════════════════════════════ */

function ce_options_inject_css() {
    $defaults = [
        'ce_color_accent'     => '#e8455a',
        'ce_color_accent2'    => '#ff6b35',
        'ce_color_teal'       => '#0cd4e0',
        'ce_color_purple'     => '#6b2fa0',
        'ce_color_gold'       => '#f5c518',
        'ce_color_bg_deep'    => '#1a0a2e',
        'ce_color_bg_darkest' => '#0d0820',
    ];

    $changed = false;
    $vars = [];
    foreach ( $defaults as $option => $default ) {
        $val = get_option( $option, $default );
        if ( $val !== $default ) {
            $changed = true;
            $key = str_replace( 'ce_color_', '', $option );
            $map = [
                'accent'     => '--accent',
                'accent2'    => '--accent2',
                'teal'       => '--teal',
                'purple'     => '--purple',
                'gold'       => '--gold',
                'bg_deep'    => '--deep',
                'bg_darkest' => '--bg-deepest',
            ];
            if ( isset( $map[ $key ] ) ) {
                $vars[] = $map[ $key ] . ': ' . sanitize_hex_color( $val ) . ';';
            }
        }
    }

    // Font scale
    $scale = get_option( 'ce_font_scale', '1.0' );
    if ( $scale !== '1.0' ) {
        $changed = true;
        $vars[] = '--font-scale: ' . floatval( $scale ) . ';';
    }

    $extra_css = '';

    // ── Crosslink styles ──
    $cl_color = get_option( 'ce_crosslink_color', '#e8455a' );
    $cl_style = get_option( 'ce_crosslink_style', 'underline' );
    $cl_rules = 'color: ' . sanitize_hex_color( $cl_color ) . ';';
    switch ( $cl_style ) {
        case 'underline': $cl_rules .= ' text-decoration: underline; text-underline-offset: 2px;'; break;
        case 'dotted':    $cl_rules .= ' text-decoration: underline dotted; text-underline-offset: 2px;'; break;
        case 'bold':      $cl_rules .= ' text-decoration: none; font-weight: 600;'; break;
        case 'subtle':    $cl_rules .= ' text-decoration: none;'; break;
    }
    if ( $cl_color !== '#e8455a' || $cl_style !== 'underline' ) {
        $extra_css .= '.ce-crosslink { ' . $cl_rules . ' } ';
        $extra_css .= '.ce-crosslink:hover { opacity: 0.8; } ';
        $changed = true;
    }

    // ── Tooltip styles ──
    $tt_color = get_option( 'ce_tooltip_color', '#0cd4e0' );
    $tt_style = get_option( 'ce_tooltip_style', 'dashed' );
    $tt_rules = 'color: ' . sanitize_hex_color( $tt_color ) . ';';
    switch ( $tt_style ) {
        case 'dashed': $tt_rules .= ' border-bottom: 1px dashed ' . sanitize_hex_color( $tt_color ) . '; text-decoration: none;'; break;
        case 'dotted': $tt_rules .= ' border-bottom: 1px dotted ' . sanitize_hex_color( $tt_color ) . '; text-decoration: none;'; break;
        case 'solid':  $tt_rules .= ' border-bottom: 1px solid ' . sanitize_hex_color( $tt_color ) . '; text-decoration: none;'; break;
        case 'none':   $tt_rules .= ' border-bottom: none; text-decoration: none; cursor: help;'; break;
    }
    if ( $tt_color !== '#0cd4e0' || $tt_style !== 'dashed' ) {
        $extra_css .= '.ce-glossary-term { ' . $tt_rules . ' } ';
        $extra_css .= '.ce-glossary-term:hover { border-bottom-style: solid; border-bottom-color: ' . sanitize_hex_color( $tt_color ) . '; } ';
        $changed = true;
    }

    if ( $changed ) {
        $root_css = ! empty( $vars ) ? ':root { ' . implode( ' ', $vars ) . ' } ' : '';
        echo '<style id="ce-theme-options">' . $root_css . $extra_css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- colours pass sanitize_hex_color(); other values are whitelisted keywords.
    }
}
add_action( 'wp_head', 'ce_options_inject_css', 5 );


/* ═══════════════════════════════════════════════════════════════════════
   MANUAL CONTENT SYNC — Tools Tab
   ═══════════════════════════════════════════════════════════════════════ */

/**
 * Manual content sync — called when the "Run Content Sync Now" button is clicked.
 *
 * Previously this function maintained its own parallel sync implementation with
 * separate (and incomplete) topic migration maps, no slug-rename handling, and
 * no orphan detection — AND it set the version key, which silently blocked the
 * automatic admin_init sync from ever running.
 *
 * Now it simply:
 *   1. Clears the version key and lock so ce_sync_article_content() runs fresh.
 *   2. Calls ce_sync_article_content() — the single source of sync truth.
 *   3. Returns a result array the UI can report on.
 *
 * @since 2.3.9
 */
function ce_manual_content_sync(): array {
    $result = [
        'success'          => false,
        'synced'           => 0,
        'topics_created'   => 0,
        'topics_cleaned'   => 0,
        'duplicates_found' => 0,
        'error'            => '',
        'fallback'         => false,
        'fallback_synced'  => 0,
    ];

    if ( ! current_user_can( 'manage_options' ) ) {
        $result['error'] = 'Insufficient permissions.';
        return $result;
    }

    // ── Clear state so ce_sync_article_content() runs unconditionally ─────────
    $theme_version = wp_get_theme()->get( 'Version' );
    $version_key   = 'ce_content_sync_' . str_replace( '.', '_', $theme_version );
    delete_option( $version_key );
    delete_transient( 'ce_content_sync_lock' );

    // ── Load the canonical sync function ─────────────────────────────────────
    // ce-content-sync.php is required via functions.php, but require_once is safe.
    $sync_path = get_stylesheet_directory() . '/inc/ce-content-sync.php';
    if ( file_exists( $sync_path ) && ! function_exists( 'ce_sync_article_content' ) ) {
        require_once $sync_path;
    }

    if ( ! function_exists( 'ce_sync_article_content' ) ) {
        $result['error'] = 'ce_sync_article_content() not found — check ce-content-sync.php.';
        return $result;
    }

    // ── Run sync ──────────────────────────────────────────────────────────────
    // force_run = true bypasses the version-key option check entirely,
    // which can silently block sync on hosts with a persistent object cache.
    ce_sync_article_content( true );

    // ── Check outcome ─────────────────────────────────────────────────────────
    $synced_at = get_option( $version_key );
    if ( $synced_at ) {
        $result['success']        = true;
        $result['synced']         = wp_count_posts( 'ce_article' )->publish ?? 0;
        $result['topics_created'] = count( get_terms( [ 'taxonomy' => 'ce_topic', 'hide_empty' => false ] ) );
        $result['topics_cleaned'] = 6; // 4 from v2.3.1 + 2 from v2.3.6
        update_option( 'ce_content_sync_last_run', date( 'Y-m-d H:i:s' ) );
    } else {
        // Sync ran but version key not set — it exited early (loader error, lock, etc.)
        // Check WP debug log for CE Article Loader errors.
        $result['error'] = 'Sync completed but version key was not set — article loading may have failed. Check Theme Options → Tools & Sync diagnostics panel and WP debug log.';
    }

    return $result;
}


/**
 * Render the article data diagnostics panel — Tools tab only.
 *
 * Replaces the standalone inc/articles/diagnose.php file which was publicly
 * accessible via URL and had no authentication or WordPress context.
 * This function runs inside ce_options_page() which already gates on
 * current_user_can('manage_options').
 */
function ce_render_article_diagnostics(): void {

    $articles_dir  = get_stylesheet_directory() . '/inc/articles';
    $manifest_path = $articles_dir . '/manifest.json';
    $loader_path   = get_stylesheet_directory() . '/inc/class-ce-article-loader.php';

    $ok    = '#2ea44f';
    $warn  = '#bf8700';
    $fail  = '#cf222e';
    $muted = '#666';

    $row = static function( string $label, string $value, string $color = '' ) use ( $muted ): void {
        $style = $color ? "color:{$color};font-weight:600;" : "color:{$muted};";
        echo '<div style="display:flex;justify-content:space-between;padding:0.35rem 0;border-bottom:1px solid #f0f0f0;font-size:0.82rem;">';
        echo '<span>' . esc_html( $label ) . '</span>';
        echo '<span style="' . esc_attr( $style ) . '">' . esc_html( $value ) . '</span>';
        echo '</div>';
    };

    echo '<div style="background:#fff;border:1px solid #e0e0e0;border-radius:4px;padding:1rem;">';

    // ── PHP & environment ──────────────────────────────────────────────
    echo '<p style="margin:0 0 0.6rem;font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#888;">Environment</p>';
    $row( 'PHP Version',        PHP_VERSION );
    $row( 'Theme Directory',    get_stylesheet_directory() );
    $row( 'Articles Directory', $articles_dir );
    $row( 'Directory exists',   is_dir( $articles_dir )      ? 'YES' : 'NO',  is_dir( $articles_dir ) ? $ok : $fail );
    $row( 'Directory readable', is_readable( $articles_dir ) ? 'YES' : 'NO',  is_readable( $articles_dir ) ? $ok : $fail );

    echo '<br>';

    // ── Manifest ───────────────────────────────────────────────────────
    echo '<p style="margin:0 0 0.6rem;font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#888;">manifest.json</p>';

    if ( ! file_exists( $manifest_path ) ) {
        $row( 'Manifest', 'NOT FOUND', $fail );
        echo '</div>';
        return;
    }

    $row( 'Manifest exists',   'YES', $ok );
    $row( 'Manifest readable', is_readable( $manifest_path ) ? 'YES' : 'NO', is_readable( $manifest_path ) ? $ok : $fail );

    $raw_manifest = file_get_contents( $manifest_path );

    if ( $raw_manifest === false ) {
        $row( 'Manifest read', 'FAILED', $fail );
        echo '</div>';
        return;
    }

    $manifest = json_decode( $raw_manifest, true );

    if ( json_last_error() !== JSON_ERROR_NONE ) {
        $row( 'Manifest JSON', 'PARSE ERROR: ' . json_last_error_msg(), $fail );
        echo '</div>';
        return;
    }

    $row( 'Manifest JSON',    'Valid', $ok );
    $row( 'Manifest version', $manifest['version'] ?? 'NOT SET' );
    $row( 'Batch count',      isset( $manifest['batches'] ) ? (string) count( $manifest['batches'] ) : '0',
          ( isset( $manifest['batches'] ) && count( $manifest['batches'] ) >= 5 ) ? $ok : $warn );
    $row( 'Total articles',   isset( $manifest['total_articles'] ) ? (string) $manifest['total_articles'] : 'NOT SET' );

    echo '<br>';

    // ── Per-batch checksum verification ───────────────────────────────
    echo '<p style="margin:0 0 0.6rem;font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#888;">Batch File Checksums</p>';

    if ( ! empty( $manifest['batches'] ) ) {
        $all_ok = true;
        foreach ( $manifest['batches'] as $batch ) {
            $batch_path = $articles_dir . '/' . $batch['file'];

            if ( ! file_exists( $batch_path ) ) {
                $row( $batch['file'], 'FILE NOT FOUND', $fail );
                $all_ok = false;
                continue;
            }

            $content = file_get_contents( $batch_path );

            // Mirror the loader's normalisation: strip BOM + normalise CRLF
            if ( substr( $content, 0, 3 ) === "\xEF\xBB\xBF" ) {
                $content = substr( $content, 3 );
            }
            $content = str_replace( "\r\n", "\n", $content );

            $actual   = hash( 'sha256', $content );
            $expected = $batch['checksum'] ?? '';
            $match    = ( $actual === $expected );

            $batch_data     = json_decode( $content, true );
            $article_count  = ( $batch_data && isset( $batch_data['articles'] ) )
                ? count( $batch_data['articles'] ) : 0;

            $label  = $batch['file'] . ' (' . $article_count . ' articles)';
            $status = $match ? 'Checksum OK ✓' : 'CHECKSUM MISMATCH ✗';
            $color  = $match ? $ok : $fail;

            $row( $label, $status, $color );

            if ( ! $match ) {
                $all_ok = false;
                $row( '  └ Expected', substr( $expected, 0, 20 ) . '…' );
                $row( '  └ Actual',   substr( $actual,   0, 20 ) . '…' );
            }
        }

        if ( $all_ok ) {
            echo '<p style="margin:0.6rem 0 0;font-size:0.82rem;color:' . esc_attr( $ok ) . ';font-weight:600;">All checksums verified ✓</p>';
        } else {
            echo '<p style="margin:0.6rem 0 0;font-size:0.82rem;color:' . esc_attr( $fail ) . ';font-weight:600;">Checksum failure — run build-articles.php to regenerate JSON files and manifest.</p>';
        }
    } else {
        $row( 'Batches', 'None found in manifest', $fail );
    }

    echo '<br>';

    // ── Loader test ───────────────────────────────────────────────────
    echo '<p style="margin:0 0 0.6rem;font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#888;">Article Loader</p>';

    $row( 'Loader file exists', file_exists( $loader_path ) ? 'YES' : 'NO', file_exists( $loader_path ) ? $ok : $fail );

    if ( file_exists( $loader_path ) ) {
        if ( ! class_exists( 'CE_Article_Loader' ) ) {
            require_once $loader_path;
        }

        if ( class_exists( 'CE_Article_Loader' ) ) {
            $loader   = new CE_Article_Loader();
            $articles = $loader->load_all_articles();
            $count    = count( $articles );

            $row( 'Articles loaded', (string) $count, $count >= 110 ? $ok : ( $count > 0 ? $warn : $fail ) );

            if ( $loader->has_errors() ) {
                echo '<p style="margin:0.4rem 0 0.2rem;font-size:0.82rem;color:' . esc_attr( $fail ) . ';font-weight:600;">Loader errors:</p>';
                echo '<ul style="margin:0;padding-left:1.2rem;font-size:0.8rem;color:' . esc_attr( $fail ) . ';">';
                foreach ( $loader->get_errors() as $error ) {
                    echo '<li>' . esc_html( $error ) . '</li>';
                }
                echo '</ul>';
            } else {
                $row( 'Loader errors', 'None ✓', $ok );
            }
        } else {
            $row( 'CE_Article_Loader class', 'NOT FOUND after require', $fail );
        }
    }

    echo '<br>';

    // ── Sync key status ───────────────────────────────────────────────
    echo '<p style="margin:0 0 0.6rem;font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;color:#888;">Sync Key</p>';

    $theme_ver  = wp_get_theme()->get( 'Version' );
    $sync_key   = 'ce_content_sync_' . str_replace( '.', '_', $theme_ver );
    $sync_val   = get_option( $sync_key );
    $sync_time  = $sync_val ? date( 'Y-m-d H:i:s', (int) $sync_val ) : null;

    $row( 'Option key',   $sync_key );
    $row( 'Sync run',     $sync_val ? 'Yes — ' . $sync_time : 'Not yet (will run on next admin page load)', $sync_val ? $ok : $warn );

    echo '</div>'; // end white inner box
}
