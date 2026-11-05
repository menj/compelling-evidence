=== CE Theme ===
Contributors: menj
Theme URI: https://github.com/menj/compelling-evidence
Author: MENJ
Author URI: https://menj.blog
Requires at least: 6.7
Tested up to: 7.1
Requires PHP: 8.0
Version: 2.18.9
Stable tag: 2.18.9
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Template: twentytwentyfive
Tags: custom-menu, custom-logo, featured-images, rtl-language-support, translation-ready

Child theme of Twenty Twenty-Five for compelling-evidence.com: 14 journeys, a persona quiz and 143 articles on God, evidence and Islam.

== Description ==

CE Theme runs one site, compelling-evidence.com. It is a child of Twenty Twenty-Five and is not built as a general-purpose theme.

A reader takes a 10-question quiz, gets routed to 1 of 14 journeys written for a starting point (atheist, agnostic, scientist, sceptic and so on), and reads on into 143 articles in 11 topics. Everything the theme needs (articles, topics, pages) ships inside it and syncs to the database on activation.

All fonts are self-hosted. With default settings the theme makes no request to another server from a visitor's browser, sets no cookie and tracks nothing.

== Features ==

* 14 journeys, each a stand-alone page with saved progress, a visible FAQ and FAQPage markup.
* A persona quiz in three modes (quick, standard, full) with weighted scoring; it also carries a visible FAQ and FAQPage markup.
* 143 articles and their topic taxonomy (ce_topic), seeded from JSON batch files.
* Reader questions (ce_question) with an Ask a Question form, spam honeypot and nonce.
* A progress page driven by localStorage.
* Votes and resonance buttons under each article.
* A glossary of Islamic and Arabic terms with tooltips, and automatic crosslinks to this site and to 6 other sites.
* One schema.org graph per page (Article, FAQPage, Quiz, LearningResource, QAPage, ImageObject, Quotation and more).
* A built-in lightbox, figure credits and import of registered images into the Media Library.
* Security switches: XML-RPC off, application passwords off, REST changes only from a logged-in browser, and a gambling-spam gate.
* Optional first-party analytics, off until you switch it on.
* Social profile icons, unlisted posts and a configurable reading progress bar.
* RTL Arabic with the KFGQPC HAFS Uthmanic Script for Quran text. Malay (ms_MY) translation included.
* A Theme Options page with 12 tabs.

== Usage ==

Open Appearance > CE Theme Options. Each tab opens with a short note on what it controls and saves on its own. Use the arrow keys to move between tabs.

* General: hero title, subtitle, the Arabic verse with its number, translation and reference, and the footer line.
* Author & Identity: author name, bio, profiles and logo for the byline and the schema.
* Colors: 7 brand colours in a picker grid. A colour left at its default prints nothing.
* Typography: click a card to pick the heading, reading and label faces; pick a size scale.
* Links & Tooltips: crosslinks, links to other sites, murtadd.org counterparts and glossary tooltips. Sliders show the value they will save.
* Media: lightbox, automatic Pexels lead images (off by default, needs your own key) and image import.
* Quiz & Journeys: quiz on or off, answers per question, cards per row.
* Engagement: votes, resonance, home page search, archive page size.
* Analytics: the opt-in switch, scroll and search tracking, retention.
* Performance: parallax, font preloading, the 410 Gone list for retired paths.
* Security: the remote-publishing switches, the spam gate, the login screen design, and the spam scan, password and session tools.
* Tools & Sync: run the article sync by hand, check the batch files, and install the error pages, with the lines to paste into .htaccess.

The analytics report is under CE Analytics in the admin menu.

== Installation ==

1. Install Twenty Twenty-Five. It must be on disk; it need not be active.
2. Upload the theme zip under Appearance > Themes > Add New > Upload Theme, and activate it.
3. Load any admin page once. The theme creates its topics, publishes the articles from inc/articles/ and creates the secondary pages (About, Editorial Policy, Privacy, Contact, FAQ, Glossary, Ask a Question).
4. Go to Settings > Permalinks and click Save Changes.
5. Go to Settings > Reading and pick a static front page if you want one. The theme follows that setting.
6. Check that /articles/ lists the topics and /journeys/ lists 14 paths.

To install by FTP, turn on hidden files in your FTP program, upload the compelling-evidence folder to wp-content/themes/, then activate it under Appearance > Themes and carry on from step 3.

= Updating from the dashboard =

Upload the new zip under Appearance > Themes > Add New > Upload Theme and choose "Replace active with uploaded". Do not switch to another theme first: a switch takes the theme's error pages and its .htaccess sections away. Then load any admin page. The version change runs the article sync, and the theme writes its error pages and .htaccess sections again.

