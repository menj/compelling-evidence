# Changelog — Compelling Evidence Theme

All notable changes to this theme are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [2.6.33] — September 2026

### Article media, native lightbox, plugin integration

**Added**

- **Figures.** `[ce_figure]` renders an image from the new registry `inc/articles/media.json` with caption and an automatic credit line (author, licence, source). Four images registered from Wikimedia Commons, each checked through the Commons API for licence: the ending of Mark in Codex Sinaiticus (public domain), Medina about 1880 (CC0, cropped to the photograph), the divine name al-Wadūd in calligraphy (CC0), and a Sūrat al-Tawbah page in Qundusi script (public domain). The registry also supports Pexels and Flickr entries.
- **Image import.** Registry images are copied into the Media Library a few at a time on admin page loads, or all at once from the new Theme Options → Media tab, with crops applied and alt text, credit and licence stored on the attachment. Figures then serve responsive local images.
- **Diagrams.** `[ce_diagram]` prints an SVG from `assets/diagrams/` inline, styled from the theme's colour tokens. Three diagrams: the Islamic Dilemma's two options against the Quran's third position; the staged sequence of 4:34 and 4:35; the Pew 2013 figures on violence against civilians.
- **Tables.** Article tables are wrapped in a scrollable, keyboard-focusable region with the caption as its label, styled in `assets/css/ce-media.css`.
- **Lightbox.** `assets/js/ce-lightbox.js`, a dependency-free lightbox for figures and diagrams with grouped navigation, keyboard and swipe control, focus management and an "n of m" counter. Replaces the Lightbox2 plugin (which needs jQuery).
- **Pretty search URLs.** `/?s=term` 301s to `/search/term/` (`inc/ce-search-permalinks.php`), with the base configurable. Replaces the Pretty Search Permalinks plugin, encoding spaces as `%20` rather than `+` and ignoring empty searches. robots.txt follows the configured base.
- **Contact Form 7.** Forms take the theme's design (`assets/css/ce-cf7.css`); the plugin's stylesheet is switched off and its script loads only on pages that contain a form.
- **Batch 007 content.** All eight articles gain visual material: four figures, three diagrams and seven tables, every one drawn from claims already in the article text.

**Changed**

