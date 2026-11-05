<?php
/**
 * Stub test of the article anatomy (inc/ce-article-anatomy.php): key points,
 * the passages read from the Qur'an and hadith cards, the article's own
 * Sources list and its citations, the FAQ items that show, and the visible
 * FAQ and sources sections.
 *
 * Run: php tests/article-anatomy-test.php
 */

if ( 'cli' !== PHP_SAPI ) {
	exit;
}
define( 'ABSPATH', '/' );
$GLOBALS['meta'] = [];
function add_action() {}
function add_filter() {}
function __( $s ) { return $s; }
function esc_html__( $s ) { return $s; }
function esc_html( $s ) { return htmlspecialchars( (string) $s, ENT_QUOTES ); }
function esc_attr( $s ) { return esc_html( $s ); }
function wp_strip_all_tags( $s ) { return strip_tags( (string) $s ); }
function wpautop( $s ) { return '<p>' . $s . '</p>'; }
function get_post_meta( $id, $key ) { return $GLOBALS['meta'][ $key ] ?? ''; }
class WP_Post {
	public $ID            = 5;
	public $post_password = '';
	public $post_content  = '';
	public $post_excerpt  = '';
}

require dirname( __DIR__ ) . '/inc/ce-article-anatomy.php';

$fail = 0;
$n    = 0;
function check( $ok, $label ) {
	global $fail, $n;
	++$n;
	if ( ! $ok ) {
		++$fail;
		echo "FAIL: $label\n";
	}
}

check( [ 'One', 'Two', 'Three' ] === ce_anatomy_points( " One\n\nTwo ;; Three\r\n" ), 'key points split on lines and ;;' );

$content = '<p>Lead.</p><div class="quran-citation"><div class="quran-arabic">…</div><div class="quran-translation">“T”</div><div class="quran-ref">Sūrat al-Māʾidah 5:48</div></div>'
	. '<div class="hadith-citation"><div class="hadith-arabic">…</div><div class="hadith-translation">“H”</div><div class="hadith-ref">Ṣaḥīḥ al-Bukhārī <em>3635</em></div></div>'
	. '<h2>Sources</h2><ul><li>Ibn Kathīr, <em>Tafsīr</em>, 3:128. <a href="https://shamela.ws/book/8473/1393">shamela</a></li><li>al-Ṭabarī, <em>Jāmiʿ al-Bayān</em>.</li></ul>';

check( [ [ 'quran', 'Sūrat al-Māʾidah 5:48' ], [ 'hadith', 'Ṣaḥīḥ al-Bukhārī 3635' ] ] === ce_anatomy_passages( $content ), 'passages read from the cards, in order' );
check( [ [ 'Ibn Kathīr, Tafsīr, 3:128. shamela', 'https://shamela.ws/book/8473/1393' ], [ 'al-Ṭabarī, Jāmiʿ al-Bayān.', '' ] ] === ce_anatomy_references( $content ), 'the Sources list, with the first address' );
$cites = ce_anatomy_reference_citations( $content );
check( 2 === count( $cites ) && 'https://shamela.ws/book/8473/1393' === $cites[0]['url'] && ! isset( $cites[1]['url'] ), 'Sources become CreativeWork citations' );
check( [] === ce_anatomy_references( '<h2>Notes</h2><ul><li>x</li></ul>' ), 'no Sources heading: no references' );

$post               = new WP_Post();
$post->post_content = $content;
$section            = ce_anatomy_sources_section( $post );
check( false !== strpos( $section, '<h2 id="ce-sources-title">Passages quoted</h2>' ), 'with its own Sources list the section is Passages quoted' );
check( false !== strpos( $section, '<a href="#passage-1">Sūrat al-Māʾidah 5:48</a>' ) && false !== strpos( $section, '<a href="#passage-2">' ), 'each passage links to its card' );
$post->post_content = '<p>x</p>' . substr( $content, 0, strpos( $content, '<h2>Sources' ) );
check( false !== strpos( ce_anatomy_sources_section( $post ), '<h2 id="ce-sources-title">Sources</h2>' ), 'without one it is Sources' );
$post->post_content = '<p>No cards.</p>';
check( '' === ce_anatomy_sources_section( $post ), 'no cards: no section' );

$GLOBALS['meta']['_ce_faq_items'] = [ [ 'q' => 'Q one?', 'a' => 'A one.' ], [ 'q' => ' ', 'a' => 'No question' ], [ 'q' => 'Q <b>two</b>?', 'a' => 'A two.' ] ];
check( 2 === count( ce_anatomy_faq_items( $post ) ), 'incomplete FAQ rows are left out' );
$faq = ce_anatomy_faq_section( $post );
check( 2 === substr_count( $faq, '<details class="ce-faq-item">' ) && false !== strpos( $faq, 'Q &lt;b&gt;two&lt;/b&gt;?' ), 'the FAQ shows each question, escaped' );
$GLOBALS['meta'] = [];
check( '' === ce_anatomy_faq_section( $post ) && [] === ce_anatomy_faq_items( null ), 'no items: no FAQ' );

check( ! ce_anatomy_answer_shows( $post ), 'no excerpt and no key points: no box' );
$post->post_excerpt = 'A summary.';
check( ce_anatomy_answer_shows( $post ), 'an excerpt shows the box' );

echo $fail ? "$fail of $n checks failed\n" : "All $n checks passed\n";
exit( $fail ? 1 : 0 );