= Updating by FTP =

1. Turn on hidden files in your FTP program. The theme carries files whose names begin with a dot, such as .htaccess, which keep its private files closed to visitors. In FileZilla: Server > Force showing hidden files.
2. Upload the new compelling-evidence folder next to the old one under a temporary name, such as wp-content/themes/compelling-evidence-new. Wait until every file has arrived.
3. Rename the old folder to compelling-evidence-old and, straight after, rename compelling-evidence-new to compelling-evidence. Between the two renames the site has no theme folder and its pages may fail for that moment, so do them one after the other and keep the admin closed until both are done.
4. Load any admin page, check the site, then delete compelling-evidence-old.

If you upload straight over the live folder and the copy stops halfway, leaving one of the theme's PHP files missing or broken, visitors see a short "Back shortly" page with a 503 status instead of an error, and you can still log in. Upload the missing files and the site returns. That page comes from functions.php, so it cannot help while functions.php itself is missing or half written, or in the moment between the two renames above. It checks the PHP files only: a missing stylesheet, font or data file, or a PHP file cut short at a point where it still reads as valid code, slips past it. The temporary-folder method avoids those cases.

Never upload a fresh copy of WordPress itself over the site without keeping wp-config.php. That file holds the database details; without it WordPress opens its installer to anyone who visits.

== Frequently Asked Questions ==

= Does the theme track visitors? =

Not by default. Analytics stays off until you switch it on under Appearance > CE Theme Options > Analytics. See Privacy below for what it records once on.

= How does content sync work? =

inc/ce-content-sync.php builds a sync key from the theme version. A version bump runs a fresh sync on the next admin page load. Edits made to article content in the database are overwritten by the next sync, so edit the JSON batch files instead.

= How do I add new articles? =

Add the article to a batch file in inc/articles/, update manifest.json and its checksum, and bump the theme version. Tools & Sync runs the sync by hand and checks the checksums.

= Where are journey files served from? =

/journey/[slug]/, through page-journey.php, which prints a stand-alone document. The prose sits in journeys/; the shared code is assets/css/journey.css and assets/js/journey.js, printed by inc/ce-journey-assets.php.

= How do I change the hero Arabic verse? =

Appearance > CE Theme Options > General. Change the verse, its number, translation and reference together.

= Why are feed links missing from the page head? =

The theme declares automatic-feed-links, and its feed redirector then removes the links and sends feed URLs to the matching page. The site publishes no feed on purpose.

= Why did the theme delete .git, tests and doc from its folder? =

They are development files, and a web server can hand them to anyone who asks. On a live server the theme removes them on activation and once per version. Until then Apache refuses them through the theme's .htaccess; Nginx does so only if the host has added the rules in doc/readme.md. A site where you work on the theme keeps them: set WP_ENVIRONMENT_TYPE to local or development in wp-config.php, or add define( 'CE_KEEP_DEV_FILES', true );. The documentation is in the theme zip and in the repository.

= A tool I use stopped publishing after an update. =

The Security tab turns off XML-RPC, application passwords and REST changes from outside a logged-in browser, because spam tools use them. Switch off the one your tool needs. Password logins sent through XML-RPC are refused while either the XML-RPC switch or the REST switch is on (the REST switch guards that route too), so a tool that logs in to XML-RPC with a password needs both off. A tool that writes through the REST API with an application password needs both the REST switch and the application-password switch off; one that uses a logged-in browser session needs neither. In an emergency, define( 'CE_SECURITY_LOCK', false ); in wp-config.php lifts all three.

= What are the error pages? =

WordPress shows a plain screen when it is updating, when PHP fails or when the database is down. The theme replaces those three screens with its own, and ships pages for the errors your web server answers itself (403, 500, 502, 503 and 504). Tools & Sync shows whether each is installed and gives the lines for .htaccess.

== Privacy ==

With default settings the theme sets no cookie, stores nothing about visitors and loads nothing from another server.

* Analytics (opt-in). Once switched on, the theme records page views, quiz starts, answers and results, journey progress, article reads and scroll depth, and site search terms in the wp_ce_analytics table. Each row has a salted hash of the IP address (the raw address is not kept), a per-tab session id held in sessionStorage, the page address, the referrer and a device type. Rows older than the retention period (30 days to 1 year) are deleted daily, whether tracking is on or off. Nothing is sent to a third party.
* Votes and resonance store a count per article and a salted IP hash to stop repeat votes.
* Ask a Question stores the name, email address, topic and question as a pending ce_question post.
* Journey and quiz progress lives in the visitor's localStorage. For a logged-in user the quiz result and journey progress are also saved in user meta.
* Automatic images (opt-in) call the Pexels API from the server with your key. Image import copies registered images from Wikimedia Commons, Pexels or Flickr to the Media Library from the server; until an image is imported, its figure loads it from the source site.

