# SSOT — Single Source of Truth
## Compelling Evidence Theme — System Design, Naming Conventions, Configuration Rules

**Version:** 2.6.38  
**Location:** `/doc/SSOT.md`

This document is authoritative. When any other file conflicts with a rule stated here, this document is correct and the other file should be updated.

---

## 1. Theme Identity

| Property | Value |
|---|---|
| Theme Name | CE Theme |
| Theme URI | https://github.com/menj/compelling-evidence (always this repository) |
| Author / Author URI | MENJ / https://menj.blog |
| Folder name | `compelling-evidence` (always the zip root; never renamed) |
| Text Domain | `compelling-evidence` |
| Parent Theme | `twentytwentyfive` (Template field in style.css) |
| Version source | `style.css` — the only place version is set |
| Site URL | https://compelling-evidence.com |
| WP minimum | 6.7 (Twenty Twenty-Five 1.5 requires it) |
| Parent version tested | Twenty Twenty-Five 1.5 (`CE_PARENT_TESTED_VERSION`) |
| PHP minimum | 8.0 |

---

## 2. Documentation Structure

All theme documentation is in `/doc/`:

| File | Purpose |
|------|---------|
| `doc/readme.md` | Theme overview, quick start, architecture |
| `doc/changelog.md` | Version history and release notes |
| `doc/upgrading.md` | Migration guides for each version |
| `doc/ssot.md` | This file — naming conventions and system design |
| `readme.txt` | WordPress.org standard (remains in root) |

---

## 3. Custom Post Type

**Slug (registered):** `ce_article`  
**Archive slug:** `articles` (registered as `['slug' => 'articles']` in `register_post_type`)  
**Article URL pattern:** `/articles/{post-name}/`  
**Taxonomy:** `ce_topic`  
**Taxonomy URL pattern:** `/topic/{term-slug}`

These slugs are permanent. Changing either requires:
- Updating the rewrite rule registration in `functions.php`
- Updating the template file names (WP naming mandates underscores: `single-ce_article.php`, `archive-ce_article.php`, `taxonomy-ce_topic.php`)
- Flushing rewrite rules
- Updating all crosslink base URLs in `ce-crosslinks.php`

**Template naming rule:** WordPress mandates underscores in CPT/taxonomy template file names where the registered slug contains an underscore. `single-ce_article.php` is correct. `single-ce-article.php` would not be recognised by WordPress. This is a WordPress rule, not a project preference.

---

## 4. Article Data — Source of Truth (JSON-Based)

**Security Update (v2.3.0):** Article data is now stored in JSON format instead of PHP files to prevent code injection attacks. JSON files cannot execute code — they are pure data.

The JSON data files are the authoritative source for article content:

```
inc/articles/
├── manifest.json             # Registry with SHA256 checksums (version-matched)
├── batch-001.json            # 24 articles
├── batch-002.json            # 24 articles
├── batch-003.json            # 24 articles
├── batch-004.json            # 24 articles
└── batch-005.json            # 24 articles (including order 120, added v2.3.1)
```

**Note on batch-006:** After the next `build-articles.php` run, `articles-data-6.php` will produce a separate `batch-006.json` (1 article). At that point update `$expected_batches` in `class-ce-article-loader.php` from 5 → 6.

**The WordPress database is a cache.** `ce-content-sync.php` overwrites `post_content` and `post_excerpt` in `wp_posts` on every version bump. Do not edit articles in the WordPress editor with the expectation that edits will persist across upgrades.

**Security Features:**

| Feature | Purpose |
|---------|---------|
| SHA256 Checksums | Detect file tampering before loading |
| JSON Only (No PHP) | Zero code execution risk |
| Graceful Failures | Invalid JSON/checksum = skip batch, don't crash |
| `wp_kses_post()` | Sanitize content before database insert |

**Loading Mechanism:**

```php
// Secure JSON loading (v2.3.0+)
$loader = new CE_Article_Loader();
$articles = $loader->load_all_articles(); // Verifies checksums automatically

// Legacy PHP loading (DEPRECATED — removed in v2.3.0)
// $articles = ce_get_articles_data(); // No longer works
```

**JSON Batch File Structure:**

```json
{
  "meta": {
    "batch": "001",
    "generated": "2026-04-21T00:00:00Z",
    "source_file": "inc/articles-data.php",
    "article_count": 20,
    "checksum": "sha256:abc123..."
  },
  "articles": [
    {
      "slug": "why-does-anything-exist",
      "title": "Why Does Anything Exist Rather Than Nothing?",
      "topic": "Does God Exist?",
      "order": 1,
      "excerpt": "...",
      "content": "<p>...</p>"
    }
  ]
}
```

**Required keys per article:**

```
slug    // string — URL-safe, hyphenated, unique across all batches
title   // string — displayed as post_title and <h1>
topic   // string — must exactly match a registered ce_topic term name
'order'   // int — menu_order; determines display sequence within topic
'excerpt' // string — post_excerpt; used in cards and meta descriptions
'content' // string — full HTML article body
```

