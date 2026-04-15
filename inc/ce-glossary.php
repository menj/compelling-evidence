<?php
/**
 * Glossary Auto-Linking System with Rollover Tooltips
 * Auto-links Islamic/Arabic terms in articles and shows definition tooltips
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get all glossary terms as a flat array for lookup
 */
function ce_get_glossary_terms() {
    return [
        ['term' => 'Actionalism', 'arabic' => '', 'def' => 'The principle that moral action, freely chosen, is the purpose of human existence.'],
        ['term' => 'Akhira', 'arabic' => 'آخرة', 'def' => 'The Hereafter; the life after death.'],
        ['term' => 'Allah', 'arabic' => 'الله', 'def' => 'The proper name for God in Islam.'],
        ['term' => 'Amanah', 'arabic' => 'أمانة', 'def' => 'The trust; the moral law.'],
        ['term' => 'Ayah', 'arabic' => 'آية', 'def' => 'A sign; a verse of the Quran.'],
        ['term' => 'Barzakh', 'arabic' => 'برزخ', 'def' => 'The intermediate realm between death and resurrection.'],
        ['term' => 'Dawah', 'arabic' => 'دعوة', 'def' => 'Invitation; calling others to Islam through evidence.'],
        ['term' => 'Dhikr', 'arabic' => 'ذكر', 'def' => 'Remembrance of God.'],
        ['term' => 'Dunya', 'arabic' => 'دنيا', 'def' => 'This world; the temporal realm.'],
        ['term' => 'Falah', 'arabic' => 'فلاح', 'def' => 'Felicity; success through ethical effort.'],
        ['term' => 'Fard', 'arabic' => 'فرض', 'def' => 'An obligatory religious duty.'],
        ['term' => 'Fitrah', 'arabic' => 'فطرة', 'def' => 'The innate human disposition toward recognising God.'],
        ['term' => 'Ghayb', 'arabic' => 'غيب', 'def' => 'The unseen; realities beyond empirical measurement.'],
        ['term' => 'Hadith', 'arabic' => 'حديث', 'def' => 'A report of the words, actions, or approvals of the Prophet Muhammad.'],
        ['term' => 'Hajj', 'arabic' => 'حج', 'def' => 'The pilgrimage to Mecca.'],
        ['term' => 'Haram', 'arabic' => 'حرام', 'def' => 'Forbidden; prohibited by Islamic law.'],
        ['term' => 'Hawā', 'arabic' => 'هوى', 'def' => 'Desire; the pull of what one wishes were true.'],
        ['term' => 'Hikmah', 'arabic' => 'حكمة', 'def' => 'Wisdom; the ability to apply knowledge appropriately.'],
        ['term' => 'Ibadah', 'arabic' => 'عبادة', 'def' => 'Worship; service.'],
        ['term' => 'Ihsan', 'arabic' => 'إحسان', 'def' => 'Excellence in worship.'],
        ['term' => 'Iman', 'arabic' => 'إيمان', 'def' => 'Faith; truth appropriated by the mind after honest evaluation.'],
        ['term' => 'Islam', 'arabic' => 'إسلام', 'def' => 'Submission; the religion of surrendering to God\'s will.'],
        ['term' => 'Jannah', 'arabic' => 'جنة', 'def' => 'Paradise; the Garden.'],
        ['term' => 'Jihad', 'arabic' => 'جهاد', 'def' => 'Struggle; striving.'],
        ['term' => 'Kafir', 'arabic' => 'كافر', 'def' => 'One who covers or denies the truth.'],
        ['term' => 'Khalifah', 'arabic' => 'خليفة', 'def' => 'Vicegerent; God\'s representative on earth.'],
        ['term' => 'Kufr', 'arabic' => 'كفر', 'def' => 'Disbelief; covering the truth.'],
        ['term' => 'Mumin', 'arabic' => 'مؤمن', 'def' => 'A believer; one who has recognised the truth.'],
        ['term' => 'Muhasabah', 'arabic' => 'محاسبة', 'def' => 'Self-reckoning; spiritual discipline of examining motives.'],
        ['term' => 'Muslim', 'arabic' => 'مسلم', 'def' => 'One who submits to God.'],
        ['term' => 'Nafs', 'arabic' => 'نفس', 'def' => 'The self; the soul; the psyche.'],
        ['term' => 'Niyyah', 'arabic' => 'نية', 'def' => 'Intention.'],
        ['term' => 'Normativeness', 'arabic' => '', 'def' => 'The principle that God\'s existence is a moral event.'],
        ['term' => 'Quran', 'arabic' => 'القرآن', 'def' => 'The Recitation; the final revealed scripture in Islam.'],
        ['term' => 'Rahmah', 'arabic' => 'رحمة', 'def' => 'Mercy; compassion; womb-like care.'],
        ['term' => 'Ramadan', 'arabic' => 'رمضان', 'def' => 'The ninth month of the Islamic calendar, month of fasting.'],
        ['term' => 'Ridwan', 'arabic' => 'رضوان', 'def' => 'God\'s pleasure; His satisfaction.'],
        ['term' => 'Salah', 'arabic' => 'صلاة', 'def' => 'Prayer; the five daily ritual prayers.'],
        ['term' => 'Salam', 'arabic' => 'سلام', 'def' => 'Peace; the greeting of Muslims.'],
        ['term' => 'Sawm', 'arabic' => 'صوم', 'def' => 'Fasting.'],
        ['term' => 'Shahada', 'arabic' => 'شهادة', 'def' => 'The declaration of faith.'],
        ['term' => 'Shariah', 'arabic' => 'شريعة', 'def' => 'The Islamic law; the path to water.'],
        ['term' => 'Shirk', 'arabic' => 'شرك', 'def' => 'Associating partners with God.'],
        ['term' => 'Sunnah', 'arabic' => 'سنة', 'def' => 'The way of the Prophet Muhammad.'],
        ['term' => 'Sunan', 'arabic' => 'سنن', 'def' => 'God\'s immutable patterns in creation.'],
        ['term' => 'Tafsir', 'arabic' => 'تفسير', 'def' => 'Quranic exegesis; scholarly interpretation.'],
        ['term' => 'Taqwa', 'arabic' => 'تقوى', 'def' => 'God-consciousness; awareness of the divine presence.'],
        ['term' => 'Tawhid', 'arabic' => 'توحيد', 'def' => 'The oneness of God.'],
        ['term' => 'Tawbah', 'arabic' => 'توبة', 'def' => 'Repentance; return to God.'],
        ['term' => 'Ummah', 'arabic' => 'أمة', 'def' => 'The Muslim community.'],
        ['term' => 'Unity of Truth', 'arabic' => '', 'def' => 'The principle that if God is one, truth is one.'],
        ['term' => 'Waswas', 'arabic' => 'وسوسة', 'def' => 'Satanic whispering; intrusive doubt.'],
        ['term' => 'Zakat', 'arabic' => 'زكاة', 'def' => 'Obligatory charity; wealth purification.'],
    ];
}