== Resources ==

CE Theme, Copyright 2026 MENJ. Licensed GPLv2 or later.

* Parent theme: Twenty Twenty-Five, Automattic, GPLv2 or later.
* Playfair Display, Copyright 2017 The Playfair Display Project Authors, SIL OFL 1.1. Source: Google Fonts. assets/fonts/licenses/Playfair-Display-OFL.txt
* DM Sans, Copyright 2014 The DM Sans Project Authors, SIL OFL 1.1. Source: Google Fonts. assets/fonts/licenses/DM-Sans-OFL.txt
* Cormorant Garamond, Copyright 2015 The Cormorant Project Authors, SIL OFL 1.1. Source: Google Fonts. assets/fonts/licenses/Cormorant-Garamond-OFL.txt
* EB Garamond, Copyright 2017 The EB Garamond Project Authors, SIL OFL 1.1. assets/fonts/licenses/EB-Garamond-OFL.txt
* Special Elite, Astigmatic, Apache License 2.0. assets/fonts/licenses/Special-Elite-LICENSE.txt
* CE Hadith (ce-hadith-*.woff2): Noto Naskh Arabic, The Noto Project Authors, SIL OFL 1.1. assets/fonts/licenses/Noto-Naskh-Arabic-CE-Hadith-OFL.txt
* KFGQPC HAFS Uthmanic Script, King Fahd Glorious Quran Printing Complex, under its own free-distribution licence (assets/fonts/licenses/kfgqpc-hafs-uthmanic-script-LICENSE.txt). It is not GPL-compatible and forbids modification; the owner chose to ship a WOFF2 conversion. Used for Quran text only.
* Sabon Next LT (sabon-next-lt-*.woff2), Monotype, a commercial licence held by the site owner. It is not GPL-compatible and may not be redistributed with the theme outside this site. It is an optional face in Typography; the default faces do not use it.
* Images in assets/images and assets/diagrams (icons, favicons, the Al-Rahman calligraphy, diagrams) and screenshot.png: made for this site, GPLv2 or later.
* Social icons in assets/images/social (X, Facebook, YouTube, Instagram, TikTok, Threads, Bluesky, Mastodon, Telegram, WhatsApp): Minimalist Social Icons Pack 2.8, GPL. These ten come from CC0 sources; the pack's two CC BY 4.0 icons are not bundled. The brand marks belong to their owners.
* Article figures are not bundled. inc/articles/media.json lists each with its source page, creator and licence, and the credit prints under the figure.

== Changelog ==

= 2.18.9 - November 2026 =
* The documentation is up to date with the theme: how to update by FTP, every settings tab, the security switches, the error pages and the files the theme removes from a live server.

= 2.18.8 - November 2026 =
* The footer column headings are level 2, so the 404 page has no skipped heading level.
* The analytics tracker reads the article slug correctly when WordPress sits in a subfolder.
* The web server refuses the doc folder and every Markdown file in the theme, and the production cleanup deletes the doc folder.

= 2.18.7 - November 2026 =
* A theme caught half uploaded shows a short "Back shortly" page (503) instead of an error.
* The theme folder refuses its development files and PHP files before WordPress starts, and wp-config.php is refused too.
* The live-server cleanup takes a proper lock and costs no database query on page views.

= 2.18.6 - November 2026 =
* The Read first strip under the header is a navigation landmark named "Read first", so screen readers can find it and axe passes.
* The most read list falls back to the reading order when none of its cached articles can still be shown.
* On a live server the theme deletes its development files (.git, .claude, tests, bin, tools), and the web server refuses direct requests for them and for core PHP files that never serve a page.
* Visitors who are not logged in can no longer list the site's user accounts.
* /sitemap.xml and /sitemaps.xml lead to the sitemap, the footer's privacy link follows the privacy page set in WordPress, more retired shop addresses answer 410, and links to the main sections end in a slash.

= 2.18.5 - November 2026 =
* The home page reads as one story in four steps: the key questions, your personalised path, where to go next, and the invitation to ask.
* The Top Questions and What's New cards now open the article archive at a list of the most read or the newest articles and answers.