**Article Data Migration:**

The build script `build-articles.php` converts legacy PHP files to JSON:

```bash
# Convert all PHP article files to secure JSON
php build-articles.php
```

**Security note:** The script is CLI-only (`php_sapi_name() !== 'cli'` check) and uses regex parsing — it never executes the PHP files, only reads them as text.

This generates:
- 5 JSON batch files with embedded SHA256 checksums
- `manifest.json` with registry of all batches
- Content is preserved exactly, format changes only

**Security Comparison:**

| PHP System (v2.2.x) | JSON System (v2.3.0+) |
|---------------------|----------------------|
| `require_once` — executes code | `file_get_contents` + `json_decode` — reads data only |
| Parse errors crash admin | JSON errors logged gracefully |
| No tamper detection | SHA256 checksums verify integrity |
| Apostrophe escaping required | Standard JSON encoding |

---

## 5. The 11 Canonical Topic Categories

These are the only valid values for the `'topic'` key in article data. Exact string match required.

| Topic name (exact) | Taxonomy slug |
|---|---|
| Does God Exist? | `does-god-exist` |
| The Problem of Evil | `the-problem-of-evil` |
| Ethics Without God? | `ethics-without-god` |
| Science & Evidence | `science-evidence` |
| The Quran & Its Sources | `quran-and-sources` |
| History, Context & Comparison | `history-context-comparison` |
| Divine Justice & Fairness | `divine-justice-fairness` |
| Islamic Practice & Ritual | `islamic-practice-ritual` |
| Rights & Freedom | `rights-freedom` |
| The Inner Journey | `the-inner-journey` |
| Revelation & Meaning | `revelation-meaning` |

**Deprecated topics — do not use in new article data:**

| Deprecated name | Replaced by | Since |
|---|---|---|
| Examining the Quran | The Quran & Its Sources | v2.3.1 |
| Examining the Sources | The Quran & Its Sources | v2.3.1 |
| History & Context | History, Context & Comparison | v2.3.1 |
| The Bigger Picture | Revelation & Meaning | v2.3.1 |
| Islamic Beliefs & Practice | Islamic Practice & Ritual | v2.3.6 |
| Rights, Freedoms & Hard Questions | Rights & Freedom | v2.3.6 |

`ce-content-sync.php` automatically remaps deprecated topic names during sync. `build-articles.php` should not produce deprecated topic names in new batch files.

---

## 6. Article Ordering Convention

Articles are ordered by the `'order'` integer key, which becomes `menu_order` in
`wp_posts`. The archive template (`archive-ce_article.php`) groups articles by
topic using a hardcoded canonical array, then sorts within each topic by
`menu_order ASC`.

**Order numbers are no longer contiguous per topic.** Earlier versions
(pre-v2.3.6) grouped order numbers in tight ranges per topic — orders 1–14 for
Does God Exist?, 15–22 for The Problem of Evil, and so on. The v2.3.6
redistribution that balanced batches to exactly 24 articles each broke that
contiguity. As of v2.6.2 the actual ranges are:

| # | Topic | Count | Order range (non-contiguous) |
|---|---|---|---|
| 1 | Does God Exist? | 12 | 1–97 |
| 2 | The Problem of Evil | 9 | 9–101 |
| 3 | Ethics Without God? | 5 | 6–59 |
| 4 | Science & Evidence | 11 | 5–99 |
| 5 | The Quran & Its Sources | 22 | 14–114 |
| 6 | History, Context & Comparison | 10 | 16–107 |
| 7 | Divine Justice & Fairness | 5 | 28–120 |
| 8 | Islamic Practice & Ritual | 6 | 104–116 |
| 9 | Rights & Freedom | 12 | 29–119 |
| 10 | The Inner Journey | 17 | 30–100 |
| 11 | Revelation & Meaning | 11 | 17–98 |

**Total: 131 articles.** Reading-sequence ordering on `/articles/` is therefore
determined by the archive template's canonical topic array (groups), then by
`menu_order` within each topic (intra-group sequence). The `'order'` key in
batch JSON is only meaningful relative to other articles of the same topic.

---

## 7. PHP Function Naming Conventions

**Prefix:** `ce_` (Compelling Evidence) — all functions, actions, filters, and option keys.

**File-based grouping:**
- `ce_register_*` — registration functions (CPT, taxonomy, menus)
- `ce_ajax_*` — AJAX action handlers
- `ce_handle_*` — form POST handlers
- `ce_get_*` — data retrieval (pure functions, no side effects)
- `ce_render_*` — HTML output functions (echo/return markup)
- `ce_enqueue_*` — asset registration
- `ce_maybe_*` — conditional one-time runs (guarded by option flags)
- `ce_migrate_*` — database migration routines

**Option keys:** `ce_{descriptor}` — all stored in `wp_options` with this prefix.

---

## 8. CSS Class Naming Conventions

