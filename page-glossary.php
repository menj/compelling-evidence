<?php
/**
 * Template Name: Glossary
 * Description: Alphabetical glossary of Islamic terms used throughout the site
 */
get_header();

$terms = function_exists( 'ce_get_glossary_terms' ) ? ce_get_glossary_terms() : [];

// Group by first letter
$grouped = [];
foreach ( $terms as $t ) {
    $letter = strtoupper( mb_substr( $t['term'], 0, 1 ) );
    $grouped[ $letter ][] = $t;
}
ksort( $grouped );
?>

<main class="ce-page ce-glossary" id="main">

  <header class="page-hero glossary-hero">
    <div class="page-hero-inner">
      <p class="section-label">Reference</p>
      <h1>Glossary of Terms</h1>
      <p class="hero-sub">Key concepts from the Islamic intellectual tradition used throughout this site — defined clearly and without jargon.</p>
    </div>
  </header>

  <?php /* Item 15 — Glossary editorial framing. */ ?>
  <aside class="page-intro-context" aria-label="<?php esc_attr_e( 'About this glossary', 'compelling-evidence' ); ?>">
    <div class="page-intro-inner">
      <p>
        <?php esc_html_e( 'Specialised vocabulary makes any tradition harder to engage with from outside. The Islamic intellectual tradition uses a precise technical vocabulary, much of it Arabic in origin, and the standard English translations of these terms often lose what makes the original specific. This glossary aims to bridge that gap — not by replacing the technical term, but by explaining it in plain English so the term remains usable in argument.', 'compelling-evidence' ); ?>
      </p>
      <p>
        <?php esc_html_e( 'Each entry includes the Arabic original alongside the transliteration and a short definition oriented to how the term actually functions in classical and contemporary Islamic discourse. The longer definitions situate the term within the conversations where it does real work: theological, legal, ethical, or spiritual. Where the term has been weaponised or distorted in popular usage, that is noted rather than glossed over.', 'compelling-evidence' ); ?>
      </p>
      <p>
        <?php esc_html_e( 'The collection covers eighty-four terms across hadith methodology, ritual practice, theological vocabulary, legal reasoning, spiritual postures, and core doctrinal concepts. Articles on the site auto-link to relevant entries via tooltip when these terms appear in body content; this page is the canonical reference where the full definitions live.', 'compelling-evidence' ); ?>
      </p>
    </div>
  </aside>

  <div class="glossary-body">

    <nav class="glossary-alphabet" aria-label="Jump to letter">
      <?php foreach ( array_keys( $grouped ) as $letter ) : ?>
        <a href="#letter-<?php echo esc_attr( $letter ); ?>"><?php echo esc_html( $letter ); ?></a>
      <?php endforeach; ?>
    </nav>

    <?php foreach ( $grouped as $letter => $letter_terms ) : ?>
      <section class="glossary-group" id="letter-<?php echo esc_attr( $letter ); ?>">
        <h2 class="glossary-letter"><?php echo esc_html( $letter ); ?></h2>
        <?php foreach ( $letter_terms as $t ) : ?>
          <div class="glossary-entry" id="term-<?php echo esc_attr( sanitize_title( $t['term'] ) ); ?>">
            <dt class="glossary-term">
              <?php echo esc_html( $t['term'] ); ?>
              <?php if ( $t['arabic'] ) : ?>
                <span class="glossary-arabic" lang="ar" dir="rtl"><?php echo esc_html( $t['arabic'] ); ?></span>
              <?php endif; ?>
            </dt>
            <dd class="glossary-def"><?php echo wp_kses_post( $t['def_long'] ?? $t['def'] ); ?></dd>
          </div>
        <?php endforeach; ?>
      </section>
    <?php endforeach; ?>

  </div>

<?php
// DefinedTermSet schema markup
$term_schema = [];
foreach ( $terms as $t ) {
    $term_schema[] = [
        '@type'       => 'DefinedTerm',
        'name'        => $t['term'],
        'termCode'    => $t['arabic'] ?: null,
        'description' => wp_strip_all_tags( $t['def_long'] ?? $t['def'] ),
    ];
}
?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "DefinedTermSet",
  "name": "Compelling Evidence Glossary of Islamic Terms",
  "description": "Key concepts from the Islamic intellectual tradition used throughout this site — defined clearly and without jargon.",
  "inLanguage": "en",
  "publisher": {
    "@type": "Organization",
    "name": "Compelling Evidence",
    "url": "https://compelling-evidence.com"
  },
  "hasDefinedTerm": <?php echo wp_json_encode( $term_schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?>
}
</script>


</main>

<?php get_footer(); ?>