= 2.18.4 - November 2026 =
* The web server error pages work when WordPress sits in a subfolder. The theme writes a copy of each into wp-content/compelling-evidence-errors with the site's home address, and the .htaccess lines on Tools & Sync point there.

= 2.18.3 - November 2026 =
* The error pages are written when the theme is activated, with no button press, and removed when another theme is activated. Pages another theme left behind are replaced.
* A 504 page for the web server, with its .htaccess line.

= 2.18.2 - November 2026 =
* The article on salvation by works answers three more claims: that the Prophet is in hell, what "I do not know what will be done with me" meant at the death of Uthman ibn Maz'un, and what assurance Islam gives a believer.

= 2.18.1 - November 2026 =
* The Islamic dilemma article and two journey FAQ answers no longer call other websites related to this one. Every link stays.
* The structured data names the author as Mohd Elfie Nieshaem Juferi, at https://menj.bio. The visible byline is unchanged.

= 2.18.0 - November 2026 =
* Error pages in the site's design for an update (503), a PHP fault (500) and a lost database (503). Install them from CE Theme Options > Tools & Sync, which shows each as installed, outdated or someone else's file.
* Static 403, 500, 502 and 503 pages for the web server, with the .htaccess lines on the same tab.
* The 404 page tells search engines not to index it.
* Every template and include file stops when it is opened directly, so a request to a theme file can no longer cause a PHP error.

= 2.17.0 - November 2026 =

* The login screen wears the site design: the CE mark, the wordmark, the night palette and your Colors and Typography choices. Switch it off, or set a logo and a line above the form, under Security > Login screen. A login plugin such as LoginPress takes over while it is active.

= 2.16.0 - November 2026 =

* Social icons for the site profiles, in the footer, the [ce_social_icons] shortcode and the Social icons block pattern. Set the URLs under Author & Identity > Social icons.
* Unlist a post, page, article or question from its editor sidebar, Quick Edit or Bulk Edit. It keeps its URL and drops out of the home page, archives, feeds, search, sitemaps, related links and REST lists, with noindex. A list saved by the Unlist Posts plugin carries over.
* The reading progress bar has settings under Engagement: on or off, colour, height, top or bottom, and the post types. It can now show on pages.
* The search base can also be set in Settings > Permalinks. Paged searches redirect to /search/term/page/2/, and rewrite rules flush once per theme version.

= 2.15.1 - November 2026 =

* Every Article in the JSON-LD has an image. A journey's Article and the one Rank Math prints on the quiz page had none. The image is the featured image where the page has one, else the Organization logo when it is at least 1200px wide, else screenshot.png, as on articles already.
* On a single page, an Article without datePublished or dateModified takes them from the page. Journeys had neither. Views with no post get no date.

= 2.15.0 - November 2026 =

* Analytics is opt-in. The tracker, the quiz and journey events and the endpoint stay off until the Analytics tab switches them on; a site that had saved tracking on keeps it. The scroll and search switches now work, and retention purging runs whether tracking is on or off.
* Theme Options rebuilt: cards, keyboard tabs with ARIA roles, toggle switches, selector cards for fonts and link styles, sliders that show their value, a colour picker grid, a usage note on each tab, and a layout that stacks at 782px. Option keys and their effects are unchanged. Values are checked against their type, range and allowed choices on save.
* The page assets (CSS and JS) load only on the Theme Options screen. The inline script is gone.
* Strings in the templates, the options page and two admin notices are translatable. Both admin notices can be dismissed.
* automatic-feed-links declared; post_class on every post loop. wp_safe_redirect for internal redirects; request paths unslashed before use.
* Licence header in style.css, a Resources section, OFL files for Playfair Display, DM Sans, Cormorant Garamond and Noto Naskh Arabic, and a .gitattributes that keeps dev files out of release zips.

= 2.14.0 - November 2026 =
* Mobile and accessibility pass, done to two owner briefs of 4 November 2026: the mobile-first audit and the WCAG 2.2 AA policy. Pages were measured at 9 widths from 320px to 1280px with axe-core. No page scrolled sideways before or after.
* Small labels, counts and excerpts on the purple and dark grounds now reach 4.5:1. They sat between 1.3:1 and 4.4:1, on the article index, search, articles, the journey list and every journey chapter. Same hues, higher alpha, so the colours shift a little on desktop as well.
* The journey Continue button had no styles and stood 23px high. It now matches the site's teal button at 44px. The closing links on each journey are no longer browser blue.
* The menu button is 44px square and names the menu it opens (aria-controls). Mobile menu links, footer links, topic filters and the hero chips are 44px high below 1025px. The home search field uses 16px text, so iOS stops zooming into it.
* The vote buttons under articles have names (Helpful, Not helpful). Collapsed FAQ answers and the hidden scroll hint on the home page no longer take keyboard focus. The current chapter dot carries aria-current.
* Every page starts with a Skip to content link, and it moves keyboard focus into the content (each main element takes tabindex -1). The journeys had no main landmark; the chapters and the FAQ now sit inside one.
* Anchor jumps stop below the fixed header and the featured strip. main.js sets the offset from their real height, so it shrinks when the strip hides on scroll.
* On phones, a focused quiz option clears the sticky Back and Next bar.
* The journeys and the quiz preload Playfair Display 900 and DM Sans 400. The heading reflowed when they arrived (layout shift up to 0.4 at 768px).