**Prefix:** `ce-` for all theme-specific classes.

| Pattern | Usage |
|---|---|
| `ce-` | Component wrapper (e.g., `ce-nav`, `ce-hero`, `ce-card`) |
| `ce-*-wrap` | Layout wrapper for section |
| `ce-*-inner` | Inner constrained container |
| `ce-*-btn` | Button variants |
| `ce-*-link` | Link variants |
| `ce-*-title` | Heading elements |
| `ce-*-text` | Text content blocks |

No BEM. Single class per element, nested selectors in CSS.

---

## 9. Database Tables

Two custom tables are created on theme activation:

**`{prefix}_ce_analytics`** — Privacy-first analytics storage
- Columns: id, event_type, event_data (JSON), visitor_hash, session_id, page_url, referrer, device_type, created_at
- Indexes: event_type, created_at, visitor_hash, session_id

**`{prefix}_ce_engagement`** — Individual vote/resonance records (for duplicate prevention)
- Columns: id, post_id, user_id, ip_hash, action_type, created_at
- Indexes: post_id + action_type, ip_hash + post_id + action_type

Both tables use `$wpdb->get_charset_collate()` for proper UTF-8 mb4 support.

**Creation rule (since 2.6.21).** Tables are created through `ce_create_table()` in `inc/ce-db.php`, which runs the `CREATE TABLE IF NOT EXISTS` statement directly and confirms the result with `ce_table_exists()`. Theme code must not call `dbDelta()` or require `wp-admin/includes/upgrade.php`; a missing core file there is fatal. Table checks run on `after_switch_theme` and `admin_init` only, never on front-end `init`. A table's version option (`ce_analytics_table_version`, `ce_engagement_db_version`) is written only after the table is confirmed to exist.

**Seeded terms.** The five `ce_qstatus` terms are created by `ce_seed_question_statuses()` on `admin_init` and `after_switch_theme`, gated by the `ce_qstatus_seeded` option. `ce_register_question_status()` registers the taxonomy only and performs no queries.

---

## 10. Version Bumping Checklist

When incrementing the version in `style.css`:

1. [ ] Update `Version:` header in `style.css`
2. [ ] Update version reference in `doc/readme.md`
3. [ ] Update version reference in `doc/ssot.md` (this file)
4. [ ] Add entry to `doc/changelog.md`
5. [ ] Add entry to `doc/upgrading.md` (if migration needed)
6. [ ] Update `readme.txt` version header and changelog section
7. [ ] Regenerate `manifest.json` via `php build-articles.php` if batch files changed
8. [ ] Test content sync fires on next admin page load
9. [ ] Verify all 131 articles re-sync correctly
10. [ ] Confirm `inc/articles/backup/articles-data-5.php` topics match `batch-005.json`

---

## 11. Git Workflow

Main branch is `main`. Version tags follow semantic versioning prefixed with `v`:
- `v2.2.78`
- `v2.3.0`

Distribution zip is built from a clean checkout:
```bash
git checkout v2.3.0
zip -r compelling-evidence.zip compelling-evidence -x "*.git*" -x "*.DS_Store"
```

---

## 12. Journey File Conventions

**Location:** `/journeys/[slug]-journey.html`

**Slug format:** `{persona}-journey` where persona matches the quiz result key exactly.

**Structure:** Self-contained HTML document, no WordPress template wrapping. Must include:
- `<!DOCTYPE html>`
- `<html lang="en">` (or appropriate lang code)
- `<head>` with inline CSS or link to journey-base.css
- `<body>` with navigation and content
- Progress storage via `localStorage.setItem('ce_journey_progress_{slug}', screenId)`

**Translations:** Place at `/journeys/{locale}/[slug]-journey.html` where locale is WordPress locale string (e.g., `ms-MY`).

---

## 13. Performance Budgets

Hard limits (measured in Chrome DevTools, throttled 4G):

| Metric | Target | Maximum |
|---|---|---|
| First Contentful Paint | < 1.2s | 1.5s |
| Largest Contentful Paint | < 2.0s | 2.5s |
| Time to Interactive | < 3.0s | 4.0s |
| Total Blocking Time | < 100ms | 200ms |
| Cumulative Layout Shift | < 0.1 | 0.15 |

**Asset budgets:**
- CSS (all): < 110 KiB compressed
- JS (all): < 25 KiB compressed
- Fonts: < 180 KiB (6 files × ~30 KiB)
- Hero image: < 150 KiB (PNG-8 with alpha)

---

## 14. Font Architecture

All fonts are self-hosted (zero external Google Fonts requests).

### Font Inventory (23 woff2 files, ~1.1MB on disk; browsers download only the families in use)

