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
    register_setting( 'ce_options_general', 'ce_hero_verse_ref', [ 'default' => 'Ar-Rahman 55:13' ] );
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

    // ── Analytics ──
    register_setting( 'ce_options_analytics', 'ce_analytics_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_analytics', 'ce_analytics_retention_days', [ 'default' => '90' ] );
    register_setting( 'ce_options_analytics', 'ce_analytics_track_scroll', [ 'default' => '1' ] );
    register_setting( 'ce_options_analytics', 'ce_analytics_track_search', [ 'default' => '1' ] );

    // ── Performance ──
    register_setting( 'ce_options_performance', 'ce_parallax_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_performance', 'ce_preload_fonts', [ 'default' => '1' ] );
    register_setting( 'ce_options_performance', 'ce_minify_inline', [ 'default' => '0' ] );
}
add_action( 'admin_init', 'ce_options_register' );


/* ═══════════════════════════════════════════════════════════════════════
   ADMIN MENU
   ═══════════════════════════════════════════════════════════════════════ */

function ce_options_menu() {
    add_theme_page(
        'CE Theme Options',
        'CE Theme Options',
        'manage_options',
        'ce-theme-options',
        'ce_options_page'
    );
}
add_action( 'admin_menu', 'ce_options_menu' );


/* ═══════════════════════════════════════════════════════════════════════
   ADMIN PAGE — TABBED INTERFACE
   ═══════════════════════════════════════════════════════════════════════ */