= 2.13.0 - November 2026 =
* The 14 journey pages and the quiz end with a short FAQ section, 4 or 5 questions each, in native details and summary elements. Styles are in assets/css/standalone-faq.css and use each page's own colours.
* Each of those pages carries a FAQPage built from the same file as the visible section, inc/data/standalone-faq.json. It joins the page's one graph by @id. The standalone pass no longer strips FAQPage, and the old FAQPage nodes in four journey.json files are gone.
* The site FAQ now says what the quiz records: a start, the suggested path and mode on completion, and how far you got if you leave early. Your answers stay in your browser.
* tests/schema-graph-test.php requires one FAQPage on every journey and on the quiz, and checks that each question and answer appears in the page's visible HTML.

= 2.12.0 - November 2026 =
* Every page prints one JSON-LD graph. The FAQ, glossary and journey templates no longer print blocks of their own, and with Rank Math active the theme nodes go into Rank Math's graph.
* New markup for what the pages show: QAPage on answered reader questions, Quiz on the quiz, LearningResource with ordered chapters on the 14 journeys, ItemList on topic pages, ContactPage, AboutPage and SearchResultsPage, header, footer, menus and the article sidebar.
* Article figures and diagrams are ImageObject nodes with their credit and licence. The 423 Qur'an and hadith blocks are Quotation nodes, Arabic and English, tied to their surah or hadith collection.
* No schema type was removed (owner rule of 3 November 2026). tests/schema-graph-test.php checks the graph.
* A search URL keeps its query intact in every @id. The theme no longer adds a slash after the query.
* The main menu markup lists the submenu links the header prints, in menu order.
* A reader question whose answer renders to nothing gets no acceptedAnswer.
* The schema test runs on PHP 8.0 and older. It brings its own array_is_list() when PHP lacks one.

= 2.11.0 - November 2026 =
* Every title tag reads "<page title> | Compelling Evidence" and stays under 60 characters, with Rank Math active or not. The site name is never dropped.
* All 143 articles have a short title of 37 characters or fewer in inc/articles/seo.json. 106 of them were too long before.
* The theme no longer writes titles into Rank Math's title field. A title an editor typed there is kept as the first part of the title.
* The hreflang tags are gone. They pointed every page at the home page.
* New image sitemap at /image-sitemap.xml when no SEO plugin is active. The users sitemap is off.
* Reader-question pages print their Article markup in the theme graph, so Rank Math pages carry one Article. No schema type was removed.

= 2.10.1 - November 2026 =
* Links to other sites follow their new addresses, which carry no verse numbers, counts or years.

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
under a shared marker), repairs it hourly from the admin and removes it when switched off or
when the theme is switched. Refused XML-RPC requests are counted with one small row write each. See
doc/upgrading.md.

= 2.7.0 - October 2026 =
Security tab under CE Theme Options, ported from the Abrahamic theme after the October 2026 attack
on abrahamic-religions.com: XML-RPC off, application passwords off, REST writes only from a
browser session, gambling spam and remote publishing held as Pending, a spam scan that moves
spam to the trash, 410 Gone for removed spam, reports and a security log. See doc/upgrading.md.

= 2.6.43 - October 2026 =
Content review fixes: author credits restored on the journey pages (C.S. Lewis, Alvin Plantinga,
Sam Harris), hadith references and gradings corrected against shamela.ws, Quran blocks show their
full verse ranges, the hidden-treasure saying is presented as having no chain, the FAQ describes
the analytics truthfully, the cosmological-constant claim is corrected, and the untranslated
ms-MY journey copies are removed so the English pages are served.
Articles that murtadd.org entries name as their counterpart end with a "Read this on murtadd.org"
card (data in inc/data/murtadd-counterparts.json, built by bin/build-murtadd-counterparts.py;
switch in CE Theme Options → Links & Tooltips).