| Font | Weights | Usage | Size |
|---|---|---|---|
| Playfair Display | 400, 700, 900, 400i | Headings, titles | ~22KB each |
| DM Sans | 300, 400, 500, 600 | UI, labels, body | ~14KB each |
| Cormorant Garamond | 300, 400, 600, 300i, 400i | Article reading body | ~22KB each |
| EB Garamond | 400-800 variable, roman + italic | Optional heading / reading font (OFL) | 69KB + 74KB |
| Special Elite | 400 | Optional typewriter label font (Apache 2.0) | 49KB |
| Sabon Next LT | 400, 700, 400i, 700i | Optional heading / reading font (commercial, licensed by the site owner) | ~100-107KB each |
| CE Hadith | 400 | Arabic text (Noto Naskh Arabic subset) | ~69KB |
| Uthmani Quran | 400 | Quranic verses (KFGQPC HAFS Uthmanic Script) | 107KB |

**Note:** Amiri font was removed in v2.2.76 (Latin-subset only, useless for Arabic).

### Font Roles (since 2.6.23)

CSS never names a Latin family directly outside `@font-face`. It uses four role variables declared on `:root` in `main.css`:

| Variable | Default | Set by |
|---|---|---|
| `--font-heading` | Playfair Display | Typography → Heading Font (`ce_heading_font`) |
| `--font-reading` | Cormorant Garamond | Typography → Reading Body Font (`ce_reading_font`) |
| `--font-ui` | DM Sans | Fixed |
| `--font-accent` | `var(--font-ui)` | Typography → Label & Eyebrow Font (`ce_accent_font`) |

`inc/ce-fonts.php` holds the registry (`ce_font_registry()`), prints a `:root` override only for roles that differ from their defaults, adds the `ce-accent-typewriter` body class when Special Elite is chosen, and prints the font preload tags in `header.php`. To add a family: add its `@font-face` rules to `main.css`, its file to `assets/fonts/`, its licence to `assets/fonts/licenses/`, and one registry entry naming the roles it may fill.

**Transliteration rule.** Any family offered for the heading or reading role must contain ā ī ū ḥ ṣ ḍ ṭ ẓ ʿ ʾ and their capitals, because titles and article text carry them. The EB Garamond subset was built to include them. Special Elite lacks the dotted consonants and the ʿ ʾ marks, so it is restricted to the accent role, and the accent selector list (end of `main.css` and `templates.css`) must only contain English label elements.

**Sabon Next LT and the fill faces.** Sabon lacks ḥ ḍ ṭ ẓ ʿ ʾ and the capitals Ḥ Ḍ Ṭ Ẓ. The `'Sabon Next LT'` family in `main.css` therefore has two extra `@font-face` rules per style that serve those code points from the EB Garamond files through `unicode-range`. Lowercase letters and ʿ ʾ use `size-adjust: 109.8%` (Sabon x-height 0.439 ÷ EB Garamond 0.400); capitals use `104.8%` (cap height 0.681 ÷ 0.650). The fill rules come after the Sabon rules so they win for their ranges. The Sabon files are format conversions of the licensed TTFs with glyph data unchanged; no licence text ships in `assets/fonts/licenses/` because the licence is held by the site owner. Sabon's x-height is about 14% larger than Cormorant's, so reading text set in Sabon looks larger at the same size; use Font Size Scale to compensate if needed.

**Journey pages.** The static HTML files in `journeys/` declare their own fonts and do not read the role variables.

### Arabic Font Rendering Chain

Quranic text uses this font stack:
1. **Uthmani Quran** — Primary. 608 glyphs covering U+0600-06FF + U+FB50-FDFF.
2. **CE Hadith** — Secondary for non-Quranic Arabic.
3. **System fonts** — Noto Naskh Arabic (Android/Linux), Traditional Arabic (Windows), Geeza Pro (macOS/iOS).

### Known Limitation
Ornate brackets ﴿﴾ (U+FD3E-FD3F) are not in the Uthmani font. They render via system Arabic fonts.

---

## 15. Accessibility Requirements

All templates must pass these checks:

- `<main>` landmark present (exactly one per page)
- `<nav>` with `aria-label` for navigation regions
- Heading hierarchy correct (no skipped levels)
- Focus visible on all interactive elements
- Color contrast 4.5:1 minimum for body text
- Color contrast 3:1 minimum for large text/UI components
- Keyboard navigable (Tab order logical, focus traps avoided)
- Alt text on all informative images (decorative images empty alt)
- Form labels associated with inputs

---

## 15. Crosslink Engine Ground Rules

The unified crosslink + tooltip engine (`ce-crosslinks.php`) enforces these rules hardcoded:

1. **Once per keyword** — Any phrase can be linked or tooltipped at most once per article.
2. **No headings** — Never insert links or tooltips inside `<h1>` through `<h6>`.
3. **Paragraph exclusivity** — A crosslink and a tooltip cannot appear in the same paragraph.
4. **Minimum crosslinks** — Every article gets at least 3 crosslinks. Second pass with relaxed rules if needed.
5. **Per-post disable** — `_ce_disable_crosslinks` and `_ce_disable_tooltips` post meta can disable either system.
6. **Per-category disable** — Comma-separated slugs in Theme Options disable both systems for those topic categories.

