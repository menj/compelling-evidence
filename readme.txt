=== CE Theme ===
Contributors: menj
Theme URI: https://github.com/menj/compelling-evidence
Author: MENJ
Author URI: https://menj.blog
Requires at least: 6.7
Tested up to: 7.1
Requires PHP: 8.0
Version: 2.12.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Template: twentytwentyfive
Tags: custom-menu, custom-logo, featured-images, rtl-language-support, translation-ready

A child theme of Twenty Twenty-Five for compelling-evidence.com — an Islamic apologetics site
delivering personalised inquiry journeys for 14 intellectual starting points, 131 articles,
a weighted persona quiz, and a full engagement layer.

== Description ==

Compelling Evidence is a purpose-built WordPress child theme. It is not a general-purpose theme
and is not submitted to the WordPress.org theme directory. This readme.txt is included for
structural compliance inside the .zip distribution package.

The theme delivers:

* 14 self-contained persona journey paths (Volume I complete)
* 131 articles across 11 topic categories, auto-published on activation
* 10-question weighted persona quiz with 6-layer scoring algorithm
* Gamified progress dashboard driven by localStorage
* Privacy-first server-side analytics (IP-hashed, no raw storage)
* Voting, resonance feedback, and 9-platform social sharing
* 85-term Islamic/Arabic glossary with hover/click tooltip auto-linking
* 197 phrase-to-article crosslink mappings with enforced ground rules
* 8-tab admin Theme Options panel (Appearance > CE Theme Options)
* RTL Arabic support with self-hosted KFGQPC HAFS Uthmanic Script (Quranic verses only) and CE Hadith fonts
* Malay (ms-MY) translation included

== Installation ==

1. Install Twenty Twenty-Five parent theme (must be present on disk, need not be active).
2. Upload compelling-evidence.zip via Appearance > Themes > Add New > Upload Theme.
3. Activate the theme. On first admin page load four auto-run modules fire automatically:
   - Category migration (creates 10 taxonomy terms, remaps articles)
   - Content sync (publishes all 131 articles from the JSON batch files to the database)
   - Secondary pages creation (About, Editorial Policy, Privacy, Contact, FAQ, Glossary,
     Ask a Question)
   - Next articles creation (ce_create_next_articles — publishes orders 100+)
4. Go to Settings > Permalinks and click Save Changes to flush rewrite rules.
5. Go to Settings > Reading and set a static front page.
6. Verify: /articles/ shows all 10 topic categories; /journeys/ shows all 14 paths.

= Server configuration =

The .htaccess-performance file in the theme root contains Apache mod_deflate/mod_brotli
compression rules and mod_expires cache headers. These must be manually merged into the
WordPress root .htaccess — the theme cannot activate them automatically.

== Frequently Asked Questions ==

= How does content sync work? =

ce-content-sync.php derives a sync key from the theme version in style.css. Each version bump
triggers a fresh sync on next admin page load. Manual DB edits to article content will be
overwritten on the next version deploy.

= How do I add new articles? =

Add entries to inc/articles-data-next.php using nowdoc format (slug, title, topic, order,
excerpt, content). Bump the theme version in style.css — content sync publishes automatically.

= Where are journey files served from? =

/journeys/[slug]-journey.html. Served via page-journey.php which bypasses wp_head/wp_footer.
Each file holds the prose, its own few styles and a CE_JOURNEY data object. The shared code is in
assets/css/journey.css and assets/js/journey.js, printed by inc/ce-journey-assets.php.

= How do I change the hero Arabic verse? =

Appearance > CE Theme Options > General tab. No template editing required.

= What is the .htaccess-performance file? =

Apache rules for gzip/brotli compression and 1-year Cache-Control headers. Must be merged into
the WordPress root .htaccess manually — it is not activated by WordPress automatically.

== Theme Options ==

Appearance > CE Theme Options — 8 tabs:

1. General       Hero title, subtitle, Arabic verse, verse number, translation, reference, footer text
2. Colors        7 color pickers injected as CSS custom properties via wp_head
3. Typography    Heading font, reading body font and label font (Playfair Display, Cormorant
                  Garamond, EB Garamond, Sabon Next LT, DM Sans, Special Elite), scale (90-120%)
