<?php
/**
 * Quranic verse text for the KFGQPC HAFS Uthmanic Script face.
 *
 * The verses in the article data follow the Tanzil Uthmani encoding: sukun is
 * U+0652 and the small rounded zero over a silent letter is U+06DF. The KFGQPC
 * face follows the King Fahd Complex's own encoding, where the Madani sukun is
 * U+06E1 and the rounded zero is U+0652; it draws U+06DF as a spacing
 * ornament. It also draws Arabic-Indic digits in a verse as the ayah-end
 * medallion by itself, so the ornate brackets ﴿﴾ written round an ayah number
 * (or an U+06DD before it) would draw a second ornament.
 *
 * Each verse element is fitted as it renders; the stored text stays standard
 * and the font is never touched (its licence forbids modification). Only
 * elements with the class quran-arabic are changed: never hadith or any
 * other Arabic. A fitted element is marked data-ce-quran="kfgqpc", so a
 * second pass leaves it alone; a verse already in the Complex's encoding
 * (it carries U+06E1) keeps its marks and only loses the brackets.
 *
 * @package compelling-evidence
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Fit one verse's text to the KFGQPC face.
 *
 * @param string $text Verse text, no tags.
 * @return string
 */
function ce_quran_fit_text( $text ) {
    if ( false === strpos( $text, "\u{06E1}" ) ) {
        $text = strtr(
            $text,
            array(
                "\u{0652}" => "\u{06E1}",
                "\u{06DF}" => "\u{0652}",
            )
        );
    }
    // On invalid UTF-8 a /u replace returns null; keep the text as it was.
    $text = preg_replace( '/[\x{FD3E}\x{FD3F}]\s*([\x{0660}-\x{0669}]+)\s*[\x{FD3E}\x{FD3F}]/u', '$1', $text ) ?? $text;
    $text = preg_replace( '/\x{06DD}\s*([\x{0660}-\x{0669}]+)/u', '$1', $text ) ?? $text;
    return $text;
}

/**
 * Fit every quran-arabic element in the content that is not fitted yet.
 *
 * @param string $content Post content.
 * @return string
 */
function ce_quran_fit_content( $content ) {
    if ( false === strpos( $content, 'quran-arabic' ) ) {
        return $content;
    }
    // Byte-wise (no /u): the delimiters are ASCII, so a stray invalid byte
    // elsewhere in the post neither stops every verse being fitted nor, with
    // the font now keyed to data-ce-quran, leaves them in the fallback face.
    $out = preg_replace_callback(
        '#<(div|span|p)(\b[^>]*\bclass="[^"]*\bquran-arabic\b[^"]*"[^>]*)>([^<]*)</\1>#',
        static function ( $m ) {
            if ( false !== strpos( $m[2], 'data-ce-quran=' ) ) {
                return $m[0];
            }
            return '<' . $m[1] . $m[2] . ' data-ce-quran="kfgqpc">' . ce_quran_fit_text( $m[3] ) . '</' . $m[1] . '>';
        },
        $content
    );
    return null === $out ? $content : $out;
}
add_filter( 'the_content', 'ce_quran_fit_content', 8 );

/**
 * The hero verse's ayah number from Theme Options, as bare Arabic-Indic
 * digits for the face to draw as one medallion. Brackets, U+06DD and spaces
 * saved under the old bracketed style are dropped; Western digits are mapped.
 *
 * @return string
 */
function ce_hero_verse_number() {
    $raw    = strtr( (string) get_option( 'ce_hero_verse_number', '١٣' ), array_combine( range( '0', '9' ), array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' ) ) );
    $digits = preg_replace( '/[^\x{0660}-\x{0669}]/u', '', $raw );
    return '' === (string) $digits ? '١٣' : $digits;
}