---

*Last updated: October 2026 (v2.6.38)*
---

## 17. Feed Redirector (`inc/class-ce-feed-redirector.php`)

**Class:** `CE_Feed_Redirector` (singleton)  
**Booted via:** `after_setup_theme` action in `functions.php`  
**Since:** v2.4.9 (stabilised v2.6.2)

Suppresses WordPress RSS/Atom feeds entirely. The site has no use case for syndication feeds.

**What it does:**

| Action | Detail |
|---|---|
| Removes `<link>` autodiscovery | `feed_links` and `feed_links_extra` unhooked from `wp_head` |
| 301 redirects all feed URLs | `/feed/`, `/feed/rss/`, `/feed/atom/`, `?feed=rss2`, author/category/comment variants |
| Noindex header | `X-Robots-Tag: noindex, nofollow` on any feed response that reaches `send_headers` |
| robots.txt rules | 8 `Disallow` lines appended at priority 20 (after CE sitemap rule at priority 10) |
| Sitemap exclusion | Removes `posts` provider from WP core sitemap when no SEO plugin detected |

**Safe contexts:** Skips entirely in WP-CLI, cron, `is_admin()`, and REST API requests.

**SEO plugin detection:** Checks 9 known plugins. If any is active, sitemap exclusion is skipped (the SEO plugin manages that).

**robots.txt priority note:** `ce_robots_txt()` runs at priority 10. The feed redirector appends at priority 20, so the sitemap URL line always appears before the feed disallow block.

**Redirect loop prevention:** If the computed canonical URL still contains `/feed/`, falls back to `home_url('/')`.

**Physical robots.txt conflict:** If a physical `robots.txt` file exists in the WordPress root, the `robots_txt` filter does not run. Feed disallow rules must be added manually in that case.

---

## 18. Retired URLs (`inc/ce-gone.php`)

`ce_maybe_send_gone()` runs on `init` at priority 0 and answers any request whose path begins with a retired prefix with `410 Gone`, `X-Robots-Tag: noindex, nofollow`, and a one-line body, then exits before the main query, the template, or any plugin 404 logging runs.

| Option | Default | Purpose |
|---|---|---|
| `ce_gone_enabled` | `1` | Master switch |
| `ce_gone_paths` | `/shop/manufacturer-site`, `/product-similar-image`, `/product/category/` | Path prefixes, one per line, case-insensitive |

Rules: a blank line or a bare `/` is ignored, so the whole site can never be retired by mistake. Admin, AJAX, cron, REST and WP-CLI requests are exempt. Never add a prefix that real content uses.

---

## 19. Directly Drafted Batches and Citation Rules (since 2.6.22)

Batches 006 and 007 are written directly as JSON; they have no PHP source in `inc/articles/backup/` and `build-articles.php` does not regenerate them. When adding a batch this way: write the file with 2-space indentation and unescaped Unicode, compute its SHA-256 over the exact bytes on disk, add the entry to `manifest.json`, and update `total_articles`.

Citation blocks in new articles follow two rules. Quran Arabic is copied verbatim from a verified Uthmani source, never typed. Hadith Arabic must match the source text of the cited collection, and the batch-007 build script refused to run on any mismatch. From batch-007 onward, `quran-ref` and `hadith-ref` lines carry no leading dash.

---

## 20. Working With the Parent Theme (since 2.6.28)

CE Theme stays a child of Twenty Twenty-Five and is built to coexist with it.

**Template priority.** WordPress gives a block template priority over a PHP template at the same hierarchy level. The parent ships `index`, `home`, `single`, `page`, `page-no-title`, `search`, `archive` and `404` block templates. `ce_prefer_child_php_templates()` in `inc/ce-parent-compat.php` sets aside block templates whose source is a theme file, on the front end only, so CE's PHP templates render. Site Editor customisations (source `custom`) are kept and win, which lets a site owner override a view on purpose. The admin, the Site Editor and the REST API see the parent's templates unchanged. CE ships no block templates; if it ever adds a `templates/` folder, this filter must be narrowed to the parent's files.

**Presets and global styles.** The child `theme.json` keeps every parent preset slug and maps it to CE values, so parent patterns, blocks and any element CE's CSS does not style inherit CE's look instead of Twenty Twenty-Five's white page and Manrope type. Colour: `base` #2a1050, `contrast` #f5f0ff, `accent-1` #e8455a, `accent-2` #0cd4e0, `accent-3` #6b2fa0, `accent-4` #b4a8c7, `accent-5` #1a0a2e, `accent-6` unchanged. Fonts: CE's families are registered for the editor, and the parent's `manrope` and `fira-code` entries are carried over verbatim (their files resolve from the parent folder). Global body and heading styles use `var(--font-ui)` and `var(--font-heading)` with fallbacks, so they follow Theme Options → Typography on the front end. Global styles are emitted with zero specificity, so CE's stylesheets remain authoritative wherever they set a value.

