# Compelling Evidence — WordPress Theme

An evidence-based inquiry site delivering personalised argumentation journeys across 14 persona paths, 100 articles across 10 investigative categories, a gamified quiz and progress system, and a full engagement layer. Built as a child theme of Twenty Twenty-Five.

---

## Quick Start

**Requires:** WordPress 6.4+, PHP 8.0+, Twenty Twenty-Five parent theme

```
1. Upload compelling-evidence.zip via Appearance → Themes → Add New → Upload
2. Activate the theme
3. Visit any admin page (auto-runs: category migration, content sync, page creation)
4. Settings → Permalinks → Save Changes (flush rewrite rules)
5. Settings → Reading → set static front page
```

---

## Architecture

### The Journey System

14 persona paths, each a self-contained single-page HTML application in `/journeys/`. Each contains:

- **10 screens** — Horizon + 7 argument chapters + Resonance + Transmission + Conclusion
- **6 reflection choices per chapter** — 744 total across all journeys
- **Volume I → II bridge** — all 13 standard journeys end with a Volume II preview
- **localStorage progress** — saves current screen, resumes on return
- **Resume banner** — appears if reader returns mid-journey
- **Secondary resonance widget** — sidebar suggests complementary paths from quiz results

The Committed Muslim path (14th) has a separate 8-screen structure (Foundation → Mission + Conclusion) and does not participate in the Volume pipeline.

**Journey page styling:**
- Self-contained HTML files served via `page-journey.php` (bypass WordPress rendering — no `wp_head` fires)
- Layout: 1120px max-width, 1fr + 280px sidebar grid, collapses to single-column 760px on mobile
- Auto Justify Content plugin integration: `page-journey.php` reads AJC settings from the database and injects matching CSS into the journey HTML `<head>` before echo. Targets `.reading p` and `.reading li`. Excludes citation blocks.
- Drop cap: one per chapter screen, targeting `.chapter-num + .reading > p:first-child::first-letter`
- Quran/hadith citations in articles use `!important` center alignment to override AJC justify

### The Quiz

`/journeys/quiz.html` — 10-question intake quiz with weighted scoring across all 14 personas. Allows up to 3 selections per question, with 3-choice calibrated scoring, dominance weighting, conflict dampening, and near-tie overlap handling. Produces primary path + secondary resonances. Saves to `localStorage`.

**6-layer scoring algorithm:**
1. Base scoring — 70 answer→persona weight mappings, divided by selection count
2. Question weights — Q6 (faith status) heaviest at 1.9×, Q7 (doubt reaction) lowest at 0.8×
3. Question dominance — bonus for the persona that "wins" each question by margin
4. Signal boosts — pattern recognition for key identity signals and convergence combos
5. Persona separation — hard-coded rules to pull apart overlapping personas (freethinker/new-atheist, deist/spiritual-seeker, apatheist/secular-humanist, etc.)
6. Conflict adjustments — suppresses impossible combinations (e.g. practising Muslim + antitheist)

### Article Ecosystem

100 articles across 10 categories, stored in 5 PHP data files using nowdoc syntax. Auto-published as `ce_article` custom post type with `ce_topic` taxonomy.

| # | Category | Count | Description |
|---|---|---|---|
| 1 | Does God Exist? | 14 | Cosmological, fine-tuning, ontological, nihilism — examined from the ground up |
| 2 | The Problem of Evil | 8 | The strongest objection to theism, taken at full strength |
| 3 | Ethics Without God? | 5 | Where moral facts come from and whether they need a foundation |
| 4 | Science & Evidence | 11 | Evolution, neuroscience, cosmology, the unseen — what the evidence shows |
| 5 | Examining the Quran | 10 | Preservation, variant readings, literary claims — source criticism |
| 6 | Examining the Sources | 8 | Hadith methodology, prophetic biography, Muhammad's credibility — scrutinised |
| 7 | History & Context | 7 | Institutions, civilisations, and the record |
| 8 | Rights & Freedom | 9 | Apostasy, gender, sexuality, law |
| 9 | The Inner Journey | 17 | Understanding Doubt (6), The Emotional Cost (5), What Happens Next (5), Return (1) |
| 10 | The Bigger Picture | 11 | Purpose, meaning, eschatology, spirituality, competing claims, conversion patterns |

### Auto-Run Modules

Four modules fire on `admin_init`, each with its own option flag (runs once per condition):

| Module | Flag | Action |
|---|---|---|
| `ce-migration-2-2-0.php` | `ce_category_migration_2_2_0` | Creates 10 taxonomy terms, remaps articles, deletes old terms |
| `ce-content-sync.php` | `ce_content_sync_{version}` | Syncs article content from data files to database. Key derived from theme version — every version bump triggers a fresh sync automatically |
| `ce-secondary-pages.php` | `ce_secondary_pages_created` | Creates About, Editorial, Privacy, Contact, Ask a Question pages |
| `ce-secondary-pages.php` | `ce_v2_pages_created` | Creates FAQ and Glossary pages (added v2.2.51) |

### Article Enhancements

- **Auto Table of Contents**: Sidebar widget auto-generated from h2 headings via vanilla JS. Shows only when article has 2+ sections. Numbered entries with scroll-spy active highlighting and smooth scroll on click.
- **Breadcrumbs**: Home › Articles › Topic › Title. Styled inline, matches existing BreadcrumbList JSON-LD schema in `ce-seo.php`.
- **Related Articles**: 3-card grid below article content showing same-topic articles with topic label, title, and reading time. Responsive grid.
- **Cross-links**: `ce-crosslinks.php` — 161 phrase→article mappings, first-mention linking, max 5 per article.

