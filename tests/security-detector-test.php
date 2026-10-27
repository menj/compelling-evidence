<?php
/**
 * Self-test for inc/ce-security-detector.php. Run: php tests/security-detector-test.php
 *
 * Exit code 0 when nothing the theme seeds reads as spam (every article in
 * inc/articles/batch-*.json, seo.json and media.json, the secondary and
 * journey pages, the journeys, the glossary, the FAQ and the quiz templates,
 * the topic names) and every sample from the October 2026 attack is caught.
 *
 * @package CE_Theme
 */

define( 'CE_SECURITY_TEST', true );
$root = dirname( __DIR__ );
require $root . '/inc/ce-security-detector.php';

$fail  = 0;
$n     = 0;
$noisy = 0;

/**
 * Score one seeded item; a spam verdict is a false positive.
 *
 * @param string $label   Where it comes from.
 * @param string $title   Title.
 * @param string $slug    Slug.
 * @param string $content Content.
 */
$check = function ( $label, $title, $slug, $content ) use ( &$fail, &$n, &$noisy ) {
	++$n;
	$r = ce_spam_score( (string) $title, (string) $slug, (string) $content );
	if ( $r['spam'] ) {
		++$fail;
		echo 'FALSE POSITIVE (score ' . $r['score'] . '): ' . $label . ' ' . implode( '; ', $r['reasons'] ) . "\n";
	} elseif ( $r['score'] > 0 ) {
		++$noisy;
		echo 'note (score ' . $r['score'] . ', below threshold): ' . $label . ' ' . implode( '; ', $r['reasons'] ) . "\n";
	}
};

// Articles.
$manifest = json_decode( file_get_contents( $root . '/inc/articles/manifest.json' ), true );
$topics   = array();
foreach ( $manifest['batches'] as $batch ) {
	$data = json_decode( file_get_contents( $root . '/inc/articles/' . $batch['file'] ), true );
	foreach ( $data['articles'] as $a ) {
		$check( 'article ' . $a['slug'], $a['title'], $a['slug'], $a['content'] . ' ' . $a['excerpt'] );
		$topics[ $a['topic'] ] = true;
	}
}
foreach ( array_keys( $topics ) as $topic ) {
	$check( 'topic ' . $topic, $topic, '', '' );
}

// Every string in the SEO and media data.
foreach ( array( 'seo.json', 'media.json' ) as $file ) {
	$strings = array();
	$data    = json_decode( file_get_contents( $root . '/inc/articles/' . $file ), true );
	array_walk_recursive(
		$data,
		function ( $v, $k ) use ( &$strings ) {
			if ( is_string( $v ) && ! preg_match( '#^https?://#', $v ) ) {
				$strings[] = $v;
			}
		}
	);
	foreach ( $strings as $i => $text ) {
		$check( $file . ' #' . $i, '', '', $text );
	}
}

// Journeys (HTML, every locale).
$journeys = array_merge( glob( $root . '/journeys/*.html' ), glob( $root . '/journeys/*/*.html' ) );
foreach ( $journeys as $file ) {
	$check( 'journey ' . basename( dirname( $file ) ) . '/' . basename( $file ), '', '', file_get_contents( $file ) );
}

// Seeded pages and front-end text held in PHP: every string literal and every
// piece of inline HTML.
$php_files = array_merge(
	array( $root . '/inc/ce-secondary-pages.php', $root . '/inc/ce-glossary.php', $root . '/functions.php' ),
	glob( $root . '/page-*.php' )
);
foreach ( $php_files as $file ) {
	$text = '';
	foreach ( token_get_all( file_get_contents( $file ) ) as $tok ) {
		if ( is_array( $tok ) && in_array( $tok[0], array( T_CONSTANT_ENCAPSED_STRING, T_INLINE_HTML, T_ENCAPSED_AND_WHITESPACE ), true ) ) {
			$text .= ' ' . stripcslashes( trim( $tok[1], "'\"" ) );
		}
	}
	$check( basename( $file ), '', '', $text );
}
// Seeded page titles.
foreach ( array( 'About This Site', 'Editorial Policy', 'Privacy Policy', 'Contact', 'Ask a Question', 'Frequently Asked Questions', 'Glossary of Terms', 'Find Your Path', 'My Progress', 'Your Journey', 'Journey', 'Rights & Freedom' ) as $t ) {
	$check( 'page title ' . $t, $t, '', '' );
}
echo "Seeded items checked: $n ($noisy below the threshold with a weak match)\n";