**Parent assets left alone.** The parent's `style.min.css` (611 bytes: link underline thickness, focus outlines, navigation spacing) still loads. CE removes only `wp-block-library-theme`. No CE function uses the `twentytwentyfive_` prefix.

**Parent updates.** `ce_parent_theme_notice()` warns administrators on the Themes screen, the Dashboard and CE Theme Options when the parent is missing or when its major version is higher than `CE_PARENT_TESTED_VERSION`. After testing a new parent release, update that constant and this section.

---

## 21. The 404 Page (since 2.6.29)

`404.php` opens on a random excuse from `ce_404_excuses()` in `inc/ce-404.php`. Each excuse has a headline, a one-line explanation, the argument it riffs on and the slug of the article that treats that argument seriously; the page links to it with "Read the real argument". "Hear another excuse" is swapped client-side by `assets/js/ce-404.js`; without JavaScript its `?excuse=N` link reloads the page on the next excuse.

**House rules for excuses.** The joke lands on the missing page, the server or the webmaster, never on a faith, a scripture or the reader. Every entry points at a real article (or `''` for a general link to /articles/). Copy follows the site's English rules: no contractions, no em-dashes, no contrastive negation.

---

## 22. Responsive Behaviour (since 2.6.30)

**Test matrix.** Every release that touches layout is checked at 1440, 1280, 1024, 820, 390 and 360px on the home page, the article archive, a single article, a topic page, a plain page, FAQ, glossary, quiz, journeys, a single journey, Ask a Question, Q&A, My Progress, search and 404. Pass conditions: no horizontal overflow, no script errors, tap targets of at least 24px and no text below 12px on phones.

**Article sidebar (901px and up).** Only `.sidebar-sticky` (table of contents + reading progress) is sticky. It sits in `.sidebar-sticky-track`, which fills the sidebar's height, so the group stays in view for the length of the article; the remaining widgets follow the track at the end of the sidebar. A long table of contents scrolls inside its own card. The sidebar itself is never an internal scroll container: that arrangement let the mouse wheel scroll the sidebar and carry the progress card away. Below 901px the sidebar follows the article and the progress card is hidden.

**Top progress bar.** `#ce-reading-progress` sits at the bottom edge of the fixed header (`top: var(--nav-h)`) on all widths and is the progress indicator on phones and tablets.

**Mobile menu.** Closed, `.nav-links` sits wholly above the viewport with `visibility: hidden`, so it neither shows through the header nor takes keyboard focus. Escape closes it; widening the window past 900px closes it.

**Journey pages.** `assets/css/journey-base.css` is the shared responsive layer for all standalone journey HTML files (English and ms-MY). `page-journey.php` injects it after each page's own styles.

---

## 23. Search Guidelines (since 2.6.31)

Reference documents: Google's SEO Starter Guide and Google's list of structured data features it supports.

**Structured data the theme emits.** WebSite (name and URL only; site names still use it), Organization, Article and BreadcrumbList on articles, Article on answered reader questions, CollectionPage on archives, DefinedTermSet on the glossary, FAQPage on /faq/ only (its questions and answers are visible there). Article carries headline, image (featured image or `screenshot.png`), datePublished, dateModified, author with name and URL, and publisher.

**Never emit.** FAQPage for content not shown as Q&A on the page; QAPage anywhere (Google reserves it for pages where users submit answers, and lists a site-written single answer as invalid); ItemList outside a carousel; Speakable (news publishers only); SearchAction (sitelinks search box retired 21 November 2024). Journey and quiz HTML is cleaned at serve time by `ce_standalone_html_seo()`.

**Meta descriptions.** Every indexable page type has one, at most 130 characters including a call to action (`ce_meta_description_with_cta()`), unique per page. Topic pages use the term description or a count-based sentence; the old placeholder "Compelling Evidence article topic" is ignored. Search results and 404s carry none.

**Titles.** Article titles add "| Topic" only while the whole title stays within 65 characters; titles over 70 characters drop the site name. Standalone pages lead with the topic, brand last.

**Rank Math.** When Rank Math is active it owns titles, meta descriptions, canonical tags, Open Graph, robots.txt and Article schema; the theme prints none of these, so no page carries two of them.

**URLs and crawling.** One URL per piece of content: placeholder pages that duplicate a topic 301 to it. Canonicals must point at the URL actually served, never at a redirect. robots.txt allows /wp-includes/ (CSS and JS Google needs to render) and disallows internal search results. The core XML sitemap excludes redirecting and noindexed pages. My Progress is noindexed.

**On the page.** One h1 per page; topic pages use the topic name. Card links read "Read more" visually but carry the article title as screen-reader text; icon-only card placeholders are not links. Every theme image has alt text.

---

## 24. Coding Standard (since 2.6.32)

`phpcs.xml.dist` in the theme root defines the standard: PHPCompatibilityWP for PHP 8.0 to 8.4, plus the WordPress Security, PreparedSQL, PreparedSQLPlaceholders, DeprecatedFunctions, DeprecatedParameters, EnqueuedResources and StrictInArray sniffs. Run `phpcs` from the theme root before every release; the target is zero errors.