/**
 * Auto-link glossary terms in content
 */
function ce_auto_link_glossary_terms( $content ) {
    // Don't process if in admin or if content is empty
    if ( is_admin() || empty( $content ) ) {
        return $content;
    }

    $terms = ce_get_glossary_terms();
    $glossary_url = home_url( '/glossary/' );

    // Sort terms by length (longest first) to prevent partial replacements
    usort( $terms, function( $a, $b ) {
        return strlen( $b['term'] ) - strlen( $a['term'] );
    });

    // Create a map of terms to track what we've already linked (prevent double-linking)
    $linked_positions = [];

    foreach ( $terms as $term_data ) {
        $term = $term_data['term'];
        $arabic = $term_data['arabic'];
        $definition = esc_attr( $term_data['def'] );
        $anchor = sanitize_title( $term );
        $url = $glossary_url . '#term-' . $anchor;

        // Match whole words only, case-insensitive, avoid already linked text
        $pattern = '/\b(' . preg_quote( $term, '/' ) . ')\b/i';

        $content = preg_replace_callback( $pattern, function( $matches ) use ( $url, $definition, $arabic, &$linked_positions, $content ) {
            // Check if this position is already linked
            $match_pos = strpos( $content, $matches[1] );

            // Skip if inside HTML tags or already linked
            if ( ce_is_inside_html_tag( $content, $match_pos ) || ce_is_already_linked( $content, $match_pos ) ) {
                return $matches[1];
            }

            $arabic_attr = $arabic ? ' data-arabic="' . esc_attr( $arabic ) . '"' : '';

            return '<a href="' . esc_url( $url ) . '" class="ce-glossary-term" data-definition="' . $definition . '"' . $arabic_attr . '>' . $matches[1] . '</a>';
        }, $content, 3 ); // Limit to 3 replacements per term per article
    }

    return $content;
}

