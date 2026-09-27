<?php
/**
 * CE Theme: the 404 page's "excuses".
 *
 * Each excuse riffs on an argument the site treats seriously and links to
 * the article that does so. The page opens on a random excuse; "Hear
 * another excuse" cycles through the rest (assets/js/ce-404.js), and
 * falls back to a ?excuse=N reload without JavaScript.
 *
 * House rules for new entries: humour aims at the missing page, the server
 * or the webmaster, and never at a faith, a scripture or a reader.
 *
 * @since 2.6.29
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** @return array<int, array{title:string, body:string, label:string, slug:string}> */
function ce_404_excuses(): array {
    return [
        [ 'title' => 'Absence of evidence is, in this case, evidence of absence.',
          'body'  => 'We usually resist that inference. Here it holds: we searched the whole site, and this page is nowhere in it.',
          'label' => 'The burden of proof', 'slug' => 'burden-of-proof' ],
        [ 'title' => 'Everything that begins to exist has a cause. This page never began.',
          'body'  => 'The kalām argument handles universes with ease. For an address that was never created, the causal chain ends at a keyboard.',
          'label' => 'The cosmological argument', 'slug' => 'existence-come-out-of-nothing' ],
        [ 'title' => 'A page than which none greater can be conceived.',
          'body'  => 'Anselm would say that a page existing in reality is greater than one existing only in the mind. Our server has read the argument and remains unpersuaded.',
          'label' => 'The ontological argument', 'slug' => 'ontological-argument' ],
        [ 'title' => 'If a good webmaster exists, why do broken links?',
          'body'  => 'The problem of evil in its smallest possible form. Our theodicy rests on free will: someone, somewhere, freely typed this address.',
          'label' => 'The problem of evil', 'slug' => 'problem-of-evil-response' ],
        [ 'title' => 'Fine-tuned for life. Hopelessly detuned for this URL.',
          'body'  => 'The constants of physics fall within a narrow life-permitting range. The characters in this address fall outside every page-permitting range we have.',
          'label' => 'The fine-tuning argument', 'slug' => 'fine-tuning-universe' ],
        [ 'title' => 'We checked the gaps. The page is missing from those too.',
          'body'  => 'Critics warn against a God of the gaps. We apply the same discipline here and decline to invent a page to fill one.',
          'label' => 'The God-of-the-gaps objection', 'slug' => 'god-of-gaps' ],
        [ 'title' => 'Some hiddenness raises deep questions. This hiddenness raises a typo.',
          'body'  => 'Divine hiddenness deserves a serious answer, and it gets one elsewhere on this site. This page simply moved, or never lived here.',
          'label' => 'Divine hiddenness', 'slug' => 'divine-hiddenness' ],
        [ 'title' => 'Is the page missing because we deleted it, or did we delete it because it was missing?',
          'body'  => 'Euthyphro asked the harder version, about goodness and the gods. Ours has a duller answer: the link is out of date.',
          'label' => 'The Euthyphro dilemma', 'slug' => 'euthyphro-dilemma' ],
        [ 'title' => 'In some other universe, this page exists.',
          'body'  => 'Somewhere in the multiverse, every address resolves. The multiverse cannot be observed, and neither, regrettably, can that page.',
          'label' => 'The multiverse objection', 'slug' => 'multiverse-objection' ],
        [ 'title' => 'Paley found a watch on the heath. You found this.',
          'body'  => 'From a watch, Paley inferred a watchmaker. From an empty page, the only safe inference is an empty page.',
          'label' => 'The design argument', 'slug' => 'evolution-explains-design' ],
        [ 'title' => 'The hard problem of consciousness remains unsolved. The easy problem of this page is solved: it does not exist.',
          'body'  => 'Explaining subjective experience defeats the best neuroscience. Explaining this error takes one line of server log.',
          'label' => 'The hard problem of consciousness', 'slug' => 'consciousness-hard-problem' ],
        [ 'title' => 'Ockham\'s razor has been applied. The simplest explanation is a typo.',
          'body'  => 'Entities should not be multiplied beyond necessity. Neither should URLs.',
          'label' => 'Ockham\'s razor', 'slug' => '' ],
    ];
}

/** Excuses with resolved links, ready for the template and the script. */
function ce_404_excuses_resolved(): array {
    $out = [];
    foreach ( ce_404_excuses() as $e ) {
        $url  = home_url( '/articles/' );
        $cta  = 'Browse the arguments';
        if ( $e['slug'] ) {
            $post = get_page_by_path( $e['slug'], OBJECT, 'ce_article' );
            $url  = $post ? get_permalink( $post ) : home_url( '/articles/' . $e['slug'] . '/' );
            $cta  = 'Read the real argument';
        }
        $out[] = [ 'title' => $e['title'], 'body' => $e['body'], 'label' => $e['label'], 'url' => $url, 'cta' => $cta ];
    }
    return $out;
}

/** Enqueue the excuse rotator on 404 pages only. */
function ce_404_enqueue() {
    if ( is_404() ) {
        wp_enqueue_script( 'ce-404', get_stylesheet_directory_uri() . '/assets/js/ce-404.js', [], wp_get_theme()->get( 'Version' ), true );
    }
}
add_action( 'wp_enqueue_scripts', 'ce_404_enqueue' );