**Rules the codebase follows.**
- Escape at the point of output (`esc_html`, `esc_attr`, `esc_url`, `esc_js`, `wp_kses_post`), never earlier into a variable that is echoed later.
- Unslash, then sanitise, every value from `$_GET`, `$_POST` and `$_SERVER`.
- Custom tables are named with the `%i` identifier placeholder inside `$wpdb->prepare()`; check that `$table` sits at the argument position matching `%i`.
- Every `in_array()` is strict.
- An intentional exception carries a `phpcs:ignore` naming the sniff and a reason: complete static HTML documents (journeys, quiz), pre-sanitised CSS, the static SVG from `ce-icons.php`, and read-only display flags from the query string.

**Runtime check.** Each release is loaded on a local WordPress install with `WP_DEBUG` and `WP_DEBUG_LOG` on: all front-end page types, every CE Theme Options tab, the analytics dashboard, the article and question admin screens, and the analytics and search AJAX endpoints. The log must contain no PHP notices, warnings or deprecations from the theme.

---

## 25. Article Media (since 2.6.33)

**Three kinds of visual material.** `[ce_figure id="…"]Caption[/ce_figure]` for photographs and scans; `[ce_diagram id="…"]Caption[/ce_diagram]` for SVG diagrams in `assets/diagrams/`; plain HTML `<table class="ce-table">` with a `<caption>` for tables. `inc/ce-media.php` renders all three and wraps every article table in a scrollable, labelled region.

**Registry.** `inc/articles/media.json` holds one entry per image: `source` (`wikimedia-commons`, `pexels`, `flickr`), `file`, `url` (original), `thumb`, `page` (source page), `author`, `license`, `license_url`, `alt`, `width`, `height`, optional `crop` as fractions `[left, top, right, bottom]`, `variant` (`photo` or `light`; `light` puts a scan or calligraphy on a paper panel), and `verified` (the date the licence was checked against the source). A figure cannot render without a registry entry, and the credit line (author, licence, source link) is built from the entry. Rules: Commons images are checked through the Commons API (`extmetadata`) for licence and author before registration; Flickr images must carry a Creative Commons or public-domain licence; Pexels images carry the Pexels licence. Every image needs alt text that describes what is seen.

**Import.** `ce_media_import_pending()` copies registry images into the Media Library (three per admin page load, or all from Theme Options → Media → "Import pending images now"), applying any crop, and records ids in the `ce_media_attachments` option. Imported figures get srcset and lazy loading; until import, the figure loads the source thumbnail. Attachment meta: `_ce_media_key`, `_ce_media_source`, `_ce_media_license`.

**Diagrams.** SVGs use classes only (`dg-box`, `dg-title`, `dg-text`, `dg-ref`, `dg-label`, `dg-line`, `dg-step`, `dg-bar`), styled in `assets/css/ce-media.css` from the theme's colour tokens, so they follow Theme Options → Colors. Each carries `<title>` and `<desc>`. Every claim in a diagram or table must already appear in the article's own text; visuals summarise, they never add facts.

**Lightbox.** `assets/js/ce-lightbox.js`, no dependencies: grouped navigation, keyboard and swipe, focus management, inline-SVG diagrams. Theme Options → Media → "Open article images and diagrams in a lightbox". The Lightbox2 plugin is not needed and should be deactivated.

**Links and tooltips** never enter figures, diagrams or tables (`ce-crosslinks.php` skips those elements).

**Coverage.** All 131 articles open with a lead figure placed after the first paragraph; batch 007 also carries diagrams and tables. Registry: 131 images (124 Pexels, 7 Wikimedia Commons).

**Featured images.** A registry entry's `featured_for` names the article whose featured image it becomes on import (cards, Open Graph, Article schema). An editor's own featured image is never replaced. `ce_media_assign_featured()` backfills once per registry change.

**Choosing images.** No identifiable person on articles about leaving Islam, trauma, honour killings, sexuality, mental illness, prison or cult accusations; use objects and landscapes there. No symbols of another religion unless the article is about that scripture. Captions describe what the picture shows; alt text comes from the source's own description where it is accurate. Every pick is reviewed by eye before registration.

**API keys.** The Pexels API key lives only in the `ce_pexels_key` option (Theme Options → Media), never in the theme files or the registry.

