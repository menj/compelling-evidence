# CE Theme (Compelling Evidence) — WordPress Theme

**Version:** 2.6.33 | **Parent theme:** Twenty Twenty-Five (tested with 1.5) | **Requires:** WordPress 6.7+ (tested to 7.1), PHP 8.0 to 8.4  
**Site:** https://compelling-evidence.com | **Repository:** https://github.com/menj/compelling-evidence | **Author:** [MENJ](https://menj.blog) | **Text domain:** `compelling-evidence`

An Islamic apologetics child theme delivering personalised argumentation journeys across 14 persona paths, 131 articles across 11 investigative categories, a weighted intake quiz, a gamified progress system, a privacy-first analytics layer, and a full engagement suite. Built for a single deployment — not a general-purpose theme.

---

## Documentation Structure

| File | Purpose |
|------|---------|
| `readme.md` | This file — quick start and architecture overview |
| `changelog.md` | Version history and release notes |
| `upgrading.md` | Migration guides for each version |
| `ssot.md` | Single Source of Truth — naming conventions and system design |

---

## Quick Start

```
1. Install Twenty Twenty-Five (parent — must be present, need not be active)
2. Upload zip via Appearance → Themes → Add New → Upload Theme
3. Activate — four auto-run modules fire on first admin page load
4. Settings → Permalinks → Save Changes  (flush rewrite rules)
5. Settings → Reading → set static front page
6. Merge .htaccess-performance into WordPress root .htaccess
```

---

## File Structure

```
compelling-evidence/
├── style.css                         Theme header (Version, Template, Text Domain)
├── functions.php                     CPT, taxonomy, AJAX, hooks, all performance/SEO filters
├── header.php                        Nav (logo, primary menu, random article, progress link)
├── footer.php                        4-column footer grid (brand, topics, navigate, about)
├── front-page.php                    Homepage (hero, quick-access, cards, journey entry, CTA)
├── single-ce_article.php             Article view (breadcrumbs, TOC, related cards, journey widget)
├── single.php                        Standard post view (mirrors article template structure)
├── archive-ce_article.php            /articles/ — 10 topic sections in logical reading order
├── taxonomy-ce_topic.php             /topic/[slug]/ — delegates to archive-ce_article.php
├── page-quiz.php                     /quiz — renders /journeys/quiz.html
├── page-journey.php                  /journey/[slug]/ — renders journey HTML (bypasses wp_head)
├── page-journeys.php                 /journeys — all-paths overview grid
├── page-progress.php                 /my-progress — localStorage-driven progress dashboard
├── page-faq.php                      /faq — accordion UI + FAQPage JSON-LD schema
├── page-glossary.php                 /glossary — 85-term alphabetical glossary
├── page-ask-a-question.php           /ask-a-question — question form with admin email notification
├── search.php                        /?s= — ce_article + post combined results
├── 404.php                           Not found
├── page.php                          Generic page fallback
├── sitemap.xml                       Redirect pointer to /wp-sitemap.xml
├── readme.txt                        WordPress.org standard readme (root only)
│
├── doc/                              Documentation (this folder)
│   ├── README.md                     Theme overview and quick start
│   ├── CHANGELOG.md                  Version history
│   ├── UPGRADING.md                  Upgrade instructions
│   └── SSOT.md                       System design and naming conventions
│
├── inc/                              PHP includes
│   ├── articles-data.php             Articles 1-20 (Does God Exist? + Problem of Evil)
│   ├── articles-data-2.php           Articles 21-41 (Ethics, Science, Quran, Sources)
│   ├── articles-data-3.php           Articles 42-76 (History, Rights, Inner Journey)
│   ├── articles-data-4.php           Articles 77-90 (Bigger Picture)
│   ├── articles-data-next.php        Articles 91-119 (next-question articles + additions)
│   ├── ce-analytics.php              Privacy-first analytics: 12 event types, IP hashing, dashboard
│   ├── ce-content-sync.php           Version-keyed content sync (data files → WP database)
│   ├── ce-db.php                     Custom-table helpers (no dbDelta/upgrade.php dependency)
│   ├── ce-gone.php                   410 Gone for retired spam URL prefixes (Performance tab)
│   ├── ce-404.php                    404 excuses (data, link resolution, script enqueue)
│   ├── ce-icons.php                  Topic line icons for article cards without a featured image
│   ├── ce-parent-compat.php          Template priority over parent block templates; parent version notice
│   ├── ce-fonts.php                  Font registry, role variables, font preload tags (Typography tab)
│   ├── ce-crosslinks.php             Unified crosslink + tooltip filter (197 phrases, 85 glossary terms)
│   ├── ce-engagement.php             Voting, resonance, 9-platform social sharing, reading time
│   ├── ce-glossary.php               85 Islamic/Arabic terms with Arabic script + definitions
│   ├── ce-migration-2-2-0.php        Category restructure (creates 10 terms, remaps articles)
│   ├── ce-migration-2-2-74.php       v2.2.74 data migration
│   ├── ce-secondary-pages.php        Auto-creates About, Editorial, Privacy, Contact, FAQ,
│   │                                 Glossary, Ask a Question pages on activation
│   ├── ce-seo.php                    Meta, canonical, OG/Twitter, JSON-LD (14 schema types)
│   └── class-ce-feed-redirector.php  Feed suppression (301 redirects, noindex, robots.txt, sitemap)
│   ├── ce-theme-options.php          8-tab admin settings UI
│
├── journeys/                         Journey HTML files (self-contained SPAs)
│   ├── quiz.html                     10-question intake quiz
│   ├── [14 persona]-journey.html     Individual journey paths
│   └── ms-MY/                        Malay translations
│
├── assets/                           Static assets
│   ├── css/                          Stylesheets
│   ├── js/                           JavaScript
│   ├── fonts/                        23 self-hosted woff2 files, licenses/ (OFL, Apache)
│   └── images/                       Theme images
│
└── languages/                        Translations
    ├── compelling-evidence.pot       Translation template
    ├── ms_MY.po                      Malay source
    └── ms_MY.mo                      Malay compiled binary
```