4. Links &       Crosslinks and glossary tooltips: enable/disable, max counts, link styles, colors,
   Tooltips      Arabic display toggle, trigger method (hover/click)
5. Quiz &        Quiz enable/disable, max selections per question, journey progress badges, cards per row
   Journeys
6. Engagement    Toggle voting, resonance, crosslinks, search; articles per page
7. Analytics     Toggle tracking, scroll depth logging, search query logging; data retention; auto-purge
8. Performance   Toggle parallax, font preloading, inline script minification;
                  Retired URLs (410 Gone path prefixes)
9. Security      XML-RPC, application password and REST locks; spam gate;
                  spam scan and trash; reports and security log

== Changelog ==

= 2.12.0 - November 2026 =
* Every page prints one JSON-LD graph. The FAQ, glossary and journey templates no longer print blocks of their own, and with Rank Math active the theme nodes go into Rank Math's graph.
* New markup for what the pages show: QAPage on answered reader questions, Quiz on the quiz, LearningResource with ordered chapters on the 14 journeys, ItemList on topic pages, ContactPage, AboutPage and SearchResultsPage, header, footer, menus and the article sidebar.
* Article figures and diagrams are ImageObject nodes with their credit and licence. The 423 Qur'an and hadith blocks are Quotation nodes, Arabic and English, tied to their surah or hadith collection.
* No schema type was removed (owner rule of 3 November 2026). tests/schema-graph-test.php checks the graph.

= 2.11.0 - November 2026 =
* Every title tag reads "<page title> | Compelling Evidence" and stays under 60 characters, with Rank Math active or not. The site name is never dropped.
* All 143 articles have a short title of 37 characters or fewer in inc/articles/seo.json. 106 of them were too long before.
* The theme no longer writes titles into Rank Math's title field. A title an editor typed there is kept as the first part of the title.
* The hreflang tags are gone. They pointed every page at the home page.
* New image sitemap at /image-sitemap.xml when no SEO plugin is active. The users sitemap is off.
* Reader-question pages print their Article markup in the theme graph, so Rank Math pages carry one Article. No schema type was removed.

= 2.10.1 - November 2026 =
* Links to the sister sites follow their new addresses, which carry no verse numbers, counts or years.

= 2.10.0 - November 2026 =
* The quiz has three depths. Quick asks 5 questions, Standard 10 and Full 15. You choose on the start screen, and a Quick result can carry on into Standard with your answers kept.
* Every question has a "Not sure, or rather not say" option. A skipped question counts for nothing, and scores are divided by the questions you answered, so the 3 modes sit on one scale.
* The questions are rewritten in a gentler voice and asked easiest first. Each one shows a short topic label. Q4 gains "Nothing really; my questions lie elsewhere" for a believer with no complaint.
* "How does faith sit in your life right now?" takes one answer only. A visitor who said they are a practising or doubting Muslim no longer lands on the Ex-Believer path, which now rises only on "It used to matter a great deal, and now it does not".
* When the answers point to the Questioning Muslim or the Ex-Believer, Standard and Full ask one more question: what weighs on you most. It leaves the path alone and picks 2 or 3 murtadd.org pages for a "You are not alone" list. bin/build-quiz-murtadd-links.py builds the list from murtadd's seed data and fails on any slug that does not exist.
* /quiz/?from=murtadd opens on Quick with a short welcome.
* The result shows the closest path, 2 near paths as links, a note on how the match works, and share, retake and "Try another path". The percentage bars are gone.
* Analytics get quiz_start with the mode and quiz_complete with the path and mode. The quiz no longer sends an event per answer.
* Options are native radio buttons and checkboxes in a fieldset. Focus moves to each new question, the question count is announced, Next is a real disabled button until you answer, and the grey text passes 4.5:1. Ties go to a fixed order written in the quiz data.
* bin/quiz-sim.js runs random visitors through each mode and checks that all 14 paths can be reached.

