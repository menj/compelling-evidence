<?php
/**
 * Interface text for the persona journeys, English.
 *
 * The template (template-parts/journey/) and assets/js/journey.js print
 * these. Chapter text, sidebar items and the Volume II links are content
 * and stay in journeys/data/<key>/. A translation copies this file to
 * journeys/i18n/<locale>.php (for example ms-MY.php) and changes the
 * values only; a key it leaves out falls back to English.
 *
 * @since 2.9.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

return [
	'lang' => 'en',

	// Header.
	'logo_first'  => 'Compelling',
	'logo_second' => ' Evidence',

	// Volume II preview chapter.
	'v2_status'   => '✦ In development: coming soon',
	'v2_chapters' => [
		[ 'Chapter 1', 'Criterion', 'What authentic revelation would look like' ],
		[ 'Chapter 2', 'Convergence', 'The case against polytheism' ],
		[ 'Chapter 3', 'Preservation', 'Which text survived transmission?' ],
		[ 'Chapter 4', 'Coherence', 'Internal consistency, no councils' ],
		[ 'Chapter 5', 'Address', 'Which message addresses all humanity?' ],
		[ 'Chapter 6', 'Character', 'The Prophet on the historical record' ],
		[ 'Chapter 7', 'Verification', 'Falsifiable claims, tested' ],
		[ 'Chapter 8', 'Reckoning', 'Where the evidence lands' ],
	],
	'v2_back'     => 'Back to Volume I conclusion',

	// assets/js/journey.js (printed as window.CE_JOURNEY_STRINGS).
	'js' => [
		'siteName'     => 'Compelling Evidence',
		'resumeTitle'  => 'Resume where you left off',
		'resume'       => 'Resume',
		'dismiss'      => 'Dismiss',
		'explore'      => 'Explore →',
		'goDeeper'     => 'Go deeper',
		'complete'     => 'Journey Complete',
		'tapToShare'   => 'Tap to share',
		'shareText'    => 'I followed the evidence honestly on the %s path | %s',
		'copyLink'     => 'Copy link',
		'copied'       => 'Copied!',
		'copyPrompt'   => 'Copy this link:',
		'share'        => 'Share',
		'shareBadge'   => 'Journey complete. Show the share options',
		'pathNames'    => [
			'new-atheist'       => 'The New Atheist',
			'agnostic'          => 'The Agnostic',
			'secular-humanist'  => 'The Secular Humanist',
			'antitheist'        => 'The Antitheist',
			'materialist'       => 'The Materialist',
			'muslim-doubts'     => 'The Questioning Muslim',
			'apatheist'         => 'The Apatheist',
			'deist'             => 'The Deist',
			'scientist'         => 'The Scientist',
			'classical-atheist' => 'The Classical Atheist',
			'ex-believer'       => 'The Ex-Believer',
			'spiritual-seeker'  => 'The Spiritual Seeker',
			'freethinker'       => 'The Freethinker',
			'true-muslim'       => 'The Committed Muslim',
		],
	],
];