- Crosslinks and glossary tooltips no longer enter figures, diagrams or tables.
- The featured-link strip and the article column can no longer widen a phone page (a table's minimum width had pushed the layout to 541px).

**Decided:** Contact Form 7, CFDB7 and WPS Hide Login remain plugins; see SSOT section 26.

---

## [2.6.32] — September 2026

### Code-standards pass

**Method.** PHP_CodeSniffer 3.13 with WordPress Coding Standards 3 and PHPCompatibilityWP, testing PHP 8.0 to 8.4 (production runs PHP 8.4). Then a runtime pass on WordPress 7.1.2 with `WP_DEBUG_LOG` on across every front-end page type, all CE Theme Options tabs, the analytics dashboard, admin list and editor screens, and the AJAX endpoints.

**Results.** PHP 8.0 to 8.4 compatibility: no issues before or after. WordPress security, database and deprecation sniffs: 115 findings before, 0 errors after (2 warnings for read-only query-string flags on the Ask a Question page). No PHP notices, warnings or deprecations in the runtime log.

**Fixed**

- **Escaping at output** across templates and admin screens: article and question titles in related lists and sidebars, card excerpts, pagination, the search result count, analytics dashboard links, styles and percentages, widget form fields, the footer year and site name (`date()` replaced by the timezone-aware `wp_date()`). Share buttons and social meta now escape at the point of output instead of echoing variables escaped earlier.
- **Input handling.** The Ask a Question nonce, referrer URLs and the 410 request path are unslashed and sanitised. The Ask form's value attributes read `$_POST` on a page that is only ever reached by a GET redirect, so they were always empty; removed. The form's example email address, a real mailbox, is now `name@example.com`.
- **SQL.** Ten queries on the theme's own tables now pass the table name through the `%i` identifier placeholder (WordPress 6.2+). `ce_analytics_count()` loses an unused `$extra_where` parameter that interpolated raw SQL. The live-search query, built entirely from prepared fragments, is annotated.
- **Deprecated and dead code.** A `wp_count_terms()` call using the argument form deprecated in WordPress 5.6, whose result was never used, is removed.
- **Strict comparisons** for the three `in_array()` checks in the article archive.

**Added:** `phpcs.xml.dist` (see SSOT section 24). `Tested up to: 7.1` in `style.css` and `readme.txt`.

---

## [2.6.31] — September 2026

### Search guidelines pass (Google SEO Starter Guide, supported structured data)

**Method.** The local WordPress 7.1.2 install was crawled page type by page type, recording status, title, meta description, canonical, robots directives, heading structure, image alt text, anchor text and every JSON-LD type, before and after the changes. Google's current rules for QAPage, FAQPage and the retired sitelinks search box were checked against Google's own documentation.

**Structured data removed**

- **Front-page FAQPage.** Marked six article titles as questions and article excerpts as answers; none of it is visible as Q&A on the page.
- **Per-article FAQPage** built from `_ce_faq_items` meta, which is never displayed.
- **QAPage on answered reader questions** (`single-ce_question.php`). Google reserves QAPage for pages where users submit answers and names a site-written single answer as an invalid use. Replaced with Article markup.
- **Table-of-contents ItemList and SiteNavigationElement** in `single-ce_article.php` and `single.php` (ItemList is only used for carousels; the ItemList name was also built with `esc_js` inside JSON).
- **Speakable** on articles, the glossary and one journey (Google limits it to news publishers).
- **SearchAction** on WebSite (sitelinks search box retired 21 November 2024). WebSite itself stays.
- **Journey and quiz HTML:** FAQPage and Speakable stripped at serve time.

**Fixed**

- **Duplicate head tags with Rank Math.** The theme printed its own meta description, canonical and Open Graph tags even when Rank Math was active, so live pages carried two of each. It now defers, as it already did for titles, robots.txt and Article schema.
- **Missing and leaked meta descriptions.** FAQ, Glossary, Quiz, Journeys, Q&A and My Progress had none; Ask a Question published "This page uses a custom template. Content is rendered by page-ask-a-question.php."; all eleven topic pages shared "Compelling Evidence article topic". Every page type now has its own description of at most 130 characters with a call to action. Content sync no longer writes the placeholder topic description.
- **Journey canonical pointing at a redirect.** Journeys declared `https://compelling-evidence.com/journeys/{key}/` as canonical, a URL that 301s back to `/journey/{key}/`. Canonical, `og:url` and JSON-LD now use the served URL. Journey titles lead with the path name, meta descriptions are trimmed to the house limit, and only the first of each journey's 8 to 10 `<h1>` elements stays an `<h1>`.
- **Topic pages** all carried the archive heading "The Evidence, Examined"; they now use the topic name and description.
- **Duplicate placeholder pages.** The Rights & Freedom page guard tested one slug and inserted another, so each theme activation added a copy (`rights-freedom-2`, …). Guard fixed; all such pages 301 to `/topic/rights-freedom/` and are excluded from the sitemap.
- **Long titles.** Article titles add the topic only while the whole title stays within 65 characters, and drop the site name beyond 70.
- **Anchor text.** "Read more" card links carry the article title as screen-reader text; icon placeholders are no longer empty links; archive thumbnails get the article title as alt text.
- **robots.txt** no longer disallows `/wp-includes/` (CSS and JS needed for rendering), allows `admin-ajax.php`, and disallows internal search results. The core sitemap excludes the `/journey/` redirect page, the placeholders and My Progress, which is now noindexed.
- **Article schema** gains an image (featured image, else `screenshot.png`) and an author URL.

---

## [2.6.30] — September 2026

### Desktop, tablet and mobile pass

**Method.** A local WordPress 7.1.2 install with Twenty Twenty-Five 1.5, this theme and all 131 articles, driven by headless Chromium across 15 page types at 1440, 1280, 1024, 820, 390 and 360px, checking for horizontal overflow, script errors, tap-target size, text size and menu behaviour. After the fixes: no page overflows at any width, and small tap targets on phones fell from about 30 per page to between 1 and 7.

**Fixed**

- **Journey pages on phones.** The article and its 280px sidebar stayed side by side, and the header breadcrumb pushed the page 219px past the screen edge. `page-journey.php` has always linked `assets/css/journey-base.css`, but the file did not exist (the source of a 404 console error on every journey page). It now does: one column below 960px, sidebar below the article, compact header on phones, and enough top padding that the topic label clears the fixed header (it had been hidden on phones and 4px clear on desktop). Covers all 14 English and 14 ms-MY journeys.
- **Reading progress scrolling out of view.** The whole sidebar was sticky with a capped height and a hidden internal scrollbar, so the mouse wheel over the sidebar scrolled the sidebar itself and carried the progress card away. The table of contents and progress card now form a sticky group (`.sidebar-sticky`) that stays in view for the article; the other widgets follow at the end of the sidebar; a long table of contents scrolls inside its own card. Applied to `single-ce_article.php` and `single.php`. The progress card gains `role="progressbar"` with a live `aria-valuenow`.
- **Top progress bar.** Moved from the very top edge of the window, above the header, to the bottom edge of the header, on all screen sizes. On phones and tablets it is now the only progress indicator; the sidebar card is hidden there because the sidebar sits below the article.
- **Mobile menu showing through.** The closed menu used `translateY(-110%)`, which left its last item ("FAQ") and part of its background over the header and logo. It now sits fully above the viewport, hidden from keyboard and screen readers, on an opaque panel. Escape closes it and returns focus to the toggle; rotating a tablet past 900px closes it.
- **Featured strip chips** were squeezed to "Doe…" and "Why D…" on phones. They keep their width and scroll sideways, with an edge fade.
- **Logo** read "CompellingEvidence": the two words are flex items, so the leading space was dropped. Now spaced with a margin.
- **"Surah Ar-Rahman (55:13)"** on the home page broke at its hyphen on phones; it now stays on one line.
- **Tap targets and text size.** Footer links, breadcrumbs, "Read more" links and the logo reach at least 24px of tap height (WCAG 2.2); labels, verse references and counts that dropped to 9.6 to 11.5px on phones are raised to 12px.

---

## [2.6.29] — September 2026

### A 404 page with a sense of humour

**Changed:** `404.php` rewritten around the site's own subject. The kicker reads "Exhibit 404 · Evidence not found" (in the label font, so it turns typewriter when Special Elite is selected). The headline is one of twelve excuses, each a joke on an argument the site treats seriously: the burden of proof, the cosmological, ontological and fine-tuning arguments, the problem of evil, the God of the gaps, divine hiddenness, Euthyphro, the multiverse, Paley's watch, the hard problem of consciousness and Ockham's razor. Each links to the article that takes the argument seriously.

**Added**

- `inc/ce-404.php`: the excuse list, link resolution through `get_page_by_path()`, and the script enqueue (404 pages only).
- `assets/js/ce-404.js`: "Hear another excuse" swaps the excuse in place with a short fade (instant under reduced motion). Without JavaScript the link reloads with `?excuse=N`.
- "Recent case files": the three newest articles, below the search box.

**Fixed:** the old copy used contractions and an em-dash, and its subtitle sat at 50% opacity (below contrast minimums); the excuse text now sits at 78%.

**Verified** in headless Chromium at 1440px and 390px: layout, the excuse swap and the link update to the matching article.

---

## [2.6.28] — September 2026

### Parent-theme compatibility (Twenty Twenty-Five 1.5)

**Fixed**

- **Parent block templates overriding CE templates.** Twenty Twenty-Five is a block theme, and WordPress gives a block template priority over a PHP template at the same hierarchy level. The parent's `index`, `home`, `single`, `page`, `search`, `archive` and `404` templates could replace CE's `index.php`, `single.php`, `page.php`, `search.php` and `404.php`, and the blog index. New `inc/ce-parent-compat.php` sets aside theme-file block templates on the front end. Site Editor customisations still win; the admin, Site Editor and REST API are untouched.
- **Parent global styles leaking into CE pages.** The parent's `theme.json` styles headings at weight 400 in Manrope and body text in Manrope at the `large` size on a white background. Any element CE's CSS did not style picked these up; the quick-access card titles fixed in 2.6.26 were one case. The child `theme.json` now maps the parent's colour slugs to CE colours and sets body and heading typography to CE's font roles.

**Added**

- CE font families (Playfair Display, Cormorant Garamond, EB Garamond, DM Sans) registered in `theme.json` for the block editor. The parent's Manrope and Fira Code presets are kept so parent patterns still resolve.
- Administrator notice when the parent theme is missing or its major version is newer than the tested version (`CE_PARENT_TESTED_VERSION = '1.5'`).

**Changed**

- Minimum WordPress raised from 6.4 to 6.7 (`style.css`, `readme.txt`), matching Twenty Twenty-Five 1.5.

**Verified:** the template filter keeps Site Editor templates and removes theme-file templates on the front end only; template parts pass through untouched; the notice stays silent on 1.5 and appears for a missing parent or a 2.x parent. `theme.json` validates as JSON and every font file it references exists in CE or in the parent.

---

## [2.6.27] — September 2026

### Table of contents links fixed

**Cause.** The TOC script (inline in `single-ce_article.php` and `single.php`) computed the header offset with `parseInt(getComputedStyle(documentElement).getPropertyValue('--total-offset'))`. Custom properties are returned unresolved, as the string `calc(var(--nav-h) + var(--strip-h))`, so `parseInt` returned `NaN`, the `|| '116'` fallback never applied (the string is not empty), and `window.scrollTo({ top: NaN })` scrolled to the top of the page. The same `NaN` disabled the scroll spy, so no TOC entry was ever highlighted. The script also used `offsetTop`, which is measured from the nearest positioned ancestor, not the document.

**Fixed**

- New `assets/js/ce-toc.js`, enqueued on single articles and posts, replaces both inline copies (51 lines each). It scrolls with `scrollIntoView()`, moves focus to the section heading, and records the section in the address bar so Back and Forward work.
- `main.css`: `#article-body h2 { scroll-margin-top: calc(var(--total-offset, 116px) + 20px); }` keeps headings clear of the fixed nav and featured strip. The script reads this value back as resolved pixels for the scroll spy.
- The scroll spy now highlights the current section and sets `aria-current="location"` on its link.
- Deep links such as `/articles/…#section-3`, which the structured data advertises, now land on the section. Previously the IDs were added after the browser had already given up on the hash.
- `html { scroll-behavior: smooth; }` now applies only under `prefers-reduced-motion: no-preference`; the TOC jumps instantly for readers who ask for reduced motion.

**Verified** in headless Chromium, with and without reduced motion: first, middle and last TOC entries each land with the heading 136px from the top (116px header plus 20px), the correct entry is highlighted, and a `#section-4` deep link lands the same way.

---

## [2.6.26] — September 2026

### Homepage design fixes (quick-access cards and article grid)

**Fixed**

- **Quick-access card titles.** `front-page.php` marks the titles up as `<h2>`, but `main.css` styled only `.qa-card h3`, so the titles fell back to a generic large sans-serif and wrapped onto two lines ("Top / Questions", "Ask a / Question"). The rule now covers `h2` and `h3`: heading font, 1.35rem, weight 700.
- **Quick-access contrast.** Description text raised from 45% to 72% opacity (about 4.0:1 to 8.2:1 on the card), link text from 50% to 85% teal, icons from 60% purple (barely visible) to 80% teal.
- **Quick-access icons.** Reader Q&A and Ask a Question used near-identical single speech bubbles. Reader Q&A now shows two bubbles; Ask a Question shows a bubble with a plus.
- **Quick-access spacing.** Top padding raised from 0.85rem to 2rem so the cards clear the hero's lower edge.
- **Section header alignment.** The intro paragraph beside "What are you searching for?" was vertically centred against the heading block; it now aligns to the heading's baseline.

**Changed**

- **Article card placeholders.** Cards without a featured image showed an emoji chosen by position (the ontological-argument card showed a seedling), rendered differently on every operating system, at 3.5rem inside a 4:3 panel that took over half the card. New `inc/ce-icons.php` supplies a line icon per topic (11 topics plus the static fallback tags), drawn in teal on a 16:9 panel with a faint dot grid. Used by `front-page.php` (live and fallback cards) and `index.php`.
- **Card image ratio.** `.card-img` moves from 4:3 to 16:9 so cards with and without featured images line up.

---

## [2.6.25] — September 2026

### Theme identity

**Changed** (`style.css` header, `readme.txt`, `doc/readme.md`, `doc/ssot.md` §1)

- Theme Name: Compelling Evidence → **CE Theme**.
- Theme URI: https://compelling-evidence.com → **https://github.com/menj/compelling-evidence**.
- Author: compelling-evidence → **MENJ**; Author URI → **https://menj.blog**.
- Unchanged: the folder name and package root (`compelling-evidence`), the text domain, and the site URL in the documentation.

---

## [2.6.24] — September 2026

### Sabon Next LT as a heading and reading option

**Added**

- `assets/fonts/sabon-next-lt-400.woff2`, `-400-italic`, `-700`, `-700-italic`: Sabon Next LT, converted from the owner's licensed TTFs to WOFF2 with glyph data unchanged (100 to 107KB each).
- Registry entry `sabon` in `inc/ce-fonts.php`, offered for the Heading Font and Reading Body Font roles. Stack: Sabon Next LT, EB Garamond, Georgia.
- Transliteration fill: Sabon has no ḥ ḍ ṭ ẓ ʿ ʾ or Ḥ Ḍ Ṭ Ẓ, and a scan of all 131 articles found these are the only characters it lacks. Four extra `@font-face` rules serve them from EB Garamond via `unicode-range`, scaled with `size-adjust` (109.8% for lowercase and ʿ ʾ, 104.8% for capitals). Measured against Sabon's own h, d, H and D, the scaled glyphs match within about 3% in height and 4% in width.

---

## [2.6.23] — September 2026

### Typography release: EB Garamond, Special Elite, and font roles that work

**Added**

- `assets/fonts/eb-garamond-var.woff2` and `eb-garamond-var-italic.woff2`: EB Garamond, variable weight 400 to 800, converted from the supplied TTFs and subset to Latin, Latin Extended-A and the transliteration characters the site uses (69KB and 74KB).
- `assets/fonts/special-elite-400.woff2`: Special Elite, subset to Basic Latin and Latin-1 (49KB).
- `assets/fonts/licenses/`: the SIL OFL 1.1 text for EB Garamond and the Apache 2.0 text for Special Elite, as both licences require.
- `inc/ce-fonts.php`: font registry, role variables, typewriter body class and preload tags.
- Theme Options → Typography → **Label & Eyebrow Font** (`ce_accent_font`): DM Sans (default) or Special Elite, applied to uppercase section labels, topic tags and page eyebrows.

**Fixed**

- The **Heading Font** and **Reading Body Font** settings were registered and saved but read nowhere, so changing them had no effect. They now drive `--font-heading` and `--font-reading`, and both offer EB Garamond.
- The **Preload critical fonts** setting was ignored: `header.php` always preloaded Playfair Display 900 and DM Sans 400. Preloads now follow the setting and the chosen heading family.

**Changed**

- 132 hard-coded `font-family` declarations across `main.css`, `templates.css`, `ce-engagement.css` and `glossary-tooltip.css` now use `var(--font-heading)`, `var(--font-reading)` or `var(--font-ui)`. With default settings the rendered fonts are unchanged.

**Not included:** Sabon Next LT, pending confirmation of a web licence (added in 2.6.24).

---

## [2.6.22] — September 2026

### Content release: eight articles answering Christian and ex-Muslim critique

Source material: twelve transcripts supplied for review, covering public debates, PragerU videos, a GotQuestions explainer, ex-Muslim interviews and a Lighthouse mentoring interview. Each recurring claim was checked against the existing 123 articles. The eight claims with no coverage became new articles.

**Added: `inc/articles/batch-007.json` (8 articles, total 131)**

| Order | Slug | Topic | Subcategory |
|---|---|---|---|
| 121 | `islamic-dilemma-quran-and-bible` | The Quran & Its Sources | The Text & Its Transmission |
| 122 | `sword-verse-jizya-9-5-9-29` | The Quran & Its Sources | Difficult Passages & Doctrines |
| 123 | `is-islam-a-religion-of-peace-terrorism-data` | History, Context & Comparison | The Specific Charges |
| 124 | `does-the-quran-teach-hatred-of-jews` | History, Context & Comparison | The Specific Charges |
| 125 | `quran-4-34-wife-beating` | Rights & Freedom | Gender, Body & Sexuality |
| 126 | `is-allah-a-slave-master-or-a-god-of-love` | Divine Justice & Fairness | (flat topic) |
| 127 | `does-islam-teach-salvation-by-works` | Divine Justice & Fairness | (flat topic) |
| 128 | `is-islam-compatible-with-western-democracy` | Rights & Freedom | The Harder Accusations |

**Verification.** Every Quran block (32 verses) takes its Uthmani Arabic verbatim from a verified source, and every hadith block (10 reports) is matched against the source Arabic by the build script, which refuses to run on a mismatch. Statistics come from the 2026 Global Terrorism Index release, the 2011 NCTC report, the 2013 and 2017 Pew Research Center surveys and Britannica on jizyah. Bible references were checked against the World English Bible. All eight articles passed an automated audit for em-dashes, contractions, contrastive negation, banned vocabulary and anaphora.

**Changed**

- `manifest.json`: version 2.6.22, total_articles 123 → 131, batch-007 entry with checksum. All seven batch checksums verified with the loader's method.
- `archive-ce_article.php`: the six subcategorised slugs added to the subcategory map. Divine Justice & Fairness stays flat at seven articles.
- `inc/ce-crosslinks.php`: 18 new cross-link phrases. The existing `sword verse` phrase now points to the new dedicated article in place of `mercy-harsh-passages`.
- `inc/ce-glossary.php`: nine terms added (Awliyāʾ, Dhimmi, Injil, Jizyah, Muhaymin, Nushuz, Tahrif, Tawrat, Wadud); 93 terms in total, confirmed at runtime.
- `inc/class-ce-article-loader.php`: stale comments corrected (7 batches, 131 articles; the completeness thresholds are floors). No logic changed.
- `style.css`, `readme.txt`, `doc/readme.md`, `doc/ssot.md`: version and article counts updated. The category table in `doc/readme.md` now reflects the actual per-topic counts.

**Editorial conventions for batch-007.** Citation reference lines omit the leading dash used in earlier batches. Bible references stay inline, following the site convention. No existing article was edited.

---

## [2.6.21] — September 2026

### Stability release: fatal-proof table creation, per-request query removal, 410 for retired spam URLs

Diagnosed from the September 2026 server logs (PHP error log, Apache access and SSL logs, FTP log). No article content changed.

**Background.** The error log showed that `wp-admin/includes/schema.php` is missing from the WordPress core install, so every `require_once` of `wp-admin/includes/upgrade.php` ends in a fatal error. The theme required that file in two places, and one of them could run on front-end `init`. The log also showed database index corruption and read-only tables on 1 and 2 September, during which the theme attempted term inserts on every request. Crawlers requested spam URLs left by a past injection (`/shop/manufacturer-site`, `/product/category/…`, `/product-similar-image/`) roughly 20,000 times, each rendering the full 404 template.

**Fixed**

- **Table creation no longer depends on core admin files.** New `inc/ce-db.php` provides `ce_table_exists()` and `ce_create_table()`, which run the existing `CREATE TABLE IF NOT EXISTS` statements directly. `ce_analytics_create_table()` and `ce_engagement_create_table()` use it and no longer call `dbDelta()`.
- **Version flags written only on success.** `ce_analytics_table_version` and `ce_engagement_db_version` are recorded only after the table is confirmed, so a failed create is retried on the next admin load.
- **Engagement table check moved off the front end.** `ce_engagement_maybe_create_table()` now hooks `admin_init` instead of `init`.
- **Question-status seeding runs once.** The five `ce_qstatus` term lookups and inserts moved out of `ce_register_question_status()` (which ran on every request) into new `ce_seed_question_statuses()` on `admin_init` and `after_switch_theme`, gated by the new `ce_qstatus_seeded` option.

**Added**

- **410 Gone for retired URLs.** New `inc/ce-gone.php` answers requests whose path begins with a listed prefix with 410, `X-Robots-Tag: noindex, nofollow`, and a one-line body at `init` priority 0, before the main query, the 404 template and plugin 404 logging. Settings: Theme Options → Performance → Retired URLs (`ce_gone_enabled`, `ce_gone_paths`).

**Files changed:** `functions.php`, `inc/ce-analytics.php`, `inc/ce-engagement.php`, `inc/ce-question-cpt.php`, `inc/ce-theme-options.php`, `style.css`, `readme.txt`, `doc/*`. **Files added:** `inc/ce-db.php`, `inc/ce-gone.php`. `inc/ce-secondary-pages.php` (whitelisted in WP Perf Shield) is unchanged.

**Validation:** PHP syntax checked on every PHP file. Retired-URL matcher tested against the three spam patterns (410), the home page, an article URL and `/shop/.env` (all pass through), and a bare `/` entry (ignored).

## [2.6.20] — May 2026

### Subcategory expansion to History, Problem of Evil, and Islamic Practice (theme-level, no article changes)

A presentation-only release that completes the subcategorisation of the topic landing page. Three additional topics now render with subcategory groupings: *History, Context & Comparison* (10 articles, 3 groups), *The Problem of Evil* (9 articles, 3 groups), and *Islamic Practice & Ritual* (8 articles, 2 groups). Two topics with only 5 articles each — *Divine Justice & Fairness* and *Ethics Without God?* — intentionally remain as flat lists, since at that size the visual overhead of subcategory headers does more harm than good. Each of these flat topics is small enough that a reader can scan all 5 article titles in seconds.

After this release, 9 of the 11 topics are sub-categorised. 112 of the 123 total articles are assigned to a subcategory group. The 11 articles in the two intentionally-flat topics render as before, with no subcategory headers.

**New subcategory groups added (with one-line orientation text):**

- **History, Context & Comparison**
    - Religion in History — "What religion has done in the world, and what that proves." (3 articles: religion-cause-harm, religion-is-political-control, religion-of-your-birth)
    - Islam's Intellectual Tradition — "The reason, reform, and self-criticism Islam already produced." (4 articles: islam-and-science, islam-and-enlightenment, the-freethinkers-islam-produced, the-islam-i-was-defending)
    - The Specific Charges — "Concrete accusations against Muhammad, Muslims, and the way Islam spreads." (3 articles: muhammad-and-warfare, honour-killings-culture-not-islam, islam-prison-conversion)
- **The Problem of Evil**
    - The Problem in Theory — "The classical objection: how a good God permits suffering." (4 articles: problem-of-evil, natural-evil, problem-of-evil-response, why-create-knowing-suffering)
    - Hell, Punishment & Mercy — "Eternal punishment, universal salvation, and the limits of divine mercy." (3 articles: why-hellfire, universal-salvation, finite-sins-infinite-punishment)
    - Living With Suffering — "Unanswered prayer and the personal experience of pain." (2 articles: unanswered-prayer, suffering-and-god)
- **Islamic Practice & Ritual**
    - Worship & Ritual — "Prayer, fasting, the Kaaba, and the rules that look strange from outside." (5 articles: why-arabic-prayer, does-god-need-our-prayers, kaaba-idol-worship, ramadan-fasting-purpose, islam-too-many-rules)
    - The Unseen World — "Angels, jinn, the evil eye, and the realities Islam affirms beyond the senses." (3 articles: angels-and-the-unseen, islam-jinn-mental-illness, evil-eye-islamic-view)

**Validation:**
- All 27 article slugs referenced in the new subcategory groups match articles in the JSON corpus.
- No article is referenced more than once within its topic's subcategory map.
- Every article in the three newly sub-categorised topics is assigned to exactly one subcategory.
- The two intentionally-flat topics (Divine Justice & Fairness, Ethics Without God?) continue to render flat, with no subcategory headers — preserving the editorial decision that subcategorisation is unnecessary at 5 articles or fewer.
- PHP syntax validated. Whitelist hashes verified — no flagged file was modified.

**Files changed:** `archive-ce_article.php` only (added three topic blocks to `$subcategory_descs` and three matching topic blocks to `$subcategories`). No other files affected.

**Behavioural notes:** The catch-all "Further Reading" block in the rendering logic continues to capture any article whose slug is not assigned to a subcategory group, so even if a future article is added to a sub-categorised topic but its subcategory placement is forgotten, it still renders rather than being silently dropped.

## [2.6.19] — May 2026

### Subcategory presentation polish (theme-level, no article changes)

A presentation-only release that makes the existing subcategory structure on the topic landing pages render with consistent editorial polish across all six sub-categorised topics. No article content was modified in this release; the change is to `archive-ce_article.php` and `assets/css/templates.css` only.

**Background.** The theme already defined a `$subcategories` array mapping each of six topics (Does God Exist?, The Quran & Its Sources, Science & Evidence, Rights & Freedom, The Inner Journey, Revelation & Meaning) to two-to-four named subcategory groups. The rendering logic was already in place. The Quran & Its Sources is grouped into four subcategories: The Text & Its Transmission (6 articles), Authorship & Prophethood (4 articles), Difficult Passages & Doctrines (6 articles), and History & Hadith (6 articles). Sites running an earlier theme version that pre-dated the Quran subcategory addition will, on installing this release, see the four subcategory headers render automatically on the /articles/ archive page just like the Does God Exist? and Inner Journey subcategories already do.

**The change.** Added a `$subcategory_descs` array providing one-line orientation text under each subcategory header, parallel to the topic-level `$topic_descs` array that already populated the topic headers. The subcategory descriptions are short, italic, and use the same Cormorant Garamond serif as the topic descriptions but at slightly reduced visual weight to maintain hierarchy (topic > subcategory > article).

Subcategory descriptions added:

- **Does God Exist?**
    - The Core Arguments — "Why the universe, reason, and reality point toward a Creator."
    - Common Objections — "Answering the standard sceptical and atheist replies."
- **The Quran & Its Sources**
    - The Text & Its Transmission — "How the Quran reached us, and what its literary form claims."
    - Authorship & Prophethood — "Who the Prophet was, and where the Quran came from."
    - Difficult Passages & Doctrines — "The verses that look harshest, and what they actually say."
    - History & Hadith — "Apostasy, early violence, and how the prophetic record was preserved."
- **Science & Evidence**
    - What Science Can and Can't Do — "The reach and the limits of empirical method."
    - Cosmology & Origins — "The Big Bang, fine-tuning, and the questions evolution does not answer."
    - Mind & Experience — "Consciousness, religious experience, and what the brain explains."
- **Rights & Freedom**
    - Leaving Islam — "What the tradition actually says about apostasy and conscience."
    - Gender, Body & Sexuality — "The hardest questions about women, the body, and same-sex attraction."
    - The Harder Accusations — "Slavery, hellfire, and the charge that Islam is a system of fear."
- **The Inner Journey**
    - Understanding Doubt — "What doubt is, where it comes from, and what it asks of you."
    - The Emotional Cost — "Anger, hidden disbelief, religious trauma, and the weight of leaving."
    - What Happens Next — "The social aftermath, the algorithmic environment, and the path home."
- **Revelation & Meaning**
    - Does God Speak? — "Whether God communicates, and how to evaluate competing claims."
    - Why Islam? — "What draws people to Islam, and what it offers that others do not."
    - Questions From Within — "Doubt, belief, and the things only the believer asks."

**Files changed:**
- `archive-ce_article.php` — added `$subcategory_descs` array and the conditional render block that emits the `<p class="ts-subcat-desc">` element only when a description is defined for the current topic and subcategory.
- `assets/css/templates.css` — added the `.ts-subcat-desc` rule (Cormorant Garamond italic, reduced opacity, subtle bottom margin) to keep the visual hierarchy consistent with the existing topic-description style.

**Behavioural notes:** The new array is optional. Any subcategory whose name is not present in `$subcategory_descs[$topic_name]` renders without a description, preserving graceful degradation. The catch-all "Further Reading" block (which captures any articles not yet assigned to a subcategory) renders without a description as before.

## [2.6.18] — May 2026

### Phase 2.10: The Quran & Its Sources deep edit, Session A (6 of 22 articles)

The Quran & Its Sources is the largest topic in the corpus (22 articles) and presents an unusual editorial challenge: the topic is *about* the Quran, yet many of its articles entered the deep-edit pass with only a single Quran citation and zero hadith citations. This irony shaped the citation strategy. Every article in this session needed at least one major Quranic addition or one major hadith addition (most needed both). The em-dash density was uniformly extreme (10–16 per article in this session, with cumulative reduction of approximately 78 to 14 across the 6 articles).

Session A covers the first six articles by reading order: the literary argument for divine origin, the historical preservation of the text, the question of harsh passages alongside divine mercy, scientific signs in the Quran, the secular hypothesis that Muḥammad authored the text, and the apostasy hadith. Sessions B, C, and D will cover the remaining 16 articles across 6+5+5 splits.

- *The Quran as Literary Argument* (quran-literary-argument): 1,223 → 1,461 words (8 min). Already had Sūrat al-Isrāʾ 17:88 (the *taḥaddī* challenge verse). Added Bukhārī 4981 / Muslim 152 — the powerful hadith on the Quran as the Prophet's primary sign. The hadith establishes the structural distinction between bounded miracles given to earlier prophets (Moses' staff, Jesus' healing) and the continuously verifiable sign that the Quran constitutes. The literary argument is simply the working out, in detail, of what the Prophet's hadith identifies as the Quran's distinguishing feature: a sign that remains continuously available to every generation that encounters it.

- *How the Quran Was Preserved* (quran-historical-reliability): 736 → 1,039 words (6 min). Already had Sūrat al-Ḥijr 15:9 (the preservation verse). Added Ṣaḥīḥ al-Bukhārī 5027 — "the best of you is the one who learns the Quran and teaches it." The hadith identifies the social mechanism that produced the preservation: the highest religious praise was attached to the learning and teaching of the text itself, generating the continuous, distributed verification system of the *ḥuffāẓ* across every generation. Added a new section on the 2015 Birmingham manuscript carbon-dating (95.4% probability between 568 CE and 645 CE, overlapping with the Prophet's lifetime). Article previously had zero hadith citations.

- *If God Is Merciful, Why Are Some Quranic Passages So Harsh?* (mercy-harsh-passages): 865 → 1,125 words (6 min). Already had Sūrat al-Anʿām 6:54 (mercy prescribed upon Himself). Added Bukhārī 7404 / Muslim 2751 — the *ḥadīth qudsī* "My mercy precedes My wrath." Added Sūrat al-Baqarah 2:190 (the foundational verse on defensive warfare with the proportionality constraint "do not transgress; God does not love the transgressors"). The new Quran citation strengthens the article's argument that the harsh passages on warfare are not licences for arbitrary violence but legal architecture conducted within Quran-enforced constraints. Article previously had zero hadith citations.

- *What About Scientific Miracles In The Quran?* (scientific-miracles-quran): 1,168 → 1,190 words (6 min). Already had Sūrat Fuṣṣilat 41:53 (signs in horizons and selves). Added the al-Bayhaqī hadith on reflection: "Reflect upon God's creation, and do not reflect upon God Himself." The hadith establishes the proper object of human reflection (the creation, accessible to investigation, rather than the divine essence) and frames the Quran's repeated direction of attention to natural phenomena as the disciplined working out of this principle. Article previously had zero hadith citations.

- *How Do We Know The Quran Wasn't Written By Muḥammad?* (quran-written-by-humans): 726 → 1,201 words (7 min). Largest expansion in the session. Already had Sūrat Yūnus 10:38 (the *taḥaddī* verse). Added Ṣaḥīḥ al-Bukhārī 2 / Muslim 2333 — the canonical hadith on the Prophet's own first-person account of the experience of revelation ("sometimes it comes to me like the ringing of a bell, and that is the hardest on me"). The hadith is decisive on the human-authorship question: the Prophet's self-report is the testimony of a man describing not his own composition but his receipt of speech from a source other than himself. Added a new section on the verses that argue against composition (Sūrat ʿAbasa 80:1–10, Sūrat al-Taḥrīm 66:1, Sūrat al-Tawbah 9:113–114) — the divine rebukes preserved in the canonical text that a man composing self-aggrandising material would be unlikely to preserve.

- *The Hadith "Kill Him Who Changes His Religion": A Direct Response* (kill-him-who-changes-religion): 813 → 1,018 words (6 min). Article previously had Sūrat al-Baqarah 6922 (the apostasy hadith) but NO Quran citation. Added Sūrat al-Baqarah 2:256 (no compulsion in religion) as the controlling Quranic principle. Added Sūrat al-Nisāʾ 4:137 — the decisive verse describing a person who has changed religion *four times* (believed-disbelieved-believed-disbelieved-increased in disbelief). The Quran's response is divine, not human, judgement. The verse is decisive on the apostasy question because if the death penalty were the categorical Islamic rule, the person described would have been executed on the first apostasy and the verse describing four cycles would be incoherent. The Quran's silence on a worldly punishment for the case it explicitly describes settles the matter at the textual level.

All 6 articles now display 6+ min reading time, contain at least 1 Quran citation and 1 hadith citation, pass the banned-word audit, pass the contrastive-negation audit, and have em-dash counts of 2–3 per article (down from 10–16).

## [2.6.17] — April 2026

### Phase 2.9: The Inner Journey deep edit, Session C (5 of 17 articles, completes topic)

The final five articles of the Inner Journey topic. With Sessions A (v2.6.15) and B (v2.6.16), the entire 17-article topic is now deep-edited. Session C covers the experience of imposed religious upbringing, the role of justified anger in the deconversion narrative, the priority of head versus heart in the departure, the algorithmic environment of contemporary deconversion, and the possibility of return after having left.

The em-dash density across these articles was uniformly extreme (21–26 each) and the total dropped from approximately 120 to 14 across the 5 articles in this session.

- *When Religion Was Imposed, Not Discovered* (when-religion-was-imposed-not-discovered): 1,249 → 1,423 words (8 min). Already had three Quran citations (Sūrat al-Baqarah 2:256 on no compulsion, Sūrat Muḥammad 47:24 on reflection, Sūrat al-Kahf 18:29 on free belief). Added Bukhārī 1 / Muslim 1907 (the foundational hadith on intentions). The hadith is decisive on the imposition question: if the moral and spiritual worth of every action depends on the intention behind it, then coerced practice has no spiritual value. The God who reads every heart knows the difference between the practice produced by free conviction and the practice extracted by social pressure.

- *The Anger Is Real, And It Deserves an Answer* (the-anger-is-real): 1,233 → 1,419 words (8 min). Already had Sūrat al-Jāthiyah 45:7 (woe to every sinful liar). Added Bukhārī 6126 / Muslim 2327 — the powerful hadith on the Prophet's own anger: "The Messenger of God never took revenge for himself, except when the limits of God were violated." The hadith establishes the legitimate object of religious anger: the violation of justice and truth, the harm done to the vulnerable. Anger directed at the people who used God's name to harm you is anger of the kind the Prophet himself modelled.

- *Did Your Heart Leave Before Your Head?* (did-your-heart-leave-before-your-head): 1,105 → 1,259 words (7 min). Already had Sūrat al-Jāthiyah 45:23 (the *hawā* verse — taking desire as one's god). Added Bukhārī 1 / Muslim 1907 (the intentions hadith) examined here for its symmetric application to belief and disbelief: the action of departing Islam, like every other action, has the moral worth of the intention behind it. The person who departs because they have honestly concluded the evidence does not support belief is doing one thing; the person who departs because the social pressure for departure has become more intolerable than the social pressure for staying is doing something different.

- *The Algorithm That Deconverted You* (the-algorithm-that-deconverted-you): 1,185 → 1,321 words (7 min). Already had Sūrat al-Ḥujurāt 49:6 (the *fatabayyanū* verification verse). Added Ṣaḥīḥ Muslim 5 (introduction) — the hadith on relaying everything one hears as itself a form of falsehood. The hadith establishes the discipline of intellectual humility before claims that have not been verified. The person who builds their position on the unsifted output of an algorithm has done what the hadith warns against.

- *Coming Back After Leaving: Is Return Possible?* (coming-back-after-leaving): 1,082 → 1,187 words (6 min). Already had Sūrat al-Zumar 39:53 ("indeed, God forgives all sins"). Added Bukhārī 6309 / Muslim 2747 — the famous lost-camel hadith: "God is more joyful at the repentance of His servant than one of you who finds his lost camel in a desert." The hadith is one of the most powerful in the entire tradition on the question of return. The image is of a man in a barren desert, having lost the camel that carried his food and water, despairing of finding it, lying down in the shade of a tree expecting to die, then suddenly seeing the camel standing beside him. The God of the Islamic tradition is described as more joyful at the repentance of His servant than the desperate desert traveller is at the recovery of the camel that means his survival.

All 5 articles now display 6+ min reading time, contain at least 1 Quran citation and 1 hadith citation, pass the banned-word audit, pass the contrastive-negation audit, and have em-dash counts of 2–5 per article (down from 21–26).

**Topic completion:** The Inner Journey — 17 of 17 articles deep-edited across v2.6.15 + v2.6.16 + v2.6.17. The topic was the most pastorally sensitive of the eleven topics, written for people in active doubt, deconstruction, the dual life, the anger phase, religious trauma, and the experience of social isolation that comes with leaving Islam. The editorial pass had to maintain the warm, non-judgmental tone the articles already carried while removing the contrastive negations, the high em-dash density (often 20–32 per article), and the missing hadith citations that almost every article had.

## [2.6.16] — April 2026

### Phase 2.9: The Inner Journey deep edit, Session B (6 of 17 articles)

The middle six articles of the 17-article Inner Journey topic. Session B continues the pastorally sensitive editorial work begun in Session A, covering practising-without-belief, religious trauma, the sociology of leaving, the right framing of doubt, the Good Muslim Paradox, and spiritual dryness. Session C will close the topic with the remaining 5 articles.

The em-dash density across these articles was the highest in the corpus going in: most carried 18–32 em-dashes per article. The total dropped from approximately 130 to 14 across the 6 articles in this session.

- *Fasting Without Faith: What Ritual Means When You No Longer Believe* (practising-without-belief): 823 → 1,307 words (7 min). Already had Sūrat al-Baqarah 2:183 (the foundational Ramadan verse). Added Bukhārī 1 / Muslim 1907 — the foundational hadith on intentions, examined here for what it actually says about practice without belief: not that the practice is invalid, but that the moral and spiritual worth of every action is calibrated to the intention behind it. Added two new sections on the communal dimension of practice and the unity of inner and outer.

- *When Religion Hurts: Religious Trauma and the God Question* (religious-trauma): 761 → 1,085 words (6 min). Already had Sūrat al-Baqarah 2:286 (God does not burden a soul beyond its capacity). Added Bukhārī 7404 / Muslim 2751 — the *ḥadīth qudsī* "My mercy precedes My wrath." The hadith is decisive on the trauma question: the God whose mercy is structurally prior to His wrath is not the God of the abusive classroom or the threatening community. Article previously had zero hadith citations.

- *How Muslims Actually Leave: The Inner Journey* (how-muslims-leave-the-sociology): 1,175 → 1,450 words (8 min). Article previously had zero Islamic citations. Added Tirmidhī 2459 — the famous hadith on *muḥāsabah* (the wise person calls himself to account). The hadith establishes self-reckoning as a defining mark of wisdom and applies symmetrically to the believer and the doubter. Added Sūrat al-Ḥashr 59:18 (let every soul look to what it has put forth for tomorrow) imposing the same self-examining discipline as a religious obligation.

- *Your Doubts Are Not a Disease* (your-doubts-are-not-a-disease): 1,314 → 1,496 words (8 min). Already had three Quran citations (Sūrat al-Naḥl 16:43 on asking people of knowledge, Sūrat al-Zumar 39:9 on those who know vs. those who don't, Sūrat Fuṣṣilat 41:53 on signs in the horizons). Added Sunan Ibn Mājah 224 — seeking knowledge as a binding religious duty. The hadith establishes that the duty cannot be fulfilled without questions, since questions are the operating mechanism of knowledge-seeking. A community that punishes questions has made the prophetic duty impossible to fulfil.

- *The Good Muslim Paradox* (the-good-muslim-paradox): 1,241 → 1,567 words (8 min). Article previously had zero Islamic citations. Added Bukhārī 71 / Muslim 1037 — "Whomever God wishes good for, He gives him deep understanding (*fiqh*) in the religion." The hadith is decisive on the Good Muslim Paradox: the communities that withhold deep understanding from their members are withholding what the hadith identifies as God's actual gift. The collapse of the simplified version is, on this reading, the prerequisite for the deeper understanding the hadith identifies as God's good for the person. Added Sūrat al-Zumar 39:9 (those who know are not equal to those who do not know) framing the journey toward knowledge as the structural priority the Quran establishes.

- *When the Presence Fades* (when-the-presence-fades): 1,201 → 1,433 words (8 min). Already had Sūrat al-Raʿd 13:28 (in the remembrance of God hearts find rest). Added Ṣaḥīḥ Muslim 2999 — "How wonderful is the affair of the believer." The hadith identifies the structure of the believer's experience as one in which both spiritual ease and spiritual hardship serve the same purpose: the cultivation of character through the response to each. The dry season is the condition under which patience can be exercised in a way that the season of spiritual warmth does not allow. Article previously had zero hadith citations.

All 6 articles now display 6+ min reading time, contain at least 1 Quran citation and 1 hadith citation, pass the banned-word audit, pass the contrastive-negation audit, and have em-dash counts of 1–4 per article (down from 18–32).

## [2.6.15] — April 2026

### Phase 2.9: The Inner Journey deep edit, Session A (6 of 17 articles)

The Inner Journey is the most pastorally sensitive topic in the entire corpus. The articles are written for people in active doubt, deconstruction, the dual life, the anger phase, and the experience of social isolation that comes with leaving Islam in a Muslim-majority context. The editorial pass had to maintain the warm, non-judgmental tone the articles already carried while removing the contrastive negations, the high em-dash density (11–32 per article), and the missing hadith citations that almost every article had.

Session A covers the foundational six articles on the experience of doubt and departure: was my faith just conditioning, did I leave because of specific source problems, the dual life of hidden doubt, the anger phase, the real social cost of leaving, and the scale of hidden doubt across the Muslim world. Sessions B and C will cover the remaining 11 articles in subsequent releases.

- *Was My Faith Just Conditioning?* (faith-was-just-conditioning): 908 → 1,055 words (6 min). Already had the canonical *fiṭrah* hadith (Bukhārī 1359 / Muslim 2658). Added Sūrat al-Baqarah 2:170 — the Quranic criticism of inheritance-without-examination ("we will follow what we found our fathers doing"). The verse is decisive on the question because it builds the conditioning critique into the Quran itself: the Quran is asking the reader to do exactly what the conditioning observation says religious people fail to do, namely to examine the content rather than inherit it without thinking.

- *I Left Because Of Specific Problems In The Sources* (left-because-of-specific-problems): 799 → 1,374 words (7 min). Largest expansion in the session. Already had Sūrat Muḥammad 47:24 (the verse on locks on hearts). Added Bukhārī 110 / Muslim 3 — the famous hadith on the gravity of misattributing statements to the Prophet ﷺ ("whoever lies about me intentionally, let him take his seat in the Fire"). The hadith is the founding principle of the entire science of hadith criticism: the person who left because of a difficult hadith may have left because of a hadith the tradition itself has serious questions about. Added two new sections on classical hadith grading methodology and the interpretive tradition on difficult passages.

- *Living Two Lives: The Weight Of Hidden Doubt* (the-dual-life): 1,044 → 1,147 words (6 min). Already 6 min and well-written; primarily added a Quranic anchor and reduced em-dashes from 32 to 2. Added Sūrat Qāf 50:16 ("We know what his soul whispers; We are closer to him than his jugular vein") which addresses the dual life directly: the part of the person that the visible community cannot see is the part that God sees most directly. Already had Bukhārī 1 / Muslim 1907 (the intention hadith).

- *The Anger Phase: What It Is And What It Means* (anger-at-religion): 799 → 1,063 words (6 min). Kept the existing Sūrat Fuṣṣilat 41:34 (repel evil with that which is better). Added Bukhārī 6114 / Muslim 2609 — the Prophet's redefinition of strength as self-mastery during anger. The hadith reframes the anger phase: the point is not that anger is illegitimate, but that the strong one is the one whose anger does not master the reasoning. Article previously had zero hadith citations. Em-dashes reduced from 14 to 2.

- *The Real Cost Of Leaving: What No One Tells You* (social-cost-of-leaving): 684 → 1,055 words (6 min). Article previously had zero Islamic citations. Added Ṣaḥīḥ Muslim 2999 — the "how wonderful is the affair of the believer" hadith identifying the structural asymmetry by which the believer's response to hardship transforms its meaning. Added Sūrat Qāf 50:16 — the verse on God being closer than the jugular vein, addressed as a counterweight to the absence of human relationship that the social cost imposes. The article is now the most pastorally substantial in the session.

- *You Are Not Alone: The Scale Of Doubt In The Muslim World* (scale-of-leaving): 705 → 1,202 words (7 min). Already had Sūrat Qāf 50:16 (We know what his soul whispers). Added Bukhārī 13 / Muslim 45 — the foundational hadith on the believer's care for others ("none of you truly believes until he loves for his brother what he loves for himself"). The hadith establishes that the scale of hidden doubt is a moral indictment of the communities that allowed the doubt to become hidden. Added a new section on al-Ghazālī's *al-Munqidh min al-Ḍalāl* as evidence that the classical tradition handled doubt differently from the contemporary Muslim community's suppression of it. Article previously had zero hadith citations.

All 6 articles now display 6+ min reading time, contain at least 1 Quran citation and 1 hadith citation, pass the banned-word audit, pass the contrastive-negation audit, and have em-dash counts of 2–5 per article (down from 11–32).

## [2.6.14] — April 2026

### Phase 2.8: Science & Evidence deep edit, Session B (6 of 11 articles, completes topic)

The second half of the Science & Evidence topic. With Session A (v2.6.13), the entire 11-article topic is now deep-edited. Session B covers the science-and-religion meta-question, the two evolution articles, the neuroscience-of-religious-experience question, near-death experiences, and the rationality of belief in the unseen.

In contrast to Session A's deliberate Quran-only approach (where the philosophical questions did not have natural hadith parallels), Session B recovers the typical Quran-and-hadith ratio. Five of the six articles now carry both a Quran and hadith citation. The exception is *evolution-explains-design*, which kept its single Quran citation (Sūrat al-Mulk 67:3, the empirical-challenge verse) since the article's argument is structural rather than ethical.

- *Does Science Provide All The Answers?* (science-and-religion): 1,294 → 1,379 words (7 min). Already 7 min and well-cited (Sūrat Fāṭir 35:43 on God's *sunan*); primarily structural cleanup and one critical hadith addition. Added Sunan Ibn Mājah 224 — the famous *ṭalab al-ʿilm* hadith on seeking knowledge as a binding religious duty (*farīḍah*) on every Muslim. The hadith establishes that the Islamic obligation to seek knowledge covers what the classical scholars called *al-ʿulūm al-ʿaqliyyah* (the rational sciences) — the framework under which the House of Wisdom in Baghdad, Ibn Sīnā's medical works, al-Bīrūnī's astronomy, and Ibn al-Haytham's optics were all conducted. Em-dashes reduced from 28 to 2.

- *Does Human Evolution Contradict Islamic Theology?* (evolution-and-islam): 1,289 → 1,375 words (7 min). Already 7 min and richly cited (four Quran verses: Fāṭir 35:43 on *sunan*, Āl ʿImrān 3:190 on signs, Baqarah 2:30 on *khalīfah*, Aḥzāb 33:72 on *amānah*). Primarily structural cleanup and added the same Sunan Ibn Mājah 224 hadith on seeking knowledge as a religious duty, anchoring the article's central claim that the Muslim who refuses to engage scientific evidence is in violation of the prophetic instruction. Em-dashes reduced from 29 to 5.

- *Doesn't Evolution Make God Unnecessary?* (evolution-explains-design): 811 → 1,098 words (6 min). Added Sūrat al-Mulk 67:3 ("look again: do you see any flaw?") anchoring the design argument at the level of physical constants and cosmic order, where evolution operates within rather than producing. Article previously had zero Islamic citations.

- *Can Neuroscience Explain Religious Experience?* (religious-experience-neuroscience): 710 → 1,077 words (6 min). Kept the existing Sūrat Fuṣṣilat 41:53 (signs in horizons and selves). Added Bukhārī 52 / Muslim 1599 — the Prophet's hadith on the heart as the seat of moral and spiritual perception. The hadith locates the centre of religious experience in the heart conceived as the organ of recognition, complementing rather than competing with neuroscience's account of the brain. Article previously had zero hadith citations.

- *Near-Death Experiences: Evidence for What?* (near-death-experiences): 737 → 1,021 words (6 min). Kept the existing Sūrat al-Baqarah 2:154 (those killed in God's way are alive but you do not perceive it). Added Sunan al-Tirmidhī 2460 — the grave-as-garden-or-pit hadith establishing that the period between death and resurrection is a conscious experience whose moral content aligns with the broad shape of NDE reports. Article previously had zero hadith citations.

- *How Can a Rational Person Believe in the Unseen?* (how-can-a-rational-person-believe-in-the-unseen): 1,177 → 1,434 words (8 min). Added Sūrat al-Baqarah 2:3 — the verse identifying belief in the unseen (*al-ghayb*) as the first characteristic of those who follow guidance. Added Ṣaḥīḥ Muslim 8 — the Ḥadīth of Gabriel's definition of *iḥsān*: "to worship God as though you see Him, and if you do not see Him, to know that He sees you." The hadith identifies the highest dimension of religious life precisely in terms of the proper attitude toward the unseen. Em-dashes reduced from 28 to 2.

All 6 articles now display 6+ min reading time, contain at least 1 Quran citation, contain at least 1 hadith citation (5 of 6), pass the banned-word audit, pass the contrastive-negation audit, and have em-dash counts of 1–5 per article (down from 7–29).

**Topic completion:** Science & Evidence — 11 of 11 articles deep-edited across v2.6.13 + v2.6.14.

## [2.6.13] — April 2026

### Phase 2.8: Science & Evidence deep edit, Session A (5 of 11 articles)

The Science & Evidence topic is the most citation-thin of the eleven topics: 8 of the 11 articles had zero Quran AND zero hadith citations going in. Session A covers the foundational five articles on the philosophical limits of scientific naturalism: the hard problem of consciousness, the limits of science, the multiverse objection, the universe's beginning, and the god-of-the-gaps objection. Session B will cover the remaining six (evolution, religious experience, NDEs, science-and-religion, belief in the unseen) in the next release.

The Islamic citation work for this topic required care. The arguments are primarily philosophical, and forcing scriptural citations into philosophical arguments produces precisely the bad apologetics this site is designed to avoid. The approach taken: locate the verses where the Quran addresses the same questions the philosophical arguments address (the irreducibility of inner life, the limits of human knowledge, the precision of cosmic order, the framework of *āyāt* in nature, the unified beginning of the cosmos), and present them as evidence of convergence between Quranic theology and what philosophical reasoning has independently arrived at — rather than as scriptural support for the philosophical conclusions themselves.

- *The One Thing Neuroscience Cannot Explain* (consciousness-hard-problem): 928 → 1,242 words (7 min). Added Sūrat al-Isrāʾ 17:85 — the Quranic statement on the *rūḥ* ("they ask you about the spirit. Say: the spirit is of the affair of my Lord, and you have been given of knowledge only a little"). The verse identifies inner life as belonging to a domain human knowledge does not exhaust — exactly the position the hard problem has converged on fourteen centuries later. Added Sūrat al-Dhāriyāt 51:21 ("and within yourselves — will you not see?") which directs the gaze inward to find God's signs in the structure of conscious experience itself. Article previously had zero Islamic citations.

- *What Science Cannot Tell You* (science-limits): 895 → 1,309 words (7 min). Added Sūrat al-Isrāʾ 17:85 ("you have been given of knowledge only a little") establishing the Quranic position on the structural limits of human knowledge. Added Sūrat Āl ʿImrān 3:190 (the *ulū al-albāb* verse — signs for people of understanding in the alternation of night and day) showing that the Quran is not opposed to scientific inquiry but encourages it within its proper scope. Article previously had zero Islamic citations.

- *Does the Multiverse Explain Away Fine-Tuning?* (multiverse-objection): 781 → 1,196 words (6 min). Added Sūrat al-Qamar 54:49 ("indeed, We have created everything with measure") — the term *qadar* (precise calibration) names exactly what fine-tuning analysis has documented in the cosmological constants. Added Sūrat al-Mulk 67:3 ("look again: do you see any flaw?") — the verse issues an empirical challenge to examine the cosmos for design, anticipating the empirical method itself. Article previously had zero Islamic citations.

- *The Universe Had a Beginning* (big-bang-creation): 777 → 1,163 words (6 min). Added Sūrat al-Anbiyāʾ 21:30 — the famous *ratq*/*fataq* verse describing the cosmos as having begun in a unified state and then been split apart, with surprising convergence on what modern cosmology has established. Added Sūrat al-Dhāriyāt 51:47 (the active participle *mūsiʿūn*, "indeed We are expanding it") which describes ongoing cosmic expansion, the empirical confirmation of which came in the twentieth century. Article previously had zero Islamic citations.

- *Is God Just an Explanation for What We Don't Know Yet?* (god-of-gaps): 895 → 1,324 words (7 min). Added Sūrat Fuṣṣilat 41:53 ("We will show them Our signs in the horizons and within themselves") — the Quranic vocabulary of *āyāt* (signs) names the kind of evidence the serious arguments for God appeal to: features of reality, not gaps in knowledge. Added Sūrat Yūnus 10:101 ("look at what is in the heavens and the earth") — the Quranic method is to direct attention at what is known, with the recognition that what is examined points beyond itself to its source. Article previously had zero Islamic citations.

All 5 articles now display 6+ min reading time, contain at least 2 Quran citation blocks, pass the banned-word audit, pass the contrastive-negation audit, and have em-dash counts of 2 per article (down from 13–28).

The hadith count is zero across this session, by design. The hard problem of consciousness, the limits of scientific method, multiverse cosmology, the Big Bang, and the god-of-the-gaps objection are philosophical questions for which the Quran has direct address, and where the hadith literature does not carry the same kind of foundational philosophical claim. Forcing hadith citations into these articles would produce exactly the contrived apologetics the editorial overhaul is designed to remove. Future sessions on different topics will recover the typical Quran-and-hadith ratio.

## [2.6.12] — April 2026

### Phase 2.7: Rights & Freedom deep edit, Session B (6 of 13 articles, completes topic)

The second half of the Rights & Freedom topic. With Session A (v2.6.11), the entire 13-article topic is now deep-edited. The session covers the international-law dimension of apostasy, the hijab-as-control critique, same-sex attraction, ritual purity around menstruation, the fear-and-control critique, and the cult comparison. Five of the six articles had no hadith citations going in, and most articles had high em-dash density (the cult article had 22).

- *Apostasy Law and the Universal Declaration of Human Rights* (apostasy-international-law): 665 → 1,081 words (6 min). Added Sūrat al-Ghāshiyah 88:21–22 ("Remind, for you are only a reminder. You are not over them a controller") establishing structurally that even the Prophet himself was not authorised to enforce inward belief. Added Bukhārī 4351 / Muslim 1064 — the powerful hadith where the Prophet ﷺ explicitly states "I have not been commanded to investigate the hearts of people or to split open their bellies." The hadith was spoken in a specific context where suspicion of someone's outward behaviour was raised, and the Prophet's response is decisive on the limits of human judgement on inward states. Article previously had zero hadith citations.

- *Is the Hijab About Male Control, or Divine Command?* (hijab-male-control-or-divine-command): 896 → 1,252 words (7 min). Added Sūrat al-Nūr 24:31 (the parallel injunction to women, presented immediately after 24:30 to women) showing the lexical and structural parallelism with the men's instruction. Added Bukhārī 9 / Muslim 35 establishing *ḥayāʾ* as a branch of *īmān* itself — universal in its address, applicable to both genders. Article previously had zero hadith citations.

- *Islam and Same-Sex Attraction* (islam-and-same-sex-attraction): 1,146 → 1,305 words (7 min). Already a sensitive article handled with care; primarily strengthened the framing with a key prophetic citation. Added Bukhārī 6114 / Muslim 2609 — the Prophet's redefinition of strength: "the strong one is not the one who overcomes others by physical strength; the strong one is the one who controls himself when angry." The hadith generalises beyond anger to every powerful inclination the believer is asked not to act on, and locates the moral struggle of the person carrying same-sex attraction in dignity rather than in defective creature-hood. Article previously had zero hadith citations.

- *Why Does Islam Treat the Body as Impure?* (ritual-purity-wudu-menstruation): 768 → 1,177 words (6 min). Added the decisive hadith from Bukhārī 297 / Muslim 301: ʿĀʾishah's narration that the Prophet ﷺ used to recline in her lap while she was menstruating, and would recite the Quran from there. The hadith settles the question of how Islam treats the menstruating woman — the Prophet did not banish her from his side or from the most spiritually significant act he performed. Added the closing of Sūrat al-Baqarah 2:222 ("God loves those who turn to Him in repentance and those who keep themselves pure") framing purity as a positive religious virtue rather than defensive avoidance of contamination. Article previously had zero hadith citations.

- *Is Islam Just a System of Control Built on Fear?* (islam-built-on-fear): 616 → 1,331 words (7 min). Largest expansion in the session. Added Sūrat al-Anʿām 6:54 ("Your Lord has prescribed mercy upon Himself") with the unusual grammatical force of *kataba* — God has imposed mercy upon Himself as a structural feature of His own nature, the same verb used for binding religious obligations. Added Ṣaḥīḥ Muslim 2999 ("how wonderful is the affair of the believer — all of his affair is good") — the hadith identifies a structural feature of believer's existence that is incompatible with the fear-based caricature: the orientation is upward and trusting, not crouched and terrified. Article had only one hadith going in.

- *Is Islam a Cult?* (is-islam-a-cult): 1,124 → 1,404 words (8 min). Already 6 min and well-cited; primarily added a key hadith to support the article's "no living infallible authority" argument and reduce em-dash density (was 22, now 4). Added Bukhārī 7352 / Muslim 1716 — the hadith that builds honest scholarly disagreement into the structure of Islamic legal authority itself: a scholar who reaches the wrong conclusion through legitimate effort still receives one reward. The hadith makes the structural argument that a tradition that rewards honest scholarly error cannot coherently be a cult, because cults punish dissent rather than rewarding it. Article previously had zero hadith citations.

All 6 articles now display 6+ min reading time, contain at least 1 Quran and 1 hadith citation block (most have multiple), pass the banned-word audit, pass the contrastive-negation audit, and have em-dash counts of 3–4 per article (down from 8–22).

**Topic completion:** Rights & Freedom — 13 of 13 articles deep-edited across v2.6.11 + v2.6.12.

## [2.6.11] — April 2026

### Phase 2.7: Rights & Freedom deep edit, Session A (7 of 13 articles)

The Rights & Freedom topic is the most legally and theologically sensitive of the eleven topics. Session A covers the foundational seven articles: the question of leaving Islam (apostasy), what happens to non-Muslims who never received the message, post-Muslim identity, the no-compulsion verse, women in Islamic law, slavery in the sources, and the political history of apostasy law. Session B will cover the remaining six articles (the international-law dimension, hijab, same-sex attraction, ritual purity, the cult-comparison, and the fear-and-control critique) in the next release.

The topic was citation-thin. Six of the seven articles in this session had zero hadith citations before the pass, and one had no citations of any kind. All articles required careful theological-legal framing on sensitive topics where surface-level reading produces the polemical conclusions the articles are designed to address.

- *Why Can't People Leave Islam Without Consequences?* (apostasy-and-freedom): 691 → 1,367 words (7 min). Largest expansion in the session. Added Sūrat Yūnus 10:99 (the verse addressing the Prophet ﷺ directly: "if your Lord had willed, all people would have believed; will you then compel them?") which forecloses the very mechanism the polemic attributes to Islam. Added Bukhārī 1 / Muslim 1907 (actions are by intentions) — the foundational hadith of Islamic law and spirituality, decisive on the question of coerced confession. Added a new section on the early Muslim community's historical practice (the case of ʿUbaydullāh ibn Jaḥsh recorded by Ibn Hishām: emigrated as Muslim, converted to Christianity in Abyssinia, no execution on the report of his death) and a new section on contemporary scholarship (Kamali, An-Naʿim, Abou El Fadl). Article previously had zero hadith citations.

- *What Happens To Good People Who Never Heard Of Islam?* (do-good-non-muslims-go-to-hell): 772 → 1,129 words (6 min). Added Sūrat al-Baqarah 2:286 ("God does not burden any soul beyond its capacity") establishing the constraint that completes the principle of *ḥujjah*. Added the powerful hadith qudsī from Bukhārī 7404 / Muslim 2751: "My mercy precedes My wrath" — establishing the structural priority of mercy in the divine character. Cited al-Ghazālī's *Fayṣal al-Tafriqah* on those who never received the call. Article previously had zero hadith citations.

- *Can You Leave Islam And Still Be Yourself?* (post-muslim-identity): 761 → 1,001 words (6 min). Added the canonical fitrah hadith (Bukhārī 1359 / Muslim 2658) directly anchoring the article's central observation about the persistence of religious formation in post-Muslim identity. Article previously had zero hadith citations.

- *"There Is No Compulsion In Religion" — Who Does It Actually Protect?* (no-compulsion-in-religion): 653 → 1,381 words (7 min). Added Sūrat al-Raʿd 13:40 ("your duty is only to convey the message; reckoning is upon Us") establishing the structural limit on the prophetic role itself. Added Bukhārī 1 / Muslim 1907 (the intention hadith). Added a new section on the Prophet's own example of restraint (the Constitution of Medina, the conquest of Mecca where forced conversion was structurally unavailable) and a new section on what the verse rules out vs. what it does not. Article previously had zero hadith citations.

- *Women, God, and Islamic Law* (women-in-islam): 1,047 → 1,310 words (7 min). Already 6 min and reasonably cited; primarily strengthening citations and adding the equal-spiritual-standing argument. Added Sūrat al-Naḥl 16:97 (whoever does righteous deed, male or female... We will give them a good life) explicitly grounding the equal spiritual standing in canonical text. Added Bukhārī 3331 / Muslim 1468 — the famous farewell-sermon hadith *istawṣū bi'l-nisāʾi khayran* ("take care of women, treat them well") — delivered to the largest assembly of Muslims of the Prophet's lifetime. Article previously had zero hadith citations.

- *Slavery in Islamic Sources: The Hardest Question* (slavery-in-islamic-sources): 676 → 1,090 words (6 min). Added Sūrat al-Nūr 24:33 (the Quranic command on the *mukātabah* contract of self-purchase) establishing the structural legal mechanism by which Islam built dismantling into the institution of slavery itself, with community-funded assistance. Added Bukhārī 30 / Muslim 1661 — the Prophet's powerful instruction on the treatment of slaves: "they are your brothers whom God has placed under your hand. Feed them from what you eat, clothe them from what you wear..." which abolishes the moral logic of chattel slavery even where the institution itself was not abolished outright. Article previously had zero hadith citations.

- *The Political History of Apostasy Law in Islam* (apostasy-political-history): 697 → 1,043 words (6 min). Added the Sūrat al-Baqarah 2:256 (no-compulsion) anchor that was missing from the article entirely (article had zero citations going in). Added Bukhārī 7185 / Muslim 1713 — the Prophet's own statement that even he, when functioning as a judge, could only rule on what was manifest, with the interior reality reserved to God. The hadith establishes the structural limit on what human courts can adjudicate, decisive for the question of criminalising private disbelief. Article previously had zero citations.

All 7 articles now display 6+ min reading time, contain at least 1 Quran and 1 hadith citation block (most have multiple), pass the banned-word audit, pass the contrastive-negation audit, and have em-dash counts of 2–4 per article (down from 6–15).

## [2.6.10] — April 2026

### Architectural change: About-page mission content migrated off the front page

The "What this site is" mission section (~650 words across four sub-headed sub-sections: The premise, The method, What the work covers, Who this is for) was previously rendered inline on the home page between the quick-access cards and the article grid. This made the home page feel heavy and pushed the actual articles below the fold for any visitor on a typical viewport. Mission content of this length is About-page material rather than home-page material.

**Front page:** removed the entire `<section class="home-mission">` block (lines 177–223 in `front-page.php`). The home page now flows from hero → quick-access cards → article grid, which is the architecture a content-driven site of this kind should have.

**About page:** replaced the older auto-created About page content with the better mission writing that previously lived on the home page. The migration also fixed a stale article count (the front-page version mentioned "120 articles" — actual count is 123, so the new About page omits the specific number and uses general phrasing). Removed the front-page-specific "Browse all 120 articles" CTA button and replaced with a simpler `<a href="/articles">Browse all articles →</a>` link in keeping with About-page formatting.

**Production-upgrade path:** added a new `ce_update_about_page_v2()` function in `inc/ce-secondary-pages.php`. The auto-page-creation logic only runs once per site (using the `ce_secondary_pages_created` flag), which means simply updating the theme file does not refresh the About page on sites where the theme was previously activated. The new function uses its own flag (`ce_about_v2_updated`) and runs exactly once via `wp_update_post()` to refresh the existing About page content on existing installs. The flag prevents repeated overwrites if the site administrator further customises the About page after the upgrade has run.

**Whitelist hash update:** because `inc/ce-secondary-pages.php` was modified, its SHA-256 changed (from `dd4f253944839cba...` to `ca4e9220e06077e6...`). The WP Perf Shield plugin (where this file is whitelisted to suppress the false-positive `compelling-evidence.com` substring match) needs the corresponding hash update — shipped as WP Perf Shield v1.2.1.

## [2.6.9] — April 2026

### Phase 2.6: Revelation & Meaning deep edit, Session B (6 of 11 articles, completes topic)

The second half of the Revelation & Meaning topic. With Session A (v2.6.8), the entire 11-article topic is now deep-edited. The session covers the epistemology of revelation (does God communicate, what authentic revelation would look like, how to evaluate competing claims), what draws people to Islam, the eschatology, and the spiritual heart of the tradition.

The topic continued to be citation-thin: 2 of the 6 articles in this session had zero citations of any kind before the pass, and several had no hadith. All articles also had high em-dash counts (12–28 per article).

- *Does God Communicate With Humanity?* (does-god-communicate-with-humanity): 870 → 1,254 words (7 min). Article had a structural bug at the end with mis-nested `</p>` tags inside the closing argument — fixed during rewrite. Kept the existing Sūrat al-Shūrā 42:51 (the three modes of divine communication) and expanded its translation to include the full verse. Added Sūrat al-Shuʿarāʾ 26:192–195 (the Quran's claim about its own source and the agent of transmission). Added Ṣaḥīḥ al-Bukhārī 2 — the Prophet's first-person description of the experience of revelation as physically painful (the ringing-of-a-bell hadith), which argues against the imagination-source hypothesis.

- *What Would Authentic Revelation Look Like?* (what-would-authentic-revelation-look-like): 1,254 → 1,408 words (8 min). Same closing-tag bug as the previous article — fixed. Added Sūrat al-Ikhlāṣ 112:1–4 anchoring the "honour the reality of God" criterion in the canonical creedal text. Replaced Sūrat al-Anʿām 6:164 with the parallel verse Sūrat Fāṭir 35:18 ("if a heavily-laden soul calls for its load to be carried, none of it will be carried") which makes the personal-accountability criterion more sharply. Added Bukhārī 6018 / Muslim 47 ("whoever believes in God and the Last Day should speak good or remain silent...") establishing that authentic belief in revelation produces practical moral consequence.

- *How Do We Evaluate Competing Claims to Revelation?* (how-do-we-evaluate-competing-claims-to-revelation): 704 → 1,122 words (6 min). The shortest article in the topic at 4 min display going in. Added Sūrat al-Baqarah 2:111 (the Quran's "bring your proof" challenge) anchoring the call for evidential symmetry — the same standard the Muslim must meet, the inquirer applies to all claimants. Added Sūrat al-Anbiyāʾ 21:107 (Muḥammad sent as a mercy to the worlds) on the universality criterion. Added Bukhārī 335 / Muslim 521 — the Prophet's explicit statement that earlier prophets were sent to specific peoples while his mission was to all of humanity. Article previously had zero citations.

- *What Draws People to Islam Today?* (what-draws-people-to-islam-today): 995 → 1,211 words (7 min). Added Sūrat al-Baqarah 2:23 (the Quran's "produce a sūrah like it" challenge) anchoring the literary-character argument. Added the canonical fitrah hadith (Bukhārī 1359 / Muslim 2658) directly connecting the convert "reversion" pattern to the doctrine that predicts it. Article previously had zero citations.

- *What Does Islam Say Happens After Death?* (what-does-islam-say-happens-after-death): 1,561 → 1,650 words (9 min). Already 8 min and well-cited; primarily structural cleanup and citation strengthening. Converted the prose-quoted "garden or pit" hadith into a proper citation block (Sunan al-Tirmidhī 2460). Added the powerful "more merciful than this woman is to her child" hadith (Bukhārī 5999 / Muslim 2754) — the hadith was spoken by the Prophet ﷺ at the moment of seeing a captive woman searching frantically for her child and finally embracing her with overwhelming relief. Em-dashes reduced from 28 to 5.

- *The Spiritual Heart of Islam* (the-spiritual-heart-of-islam): 1,036 → 1,247 words (7 min). Article previously had zero citations despite being about the deepest practices in the tradition. Added Ṣaḥīḥ Muslim 8 — the famous Ḥadīth Jibrīl on the three dimensions of religion (Islām, Īmān, Iḥsān), with iḥsān defined as "to worship God as though you see Him." Added Sūrat al-Baqarah 2:152 ("remember Me, I will remember you") and Sūrat al-Raʿd 13:28 ("in the remembrance of God hearts find rest") anchoring the dhikr practice. Added Bukhārī 52 / Muslim 1599 — the Prophet's statement on the heart as the seat where the entire spiritual architecture takes its effect.

All 6 articles now display 6+ min reading time, contain at least 1 Quran and 1 hadith citation block (most have multiple), pass the banned-word audit, pass the contrastive-negation audit, and have em-dash counts of 2–5 per article (down from 12–28).

**Topic completion:** Revelation & Meaning — 11 of 11 articles deep-edited across v2.6.8 + v2.6.9.

## [2.6.8] — April 2026

### Phase 2.6: Revelation & Meaning deep edit, Session A (5 of 11 articles)

The Revelation & Meaning topic addresses the core question of human purpose, the comparison between Islam and other monotheisms, the doctrine of shirk and its proportionality, the place of doubt in Islamic intellectual life, and the cognitive science of religious belief. Session A covers the foundational 5 articles. Session B will cover the remaining 6 (revelation epistemology and the spiritual life) in the next release.

The topic was citation-thin going in: 4 of the 5 articles in this session had zero hadith citations before the pass, and several articles relied on the contested "hidden treasure" hadith (already noted in earlier sessions as a problem). All articles also had high em-dash counts (24–30 per article) and significant contrastive-negation density.

- *What Is the Purpose of Life?* (purpose-of-life): 1,459 → 1,436 words (8 min). Already 8 min; primarily cleanup and citation strengthening. Removed the contested "hidden treasure" hadith, replacing it with Sūrat Qāf 50:16 (closer than the jugular vein) which makes the same point about divine nearness through canonical text. Added Ṣaḥīḥ Muslim 2999 ("how wonderful is the affair of the believer — all of his affair is good"). Em-dashes reduced from 30 to 4.

- *Why Islam and Not Another Religion?* (why-islam-not-christianity): 847 → 1,097 words (6 min). Added Sūrat al-Ikhlāṣ 112:1–4 — the four-line creedal statement of divine unity — anchoring the "first filter: nature of God" argument in canonical text rather than philosophical assertion alone. Added Sūrat al-Ḥijr 15:9 (the Quran's own claim about its preservation) anchoring the second filter on scripture. Note: "New Testament" appears once in the text — this is the proper name of the Christian scripture canon, not the banned figurative use of "testament" meaning evidence/proof.

- *Why Is Associating Partners With God The One Unforgivable Sin?* (shirk-unforgivable): 1,195 → 1,414 words (8 min). Added Sūrat al-Isrāʾ 17:15 (We never punish until We have sent a messenger) anchoring the principle that *ḥujja* must be established before accountability attaches — directly addresses the question of sincere theological error. Added a powerful hadith qudsī from Sunan al-Tirmidhī 3540: "if you came to Me with sins as great as the earth and met Me without associating anything with Me, I would meet you with forgiveness as great as that" — establishing the asymmetry the proportionality objection has trouble accommodating. Em-dashes reduced from 25 to 3.

- *Is Doubt Permitted in Islam?* (doubt-permitted-in-islam): 1,090 → 1,262 words (7 min). Kept the three existing Quranic citations on reason and inquiry. Added Sunan Ibn Mājah 224 (seeking knowledge as a universal obligation — the structural argument for why doubt-driven inquiry is required rather than forbidden). Added the powerful hadith from Ṣaḥīḥ Muslim 132 — when companions reported intrusive thoughts so disturbing they would rather fall from the sky than utter them, the Prophet ﷺ replied "that is the clearest sign of faith." The hadith reframes intrusive doubt (*waswās*) as evidence of a faith that is real rather than its absence. Em-dashes reduced from 29 to 5.

- *Why Do Human Beings Believe In God At All?* (why-humans-believe-in-god): 1,257 → 1,263 words (7 min). Article previously had only one Quran citation despite directly addressing the cognitive science of religion question. Added the canonical fitrah hadith (Bukhārī 1359 / Muslim 2658) — the classical hadith that maps directly onto what cognitive science has found about default human orientation toward God. The hadith is decisive for the article's argument because it identifies the underlying orientation the cognitive mechanisms serve. Em-dashes reduced from 30 to 2.

All 5 articles now display 6+ min reading time, contain at least 2 Quran citations and at least 1 hadith citation block (most have multiple), pass the contrastive-negation audit, and have em-dash counts of 2–5 per article (down from 24–30).

## [2.6.7] — April 2026

### Phase 2.5: History, Context & Comparison deep edit, Session B (5 of 10 articles, completes topic)

The second half of the History, Context & Comparison topic. With Session A (v2.6.6), the entire 10-article topic is now deep-edited. Three articles in this session needed substantial expansion (under 6 min display); two articles dealt with sensitive topics (honour killings and the question of warfare in the Prophet's career) requiring careful citation work.

- *The Freethinkers Islam Produced* (the-freethinkers-islam-produced): 1,126 → 1,348 words (7 min). Added Sūrat al-Zumar 39:18 (those who listen to the word and follow the best of it — describing the practice of intellectual engagement across positions) and Ṣaḥīḥ al-Bukhārī 7352 / Muslim 1716 (the judge who exerts effort and reaches the right ruling gets two rewards, who errs gets one — establishing the principle that honest scholarly disagreement is rewarded). Article previously had zero Islamic citations.

- *Honour Killings: Culture, Not Islam* (honour-killings-culture-not-islam): 643 → 1,065 words (6 min). Largest expansion in this session. Added Sūrat al-Māʾidah 5:32 (whoever kills a soul without right is as if he killed all of humanity) framing the cosmic weight of one human life. Added the powerful hadith from the Prophet's farewell sermon (Ṣaḥīḥ Muslim 1218) — "every matter of pre-Islamic ignorance is under my feet, abolished, including the blood-feuds of the pre-Islamic period" — directly placing tribal honour-killing logic among the practices the Prophet explicitly buried. Kept the existing Sūrat al-Isrāʾ 17:33 and Sūrat al-Baqarah 2:228 citations. Added a closing section on the honest conclusion: the practice violates every level the tradition speaks to, and the remedy runs through the same texts the practice violates.

- *Isn't Your Religion Just an Accident of Where You Were Born?* (religion-of-your-birth): 869 → 1,196 words (6 min). Added the canonical fitrah hadith (Ṣaḥīḥ al-Bukhārī 1359 / Muslim 2658) — directly addressing the geographic correlation argument with the Islamic claim that the original orientation is one and the cultural overlay varies. Added Sūrat al-Baqarah 2:170 (the Quran's own criticism of religion held merely as ancestral inheritance — the verse turns the geographic objection back on the Muslim who has not examined the faith they inherited).

- *Why Does Islam Spread So Fast in Prison?* (islam-prison-conversion): 733 → 1,140 words (6 min). Added Sūrat al-Zumar 39:53 ("O My servants who have wronged themselves, do not despair of God's mercy. God forgives all sins") — one of the most quoted verses among prison populations. Added Sūrat al-Furqān 25:70 (those who repent will have their evil deeds replaced with good ones) — the verse that addresses the question of whether the past must remain the past. Kept the existing Ibn Mājah 4251 hadith. Article previously had only one hadith citation despite directly addressing the question of conversion.

- *If Muhammad Was a Prophet, Why Did He Need a Sword?* (muhammad-and-warfare): 687 → 1,182 words (6 min). Added the foundational Quranic charter for defensive warfare: Sūrat al-Ḥajj 22:39 (permission to those who are fought, because they have been wronged), Sūrat al-Baqarah 2:190 (fight those who fight you, do not transgress), and Sūrat al-Baqarah 2:193 (if they cease, no aggression). The three verses establish that Quranic permission for warfare is conditional, narrowly framed, and has explicit termination conditions. Expanded the hadith on rules of engagement (Sunan Abī Dāwūd 2614) to include the full text including "do not be treacherous" and "do good — God loves those who do good." Added context on Abū Bakr's instructions to commanders extending the constraints. Article previously had zero Quranic citations on a topic that turns directly on what the Quran actually says about warfare.

All 5 articles now display 6+ min reading time, contain at least one Quran and one hadith citation block (most have multiple), pass the banned-word audit, pass the contrastive-negation audit, and have em-dash counts of 2–4 per article (down from 11–21).

**Topic completion:** History, Context & Comparison — 10 of 10 articles deep-edited across v2.6.6 + v2.6.7.

## [2.6.6] — April 2026

### Phase 2.5: History, Context & Comparison deep edit, Session A (5 of 10 articles)

This topic addresses the contextual and comparative questions about Islam: the relationship to science and the Enlightenment, the harm-and-political-control objections, and the experience of ex-Muslims who feel they were defending a version of Islam that did not exist. The topic was citation-thin: 3 of the 5 articles in this session had zero Islamic citations before the pass.

- *Islam and the Scientific Tradition* (islam-and-science): 662 → 1,129 words (6 min). Expanded the Sūrat al-ʿAlaq 96:1 opening to include verses 1–5 (the full revelation of the pen and acquired knowledge). Added Sūrat al-Zumar 39:9 (are those who know equal to those who do not?) and Sunan Ibn Mājah 224 (seeking knowledge as a universal obligation). Added context on the rational sciences (al-ʿulūm al-ʿaqliyyah) and named figures (Ibn al-Haytham, Ibn Sīnā, al-Bīrūnī).

- *Hasn't Religion Caused Enough Harm?* (religion-cause-harm): 815 → 1,225 words (7 min). Added Sūrat al-Māʾidah 5:32 (whoever kills a soul... it is as if he killed all of humanity), Sūrat al-Nisāʾ 4:135 (stand firmly for justice even against yourselves), and Ibn Mājah 2341 / Muwaṭṭaʾ Mālik 31:31 (lā ḍarar wa lā ḍirār — the foundational juridical principle that there shall be no harm and no reciprocal harm). Article previously had zero citations.

- *Isn't Religion Just A Tool Of Political Control?* (religion-is-political-control): 747 → 1,083 words (6 min). Added Sūrat al-Nisāʾ 4:135 (stand for justice against your own faction) and Sunan Abī Dāwūd 4344 / al-Tirmidhī 2174 ("the best jihad is a word of truth in the presence of a tyrannical ruler") — both directly addressing the structural mechanism by which religion gets weaponised for political control. Article previously had zero citations.

- *Why Hasn't Islam Had Its Enlightenment?* (islam-and-enlightenment): 808 → 1,038 words (6 min). Kept the existing Sūrat al-Naḥl 16:125 citation. Added Sūrat al-Mujādalah 58:11 (God will raise those who believe and those given knowledge) and Sunan Ibn Mājah 224 (seeking knowledge is an obligation upon every Muslim).

- *The Islam I Was Defending Did Not Exist* (the-islam-i-was-defending): 1,160 → 1,448 words (8 min). Added Sūrat al-Isrāʾ 17:36 (do not pursue what you have no knowledge of) — fits the article's central argument about superficial vs. deep engagement directly. Added Sunan Abī Dāwūd 3641 / al-Tirmidhī 2682 (the scholars are heirs of the prophets, who leave knowledge as inheritance). Article previously had zero citations despite running 1,160 words.

All 5 articles now display 6+ min reading time, contain at least one Quran and one hadith citation block, pass the banned-word audit, pass the contrastive-negation audit, and have em-dash counts of 2–3 per article (down from 7–21).

## [2.6.5] — April 2026

### Phase 2.4: Does God Exist? deep edit, Session B (6 of 12 articles, completes topic)

The second half of the Does God Exist? topic, covering questions about the nature of God once theism is granted: personal vs. deistic, divine hiddenness, free will and predestination, the projection objection, the burden of proof, and the meaning question. Together with Session A (v2.6.4), the entire 12-article topic is now deep-edited.

**Notable repair:** divine-hiddenness contained corrupted Arabic in its Quran citation block — `وَفِي! أَنفُسِهِمْ` had a stray `!` injected into the middle of the verse, and the citation closed with `ل3ل5` (apparently a transliteration artifact mistaken for Arabic characters). Repaired with the proper Arabic text of Sūrat Fuṣṣilat 41:53. The article also contained the contested "hidden treasure" hadith without Arabic text or any chain disclaimer; replaced with Sūrat al-Aʿrāf 7:172 (the primordial covenant) and Ṣaḥīḥ al-Bukhārī 1359 / Muslim 2658 (the canonical fitrah hadith with proper Arabic and full chain attestation).

- *Is God Personal Or Just A First Cause?* (god-personal-or-deist): 1,001 → 1,221 words (7 min). Added Sūrat Qāf 50:16 (closer than the jugular vein) and Sūrat al-Baqarah 2:186 (when My servants ask, I am near) — both directly contradicting the deist's distant clockmaker picture. Article previously had zero citations.

- *If God Wants To Be Known, Why Is God Hidden?* (divine-hiddenness): 1,293 → 1,454 words (8 min). Repaired corrupted Arabic in Sūrat Fuṣṣilat 41:53 citation. Replaced contested hidden-treasure hadith with the canonical fitrah hadith (Bukhārī 1359 / Muslim 2658) and Sūrat al-Aʿrāf 7:172 (primordial covenant), both with proper Arabic text and verified chains.

- *If God Knew Everything in Advance, How Is My Choice Genuinely Free?* (free-will-predestination): 849 → 1,303 words (7 min). Added Ṣaḥīḥ al-Bukhārī 6594 / Muslim 2643 (the four-written-matters hadith on divine decree at conception) with the classical commentators' anti-fatalist reading. Added Ṣaḥīḥ al-Bukhārī 6614 / Muslim 2652 (the Adam–Moses dialogue on qadar). Article previously had no hadith citations.

- *Is God Just a Projection of Human Psychology?* (god-as-psychological-projection): 877 → 1,189 words (6 min). Added the canonical fitrah hadith (Bukhārī 1359 / Muslim 2658) and Sūrat al-Aʿrāf 7:172 (primordial covenant) — both directly addressing the projection theorist's account of religious universality with an alternative account of the same phenomenon. Article previously had zero citations.

- *Who Bears The Burden of Proof?* (burden-of-proof): 846 → 1,240 words (7 min). Added Sūrat Ibrāhīm 14:10 (the rhetorical "is there doubt about God, the Originator of the heavens and the earth?") and Sūrat Luqmān 31:25 (if you ask them who created the heavens and the earth, they will say: God) — both reframing the question by inverting the assumed default position. Article previously had zero citations.

- *If Nothing Really Matters, Why Does It Feel Like It Should?* (if-nothing-really-matters): 1,217 → 1,423 words (8 min). Added Sūrat al-Muʾminūn 23:115 (did you think We created you in vain?) and Sūrat al-Qiyāmah 75:36 (does the human think he will be left without purpose?) — both posing the meaning question with the same directness modern philosophy has only recently rediscovered. Article previously had zero citations despite running 1,217 words.

All 6 articles now display 6+ min reading time, contain at least one Quran citation block (most have 2+ Quran with hadith where natural), pass the banned-word audit, pass the contrastive-negation audit, and have em-dash counts of 2–4 per article (down from 13–24).

**Topic completion:** Does God Exist? — 12 of 12 articles deep-edited across v2.6.4 + v2.6.5.

## [2.6.4] — April 2026

### Phase 2.4: Does God Exist? deep edit, Session A (6 of 12 articles)

The Does God Exist? topic contains 12 articles covering the classical theistic arguments and questions about the nature of God. The topic was citation-thin: only 2 of 12 articles had any Islamic citations before the pass. Session A covers the first 6 articles (the foundational arguments). Session B will cover the remaining 6 (questions about the nature of God once theism is granted) in the next release.

All 6 articles in this session had pre-existing structural issues from earlier edits (large content blocks injected mid-paragraph with dangling text after closing tags) — repaired during the rewrite.

- *Why Does Anything Exist Rather Than Nothing?* (why-does-anything-exist): 1,097 → 1,330 words (7 min). Added Sūrat al-Ṭūr 52:35–36 (the Quranic posing of the cosmological trilemma: created from nothing, themselves the creators, or by something else?) and Āyat al-Kursī (Sūrat al-Baqarah 2:255) for divine self-subsistence (al-Ḥayy al-Qayyūm). Article previously had zero citations.

- *The Universe Is Absurdly Specific* (fine-tuning-universe): 1,026 → 1,362 words (7 min). Added Sūrat al-Mulk 67:3–4 (the Quran's empirical challenge: "look again — do you see any rifts?") and Sūrat Fuṣṣilat 41:53 (signs in the horizons and within yourselves) framing modern fine-tuning research as the kind of looking the Quran calls for.

- *The God Who Cannot Not Exist* (ontological-argument): 858 → 1,239 words (7 min). Added Sūrat al-Ikhlāṣ 112:1–4 (the four-line Quranic articulation of necessary, indivisible, self-subsisting, unparalleled existence — corresponding directly to the metaphysical features the modal argument identifies). Added Sūrat al-Anbiyāʾ 21:22 (the unicity argument from divine plurality leading to ruin), addressing the modal argument's worry about plural maximally great beings.

- *If Your Brain Is Just Atoms, Can You Trust It?* (argument-from-reason): 735 → 1,177 words (6 min). The shortest in this session; needed the most expansion. Added Sūrat al-Mulk 67:23 (hearing, sight, and hearts as God-given cognitive equipment) and Sūrat al-Isrāʾ 17:36 (the same faculties held to account for proper use). Added Sunan Abī Dāwūd 4403 / al-Tirmidhī 1423 (the hadith establishing intellect as the precondition for moral and religious responsibility — "the pen is lifted from three" hadith).

- *Can Existence Come Out Of Nothing?* (existence-come-out-of-nothing): 871 → 1,259 words (7 min). Added Sūrat al-Ṭūr 52:35 (the same Quranic trilemma framing) and Sūrat al-Anbiyāʾ 21:30 (the heavens and earth as a joined entity that We separated — the Quran's image of the early universe). Article previously had zero citations.

- *Does A Higher Power Exist?* (does-god-exist): 873 → 1,236 words (7 min). Added Sūrat al-Baqarah 2:164 (signs in the alternation of night and day, addressed to those who use intellect — qawm yaʿqilūn) and Sūrat al-Rūm 30:22 (signs in the diversity of human languages and colours — pairing cosmic-scale and human-scale evidence).

All 6 articles now display 6+ min reading time, contain at least 2 Quran citation blocks (one also adds a hadith on intellect), pass the banned-word audit, pass the contrastive-negation audit, and have em-dash counts of 2–3 per article (down from 10–24).

A note on hadith for this topic: hadith citations are less natural for abstract metaphysical arguments (existence, fine-tuning, modal logic) than for practice-and-conduct topics. Where hadith fit (argument-from-reason, on the rational faculty as precondition for accountability) they are included. Where hadith would feel forced, the article relies on Quranic verses directly addressing metaphysical questions — of which there are many.

## [2.6.3] — April 2026

### Phase 2.3: The Problem of Evil deep edit (9 articles)

The largest topic deep-edit so far: 9 articles covering the philosophical and theological responses to suffering, hell, unanswered prayer, and creation. Several articles had pre-existing structural bugs from earlier edits (duplicate paragraphs at the end of bodies, misplaced text blocks injected mid-sentence, dangling text after closing tags) which were repaired during the rewrite.

- *If God Is Good, Why Is There Suffering?* (problem-of-evil): 1,248 → 1,256 words (7 min). Fixed a major structural bug where a 350-word block had been injected mid-sentence between "The problem of evil exists in two forms." and the rest of that sentence. Removed duplicate identical closing paragraph that appeared twice. Added Ṣaḥīḥ al-Bukhārī 5641 on suffering as expiation.

- *Earthquakes, Cancer, and a Good God* (natural-evil): 931 → 1,185 words (6 min). Added Sūrat al-Anbiyāʾ 21:35 (We test you with evil and good as a trial), and Ṣaḥīḥ al-Bukhārī 5660 on illness shedding sins like leaves from a tree. Article previously had zero citations; now grounded in two.

- *But What About The Problem Of Evil?* (problem-of-evil-response): 906 → 1,232 words (7 min). Added Sūrat al-Mulk 67:2 (created death and life to test which of you is best in deed) and the "wonderful is the affair of the believer" hadith from Ṣaḥīḥ Muslim 2999. Article previously had zero citations.

- *Why Would God Send Anyone To Hellfire?* (why-hellfire): 873 → 1,056 words (6 min). Added the mercy-precedes-wrath hadith (Bukhārī 7554, Muslim 2751) and the 100-parts-of-mercy hadith (Muslim 2752) establishing the priority and scope of divine mercy before the question of hell can be properly addressed.

- *If God Listens, Why Doesn't God Respond?* (unanswered-prayer): 870 → 1,105 words (6 min). Converted the prose-quoted "three responses" hadith into a proper citation block (Musnad Aḥmad 11149: God hastens, stores, or averts an equal harm). Added Ṣaḥīḥ Muslim 2735 on the trap of haste in supplication.

- *Why Doesn't God Simply Forgive Everyone?* (universal-salvation): 1,133 → 1,222 words (7 min). Already over the 6-min threshold; primarily citation enrichment + cleanup of 6 contrastive-negation instances and 24 em-dashes. Added Ṣaḥīḥ Muslim 2752 (100 parts of mercy) and Ṣaḥīḥ Muslim 2755 (the symmetry-of-hope-and-fear hadith on the believer and disbeliever's perception of mercy and punishment).

- *Why Did God Create Humanity Knowing How Much We Would Suffer?* (why-create-knowing-suffering): 940 → 1,206 words (7 min). Fixed duplicate-paragraph structural bug at end of body (the khalīfah's-vocation closing appeared twice in slightly different forms). Added Sūrat al-Mulk 67:2 anchoring the testing-purpose framing in the Quran. Strengthened the disclosure on the hidden-treasure hadith's contested chain of transmission.

- *Finite Sins, Infinite Punishment: Is Hell Proportionate?* (finite-sins-infinite-punishment): 723 → 1,143 words (6 min). The shortest in the topic at 4 min display. Added Sūrat al-Zalzalah 99:7–8 (atom's weight of good, atom's weight of evil) establishing the granularity of divine accounting. Added Ṣaḥīḥ al-Bukhārī 44 on those who exit hellfire with the smallest measure of good in their hearts, qualifying the popular caricature of unqualified eternal damnation.

- *Suffering and God: The Islamic Account* (suffering-and-god): 1,509 → 1,454 words (8 min). Already 8 min; cleanup pass. Em-dashes reduced from 33 to 5. Six contrastive-negation instances rewritten. Converted the prose-quoted "eyes shed tears" hadith into a proper citation block (Ṣaḥīḥ al-Bukhārī 1303). Added Ṣaḥīḥ al-Bukhārī 5641 on suffering as expiation.

All 9 articles now display 6+ min reading time, contain at least one Quran and one hadith citation block (most have multiple), pass the banned-word audit, pass the contrastive-negation audit, and have em-dash counts of 2–5 per article (down from 8–33).

## [2.6.2] — April 2026

### Phase 2.2: Ethics Without God? deep edit (5 articles)

This topic required substantial citation work: 4 of 5 articles had zero Quran or hadith citations before the pass. The articles deal with secular ethical theory and the moral argument for God's existence, where the Islamic position needs to be visible alongside the philosophical argument.

- *Where Do Moral Facts Live?* (moral-argument-god): 880 → 1,262 words (7 min). Added a section on fitrah and the moral sense with Sūrat al-Shams 91:7–10 (the soul inspired with awareness of its wickedness and piety) and Ṣaḥīḥ Muslim 2553 (righteousness is good character; sin is what wavers in the chest). Added Sūrat al-Naḥl 16:90 anchoring the divine nature theory in the Quran's explicit statement of what God commands and forbids. The article previously had zero citations; it now has three citation blocks and is grounded directly in the Islamic articulation of the moral argument.

- *Can You Ground Ethics Without God?* (ethics-without-god): 821 → 1,291 words (7 min). Added a section on fitrah as the Islamic frame for the secular humanist's appeal to innate compassion, with Sūrat al-Rūm 30:30 on the fitrah of God upon which He created people, and Musnad Aḥmad 17999 (consult your heart; righteousness is what the soul finds peace in). The hadith presents the inner moral compass as a universal capacity, which is exactly what the secular humanist relies on without explicit acknowledgement of its source.

- *Is Morality Just Opinion?* (objective-morality): 816 → 1,205 words (7 min). Added a section on the anthropological evidence for moral universals (in-group prohibition on murder, obligation to care for children, norm of reciprocity, prohibition on theft, expectation of honesty, requirement of justice in distribution of basic goods). Added Sūrat al-Shams 91:7–8 grounding the universal capacity in fiṭrah, and Ṣaḥīḥ Muslim 2553 on the experiential phenomenology of conscience. Article previously had zero citations; now grounded in two.

- *What Ethics & Morality In Irreligion?* (ethics-morality): 693 → 1,356 words (7 min). The shortest in the topic at 4 min display, this article needed the most expansion. Added a section on the genealogy problem (secular ethics inheriting concepts like inherent human dignity from monotheistic intellectual traditions whose theological grounding it has officially removed), a section on fitrah with Sūrat al-Rūm 30:30, and the consult-your-heart hadith from Musnad Aḥmad 17999. Added Sūrat al-Naḥl 16:90 grounding the theistic answer in the Quran's explicit statement.

- *Is Goodness What God Commands, Or Does God Command What Is Good?* (euthyphro-dilemma): 829 → 1,254 words (7 min). Expanded the Ashʿarite/Muʿtazilite section with the names of the major Ashʿarite contributors (al-Ashʿarī, al-Bāqillānī, al-Juwaynī, al-Ghazālī). Added Ṣaḥīḥ Muslim 2577 (the hadith qudsī "I have forbidden injustice on Myself") which forecloses the dilemma's first horn at the level of the tradition's own most explicit text. Added Sūrat al-Nisāʾ 4:135 on standing firm for justice even against oneself, supporting the claim that Quranic moral epistemology relies on the believer's capacity to recognise justice independently.

All 5 articles now display 7 min reading time, contain at least one Quran and one hadith citation, pass the banned-word audit, pass the contrastive-negation audit, and have em-dash counts of 1–3 per article (down from 11–19). Pre-existing HTML structure issues in three of the articles (dangling sentence fragments after closing `</p>` tags, unmatched paragraph tags) were repaired during the rewrite.

## [2.6.1] — April 2026

### Phase 2.1: Divine Justice & Fairness deep edit (5 articles)

Topic-level deep edit applied with the same standards as Phase 1's Islamic Practice & Ritual benchmark: 6+ min reading time minimum, anti-AI compliance (no banned vocabulary, no contrastive negations), em-dash counts reduced from 16–28 to 4–5 per article, citation work where appropriate.

- *Why Would God Need To Be Worshipped?* (why-does-god-need-compelled-worship): 788 → 1,375 words (7 min). Added a section on the contrast with pagan theologies (Greek, Roman, Mesopotamian, pre-Islamic Arabian) where gods genuinely needed sacrifice, with Sūrat al-Ḥajj 22:37 ("their meat does not reach God, nor their blood, but what reaches Him is the God-consciousness from you") as the Quran's own response to that frame. Introduced al-Ṣamad as the technical Arabic term for self-sufficiency. Identified ʿujb (self-amazement, the inflated self-regard that ends in collapse) as the classical Islamic identification of the same psychological pattern modern psychology calls narcissistic vulnerability.

- *If God Answers Prayer, Why Can't You Demonstrate It?* (does-god-answer-prayer): 1,382 → 1,337 words (7 min). Already over the 6-min threshold; cleanup-only pass. Em-dashes reduced from 28 to 4. Three contrastive negations rewritten. The Prophet's hadith on hasty supplication (Ṣaḥīḥ Muslim 2735) converted from prose quotation to proper hadith-citation block. Tightened the prose throughout.

- *If God Doesn't Need Worship, Why Does the Quran Demand It?* (why-does-god-need-worship): 835 → 1,196 words (6 min). Added a section on al-Ghazālī's treatment of why ritual takes its specific bodily-postural form (Iḥyāʾ ʿulūm al-dīn), connecting the prescribed actions to the architecture of the human creature whose consciousness is shaped by repeated bodily action. Added the "smile is charity" hadith (Sunan al-Tirmidhī 1956) showing the breadth of ʿibādah as a category. Added Sūrat Fāṭir 35:15 (you are the ones in need of God) as a paired Quran citation.

- *If God Rewards Faith and Punishes Doubt, Isn't That Just a Loyalty Test?* (god-rewards-faith-punishes-doubt): 971 → 1,297 words (7 min). Fixed pre-existing HTML structure issues (missing closing tags, dangling sentence fragments at the end of the body). Added Sūrat al-Baqarah 2:170 explicitly rejecting taqlīd of inherited belief without independent examination. Expanded the section on Quranic emphasis on reflection (a-fa-lā yaʿqilūn / a-fa-lā yatadabbarūn / a-fa-lā yatafakkarūn / la-ʿallakum taʿqilūn). Added discussion of the category of ahl al-fatrah (people of the gap) covering those outside the reach of clear prophetic communication.

- *Why Would God Create Enemies?* (why-would-god-create-enemies): 1,479 → 1,581 words (8 min). Already over the 6-min threshold; primarily a cleanup pass. Em-dashes reduced from 24 to 5. Four contrastive negations rewritten. Tightened the closing section on tawḥīd as the organising principle that answers the various challenges together.

## [2.6.0] — April 2026

### Phase 1 cleanup + Islamic Practice & Ritual deep edit

A comprehensive audit of all 123 articles identified the following:
- 84% (103) under 6 min reading time, target is 6–15 min
- 22 articles with banned AI vocabulary (40+ word list from anti-AI style guide)
- 49 articles with 75 contrastive-negation instances detected
- 11 Islamic-topic articles missing both Quran and hadith citations
- 2,081 em-dashes across the corpus (avg 17 per article), heaviest single article had 35

**Mechanical cleanup applied to all 123 articles** (safe, regex-based, no editorial judgement required):

29 banned-word swaps applied via context-aware replacement map (e.g., adaptive→flexible, testament→evidence, navigate→work through, transformative→profound, robust→sturdy, realm→domain, intuitive→familiar). All banned-word hits across the corpus cleared.

15 contrastive-negation rewrites applied for the safest pattern variants only ("It is not X. It is Y" / "It's not X. It's Y" / "This is not X. This is Y" — patterns where the subject pronoun is repeated explicitly on both sides, eliminating antecedent ambiguity). The transformation drops the negated frame entirely and keeps only the positive claim, per the principle that the reader does not need to be told what something is not before learning what it is.

281 deeper subject-based contrastive negations ("[noun phrase] is not X. It is Y") were attempted as a mechanical pass and rolled back when sample-checking revealed antecedent-merging bugs: in some cases the second-sentence "It" referred to a different antecedent than the negated subject, and mechanical merging produced semantic nonsense. These 281 instances are flagged for editorial deep-edit work in the topic-by-topic passes that follow, where the antecedent of each "It" can be resolved by a human reading the context.

2 manual fixes applied for cases where the multi-word subject was unambiguous: big-bang-creation ("is not merely scientifically controversial. It is philosophically baffling" → "is philosophically baffling") and reading-the-quran-for-the-first-time ("It is not a philosophical treatise. It is something else" → "It is something else").

**Deep edit on Islamic Practice & Ritual (6 articles)** as the tone reference for subsequent topic passes:

- *Why Do Muslims Pray in Arabic if God Understands All Languages?* (why-arabic-prayer): 732 → 1,211 words. Added Sūrat Qāf 50:16 on God's nearness in private supplication, Sūrat Yūsuf 12:2 on the Arabic of revelation, and a section addressing the "minority of native speakers" objection with the historical pattern of progressive understanding across non-Arab Muslim communities. Bilāl ibn Rabāḥ included as the canonical example of accented Arabic recitation defended by the Prophet.

- *Why Does Islam Have So Many Rules About Trivial Things?* (islam-too-many-rules): 722 → 1,109 words. Added the five-category framework (farḍ / mustaḥabb / mubāḥ / makrūh / ḥarām), the maqāṣid al-sharīʿah doctrine with al-Ghazālī's five protected objectives, Sūrat al-Baqarah 2:185 on God wanting ease, and Ṣaḥīḥ al-Bukhārī 39 on religion being ease.

- *Is Circling the Kaaba and Kissing the Black Stone Just Idol Worship?* (kaaba-idol-worship): 741 → 1,183 words. Added Sūrat al-Baqarah 2:115 (wherever you turn, there is the face of God) and Sūrat al-Ḥajj 22:26 (Ibrāhīm's commission to purify the house from any associate). Added Ṣaḥīḥ al-Bukhārī 4287 on the Prophet's destruction of 360 idols at the conquest of Mecca with the verse "the truth has come and falsehood has vanished." Closed with a section on shirk being the foundational prohibition that frames all of Islamic theology, making the idol-worship accusation contradict the religion's own internal logic. The article now has both Quran and hadith citations (was missing Quran before).

- *Does Islam Say Mental Illness Is Caused by Jinn?* (islam-jinn-mental-illness): 631 → 1,089 words. Added a section on the medieval Islamic psychiatric hospitals (the bīmāristāns of Baghdad, Cairo, Damascus), al-Rāzī's clinical writings on melancholia and epilepsy, and the legal category of junūn with the supporting hadith from Sunan Abū Dāwūd 4403 (the pen is lifted from three: the sleeper, the child, the insane).

- *What Is Ramadan Actually For?* (ramadan-fasting-purpose): 608 → 1,151 words. Converted the prose-quoted "false speech" hadith into a proper hadith-citation block (Ṣaḥīḥ al-Bukhārī 1903). Added the four spiritual functions of fasting (taqwā, ṣabr, shukr, muwāsāh) with their specific mechanisms, a section on Laylat al-Qadr with Sūrat al-Qadr 97:1–3, the formal exemption structure with Sūrat al-Baqarah 2:184, and the communal dimension. The article now has both Quran and hadith citations (was missing hadith before).

- *Does Islam Really Believe in the Evil Eye and Magic?* (evil-eye-islamic-view): 707 → 1,144 words. Added the protective practices section (al-Muʿawwidhatān, the verbal redirection of mā shāʾa Allāh), the Prophet's documented case with Sahl ibn Ḥunayf, the contemporary parallel with placebo / psychosomatic / social-attention research, and Ibn Qayyim al-Jawziyyah's framing in Zād al-Maʿād pairing affirmation of the phenomenon with the insistence that medical inquiry must continue alongside spiritual remedies.

All 6 articles now display 6+ minutes reading time, contain both Quran and hadith citation blocks (where doctrinally appropriate), pass the banned-word audit (40+ word list), pass the contrastive-negation audit, and have em-dash counts reduced from 11–19 per article to 3–4 per article.

## [2.5.1] — April 2026

### Editorial: Logic Over Faith critique coverage (3 new articles + 3 expansions)

A review of a "Logic Over Faith" YouTube video and accompanying comment identified 16 critique points plus a "cult" charge. Coverage check against the existing 120 articles found 11 directly covered, 3 partially, and 3 actual gaps. This update closes the gaps and strengthens the partial-coverage articles.

**Three new articles** (batch-006.json, +3 articles, total 123):

- **"How Many Angels Are There? Material Logic and the Unseen"** (821 words, Islamic Practice & Ritual). Responds to the "16 billion recording angels is logistically absurd" critique. Frames the response around the category error of imposing material-spatial constraints on metaphysical entities, brings in ghayb as the relevant epistemic category, and notes that counterintuitive ontology is the rule rather than the exception when the questions get serious (quantum entanglement, dark matter, mathematical objects).

- **"Is the Hijab About Male Control, or Divine Command?"** (896 words, Rights & Freedom). Responds to the genetic-fallacy critique that hijab is patriarchal control wearing religious clothing. Names the genetic fallacy explicitly, walks through Sūrat al-Nūr 24:30–31 (men addressed first), introduces ḥayāʾ and ʿiffah as cross-gender ethics, and separates the rule itself from its abuse by patriarchal cultures.

- **"Does God Need Our Prayers?"** (932 words, Islamic Practice & Ritual). Responds to the "five daily prayers as servile worship of an insecure deity" critique. Frames the response around the category error at the centre of the analogy (citing Sūrat Fāṭir 35:15), explains what ṣalāh actually does for the worshipper rather than for God, walks through the five-time rhythm and what each prayer interrupts, and notes how secular wellness literature has been independently rediscovering the case for structured spiritual practice.

**Three article expansions:**

- **"The Quran as Literary Argument"** — added "The 'iʿjāz disappears in translation' objection" section (~400 words) addressing the claim that literary inimitability should survive translation. Explains why language-bound properties cannot survive translation by definition (Homer in English paperback, Shakespeare in Mandarin), and notes that the original challenge concerned Arabic prose addressed to Arabs and judged by their own poetic standards. References al-Walīd ibn al-Mughīrah's documented response.

- **"What Does Islam Say Happens After Death?"** — added two sections: "The 'desert man's fantasy' objection" (~280 words) addressing the cultural-resonance critique with the genetic fallacy and the qudsī hadith on paradise transcending imagination, and "The wine question" (~250 words) explaining the ʿilla framework that resolves the apparent contradiction between earthly prohibition and afterlife reward (Sūrat al-Wāqiʿah 56:19).

- **"Women, God, and Islamic Law"** — replaced the brief testimony paragraph with a four-paragraph expanded section (~360 words). Adds the Sūrat al-Baqarah 2:282 citation block, explains the verse's specific commercial-debt context (āyat al-mudāyanah), names al-Ṭabarī and al-Qurṭubī's commentary on the seventh-century social-context reading, and details how classical jurisprudence (including the Hanafi school) accepted women's testimony in many domains without the 2:282 ratio.

All new content was drafted under the anti-AI writing style: no banned vocabulary, no contrastive negation constructions ("not X, but Y" / "this is not… it is…"), em-dashes used sparingly, sentence-case headers, Arabic terms with both translation and transliteration. The new articles were audited against a banned-word list of 40+ AI-overrepresented terms before commit.

The pre-existing prose of the three expanded articles was left untouched. One pre-existing usage of "realm" was caught by the audit in the after-death article and was deliberately left in place — scope discipline calls for new content compliance, not retrospective rewriting of previously-shipped prose.

`manifest.json` updated: total_articles 120 → 123, batch-006 entry added with checksum.

### Day 5 SEO/AEO implementation — Polish + Rank Math/Abstract Box coordination

**Item 18 — Conditional `twitter:card` type**
The OG/Twitter meta block now declares `summary_large_image` only when the page actually has a per-post hero image. Pages without a featured image (using only the sitewide `screenshot.png` fallback) declare the small-thumbnail `summary` card instead, so Twitter doesn't render an awkwardly-stretched logo as a hero banner.

**Item 19 — Title separator + Rank Math yield**
- Internal title separator changed from `—` (em-dash) to `|` (pipe), matching the standard Rank Math global setting.
- `ce_custom_title` now yields entirely to Rank Math when active. Both filters running on `document_title_parts` at default priority 10 produced non-deterministic ordering; explicit detection via `class_exists( 'RankMath' )` removes the conflict. Theme's filter remains active as a fallback for sites without Rank Math.

**Item 21 — Demote duplicate H2 in Q&A archive empty state**
The fallback `<h2>No published answers yet</h2>` block in `archive-ce_question.php` shared its heading level with the per-question `<h2 class="qa-title">` items in the populated state. Demoted to `<h3>` for clean document outline. Updated the `.qa-empty h2` CSS selector to `.qa-empty h3` to preserve the styling.

**Item 22 — `aria-describedby` on glossary tooltip auto-links**
Glossary auto-linker (`inc/ce-glossary.php`) now emits a visually-hidden `<span>` containing the term's definition alongside each link, with `aria-describedby` on the anchor pointing to it. Sighted users continue to see the JS-driven tooltip via `data-definition` (unchanged); screen-reader and keyboard users now get the same context via standard ARIA. A counter in the closure produces unique IDs (`ce-gloss-{slug}-{n}`) for multiple occurrences of the same term in a single content pass. Same treatment applied to the `[glossary]` shortcode renderer with a `static` counter for cross-invocation uniqueness. New `.ce-glossary-sr-only` utility class in `glossary-tooltip.css` uses the standard inclusive-design clip-rect technique to hide the spans visually while keeping them discoverable by assistive tech.

**Item 24 — Abstract Box plugin schema coordination**
- `abstract_box_schema_type` filter forces the plugin to emit `Article` schema type, matching the theme's Article schema for type consistency.
- `abstract_box_schema_payload` filter injects the theme's configured Person author (from Day 1's `ce_get_author_person_schema()`), replacing the plugin's default which uses only the WP `post_author` display_name without `sameAs`/`jobTitle`/`url`.
- Both filters degrade gracefully when Abstract Box isn't installed.

### Rank Math coordination cleanup (replaces Item 16 — sitemap lastmod)

Three coordination changes ensure the theme cooperates cleanly with Rank Math instead of competing with it:

**`ce_robots_txt` yields to Rank Math.** When Rank Math is active, the theme returns the unmodified `$output` instead of overwriting with its own `wp-sitemap.xml` declaration. Rank Math has its own admin-managed robots.txt at the correct `sitemap_index.xml` URL. Both filters were running at priority 10, producing non-deterministic ordering — explicit detection removes the conflict. New helper `ce_is_rankmath_active()` centralizes the check (used here and at the sitemap filter and title filter sites).

**Core sitemap filters guarded.** The three filters that enable WordPress core sitemaps (`wp_sitemaps_enabled`, `wp_sitemaps_post_types`, `wp_sitemaps_taxonomies`) are now wrapped in an `if ( ! ce_is_rankmath_active() )` guard. Rank Math's `class-redirect-core-sitemaps.php` intercepts core sitemaps anyway, but registering inert filters wastes cycles on every request. With Rank Math active, filters skip registration entirely. Without Rank Math, behavior is unchanged.

**Theme Article schema skipped when Rank Math is active.** Rank Math's `class-jsonld.php` emits its own `Article`/`BlogPosting`/`NewsArticle` schema on every singular post. The theme's Article schema overlapped directly. Now skipped when Rank Math is active — Rank Math owns per-article schema, the theme owns sitewide schema (WebSite, Organization, BreadcrumbList, FAQPage, TOC), and there's no overlap. Without Rank Math, the theme's Article schema fills the gap as before.

### Day 4 SEO/AEO implementation — Schema completeness, image sizing, TOC

**Item 8 — Semantic-entity fields on Article schema**
The Article schema in `inc/ce-seo.php` now carries a richer set of properties that help search engines understand and rank the content:

- `keywords` — assembled from the article's `ce_topic` taxonomy and any post tags, with empty values filtered and duplicates removed
- `isAccessibleForFree: true` — confirms the content is open access, a ranking signal for inclusive search
- `accessMode: ["textual", "visual"]` — declares the content can be consumed via reading and viewing
- `accessibilityFeature: ["readingOrder", "structuralNavigation", "tableOfContents", "alternativeText"]` — accessibility metadata used by reader-mode rendering and assistive contexts
- `timeRequired` — ISO 8601 duration (`PT{n}M`) computed from the same 200-wpm reading speed used by the visible reading-time UI

**Item 9 — `WebPage.primaryImageOfPage` and tightened `inLanguage` linkage**
The `mainEntityOfPage` block on Article schema is now a fuller WebPage object: `@type`, `@id`, explicit `url`, and `inLanguage`. When the article has a featured image, `primaryImageOfPage` is added as a proper ImageObject, giving search engines a clear chain Article → WebPage → primaryImageOfPage that strengthens page-image association for Discover and image search.

**Item 10 — Tighter meta descriptions**
- `wp_trim_words` reduced from 30 to 25 (closer match to Google's 155-160 char snippet limit).
- New helper `ce_trim_meta_description( $text, 160 )` enforces a hard 160-character ceiling at the last word boundary, with mb-string handling so Arabic and UTF-8 content count correctly.
- The ellipsis (`…`) is reserved 1 char so the visible output never exceeds 160 even with the ellipsis appended.
- Trailing punctuation (`,;:.-`) is stripped before the ellipsis so cuts don't produce awkward "lorem ipsum, …" artefacts.
- Applied to all six page-type branches in `ce_meta_description()` (single article, post-type archive, taxonomy, front page, search, page). Tested against five edge cases including short pass-through, long truncation, exact-at-limit, word boundaries, and Arabic UTF-8.

**Item 20 — Table-of-contents schema upgraded**
The existing `ItemList` schema for the article TOC is preserved (Google's documented format), and a parallel `SiteNavigationElement[]` schema is now emitted alongside it. Bing's deep-link parser prefers `SiteNavigationElement` for in-article navigation, so the two schemas together broaden answer-engine coverage without conflict. Both schemas only emit when 2+ H2 headings exist. The visible `<nav>` element gained `role="doc-toc"` for proper landmark semantics. JSON output uses the same `JSON_HEX_TAG` hardening as Day 2's QAPage block.

**Item 23 — Dedicated 1200×630 OG share image size registered**
- `add_image_size( 'og-share', 1200, 630, true )` registered in `ce_theme_setup()`. The hard crop preserves the 1200×630 aspect ratio across image shapes by cropping rather than letterboxing.
- New helper `ce_get_share_image_url()` prefers `og-share` and falls back to `large` for media uploaded before the new size existed.
- All four call sites in `inc/ce-seo.php` (OG image meta, Twitter image meta, Article schema image, primaryImageOfPage) now use the helper.
- For sites with existing media, running a regenerate-thumbnails plugin will back-fill the new size; until then the fallback to `large` keeps OG cards working without disruption.

### Day 3 SEO/AEO implementation — Content depth across landing surfaces

**Item 6 — Article archive intro (222 words)**
Added an `<aside class="archive-intro-context">` between the hero and the topic filter on `/articles/`. Four paragraphs covering what the archive contains, the methodology of stating the strongest objection first, the eleven-topic structure, and the audience (the honest inquirer rather than the already-convinced). Constrained to a 720px reading column with serif body type for editorial tone consistency.

**Item 7 — Front-page mission section (646 words)**
Added a `<section class="home-mission">` between the quick-access cards and the article grid on the homepage. Structured as H2 + lead paragraph + four H3 sub-sections (premise, method, what the work covers, who this is for) + tone note + CTA link to /articles/. Uses Playfair Display for headings, Cormorant Garamond for prose, accent-colored CTA. The lead paragraph is italicised at 1.25rem to function as both editorial framing and visual hook.

**Item 13 — Heading hierarchy fix in `archive-ce_article.php`**
Promoted the topic-section title from `<span class="ts-name">` to `<h2 class="ts-name">`. The archive previously jumped from H1 directly to H3 inside subcategory groups, breaking the document outline. With the H2 in place, the hierarchy now reads correctly: H1 (page title) → H2 (topic section) → H3 (subcategory). Added `margin: 0; line-height: 1.2;` to `.ts-name` CSS to neutralise the browser's default H2 spacing and font-size, keeping the visual treatment unchanged.

**Item 14 — Q&A archive intro (188 words)**
Added an `<aside class="qa-archive-intro">` between the Q&A hero and the question list. Four paragraphs covering how the surface works (curated rather than every-question-published), the editorial process (review before publishing, overlap with existing articles redirected), response time, and a pointer to the topic browser. CSS uses the same 720px reading column treatment as the article archive intro for consistency.

**Item 15 — Editorial intros for Glossary, FAQ, and Ask a Question pages (~180-200 words each)**
Added `<aside class="page-intro-context">` blocks to all three landing pages. The glossary intro (179 words) explains why the eighty-four terms include Arabic originals alongside transliterations, and how the long-form definitions situate terms within the conversations where they do real work. The FAQ intro (182 words) covers the three editorial principles (honesty, autonomy, accountability) and points readers to the right surfaces for substantive vs. structural questions. The Ask a Question intro (203 words) covers the response timeline, what gets answered privately vs. publicly vs. turned into articles, and how to submit useful questions.

A shared `.page-intro-context` / `.page-intro-inner` CSS rule serves all three pages — same 720px reading column treatment as the Q&A archive intro for visual consistency across the site.

### Day 2 SEO/AEO implementation — Q&A schema + FAQ + Entities

**Item 5 — `QAPage` schema in `single-ce_question.php`**
Each published Q&A page now emits a JSON-LD QAPage block with `mainEntity` (Question), `acceptedAnswer` (Answer), and proper author attribution. Schema is gated by the `ce_qstatus = 'published'` taxonomy state, matching the visibility gate in `inc/ce-question-cpt.php` — pending or in-review questions don't leak schema. The Answer's author uses the configured Person from the Identity tab (Day 1) when available, falling back to the Organization. Output uses `JSON_HEX_TAG` and friends, so the JSON is safe inside the `<script>` tag even when questions or answers contain quotes or angle brackets.

Also fixed during this work: an unescaped `human_time_diff` output in the Q&A footer, and inconsistency between the schema-captured answer (which ran the `the_content` filter) and the visible answer (which used raw `get_the_content()` for the empty-check) — both paths now use the same filter chain.

**Item 11 — FAQ items meta box + FAQPage schema**
New module `inc/ce-aeo.php` provides a "FAQ Items (FAQPage schema)" meta box on `ce_article` and `post` edit screens with repeatable Q&A rows. Vanilla JS handles add/remove (no jQuery dependency), with the constraint that the last remaining row clears rather than disappears so editors always have an editable slot. Saves to `_ce_faq_items` post meta as an array, dropping any incomplete rows server-side. The FAQPage JSON-LD is appended to the article's @graph in `inc/ce-seo.php` only when at least one valid Q/A pair exists.

**Item 12 — AEO Entities meta box**
Same module provides an "AEO Entities (about & mentions)" meta box with two comma-separated text fields. Stores `_ce_about` and `_ce_mentions` post meta. The Article schema in `inc/ce-seo.php` now reads these via `ce_aeo_parse_entities()` (which trims whitespace, deduplicates, and skips empty entries) and emits them as `about` and `mentions` arrays of schema.org `Thing` objects, helping search engines disambiguate the article's subject matter and connect it to entities in their knowledge graph.

Both meta boxes follow the standard nonce + capability + DOING_AUTOSAVE checks. Both delete their post meta when emptied so the database stays clean rather than accumulating empty arrays.

### Day 1 SEO/AEO implementation — Author identity end-to-end

**Item 1 — New "Author & Identity" admin tab in `inc/ce-theme-options.php`**
A new tab (placed between General and Colors) collects all author/organization metadata in one place. Fields cover the author Person (name, jobTitle, bio, profile URL), author social profiles for the schema `sameAs` array (Twitter/X, YouTube, GitHub, LinkedIn, plus a free-form "other URLs" textarea for Wikipedia entries, Scholar profiles, ORCID, etc.), site-level social and contact (Twitter handle, YouTube channel, Facebook page, contact email), and Organization logo (URL + explicit width/height for Google rich-result eligibility).

**Item 2 — `Person` schema replaces Organization-only authorship in `inc/ce-seo.php`**
Article schema's `author` field is now a Person object when the Identity tab has been populated, with `name`, `url`, `jobTitle`, `description` (300-char-capped), `sameAs` array, and `worksFor` linking to the Organization. Falls back to Organization-as-author when no author name has been configured, preserving compatibility on sites that haven't yet populated the new tab. Implemented via three new helpers: `ce_get_author_person_schema()`, `ce_get_author_same_as_urls()`, `ce_get_organization_schema()`.

**Item 3 — Visible byline in `single-ce_article.php` and `single.php`**
A byline renders directly under the article H1 when an author is configured: "By [Author Name] — [Job Title]". The byline link target is the explicit profile URL if set, otherwise the WordPress author archive (giving Google an internal author-page to crawl). The link uses `rel="author"` and a custom CSS class with hover state. CSS added to `assets/css/templates.css` between the `.article-title` and `.article-meta` rules.

**Item 4 — Extended Organization schema**
The standalone Organization @graph entry (sitewide) and the publisher field of Article schema now both use `ce_get_organization_schema()` as the single source. Adds `logo` as a proper ImageObject with explicit width/height (replacing the previous bare URL), `sameAs` array (assembling site Twitter/YouTube/Facebook), `contactPoint` (with editorial email), and `founder` linking to the Person when configured. Logo dimensions default to 512×512 if the Identity tab hasn't been populated, so existing rich-result eligibility is preserved.

**Item 17 — `twitter:site` and conditional `twitter:creator` meta tags**
The OG/Twitter meta block now emits `<meta name="twitter:site" content="@handle">` when the site Twitter handle is configured. On `ce_article` and `post` singular views, `twitter:creator` is also emitted when the author Twitter URL is set — the URL is regex-parsed to extract the handle (supports both twitter.com and x.com), capped at 15 characters per Twitter's identifier rules.

### Article archive excerpts capped at 120 characters
- Added `ce_excerpt_chars( $post, $limit = 120 )` helper in `functions.php` that truncates at the last word boundary at or below the character limit, with UTF-8-correct length counting (Arabic, em-dashes, curly quotes don't cost extra characters), HTML stripping for the content fallback path, whitespace collapsing, and trailing-punctuation cleanup before the ellipsis.
- Applied to all three render branches in `archive-ce_article.php` (subcategorised topics, "Further Reading" subgroup, ungrouped/legacy branch).
- Replaces the previous mixed approach of nested `wp_trim_words()` calls that could produce excerpts of widely varying length depending on whether the source was the post's stored excerpt or a content fallback.
- The 120-character cap improves visual rhythm on the archive — every card now occupies a predictable amount of vertical space.

**Additional output escaping cleanup**
- `archive-ce_article.php`: 12 unescaped echoes fixed — `$total_pub`, `$tf_link`, `$active`, `$t->count`, `$count`, `$article_counter`, `$mins`, `get_the_title($post)`, `wp_trim_words(...)`, and `$extra_class` all now appropriately escaped via `(int)` cast, `esc_url`, `esc_attr`, or `esc_html` per context.
- `single.php`: `$reading_mins` cast to `(int)`, `number_format($word_count)` wrapped in `esc_html`.
- `single-ce_article.php`: `$reading_mins` and `$rc_mins` cast to `(int)`.

**Additional security**
- `ce_ajax_search`: `$_GET['term']` now uses `wp_unslash()` before `sanitize_text_field()` per WP standards.

**Output escaping cleanup**
- `inc/ce-seo.php`: Open Graph `og:type`, `og:site_name`, `og:locale`, `article:published_time`, and `article:modified_time` now `esc_attr`'d on output.
- `inc/ce-theme-options.php`: `min`/`max`/`step` HTML attributes and admin tab URL now `esc_attr`/`esc_url` on output.
- `inc/ce-engagement.php`: vote widget `$post_id` cast to `(int)`, `$cls_vote` and `$cls` `esc_attr`'d, vote/resonance counts cast to `(int)`. Most-Resonant widget title escaped via `esc_html`.
- `search.php`, `page-journeys.php`: integer counters cast to `(int)`, text outputs wrapped in `esc_html`.

**Additional code quality**
- **Removed dead code**: unused `$ver = '1.0.0'` and empty `// ── ENQUEUE MASONRY + INFINITE SCROLL` section marker in `functions.php` deleted. `ce_enqueue_assets` reorganised to compute `$stylesheet_uri` and `$version` once and reuse, eliminating four duplicate `wp_get_theme()->get('Version')` calls.

**Code quality**
- **Extracted `ce_get_journey_paths()` helper**: the 14-path array was previously inlined in `ce_create_journey_pages()` and the validation logic was duplicated across `ce_save_journey_progress` and `ce_save_quiz_result`. Single source of truth now.
- **Extracted `ce_get_journey_screens(string $path)` helper**: the per-path screen sequence was previously inlined as if/else inside the AJAX handler.
- **Anonymous closure replaced with named function**: `add_action('after_setup_theme', function() { CE_Feed_Redirector::get_instance(); })` → `ce_init_feed_redirector()`. Allows `remove_action()` from plugins, surfaces in stack traces.
- **Most-Resonant widget output**: added `phpcs:ignore` markers on `$args['before_widget']` etc. — these are trusted output from `register_sidebar()` but the markers document the intent for static analysis.

### Glossary (full unification)
- **Single source of truth**: `inc/ce-glossary.php`'s `ce_get_glossary_terms()` is now the only place glossary data lives. The hardcoded duplicate `$terms` array in `page-glossary.php` was deleted; the page now calls the function. Each entry now carries `term`, `arabic`, `def` (short, for tooltips), and `def_long` (rich prose, for the public page) — both fields on every term.
- **31 terms had no public glossary entry**: Tooltips on article auto-links pointed readers to `/glossary/#term-{slug}`, but the slug had no destination — clicking landed on a fragment that did not exist. Drafted long-form prose entries for all 31: `Adha`, `Ahad`, `Ahl al-Fatrah`, `Alaqa`, `Ayn`, `Bismillah`, `Da'if`, `Du'a`, `Hasan`, `Ijtihad`, `Ikhtilaf`, `Isnad`, `Kashf`, `Khawf`, `Mahabbah`, `Maslaha`, `Matn`, `Mutawatir`, `Qiblah`, `Raja'`, `Rijal`, `Ruqya`, `Sahih`, `Shafa'ah`, `Sihr`, `Tabi'un`, `Tafakkur`, `Tahara`, `Taqlid`, `Wudu`, `Zakat al-Fitr`. Each entry follows the established editorial voice — informed, accessible, anchored in the Islamic intellectual tradition.
- **Alphabetical sort**: Previous data had 5 terms out of order (Muhasabah, Sunan, Tawbah) and an entire 26-term block appended without resorting. All 84 terms now sort alphabetically with apostrophes ignored for sort key (so `Da'if` sorts with D, `Raja'` with R, `Shafa'ah` with S).
- **12 legacy entries rewrote** to comply with the contrastive-negation ban applied across the site: `Allah`, `Haram`, `Ibadah`, `Iman`, `Jannah`, `Kufr`, `Ridwan`, `Shariah`, `Shirk`, `Taqwa`, `Tawhid`, `Ummah`. Each rewrite preserves the original meaning while replacing constructions like "not X but Y" with direct, declarative prose.
- **JSON-LD schema enrichment**: The `DefinedTerm` schema markup on `/glossary/` now uses the long-form description rather than the short tooltip, giving search engines richer indexable content per entry.

### Added (Q&A discoverability)
- **`/qa/` was orphaned** with no inbound links from any nav surface. The only link to it lived inside individual question pages as a "Back to Q&A" button — meaning a visitor had to already know the URL to find the archive. Added Q&A link to:
  - **Header main nav** (between Articles and Ask a Question, in the fallback menu)
  - **Footer Navigate column** (between Articles and Ask a Question)
  - **Front-page quick-access cards** (new Reader Q&A card between Top Questions and What's New)
- **Quick-access grid expanded** from 3 to 4 columns at desktop widths, with a 2-column intermediate breakpoint at ≤1024px and the existing 1-column breakpoint at ≤680px. Maintains visual rhythm at all viewport sizes.

### Fixed (Q&A System)
- **Ask a Question form redirected to nowhere after submission**: The handler called `wp_redirect( add_query_arg('sent', '1', get_permalink()) )` from inside `admin-post.php`, where there is no global `$post` — `get_permalink()` returned falsy and the success page was never reached. Users saw a redirect to the home URL or a blank screen instead of the "Question received!" confirmation. Fixed: handler now resolves the return URL via (1) a hidden `ask_return_to` field in the form, (2) `wp_get_referer()`, (3) hard-coded fallback to `/ask-a-question/` — all validated as same-host before redirect via `wp_safe_redirect()`.
- **Submitted questions immediately publicly accessible at `/qa/{slug}/`**: Handler created posts with `post_status = 'publish'` on the assumption that the `ce_qstatus` taxonomy alone gated visibility. It did not — anyone guessing or scraping the slug could read a question (containing potentially personal context) before review. Fixed: `post_status = 'pending'` on submission. Added a `template_redirect` visibility gate that 404s any single `ce_question` page where `ce_qstatus` is not `published`, except for users with `edit_posts` capability. Added a `pre_get_posts` filter that constrains the public archive query at `/qa/` to `ce_qstatus=published` only.
- **`post_status` and `ce_qstatus` workflow now synced automatically**: Previously, an admin had to flip both the WordPress post status AND the editorial workflow taxonomy term to make a question publicly visible. The two-step requirement was undocumented and confusing. Added `set_object_terms` hook: when `ce_qstatus` becomes `published`, `post_status` flips to `publish`; when reverted to any other state (new, review, answered, archived), `post_status` reverts to `pending`. Includes recursion guard.
- **Duplicate admin email on submission**: Both `ce_handle_ask_question()` and `ce_notify_new_question()` (hooked to `wp_insert_post`) sent the same notification, so admin received two emails per submission. Removed the manual `wp_mail()` call from the handler — the `wp_insert_post` hook is the canonical notification path.
- **`wp_die()` validation errors gave hostile UX**: Missing field, bad email, etc. dumped a black-text-on-white WP error page with no way back to the form. Replaced with redirect-back-to-form using `?ask_error={code}` parameters; the form template renders a friendly inline error message and re-populates the user's input so they don't lose what they typed.

### Added (Q&A spam protection & rate limit)
- **Honeypot field** in the Ask a Question form. An invisible `ask_website` text input (CSS-hidden + `tabindex=-1` + `aria-hidden`) is filled by automated bots but never by humans. Submissions with this field non-empty are silently fake-acknowledged and discarded.
- **Per-IP rate limit**: 3 submissions per IP per hour, using the existing `ce_rate_limited()` helper from `inc/ce-engagement.php`. Bots that bypass the honeypot still hit a wall after 3 tries.
- **Minimum question length**: 10 characters, blocking trivial single-word junk submissions.

### Security
- **CSRF on `?ce_force_sync` URL parameter**: The force-sync GET trigger relied on capability check alone (`current_user_can('manage_options')`). An attacker could craft a link such as `https://site.example/wp-admin/?ce_force_sync=1` and an admin clicking it would trigger a 5-minute DB lock and full content sync without consent — DoS vector even though article data ends up unchanged. Fixed: nonce verification added. The URL must now include a valid `_wpnonce` parameter created via `wp_create_nonce( 'ce_force_sync' )`. Capability check retained.
- **`ce_ajax_random` missing nonce verification**: Inconsistent with the rest of the AJAX surface (search, vote, resonance, analytics, progress all enforce `check_ajax_referer`). Low-impact endpoint (returns a random article URL), but a public AJAX endpoint with no nonce can be abused for cache-busting traffic. Fixed: `check_ajax_referer( 'ce_nonce', 'nonce' )` added.

### Fidelity
- **Topic migration map corrected**: `ce_get_topic_migration_map()` mapped the deprecated `'Islamic Beliefs & Practice'` to `'Does God Exist?'`, contradicting the SSOT which establishes `'Islamic Practice & Ritual'` as the correct replacement. The old mapping would silently route any article still using the deprecated name into the broad theological category instead of the ritual-specific one. Corrected to match SSOT § 5. Legacy v2.3.1 retirement names also restored to the map (`Examining the Quran`, `Examining the Sources`, `History & Context`, `The Bigger Picture`) as a safety net for older deployments.
- **N+1 query in Q&A archive sidebar**: The sidebar topic list ran 11 separate `WP_Query` instantiations to count published Q&As per topic. Replaced with a single aggregated SQL query joining `posts`, `postmeta`, `term_relationships`, `term_taxonomy`, and `terms` — counts all topics in one round trip. `get_term_link()` return value now also checked for `WP_Error` before being passed to `esc_url()`.

### Added
- **CE_Feed_Redirector** (`inc/class-ce-feed-redirector.php`): Integrated feed suppression system. Removes feed autodiscovery `<link>` tags from `<head>`, 301-redirects all feed URL formats to their canonical page equivalents, sends `X-Robots-Tag: noindex, nofollow` on any feed response, appends feed `Disallow` rules to `robots.txt` (priority 20, after the CE sitemap rule at priority 10), and excludes feeds from the WordPress core sitemap when no SEO plugin is active. Safe in WP-CLI, cron, admin, and REST API contexts. Prevents redirect loops. Handles query-string feed params, search feeds, author feeds, comment feeds, and CPT feeds.

### Fixed
- **`term_exists` cache miss silently dropping topic assignments**: Both the update and create branches in `ce_sync_article_content()` used `if ( ! is_wp_error( $result ) )` with no fallback after `wp_insert_term()`. On hosts where WordPress's object cache returns a cache miss for a term that already exists mid-request, `wp_insert_term()` returns `WP_Error('term_exists')` — the code treated this as a genuine failure, left `$topic_id = 0`, and silently skipped `wp_set_object_terms()`. Fixed in both branches: when `get_error_code() === 'term_exists'`, `get_error_data('term_exists')` recovers the existing `term_id` and the assignment proceeds. A `WP_DEBUG` log line distinguishes update vs create context.
- **Manual sync button blocked by persistent object cache**: The `bool $force_run = false` parameter and its bypass logic were not consistently carried forward across intermediate releases. The `$_GET['ce_force_sync']` approach present in earlier builds is only reachable via direct URL navigation — the admin button in `ce_manual_content_sync()` called `ce_sync_article_content()` with no arguments, leaving it vulnerable to the same cached-option short-circuit on Redis/Memcached hosts. Fixed: `bool $force_run = false` parameter restored; `ce_manual_content_sync()` passes `true`. The `$_GET` URL path is preserved and merged via `$force_sync = $force_run || $url_force`.
- **`articles-data-5.php` backup reverted to stale topics**: Six articles (`why-arabic-prayer`, `islam-too-many-rules`, `kaaba-idol-worship`, `islam-jinn-mental-illness`, `ramadan-fasting-purpose`, `evil-eye-islamic-view`) had reverted to `'topic' => 'Does God Exist?'` in the backup PHP source. A `build-articles.php` run would have silently regenerated `batch-005.json` with wrong topics, corrupting the next sync. All six corrected to `'Islamic Practice & Ritual'`.
- **Q&A page CSS dropped in 2.4.8**: 339 lines of Q&A styling restored to `assets/css/templates.css` — `archive-ce_question.php` and `single-ce_question.php` were completely unstyled. Full layout grid, hero section, question/answer cards, label pills, empty state, sticky sidebar, topic list, pagination, and single-view styles restored.
- **Q&A topic list order**: Sidebar topics now render in canonical 11-topic order (Does God Exist? first, Revelation & Meaning last) instead of WordPress alphabetical default.
- **Q&A topic links**: Topics with zero published Q&As dimmed to 45% opacity.
- **`manifest.json` version stale**: Version field corrected to match `style.css` on each release.

---

## [2.5.0] — April 2026

### Code Review (full theme audit)

**Security**
- **`main.js` search-results XSS**: `item.title` and `item.excerpt` were inserted into innerHTML without HTML-escaping. The server uses `html_entity_decode()` on these fields before JSON-encoding, so any HTML in a post title or excerpt would render as live markup. Fixed: both fields now pass through `escHtml()` before insertion.
- **`ce-engagement.php` share bar XSS hardening**: pre-escaped `$url` and `$title` once with `esc_url`/`esc_attr`, then reused in JS string literals inside `onclick` handlers. HTML-attribute escaping does not protect against JS-context quote-breakout. Refactored to per-context escaping: `esc_url`/`esc_attr` for HTML attributes, separate `esc_js`/`esc_url` variables for JS string contexts. Eight platforms updated.
- **`ce-question-cpt.php` CSRF in status saver**: the question-status save handler had only a capability check — no nonce. A logged-in editor could be CSRF-tricked into changing a question's editorial state. Fixed: added `wp_nonce_field` to the meta box and `wp_verify_nonce` in the save handler.
- **Vote and resonance now reject non-published, non-article posts**: AJAX handlers only checked that a post existed for the given ID, allowing engagement rows to be seeded for drafts, attachments, pages, and arbitrary CPTs. Added `post_status === 'publish'` and `post_type` allowlist guards.
- **Q&A archive content escaping**: `echo $answer` was outputting raw `get_the_content()` with no `the_content` filter applied. Now runs `apply_filters('the_content', ...)` for proper paragraph wrapping/shortcodes/oEmbed, then `wp_kses_post()` for whitelist-based HTML sanitisation.
- **Quiz handler now validates path against allowlist**: `$primary` was previously stored in user meta with only `sanitize_key()` applied — any string slug-shape would be accepted. Added validation against `ce_get_journey_paths()`. Same allowlist applied to `ce_save_journey_progress`.
- **REQUEST_URI sanitisation**: both call sites in `functions.php` now use `sanitize_text_field(wp_unslash(...))` with `isset()` guards. `wp_redirect` upgraded to `wp_safe_redirect` in legacy redirects.
- **HTTP_USER_AGENT sanitisation**: analytics device-detection now wraps the header in `sanitize_text_field(wp_unslash(...))`.
- **IP detection refactor**: replaced cascaded if/elseif chain with a header allowlist loop that validates each candidate before use, and added a docblock documenting the proxy-trust assumption.
- **Theme options POST handling**: added `wp_unslash()` before sanitisation, an `isset()` guard, and a new `textarea` field-type branch using `sanitize_textarea_field`.
- **Analytics POST handling**: replaced `stripslashes()` with `wp_unslash()` (WP-canonical primitive).
- **Question CPT meta save**: per-field sanitiser map (`sanitize_email` for email, `sanitize_textarea_field` for admin notes, `sanitize_text_field` for short fields) replacing a single `sanitize_text_field` for all four fields.
- **Nonce verification consistency**: `ce_crosslink_save_meta`, `ce_save_question_meta`, and `ce_save_question_status` now `sanitize_text_field(wp_unslash(...))` the nonce input before passing it to `wp_verify_nonce` — cosmetic for nonces (they're alphanumeric) but consistent with WP standards.

## [2.4.9] — April 2026

### Added
- **CE_Feed_Redirector** (initial integration): Feed suppression class first introduced. Removed feed autodiscovery links, 301 redirects, robots.txt rules, noindex headers. All functionality forward-ported and stabilised in v2.5.0.

### Fixed
- Q&A page CSS restored after 2.4.8 regression. Q&A sidebar topic order corrected.

---

## [2.4.7] — April 2026

### Fixed
- **Subcategories still not appearing after v2.4.6**: The orphan detection in v2.4.6 incorrectly included old slugs as "valid", which prevented duplicate cleanup. When the sync renamed an article from old slug → new slug, if a duplicate with the new slug already existed, both survived. Fixed orphan detection to only consider JSON slugs as valid — old-slug articles are now properly trashed after rename.
- **Added debug logging**: Enhanced error logging for slug rename detection and orphan cleanup to help diagnose sync issues.

---

## [2.4.6] — April 2026

### Fixed
- **Subcategories not appearing**: The `$subcategories` array in `archive-ce_article.php` groups articles by slug, but when articles were renamed in JSON, the sync created duplicates instead of updating in place. Articles in the database had old slugs that didn't match the subcategory slug lists, so all articles fell through to "Further Reading" instead of appearing in their proper subcategories.
- **`ce_get_slug_rename_map()` missing**: Changelog v2.3.7 claimed this was added, but it was never actually implemented. Now added with `array_search()` lookup — if new slug not found, falls back to old slug, updates in place, and renames `post_name`.
- **Orphan detection not implemented**: Duplicate articles from slug renames were never cleaned up. Added orphan detection after sync loop — any `ce_article` post whose slug is not in JSON (or rename map) is automatically trashed.

---

## [2.4.5] — April 2026

### Fixed
- **`ce_cleanup_deprecated_topics()` missing**: Function was called in sync but never defined, causing fatal error when sync attempted to clean up retired topics.
- **Topic name mismatch**: `ce_ensure_topics_exist()` was creating "Islamic Beliefs & Practice" but JSON articles and archive template expect "Islamic Practice & Ritual". Fixed to use correct name.
- **Retired topics recreated after cleanup**: `ce_ensure_topics_exist()` was recreating retired topics immediately after they were deleted. Removed retired topics from required list.
- **Topic migration not working**: Articles assigned to retired topics were not being migrated because `ce_get_topic_migration_map()` existed but was never called during sync. Added migration logic to both update and create branches.

---

## [2.4.4] — April 2026

### Fixed
- **Category restructure not applying**: Version key `ce_content_sync_2_4_3` was already set when 2.4.3 was first deployed, so the sync never re-ran with the updated batch files. The 6 Islamic Practice & Ritual articles remained in Rights & Freedom. Version bump forces a fresh sync on next admin page load.

---

## [2.4.3] — April 2026

### Added
- **Islamic Practice & Ritual** reinstated as an 11th canonical topic category. Six articles moved from Rights & Freedom: *Why Do Muslims Pray in Arabic*, *Why Does Islam Have So Many Rules*, *Is Circling the Kaaba Idol Worship*, *Does Islam Say Mental Illness Is Caused by Jinn*, *What Is Ramadan Actually For*, *Does Islam Believe in the Evil Eye*. Rights & Freedom drops from 18 to 12 articles and becomes a coherent rights-focused category.

### Changed
- **`archive-ce_article.php`**: Subcategories added for four categories — Science & Evidence (What Science Can and Can't Do / Cosmology & Origins / Mind & Experience), Rights & Freedom (Leaving Islam / Gender, Body & Sexuality / The Harder Accusations), Revelation & Meaning (Does God Speak? / Why Islam? / Questions From Within), and The Quran & Its Sources (existing subcats retained). Islamic Practice & Ritual renders as a flat list (6 articles). All 120 articles verified as renderable.
- **Topic descriptions updated**: Rights & Freedom description reflects its consolidated scope. Islamic Practice & Ritual description added.
- **`ce-content-sync.php`**: Islamic Practice & Ritual added to `ce_ensure_topics_exist()`.
- **`doc/ssot.md`**: Topics table updated to 11 canonical categories.

---

## [2.4.2] — April 2026

### Fixed
- **Manifest checksums broken**: All 5 batch JSON files were updated (31 articles revised in v2.4.1) but `manifest.json` checksums were not regenerated. Loader rejected every batch on checksum verification, preventing sync from running. Checksums recomputed to match deployed files.

---

## [2.4.1] — April 2026

### Enhanced
- **Article argumentation depth — 31 articles updated**: A structured logical reinforcement layer has been added to 31 articles where the underlying argument structure most directly benefits from explicit logical scaffolding. Each addition is written in the voice and register of the surrounding article — no jargon, no labels, no announced principles. The additions work by making the logical shape of the argument impossible to step around: laying out the available options, showing what each one requires, and demonstrating which position is coherent. Articles updated span all major argument categories: cosmological and existence arguments (*Why Does Anything Exist*, *The Universe Had a Beginning*, *Can Existence Come Out of Nothing*, *Does a Higher Power Exist*), fine-tuning (*The Universe Is Absurdly Specific*, *Does the Multiverse Explain Away Fine-Tuning*), consciousness and reason (*The One Thing Neuroscience Cannot Explain*, *If Your Brain Is Just Atoms*), morality (*Where Do Moral Facts Live*, *Can You Ground Ethics Without God*, *Is Morality Just Opinion*, *If Nothing Really Matters*), objections to God (*The Problem of Evil*, *Earthquakes and a Good God*, *Divine Hiddenness*, *The Euthyphro Dilemma*, *Free Will and Predestination*, *God as Psychological Projection*), epistemological objections (*Who Bears the Burden of Proof*, *Is God Just a God of the Gaps*, *Doesn't Evolution Make God Unnecessary*, *What Science Cannot Tell You*), revelation and communication (*Does God Communicate With Humanity*, *What Would Authentic Revelation Look Like*, *If God Sends Prophets Why Do Messages Contradict*), and sociological objections (*Isn't Your Religion an Accident of Birth*, *Why Do Human Beings Believe in God*, *Is God Personal or Just a First Cause*, *If God Rewards Faith and Punishes Doubt*).

---

## [2.4.0] — April 2026

### Fixed
- **Manual sync silently blocking automatic sync**: `ce_manual_content_sync()` (Tools & Sync button) was calling `update_option($version_key)` at the end of its run. This marked the current version as already-synced, so the fixed `ce_sync_article_content()` on `admin_init` saw the key and returned immediately — every deploy since 2.3.4 has done nothing on admin load. Root cause of all "articles not showing" reports.
- **Manual sync had stale, incomplete implementation**: Maintained its own parallel sync loop with an outdated topic migration map (v2.3.1 only, missing v2.3.6 entries), no slug-rename handling, no orphan detection, and a hardcoded `topics_cleaned = 4` counter.

### Changed
- `ce_manual_content_sync()` rewritten to delegate entirely to `ce_sync_article_content()`. It now clears the version key and lock transient, calls the canonical sync function, then reports results from the resulting DB state. One sync implementation, used by both code paths.
- `ce_fallback_sync()` removed — it was only reachable from `ce_manual_content_sync()` and is no longer needed.

---

## [2.3.8] — April 2026

### Fixed
- **`coming-back-after-leaving` invisible on `/articles/`**: The Inner Journey uses subcategory rendering, which only displays articles explicitly listed in `$subcategories` slug arrays. This article (order 100) was not in any of the three subcategory lists — it existed in the DB and had the correct topic but was silently skipped by the renderer and never appeared on the page. Added to the *What Happens Next* subcategory.
- **Stale `$topic_descs` entries in `archive-ce_article.php`**: Retired topic descriptions for `Islamic Beliefs & Practice` and `Rights, Freedoms & Hard Questions` were still present. Removed; `Rights & Freedom` description updated to reflect its full scope after the v2.3.6 consolidation.

---

## [2.3.7] — April 2026

### Fixed
- **Retired topics not removed**: `Islamic Beliefs & Practice` and `Rights, Freedoms & Hard Questions` persisted in the taxonomy with 0 articles because `ce_cleanup_deprecated_topics()` only knew about the v2.3.1 set. Both are now included and will be deleted on the next sync.
- **Slug rename creating duplicate articles**: Renaming `if-god-answers-prayer-why-cant-you-prove-it` → `does-god-answer-prayer` in the JSON caused the sync to create a new post instead of updating the existing one (lookup by slug failed). Sync now checks `$slug_rename_map` — if the new slug is not found in the DB, it falls back to the old slug, updates in place, and renames `post_name`. No duplicate is created.
- **Orphaned articles not cleaned up**: After a slug rename, the old-slug post remained published. Sync now runs orphan detection after the main loop — any `ce_article` post whose slug is not present in the JSON is automatically moved to Trash. This also catches any articles from the legacy PHP creation system that are no longer in the data set.
- **Articles not recategorised on re-sync**: Articles previously assigned to retired topics were not being moved because `$topic_migration` in the sync loop did not include the v2.3.6 retired topics. Both `Islamic Beliefs & Practice → Does God Exist?` and `Rights, Freedoms & Hard Questions → Rights & Freedom` are now in the migration map.
- **`ce_ensure_topics_exist()` recreating retired topics**: The function was listing `Islamic Beliefs & Practice` and `Rights, Freedoms & Hard Questions` in `$required_topics`, causing them to be re-created immediately after cleanup on the same sync run. Both removed from the required list — only the 10 canonical active topics are now registered.

### Changed
- Sync log format extended: now reports `Updated`, `Created`, `Renamed`, `Orphans trashed`, and `Failed` counts separately.
- `ce_sync_set_topic()` and `ce_sync_get_or_create_topic()` extracted as named helper functions (previously inline code duplicated in update and create branches).

---

## [2.3.6] — April 2026

### Added
- **Favicons & Site Icon**: Full favicon set self-hosted in `assets/images/` — `ce-icon.svg`, `favicon.ico`, `favicon-16x16.png`, `favicon-32x32.png`, `favicon-96x96.png`, `apple-touch-icon.png`. Declared in `header.php` `<head>`. WordPress admin uses CE icon via `get_site_icon_url` filter in `functions.php`.
- **CE Admin Icon**: Admin menu entry for Theme Options shows CE branded icon (`ce-admin-icon.svg` + PNG variants `ce-admin-icon-20.png`, `ce-admin-icon-40.png`). Injected via `ce_admin_icon_styles()` in `functions.php`.
- **CE Icon in Theme Options title**: Admin page title now renders the `ce-icon.svg` instead of the previous text monogram.

### Changed
- **Category consolidation**: `Islamic Beliefs & Practice` and `Rights, Freedoms & Hard Questions` retired. Articles reassigned to `Rights & Freedom`, `Does God Exist?`, `The Problem of Evil`, and other existing categories. Theme now operates on 10 canonical topics.
- **Article slug renamed**: `if-god-answers-prayer-why-cant-you-prove-it` → `does-god-answer-prayer`. Title unchanged. Order 87, topic: Does God Exist?
- **Batch redistribution**: All 5 batches now hold exactly 24 articles each (previously variable). No articles added or removed — purely structural.
- **`archive-ce_article.php`**: `$topic_order` corrected to 10 active categories; stale `Islamic Beliefs & Practice` and `Rights, Freedoms & Hard Questions` entries removed.
- **`ce-crosslinks.php`**: `unanswered prayer` slug updated to `does-god-answer-prayer` to match renamed article.
- **`manifest-clean.json`**: Version and batch source fields updated to match current state.
- **`doc/ssot.md`**: Topics table corrected to 10 canonical categories; retired topics removed.

---

## [2.3.5] — April 2026

### Security
- **Removed `inc/articles/diagnose.php`**: Standalone diagnostic script was directly accessible via public URL with no authentication, leaking server paths, checksum values, and article data structure to anyone with the link.

### Added
- **Article Data Diagnostics panel** in Theme Options → Tools & Sync tab: Replaces the removed standalone file with a proper in-admin diagnostic tool, gated by `manage_options` capability. Shows PHP version, directory status, per-batch checksum verification (using the same BOM-strip + CRLF-normalise logic as the loader), articles loaded count, loader error list, and sync key status.

### Changed
- **Article redistributed across batches**: All 5 batches now hold exactly 24 articles each (previously variable: 20/21/35/14/30). No articles added or removed — purely structural.
- **Article slug renamed**: `if-god-answers-prayer-why-cant-you-prove-it` → `does-god-answer-prayer`. Title unchanged. Order 87, topic: Does God Exist?
- **Category consolidation**: `Islamic Beliefs & Practice` and `Rights, Freedoms & Hard Questions` retired — articles reassigned to `Rights & Freedom`, `Does God Exist?`, `The Problem of Evil`, and other existing categories. Theme now operates on 10 canonical topics.
- **`archive-ce_article.php`**: `$topic_order` corrected to 10 active categories (removed stale `Islamic Beliefs & Practice` and `Rights, Freedoms & Hard Questions` entries).

### Fixed
- **`manifest-clean.json`**: Version updated from `2.3.3` to `2.3.5`; batch source fields corrected.

---

## [2.3.4] — April 2026

### Fixed
- **Content Sync Never Firing**: `manifest.json` checksums were stale — computed from a different build run than the deployed batch files. All 5 batches failed SHA256 verification on every load, causing `CE_Article_Loader` to return zero articles and `ce_sync_article_content()` to abort silently. Manifest regenerated with checksums matching the deployed files.
- **`build-articles.php` Always Produces Wrong Checksums**: Checksum was computed on the intermediate JSON (before `meta.checksum` was embedded), then `meta.checksum` was added and the file re-encoded — producing a different on-disk payload whose hash never matched the manifest. Fixed: checksum is now computed on the final JSON that is written to disk. `meta.checksum` embedding removed (unused by the loader).
- **Batch Collision in `build-articles.php`**: `articles-data-5.php` and `articles-data-6.php` were both mapped to `'batch' => '005'`. On the next build run the second would overwrite the first and produce a duplicate manifest entry. Fixed: `articles-data-6.php` now maps to `'batch' => '006'`.

### Changed
- `build-articles.php` manifest `version` field now tracks the theme version instead of a hardcoded `2.3.0`.

---

## [2.3.3] — April 2026

### Fixed
- **Topic Migration in Sync**: Added topic migration map to `ce-content-sync.php` — deprecated topic names (`Examining the Quran`, `Examining the Sources`, `History & Context`, `The Bigger Picture`) are now remapped to their current equivalents before syncing, preventing orphaned articles.
- **Topic Migration in Theme Options**: Same migration map added to `ce-theme-options.php` import path for consistency.

### Changed
- `archive-ce_article.php` topic list and descriptions updated to reflect restructured category names from v2.3.1.

---

## [2.3.1] — April 2026

### Changed
- **Category Restructure**: Streamlined 10 topic categories for clearer navigation
  - Merged "Examining the Quran" + "Examining the Sources" → "The Quran & Its Sources"
  - Renamed "History & Context" → "History, Context & Comparison"
  - Renamed "The Bigger Picture" → "Revelation & Meaning"
  - New category: "Divine Justice & Fairness" (9 articles about God's fairness, afterlife, judgment)
  - New category: "Islamic Beliefs & Practice" (12 articles about rituals, practices)
  - Expanded "Rights & Freedom" → "Rights, Freedoms & Hard Questions"
  - Streamlined "Does God Exist?" from 19 to 10 articles (moved practice questions to new categories)
- **File Naming Consistency**: Renamed `articles-data-next.php` to `articles-data-5.php`
- **New Article Added**: "Why Would God Create Enemies? The Vast Universe and Divine Judgment" (Order 120, Category: Divine Justice & Fairness)

### Added
- **Auto-Topic Creation**: Sync now auto-creates topic terms if they don't exist
- **Topic Cleanup**: Sync automatically deletes deprecated topic terms after restructure
- **Backup Archive**: New `articles-data-6.php` in backup folder containing new article

### Fixed
- **Article Content**: All 120 articles now have full HTML content (previously empty)

---

## [2.3.0] — April 2026

### Security
- **JSON Article Storage**: Migrated from PHP data files to JSON format — eliminates code injection risk
  - Old: `inc/articles-data.php` required via `require_once` (executes code)
  - New: `inc/articles/batch-XXX.json` loaded via `file_get_contents()` + `json_decode()` (data only)
  - SHA256 checksums verify file integrity before loading
  - Graceful degradation: failed checksums skip batch rather than crash admin
- **CE_Article_Loader Class**: New secure loader with tamper detection
- **Content Sanitization**: All article content filtered through `wp_kses_post()` before database insert

### Fixed
- **PHP 8.4 Compatibility**: Fixed 5 unescaped apostrophes in `articles-data-5.php` (formerly `articles-data-next.php`) that caused parse errors
- **PHP 8.4 Nullable Parameters**: Added explicit `?int` type declarations to `ce_render_share_bar()`, `ce_render_vote_widget()`, and `ce_render_resonance()` in `ce-engagement.php`
- **Cron Scheduling**: Wrapped `wp_schedule_event()` call in proper `init` hook function to prevent fatal errors when theme files load before WordPress is fully initialized

### Added
- **Q&A System**: New `ce_question` Custom Post Type with ticketing workflow (New → In Review → Answered → Published/Archived)
- **Q&A Admin**: Dashboard widget, custom columns, status taxonomy, email notifications
- **Public Q&A Archive**: `/qa/` page showing published questions with answers
- **Content Sync Safeguards**: Time limit (300s), memory limit (256M), locking to prevent concurrent runs
- **Build Script**: `build-articles.php` to convert PHP → JSON (CLI-only for security)
- **Data Completeness Check**: Sync aborts if JSON files are incomplete (placeholders detected)

### Changed
- **Article Data Structure**: Reorganized from 5 PHP files to `inc/articles/` folder with JSON batches
  - `articles-data.php` → `articles/batch-001.json` (Articles 1-20)
  - `articles-data-2.php` → `articles/batch-002.json` (Articles 21-41)
  - `articles-data-3.php` → `articles/batch-003.json` (Articles 42-76)
  - `articles-data-4.php` → `articles/batch-004.json` (Articles 77-90)
  - `articles-data-5.php` → `articles/batch-005.json` (Articles 91-119)
  - `articles-data-6.php` → `articles/batch-005.json` (Article 120 — added during category restructure)
- **Documentation Structure**: Moved all documentation files to `/doc/` folder:
  - `README.md` → `doc/readme.md` (updated with folder structure reference)
  - `CHANGELOG.md` → `doc/changelog.md` (this file)
  - `UPGRADING.md` → `doc/upgrading.md`
  - `SSOT.md` → `doc/ssot.md`
  - `readme.txt` remains in root for WordPress.org compliance

---

## [2.2.78] — April 2026

### New Articles (17)
- Why Does God Need Worship?
- Isn't Your Religion Just an Accident of Birth?
- Why Do Muslims Pray in Arabic?
- Isn't the Quran Just Recycled Bible Stories?
- Why Does Islam Spread So Fast in Prison?
- If Muhammad Was a Prophet, Why Did He Need a Sword?
- Why Does Islam Have So Many Rules?
- If God Rewards Faith and Punishes Doubt — Loyalty Test?
- Can We Trust That Hadith Actually Go Back to the Prophet?
- The Quran Describes Human Creation Four Ways — Contradiction?
- Is Circling the Kaaba and Kissing the Black Stone Idol Worship?
- Does Islam Say Mental Illness Is Caused by Jinn?
- Did the Moon Actually Split?
- What Is Ramadan Actually For?
- Does Islam Really Believe in the Evil Eye?
- Why Does Islam Treat the Body as Impure?
- Is Islam Just a System of Control Built on Fear?

### Article Expansions (16)
Expanded all articles below 600 words to 700+ words:
- unanswered-prayer, religious-trauma, apostasy-international-law, near-death-experiences
- god-as-psychological-projection, religious-experience-neuroscience, hadith-reliability
- euthyphro-dilemma, finite-sins-infinite-punishment, burden-of-proof
- evolution-explains-design, apostasy-political-history, science-limits
- god-of-gaps, does-god-communicate-with-humanity
- how-do-we-evaluate-competing-claims-to-revelation
- honour-killings-culture-not-islam

### divine-hiddenness: Full Rewrite (340w → 1,108w)
Added fitrah section, hadith qudsi "I was a hidden treasure", Arabic citations, expanded all sections.

### Glossary: 53 → 85 Terms
Added 32 Islamic/Arabic terms: isnad, matn, rijal, sahih, hasan, da'if, mutawatir, ahad, wudu, tahara, ruqya, sihr, tafakkur, kashf, ijtihad, maslaha, taqlid, qiblah, bismillah, du'a, khawf, raja', mahabbah, ayn, alaqa, adha, ahl al-fatrah, tabi'un, zakat al-fitr, shafa'ah, ikhtilaf, and more.

### Crosslinks: 161 → 197 Phrase Mappings
Fixed 18 orphaned crosslink targets. Added crosslinks for all 17 new articles.

### Performance
- Preload uthmani-quran.woff2 + ce-hadith-400.woff2 on front page (LCP fix)
- Inline @font-face for Arabic fonts at wp_head priority 0
- templates.css now excluded from front page (saves 42 KiB unused CSS)
- Cache-Control headers set to max-age=31536000 for fonts/versioned assets
- Removed unused preconnects to fonts.googleapis.com / fonts.gstatic.com
- GTM deferred via script_loader_tag filter
- Fixed forced reflow: heroH cached outside parallax scroll loop

### Accessibility
- Added `<main>` landmark to 9 templates
- Fixed scroll-hint focusable descendants (tabindex=-1)
- Fixed heading order (h3→h2 in quick-access, h4→h3 in footer)
- Fixed 6 contrast failures in footer (tagline, links, headings, bottom bar)
- Added descriptive aria-labels to card-link "Read more" anchors

### SEO
- robots.txt programmatic generation pointing to /wp-sitemap.xml
- WordPress dynamic sitemap enabled with ce_article + ce_topic registered
- Static sitemap.xml replaced with redirect to wp-sitemap.xml
- Fixed 4 non-descriptive "READ MORE" link texts

### Bug Fixes
- ce-crosslinks.php: hadith-authenticity slug corrected (was pointing to non-existent hadith-reliability)
- articles-data-next.php: apostrophe escaping fixed in excerpts
- single.php: inline CSS (5,121 chars) extracted to templates.css
- ce_create_next_articles(): now handles all 8 topic categories (was hardcoded to The Bigger Picture)
- functions.php: ce_remove_unused_preconnects removes parent theme Google Fonts hints

### Unified Crosslink + Tooltip Engine
Rewrote ce-crosslinks.php (428 lines) as a single-pass content filter that handles both internal crosslinks and glossary tooltips.

**Ground rules (hardcoded, always enforced):**
1. Same keyword linked/tooltipped at most ONCE per article
2. NEVER inside heading text h1-h6
3. Crosslink and tooltip NEVER appear in the same paragraph
4. Every article gets at least 3 internal crosslinks
5. Per-post override via meta box in article editor
6. Per-category disable via Theme Options

### Theme Options — Links & Tooltips Tab (8th Tab)
13 configurable fields across two sections:

**Internal Cross-Links:**
- Enable/disable crosslinks globally
- Maximum/minimum crosslinks per article
- Link style and color
- rel="nofollow" toggle
- Disabled categories

**Glossary Tooltips:**
- Enable/disable tooltips globally
- Maximum tooltips per article
- Link style and color
- Show Arabic script toggle
- Trigger method (hover/click)

---

## [2.2.77] — April 2026

### Unified Crosslink + Tooltip Engine
Replaced two separate filters with single-pass unified engine.

### Theme Options — Links & Tooltips Tab
Added 8th tab with 13 configurable fields for crosslinks and tooltips.

---

## [2.2.76] — March 2026

### Amiri Font Removed
Deleted all Amiri font files (Latin-subset only, useless for Arabic).
CE Hadith (Noto Naskh Arabic) is now the sole Arabic font for non-Quranic text.

### Verse End Mark
Front page verse mark now uses ornate brackets rendered by CE Hadith font.

### Theme Options Introduced
New tabbed admin settings page with 7 tabs:
General, Colors, Typography, Quiz & Journeys, Engagement, Analytics, Performance.

---

## [2.2.75] — March 2026

### Uthmani Quran Font Updated
Replaced with KFGQPC HAFS version (608 glyphs).
CE Hadith font added for non-Quranic Arabic text.

---

## [2.2.74] — March 2026

### Layout Fix
Assigned page-journey.php template to parent /journey/ page.
Added theme.json to child theme for proper content sizing.

---

## [2.2.0] — February 2026

### Category Restructure
Replaced previous taxonomy structure with 10 canonical categories.
Automatic migration handled by ce-migration-2-2-0.php.

---

*For full version history, see the git log.*
