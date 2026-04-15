=== Compelling Evidence ===
Contributors: compelling-evidence
Requires at least: 6.4
Tested up to: 6.7
Requires PHP: 8.0
Version: 2.2.71
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Template: twentytwentyfive
Tags: blog, custom-menu, featured-images, threaded-comments, translation-ready

== Description ==

Compelling Evidence is an evidence-based inquiry theme for WordPress. It delivers 14 personalised argumentation journeys, 100 auto-published articles across 10 investigative categories, a gamified progress system, social sharing, and a returning-visitor dashboard. Built as a child theme of Twenty Twenty-Five.

The site is designed for honest inquiry into the deepest questions — God, existence, consciousness, and purpose — meeting each reader at their specific intellectual starting point through a 10-question intake quiz that routes to a persona-matched journey path.

== Features ==

* 14 persona journey paths — each with 10 screens (7 argument chapters + Resonance + Transmission + Conclusion), 6 reflection choices per chapter (744 total)
* 10-question intake quiz — weighted scoring across all 14 personas, up to 3 selections per question, 3-choice calibrated scoring, dominance weighting, conflict dampening, and near-tie overlap handling
* 100 articles across 10 categories — Does God Exist?, The Problem of Evil, Ethics Without God?, Science & Evidence, Examining the Quran, Examining the Sources, History & Context, Rights & Freedom, The Inner Journey, The Bigger Picture
* Volume I → II bridge — all 13 standard journeys include a Volume II preview section
* localStorage progress saving — resume where you left off, syncs to WP user meta for logged-in users
* 9-platform social sharing, up/down voting, resonance feedback, Most Resonant widget
* Auto table of contents sidebar on articles with 2+ sections, with scroll-spy active highlighting
* Breadcrumbs on article pages (Home › Articles › Topic › Title) with BreadcrumbList schema
* Related articles 3-card grid below each article (same topic)
* 161 automated cross-link phrase mappings across all 100 articles
* FAQ page with accordion UI and FAQPage JSON-LD schema markup (18 Q&As)
* Glossary page with 23 Islamic terms, Arabic script, and alphabetical navigation
* Quran citations in Uthmanic rasm with self-hosted font infrastructure
* Hadith citations with Amiri (standard Arabic typesetting)
* SEO: 14 schema types, FAQPage schema, 9 OG properties, print CSS
* 15+ responsive breakpoints for desktop, tablet, and mobile
* 7 auto-created pages: About, Editorial Policy, Privacy Policy, Contact, Ask a Question, FAQ, Glossary
* Content sync fires automatically on every theme version bump — no manual key management
* Self-hosted fonts: Playfair Display, DM Sans, Cormorant Garamond, Amiri, Uthmani Quran — zero external Google Fonts requests
* Quiz save/resume: progress saved to localStorage after every answer, restored on return
* Quiz accessibility: role/tabindex/aria-selected on all options, keyboard Enter/Space selection, focus-visible styles
* Quiz SEO: controlled by Rank Math (or any wp_head SEO plugin) via page-quiz.php injection; hardcoded fallback tags
* Rate limiting on AJAX: transient-based, 15-20 requests/min per IP on vote, resonance, and search endpoints
* IP dedup behind CDN: resolves real IP via CF-Connecting-IP, X-Real-IP, X-Forwarded-For headers
* Quiz scoring: 6-layer algorithm with question weights, dominance bonuses, signal boosts, persona separation rules, and conflict dampening — rebalanced in v2.2.55 for tighter persona differentiation
* Journey pages: self-contained HTML with theme-injected AJC justify/drop-cap CSS, widened layout matching article proportions, one drop cap per chapter screen
* Citation alignment: nuclear !important override ensures Quran/hadith verses stay centered even with justify plugins active
* Database migration and content sync modules (run once on first admin load)
* Custom Post Type (ce_article) + Custom Taxonomy (ce_topic)
* AJAX: live search, random article, progress sync, quiz save
* Full design system: deep purple backgrounds, teal accent, gold conclusions, Playfair Display + DM Sans + Cormorant Garamond typography