---

## Architecture

### Custom Post Type: `ce_article`

Registered in `functions.php`. Key properties:

- `public`: true
- `has_archive`: true (archive at `/articles/`)
- `rewrite`: `['slug' => 'articles']`
- `supports`: title, editor, excerpt, thumbnail, page-attributes
- `menu_order` used for article ordering within topics

### Custom Taxonomy: `ce_topic`

- `hierarchical`: false
- Attached to: `ce_article` and `post`
- 11 terms in canonical order (see Article Categories below)
- Term slugs registered at migration time — do not rename

### Auto-Run Modules

Five modules fire on `admin_init`, each guarded by an option flag:

| File | Option flag | Fires when |
|---|---|---|
| `ce-migration-2-2-0.php` | `ce_category_migration_2_2_0` | Flag absent |
| `ce-content-sync.php` | `ce_content_sync_{version}` | Version changes |
| `ce-secondary-pages.php` | `ce_secondary_pages_created` | Flag absent |
| `ce-secondary-pages.php` | `ce_v2_pages_created` | Flag absent |
| `ce-secondary-pages.php` | `ce_about_v2_updated` | Flag absent (added v2.6.10) |

Content sync key is derived directly from `wp_get_theme()->get('Version')`. Every version bump triggers a fresh sync. Article content in the database is authoritative for display but treated as a cache — the PHP data files are the source of truth.

### Article Data Format

Five PHP files in `inc/`, each returning an array from a getter function. All content uses PHP nowdoc syntax to avoid escaping issues:

```php
[
    'slug'    => 'article-slug',
    'title'   => 'Article Title',
    'topic'   => 'Does God Exist?',
    'order'   => 1,
    'excerpt' => 'Meta description and card text.',
    'content' => <<<'CE_ARTICLE_1'
<p class="article-lead">...</p>
<h2>Section heading</h2>
<p>Body text.</p>
CE_ARTICLE_1,
]
```

Single quotes in excerpts must be escaped as `\'`. Article titles with apostrophes use PHP mixed-quote syntax: `'title' => "It's a title"`.

---

## Article Categories (11 topics, canonical order)