**Automatic images (since 2.6.35, `inc/ce-media-auto.php`).** Hourly cron `ce_media_auto_cron`, plus Theme Options → Media → Run now. Each run imports up to ten registered images, then, when "Find and publish lead images" is on, places images on up to five published articles that have no featured image and no `[ce_figure]`.
- *Query:* the two most specific words of the title (stop words and question words removed); then the article's topic; then a safe fallback. On the title query the photograph's own description must mention one of those words.
- *Rules:* `ce_media_auto_banned_words()` (other faiths' symbols, alcohol, pork, weapons, blood, protests, revealing clothing, gambling, tattoos, skulls) rejects any candidate whose description contains them; on sensitive articles (`ce_media_auto_sensitive_words()`: leaving, doubt, trauma, abuse, violence, honour, sexuality, mental illness, jinn, prison, cult, control, fear, anger, terror, hijab, women, slavery, marriage) any candidate describing people is rejected and searches add "landscape". Both lists are filterable. Images narrower than 1,600 pixels and photographs already used or rejected are skipped.
- *Publication:* the entry goes to the `ce_media_auto_registry` option (merged into the registry), is imported, becomes the featured image, and is shown after the first paragraph by a `the_content` filter (`_ce_auto_figure`, `_ce_auto_caption` post meta), so article sync never overwrites it. The caption is the first sentence of the photograph's own description.
- *Corrections:* Replace deletes the image, records the Pexels id in `ce_media_auto_rejected` and picks the next acceptable photograph; Remove deletes it and adds the article to `ce_media_auto_skip`, so it is not filled again. Articles for which no acceptable photograph exists are also skipped.
- *Limits:* the rules read the stock site's own description, which can miss what is in the picture; captions are descriptive, never argumentative. Review the list in Theme Options → Media after each new batch.

## 26. Plugins: native or not (since 2.6.33)

- **Lightbox2** → replaced by the theme's own lightbox.
- **Pretty Search Permalinks (wp-seo-search)** → replaced by `inc/ce-search-permalinks.php` (Theme Options → Links & Tooltips → Search URLs); the theme steps aside if the plugin is still active.
- **Contact Form 7** and **Contact Form CFDB7** → stay plugins (forms and stored messages must survive a theme change). The theme styles CF7 forms (`assets/css/ce-cf7.css`) and loads CF7 scripts only on pages containing a form (`inc/ce-plugin-compat.php`).
- **WPS Hide Login** → stays a plugin: the login URL is a security control and must not depend on the active theme.

---

## 27. Rank Math (since 2.6.36)

**Precedence.** Where Rank Math and the theme overlap, Rank Math wins; the theme supplies a value only where Rank Math's is empty (`inc/ce-rankmath.php`).
- Title, meta description, canonical: printed by Rank Math. Empty values are filled through `rank_math/frontend/title`, `rank_math/frontend/description` and `rank_math/frontend/canonical` from `ce_get_meta_description()` and `ce_get_canonical_url()`.
- JSON-LD: Rank Math's graph is the only block. `ce_rankmath_json_ld()` (`rank_math/json_ld`) adds theme nodes (Article, BreadcrumbList, CollectionPage, WebSite, Organization) only when no node of that family is present. `ce_schema_nodes()` builds the nodes; `ce_schema_jsonld()` prints them only without Rank Math.
- robots.txt, sitemaps, Open Graph: Rank Math only.

**Article SEO data.** `inc/articles/seo.json` holds, per article slug, `focus_keyword` (primary keyword first, then related terms already present in the text), `title` and `description` (130 characters including a call to action). `ce_rankmath_fill_empty_fields()` writes them to `rank_math_focus_keyword`, `rank_math_title`, `rank_math_description` only where empty, after every content sync and once whenever seo.json changes. Editor changes in Rank Math are never overwritten.

**Keyword rules.** The primary keyword is a contiguous part of the slug (Rank Math's URL test), starts the SEO title or sits in its first half, appears in the description and within the first 10 percent of the text (the lead figure caption carries the SEO title where the opening paragraph does not), and in the lead image's alt text. Related keywords bring the combined density between 1.0 and 2.5 percent. No keyword is used twice.

**Content analysis.** Rank Math scores editor text. `assets/js/ce-rankmath-admin.js` hooks `rank_math_content` and, while the editor text matches the saved article, gives the analysis the published article body (figures, links, lead image), then calls `rankMathEditor.refresh('content')`. `rank_math/metabox/post/values` declares the theme's table of contents (`assessor.hasTOCPlugin`) for articles with two or more H2 headings.

**Scores (Rank Math 1.0.279, all 131 articles, analysed in the block editor).** With the Content AI module off: 90 to 95. With it on: 85 to 90, because its 5 points are earned only by using Rank Math's paid Content AI. Points still lost by design: content length (articles are 650 to 1,700 words; Rank Math wants 2,500), number in title, and on some titles the sentiment or power-word tests. Two slugs exceed Rank Math's URL length; slugs are not changed.

**Images.** Each article now has three in-body figures (lead, middle, late), plus the featured image: 393 registered images. Rank Math's media test needs four images (counting the featured image) for full marks.

## 28. Content Sync Safety (since 2.6.36)

The orphan pass (trashing articles no longer in the JSON) runs only when the loader reports no errors. Before 2.6.36, one batch failing its checksum removed that batch from the article list and the orphan pass trashed every article in it. After editing any batch file, recompute its checksum in `manifest.json`.

