<?php
/**
 * Compelling Evidence — topic icons.
 *
 * Line icons (24×24, stroke) for article cards that have no featured image.
 * Replaces the position-based emoji placeholders, which rendered differently
 * on every operating system and bore no relation to the article's topic.
 *
 * @since 2.6.26
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** SVG inner markup keyed by topic name (plus legacy static-card tags). */
function ce_topic_icon_paths(): array {
    $sun     = '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>';
    $cloud   = '<path d="M20 16.2A4.5 4.5 0 0 0 17.5 8h-1.8A7 7 0 1 0 4 14.9"/><path d="M8 14v6M12 16v6M16 14v6"/>';
    $compass = '<circle cx="12" cy="12" r="10"/><path d="M16.24 7.76l-2.12 6.36-6.36 2.12 2.12-6.36z"/>';
    $flask   = '<path d="M9 3h6M10 3v6L4.5 19a1.5 1.5 0 0 0 1.3 2h12.4a1.5 1.5 0 0 0 1.3-2L14 9V3"/><path d="M7 15h10"/>';
    $book    = '<path d="M2 4h6a4 4 0 0 1 4 4v13a3 3 0 0 0-3-3H2z"/><path d="M22 4h-6a4 4 0 0 0-4 4v13a3 3 0 0 1 3-3h7z"/>';
    $columns = '<path d="M3 22h18M6 18v-7M10 18v-7M14 18v-7M18 18v-7M12 2l8 5H4z"/>';
    $scale   = '<path d="M12 3v18M7 21h10M4 7h16"/><path d="M7 7l-3 7a3 3 0 0 0 6 0zM17 7l-3 7a3 3 0 0 0 6 0z"/>';
    $moon    = '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/>';
    $key     = '<circle cx="7.5" cy="15.5" r="5.5"/><path d="M11.5 11.5L21 2M16 7l3 3M19 4l2 2"/>';
    $heart   = '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8z"/>';
    $spark   = '<path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9z"/><path d="M19 16v5M16.5 18.5h5"/>';

    return [
        'Does God Exist?'               => $sun,
        'The Problem of Evil'           => $cloud,
        'Ethics Without God?'           => $compass,
        'Science & Evidence'            => $flask,
        'The Quran & Its Sources'       => $book,
        'History, Context & Comparison' => $columns,
        'Divine Justice & Fairness'     => $scale,
        'Islamic Practice & Ritual'     => $moon,
        'Rights & Freedom'              => $key,
        'The Inner Journey'             => $heart,
        'Revelation & Meaning'          => $spark,
        // Tags used by the static fallback cards on the front page.
        'Existence' => $sun, 'Cosmology' => $spark, 'Theology' => $sun,
        'Theodicy'  => $cloud, 'Science' => $flask, 'Ethics' => $compass,
    ];
}

/** Card placeholder markup for a topic; unknown topics get a question-mark icon. */
function ce_card_placeholder( string $topic_name = '' ): string {
    $paths = ce_topic_icon_paths();
    $inner = $paths[ html_entity_decode( $topic_name, ENT_QUOTES ) ]
        ?? '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3M12 17h.01"/>';
    return '<div class="card-img-placeholder"><svg class="card-topic-icon" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
        . $inner . '</svg></div>';
}