= 2.6.42 - October 2026 =
The Quran face is served as a woff2 conversion (105 KiB, from 291 KiB) in place of the original
TTF, by the owner's decision, accepting that the font's licence forbids modifying it.
Review fixes: disabled topics hold for answered questions, one outside link per list item,
glossary tooltips load on answered questions, and the hero ayah number follows Theme Options.

= 2.6.41 - October 2026 =
Quranic verse text is fitted to the KFGQPC encoding as it renders (sukun, the rounded zero, and
one ayah-number medallion in place of brackets round a medallion). Stored text is unchanged.

= 2.6.40 - October 2026 =
Quranic verses, and only Quranic verses, are set in the original KFGQPC HAFS Uthmanic Script
TTF, distributed unmodified under its licence. It replaces the subset uthmani-quran.woff2.
Hadith and other Arabic keep CE Hadith.

= 2.6.39 - October 2026 =
Contextual links to other sites: the first mention of a phrase another site
treats now links to that inner page, alongside the existing links to this site's own articles.
One shared phrase map, inc/data/network-links.json. Answered
questions get crosslinks too. Theme Options styles move to their own stylesheet.

= 2.6.38 - October 2026 =
Content release: five new articles (batch 009, 142 in total) on the Prophet's marriages, alleged
Quran contradictions, Dhul-Qarnayn and the muddy spring, the selective use of hadith, and the
crucifixion; six existing articles gain sections answering the specific forms of each objection.

= 2.6.37 - October 2026 =
Content release: six new articles (batch 008, 137 in total) answering the strongest recurring
charges in Christian polemic: Haman and Esther, the moon god claim, the first revelation and the
possession charge, the killing of critics, Jesus as Word of God, and Muhammad in the Bible.

= 2.6.36 - October 2026 =
Rank Math integration and content compliance. Rank Math owns titles, descriptions, canonicals and
schema; the theme fills only empty values. Every article gets a focus keyword, SEO title and
description in Rank Math's empty fields, two more images, shorter paragraphs and keyword-bearing
captions. Tested with Rank Math 1.0.279: all 131 articles score 90 to 95 with the Content AI
module off. Fixes a content-sync fault that could trash every article in a batch.

= 2.6.35 - September 2026 =
Automatic lead images. Every hour, new articles without an image get a Pexels photograph chosen
from their title, checked against the site's image rules, imported, credited and published as the
lead figure and featured image. Registered images also import on the same schedule, so deploys
need no clicks. Theme Options → Media lists each automatic choice with Replace and Remove.

= 2.6.34 - September 2026 =
Every one of the 131 articles now opens with a licensed lead image (124 from Pexels, 7 from
Wikimedia Commons), each with caption and credit, and uses it as its featured image on cards and
in social shares. Photographers are linked in the credit line. Import runs 20 images per click.

= 2.6.33 - September 2026 =
Article media: figures with licensed images (Wikimedia Commons, Pexels, Flickr) and automatic credit
lines, inline SVG diagrams in the theme's colours, responsive tables, and a native lightbox that
replaces the Lightbox2 plugin. Pretty search URLs built in (replaces Pretty Search Permalinks).
Contact Form 7 forms take the theme's design and load their scripts only on pages with a form.
The eight articles of batch 007 gain figures, diagrams and tables. New Media tab in Theme Options.

= 2.6.32 - September 2026 =
Code-standards pass. PHP 8.0 to 8.4 compatibility and the WordPress security, database and
deprecation sniffs now pass with no errors. Output is escaped where it is printed, form and
server input is unslashed and sanitised, custom-table queries use the %i identifier
placeholder, deprecated and dead code removed. Verified on WordPress 7.1.2 with WP_DEBUG on.
Adds phpcs.xml.dist.

= 2.6.31 - September 2026 =
Search guidelines pass against Google's SEO Starter Guide and its list of supported structured
data. Removed markup Google does not support or that described content not on the page
(front-page and per-article FAQPage, QAPage on single-answer questions, table-of-contents
ItemList, Speakable, the retired sitelinks SearchAction). Meta descriptions for every page type,
130 characters including a call to action; no duplicate description or canonical tags when Rank
Math is active. Topic pages get their own heading; journey pages get a correct canonical and one
h1; duplicate placeholder pages redirect to their topic; robots.txt no longer blocks /wp-includes/.

= 2.6.30 - September 2026 =
Desktop, tablet and mobile pass, tested on a local WordPress 7.1.2 install with all 131
articles at six widths (1440 to 360px). Journey pages now collapse to one column on phones
(the stylesheet that should have done this, journey-base.css, never existed). The closed
mobile menu no longer shows through the header. The article sidebar keeps the table of
contents and reading progress in view for the whole article, and the progress bar now sits
under the header on every screen size. Larger tap targets and a 12px minimum text size on
phones.