function ce_options_page() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $tabs = [
        'general'     => [ 'label' => 'General',           'icon' => 'dashicons-admin-home' ],
        'colors'      => [ 'label' => 'Colors',            'icon' => 'dashicons-art' ],
        'typography'  => [ 'label' => 'Typography',        'icon' => 'dashicons-editor-textcolor' ],
        'links'       => [ 'label' => 'Links & Tooltips',  'icon' => 'dashicons-admin-links' ],
        'quiz'        => [ 'label' => 'Quiz & Journeys',   'icon' => 'dashicons-forms' ],
        'engagement'  => [ 'label' => 'Engagement',        'icon' => 'dashicons-thumbs-up' ],
        'analytics'   => [ 'label' => 'Analytics',         'icon' => 'dashicons-chart-area' ],
        'performance' => [ 'label' => 'Performance',       'icon' => 'dashicons-performance' ],
    ];

    $active = sanitize_key( $_GET['tab'] ?? 'general' );
    if ( ! isset( $tabs[ $active ] ) ) $active = 'general';

    // Handle save
    if ( $_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer( 'ce_options_' . $active ) ) {
        $option_group = 'ce_options_' . $active;
        // WordPress settings API handles the save via options.php,
        // but we use a direct approach for our tabbed interface.
        $fields = ce_get_tab_fields( $active );
        foreach ( $fields as $field ) {
            $key = $field['id'];
            if ( $field['type'] === 'checkbox' ) {
                update_option( $key, isset( $_POST[ $key ] ) ? '1' : '0' );
            } else {
                $value = $_POST[ $key ] ?? '';
                if ( $field['type'] === 'color' ) {
                    $value = sanitize_hex_color( $value ) ?: $field['default'];
                } elseif ( $field['type'] === 'number' ) {
                    $value = absint( $value );
                } else {
                    $value = sanitize_text_field( $value );
                }
                update_option( $key, $value );
            }
        }
        $saved = true;
    }

    ?>
    <div class="wrap ce-options-wrap">
        <h1 class="ce-options-title">
            <span class="ce-options-logo">C<span style="color:#e8455a;">E</span></span>
            Theme Options
        </h1>

        <?php if ( ! empty( $saved ) ) : ?>
            <div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>
        <?php endif; ?>

        <style>
            .ce-options-wrap { max-width: 960px; }
            .ce-options-title { display: flex; align-items: center; gap: 0.6rem; font-size: 1.6rem; font-weight: 700; margin-bottom: 1.2rem; }
            .ce-options-logo { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #1a0a2e; border-radius: 8px; color: #f5f0ff; font-weight: 900; font-size: 0.9rem; letter-spacing: -0.03em; }

            .ce-tabs { display: flex; gap: 0; border-bottom: 2px solid #ddd; margin-bottom: 0; background: #fff; border-radius: 8px 8px 0 0; overflow-x: auto; }
            .ce-tab { display: flex; align-items: center; gap: 0.4rem; padding: 0.85rem 1.1rem; border: none; background: none; color: #666; font-size: 0.82rem; font-weight: 500; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; white-space: nowrap; transition: all 0.15s; text-decoration: none; }
            .ce-tab:hover { color: #1d2327; background: rgba(0,0,0,0.02); }
            .ce-tab.active { color: #2271b1; border-bottom-color: #2271b1; font-weight: 600; }
            .ce-tab .dashicons { font-size: 16px; width: 16px; height: 16px; line-height: 16px; }

            .ce-panel { background: #fff; border: 1px solid #ddd; border-top: none; border-radius: 0 0 8px 8px; padding: 2rem 2.2rem; }

            .ce-field { margin-bottom: 1.6rem; }
            .ce-field:last-child { margin-bottom: 0; }
            .ce-field-label { display: block; font-size: 0.82rem; font-weight: 600; color: #1d2327; margin-bottom: 0.4rem; }
            .ce-field-desc { font-size: 0.78rem; color: #888; margin-top: 0.3rem; line-height: 1.5; }

            .ce-field input[type="text"],
            .ce-field input[type="number"],
            .ce-field textarea,
            .ce-field select { width: 100%; max-width: 480px; padding: 0.55rem 0.75rem; border: 1px solid #ccc; border-radius: 6px; font-size: 0.85rem; transition: border-color 0.15s; }
            .ce-field input:focus, .ce-field textarea:focus, .ce-field select:focus { border-color: #2271b1; outline: none; box-shadow: 0 0 0 1px #2271b1; }
            .ce-field textarea { min-height: 80px; resize: vertical; }

            .ce-field input[type="color"] { width: 50px; height: 36px; padding: 2px; border: 1px solid #ccc; border-radius: 6px; cursor: pointer; }
            .ce-color-row { display: flex; align-items: center; gap: 0.75rem; }
            .ce-color-hex { font-size: 0.78rem; color: #666; font-family: monospace; }

            .ce-toggle { display: flex; align-items: center; gap: 0.6rem; }
            .ce-toggle input[type="checkbox"] { width: 18px; height: 18px; accent-color: #2271b1; }

            .ce-section-title { font-size: 0.92rem; font-weight: 700; color: #1d2327; margin: 0 0 1rem; padding-bottom: 0.6rem; border-bottom: 1px solid #eee; }

            .ce-save-row { margin-top: 1.5rem; padding-top: 1.2rem; border-top: 1px solid #eee; }
            .ce-save-btn { padding: 0.6rem 1.5rem; background: #2271b1; color: #fff; border: none; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: background 0.15s; }
            .ce-save-btn:hover { background: #135e96; }

            @media (max-width: 782px) {
                .ce-tabs { flex-wrap: nowrap; overflow-x: auto; }
                .ce-tab { padding: 0.7rem 0.8rem; font-size: 0.78rem; }
                .ce-panel { padding: 1.2rem 1rem; }
            }
        </style>

        <!-- Tabs -->
        <div class="ce-tabs">
            <?php foreach ( $tabs as $key => $tab ) : ?>
                <a href="<?php echo admin_url( 'themes.php?page=ce-theme-options&tab=' . $key ); ?>"
                   class="ce-tab <?php echo $active === $key ? 'active' : ''; ?>">
                    <span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span>
                    <?php echo esc_html( $tab['label'] ); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Panel -->
        <div class="ce-panel">
            <form method="post">
                <?php
                wp_nonce_field( 'ce_options_' . $active );
                $fields = ce_get_tab_fields( $active );
                $last_section = '';
                foreach ( $fields as $field ) :
                    if ( ! empty( $field['section'] ) && $field['section'] !== $last_section ) :
                        $last_section = $field['section'];
                        ?>
                        <h3 class="ce-section-title"><?php echo esc_html( $field['section'] ); ?></h3>
                    <?php endif; ?>

                    <div class="ce-field">
                        <?php
                        $value = get_option( $field['id'], $field['default'] ?? '' );

                        switch ( $field['type'] ) :
                            case 'text':
                            case 'number': ?>
                                <label class="ce-field-label" for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                                <input type="<?php echo esc_attr( $field['type'] ); ?>"
                                       id="<?php echo esc_attr( $field['id'] ); ?>"
                                       name="<?php echo esc_attr( $field['id'] ); ?>"
                                       value="<?php echo esc_attr( $value ); ?>"
                                       <?php if ( ! empty( $field['min'] ) ) echo 'min="' . $field['min'] . '"'; ?>
                                       <?php if ( ! empty( $field['max'] ) ) echo 'max="' . $field['max'] . '"'; ?>
                                       <?php if ( ! empty( $field['step'] ) ) echo 'step="' . $field['step'] . '"'; ?>>
                                <?php break;

                            case 'textarea': ?>
                                <label class="ce-field-label" for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                                <textarea id="<?php echo esc_attr( $field['id'] ); ?>"
                                          name="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_textarea( $value ); ?></textarea>
                                <?php break;

                            case 'select': ?>
                                <label class="ce-field-label" for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                                <select id="<?php echo esc_attr( $field['id'] ); ?>" name="<?php echo esc_attr( $field['id'] ); ?>">
                                    <?php foreach ( $field['options'] as $opt_val => $opt_label ) : ?>
                                        <option value="<?php echo esc_attr( $opt_val ); ?>" <?php selected( $value, $opt_val ); ?>>
                                            <?php echo esc_html( $opt_label ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php break;

                            case 'color': ?>
                                <label class="ce-field-label"><?php echo esc_html( $field['label'] ); ?></label>
                                <div class="ce-color-row">
                                    <input type="color"
                                           id="<?php echo esc_attr( $field['id'] ); ?>"
                                           name="<?php echo esc_attr( $field['id'] ); ?>"
                                           value="<?php echo esc_attr( $value ); ?>">
                                    <span class="ce-color-hex"><?php echo esc_html( $value ); ?></span>
                                </div>
                                <?php break;

                            case 'checkbox': ?>
                                <div class="ce-toggle">
                                    <input type="checkbox"
                                           id="<?php echo esc_attr( $field['id'] ); ?>"
                                           name="<?php echo esc_attr( $field['id'] ); ?>"
                                           value="1"
                                           <?php checked( $value, '1' ); ?>>
                                    <label for="<?php echo esc_attr( $field['id'] ); ?>" class="ce-field-label" style="margin:0;"><?php echo esc_html( $field['label'] ); ?></label>
                                </div>
                                <?php break;

                            case 'info': ?>
                                <div style="background:#f8f5ff;border:1px solid #ddd;border-left:3px solid #6b2fa0;border-radius:4px;padding:0.8rem 1rem;">
                                    <strong style="font-size:0.82rem;color:#1d2327;"><?php echo esc_html( $field['label'] ); ?></strong>
                                </div>
                                <?php break;
                        endswitch;

                        if ( ! empty( $field['desc'] ) ) : ?>
                            <p class="ce-field-desc"><?php echo esc_html( $field['desc'] ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="ce-save-row">
                    <button type="submit" class="ce-save-btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Live color hex preview
    document.querySelectorAll('input[type="color"]').forEach(function(input) {
        var hex = input.nextElementSibling;
        if (hex && hex.classList.contains('ce-color-hex')) {
            input.addEventListener('input', function() { hex.textContent = input.value; });
        }
    });
    </script>
    <?php
}


/* ═══════════════════════════════════════════════════════════════════════
   FIELD DEFINITIONS
   ═══════════════════════════════════════════════════════════════════════ */

function ce_get_tab_fields( $tab ) {
    $fields = [
        'general' => [
            [ 'id' => 'ce_hero_title',             'label' => 'Hero Title',               'type' => 'text',     'default' => 'What if the evidence pointed somewhere you didn\'t expect?', 'section' => 'Hero Section' ],
            [ 'id' => 'ce_hero_subtitle',           'label' => 'Hero Subtitle',            'type' => 'text',     'default' => 'Challenging what you think you know — with evidence, not assertion.', 'desc' => 'Appears below the Arabic verse on the homepage.' ],
            [ 'id' => 'ce_hero_verse_arabic',       'label' => 'Arabic Verse',             'type' => 'text',     'default' => 'فَبِأَيِّ آلَاءِ رَبِّكُمَا تُكَذِّبَانِ' ],
            [ 'id' => 'ce_hero_verse_number',       'label' => 'Verse Number (Arabic)',     'type' => 'text',     'default' => '١٣', 'desc' => 'Displayed inside ornate brackets ﴿…﴾ after the verse.' ],
            [ 'id' => 'ce_hero_verse_translation',  'label' => 'Verse Translation',        'type' => 'text',     'default' => 'Then which of the favours of your Lord will you deny?' ],
            [ 'id' => 'ce_hero_verse_ref',          'label' => 'Verse Reference',          'type' => 'text',     'default' => 'Ar-Rahman 55:13' ],
            [ 'id' => 'ce_footer_text',             'label' => 'Footer Text',              'type' => 'text',     'default' => '© Compelling Evidence. All rights reserved.', 'section' => 'Footer' ],
        ],

        'colors' => [
            [ 'id' => 'ce_color_accent',      'label' => 'Primary Accent',     'type' => 'color', 'default' => '#e8455a', 'desc' => 'Buttons, links, category labels.', 'section' => 'Brand Colors' ],
            [ 'id' => 'ce_color_accent2',     'label' => 'Secondary Accent',   'type' => 'color', 'default' => '#ff6b35', 'desc' => 'Gradient endpoints, hover states.' ],
            [ 'id' => 'ce_color_teal',        'label' => 'Teal',               'type' => 'color', 'default' => '#0cd4e0', 'desc' => 'Quiz CTAs, journey navigation, search highlights.' ],
            [ 'id' => 'ce_color_purple',      'label' => 'Purple',             'type' => 'color', 'default' => '#6b2fa0', 'desc' => 'Article cards, Quran citation backgrounds.' ],
            [ 'id' => 'ce_color_gold',        'label' => 'Gold',               'type' => 'color', 'default' => '#f5c518', 'desc' => 'Result CTA, premium elements.' ],
            [ 'id' => 'ce_color_bg_deep',     'label' => 'Background Deep',    'type' => 'color', 'default' => '#1a0a2e', 'desc' => 'Primary background tone.', 'section' => 'Background' ],
            [ 'id' => 'ce_color_bg_darkest',  'label' => 'Background Darkest', 'type' => 'color', 'default' => '#0d0820', 'desc' => 'Deepest background (hero, nav).' ],
        ],

        'typography' => [
            [ 'id' => 'ce_heading_font', 'label' => 'Heading Font', 'type' => 'select', 'default' => 'playfair', 'section' => 'Font Families',
              'options' => [ 'playfair' => 'Playfair Display (serif, editorial)', 'cormorant' => 'Cormorant Garamond (serif, elegant)' ] ],
            [ 'id' => 'ce_reading_font', 'label' => 'Reading Body Font', 'type' => 'select', 'default' => 'cormorant',
              'options' => [ 'cormorant' => 'Cormorant Garamond (serif, literary)', 'playfair' => 'Playfair Display (serif, editorial)', 'dm-sans' => 'DM Sans (sans-serif, modern)' ] ],
            [ 'id' => 'ce_font_scale',   'label' => 'Font Size Scale', 'type' => 'select', 'default' => '1.0', 'section' => 'Sizing',
              'options' => [ '0.9' => 'Compact (90%)', '1.0' => 'Default (100%)', '1.1' => 'Large (110%)', '1.2' => 'Extra Large (120%)' ],
              'desc' => 'Scales all body text proportionally. Headings and UI elements remain fixed.' ],
        ],

        'quiz' => [
            [ 'id' => 'ce_quiz_enabled',         'label' => 'Enable persona quiz',           'type' => 'checkbox', 'default' => '1', 'section' => 'Quiz', 'desc' => 'When disabled, /quiz/ redirects to /journeys/ overview.' ],
            [ 'id' => 'ce_quiz_max_selections',   'label' => 'Max selections per question',   'type' => 'select',   'default' => '3',
              'options' => [ '1' => '1 (single select)', '2' => '2', '3' => '3 (default)' ] ],
            [ 'id' => 'ce_show_journey_progress', 'label' => 'Show journey progress badges',  'type' => 'checkbox', 'default' => '1', 'section' => 'Journeys', 'desc' => 'Display started/completed badges on the journeys overview page.' ],
            [ 'id' => 'ce_journeys_per_row',      'label' => 'Journey cards per row',         'type' => 'select',   'default' => '3',
              'options' => [ '2' => '2 cards', '3' => '3 cards (default)', '4' => '4 cards' ] ],
        ],

        'engagement' => [
            [ 'id' => 'ce_voting_enabled',     'label' => 'Enable article voting (up/down)',     'type' => 'checkbox', 'default' => '1', 'section' => 'Features' ],
            [ 'id' => 'ce_resonance_enabled',  'label' => 'Enable resonance feedback',           'type' => 'checkbox', 'default' => '1', 'desc' => '"Addressed my question" / "Still have questions" / "Want to discuss" buttons.' ],
            [ 'id' => 'ce_crosslinks_enabled', 'label' => 'Enable automatic cross-linking',      'type' => 'checkbox', 'default' => '1', 'desc' => 'Auto-links key phrases in articles to related articles (161 phrase mappings).' ],
            [ 'id' => 'ce_search_enabled',     'label' => 'Enable homepage search',              'type' => 'checkbox', 'default' => '1' ],
            [ 'id' => 'ce_articles_per_page',  'label' => 'Articles per page (archive)',          'type' => 'number',   'default' => '12', 'min' => 4, 'max' => 48, 'section' => 'Content' ],
        ],

        'analytics' => [
            [ 'id' => 'ce_analytics_enabled',        'label' => 'Enable analytics tracking',         'type' => 'checkbox', 'default' => '1', 'section' => 'Tracking', 'desc' => 'Privacy-first, server-side only. No cookies, no PII, no third-party.' ],
            [ 'id' => 'ce_analytics_track_scroll',    'label' => 'Track article scroll depth',        'type' => 'checkbox', 'default' => '1', 'desc' => 'Records 25%, 50%, 75%, 100% scroll milestones per article.' ],
            [ 'id' => 'ce_analytics_track_search',    'label' => 'Track search queries',              'type' => 'checkbox', 'default' => '1', 'desc' => 'Logs search terms (debounced 1.5s) for content gap analysis.' ],
            [ 'id' => 'ce_analytics_retention_days',  'label' => 'Data retention (days)',             'type' => 'select',   'default' => '90', 'section' => 'Data Management',
              'options' => [ '30' => '30 days', '60' => '60 days', '90' => '90 days (default)', '180' => '6 months', '365' => '1 year' ],
              'desc' => 'Auto-purge analytics data older than this. Purge runs on daily cron.' ],
        ],

        'links' => [
            [ 'id' => 'ce_crosslinks_enabled',     'label' => 'Enable automatic internal crosslinks',  'type' => 'checkbox', 'default' => '1', 'section' => 'Internal Cross-Links',
              'desc' => 'Auto-links key phrases in article text to related articles (161 phrase mappings). Obeys all ground rules.' ],
            [ 'id' => 'ce_crosslink_max_per_article', 'label' => 'Maximum crosslinks per article', 'type' => 'number', 'default' => '5', 'min' => 1, 'max' => 15,
              'desc' => 'Upper limit. Actual count may be lower if fewer phrases match.' ],
            [ 'id' => 'ce_crosslink_min_per_article', 'label' => 'Minimum crosslinks per article', 'type' => 'number', 'default' => '3', 'min' => 0, 'max' => 10,
              'desc' => 'If the first pass finds fewer than this, a second pass runs with relaxed paragraph rules to reach the minimum. Set to 0 to disable minimum.' ],
            [ 'id' => 'ce_crosslink_style',   'label' => 'Crosslink style',  'type' => 'select', 'default' => 'underline',
              'options' => [ 'underline' => 'Underline (default)', 'dotted' => 'Dotted underline', 'bold' => 'Bold text, no underline', 'subtle' => 'Color only, no underline' ] ],
            [ 'id' => 'ce_crosslink_color',   'label' => 'Crosslink color',  'type' => 'color',  'default' => '#e8455a', 'desc' => 'Text color for internal crosslinks.' ],
            [ 'id' => 'ce_crosslink_nofollow', 'label' => 'Add rel="nofollow" to crosslinks', 'type' => 'checkbox', 'default' => '0',
              'desc' => 'Not recommended — internal links should pass link equity. Only enable if you have a specific SEO reason.' ],
            [ 'id' => 'ce_crosslink_disabled_categories', 'label' => 'Disable crosslinks for categories', 'type' => 'text', 'default' => '',
              'desc' => 'Comma-separated topic slugs (e.g. "the-bigger-picture,the-inner-journey"). Articles in these categories will have no auto-crosslinks.' ],
            [ 'id' => 'ce_tooltips_enabled',        'label' => 'Enable glossary tooltips',              'type' => 'checkbox', 'default' => '1', 'section' => 'Glossary Tooltips',
              'desc' => 'Auto-links Islamic/Arabic terms and shows definition tooltips on hover. 53 terms defined.' ],
            [ 'id' => 'ce_tooltip_max_per_article',  'label' => 'Maximum tooltips per article',          'type' => 'number',   'default' => '8', 'min' => 1, 'max' => 20,
              'desc' => 'Upper limit. Only terms that actually appear in the article are linked.' ],
            [ 'id' => 'ce_tooltip_style',   'label' => 'Tooltip link style', 'type' => 'select', 'default' => 'dashed',
              'options' => [ 'dashed' => 'Dashed underline (default)', 'dotted' => 'Dotted underline', 'solid' => 'Solid underline', 'none' => 'Color only, cursor: help' ] ],
            [ 'id' => 'ce_tooltip_color',   'label' => 'Tooltip link color', 'type' => 'color',  'default' => '#0cd4e0', 'desc' => 'Text color for glossary tooltip terms.' ],
            [ 'id' => 'ce_tooltip_show_arabic', 'label' => 'Show Arabic script in tooltips', 'type' => 'checkbox', 'default' => '1', 'desc' => 'Display Arabic transliteration alongside the definition.' ],
            [ 'id' => 'ce_tooltip_trigger',  'label' => 'Tooltip trigger',   'type' => 'select', 'default' => 'hover',
              'options' => [ 'hover' => 'Hover (desktop) / Tap (mobile)', 'click' => 'Click/tap only' ],
              'desc' => 'When set to hover, tooltip appears on mouseover. On mobile, first tap shows tooltip, second tap follows link.' ],
        ],

        'performance' => [
            [ 'id' => 'ce_parallax_enabled', 'label' => 'Enable homepage parallax effect',  'type' => 'checkbox', 'default' => '1', 'section' => 'Visual Effects', 'desc' => 'Dot grid, glow orb, and Arabic verse parallax layers. Automatically disabled on mobile and prefers-reduced-motion.' ],
            [ 'id' => 'ce_preload_fonts',    'label' => 'Preload critical fonts',           'type' => 'checkbox', 'default' => '1', 'desc' => 'Adds <link rel="preload"> for Playfair Display 900 and DM Sans 400 to eliminate flash of invisible text.' ],
            [ 'id' => 'ce_minify_inline',    'label' => 'Minify inline script output',      'type' => 'checkbox', 'default' => '0', 'desc' => 'Strips whitespace from wp_localize_script output. Minor savings.' ],
        ],
    ];

    return $fields[ $tab ] ?? [];
}


/* ═══════════════════════════════════════════════════════════════════════
   AUTO-PURGE CRON — delete old analytics data
   ═══════════════════════════════════════════════════════════════════════ */

function ce_analytics_cron_purge() {
    if ( get_option( 'ce_analytics_enabled', '1' ) !== '1' ) return;

    global $wpdb;
    $table = $wpdb->prefix . 'ce_analytics';
    $days  = absint( get_option( 'ce_analytics_retention_days', 90 ) );
    $before = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );
    $wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE created_at < %s", $before ) );
}
add_action( 'ce_daily_cleanup', 'ce_analytics_cron_purge' );

// Schedule cron if not already scheduled
if ( ! wp_next_scheduled( 'ce_daily_cleanup' ) ) {
    wp_schedule_event( time(), 'daily', 'ce_daily_cleanup' );
}


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
        echo '<style id="ce-theme-options">' . $root_css . $extra_css . '</style>' . "\n";
    }
}
add_action( 'wp_head', 'ce_options_inject_css', 5 );