/**
 * Check if position is inside an HTML tag
 */
function ce_is_inside_html_tag( $content, $position ) {
    $before = substr( $content, 0, $position );
    $lt_count = substr_count( $before, '<' );
    $gt_count = substr_count( $before, '>' );
    return $lt_count > $gt_count;
}

/**
 * Check if position is already inside a link
 */
function ce_is_already_linked( $content, $position ) {
    $before = substr( $content, 0, $position );
    $after = substr( $content, $position );

    // Check for opening <a before and closing </a> after
    $last_open_a = strrpos( $before, '<a ' );
    $last_close_a = strrpos( $before, '</a>' );

    if ( $last_open_a !== false && ( $last_close_a === false || $last_open_a > $last_close_a ) ) {
        return true;
    }

    return false;
}

/**
 * Enqueue glossary tooltip assets
 */
function ce_glossary_enqueue_assets() {
    // Only on single articles and posts
    if ( ! is_singular( ['ce_article', 'post'] ) ) {
        return;
    }

    wp_enqueue_style(
        'ce-glossary-tooltip',
        get_stylesheet_directory_uri() . '/assets/css/glossary-tooltip.css',
        [],
        wp_get_theme()->get('Version')
    );

    wp_enqueue_script(
        'ce-glossary-tooltip',
        get_stylesheet_directory_uri() . '/assets/js/glossary-tooltip.js',
        [],
        wp_get_theme()->get('Version'),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'ce_glossary_enqueue_assets' );

/**
 * Apply auto-linking to article content
 */
function ce_glossary_filter_content( $content ) {
    if ( is_singular( ['ce_article', 'post'] ) ) {
        $content = ce_auto_link_glossary_terms( $content );
    }
    return $content;
}
// Content filter removed — handled by unified engine in ce-crosslinks.php
// add_filter( 'the_content', 'ce_glossary_filter_content', 20 );

/**
 * Shortcode to display a glossary term with tooltip
 * Usage: [glossary term="Tawhid"]
 */
function ce_glossary_term_shortcode( $atts ) {
    $atts = shortcode_atts( [
        'term' => '',
    ], $atts );

    if ( empty( $atts['term'] ) ) {
        return '';
    }

    $terms = ce_get_glossary_terms();
    $found = null;

    foreach ( $terms as $t ) {
        if ( strcasecmp( $t['term'], $atts['term'] ) === 0 ) {
            $found = $t;
            break;
        }
    }

    if ( ! $found ) {
        return esc_html( $atts['term'] );
    }

    $anchor = sanitize_title( $found['term'] );
    $url = home_url( '/glossary/#term-' . $anchor );
    $arabic_attr = $found['arabic'] ? ' data-arabic="' . esc_attr( $found['arabic'] ) . '"' : '';

    return '<a href="' . esc_url( $url ) . '" class="ce-glossary-term" data-definition="' . esc_attr( $found['def'] ) . '"' . $arabic_attr . '>' . esc_html( $found['term'] ) . '</a>';
}
add_shortcode( 'glossary', 'ce_glossary_term_shortcode' );
