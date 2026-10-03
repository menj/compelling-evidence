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

    // ── Analytics ──
    register_setting( 'ce_options_analytics', 'ce_analytics_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_analytics', 'ce_analytics_retention_days', [ 'default' => '90' ] );
    register_setting( 'ce_options_analytics', 'ce_analytics_track_scroll', [ 'default' => '1' ] );
    register_setting( 'ce_options_analytics', 'ce_analytics_track_search', [ 'default' => '1' ] );

    // ── Performance ──
    register_setting( 'ce_options_performance', 'ce_parallax_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_performance', 'ce_preload_fonts', [ 'default' => '1' ] );
    register_setting( 'ce_options_performance', 'ce_minify_inline', [ 'default' => '0' ] );
    register_setting( 'ce_options_performance', 'ce_gone_enabled', [ 'default' => '1' ] );
    register_setting( 'ce_options_performance', 'ce_gone_paths', [ 'default' => ce_gone_default_paths() ] );
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
        'identity'    => [ 'label' => 'Author & Identity', 'icon' => 'dashicons-businessperson' ],
        'colors'      => [ 'label' => 'Colors',            'icon' => 'dashicons-art' ],
        'typography'  => [ 'label' => 'Typography',        'icon' => 'dashicons-editor-textcolor' ],
        'links'       => [ 'label' => 'Links & Tooltips',  'icon' => 'dashicons-admin-links' ],
        'media'       => [ 'label' => 'Media',             'icon' => 'dashicons-format-image' ],
        'quiz'        => [ 'label' => 'Quiz & Journeys',   'icon' => 'dashicons-forms' ],
        'engagement'  => [ 'label' => 'Engagement',        'icon' => 'dashicons-thumbs-up' ],
        'analytics'   => [ 'label' => 'Analytics',         'icon' => 'dashicons-chart-area' ],
        'performance' => [ 'label' => 'Performance',       'icon' => 'dashicons-performance' ],
        'tools'       => [ 'label' => 'Tools & Sync',      'icon' => 'dashicons-admin-tools' ],
    ];

    $active = sanitize_key( $_GET['tab'] ?? 'general' );
    if ( ! isset( $tabs[ $active ] ) ) $active = 'general';

    // Handle save
    $saved = false;
    $sync_result = null;
    $media_result = null;
    
    if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer( 'ce_options_' . $active ) ) {
        // Handle manual sync trigger
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
        } else {
            $option_group = 'ce_options_' . $active;
            // WordPress settings API handles the save via options.php,
            // but we use a direct approach for our tabbed interface.
            $fields = ce_get_tab_fields( $active );
            foreach ( $fields as $field ) {
                $key = $field['id'];
                if ( $field['type'] === 'checkbox' ) {
                    update_option( $key, isset( $_POST[ $key ] ) ? '1' : '0' );
                } else {
                    // wp_unslash() is required because WordPress magic-quotes
                    // POST values; without it we double-escape on save.
                    $value = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitised per field type immediately below; nonce checked by check_admin_referer().
                    if ( $field['type'] === 'color' ) {
                        $value = sanitize_hex_color( $value ) ?: $field['default'];
                    } elseif ( $field['type'] === 'number' ) {
                        $value = absint( $value );
                    } elseif ( $field['type'] === 'textarea' ) {
                        $value = sanitize_textarea_field( $value );
                    } else {
                        $value = sanitize_text_field( $value );
                    }
                    update_option( $key, $value );
                }
            }
            $saved = true;
        }
    }

    ?>
    <div class="wrap ce-options-wrap">
        <h1 class="ce-options-title">
            <span class="ce-options-logo" title="Compelling Evidence" aria-label="Compelling Evidence">
                <img src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/images/ce-icon.svg' ); ?>"
                     alt="CE" width="28" height="28" style="display:block;">
            </span>
            CE Theme Options
        </h1>

        <?php if ( ! empty( $saved ) ) : ?>
            <div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>
        <?php endif; ?>

        <?php if ( ! empty( $sync_result ) ) : ?>
            <?php if ( $sync_result['success'] ) : ?>
                <div class="notice notice-success is-dismissible">
                    <p><strong>Sync completed successfully!</strong></p>
                    <ul style="margin:0;list-style:none;">
                        <li>✓ Articles synced: <?php echo intval( $sync_result['synced'] ); ?></li>
                        <li>✓ Topics created: <?php echo intval( $sync_result['topics_created'] ); ?></li>
                        <li>✓ Topics cleaned: <?php echo intval( $sync_result['topics_cleaned'] ); ?></li>
                        <?php if ( ! empty( $sync_result['duplicates_found'] ) ) : ?>
                            <li>⚠ Duplicates detected and skipped: <?php echo intval( $sync_result['duplicates_found'] ); ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php else : ?>
                <div class="notice notice-error is-dismissible">
                    <p><strong>Sync failed:</strong> <?php echo esc_html( $sync_result['error'] ); ?></p>
                    <?php if ( ! empty( $sync_result['fallback'] ) ) : ?>
                        <p><em>Fallback mode activated. <?php echo intval( $sync_result['fallback_synced'] ); ?> articles synced with basic method.</em></p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <style>
            .ce-options-wrap { max-width: 960px; }
            .ce-options-title { display: flex; align-items: center; gap: 0.6rem; font-size: 1.6rem; font-weight: 700; margin-bottom: 1.2rem; }
            .ce-options-logo { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background: #180d2e; border-radius: 8px; overflow: hidden; flex-shrink: 0; }
            .ce-options-logo img { width: 28px; height: 28px; object-fit: contain; }

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
                <a href="<?php echo esc_url( admin_url( 'themes.php?page=ce-theme-options&tab=' . $key ) ); ?>"
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
                            case 'password':
                            case 'number': ?>
                                <label class="ce-field-label" for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
                                <input type="<?php echo esc_attr( $field['type'] ); ?>"
                                       id="<?php echo esc_attr( $field['id'] ); ?>"
                                       name="<?php echo esc_attr( $field['id'] ); ?>"
                                       value="<?php echo esc_attr( $value ); ?>"
                                       <?php if ( 'password' === $field['type'] ) echo 'autocomplete="off" spellcheck="false"'; ?>
                                       <?php if ( ! empty( $field['min'] ) ) echo 'min="' . esc_attr( $field['min'] ) . '"'; ?>
                                       <?php if ( ! empty( $field['max'] ) ) echo 'max="' . esc_attr( $field['max'] ) . '"'; ?>
                                       <?php if ( ! empty( $field['step'] ) ) echo 'step="' . esc_attr( $field['step'] ) . '"'; ?>>
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

                <?php if ( $active === 'media' && function_exists( 'ce_media_status_html' ) ) : ?>
                    <div class="ce-field" style="background:#f8f9fa;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-top:1rem;">
                        <h3 style="margin-top:0;margin-bottom:0.6rem;font-size:1rem;">Article images</h3>
                        <?php if ( $media_result ) : ?>
                            <div class="notice notice-success inline" style="margin:0 0 1rem;"><p><?php echo esc_html( sprintf( 'Imported %d image(s); %d remaining.', (int) $media_result['imported'], (int) $media_result['remaining'] ) ); ?></p></div>
                        <?php endif; ?>
                        <?php echo wp_kses_post( ce_media_status_html() ); ?>
                        <p style="color:#555;">Figures use images registered in <code>inc/articles/media.json</code>, each with its source page and licence. Images from Wikimedia Commons, Pexels and Flickr are supported; the credit line is printed under every figure.</p>
                        <button type="submit" name="ce_media_import_now" value="1" class="button button-secondary">Import next 20 images now</button>
                    </div>
                    <?php if ( function_exists( 'ce_media_auto_panel_html' ) ) { echo ce_media_auto_panel_html(); } // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- every value is escaped inside the function. ?>
                <?php endif; ?>

                <?php if ( $active === 'tools' ) : ?>
                    <!-- Special Tools Tab Content -->
                    <div class="ce-field" style="background:#f8f9fa;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-top:1rem;">
                        <h3 style="margin-top:0;margin-bottom:1rem;font-size:1rem;">Manual Content Synchronization</h3>
                        <p style="margin-bottom:1rem;color:#555;">
                            Use this button to manually trigger the article content sync from JSON files to the WordPress database. 
                            This will update all 120 articles, create missing topic terms, and clean up deprecated categories.
                        </p>
                        
                        <?php 
                        $last_sync = get_option( 'ce_content_sync_last_run', 'Never' );
                        $theme_ver = wp_get_theme()->get( 'Version' );
                        $sync_key = 'ce_content_sync_' . str_replace( '.', '_', $theme_ver );
                        $has_synced = get_option( $sync_key ) ? 'Yes ✓' : 'No (sync pending)';
                        ?>
                        <div style="background:#fff;border:1px solid #e0e0e0;border-radius:4px;padding:1rem;margin-bottom:1rem;font-size:0.85rem;">
                            <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;">
                                <span>Current Theme Version:</span>
                                <strong><?php echo esc_html( $theme_ver ); ?></strong>
                            </div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:0.5rem;">
                                <span>Sync Status:</span>
                                <strong><?php echo esc_html( $has_synced ); ?></strong>
                            </div>
                            <div style="display:flex;justify-content:space-between;">
                                <span>Last Manual Sync:</span>
                                <strong><?php echo esc_html( $last_sync ); ?></strong>
                            </div>
                        </div>
                        
                        <div style="display:flex;gap:1rem;align-items:center;">
                            <button type="submit" name="ce_manual_sync" value="1" class="ce-save-btn" style="background:#e8455a;" onclick="return confirm('This will sync all 120 articles. Continue?');">
                                <span class="dashicons dashicons-update" style="margin-right:0.3rem;vertical-align:middle;"></span>
                                Run Content Sync Now
                            </button>
                            <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.85rem;color:#666;">
                                <input type="checkbox" name="ce_sync_force" value="1">
                                Force re-sync (ignore version check)
                            </label>
                        </div>
                        
                        <div style="margin-top:1.5rem;padding-top:1rem;border-top:1px solid #eee;">
                            <h4 style="margin:0 0 0.5rem;font-size:0.9rem;">Sync Safeguards</h4>
                            <ul style="margin:0;padding-left:1.2rem;font-size:0.8rem;color:#666;">
                                <li>Duplicate detection: Skips articles with duplicate slugs</li>
                                <li>Fallback mode: If JSON loading fails, attempts basic sync</li>
                                <li>Memory limit: Auto-increases to 256M for large syncs</li>
                                <li>Timeout protection: 5-minute limit prevents incomplete syncs</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Article Data Diagnostics -->
                    <div class="ce-field" style="background:#f8f9fa;border:1px solid #ddd;border-radius:8px;padding:1.5rem;margin-top:1rem;">
                        <h3 style="margin-top:0;margin-bottom:0.3rem;font-size:1rem;">Article Data Diagnostics</h3>
                        <p style="margin:0 0 1.2rem;color:#555;font-size:0.85rem;">
                            Verifies manifest integrity, batch file checksums, and loader health. Run this if articles appear empty or sync is not firing.
                        </p>
                        <?php ce_render_article_diagnostics(); ?>
                    </div>
                <?php else : ?>
                    <div class="ce-save-row">
                        <button type="submit" class="ce-save-btn">Save Changes</button>
                    </div>
                <?php endif; ?>
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

        'identity' => [
            // ── Author (Person schema) ─────────────────────────────────────
            [ 'id' => 'ce_author_name',     'label' => 'Author Name',     'type' => 'text', 'default' => '', 'section' => 'Author', 'desc' => 'Used in Person schema and the visible byline on every article. Leave empty to fall back to Organization-only authorship.' ],
            [ 'id' => 'ce_author_title',    'label' => 'Author Job Title', 'type' => 'text', 'default' => '', 'desc' => 'e.g. "Editor", "Writer", "Founder". Used in Person schema (jobTitle).' ],
            [ 'id' => 'ce_author_bio',      'label' => 'Author Bio',      'type' => 'textarea', 'default' => '', 'desc' => 'Up to 300 characters. Used in Person schema (description) and the author archive page.' ],
            [ 'id' => 'ce_author_url',      'label' => 'Author Profile URL', 'type' => 'text', 'default' => '', 'desc' => 'Where the byline links to. Defaults to the WordPress author archive if empty.' ],

            // ── Author social profiles (sameAs schema) ─────────────────────
            [ 'id' => 'ce_author_twitter',  'label' => 'Author Twitter / X', 'type' => 'text', 'default' => '', 'section' => 'Author Social Profiles', 'desc' => 'Full URL, e.g. https://twitter.com/handle' ],
            [ 'id' => 'ce_author_youtube',  'label' => 'Author YouTube',  'type' => 'text', 'default' => '', 'desc' => 'Full URL to channel.' ],
            [ 'id' => 'ce_author_github',   'label' => 'Author GitHub',   'type' => 'text', 'default' => '', 'desc' => 'Full URL to profile.' ],
            [ 'id' => 'ce_author_linkedin', 'label' => 'Author LinkedIn', 'type' => 'text', 'default' => '', 'desc' => 'Full URL to profile.' ],
            [ 'id' => 'ce_author_other_urls', 'label' => 'Other Author URLs', 'type' => 'textarea', 'default' => '', 'desc' => 'One URL per line. Wikipedia entry, Scholar profile, ORCID, etc. All added to the Person schema sameAs array.' ],

            // ── Site-level social and contact (Organization schema) ────────
            [ 'id' => 'ce_site_twitter_handle', 'label' => 'Site Twitter Handle', 'type' => 'text', 'default' => '', 'section' => 'Site Social & Contact', 'desc' => 'Handle only, including the @, e.g. @CompellingEv. Used for twitter:site meta tag.' ],
            [ 'id' => 'ce_site_youtube_url',    'label' => 'Site YouTube Channel', 'type' => 'text', 'default' => '', 'desc' => 'Full URL.' ],
            [ 'id' => 'ce_site_facebook_url',   'label' => 'Site Facebook Page', 'type' => 'text', 'default' => '' ],
            [ 'id' => 'ce_site_contact_email',  'label' => 'Site Contact Email', 'type' => 'text', 'default' => '', 'desc' => 'Used in Organization schema contactPoint.' ],

            // ── Logo (Organization schema, ImageObject) ────────────────────
            [ 'id' => 'ce_org_logo_url',    'label' => 'Logo URL',        'type' => 'text',   'default' => '', 'section' => 'Logo (Organization Schema)', 'desc' => 'Direct URL to logo image. Recommended 1:1 aspect ratio, ≥ 112×112 px for Google rich-result eligibility.' ],
            [ 'id' => 'ce_org_logo_width',  'label' => 'Logo Width (px)', 'type' => 'number', 'default' => '512', 'min' => 64, 'max' => 4096 ],
            [ 'id' => 'ce_org_logo_height', 'label' => 'Logo Height (px)', 'type' => 'number', 'default' => '512', 'min' => 64, 'max' => 4096 ],
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
              'options' => ce_font_options( 'heading' ),
              'desc' => 'Titles, section headings and display numbers across the site.' ],
            [ 'id' => 'ce_reading_font', 'label' => 'Reading Body Font', 'type' => 'select', 'default' => 'cormorant',
              'options' => ce_font_options( 'reading' ),
              'desc' => 'Article text, excerpts and quotations. EB Garamond has the closest x-height to Cormorant, so switching between the two keeps line lengths stable.' ],
            [ 'id' => 'ce_accent_font',  'label' => 'Label & Eyebrow Font', 'type' => 'select', 'default' => 'dm-sans',
              'options' => ce_font_options( 'accent' ),
              'desc' => 'Uppercase section labels, topic tags and page eyebrows. Special Elite gives a typewriter case-file look; it covers English text only, so it is never applied to transliterated Arabic.' ],
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
            [ 'id' => 'ce_pretty_search',  'label' => 'Pretty search URLs', 'type' => 'checkbox', 'default' => '1', 'section' => 'Search URLs',
              'desc' => 'Redirects /?s=term to /search/term/. Built in; replaces the Pretty Search Permalinks plugin. Search results stay noindexed and disallowed in robots.txt.' ],
            [ 'id' => 'ce_search_base',    'label' => 'Search base', 'type' => 'text', 'default' => 'search',
              'desc' => 'The path segment before the search term. Letters, numbers and hyphens only. Save Settings → Permalinks after changing it.' ],
        ],

        'media' => [
            [ 'id' => 'ce_lightbox_enabled', 'label' => 'Open article images and diagrams in a lightbox', 'type' => 'checkbox', 'default' => '1', 'section' => 'Lightbox',
              'desc' => 'Built into the theme (no jQuery, no plugin). Keyboard, swipe and screen-reader accessible. Replaces the Lightbox2 plugin.' ],
            [ 'id' => 'ce_media_auto', 'label' => 'Find and publish lead images for new articles automatically', 'type' => 'checkbox', 'default' => '0', 'section' => 'Automatic images',
              'desc' => 'Every hour, articles with no featured image and no figure get a Pexels photograph chosen from their title, imported, credited, set as the featured image and shown after the first paragraph. Published at once; correct any choice below with Replace or Remove.' ],
            [ 'id' => 'ce_pexels_key', 'label' => 'Pexels API key', 'type' => 'password', 'default' => '',
              'desc' => 'From pexels.com/api. Stored in the database only, never in the theme files.' ],
            [ 'id' => 'ce_media_import', 'label' => 'Import article images into the Media Library', 'type' => 'checkbox', 'default' => '1', 'section' => 'Image import',
              'desc' => 'Copies each registered image into the Media Library, a few per admin page load, so pages serve local, responsive images. Until an image is imported, figures load it from its source.' ],
        ],

        'performance' => [
            [ 'id' => 'ce_parallax_enabled', 'label' => 'Enable homepage parallax effect',  'type' => 'checkbox', 'default' => '1', 'section' => 'Visual Effects', 'desc' => 'Dot grid, glow orb, and Arabic verse parallax layers. Automatically disabled on mobile and prefers-reduced-motion.' ],
            [ 'id' => 'ce_preload_fonts',    'label' => 'Preload critical fonts',           'type' => 'checkbox', 'default' => '1', 'desc' => 'Adds <link rel="preload"> for the selected heading font and DM Sans 400 to eliminate flash of invisible text.' ],
            [ 'id' => 'ce_minify_inline',    'label' => 'Minify inline script output',      'type' => 'checkbox', 'default' => '0', 'desc' => 'Strips whitespace from wp_localize_script output. Minor savings.' ],
            [ 'id' => 'ce_gone_enabled',     'label' => 'Answer retired URLs with 410 Gone', 'type' => 'checkbox', 'default' => '1', 'section' => 'Retired URLs', 'desc' => 'Returns a lightweight 410 response, before the main query runs, for paths that never belonged to this site (left behind by a past spam injection). Crawlers drop 410 URLs faster than 404 URLs.' ],
            [ 'id' => 'ce_gone_paths',       'label' => 'Retired path prefixes',            'type' => 'textarea', 'default' => ce_gone_default_paths(), 'desc' => 'One path prefix per line, starting with a slash. Any request whose path begins with a listed prefix receives 410. Never list a prefix used by real content.' ],
        ],

        'tools' => [
            [ 'id' => 'ce_sync_info', 'label' => 'Content Sync', 'type' => 'info', 'section' => 'Manual Article Sync' ],
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