== Installation ==

1. Ensure Twenty Twenty-Five is installed (does not need to be active).
2. Upload compelling-evidence.zip via Appearance → Themes → Add New → Upload Theme.
3. Activate the theme.
4. Visit any WordPress admin page. Three modules auto-run:
   - Category migration — creates taxonomy terms and assigns articles
   - Content sync — publishes article content from theme data files to the database
   - Secondary pages — creates About, Editorial, Privacy, Contact, Ask a Question
5. Go to Settings → Permalinks → Save Changes (flush rewrite rules).
6. Set the front page: Settings → Reading → A static page → select the Homepage.

All 100 articles, 14 journey pages, and the quiz page are created automatically. No manual content entry required.

== Persona Paths ==

Slug                  Display Name
----                  ----
new-atheist           The New Atheist
agnostic              The Agnostic
secular-humanist      The Secular Humanist
antitheist            The Antitheist
materialist           The Materialist
muslim-doubts         The Questioning Muslim
apatheist             The Apatheist
deist                 The Deist
scientist             The Scientist
classical-atheist     The Classical Atheist
ex-believer           The Ex-Believer
spiritual-seeker      The Spiritual Seeker
freethinker           The Freethinker
true-muslim           The Committed Muslim

== Design System ==

Colours (60-30-10 rule)
  Primary (60%): #0d0820, #1a0f38, #120b2e (deep purples)
  Text (30%): #f2eeff (white), rgba(242,238,255,0.55) (muted)
  Accent (10%): #0cd4e0 (teal — links, progress), #f5c518 (gold — conclusions only)
  Logo: #e05252 (red "Compelling") + #f2eeff (white "Evidence")

Typography
  Headings:      Playfair Display (serif)
  Body:          DM Sans (sans-serif)
  Reading:       Cormorant Garamond (serif, italic for pull quotes)
  Quranic Arabic: Uthmani Quran → Amiri (Google Fonts fallback)
  Hadith Arabic:  Amiri (standard Arabic, no Uthmani)

== Frequently Asked Questions ==

= Does this theme work without Twenty Twenty-Five? =
No. It is a child theme and requires Twenty Twenty-Five as the parent.

= Why are the journey pages full-bleed with no WordPress header? =
Journey HTML files are self-contained applications with their own navigation and design. The template suppresses WP chrome to avoid conflicts.

= How does the database migration work? =
Three modules run once on the first admin page load after theme upload. Each uses an option flag so it never re-runs. Category migration creates taxonomy terms and remaps articles. Content sync updates article content from theme data files. Secondary pages creates the About/Contact/etc pages.

= How does cross-device progress work? =
For guests: localStorage only (device-specific). For logged-in users: localStorage + WP user meta via AJAX sync.

= How do I add the Uthmani Quran font? =
Download KFGQPC Uthmanic Script HAFS, convert to woff2, place as assets/fonts/uthmani-quran.woff2. The theme picks it up automatically via @font-face. See assets/fonts/README.md.

== Localisation ==

Translation-ready (text domain: compelling-evidence). Ships with ms-MY (Malay, Malaysia).

Journey content: place translated HTML at /journeys/ms-MY/[slug]-journey.html. Templates auto-detect get_locale() and serve localised files when available.

== Copyright ==

Compelling Evidence WordPress Theme, Copyright 2026 Compelling Evidence
Compelling Evidence is distributed under the terms of the GNU GPL

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

== Resources ==

Google Fonts: Playfair Display, DM Sans, Cormorant Garamond, Amiri
License: SIL Open Font License 1.1 (https://scripts.sil.org/OFL)

Parent theme: Twenty Twenty-Five
License: GPLv2 or later (https://wordpress.org/themes/twentytwentyfive/)

== Changelog ==

See CHANGELOG.md for a full record of all changes.