= 2.6.29 - September 2026 =
New 404 page: "Exhibit 404 · Evidence not found". Opens on one of twelve excuses, each a joke
on an argument the site takes seriously, linking to the article that treats it properly.
"Hear another excuse" cycles them. Recent articles listed below the search.

= 2.6.28 - September 2026 =
Parent-theme compatibility with Twenty Twenty-Five 1.5. CE's PHP templates now always render
on the front end (the parent's block templates had priority for index, home, single, page,
search, archive and 404). theme.json maps the parent's colour and font presets onto CE's design,
so anything left unstyled inherits CE's look. Minimum WordPress raised to 6.7 to match the parent.

= 2.6.27 - September 2026 =
Table of contents fixed: links now scroll to their section, the current section is
highlighted, and #section-N deep links work. The TOC script moves out of the templates into
assets/js/ce-toc.js. Smooth scrolling now respects the reduced-motion preference.

= 2.6.26 - September 2026 =
Homepage design fixes. Quick-access card titles now use the heading style (the CSS targeted
h3 while the markup used h2). Card text, links and icons meet contrast targets. Article cards
without a featured image show a topic line icon on a 16:9 panel in place of emoji.

= 2.6.25 - September 2026 =
Theme identity: name changed to CE Theme, Theme URI set to the GitHub repository
(https://github.com/menj/compelling-evidence), author set to MENJ (https://menj.blog). Folder name and
text domain stay compelling-evidence.

= 2.6.24 - September 2026 =
Adds Sabon Next LT (licensed) as a heading and reading option, in regular, italic, bold and
bold italic. Sabon lacks the dotted consonants and ʿ ʾ used in transliteration; those six
characters and their capitals are drawn from EB Garamond, scaled to Sabon's proportions.

= 2.6.23 - September 2026 =
Typography release. Adds EB Garamond (variable, 400-800, roman and italic) as a heading and
reading option and Special Elite as an optional typewriter face for labels and eyebrows. New
inc/ce-fonts.php assigns families to roles through CSS custom properties. The Heading Font and
Reading Body Font settings, which were saved but never applied, now take effect, and the
Preload critical fonts setting is now honoured.

= 2.6.22 - September 2026 =
Content release. Eight new articles (batch-007.json, total 131) answering claims from recent
Christian and ex-Muslim critique: the Islamic Dilemma, Quran 9:5 and 9:29, terrorism data,
the Quran and the Jews, Quran 4:34, divine love, salvation by mercy, and Muslim citizenship in
Western democracies. Nine new glossary terms (93 total), 18 new cross-link phrases, and the new
slugs placed in the /articles/ subcategory map.

= 2.6.21 - September 2026 =
Stability release. Custom tables are now created with a plain CREATE TABLE IF NOT EXISTS
query (new inc/ce-db.php) instead of dbDelta(), so the theme no longer requires
wp-admin/includes/upgrade.php or schema.php. Engagement table check moved from front-end
init to admin_init. Question-status terms are seeded once in admin (flag: ce_qstatus_seeded)
instead of on every request. New inc/ce-gone.php answers retired spam URL prefixes with a
410 Gone at init, configurable under Theme Options → Performance → Retired URLs.

= 2.6.10 - April 2026 =
Architectural change: ~650-word "What this site is" mission section migrated off the
front page and onto the About page. Front page now flows hero → quick-access cards →
articles. New one-time upgrade hook (ce_update_about_page_v2, flag: ce_about_v2_updated)
refreshes the existing About page content on existing installs via wp_update_post.
See doc/upgrading.md for details on opting out.

= 2.6.0 through 2.6.9 - April 2026 =
Progressive corpus-wide editorial overhaul (Phase 1 + Phase 2.1 through 2.6). 58 of 123
articles deep-edited across seven topics now complete: Islamic Practice & Ritual (6),
Divine Justice & Fairness (5), Ethics Without God (5), The Problem of Evil (9), Does God
Exist? (12), History/Context/Comparison (10), Revelation & Meaning (11). Editorial pass
applies anti-AI prose voice, contrastive-negation removal, banned-word audit, em-dash
reduction (typically from 20-30 per article down to 2-5), Quran/hadith citation
strengthening (many articles previously had zero or insufficient citations), and 6+
minute reading time minimum. See doc/changelog.md for per-article detail.

= 2.5.1 - April 2026 =
Logic Over Faith content: 3 new articles + 3 expansions.

= 2.5.0 - April 2026 =
Bug fixes: term_exists cache miss recovery added to both sync branches; topic assignments
now complete correctly on Redis/Memcached hosts. Manual sync button ($force_run=true)
bypasses object cache reliably. articles-data-5.php backup topics corrected (6 articles
restored to Islamic Practice & Ritual). manifest.json version field corrected.

= 2.4.9 - April 2026 =
Feed suppression (CE_Feed_Redirector): 301 redirects all feed URLs, removes
autodiscovery links, appends robots.txt Disallow rules, sends noindex header.
Q&A page CSS restored (dropped in 2.4.8). Q&A topic sidebar in canonical order.

= 2.4.8 - April 2026 =
Content sync fixes (ce-content-sync.php). Windsurf workflow file added.

= 2.4.0 - April 2026 =
Critical fix: manual sync button was setting the version key, silently blocking the automatic
admin_init sync on every subsequent page load. ce_manual_content_sync() rewritten to clear
the version key and delegate to ce_sync_article_content(), one sync implementation for both
code paths. ce_fallback_sync() removed.

= 2.3.8 - April 2026 =
Bug fix: coming-back-after-leaving missing from /articles/ (was not listed in Inner Journey
subcategory slug array). Stale topic descriptions for retired categories removed from archive.

= 2.3.7 - April 2026 =
Bug fixes: retired topics (Islamic Beliefs & Practice, Rights, Freedoms & Hard Questions) now
deleted on sync. Slug rename handled in place, so no more duplicate articles. Orphan detection
added: ce_article posts not in JSON are automatically trashed. Topic migration map extended
to cover v2.3.6 retired categories.

= 2.3.6 - April 2026 =
Favicons and CE admin icon added (self-hosted SVG + PNG set). Category consolidation:
Islamic Beliefs & Practice and Rights, Freedoms & Hard Questions retired; articles
reassigned to 10 canonical topics. Article slug renamed: if-god-answers-prayer → 
does-god-answer-prayer. All 5 batches redistributed to exactly 24 articles each.

= 2.3.5 - April 2026 =
Security: Removed publicly accessible diagnose.php (no auth, leaked server paths). Replaced
with inline Article Data Diagnostics panel in Theme Options → Tools & Sync, gated by
manage_options. Panel shows per-batch checksum verification, loader health, and sync key status.

= 2.3.4 - April 2026 =
Bug fixes: manifest.json checksums were stale causing content sync to never fire and articles
to appear empty. build-articles.php checksum logic fixed (was computing hash before embedding
meta.checksum, producing a permanently mismatched manifest on every build run). Batch collision
fixed: articles-data-6.php now maps to batch-006 instead of overwriting batch-005.

= 2.3.3 - April 2026 =
Topic migration map added to sync and theme options import: deprecated topic names remapped to
current categories, preventing orphaned articles. Archive page updated with current topic names.

= 2.3.1 - April 2026 =
Category restructure: 12 canonical topics replacing 10. New categories: Divine Justice &
Fairness, Islamic Beliefs & Practice, Rights, Freedoms & Hard Questions, Revelation & Meaning.
New article: Why Would God Create Enemies? (order 120). Auto-topic creation and cleanup in sync.

= 2.3.0 - April 2026 =
Security: JSON article storage replaces PHP data files (no code execution risk). SHA256
checksums verify integrity. CE_Article_Loader class with tamper detection. Q&A custom post
type. Content sync safeguards (lock, time/memory limits, completeness check).

= 2.2.78 - April 2026 =
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

For the full version history see doc/changelog.md in the theme zip.

== Upgrade Notice ==

= 2.16.0 =
Four plugin features are built in. Deactivate Reading Progress Bar, Unlist Posts & Pages and Pretty Search Permalinks once you have checked the theme settings; until then the theme leaves those jobs to the plugins.

= 2.15.1 =
Structured data fix. Nothing needs doing.

= 2.15.0 =
Analytics is now off unless the Analytics tab was saved with tracking on. To keep collecting, open Appearance > CE Theme Options > Analytics and switch it on.

= 2.7.1 =
Upload and load any admin page once. On Apache the theme writes the xmlrpc.php block into .htaccess.

= 2.5.0 =
Upload and activate. Content sync fires automatically on first admin page load.
Topic categories will be correctly assigned including Islamic Practice & Ritual.
No manual steps required.

= 2.2.78 =
Content sync runs automatically on first admin page load. No manual DB changes required.
Upgrading from below 2.2.0: run the category migration. See doc/upgrading.md.