### Engagement System

9-platform social sharing, up/down voting (IP-hash dedup), resonance feedback, Most Resonant widget, reading time + progress bar on all articles.

### SEO & Schema

`ce-seo.php` generates: Article schema, BreadcrumbList, WebSite, Organization (14 types total), 9 Open Graph properties. Print CSS included. `page-faq.php` embeds its own FAQPage JSON-LD schema (18 Q&As) for Google rich results.

---

## Persona Paths

| Slug | Display Name |
|---|---|
| `new-atheist` | The New Atheist |
| `agnostic` | The Agnostic |
| `secular-humanist` | The Secular Humanist |
| `antitheist` | The Antitheist |
| `materialist` | The Materialist |
| `muslim-doubts` | The Questioning Muslim |
| `apatheist` | The Apatheist |
| `deist` | The Deist |
| `scientist` | The Scientist |
| `classical-atheist` | The Classical Atheist |
| `ex-believer` | The Ex-Believer |
| `spiritual-seeker` | The Spiritual Seeker |
| `freethinker` | The Freethinker |
| `true-muslim` | The Committed Muslim |

---

## File Structure

```
compelling-evidence/
├── style.css                        Theme declaration
├── functions.php                    CPT, taxonomy, AJAX, page creation, rewrites
├── header.php / footer.php          Site chrome (nav: Journeys, Articles, Ask a Question, FAQ)
├── front-page.php                   Homepage
├── archive-ce_article.php           /articles/ — accordion topic sections
├── single-ce_article.php            Article view + breadcrumbs, TOC, related cards, journey widget
├── page-quiz.php                    Quiz renderer
├── page-journey.php                 Journey renderer
├── page-journeys.php                All-paths overview
├── page-progress.php                My Progress dashboard
├── page-ask-a-question.php          Question form
├── page-faq.php                     FAQ — accordion UI + FAQPage JSON-LD schema
├── page-glossary.php                Glossary — 23 Islamic terms with Arabic, alphabetical nav
├── search.php                       Search results
├── 404.php                          Not found
├── inc/
│   ├── articles-data[*].php         5 data files (nowdoc syntax), 100 articles
│   ├── ce-content-sync.php          Database content sync (key auto-derived from theme version)
│   ├── ce-crosslinks.php            161 phrase→article auto-linking mappings
│   ├── ce-analytics.php             Analytics system (database, AJAX, dashboard)
│   ├── ce-migration-2-2-0.php       Category restructure migration
│   ├── ce-secondary-pages.php       Auto-create secondary pages (About, Editorial, Privacy, Contact, FAQ, Glossary)
│   ├── ce-engagement.php            Voting, sharing, resonance
│   └── ce-seo.php                   Schema + OG tags + BreadcrumbList
├── journeys/
│   ├── quiz.html                    10-question intake quiz
│   └── [slug]-journey.html × 14     Self-contained journey applications
├── assets/
│   ├── css/main.css                 Design system + @font-face (17 self-hosted fonts)
│   ├── css/templates.css            Extracted from inline <style> blocks (1,290 lines, cacheable)
│   ├── css/journey-base.css         Shared journey styles (152 lines, injected by page-journey.php)
│   ├── css/ce-engagement.css        Engagement styles
│   ├── css/print.css                Print stylesheet
│   ├── js/main.js + ce-engagement.js
│   ├── js/ce-analytics.js           Frontend event tracker (privacy-first)
│   └── fonts/                       17 self-hosted font files (woff2 + otf)
└── languages/
    └── ms_MY.po / ms_MY.mo          Malay translation
```

---

## Design System

### Colours
```
Primary:   #0d0820, #1a0f38, #120b2e (deep purples)
Text:      #f2eeff (white), rgba(242,238,255,0.55) (muted)
Accent:    #0cd4e0 (teal), #f5c518 (gold — conclusions only)
Logo:      #e05252 (red) + #f2eeff (white)
```

### Typography

Headings: Playfair Display. Body: DM Sans. Reading: Cormorant Garamond. Quranic Arabic: Uthmani Quran → Amiri. Hadith Arabic: Amiri.

Two-layer font system: self-hosted `@font-face` for Uthmani woff2 (Quran only) with Google Fonts Amiri fallback. See `assets/fonts/README.md`.

---

## Progress Storage

| User state | Storage |
|---|---|
| Guest | `localStorage` only |
| Logged in | `localStorage` + WP user meta (AJAX sync) |

---

## AJAX Endpoints

All require `ce_nonce`. Endpoints: `ce_search`, `ce_random`, `ce_save_progress`, `ce_get_progress`, `ce_save_quiz`.

---

## Localisation

Translation-ready (text domain: `compelling-evidence`). Ships with ms-MY (Malay). Journey content: place translated HTML at `/journeys/ms-MY/[slug]-journey.html`.

---

## Three-Volume Architecture

| Volume | Question | Status |
|---|---|---|
| I | Does God exist? | **COMPLETE** (v2.2.19) |
| II | Which God? One or many? | PLANNED |
| III | Why Islam? Why not others? | PLANNED |

See `UPGRADING.md` for the full roadmap and development plan.