// Words from religious and historical writing that must never trip the gate.
$clean = array(
	array( 'Bethlehem and Bethel in Genesis', '' ),
	array( 'The Hebrew alphabet: aleph, bet, gimel', '' ),
	array( 'Beth and Bethany', '' ),
	array( 'Between the Testaments', '' ),
	array( 'A better reading of Isaiah', '' ),
	array( 'What is at stake in the Council of Nicaea', '' ),
	array( 'Stake: burned at the stake for heresy', '' ),
	array( 'A review of the evidence', '' ),
	array( 'Book review: Misquoting Jesus', '' ),
	array( 'The bonus page on injil.help', '<p>See <a href="https://injil.help/bonus/">the bonus page</a>.</p>' ),
	array( 'A time slot for prayer', '' ),
	array( 'The odds of a fine-tuned universe', '' ),
	array( 'Pascal and the wager', '' ),
	array( 'Elizabeth and Judith in the Apocrypha', '' ),
	array( 'The crown of the Dome of the Rock', '' ),
	array( 'Kumar and the Vedas', '' ),
	array( 'Abraham bet his son on God', '' ),
	array( 'Pascal and the odds', 'Betting on God is a bet with good odds, says the wager.' ),
);
foreach ( $clean as $c ) {
	$r = ce_spam_score( $c[0], '', $c[1] );
	if ( $r['spam'] ) {
		++$fail;
		echo "FALSE POSITIVE sample: {$c[0]} " . implode( '; ', $r['reasons'] ) . "\n";
	}
}
echo 'Clean samples checked: ' . count( $clean ) . "\n";

$spam = array(
	array( 'Bet Big Win Big with Bodog Sports Edge', '' ),
	array( 'Exclusive Coolbet Bonus Code Unlocks Big Wins', '' ),
	array( 'Скачать Пинап КЗ: Официальный сайт', '' ),
	array( 'Krok po kroku: Kompleksowy przewodnik po Pelican Casino', '' ),
	array( 'Live-Roulette bei Mr Pacho', '' ),
	array( 'Crash-Games als neuer Trend', '' ),
	array( 'Persoonlijke ervaringen met top 10 online casino’s in Nederland 2026', '' ),
	array( 'Panduan Lengkap 1xBet Download', '' ),
	array( 'Pusulabet 2026 Giriş Adresi', '' ),
	array( 'Sweepstakes Casinos Explained', '' ),
	array( 'Stake Casino Australia', '' ),
	array( 'Sportwetten-Analyse vor dem Spiel', '' ),
	array( 'Connection check jk5g5zw7', '' ),
	array( 'cl-probe', '' ),
	array( 'Mostbet giriş', '' ),
	array( 'Surfplay review', '' ),
	array( 'Crownplay bonus', '' ),
	array( 'Trino Casino', '' ),
	array( 'Syndicate Casino login', '' ),
	array( 'Stellar Spins', '' ),
	array( 'Starda официальный сайт', '' ),
	array( 'Slotspalace', '' ),
	array( 'Sportaza', '' ),
	array( 'Spybet', '' ),
	array( 'Squeenaud', '' ),
	array( 'Spinjo', '' ),
	array( 'Spellwin', '' ),
	array( 'Spinsup', '' ),
	array( 'Spinch', '' ),
	array( 'Spin Samurai', '' ),
	array( 'Slots Gallery', '' ),
	array( 'Najboljše igralne avtomate v Sloveniji', '' ),
	array( 'Casino Oyunlarında Bankroll Yönetimi', '' ),
	array( 'Glücksspiel in der Schweiz', '' ),
	array( 'Ausländische Online Casinos', '' ),
	array( 'Untitled', 'casinos-online-mas-seguros-espana-2026' ),
	array( 'Untitled', 'zahranicne-stavkove-kancelarie-na-slovensku-3' ),
	array( 'Untitled', 'pin-ap-kazino-oficialnyj-sajt' ),
	array( 'Untitled', 'kto-brasil-cassino-online-e-apostas' ),
	array( 'Vavada зеркало', '' ),
	array( 'Melbet app', '' ),
	array( 'Betify login', '' ),
	array( 'Casinozer avis', '' ),
	array( 'AureonPlay', '' ),
	array( 'PlatinCasino', '' ),
	array( 'True Blue Casino', '' ),
	array( 'SydSpin', '' ),
	array( 'TCL99', '' ),
	array( 'Vegas Stars', '' ),
	array( 'Surge Casino', '' ),
	array( '1win', '' ),
	array( 'Best betting odds for poker tonight', '' ),
);
foreach ( $spam as $s ) {
	$r = ce_spam_score( $s[0], $s[1], '' );
	if ( ! $r['spam'] ) {
		++$fail;
		echo "MISSED: {$s[0]} {$s[1]} (score {$r['score']})\n";
	}
}
$r = ce_spam_score( 'A guide', '', '<p>See <a href="https://best-casino.example/">here</a></p>' );
if ( ! $r['spam'] ) {
	++$fail;
	echo "MISSED: gambling link\n";
}
echo 'Spam samples checked: ' . ( count( $spam ) + 1 ) . "\n";
echo $fail ? "FAILED: $fail\n" : "PASS\n";
exit( $fail ? 1 : 0 );