= 2.9.0 - November 2026 =
* Body text in the journeys is regular weight (400) instead of light (300), so Cormorant Garamond and DM Sans read more clearly on the dark background.
* In the New Atheist journey, three sentences pointed to a chapter whose name had dropped out. They now read "in the Constant chapter" and "earlier".
* The 14 journeys are built from one template. Each persona is a data folder in journeys/data/ (journey.json, its own style.css and one HTML file per chapter, the chapter text unchanged), and template-parts/journey/ prints the head, header, chapter wrappers, Volume II preview and scripts. The old journeys/*-journey.html files are gone. A hand-made HTML file in a locale folder still works.
* Interface text the template and journey.js print sits in journeys/i18n/en.php, so a Malay version needs text only.
* The journeys now load DM Sans, Cormorant Garamond and the Playfair Display weights they use. Before, only Playfair 400 loaded and the rest of the text used system fallbacks, so the pages now look as designed.
* Choice cards, reading-path steps, header dots, the logo and the completion badge work from the keyboard: Tab reaches them, Enter or Space presses them, and focus moves to the new chapter.
* The Volume II preview on the New Atheist and Freethinker journeys showed a blank page. A missing closing tag put it inside the conclusion.
* On phones the meta row under each chapter title wraps, so the Scientist path badge no longer runs past the right edge.
* The quiz sends quiz_abandon with a keepalive fetch when the browser refuses the beacon.

= 2.8.7 - November 2026 =
* Each persona journey now shows one chapter at a time. The stylesheets never hid the inactive chapters, so every journey rendered all of them as one long page, and Next or a sidebar step only jumped back to the top. `journey-base.css`, which every journey loads, now hides every `.screen` except the one `goTo()` marks active.
* When a keyboard user presses a button that moves to another chapter, focus now moves to the new chapter. Before, the button was hidden with its chapter and focus fell back to the page.
* The Freethinker journey's completion badge and share text said "The Agnostic" and now say "The Freethinker".

= 2.8.6 - November 2026 =
The 14 journeys and the quiz load their shared CSS and JS from theme files in assets/css and assets/js,
with a file-time version, in place of the copies pasted into each page. Pages look and behave as before.
The analytics snippet survives blocked storage. See doc/changelog.md.

= 2.8.5 - November 2026 =
* Journey and quiz titles read `<page> | Compelling Evidence` again. 2.8.4 changed the source titles to `Compelling Evidence: X`, which the rewrite in `ce_standalone_html_seo()` no longer matched; it now accepts a dash, colon or pipe and puts the brand last after a pipe.
* A stray `<p.` tag in the closing section of 12 journeys is removed, and the one in the Deist sidebar is now `<p>`.
In the thirteen persona journeys, chapter V of the sidebar opens Constant and chapter VI opens Entropy.
The closing section has valid markup again. See doc/changelog.md.

= 2.8.4 - November 2026 =
The last claims and qualifiers the style pass had cut are back, in the articles and the journeys.
Articles re-sync on the next admin page load. See doc/changelog.md.

= 2.8.3 - November 2026 =
A second pass over the 2.8.2 style rewrite. Claims and qualifiers the rewrite had cut are back, leftover
reframes are rewritten, broken punctuation is fixed, and quotations match their sources again. Articles
re-sync on the next admin page load. See doc/changelog.md.

= 2.8.2 - November 2026 =
Article and journey text follows the owner's writing style: no em dashes or contractions, plain words
for the banned list, and claims stated directly. Quotations and scripture
translations are unchanged. Articles re-sync on the next admin page load. See doc/changelog.md.

= 2.8.1 - November 2026 =
Security fixes: the spam gate exempts only posts with the theme's seed marker, holds WP-CLI publishing,
leaves private posts private, and catches upper-case Cyrillic without mbstring. Failed logins older than
a day drop out of the report, trashed spam answers 410 at /?p=ID, and the Trash button waits for the scan.
See doc/changelog.md.

= 2.7.1 - November 2026 =
"Turn off XML-RPC" now also blocks xmlrpc.php in .htaccess on Apache (a "Block xmlrpc.php" section
shared with the sister themes), repairs it hourly from the admin and removes it when switched off or
when the theme is switched. Refused XML-RPC requests are counted with one small row write each. See
doc/upgrading.md.

= 2.7.0 - October 2026 =
Security tab under CE Theme Options, ported from the Abrahamic theme after the October 2026 attack
on abrahamic-religions.com: XML-RPC off, application passwords off, REST writes only from a
browser session, gambling spam and remote publishing held as Pending, a spam scan that moves
spam to the trash, 410 Gone for removed spam, reports and a security log. See doc/upgrading.md.

= 2.6.43 — October 2026 =
Content review fixes: author credits restored on the journey pages (C.S. Lewis, Alvin Plantinga,
Sam Harris), hadith references and gradings corrected against shamela.ws, Quran blocks show their
full verse ranges, the hidden-treasure saying is presented as having no chain, the FAQ describes
the analytics truthfully, the cosmological-constant claim is corrected, and the untranslated
ms-MY journey copies are removed so the English pages are served.
Articles that murtadd.org entries name as their counterpart end with a "Read this on murtadd.org"
card (data in inc/data/murtadd-counterparts.json, built by bin/build-murtadd-counterparts.py;
switch in CE Theme Options → Links & Tooltips).

= 2.6.42 — October 2026 =
The Quran face is served as a woff2 conversion (105 KiB, from 291 KiB) in place of the original
TTF, by the owner's decision, accepting that the font's licence forbids modifying it.
Review fixes: disabled topics hold for answered questions, one sister link per list item,
glossary tooltips load on answered questions, and the hero ayah number follows Theme Options.

= 2.6.41 — October 2026 =
Quranic verse text is fitted to the KFGQPC encoding as it renders (sukun, the rounded zero, and
one ayah-number medallion in place of brackets round a medallion). Stored text is unchanged.

= 2.6.40 — October 2026 =
Quranic verses, and only Quranic verses, are set in the original KFGQPC HAFS Uthmanic Script
TTF, distributed unmodified under its licence. It replaces the subset uthmani-quran.woff2.
Hadith and other Arabic keep CE Hadith.

= 2.6.39 — October 2026 =
Contextual links across the network: the first mention of a phrase another of the seven sites
treats now links to that inner page, alongside the existing links to this site's own articles.
One shared phrase map, inc/data/network-links.json, identical in all seven themes. Answered
questions get crosslinks too. Theme Options styles move to their own stylesheet.

= 2.6.38 — October 2026 =
Content release: five new articles (batch 009, 142 in total) on the Prophet's marriages, alleged
Quran contradictions, Dhul-Qarnayn and the muddy spring, the selective use of hadith, and the
crucifixion; six existing articles gain sections answering the specific forms of each objection.

= 2.6.37 — October 2026 =
Content release: six new articles (batch 008, 137 in total) answering the strongest recurring
charges in Christian polemic: Haman and Esther, the moon god claim, the first revelation and the
possession charge, the killing of critics, Jesus as Word of God, and Muhammad in the Bible.

= 2.6.36 — October 2026 =
Rank Math integration and content compliance. Rank Math owns titles, descriptions, canonicals and
schema; the theme fills only empty values. Every article gets a focus keyword, SEO title and
description in Rank Math's empty fields, two more images, shorter paragraphs and keyword-bearing
captions. Tested with Rank Math 1.0.279: all 131 articles score 90 to 95 with the Content AI
module off. Fixes a content-sync fault that could trash every article in a batch.

= 2.6.35 — September 2026 =
Automatic lead images. Every hour, new articles without an image get a Pexels photograph chosen
from their title, checked against the site's image rules, imported, credited and published as the
lead figure and featured image. Registered images also import on the same schedule, so deploys
need no clicks. Theme Options → Media lists each automatic choice with Replace and Remove.

= 2.6.34 — September 2026 =
Every one of the 131 articles now opens with a licensed lead image (124 from Pexels, 7 from
Wikimedia Commons), each with caption and credit, and uses it as its featured image on cards and
in social shares. Photographers are linked in the credit line. Import runs 20 images per click.

= 2.6.33 — September 2026 =
Article media: figures with licensed images (Wikimedia Commons, Pexels, Flickr) and automatic credit
lines, inline SVG diagrams in the theme's colours, responsive tables, and a native lightbox that
replaces the Lightbox2 plugin. Pretty search URLs built in (replaces Pretty Search Permalinks).
Contact Form 7 forms take the theme's design and load their scripts only on pages with a form.
The eight articles of batch 007 gain figures, diagrams and tables. New Media tab in Theme Options.

= 2.6.32 — September 2026 =
Code-standards pass. PHP 8.0 to 8.4 compatibility and the WordPress security, database and
deprecation sniffs now pass with no errors. Output is escaped where it is printed, form and
server input is unslashed and sanitised, custom-table queries use the %i identifier
placeholder, deprecated and dead code removed. Verified on WordPress 7.1.2 with WP_DEBUG on.
Adds phpcs.xml.dist.

= 2.6.31 — September 2026 =
Search guidelines pass against Google's SEO Starter Guide and its list of supported structured
data. Removed markup Google does not support or that described content not on the page
(front-page and per-article FAQPage, QAPage on single-answer questions, table-of-contents
ItemList, Speakable, the retired sitelinks SearchAction). Meta descriptions for every page type,
130 characters including a call to action; no duplicate description or canonical tags when Rank
Math is active. Topic pages get their own heading; journey pages get a correct canonical and one
h1; duplicate placeholder pages redirect to their topic; robots.txt no longer blocks /wp-includes/.

= 2.6.30 — September 2026 =
Desktop, tablet and mobile pass, tested on a local WordPress 7.1.2 install with all 131
articles at six widths (1440 to 360px). Journey pages now collapse to one column on phones
(the stylesheet that should have done this, journey-base.css, never existed). The closed
mobile menu no longer shows through the header. The article sidebar keeps the table of
contents and reading progress in view for the whole article, and the progress bar now sits
under the header on every screen size. Larger tap targets and a 12px minimum text size on
phones.

= 2.6.29 — September 2026 =
New 404 page: "Exhibit 404 · Evidence not found". Opens on one of twelve excuses, each a joke
on an argument the site takes seriously, linking to the article that treats it properly.
"Hear another excuse" cycles them. Recent articles listed below the search.

= 2.6.28 — September 2026 =
Parent-theme compatibility with Twenty Twenty-Five 1.5. CE's PHP templates now always render
on the front end (the parent's block templates had priority for index, home, single, page,
search, archive and 404). theme.json maps the parent's colour and font presets onto CE's design,
so anything left unstyled inherits CE's look. Minimum WordPress raised to 6.7 to match the parent.

= 2.6.27 — September 2026 =
Table of contents fixed: links now scroll to their section, the current section is
highlighted, and #section-N deep links work. The TOC script moves out of the templates into
assets/js/ce-toc.js. Smooth scrolling now respects the reduced-motion preference.

= 2.6.26 — September 2026 =
Homepage design fixes. Quick-access card titles now use the heading style (the CSS targeted
h3 while the markup used h2). Card text, links and icons meet contrast targets. Article cards
without a featured image show a topic line icon on a 16:9 panel in place of emoji.

= 2.6.25 — September 2026 =
Theme identity: name changed to CE Theme, Theme URI set to the GitHub repository
(https://github.com/menj/compelling-evidence), author set to MENJ (https://menj.blog). Folder name and
text domain stay compelling-evidence.

= 2.6.24 — September 2026 =
Adds Sabon Next LT (licensed) as a heading and reading option, in regular, italic, bold and
bold italic. Sabon lacks the dotted consonants and ʿ ʾ used in transliteration; those six
characters and their capitals are drawn from EB Garamond, scaled to Sabon's proportions.

= 2.6.23 — September 2026 =
Typography release. Adds EB Garamond (variable, 400-800, roman and italic) as a heading and
reading option and Special Elite as an optional typewriter face for labels and eyebrows. New
inc/ce-fonts.php assigns families to roles through CSS custom properties. The Heading Font and
Reading Body Font settings, which were saved but never applied, now take effect, and the
Preload critical fonts setting is now honoured.

= 2.6.22 — September 2026 =
Content release. Eight new articles (batch-007.json, total 131) answering claims from recent
Christian and ex-Muslim critique: the Islamic Dilemma, Quran 9:5 and 9:29, terrorism data,
the Quran and the Jews, Quran 4:34, divine love, salvation by mercy, and Muslim citizenship in
Western democracies. Nine new glossary terms (93 total), 18 new cross-link phrases, and the new
slugs placed in the /articles/ subcategory map.

= 2.6.21 — September 2026 =
Stability release. Custom tables are now created with a plain CREATE TABLE IF NOT EXISTS
query (new inc/ce-db.php) instead of dbDelta(), so the theme no longer requires
wp-admin/includes/upgrade.php or schema.php. Engagement table check moved from front-end
init to admin_init. Question-status terms are seeded once in admin (flag: ce_qstatus_seeded)
instead of on every request. New inc/ce-gone.php answers retired spam URL prefixes with a
410 Gone at init, configurable under Theme Options → Performance → Retired URLs.

= 2.6.10 — April 2026 =
Architectural change: ~650-word "What this site is" mission section migrated off the
front page and onto the About page. Front page now flows hero → quick-access cards →
articles. New one-time upgrade hook (ce_update_about_page_v2, flag: ce_about_v2_updated)
refreshes the existing About page content on existing installs via wp_update_post.
See doc/upgrading.md for details on opting out.

= 2.6.0 through 2.6.9 — April 2026 =
Progressive corpus-wide editorial overhaul (Phase 1 + Phase 2.1 through 2.6). 58 of 123
articles deep-edited across seven topics now complete: Islamic Practice & Ritual (6),
Divine Justice & Fairness (5), Ethics Without God (5), The Problem of Evil (9), Does God
Exist? (12), History/Context/Comparison (10), Revelation & Meaning (11). Editorial pass
applies anti-AI prose voice, contrastive-negation removal, banned-word audit, em-dash
reduction (typically from 20-30 per article down to 2-5), Quran/hadith citation
strengthening (many articles previously had zero or insufficient citations), and 6+
minute reading time minimum. See doc/changelog.md for per-article detail.

= 2.5.1 — April 2026 =
Logic Over Faith content: 3 new articles + 3 expansions.

= 2.5.0 — April 2026 =
Bug fixes: term_exists cache miss recovery added to both sync branches — topic assignments
now complete correctly on Redis/Memcached hosts. Manual sync button ($force_run=true)
bypasses object cache reliably. articles-data-5.php backup topics corrected (6 articles
restored to Islamic Practice & Ritual). manifest.json version field corrected.

= 2.4.9 — April 2026 =
Feed suppression (CE_Feed_Redirector): 301 redirects all feed URLs, removes
autodiscovery links, appends robots.txt Disallow rules, sends noindex header.
Q&A page CSS restored (dropped in 2.4.8). Q&A topic sidebar in canonical order.

= 2.4.8 — April 2026 =
Content sync fixes (ce-content-sync.php). Windsurf workflow file added.

= 2.4.0 — April 2026 =
Critical fix: manual sync button was setting the version key, silently blocking the automatic
admin_init sync on every subsequent page load. ce_manual_content_sync() rewritten to clear
the version key and delegate to ce_sync_article_content() — one sync implementation for both
code paths. ce_fallback_sync() removed.

= 2.3.8 — April 2026 =
Bug fix: coming-back-after-leaving missing from /articles/ (was not listed in Inner Journey
subcategory slug array). Stale topic descriptions for retired categories removed from archive.

= 2.3.7 — April 2026 =
Bug fixes: retired topics (Islamic Beliefs & Practice, Rights, Freedoms & Hard Questions) now
deleted on sync. Slug rename handled in place — no more duplicate articles. Orphan detection
added: ce_article posts not in JSON are automatically trashed. Topic migration map extended
to cover v2.3.6 retired categories.

= 2.3.6 — April 2026 =
Favicons and CE admin icon added (self-hosted SVG + PNG set). Category consolidation:
Islamic Beliefs & Practice and Rights, Freedoms & Hard Questions retired; articles
reassigned to 10 canonical topics. Article slug renamed: if-god-answers-prayer → 
does-god-answer-prayer. All 5 batches redistributed to exactly 24 articles each.

= 2.3.5 — April 2026 =
Security: Removed publicly accessible diagnose.php (no auth, leaked server paths). Replaced
with inline Article Data Diagnostics panel in Theme Options → Tools & Sync, gated by
manage_options. Panel shows per-batch checksum verification, loader health, and sync key status.

= 2.3.4 — April 2026 =
Bug fixes: manifest.json checksums were stale causing content sync to never fire and articles
to appear empty. build-articles.php checksum logic fixed (was computing hash before embedding
meta.checksum, producing a permanently mismatched manifest on every build run). Batch collision
fixed: articles-data-6.php now maps to batch-006 instead of overwriting batch-005.

= 2.3.3 — April 2026 =
Topic migration map added to sync and theme options import — deprecated topic names remapped to
current categories, preventing orphaned articles. Archive page updated with current topic names.

= 2.3.1 — April 2026 =
Category restructure: 12 canonical topics replacing 10. New categories: Divine Justice &
Fairness, Islamic Beliefs & Practice, Rights, Freedoms & Hard Questions, Revelation & Meaning.
New article: Why Would God Create Enemies? (order 120). Auto-topic creation and cleanup in sync.

= 2.3.0 — April 2026 =
Security: JSON article storage replaces PHP data files (no code execution risk). SHA256
checksums verify integrity. CE_Article_Loader class with tamper detection. Q&A custom post
type. Content sync safeguards (lock, time/memory limits, completeness check).

= 2.2.78 — April 2026 =
17 new articles (orders 102-119). 16 articles expanded to 700+ words. divine-hiddenness
rewritten (340w to 1,108w). Glossary: 53 to 85 terms. Crosslinks: 197 mappings, zero orphans.
Performance: LCP font preload, conditional templates.css, Cache-Control headers, GTM defer,
forced reflow fix. Accessibility: <main> landmark on 9 templates, 6 contrast fixes, heading
order, aria-labels. SEO: programmatic robots.txt, WordPress dynamic sitemap, descriptive links.
Bug fix: ce_create_next_articles assigns correct topic per article.

= 2.2.77 =
Unified crosslink + tooltip engine (single-pass, 428 lines). Links & Tooltips tab in Theme
Options (8th tab, 13 configurable fields).

= 2.2.76 =
Amiri fonts removed (Latin-subset only). CE Hadith (Noto Naskh Arabic) sole Arabic font for
non-Quranic text. Theme Options introduced (7 tabs).

= 2.2.75 =
Uthmani Quran font replaced with KFGQPC HAFS version (608 glyphs). CE Hadith added.

For full version history see CHANGELOG.md.

== Upgrade Notice ==

= 2.7.1 =
Upload and load any admin page once. On Apache the theme writes the xmlrpc.php block into .htaccess.

= 2.5.0 =
Upload and activate. Content sync fires automatically on first admin page load.
Topic categories will be correctly assigned including Islamic Practice & Ritual.
No manual steps required.

= 2.2.78 =
Content sync runs automatically on first admin page load. No manual DB changes required.
Upgrading from below 2.2.0: run the category migration — see UPGRADING.md.

== Credits ==

Parent theme: Twenty Twenty-Five by Automattic (GPL-2.0-or-later).
KFGQPC HAFS Uthmanic Script, King Fahd Glorious Quran Printing Complex (Quranic verses only), served as a woff2 conversion of the original TTF by the owner's decision, although its licence forbids modification.
CE Hadith font: Noto Naskh Arabic (Google, Apache 2.0).
Playfair Display, DM Sans, Cormorant Garamond: Google Fonts (SIL Open Font License).
All fonts self-hosted — zero external font requests at runtime.

== Privacy ==

The theme includes a privacy-first analytics system (ce-analytics.php) tracking 12 event types
via IP hashing (no raw IP storage). Data stored in a custom WP database table. Retention
configurable (30 days to 1 year), auto-purged via WP-Cron. Google Tag Manager is optional.