| # | Taxonomy term | Slug | Articles |
|---|---|---|---|
| 1 | Does God Exist? | `does-god-exist` | 12 |
| 2 | The Problem of Evil | `the-problem-of-evil` | 9 |
| 3 | Ethics Without God? | `ethics-without-god` | 5 |
| 4 | Science & Evidence | `science-evidence` | 11 |
| 5 | The Quran & Its Sources | `quran-and-sources` | 24 |
| 6 | History, Context & Comparison | `history-context-comparison` | 12 |
| 7 | Divine Justice & Fairness | `divine-justice-fairness` | 7 |
| 8 | Islamic Practice & Ritual | `islamic-practice-ritual` | 8 |
| 9 | Rights & Freedom | `rights-freedom` | 15 |
| 10 | The Inner Journey | `the-inner-journey` | 17 |
| 11 | Revelation & Meaning | `revelation-meaning` | 11 |

**Total: 131 articles** (v2.6.22)

---

## Persona Paths (14)

| File slug | Display name | Volume |
|---|---|---|
| `new-atheist` | The New Atheist | I |
| `agnostic` | The Agnostic | I |
| `secular-humanist` | The Secular Humanist | I |
| `antitheist` | The Antitheist | I |
| `materialist` | The Materialist | I |
| `muslim-doubts` | The Questioning Muslim | I |
| `apatheist` | The Apatheist | I |
| `deist` | The Deist | I |
| `scientist` | The Scientist | I |
| `classical-atheist` | The Classical Atheist | I |
| `ex-believer` | The Ex-Believer | I |
| `spiritual-seeker` | The Spiritual Seeker | I |
| `freethinker` | The Freethinker | I |
| `true-muslim` | The Committed Muslim | I (8-screen variant) |

---

## Performance Architecture

### Asset Loading Strategy

| Asset | Condition | Size |
|---|---|---|
| `main.css` | All pages | 60.5 KiB |
| `templates.css` | Non-front-page only | 42.4 KiB |
| `ce-engagement.css` | `is_singular('ce_article')` only | — |
| `glossary-tooltip.css` | `is_singular('ce_article')` only | — |
| `print.css` | All pages, media="print" | 5 KiB |

### Font Preloading

On front page only, two Arabic fonts are preloaded in `header.php`:
- `uthmani-quran.woff2` (105 KiB) — LCP element font
- `ce-hadith-400.woff2` (69 KiB)

### Cache Strategy

`Cache-Control: public, max-age=31536000, immutable` set via `send_headers` and `wp_headers` hooks for fonts and versioned CSS/JS. 30-day cache for images.

---

## Design System

### Colour Palette (CSS custom properties in `main.css :root`)

```css
--dark:    #0d0820   /* Deepest background */
--dark-2:  #1a0f38   /* Mid background */
--dark-3:  #120b2e   /* Card/surface background */
--light:   #f2eeff   /* Primary text */
--teal:    #0cd4e0   /* Accent — links, highlights */
--gold:    #f5c518   /* Conclusion screens only */
--purple:  #6b2fa0   /* Accent — secondary */
--muted:   rgba(245,240,255,0.68)  /* Secondary text */
```

All 7 are overridable via Theme Options > Colors (injected as inline CSS at `wp_head`).

---

## Theme Options Reference

**Appearance > CE Theme Options** — 8 tabs:

| Tab | Settings |
|---|---|
| General | Hero title, subtitle, Arabic verse, translation, footer text |
| Colors | 7 color pickers (accent, teal, purple, gold, backgrounds) |
| Typography | Heading font, reading font, label and eyebrow font, font scale |
| Links & Tooltips | Crosslinks and glossary tooltips configuration |
| Quiz & Journeys | Quiz enable/disable, max selections, progress badges |
| Engagement | Voting, resonance, crosslinks, search toggles |
| Analytics | Tracking enable/disable, retention days |
| Performance | Parallax, font preloading, minification |

---

## Localisation

**Text domain:** `compelling-evidence`

Shipped translations: `ms_MY` (Malay). Compile with:
```
msgfmt languages/ms_MY.po -o languages/ms_MY.mo
```

Journey translations: place translated HTML at `/journeys/ms-MY/[slug]-journey.html`. `page-journey.php` and `page-quiz.php` handle locale detection and fallback to English.

---

## Documentation Files

All theme documentation is located in `/doc/`:

- `doc/readme.md` — Theme overview and quick start
- `doc/changelog.md` — Version history and release notes
- `doc/upgrading.md` — This file (migration instructions)
- `doc/ssot.md` — Single Source of Truth (naming conventions, system design)

The `readme.txt` file remains in the theme root for WordPress.org compliance.

---

*Last updated: September 2026 (v2.6.33)*
