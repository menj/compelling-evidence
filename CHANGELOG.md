## 2.2.77
### Unified crosslink + tooltip engine with ground rules
Rewrote ce-crosslinks.php (428 lines) as a single-pass content filter that handles both internal crosslinks and glossary tooltips. Replaces the two separate filters that ran at the same priority and could clash.

**Ground rules (hardcoded, always enforced):**
1. Same keyword linked/tooltipped at most ONCE per article (`$used_keywords` tracking).
2. NEVER inside heading text h1-h6 (`$in_heading` flag on tag open/close).
3. A crosslink and a tooltip NEVER appear in the same paragraph (`$para_has_crosslink` / `$para_has_tooltip` flags).
4. Every article gets at least 3 internal crosslinks. If the first pass finds fewer, a second pass runs with relaxed paragraph rules (but still respects Rules 1 and 2).
5. Per-post override: meta box in article editor sidebar with checkboxes to disable crosslinks and/or tooltips for individual articles.
6. Per-category disable: comma-separated topic slugs in Theme Options.

**Processing order:** Content split into parts by HTML tags. For each text node: check if inside heading or protected zone (links, citations). If safe, try crosslinks first. If crosslink fires in a paragraph, no tooltip in that paragraph. If no crosslink, try tooltip. One insertion per text node maximum.

**Per-post meta box:** "Links & Tooltips" sidebar box in article editor with two checkboxes: disable crosslinks, disable tooltips. Saved as `_ce_disable_crosslinks` and `_ce_disable_tooltips` post meta.

### Theme Options — Links & Tooltips tab (8th tab)
13 configurable fields across two sections:

**Internal Cross-Links section:**
- Enable/disable crosslinks globally
- Maximum crosslinks per article (1-15, default 5)
- Minimum crosslinks per article (0-10, default 3)
- Link style: underline / dotted / bold / color-only
- Link color (color picker, default #e8455a)
- rel="nofollow" toggle
- Disabled categories (comma-separated topic slugs)

**Glossary Tooltips section:**
- Enable/disable tooltips globally
- Maximum tooltips per article (1-20, default 8)
- Link style: dashed / dotted / solid / color-only
- Link color (color picker, default #0cd4e0)
- Show Arabic script in tooltip toggle
- Trigger method: hover or click

**Ground rules display:** Read-only info panel showing the 5 enforced rules.

**CSS injection:** Crosslink and tooltip colors + styles from Theme Options are injected via `wp_head` as inline CSS overrides, alongside the existing color/typography variable injection.

## 2.2.76
### Amiri font removed completely
- Deleted all 3 Amiri woff2 files (amiri-400, amiri-700, amiri-400-italic). These were Latin-subset only and useless for Arabic text.
- Removed all Amiri @font-face declarations and font-family references across main.css, templates.css, print.css, glossary-tooltip.css, journey-base.css, quiz.html, and muslim-doubts-journey.html.
- CE Hadith (Noto Naskh Arabic) is now the sole Arabic font for non-Quranic text. Full Arabic coverage including ornate brackets.
- Font inventory: 16 woff2 files across 5 families (Playfair Display, DM Sans, Cormorant Garamond, CE Hadith, Uthmani Quran).

### Verse end mark — font-rendered ornate brackets
- Front page verse mark now uses ornate brackets rendered by CE Hadith font: `﴿١٣﴾` instead of CSS circle hack or font-dependent U+06DD character.
- CSS simplified to inline display with CE Hadith font-family.

### Theme Options (Appearance > CE Theme Options)
- New tabbed admin settings page with 7 tabs: General, Colors, Typography, Quiz & Journeys, Engagement, Analytics, Performance.
- All settings save per-tab with nonce verification and proper sanitization.
- **General tab:** Hero title, subtitle, Arabic verse, verse number, verse translation, verse reference, footer text.
- **Colors tab:** 7 configurable colors (primary accent, secondary accent, teal, purple, gold, background deep, background darkest). Changes inject as CSS custom properties via wp_head, overriding main.css :root values. Live hex preview in admin.
- **Typography tab:** Heading font selector (Playfair/Cormorant), reading body font selector (Cormorant/Playfair/DM Sans), font size scale (90%-120%).
- **Quiz & Journeys tab:** Quiz enable/disable, max selections per question, journey progress badges toggle, cards per row.
- **Engagement tab:** Toggle voting, resonance, crosslinks, search. Articles per page setting.
- **Analytics tab:** Toggle tracking, scroll depth, search query logging. Data retention period (30d-1yr). Auto-purge via daily WP cron.
- **Performance tab:** Toggle parallax, font preloading, inline script minification.
- Clean minimalist design with dashicons tab icons, section headers, inline descriptions.

## 2.2.75
### Quranic font fixes — verse marks and ornate brackets
**Root cause identified and fixed:**
- Old Uthmani Quran font had only 271 glyphs (U+0600-06FF). The unicode-range incorrectly claimed U+FB50-FDFF and U+FE70-FEFF (zero glyphs in those ranges). Browsers assigned Uthmani for ornate brackets ﴿﴾ (U+FD3E-FD3F), found no glyph, and rendered nothing — breaking every Quranic citation in all articles.
- Amiri font files were Latin-subset only (~20KB each). When characters fell through from Uthmani, Amiri had no Arabic glyphs either. Entire fallback chain was broken for Arabic presentation forms.

**Fixes applied:**
1. Replaced Uthmani font with KFGQPC HAFS Uthmanic Script — 608 glyphs (vs 271), including 332 Arabic ligatures in U+FB50-FDFF. Converted TTF→woff2 (297KB→107KB).
2. Updated unicode-range to U+0600-06FF, U+FB50-FDFF (matches actual glyph coverage). Ornate brackets ﴿﴾ (U+FD3E-FD3F) still not in font — they correctly fall through to system Arabic fonts.
3. Removed OTF fallback — woff2 is now the only format (all modern browsers support it). Old 246KB OTF deleted.
4. Added system Arabic fonts to fallback stack: 'Noto Naskh Arabic' (Android/Linux), 'Geeza Pro' (macOS/iOS) after Amiri in font-family chain. Ornate brackets now render on all major platforms.
5. Front page verse end mark: replaced font-dependent ۝١٣ (U+06DD) with CSS-rendered circle + number. Works regardless of font loading.

**Note:** Amiri woff2 files are still Latin-subset only. For full Arabic fallback, re-download Amiri with arabic+latin subsets from google-webfonts-helper. Current system font fallbacks handle most cases.

## 2.2.77
### Unified crosslink + tooltip engine with ground rules
Rewrote ce-crosslinks.php (428 lines) as a single-pass content filter that handles both internal crosslinks and glossary tooltips. Replaces the two separate filters that ran at the same priority and could clash.

**Ground rules (hardcoded, always enforced):**
1. Same keyword linked/tooltipped at most ONCE per article (`$used_keywords` tracking).
2. NEVER inside heading text h1-h6 (`$in_heading` flag on tag open/close).
3. A crosslink and a tooltip NEVER appear in the same paragraph (`$para_has_crosslink` / `$para_has_tooltip` flags).
4. Every article gets at least 3 internal crosslinks. If the first pass finds fewer, a second pass runs with relaxed paragraph rules (but still respects Rules 1 and 2).
5. Per-post override: meta box in article editor sidebar with checkboxes to disable crosslinks and/or tooltips for individual articles.
6. Per-category disable: comma-separated topic slugs in Theme Options.

**Processing order:** Content split into parts by HTML tags. For each text node: check if inside heading or protected zone (links, citations). If safe, try crosslinks first. If crosslink fires in a paragraph, no tooltip in that paragraph. If no crosslink, try tooltip. One insertion per text node maximum.

**Per-post meta box:** "Links & Tooltips" sidebar box in article editor with two checkboxes: disable crosslinks, disable tooltips. Saved as `_ce_disable_crosslinks` and `_ce_disable_tooltips` post meta.

### Theme Options — Links & Tooltips tab (8th tab)
13 configurable fields across two sections:

**Internal Cross-Links section:**
- Enable/disable crosslinks globally
- Maximum crosslinks per article (1-15, default 5)
- Minimum crosslinks per article (0-10, default 3)
- Link style: underline / dotted / bold / color-only
- Link color (color picker, default #e8455a)
- rel="nofollow" toggle
- Disabled categories (comma-separated topic slugs)

**Glossary Tooltips section:**
- Enable/disable tooltips globally
- Maximum tooltips per article (1-20, default 8)
- Link style: dashed / dotted / solid / color-only
- Link color (color picker, default #0cd4e0)
- Show Arabic script in tooltip toggle
- Trigger method: hover or click

**Ground rules display:** Read-only info panel showing the 5 enforced rules.

**CSS injection:** Crosslink and tooltip colors + styles from Theme Options are injected via `wp_head` as inline CSS overrides, alongside the existing color/typography variable injection.

## 2.2.76
### Amiri font removed completely
- Deleted all 3 Amiri woff2 files (amiri-400, amiri-700, amiri-400-italic). These were Latin-subset only and useless for Arabic text.
- Removed all Amiri @font-face declarations and font-family references across main.css, templates.css, print.css, glossary-tooltip.css, journey-base.css, quiz.html, and muslim-doubts-journey.html.
- CE Hadith (Noto Naskh Arabic) is now the sole Arabic font for non-Quranic text. Full Arabic coverage including ornate brackets.
- Font inventory: 16 woff2 files across 5 families (Playfair Display, DM Sans, Cormorant Garamond, CE Hadith, Uthmani Quran).

### Verse end mark — font-rendered ornate brackets
- Front page verse mark now uses ornate brackets rendered by CE Hadith font: `﴿١٣﴾` instead of CSS circle hack or font-dependent U+06DD character.
- CSS simplified to inline display with CE Hadith font-family.

### Theme Options (Appearance > CE Theme Options)
- New tabbed admin settings page with 7 tabs: General, Colors, Typography, Quiz & Journeys, Engagement, Analytics, Performance.
- All settings save per-tab with nonce verification and proper sanitization.
- **General tab:** Hero title, subtitle, Arabic verse, verse number, verse translation, verse reference, footer text.
- **Colors tab:** 7 configurable colors (primary accent, secondary accent, teal, purple, gold, background deep, background darkest). Changes inject as CSS custom properties via wp_head, overriding main.css :root values. Live hex preview in admin.
- **Typography tab:** Heading font selector (Playfair/Cormorant), reading body font selector (Cormorant/Playfair/DM Sans), font size scale (90%-120%).
- **Quiz & Journeys tab:** Quiz enable/disable, max selections per question, journey progress badges toggle, cards per row.
- **Engagement tab:** Toggle voting, resonance, crosslinks, search. Articles per page setting.
- **Analytics tab:** Toggle tracking, scroll depth, search query logging. Data retention period (30d-1yr). Auto-purge via daily WP cron.
- **Performance tab:** Toggle parallax, font preloading, inline script minification.
- Clean minimalist design with dashicons tab icons, section headers, inline descriptions.

## 2.2.75
### Fix Quranic verse rendering + new CE Hadith font
Root cause: Uthmani unicode-range claimed U+FB50-FDFF but only has glyphs in U+0600-06FF. Ornate brackets rendered as invisible. Amiri fonts were Latin-subset only, so the entire Arabic fallback chain was broken.

- Added CE Hadith font (Noto Naskh Arabic): ce-hadith-400.woff2 (71KB), ce-hadith-700.woff2 (72KB). 1,551 glyphs with full Arabic coverage including ornate brackets.
- Narrowed Uthmani unicode-range to U+0600-06FF only.
- Updated font stacks: Quranic text uses Uthmani then CE Hadith; hadith text uses CE Hadith directly.
- Front page verse end mark: replaced font-dependent Unicode character with CSS-styled circle + number.
- Font inventory: 19 woff2 files, ~600KB total.

## 2.2.74
### Bug Fixes
- **Journey pages not wide width** — Two-part fix for journey pages rendering at Twenty Twenty-Five's default narrow content width instead of the intended 1060px layout.
  - Added `theme.json` to the child theme overriding TT25's inherited `contentSize` (≈650px) with `1060px` content width and `1280px` wide width. Without this, any page falling through to TT25's block template system was constrained to TT25's defaults.
  - Fixed the parent `/journey/` page created by `ce_create_journey_pages()`: it was being created with no template assigned, meaning WordPress could intercept `/journey/*` requests via TT25's block template before child page routing resolved. Now assigns `page-journey.php` to the parent page and adds redirect content, matching how all 14 child journey pages are handled.

## 2.2.49
- Infused Faruqian philosophical concepts into 50 Islamic articles (45 that were at 0% + 5 partial). Each article received one targeted paragraph placing its argument within the Islamic philosophical framework. No explicit attribution — concepts presented as Islamic principles. Coverage after infusion:
  - tawhid: 31/93 articles (was 7)
  - khalifah: 25/93 (was 14)
  - unity-of-truth: 22/93 (was 4)
  - fitrah: 18/93 (was 8)
  - iman: 17/93 (was 6)
  - normativeness: 13/93 (was 6)
  - actionalism: 12/93 (was 5)
  - sunan: 11/93 (was 7)
  - amanah: 5/93 (was 3)
- 31 general theism articles left untouched (cosmological, fine-tuning, consciousness, morality arguments that stand on their own merits without Islamic-specific framing).
- Zero explicit al-Faruqi mentions across all content.

## 2.2.49
- Full Islamic philosophy infusion across 53 articles. Added one targeted paragraph per article containing only the concepts relevant to that article's topic. No al-Faruqi attribution — concepts expressed as Islamic philosophical principles.
- Concepts deployed by category:
  - Examining the Quran (8 articles): unity-of-truth, tawhid, iman, sunan
  - Examining the Sources (6): tawhid, unity-of-truth, iman, fitrah
  - Rights & Freedom (8+): fitrah, khalifah, actionalism, tawhid
  - The Inner Journey (7+): fitrah, khalifah, iman, normativeness
  - History & Context (4): tawhid, sunan, unity-of-truth, khalifah
  - The Bigger Picture (5): tawhid, khalifah, amanah, fitrah, normativeness
  - The Problem of Evil (3): khalifah, amanah, tawhid, actionalism
  - Science (2): sunan, unity-of-truth
- 31 general theism articles left untouched (cosmological, fine-tuning, consciousness, morality, etc.)
- Zero explicit al-Faruqi mentions across all 93 articles confirmed.

## 2.2.73
### Bug Fixes
- **Journey persona answers broken** — Fixed a JavaScript scoping bug affecting all 14 journey HTML files. `PATH` was declared with `var` inside an IIFE in the `ce-progress` script block, making it invisible to the `goTo()` function in the outer script. When the analytics tracker (`ceTrack`) was active, clicking any reflection choice or Continue button triggered a `ReferenceError: PATH is not defined`, halting execution before the screen transition could run. Fixed by injecting `window.PATH = '<persona-key>';` as a global inline script before the first script block in each journey file.
- **Theme installation failed on Windows-packaged zips** — WordPress's theme installer requires forward-slash path separators in zip archives. Zips built on Windows use backslashes, causing the installer to fail with "The theme is missing the style.css stylesheet" even though the file was present and valid. Repackaged all releases using Unix-style paths.

## 2.2.72
### CSS Lint Fixes — Journey Files
- Fixed all CSS lint errors in `new-atheist-journey.html`: added missing closing braces (`}`) across 50+ CSS rules including `.reading-h`, `.pull-quote`, `.note`, `.art-hero`, `.art-meta`, `.choice`, reflection section, sidebar widgets, and media queries.
- Verified CSS structure in 14 other journey files: all properly closed rulesets, no empty rulesets, valid media query syntax.
- No functional changes — purely syntax cleanup for standards compliance.

## 2.2.71
### Custom Analytics System
Privacy-first, server-side analytics built into the theme. Zero cookies, zero PII, zero third-party services.

**Backend (inc/ce-analytics.php, 459 lines):**
- Custom database table (ce_analytics) with event_type, event_data (JSON), visitor_hash, session_id, page_url, device_type, created_at.
- AJAX endpoint (ce_analytics) with nonce verification, event type whitelist, rate limiting (30/min), and data sanitization.
- Query helpers: count by type, unique visitors, top items by JSON key, daily counts, persona distribution, device breakdown, scroll depth, quiz funnel, journey funnel, search queries.
- Admin dashboard page (WP Admin → CE Analytics) with: KPI cards (visitors, pageviews, quiz/journey starts/completions/rates), persona distribution table, top articles, top search queries, device breakdown bars, scroll depth chart, 7/30/90/365 day range filter, data purge tool.

**Frontend tracker (assets/js/ce-analytics.js, 119 lines):**
- Enqueued on all WordPress pages via functions.php.
- Session ID in sessionStorage (dies on tab close, no persistence across sessions).
- Automatic tracking: pageview (with page type detection), article_view, article_scroll (25/50/75/100% depth via IntersectionObserver), article_complete (95%+), search_query (debounced 1.5s).
- Scroll depth throttled to max 1 check per 2 seconds.
- Uses sendBeacon for unload events (quiz_abandon, article_scroll).

**Quiz tracking (injected via page-quiz.php):**
- quiz_start (on Begin click), quiz_answer (question + answer on every selection), quiz_complete (persona assigned), quiz_abandon (last question + answer count, via beforeunload + sendBeacon).
- ceTrack function injected before quiz JS so all calls resolve.

**Journey tracking (injected via page-journey.php):**
- journey_start (persona, on page load), journey_screen (persona + screen ID, on every goTo call), journey_complete (persona, when conclusion screen reached).
- ceTrack function injected into standalone HTML via page-journey.php.

**12 event types tracked:**
pageview, article_view, article_scroll, article_complete, quiz_start, quiz_answer, quiz_complete, quiz_abandon, journey_start, journey_screen, journey_complete, search_query.

**Content strategy data available:**
- Which personas are most common → allocate content effort
- Quiz drop-off points → identify confusing questions
- Journey completion rates per persona → which journeys need work
- Article scroll depth → which articles lose readers
- Top search queries → content gaps (queries with no good results)
- Device breakdown → mobile optimization priority

## 2.2.70
### Extract inline CSS from templates (#5 from audit)
- Extracted 1,290 lines of inline CSS from 8 PHP templates into new cacheable assets/css/templates.css. Enqueued as ce-templates in functions.php. All 8 templates now have zero <style> blocks.

### Fix IP dedup behind CDN proxies (#6 from audit)
- ce_get_ip_hash() now resolves real client IP via CF-Connecting-IP → X-Real-IP → X-Forwarded-For → REMOTE_ADDR. Validates with filter_var(FILTER_VALIDATE_IP) to prevent header spoofing.

### Journey shared CSS extraction (#7 from audit)
- Extracted 152 common CSS lines (~14.6KB) into assets/css/journey-base.css. Shared across all 14 journey files.
- page-journey.php injects <link rel="stylesheet"> for journey-base.css before </head>, versioned with theme version for cache busting.
- Each journey file reduced by ~15KB (total savings: ~220KB across 14 files).
- Persona-specific styles remain inline in each journey file (61-378 lines per file for unique content block styles).

### Rate limiting on AJAX endpoints (#8 from audit)
- New ce_rate_limited() function: transient-based rate limiter using IP hash + action name key.
- Applied to: ce_ajax_vote (15/min), ce_ajax_resonance (15/min), ce_ajax_search (20/min).
- Returns 'Too many requests' JSON error when limit exceeded. Uses WordPress transients with auto-expiry.

## 2.2.69
### Extract inline CSS (#5 from audit)
- Extracted 1,290 lines of inline CSS from 8 templates into new cacheable assets/css/templates.css:
  - single-ce_article.php (348 lines), search.php (240 lines), page-journeys.php (253 lines), 404.php (122 lines), page-faq.php (121 lines), page-glossary.php (73 lines), page-progress.php (71 lines), archive-ce_article.php (62 lines)
- templates.css enqueued in functions.php as ce-templates with ce-main dependency.
- All 8 template files now have zero <style> blocks — CSS is browser-cached across page loads instead of re-downloaded inline with every HTML response.

### Fix IP dedup behind CDN proxies (#6 from audit)
- ce_get_ip_hash() now checks proxy headers in priority order: CF-Connecting-IP (Cloudflare) → X-Real-IP (nginx) → X-Forwarded-For (generic proxy, first IP only) → REMOTE_ADDR (direct connection).
- Added filter_var() validation on resolved IP to prevent header injection attacks.
- Behind Cloudflare or any reverse proxy, votes and engagement actions now correctly identify individual users instead of sharing one IP hash across all proxy clients.

## 2.2.68
### Fonts bundled in theme
- All 17 font files now included in assets/fonts/ (no download script needed):
  - Playfair Display: 400, 700, 900, 400i (woff2, ~22KB each)
  - DM Sans: 300, 400, 500, 600 (woff2, ~14KB each)
  - Cormorant Garamond: 300, 400, 600, 300i, 400i (woff2, ~23KB each)
  - Amiri: 400, 700, 400i (woff2, ~20KB each)
  - Uthmani Quran: 400 (OTF, 246KB — woff2 conversion requires brotli, OTF used as fallback format)
- Total font directory: 564KB. Uthmani @font-face updated with OTF fallback: src: url(woff2), url(otf).
- Download script retained for reference but no longer required.

### Rank Math controls quiz SEO (Option 2)
- page-quiz.php now captures wp_head() output via output buffering, extracts SEO meta tags (og:*, twitter:*, description, canonical, JSON-LD schema), strips hardcoded equivalents from quiz.html, and injects Rank Math's tags before </head>.
- Works with Rank Math, Yoast, or any SEO plugin that hooks into wp_head().
- Fallback: if no SEO plugin is installed, hardcoded tags in quiz.html remain untouched (the injection block only executes when plugin output is detected).
- To configure: edit the Quiz page in WordPress admin → Rank Math meta box appears → set title, description, social images as normal. Changes take effect immediately — no quiz.html editing needed.

## 2.2.67
### Quiz save/resume (#10 from audit)
- Quiz progress (all selected answers + current question number) now saved to localStorage after every selection.
- On page load, if saved progress exists and quiz hasn't been completed, a "Resume from question N" button appears below the Start button.
- Clicking Resume restores all previous selections visually and jumps to the last active question.
- Saved progress cleared on quiz completion and on retake.

### Quiz accessibility (#12 from audit)
- All 60 quiz options now have role="option", tabindex="0", and aria-selected state management.
- All 10 option containers have role="listbox" with aria-label.
- Added keyboard handler: Enter and Space keys trigger option selection (same as click).
- Added :focus-visible styles for quiz options (teal outline), buttons (next/prev/start).
- Screen readers can now navigate and select quiz options. Keyboard-only users can complete the quiz.

### Quiz SEO meta tags (#14 from audit)
- Added to standalone quiz.html: meta description, og:title, og:description, og:type, og:url, og:site_name, twitter:card, twitter:title, twitter:description, canonical URL.
- The quiz is no longer an SEO black hole — search engines can now index it with proper title, description, and social sharing metadata.

## 2.2.66
### Self-hosted fonts (eliminates render-blocking Google Fonts request)
- Removed external Google Fonts request from functions.php (wp_enqueue_style), all 14 journey HTML files, and quiz.html. Zero remaining references to fonts.googleapis.com.
- Added 17 @font-face declarations in main.css for all 4 font families: Playfair Display (400, 700, 900, 400i), DM Sans (300, 400, 500, 600), Cormorant Garamond (300, 400, 600, 300i, 400i), Amiri (400, 700, 400i). All use font-display: swap.
- Journey/quiz standalone HTML files: @font-face injected with absolute paths (/wp-content/themes/compelling-evidence/assets/fonts/).
- Added preload hints in header.php for the two most critical fonts (Playfair Display 900 for headings, DM Sans 400 for body) — eliminates flash of invisible text on first paint.
- Created assets/fonts/download-fonts.sh — run once after theme upload to download all 17 woff2 files (~500KB total). Updated assets/fonts/README.md with full setup instructions.
- Performance impact: eliminates 1 render-blocking third-party request (fonts.googleapis.com) that previously blocked first paint on every page load. Fonts now served from same origin with browser caching.

## 2.2.65
### Quiz v2 — Proper rewrite (fixes v2.2.64 which incorrectly reduced to 6 questions)
- **10 questions × 6 options** (down from 7 options). Multi-select up to 3 per question retained.
- All 10 questions rewritten with fresh option text designed for the finalised 14 personas.
- Every persona has scoring entries in ALL 10 questions (was missing Q5/Q6/Q7/Q9 for several personas).
- 60 scoring entries total (10 × 6), each mapping to 1-7 persona weights.
- All 14 personas win cleanly in simulation with ideal single-select profiles.
- Scoring architecture: base scoring with question weights + signal boosts (identity convergence combos) + conflict dampening (Muslim ↔ non-Muslim suppression).

**Question design (each probes a different axis):**
1. How did you arrive here? (origin story, 1.55×)
2. First response to God? (gut belief, 1.45×)
3. What would change your mind? (epistemology, 0.9×)
4. Biggest problem with religion? (objection type, 1.45×)
5. Most convincing about current view? (conviction source, 1.0×)
6. Current relationship with faith? (identity, 1.9× — heaviest)
7. When someone expresses doubt? (social instinct, 0.8×)
8. Why are you taking this quiz? (motivation, 1.45×)
9. Strongest case for God is...? (assessment, 0.8×)
10. Biggest unresolved question? (forward-looking, 1.35×)

**Key persona separations via option design:**
- Q1F (grew up in faith) → true-muslim + muslim-doubts cluster
- Q6A/E split committed from doubting Muslims
- Q4A (harm) vs Q4B (reasoning) separates antitheist/ex-believer from new-atheist/classical-atheist
- Q3E (experience) vs Q3D (follow evidence) separates spiritual-seeker from freethinker
- Q5C (ethics without God) + Q3F (settled) separates secular-humanist from apatheist
- Q4E (science) + Q5A (science explains) separates materialist from classical-atheist
- Q7E (examine evidence) + Q3B (scientific evidence) separates scientist from freethinker

## 2.2.64
### Quiz v2 — Complete rewrite
- Reduced from 10 questions to 6, from 7 options to 3 per question. Single-select only (no multi-select).
- Quiz is now ~60 seconds instead of ~3 minutes. Lower friction = higher completion rate.
- All 14 personas still win cleanly in simulation testing. Previous quiz v1 backed up as quiz-v1-backup.html.

**6 questions, each probing a different axis:**
1. **Faith status** (weight 2.0): Muslim committed / Muslim with doubts / Not Muslim — cleanly separates Muslim cluster
2. **God belief** (weight 1.8): Probably yes / Don't know / Probably not — separates theistic, agnostic, atheistic clusters
3. **Epistemology** (weight 1.5): Evidence-science / Reasoning-logic / Meaning-experience — separates scientist, freethinker, seeker
4. **Issue with religion** (weight 1.6): Harm / Bad reasoning / Not relevant — separates antitheist, intellectual atheists, indifferent personas
5. **What God would mean** (weight 1.5): Revelation / Rational order / Practical ethics — separates Muslim, deist, humanist personas
6. **Motivation** (weight 1.7): Exploring openly / Skeptical-curious / Processing something personal — separates agnostic, new-atheist, ex-believer

**Scoring architecture (3 layers, down from 6):**
- Layer 1: Base scoring — 18 answer-persona weight mappings × question weights
- Layer 2: Signal boosts — 14 pattern-recognition rules for key answer combinations
- Layer 3: Conflict dampening — Muslim answers suppress non-Muslim personas (×0.15), non-Muslim suppresses Muslim (×0.1-0.15)

**Persona separation via answer combinations:**
- true-muslim: Q1A + Q5A + boost
- muslim-doubts: Q1B + Q6C + boost; also Q1A + Q4A (committed Muslim troubled by harm)
- ex-believer: Q1C + Q4A + Q6C + boost
- antitheist: Q2C + Q4A + Q6B + boost
- new-atheist: Q2C + Q4A + Q5B + boost
- classical-atheist: Q2C + Q3B + Q4B + Q6B (base scoring dominant)
- materialist: Q3A + Q2C + Q5C + boost
- scientist: Q3A + Q2B + Q6A + boost
- freethinker: Q3B + Q2B + Q6A + boost
- agnostic: Q2B + Q4C + Q6A + boost
- deist: Q2A + Q5B + boost
- spiritual-seeker: Q2A + Q5A + boost
- apatheist: Q4C + Q5C + Q3A + boost
- secular-humanist: Q4C + Q5C + Q3B + boost

## 2.2.63
- Fixed ugly bare-HTML fallback on /journey/ and /quiz/ 404 pages.
  - /journey/ (no slug) now 301 redirects to /journeys/ (the all-paths overview page)
  - /journey/invalid-slug/ now shows the themed 404 page (same design as the main 404.php — gradient background, nav, footer, action buttons for quiz/journeys/articles)
  - /quiz/ fallback (if quiz.html missing) now shows themed 404 instead of raw HTML
- Both fallbacks now use get_header()/get_footer() and the existing .not-found-hero CSS class, keeping full site chrome consistent.

## 2.2.62
- Rewrote live search AJAX handler with relevance-ranked results. WordPress's default search (LIKE '%word%' on post_content) returned irrelevant results — "why does anything exist" showed "What Draws People to Islam Today?" instead of the article literally titled "Why Does Anything Exist?".
- New scoring system:
  - Exact phrase match in title: +100 points
  - Individual word match in title: +10 points per word
  - Phrase match in excerpt: +5 points
  - Content match: 0 points (included in results but not scored)
  - Results sorted by relevance score descending, then title alphabetically
- Words under 3 characters are filtered out to avoid matching on "a", "is", "to", etc.
- Uses direct SQL query with ->prepare for injection safety, bypassing WordPress's default search which does OR matching across all content with no relevance ordering.

## 2.2.61
- Journey layout now matches article layout exactly. All 14 journey files updated:
  - max-width: 1120px → 1060px (matches article-content-wrap)
  - grid: 1fr 280px (matches article sidebar width)
  - gap: 3rem (matches article gap)
  - padding: 3rem 2rem 4rem (matches article content padding)
  - Mobile: single-column at 760px max (matches article mobile)
- Reading column is now ~732px (was ~792px) — identical line length to articles. Eliminates the "huge space on the left" caused by the wider container pushing content off-center on large screens.

## 2.2.60
- Strengthened homepage parallax effect (was imperceptible in v2.2.59). Changes:
  - Arabic verse now moves OPPOSITE to scroll direction (-0.15x) and fades to transparent — creates a clear floating/receding effect
  - H1 subtitle drifts up (-0.08x) and fades faster than the verse
  - Search area fades out as you scroll past
  - Dot grid increased from 0.35x to 0.5x drift
  - Glow orb enlarged (600→700px), increased opacity (0.18→0.25), added teal edge gradient, scales up 15% during scroll
  - will-change: transform, opacity added to all parallax targets for GPU compositing
- Net effect: scrolling away from the hero, the calligraphy lifts and dissolves, the background layers separate into visible depth planes, and the content below emerges from behind the fading hero.

## 2.2.59
- Added subtle parallax effect to homepage hero. Three depth layers:
  - Dot grid pattern (::before) drifts at 0.35x scroll speed (distant background)
  - Purple glow orb (::after, new element) drifts at 0.2x (deepest layer)
  - Arabic verse block floats at 0.12x (gentle hover)
  - All other content (h1, search, chips) scrolls at normal 1x speed
- GPU-accelerated via translate3d and will-change: transform on all parallax layers.
- Automatically disabled: below 768px viewport (mobile), and when prefers-reduced-motion: reduce is set (~30% of users). requestAnimationFrame-throttled, early-exits once scrolled past hero.
- Hero overflow changed from visible to hidden (contains parallax layer shifts within bounds).
- No parallax on article pages, journey pages, or any other template — homepage only.

## 2.2.58
- New article: "Suffering and God: The Islamic Account" (slug: suffering-and-god, 1,510w, 7.5 min). The Problem of Evil category. The positive Islamic account of suffering — not defensive (why the objection fails) but constructive (what Islam says suffering IS). Covers: world as arena not paradise, amanah (33:72), suffering as test not punishment, sunan and natural regularity, the promise of 39:10, khalifah's obligation to act, the suffering that remains unexplained (2:216), and the pastoral voice for the person in actual pain. 3 Quran citations. Faruqian concepts: amanah, khalifah, sunan, actionalism.
- Total articles: 101 (9 in The Problem of Evil category).
- Restored crosslinks "problem of suffering" and "theodicy" to point to the new suffering-and-god slug (previously redirected to problem-of-evil as interim fix in v2.2.57).

## 2.2.57
- Fixed 2 broken crosslinks in ce-crosslinks.php: "problem of suffering" and "theodicy" pointed to non-existent slug suffering-and-god. Remapped to problem-of-evil ("If God Is Good, Why Is There Suffering?"). Full audit confirmed: 0 remaining broken slugs across all 161 crosslink mappings targeting 94 unique articles.

## 2.2.56
- Fixed drop cap appearing on every .reading block in journey pages (29 drop caps per journey instead of one per chapter). Changed selector from .reading > p:first-child::first-letter to .chapter-num + .reading > p:first-child::first-letter — targets only the first reading block after each chapter heading. Also added overflow: hidden to the first paragraph for proper float clearing. Result: one drop cap per screen (9 per journey), not one per reading block (29).

## 2.2.55
### Quiz Scoring Rebalance
- **Question weight rebalance**: Q4 (what drove you from religion) 1.35→1.45, Q6 (faith status) 1.8→1.9, Q7 (reaction to doubt) 0.7→0.8, Q10 (biggest question) 1.25→1.35. Q4 and Q10 were under-weighted for their diagnostic value. Q7 increase is critical for freethinker differentiation.
- **Freethinker vs New Atheist fix**: Freethinker weight on Q3D (follow evidence wherever) increased 3→4. New-atheist weight on Q7F reduced 2→1. New separation rule: Q3D + Q7F without hostile signals → boost freethinker, dampen new-atheist. Freethinker main separation rule boost increased 2.7→3.2. Edge case gap improved from 0.3 (98% ratio) to ~3.0 with full algorithm.
- **Deist vs Spiritual Seeker fix**: Deist weight on Q9F (design argument) increased 2→3. Spiritual-seeker weight on Q3E (direct experience) increased 3→4. Deist separation boost increased 1.65→2.4. New rule: Q2C + (Q9F or Q10C) → additional deist boost. Spiritual-seeker separation boost increased 1.7→2.4. New rule: Q2F + Q3E + (Q5F or Q9G) → additional seeker boost.
- **Apatheist vs Secular Humanist fix**: Apatheist weight on Q9B (even if God exists, so what) increased 3→4. Secular-humanist weight on Q4E (ethical autonomy) increased 3→4.
- All 14 ideal profiles still win cleanly. Edge case margins improved across all 4 problematic boundaries.

## 2.2.54
### Citation Alignment Fix
- Added !important to ALL citation text-align rules in main.css (quran-citation, quran-arabic, quran-translation, quran-ref, hadith-citation, hadith-arabic, hadith-translation, hadith-ref). Previously, the AJC plugin's .article-content p { text-align: justify !important } was overriding the center alignment on any citation content that WordPress wpautop wrapped in <p> tags.
- Added nuclear override rule: .quran-citation *, .hadith-citation * { text-align: center !important; hyphens: manual !important } — ensures nothing inside a citation block can be justify-aligned regardless of plugin specificity.
- Added text-align: center !important to .quran-ref and .hadith-ref (were previously missing text-align entirely — inherited from parent but could be overridden).

### Journey Layout Overhaul
- Widened all 14 journey files to match article page proportions:
  - .art-body: max-width 1080px → 1120px, gap 5rem → 3rem, sidebar 260px → 280px, padding 4rem 3.5rem → 3rem 2.5rem
  - .art-hero: padding reduced to match article hero spacing
  - Mobile: added max-width: 760px to single-column layout (matches article content width)
- Result: reading text area is ~80px wider on desktop, line lengths match articles, less wasted space between content and sidebar.

## 2.2.53
- Subtle philosophical infusion across all 13 standard journey personas. Three paragraphs added per journey (39 insertions total), expressing core Islamic philosophical concepts in plain philosophical language — no Arabic terms, no theological vocabulary, no detectable Islamic framing. The reader encounters the ideas as natural conclusions from the evidence:
  - **Transmission screen** (fitrah + khalifah): "If a Creator made beings with the capacity for reason and moral discernment, they were made for something. They carry a vocation." + "Something in the human being — an orientation toward truth that is part of the original equipment — would respond to the genuine message the way the eye responds to light."
  - **Constant screen** (normativeness): "His existence is not merely a metaphysical fact. It is a moral event. To discover that such a being exists is to discover that you stand under an obligation you did not create and cannot dismiss."
  - **Resonance screen** (purposive creation): "The evidence does not merely suggest that God exists. It suggests what kind of God exists — and that suggestion carries obligations."
- True Muslim journey (separate structure) not modified.

## 2.2.52
- Fixed Auto Justify Content plugin not working on journey pages. Root cause: journey pages serve raw HTML via file_get_contents() + exit, bypassing WordPress entirely — no wp_head fires, no wp_enqueue_scripts fires, no plugin CSS loads.
- Fix: page-journey.php now reads AJC plugin settings from the database and injects matching CSS directly into the journey HTML before echoing. Targets .reading p and .reading li with justify + hyphens. Respects AJC enabled/disabled state, hyphenation setting, and drop cap settings. Excludes Quran/hadith citations from justification.
- Quiz pages not affected — quiz HTML has no reading paragraphs, only UI elements (radio buttons, option cards, hints).

## 2.2.51
### New Pages
- **FAQ page** (page-faq.php): Accordion UI with 4 sections (About the Site, Quiz & Journeys, Common Objections, Technical & Privacy), 18 Q&As total. FAQPage JSON-LD schema markup embedded for Google rich results. Accordion uses CSS grid animation (no jQuery), one-at-a-time expand within each section.
- **Glossary page** (page-glossary.php): 23 Islamic terms (tawhid, khalifah, fitrah, sunan, iman, amanah, actionalism, etc.) with Arabic script, alphabetical navigation bar, and anchor links. Every concept used in the expanded articles is defined here.
- Both pages auto-created on first admin load via ce-secondary-pages.php (flag: ce_v2_pages_created).

### Navigation
- FAQ added to main nav fallback (same row as Journeys, Articles, Ask a Question).
- FAQ + Glossary added to footer Navigate column.

### Article Enhancements
- **Breadcrumbs**: Added to single-ce_article.php — Home › Articles › Topic › Title. Styled inline, matches existing BreadcrumbList schema in ce-seo.php.
- **Auto Table of Contents**: Sticky sidebar widget auto-generated from h2 headings via JS. Shows only when article has 2+ sections. Numbered entries with scroll-spy active highlighting (teal left border). Smooth scroll on click with offset for fixed nav.
- **Related Articles (3-card grid)**: Full-width section below article content showing 3 cards from same topic. Card shows topic label, title, and reading time. Responsive: 3-column → 1-column on mobile.

### Technical
- CSS for all new components added inline in their respective templates (breadcrumbs, TOC, related cards, FAQ accordion, glossary) — follows existing pattern of template-scoped styles.
- FAQ accordion JS: vanilla JS, aria-expanded/aria-hidden toggling, one-at-a-time within section.
- TOC JS: scans h2 elements, assigns IDs (section-1, section-2...), builds ordered list, scroll spy updates active class on scroll.

## 2.2.50
- Added 7 new articles bringing total from 93 to 100. All 7 are 5+ minute reads with Faruqian philosophical concepts woven in (without naming the source):
  1. Was Muhammad Who He Claimed To Be? (1,006w) — Examining the Sources. Tests fraud, madness, and borrowing hypotheses against historical evidence. The hinge article between 'God exists' and 'Islam is true.'
  2. What Draws People to Islam Today? (996w) — The Bigger Picture. Positive case from converts' perspective: tawhid's coherence, the Quran's voice, fitrah recognition, comprehensive life framework.
  3. What Does Islam Say Happens After Death? (994w) — The Bigger Picture. Full eschatology: barzakh, Day of Judgment (99:7-8), mercy, intercession, paradise as ridwan Allah. Completes the moral architecture.
  4. If Nothing Really Matters, Why Does It Feel Like It Should? (1,050w) — Does God Exist? Addresses nihilism and the Apatheist persona directly. Illusion theory fails, meaning crisis as symptom of absent God.
  5. The Spiritual Heart of Islam (1,051w) — The Bigger Picture. Ihsan, dhikr, taqwa, the contemplative tradition. Entry point for Spiritual Seeker persona.
  6. How Can a Rational Person Believe in the Unseen? (1,012w) — Science & Evidence. Materialism as metaphysical claim, rationality vs empiricism, sunan of the unseen.
  7. Coming Back After Leaving (1,083w) — The Inner Journey. Tawbah (39:53), psychology of return, fitrah that waited, practical shahada step. First return-focused article on the site.
- Infused 43 remaining Islamic articles with targeted Faruqian paragraphs (one per article). Final audit: 37 articles with 3+ concepts, 32 with 1-2, 24 with 0 (correctly untouched general theism). No explicit al-Faruqi name mentions.
- Content sync key derived from version 2.2.50.

## 2.2.48
- Removed all 27 explicit mentions of al-Faruqi's name from article content. His philosophical concepts (tawhid as worldview, khalifah, sunan, normativeness, actionalism, fitrah, unity of truth, no original sin) remain fully present — now expressed as Islamic philosophical principles rather than attributed to a named thinker. The ideas speak for themselves.
- Renamed section headings: "The Faruqian reading" → "The unity of truth", "The Faruqian principle" → "The deeper principle".
- Reworded all "Al-Faruqi argued that..." constructions to direct statements: "The moral worth of any action depends...", "Fitrah is not merely an emotional predisposition...", "Authentic revelation would not compartmentalise...".

## 2.2.47
- Deep Faruqian infusion pass across all 15 expanded articles (batches 1+2). Added ~1,500 words of targeted philosophical content threading in the 5 missing al-Faruqi concepts:
  - **khalifah** (man as God's vicegerent): 0/15 → 14/15. Now present in nearly every article — doubt as the khalifah's intellect doing its job, the vocation persisting through emotional turbulence, moral agency as cosmic appointment.
  - **sunan** (God's immutable patterns): 0/15 → 7/15. Threaded into articles on dryness, algorithms, freethinkers, human cognition, the Islam-I-was-defending, and authentic revelation.
  - **normativeness** (God as source of moral imperative): 1/15 → 6/15. Anger at injustice as evidence FOR the moral order, the Quran's voice as normative not merely informative, God's existence as a moral event.
  - **actionalism** (works, not grace): 2/15 → 5/15. Free moral action as the purpose of creation, guaranteed outcomes trivialise freedom, the khalifah's vocation requires informed choice not passive compliance.
  - **no original sin** (man born capable): 1/15 → 3/15. Explicit contrast with Christianity's fallen-nature doctrine in articles on coercion and universal salvation.
- Every insertion contextually placed after the most relevant heading in each article — not appended generically but woven into the argument flow.

## 2.2.46
- Batch 2: Expanded 9 articles from 236–307 words to 1,000+ words each (~7,500 new words total). All now meet the 5-minute minimum reading time. Faruqian concepts infused throughout:
  - Did Islam Spread by the Sword? (236→1,036w): Bulliet conversion data, dhimmi system, Southeast Asia as proof, Quran 2:256, comparative context
  - Universal Salvation (264→1,012w): Actionalism — choices must have consequence, amanah/trust, Quran 68:35-36, mercy vs justice, Islamic middle path between Calvinism and universalism
  - The Good Muslim Paradox (281→1,069w): Fitrah driving inquiry, iman vs conditioning, formation failure vs Islam failure, al-Ghazali's crisis
  - The Freethinkers Islam Produced (281→1,028w): Al-Razi, Ibn al-Rawandi, Mu'tazilites as evidence of intellectual vitality not weakness, tradition's resilience through challenge-response-refinement
  - Why Do Humans Believe in God? (285→1,007w): HADD, Theory of Mind, fitrah as gnoseological endowment (Quran 30:30), genetic fallacy, convergence argument
  - Reading the Quran for the First Time (298→1,069w): Literary uniqueness, Quran 4:82, unity of text, al-Faruqi's unity of truth, Quran's courtroom-witness argumentative mode
  - What Would Authentic Revelation Look Like? (300→1,020w): Four criteria from reason — strict monotheism, public preservation (Quran 15:9), unified life-guidance, moral seriousness. Soteriology: no original sin, no proxy salvation
  - How Muslims Leave (301→1,008w): Cottee/Richter research, stages of deconversion, fitrah vs algorithmic bubble, muhasabah (self-reckoning), social liberation ≠ truth
  - The Islam I Was Defending (307→1,048w): Thin formation vs deep tradition, unity of truth, usul al-fiqh as tools for hard texts, depth vs premature surrender

## 2.2.45
- Fixed Quran and hadith citation alignment. Changes to main.css and print.css:
  - .quran-ref: added display: block and margin-top: 0.4rem (was inline, sitting too tight against translation)
  - .quran-translation: added text-align: center and display: block (was inheriting left-align from max-width container)
  - .hadith-translation: added text-align: center and display: block (same fix)
  - .hadith-ref: added margin-top: 0.4rem for consistent spacing
  - Print CSS: added text-align: center and display: block to both translation and ref selectors
  - Translation margin-bottom increased from 0.6rem to 0.8rem for breathing room between translation and ref
- Standardised all 74 Quran citation refs and all 7 hadith refs to use em-dash prefix format: "— Surah Name (chapter:verse)". 40 refs were missing the em-dash.

## 2.2.44
- Content sync key is now derived automatically from the theme version in style.css. No manual key bumping needed — every version bump triggers a fresh sync on the next admin page load. Key format: ce_content_sync_{version} (e.g. ce_content_sync_2_2_44).

## 2.2.41
- Complete rewrite of the evolution article (evolution-and-islam) through Faruqian lens. Old version was defensive and hedging ("genuine difficulty," "real tension"). New version presents evolution as God's sunan operating in the biological world — not a threat to Islam but a discovery of divine patterns.
- 5 new sections: "Evolution as God's sunan," "The word 'undirected' is philosophy, not science," "Adam: biological specimen or bearer of the trust?," "The unity of truth," "What evolution actually threatens — and what it does not."
- 4 Quran citations: Fatir 35:43 (immutable sunan), Aal-Imran 3:190 (signs for people of understanding), Al-Baqarah 2:30 (khalifah announcement to angels), Al-Ahzab 33:72 (the amanah/trust).
- Al-Faruqi's core concepts now explicit: sunan as divine patterns in nature, khalifah as man's cosmic moral vocation, amanah as the trust nature refused, unity of truth as the principle that revelation and science cannot ultimately contradict.
- Key argument shift: "undirected" is a philosophical label, not a scientific finding. Evolution describes the mechanism; tawhid explains why the mechanism exists and what it produced beings for.
- Content sync key bumped to 2_2_41.

## 2.2.40
- Closed 6 Faruqian philosophy gaps identified in the al-Tawhid audit. Added ~3,000 words of new content across 6 articles, grounding the site's arguments in al-Faruqi's intellectual framework:
  1. **God as normativeness** (Is God Personal?): New section — God is not merely first cause but the "core of normativeness." His existence is a moral event that restructures everything. Distinguishes Islamic God from deist clockmaker.
  2. **Science requires God** (Does Science Provide All The Answers?): New section with Quran 35:43 citation — al-Ghazali's argument that the orderliness of nature presupposes God's immutable patterns (sunan). The materialist's trust in natural law is "animal faith"; the Muslim's trust is grounded.
  3. **Nature as good + Actionalism** (Purpose of Life): Two new sections — Islam rejects original sin, fallen nature, and salvation-by-saviour. Creation is gift, not curse. Man's fate is his own making (falah). The khalifah's vocation is cultivation, not escape. Quran 6:164 citation.
  4. **Unity of truth** (Is Doubt Permitted?): New section — if God is one, truth is one. Revelation and reason cannot ultimately contradict. Neither gets a blank cheque. Classical scholars pursued philosophy and science simultaneously because their operating principle ruled out ultimate contradiction.
  5. **Fitrah deepened** (Was My Faith Conditioning?): New section — fitrah as innate disposition toward God, not cultural conditioning. If fitrah is real, the conditioning objection reverses: atheism is the conditioned state, not theism.
  6. **Tawhid as worldview** (Why Islam?): New section — tawhid is not one doctrine but the organising principle of knowledge, ethics, metaphysics, and history. No other tradition makes this integrating move. Converting is not adding a belief but reorganising the entire framework.
- Content sync key bumped to 2_2_40.

## 2.2.39
- Added `irreligious` as a secondary quiz trait layered on top of the existing 14 personas instead of creating a new overlapping persona.
- The result screen can now show a low religious attachment or religiously detached posture when the signal is strong enough, without overriding the main journey persona.

## 2.2.38
- Rebalanced the desktop front page so the hero and closing CTA feel more proportional.
- Tightened the desktop hero width slightly and made the CTA section denser, narrower, and more visually aligned with the search-led composition.

## 2.2.37
- Reduced the front-page Quranic verse text size so it sits more quietly in the hero and no longer competes with the search bar.

## 2.2.36

- Replaced the front-page verse image with live Arabic verse text using the theme's Quran font stack and ayah end marker.
- Kept the original verse image asset and hidden fallback markup in place for future reuse.

## 2.2.35
- Tightened the desktop front-page composition so the layout feels less over-wide and less sparse.
- Made the homepage search bar the primary desktop command element with a wider field, stronger emphasis, and a calmer visual hierarchy.
- Reduced excess spacing in the featured strip, quick-access row, and article grid so the front page reads as denser, more intentional, and less stretched.

## 2.2.34
- Refined the desktop front page layout for better balance and spacing.
- Reduced the over-stretched look in the hero, restored the desktop scroll hint, and widened the search area slightly.
- Reworked the quick-access row and article grid into clearer desktop cards with breathing room, cleaner borders, and a more coherent colour balance.

## 2.2.33
- Fixed broken article styling caused by malformed closing tags in the article data files, most visibly in the Satanic Verses article.
- Repaired paragraph closures across multiple articles so Quran and hadith citation blocks, headings, and sidebar cards render consistently again.
- Content sync key bumped to 2_2_33 so the corrected article markup propagates on update.

## 2.2.32
- Standardised all 63 Quran citations across all 5 article data files to mushaf presentation:
  - All `<span class="quran-arabic">` converted to `<div class="quran-arabic">` (44 fixes) — ensures Uthmani Quran font applies correctly
  - All `<span class="quran-translation">` and `<span class="quran-ref">` converted to `<div>` for consistent block-level rendering
  - All `<p class="quran-translation">` converted to `<div>` (same reason)
  - All hadith citation tags also normalised from `<span>` to `<div>`
- Added Arabic-Indic verse-end markers ﴿number﴾ to all 63 Quran citations, mimicking mushaf presentation. Examples: ﴿٢٥٦﴾, ﴿١٥﴾, ﴿٨٠﴾, ﴿٥٣﴾. Multi-verse citations show the end marker for the final verse (e.g. 51:56-57 → ﴿٥٧﴾).
- Applied Uthmanic rasm wasla alif (ٱلْ instead of الْ) throughout all Quranic Arabic text.
- Content sync key bumped to 2_2_32 so all updated citations propagate to the database.

## 2.2.31
- Expanded automatic article cross-linking to 100% coverage. 161 phrase→article mappings covering all 93 articles. Up from 35 phrases / 29 articles in v2.2.29.
- Max links per article raised from 3 to 5 for richer internal linking.
- Added duplicate-target prevention: won't link to the same article twice in one page.
- Improved safety checks: tag-counting approach for detecting if match is inside <a>, <h1-6>, or citation blocks (replaces fragile regex lookahead).
- Added title attribute to crosslinks showing the target article's title on hover.
- New phrases include: "kalam argument", "anthropic principle", "qadar", "scientism", "big bang", "i'jaz", "inimitability", "naskh", "uthmanic codex", "isnad", "chain of transmission", "opium of the people", "ibn rushd", "sociology of apostasy", "emotional apostasy", "intellectual apostasy", "deconversion pipeline", and 100+ more.

## 2.2.30
- Complete rewrite of the Satanic Verses article (gharaniq-satanic-verses). Expanded from ~400 words with zero Arabic to ~1,800 words with 7 Arabic citation blocks covering An-Najm 53:19–20, 53:21–23, 53:3–4, Fussilat 41:42, Al-Hijr 15:9, Al-Hajj 22:52, plus the alleged gharānīq words themselves. Added specific scholar citations (al-Wāqidī, al-Tabarī, al-Bukhārī, al-Rāzī, al-Qāḍī 'Iyāḍ, Ibn Kathīr). Content sync key bumped.

## 2.2.29
- Added automatic article cross-linking via `inc/ce-crosslinks.php`. A `the_content` filter scans each article on render and links the first mention of key phrases to their corresponding articles.
- Restrained by design: max 3 links per article, first mention only, never links to self, skips text inside headings, existing links, and Quran/hadith citation blocks.
- 35 phrase-to-article mappings covering all 10 categories: cosmological argument, fine tuning, ontological argument, problem of evil, euthyphro dilemma, hard problem of consciousness, near death experiences, multiverse, qira'at, variant readings, abrogation, hadith science, age of aisha, banu qurayza, satanic verses, honour killings, apostasy law, religious trauma, purpose of life, and more.
- Phrases sorted longest-first to prevent partial matches. Case-insensitive matching.
- Styled as subtle dotted underline in teal (consistent with site accent colour), no bold or icon decoration.
- Included in functions.php via require_once.

## 2.2.28
- Replaced static "Try asking" chips with a rotating pool of 15 phrases covering all 10 categories. 6 shown per visit, randomised client-side on page load. Pool:
  - why does anything exist (Does God Exist?)
  - problem of evil (The Problem of Evil)
  - ethics without god (Ethics Without God?)
  - fine tuning universe (Science & Evidence)
  - quran preserved (Examining the Quran)
  - leaving islam (Rights & Freedom)
  - age of aisha (Examining the Sources)
  - science and religion (Science & Evidence)
  - religious trauma (The Inner Journey)
  - women in islam (Rights & Freedom)
  - hadith reliability (Examining the Sources)
  - purpose of life (The Bigger Picture)
  - islam and violence (History & Context)
  - why islam (The Bigger Picture)
  - doubt and faith (The Inner Journey)
- "why does anything exist" and "problem of evil" are anchor phrases — always included in the 6 shown. Remaining 4 drawn randomly from pool. Final 6 shuffled so anchors don't always appear first.
- All chips rendered in HTML but hidden (display:none). Inline JS picks and reveals 6. No server-side logic, works with page caching.

## 2.2.28
- Complete rewrite of the Satanic Verses article (gharaniq-satanic-verses). Expanded from ~400 words with zero Arabic to ~1,800 words with 7 Arabic citation blocks:
  - Surah An-Najm 53:19–20 — the verses mentioning al-Lāt, al-'Uzzā, and Manāt (context for the alleged incident)
  - The alleged gharānīq words themselves in Arabic: تِلْكَ ٱلْغَرَانِيقُ ٱلْعُلَا وَإِنَّ شَفَاعَتَهُنَّ لَتُرْتَجَىٰ (marked as NOT part of the Quran)
  - Surah An-Najm 53:21–23 — the actual Quranic verses that replaced the alleged interpolation (demolishing the goddesses)
  - Surah An-Najm 53:3–4 — "He does not speak from whim" (from the same surah, contradicting the claim)
  - Surah Fussilat 41:42 — "Falsehood cannot approach it"
  - Surah Al-Hijr 15:9 — "We will be its guardian"
  - Surah Al-Hajj 22:52 — the verse critics cite as "proof" (explained with tamannā dual meaning)
- Added specific scholar names and dates: al-Wāqidī (matrūk status), al-Tabarī (compiler vs authenticator), al-Bukhārī (excluded the story), al-Rāzī, al-Qāḍī 'Iyāḍ (called it fabrication in al-Shifā), Ibn Kathīr (dismantled chains individually).
- Added section "What the story claims" with full narrative reconstruction so the reader understands exactly what is being evaluated.
- Added section on Surah Al-Hajj 22:52 addressing the "Quranic confirmation" argument directly.
- Content sync key bumped to 2_2_28.

## 2.2.27
- Added concrete Arabic examples to the Qira'at article (quran-variant-readings-qiraat). Four cases showing actual Hafs vs Warsh vs Abu Amr differences with Arabic text in Uthmanic rasm:
  1. Surah Al-Fatihah 1:4 — مَالِكِ (Hafs, "Owner") vs مَلِكِ (Warsh, "King"): vowel difference, same theology
  2. Surah Al-Baqarah 2:48 — يُقْبَلُ (Hafs) vs تُقْبَلُ (Abu Amr): single dot difference in undotted rasm, identical meaning
  3. Imālah pronunciation variants (al-nās → al-nēs): dialectal, zero semantic change
  4. Surah Al-Baqarah 2:184 — يُطِيقُونَهُ vs يُطَوَّقُونَهُ (Ibn Abbas): rare wording variant that affected fiqh discussion but not the principle
- Content sync key bumped to 2_2_27 so the updated article propagates to the database on next admin load.

## 2.2.25
- Changed "Try asking" chips to lowercase phrases: "why does anything exist", "problem of evil", "ethics without god", "quran preserved", "leaving islam", "fine-tuning universe".

## 2.2.24
- Fixed broken search from multi-keyword chips. Reverted "Try asking" to single keywords only: existence, suffering, morality, Quran, apostasy, consciousness. One word per chip, one word per data-query.

## 2.2.22
- Merged best of two parallel development branches into a single release.
- Front page "Try asking" chips now use comma-separated keyword clusters that map to the site's 10 categories: "existence, cosmology, first cause" / "suffering, evil, divine justice" / "science, consciousness, limits of reason" / "Quran, preservation, hadith, sources" / "apostasy, freedom, rights, doubt" / "purpose, meaning, competing claims". 6 chips, each covering a cluster of related search terms.
- Search page suggestion pills updated to match new categories: Ethics Without God, Fine-Tuning, Quran Preservation, Hadith Reliability, Women in Islam, Purpose of Life.
- Scroll-hint third tile fixed: "Common Objections" (stale, duplicate link) replaced with "The Inner Journey" → /topic/the-inner-journey/.
- Documentation quiz description corrected: "up to 3 selections per question" with 3-choice calibrated scoring (was incorrectly stated as 2 selections).
- UPGRADING.md version reference updated to v2.2.22.

## 2.2.21
- Rewrote front page "Try asking" chips to match actual site content and reader intent. Old: generic religious questions ("Does God exist?", "Islam & science", "What happens after death?"). New: specific queries that map directly to articles ("Why does anything exist?", "If God is good, why suffering?", "Can morality exist without God?", "How was the Quran preserved?", "Can you leave Islam freely?", "Why is the universe so specific?").
- Updated search page suggestion pills from stale category names to current ones. Replaced Consciousness, Free Will, Morality, Evolution, The Quran, Hellfire with Ethics Without God, Fine-Tuning, Quran Preservation, Hadith Reliability, Women in Islam, Purpose of Life.
- Fixed scroll-hint tile below hero: third tile was "Common Objections" linking to /topic/does-god-exist/ (duplicate link, stale label). Now "The Inner Journey" linking to /topic/the-inner-journey/.

## 2.2.20
- Complete documentation overhaul — all four files rewritten with accurate v2.2.x stats.
- README.md: Rewritten from scratch. Now reflects 93 articles, 10 categories, 14 personas (with current display names), correct file structure, auto-run modules, font infrastructure, design system, and three-volume architecture.
- readme.txt: Rewritten to WordPress standard format. Updated description, features, installation steps (including auto-run modules), persona table, design system, FAQ, and localisation sections.
- UPGRADING.md: Updated version references (v1.9.3 → v2.2.19). Merged the entire Technical Upgrade Guide from UPGRADING-TECHNICAL.md into this file (auto-run modules, database migration, journey/quiz/CSS update procedures, rollback instructions).
- UPGRADING-TECHNICAL.md: Deleted. All content merged into UPGRADING.md.
- Four documentation files remain: README.md, readme.txt, CHANGELOG.md, UPGRADING.md.

## 2.2.19
- Synced persona label renames from quiz to all remaining files (41 replacements across 19 files).
- "Muslim with Doubts" → "The Questioning Muslim" and "The True Muslim" → "The Committed Muslim" now consistent everywhere: progress page, front page, all 14 journey HTML files, single article templates, and functions.php.
- Internal routing keys (muslim-doubts, true-muslim) unchanged — only display labels updated.

## 2.2.18
- Revised the visitor-facing persona labels and quiz result blurbs for clearer differentiation and less loaded language.
- Renamed selected display labels, including `true-muslim` to `The Committed Muslim` and `muslim-doubts` to `The Questioning Muslim`, while preserving internal routing keys.
- Softened persona descriptions so results feel more precise, less accusatory, and more credible to skeptical readers.

## 2.2.17
- Refined hadith citation styling to match Quran citation alignment and presentation more closely.
- Centered hadith Arabic, translation, and reference blocks for consistent article rendering.
- Updated print styles so hadith citations remain aligned with Quran citations in exported pages.

## 2.2.15

- Improved the quiz mobile layout so answer cards, navigation, and result actions stack cleanly on small screens.
- Added stronger small-screen typography, spacing, and overflow handling for long answer text.
- Made the question navigation sticky on mobile for easier progression without broken spacing.

## 2.2.14

- Recalibrated quiz scoring for 3-choice behavior so mixed selections do not overpower primary journey signals.
- Added question-level dominance weighting, identity convergence bonuses, and stronger conflict dampening.
- Preserved 3-selection flexibility while improving closest-match persona accuracy.

## 2.2.13
- Reworked quiz scoring and persona matching to produce closer journey results.
- Normalised multi-select scoring by question and added persona signal boosts and conflict dampening.
- Limited each quiz question to two selections for cleaner matching.
- Updated quiz result label to “Closest match”.

## 2.2.11
- Fix blank quiz result state caused by malformed closing markup in `journeys/quiz.html`.

## 2.2.10
- Fixed broken quiz answer list layout caused by malformed closing divs in `journeys/quiz.html`.
- Removed stray backslashes from visible quiz copy so options render cleanly.
- Preserved existing quiz scoring and journey routing logic.

## 2.2.9
- Softened and de-templated the journey HTML voice for skeptical readers.
- Replaced repetitive stock phrasing with calmer, article-specific language.
- Preserved the underlying argument while reducing overtly preachy framing.

## [2.2.8] — 2026-03-26

### Changed
- Applied a third-stage editorial tightening pass to the remaining softer articles in `articles-data-3.php`, `articles-data-4.php`, and `articles-data-next.php`.
- Reframed key introspective and bridge articles with stronger unity-centered architecture while keeping them readable to skeptical visitors.
- Strengthened revelation funnel pieces so the movement from generic theism to Islam lands more clearly.
- Bumped the one-time content sync key so existing installs receive the revised article content after update.

## [2.2.6] — 2026-03-26

### Changed
- Applied a second-stage editorial tightening pass to selected softer articles in `articles-data-3.php` and `articles-data-4.php`.
- Reframed inward-journey and history/context pieces more firmly through a more integrated and pro-Islamic lens.
- Replaced several generic closing paragraphs with article-specific conclusions for stronger tonal consistency.
- Bumped the one-time content sync key so existing installs receive the revised article content after update.

## [2.2.5] — 2026-03-26

### Changed
- Revised the article corpus with an Al Tawhid editorial foundation.
- Shifted major apologetic articles toward a more explicitly pro-Islamic posture where appropriate.
- Tightened article slugs to remain under 55 characters where needed.
- Bumped the one-time content sync key so existing installs receive the revised article content after update.

# Changelog

All notable changes to the Compelling Evidence WordPress theme are documented here.

Format follows [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).  
Versioning follows [Semantic Versioning](https://semver.org/): MAJOR.MINOR.PATCH

---

## [2.2.4] — 2026-03-26

### Added — Article content sync module

**Problem:** Article creation functions skip existing posts (`if ($existing) continue;`). So content changes in the theme data files — new Quranic citations, updated text, entire new articles — never propagate to the WordPress database. The live site shows the original content from when articles were first created, not the current data files.

**New file: `inc/ce-content-sync.php`** — Runs ONCE per version (flagged by `ce_content_sync_2_2_4`). On first admin page load after theme update:

1. Reads all 5 article data files (articles-data.php through articles-data-next.php)
2. For each article: if it exists in the database, **updates its content, title, and excerpt** from the data file
3. If an article doesn't exist yet (new articles added in this version), **creates it** with the correct topic assignment
4. Sets flag to prevent re-running

**This fixes:**
- Quran 51:56 citation (وَمَا خَلَقْتُ ٱلْجِنَّ وَٱلْإِنسَ إِلَّا لِيَعْبُدُونِ) not appearing on the live Purpose of Life article
- All 7 new articles added since initial deployment (prayer, sword, LGBTQ+, honour killings, qira'at, cosmology, emotional vs intellectual apostasy, algorithm) being created if they don't exist
- Updated content in any existing article (Uthmanic rasm citations, expanded arguments, corrected text)

**For future updates:** Bump the version key in `ce-content-sync.php` (e.g., `ce_content_sync_2_3_0`) and it will re-run on the next admin page load, syncing any new content changes.

**Execution order on admin_init:**
1. Category migration (v2.2.0) — creates terms, remaps articles
2. Content sync (v2.2.4) — updates content, creates missing articles
3. Secondary pages (v2.1.2) — creates About/Contact/etc pages

Each has its own flag and runs independently.

---

## [2.2.3] — 2026-03-25

### Added — Uthmani Quran font infrastructure + Amiri via Google Fonts

**Problem:** The uploaded OthmanyFonts are legacy 1990s fonts with custom encoding — zero Unicode Arabic characters. They cannot render standard Arabic text on the web.

**Solution:** Two-layer font system for Quranic verses:

1. **Self-hosted Uthmani font** (`@font-face` in main.css) — loads `assets/fonts/uthmani-quran.woff2` if present. Uses `unicode-range` to only activate for Arabic characters. `font-display: swap` ensures text renders immediately with fallback while font loads. See `assets/fonts/README.md` for instructions on adding the KFGQPC Uthmanic Script HAFS font.

2. **Amiri via Google Fonts** (immediate fallback) — added to the existing Google Fonts enqueue in functions.php. Amiri is designed by Khaled Hosny specifically for Arabic typesetting and handles Quranic text well. Loads weights 400 and 700.

**Font priority chain:**
- `.quran-arabic`: `'Uthmani Quran'` → `'Amiri'` → `'Traditional Arabic'` → `'Scheherazade New'` → `serif`
- `.hadith-arabic`: `'Amiri'` → `'Traditional Arabic'` → `'Scheherazade New'` → `serif` (no Uthmani — per requirement, Uthmani is Quran-only)

**Files changed:**
- `assets/css/main.css` — `@font-face` declaration + `.quran-arabic` font-family updated
- `assets/css/print.css` — split `.quran-arabic` and `.hadith-arabic` rules (were combined), Quran gets Uthmani
- `functions.php` — Amiri added to Google Fonts enqueue URL
- `assets/fonts/README.md` — instructions for adding KFGQPC font

**To complete the setup:** Download KFGQPC Uthmanic Script HAFS, convert to woff2, place as `assets/fonts/uthmani-quran.woff2`. The theme handles the rest automatically.

---

## [2.2.2] — 2026-03-25

### Added — Database migration script for category restructure

**New file: `inc/ce-migration-2-2-0.php`** — Runs ONCE on first admin page load after theme update. Handles the gap between theme files (which now reference new categories) and the WordPress database (which still has old categories from the previous version).

**What it does:**

1. **Creates 10 new taxonomy terms** if they don't exist: Does God Exist?, The Problem of Evil, Ethics Without God?, Science & Evidence, Examining the Quran, Examining the Sources, History & Context, Rights & Freedom, The Inner Journey, The Bigger Picture.

2. **Simple renames** — articles under old terms that map 1:1 to new terms get moved automatically:
   - Existence of God → Does God Exist?
   - Evil & Suffering → The Problem of Evil
   - Ethics & Morality → Ethics Without God?
   - Science & Faith + Consciousness → Science & Evidence (merged)
   - The Quran → Examining the Quran
   - Science & Islam → History & Context
   - Purpose & Meaning + The Next Question → The Bigger Picture (merged)

3. **Per-article redistribution** — 60+ articles from "Objections", "Doubts & Questions", and "Hard Questions" are individually reassigned to their correct new category based on article slug. Each assignment matches the finalised structure from v2.2.0.

4. **Slug rename** — `purpose-of-life-islam` → `purpose-of-life` via `wp_update_post`.

5. **Cleanup** — all 12 old taxonomy terms are deleted ONLY if they have no remaining articles assigned (safety check prevents data loss if migration is partial).

6. **Bridge articles** — the 3 articles in `articles-data-next.php` (Does God Communicate, Authentic Revelation, Competing Claims) are assigned to The Bigger Picture.

7. **Flagged** — uses `ce_category_migration_2_2_0` option to ensure it runs exactly once. Debug logging to `error_log` if `WP_DEBUG` is true.

**Included in `functions.php`** via `require_once`.

### Why this is necessary

WordPress stores taxonomy assignments in the database (`wp_term_relationships`), not in theme files. Updating the theme replaces the PHP/CSS/JS but does NOT change which terms exist or which articles are assigned to which terms. Without this migration, the archive page would show empty categories (new terms with no articles) while articles remain orphaned under old terms that the archive no longer looks for.

---

## [2.2.1] — 2026-03-25

### Fixed — Purpose of Life slug + Quran 51:56 citation

1. **Slug renamed:** `purpose-of-life-islam` → `purpose-of-life`. Updated across 17 references in 15 files (articles-data.php, header.php, front-page.php, functions.php, and 12 journey HTML files).

2. **Quran 51:56 citation updated** to proper Uthmanic rasm with wasla alif (ٱ): `وَمَا خَلَقْتُ ٱلْجِنَّ وَٱلْإِنسَ إِلَّا لِيَعْبُدُونِ` — "I did not create jinn and humans except to worship Me." Citation format standardised from `<span>` to `<div>` elements matching the rest of the site. Hadith Qudsi citation in the same article also standardised.

---

## [2.2.0] — 2026-03-25

### Major — Full category restructure (12 old → 10 new categories)

**The entire article taxonomy has been restructured** for neutral, investigative framing that doesn't signal a religious agenda. All 93 articles remapped. 10 top-level categories with 3 subcategories under "The Inner Journey."

#### Old → New category mapping

| Old Category | New Category | Rationale |
|---|---|---|
| Existence of God | **Does God Exist?** | Question form invites inquiry |
| Evil & Suffering | **The Problem of Evil** | Clearer, academic framing |
| Ethics & Morality | **Ethics Without God?** | Question positions it as genuine inquiry |
| Science & Faith + Consciousness | **Science & Evidence** | Merged — empirical focus |
| The Quran | **Examining the Quran** | "Examining" = investigative, not devotional |
| *(new)* | **Examining the Sources** | Hadith + Sirah combined |
| Science & Islam | **History & Context** | Broader civilisational questions |
| *(from Doubts/Objections)* | **Rights & Freedom** | Leads with what secular readers care about |
| Doubts & Questions | **The Inner Journey** | Personal/pastoral content, with subcategories |
| Purpose & Meaning + The Next Question | **The Bigger Picture** | Merged — purpose, claims, bridge articles |
| Objections | *(redistributed)* | Split across Does God Exist?, Problem of Evil, Rights & Freedom, etc |
| Hard Questions | *(redistributed)* | Content moved to Rights & Freedom |

#### New structure

| # | Category | Count | Subcategories |
|---|---|---|---|
| 1 | Does God Exist? | 13 | — |
| 2 | The Problem of Evil | 8 | — |
| 3 | Ethics Without God? | 5 | — |
| 4 | Science & Evidence | 10 | — |
| 5 | Examining the Quran | 11 | — |
| 6 | Examining the Sources | 7 | — |
| 7 | History & Context | 7 | — |
| 8 | Rights & Freedom | 10 | — |
| 9 | The Inner Journey | 16 | Understanding Doubt (6), The Emotional Cost (5), What Happens Next (5) |
| 10 | The Bigger Picture | 8 | — |

#### Files changed

- **All `articles-data*.php`** — topic fields updated to new category names
- **`articles-data-next.php`** — topic + order fields added to 3 bridge articles (were missing)
- **`archive-ce_article.php`** — topic_order, topic_descs, subcategory rendering logic + CSS for The Inner Journey
- **`functions.php`** — all topic_map arrays updated (3 instances), term slugs updated
- **`footer.php`** — fallback topic links updated to new slugs/names
- **`front-page.php`** — scroll-hint topic links updated

#### Design principle

Category names were chosen to feel like a research site, not a dawah site. An atheist browsing the archive sees: "Does God Exist?", "Science & Evidence", "Rights & Freedom" — questions and investigations, not doctrines. The word "Islam" does not appear in any category name.

---

## [2.1.6] — 2026-03-25

### Added — 2 research-gap articles (92 → 94, then deduplicated to 93)

Two articles based on findings from Cottee (2015), Richter (2025), and other ex-Muslim research:

1. **"Did Your Heart Leave Before Your Head?"** — Emotional vs intellectual apostasy. Some leave through pain first and rationalise later; others follow the evidence to a conclusion they didn't want. Which pathway determines how you engage with the evidence. Draws on Cottee's distinction and Richter's phase model (anger → reconciliation).

2. **"The Algorithm That Deconverted You"** — The internet deconversion pipeline: YouTube atheists, Reddit communities, recommendation algorithms that create self-reinforcing information diets. Acknowledges the reader's actual path to disbelief, then points out that simplified online arguments aren't the strongest versions. "You've seen the trailer. Now examine the evidence."

### Removed — Duplicate "Purpose of Life" article

"What Is The Purpose of Life?" existed in both `articles-data-2.php` (slug: `purpose-of-life`) and `articles-data.php` (slug: `purpose-of-life-islam`). Removed the shorter version from `articles-data-2.php`. The longer version in `articles-data.php` is the canonical one.

**Article count: 93 unique articles, 0 duplicates.**

---

## [2.1.5] — 2026-03-25

### Changed — Articles archive: bigger fonts + accordion topic sections

**Font size increases** (all in `archive-ce_article.php` inline styles):

| Element | Before | After |
|---|---|---|
| Topic section name | 1.2rem | **1.5rem** |
| Topic description | 0.88rem | **1rem** |
| Article title | 0.95rem | **1.1rem** |
| Article excerpt | 0.82rem | **0.92rem** |
| Article number | 0.62rem | **0.72rem** |
| Read time | 0.62rem | **0.72rem** |
| Topic count badge | 0.65rem | **0.72rem** |
| Article row padding | 0.75rem | **0.9rem** |

**Accordion topic sections:** Each topic group is now collapsible. Click the topic header to expand/collapse the article list beneath it. All sections start expanded (class `open`). The header shows a chevron (▼) that rotates on toggle. Smooth `max-height` + `opacity` transition (0.35s). Header gets a subtle hover background. The description, article list, and "show all" button are all inside the collapsible body.

---

## [2.1.4] — 2026-03-25

### Fixed — Progress page shows only active paths + persona names everywhere

1. **Progress page now gated:** If no paths have been started, shows a quiz CTA ("You haven't started a journey yet") instead of all 14 empty cards. Only paths the user has actually started or completed are shown.

2. **"Path 01/02/03..." replaced with real persona names everywhere.** All 6 files updated:
   - `page-progress.php` — "The New Atheist", "The Agnostic", etc.
   - `front-page.php` — returning visitor stats
   - `functions.php` — journey registration
   - `single-ce_article.php` — article sidebar "Revisit journey" link
   - `single.php` — same as above

3. **Missing personas added:** Freethinker and True Muslim were missing from PATH_NAMES in `single-ce_article.php`, `single.php`, and the clearProgress function. Added to all.

4. **Stale SCREENS array fixed** in `single-ce_article.php`, `single.php`, and `front-page.php` — was missing Resonance and Transmission chapters (8 screens instead of 10). Now all 10 screens present.

---

## [2.1.3] — 2026-03-25

### Fixed — Page creation now works on already-activated themes

Changed `ce_create_secondary_pages()` from `after_switch_theme` hook (only fires on theme switch) to `admin_init` with an option flag (`ce_secondary_pages_created`). Now creates all 5 secondary pages (About, Editorial Policy, Privacy Policy, Contact, Ask a Question) on the first admin page load after the update. Won't re-run once the flag is set.

### Added — 5 gap articles closing all remaining coverage holes

Total articles: 87 → **92**. All 5 genuine gaps from the comprehensive objection audit are now filled:

1. **"Did Islam Spread by the Sword?"** (Objections) — Conquest vs conversion distinction. Bulliet's conversion rate data (200–300 year lag). Dhimmi system as evidence against forced conversion. Quran 2:256 ("no compulsion"). Rules of engagement. Honest: some forced conversions happened, but they were not the norm and contradicted the Quran's own teaching.

2. **"Islam and Same-Sex Attraction: The Question That Can't Be Avoided"** (Objections) — Quran's position stated directly (no revisionism). Act vs attraction distinction. The ibtila' (trial) framework. "God does not burden a soul beyond its capacity" (2:286). No justification for hatred or violence. Bottom line: the question is whether you trust an omniscient God's judgment where it differs from yours.

3. **"Honour Killings: Culture, Not Islam"** (Objections) — Pre-Islamic practice predating Islam by centuries. Occurs across religions and cultures. Islamic law explicitly prohibits extrajudicial killing (17:33). No school of jurisprudence permits it. Even capital punishment requires state judicial process, not family vigilantism.

4. **"The Quran Has Variant Readings — Does That Disprove Preservation?"** (The Quran) — Qira'at explained: variant vocalisations of the same consonantal rasm. Hafs vs Warsh differences are minor (vowels, grammar). No theological contradictions. Preservation of variants is evidence FOR meticulous transmission, not against it. Sana'a palimpsest addressed.

5. **"Flat Earth, Seven Heavens, and Shooting Stars: Does the Quran's Cosmology Disprove It?"** (Science & Faith) — Dahaha etymology (expansion, not flattening). Yukawwiru (wrapping night/day = spherical rotation). Seven heavens: multiple interpretations in classical tradition. Phenomenological language vs scientific claims. The Quran is not a science textbook.

**Arabic citations added:** 10 new Quranic citations across all 5 articles (2:256, 7:80, 2:286, 17:33, 79:30, 39:5, 67:5 and others) in Uthmanic rasm with translation.

---

## [2.1.2] — 2026-03-25

### Fixed — Ask a Question page + slug corrections

1. **Ask a Question page** now auto-created on theme activation with the `page-ask-a-question.php` template assigned. The template already existed (form with name, email, topic selector, question textarea, consent checkbox, search sidebar, popular questions) with full AJAX form handling in functions.php — but the WordPress page itself was never being created, so the URL returned 404.

2. **URL slug corrections:**
   - `/about` → `/about-compelling-evidence`
   - `/contact` → `/contact-compelling-evidence`
   - Updated in: `footer.php` (4 links), `front-page.php` (1 link), `ce-secondary-pages.php` (page creation + internal links)

3. **Template assignment** — the page creation loop now supports a `template` key. Ask a Question page gets `page-ask-a-question.php` set via `_wp_page_template` post meta on creation.

### Secondary pages summary (all 5)

| Page | Slug | Template |
|---|---|---|
| About This Site | `/about-compelling-evidence` | Default (`page.php`) |
| Editorial Policy | `/editorial-policy` | Default (`page.php`) |
| Privacy Policy | `/privacy-policy` | Default (`page.php`) |
| Contact | `/contact-compelling-evidence` | Default (`page.php`) |
| Ask a Question | `/ask-a-question` | `page-ask-a-question.php` |

---

## [2.1.1] — 2026-03-25

### Added — Secondary pages (About, Editorial Policy, Privacy Policy, Contact)

**New file: `inc/ce-secondary-pages.php`** — 4 WordPress pages auto-created on theme activation:

1. **About This Site** (`/about`) — What the site is, what it isn't, who it's for, how to engage. Clearly states: built for atheists, evidence-first, no emotional manipulation, not affiliated with any organisation.

2. **Editorial Policy** (`/editorial-policy`) — Intellectual honesty commitment: strongest version of every objection, peer-reviewed sources, Arabic citations with Uthmanic rasm, no strawmanning, no fear-based arguments. Correction policy.

3. **Privacy Policy** (`/privacy-policy`) — Quiz/journey progress stored in localStorage (never sent to server). Engagement votes use anonymised IP hash. No advertising trackers, no social media pixels, no third-party analytics, no data monetisation. Essential cookies only.

4. **Contact** (`/contact`) — Routes to Ask a Question page for content questions. Error reporting. General enquiries via email.

### Changed — Footer updated

- **4th column added:** "About" column with links to all 4 secondary pages
- **Footer-bottom links updated:** Now shows About, Editorial Policy, Privacy Policy, Contact (was: Find my path, Articles)
- **Footer grid:** Updated from `1.6fr 1fr 1fr` to `1.6fr 1fr 1fr 1fr` (collapses to 2-col at 760px, 1-col at 480px as before)

---

## [2.1.0] — 2026-03-25

### Changed — Front page CTA stats updated to real numbers

| Stat | Before | After |
|---|---|---|
| Top-left | 31× Ar-Rahman repeated | **87** Articles addressing the hardest questions |
| Top-right | 6+ Core questions explored | **14** Personalised journey paths |
| Bottom-left | 1.9B Muslims | 1.9B Muslims (unchanged) |
| Bottom-right | ∞ Reasons | ∞ Reasons (unchanged) |

The old "31× Ar-Rahman" and "6+ core questions" were leftover from the initial build when the site had 6 articles. Now reflects the actual scale: 87 articles, 14 journey paths.

---

## [2.0.9] — 2026-03-25

### Fixed — Nav visibility and journey-entry section polish

1. **Nav bar now has a subtle border when scrolled** — `border-bottom: 1px solid rgba(107,47,160,0.2)` on `.ce-nav.scrolled` so the nav is clearly separated from dark content sections beneath it. Featured strip also gets a bottom border.

2. **Journey-entry section tightened** — Reduced vertical padding from 5rem/4rem to 4rem/3.5rem. Background opacity increased from 0.6 to 0.85 for better contrast against the nav/strip above.

3. **Stats row polished** — The "3 started · 3 completed · View all →" progress stats now use proper CSS classes (`.je-stats`, `.je-stats-sep`) instead of inline styles. Dot separator, consistent muted colour, proper font sizing. Removed all inline styles from the container div and the JS-generated HTML.

---

## [2.0.8] — 2026-03-25

### Added — New article: "If God Answers Prayer, Why Can't You Demonstrate It?"

**Gap identified:** A Malay ex-Muslim atheist's objections were mapped against all 14 journey paths. 7 of 8 objections were directly addressed. The one gap: the dua/prayer challenge based on Quran 40:60 ("Call upon Me; I will respond to you") — a common ex-Muslim objection that demands empirical demonstration of prayer's efficacy.

**New article** in `articles-data-4.php` (article #87):
- **Slug:** `if-god-answers-prayer-why-cant-you-prove-it`
- **Topic:** Objections
- **Arabic citations:** 3 Quranic verses (Ghafir 40:60, Al-Baqarah 2:186, Al-Anbiya 21:23) + 1 hadith (Sahih Muslim 2735)

**Core argument structure:**
1. The objection assumes prayer is a causal mechanism (input → output). But "istijāba" (respond) is relationship language, not transactional language.
2. Category error: demanding laboratory conditions for a conscious relationship. Love, trust, moral conviction — none are demonstrable under controlled conditions either.
3. The STEP trial tested third-party intercessory prayer as a remote causal mechanism — not personal supplication in a relationship context.
4. The deeper epistemological issue: if the only valid knowledge is empirically testable under controlled conditions, then logic, morality, consciousness, the reliability of reason, and the reality of the past all fall outside existence too.
5. The Quran's claim is responsiveness, not performance on demand.

**Linked from 4 journey paths** (Horizon chapter go-deeper): new-atheist, ex-believer, muslim-doubts, antitheist — the personas most likely to raise this objection.

---

## [2.0.7] — 2026-03-25

### Fixed — No duplicate articles between featured strip and front page grid

The 5 featured strip articles (Purpose of Life, Does God Exist, Why Does Anything Exist, Fine-Tuning, Problem of Evil) are now excluded from the front page "Key Questions" grid via `post__not_in`. The grid pulls the next 6 articles by menu_order that aren't already in the strip — so the reader sees 11 unique articles on the front page instead of seeing the same ones twice.

---

## [2.0.6] — 2026-03-25

### Changed — Featured strip expanded from 2 to 5 articles

"Read first" strip now shows 5 key entry-point articles following the Volume I argument flow:
1. What is the Purpose of Life?
2. Does God Exist?
3. Why Does Anything Exist? (cosmological)
4. The Universe Is Absurdly Specific (fine-tuning)
5. If God Is Good, Why Suffering? (theodicy)

Strip layout changed from `flex-wrap: wrap` to horizontal scroll with hidden scrollbar, so all 5 items stay on one clean line with swipe/scroll on narrower viewports.

### Fixed — Logo colours in quiz.html

Quiz page logo was still white+purple. Fixed to red+white matching all other pages.

---

## [2.0.5] — 2026-03-25

### Fixed — Logo colour consistency + footer overflow

1. **Logo now red-white everywhere.** "Compelling" = `#e05252` (coral red), "Evidence" = white. Previously the nav text fallback used white + purple and the footer used white + purple — inconsistent with the custom logo image. Now both `.nav-logo-ce`/`.nav-logo-ev` and `.footer-logo-ce`/`.footer-logo-ev` match the brand: red "Compelling", white "Evidence".

2. **Footer grid overflow.** The Topics column text was being cut off at the right edge on some viewport widths. Added `overflow: hidden` to `.footer-grid` to contain the columns.

---

## [2.0.4] — 2026-03-25

### Fixed — Front page scroll-hint overlap removed

The scroll-hint tiles (Existence of God, Evil & Suffering, Objections) were positioned absolutely at the bottom of the hero with `overflow: visible`, causing them to overlap with the prompt chips on desktop. Previous fixes (extra padding, z-index changes) didn't resolve it because the absolute positioning inherently conflicts with the hero's flex layout.

**Fix:** Hidden the scroll-hint globally (`display: none`). These tiles are completely redundant — the quick-access section immediately below the hero already provides the same topic links with better UX (full cards with descriptions). Restored hero bottom padding from 7rem back to 4rem.

The HTML remains in `front-page.php` (with `aria-hidden="true"`) in case it's needed later, but the CSS hides it on all viewports.

---

## [2.2.4] — 2026-03-26

### Added — Article content sync module

**Problem:** Article creation functions skip existing posts (`if ($existing) continue;`). So content changes in the theme data files — new Quranic citations, updated text, entire new articles — never propagate to the WordPress database. The live site shows the original content from when articles were first created, not the current data files.

**New file: `inc/ce-content-sync.php`** — Runs ONCE per version (flagged by `ce_content_sync_2_2_4`). On first admin page load after theme update:

1. Reads all 5 article data files (articles-data.php through articles-data-next.php)
2. For each article: if it exists in the database, **updates its content, title, and excerpt** from the data file
3. If an article doesn't exist yet (new articles added in this version), **creates it** with the correct topic assignment
4. Sets flag to prevent re-running

**This fixes:**
- Quran 51:56 citation (وَمَا خَلَقْتُ ٱلْجِنَّ وَٱلْإِنسَ إِلَّا لِيَعْبُدُونِ) not appearing on the live Purpose of Life article
- All 7 new articles added since initial deployment (prayer, sword, LGBTQ+, honour killings, qira'at, cosmology, emotional vs intellectual apostasy, algorithm) being created if they don't exist
- Updated content in any existing article (Uthmanic rasm citations, expanded arguments, corrected text)

**For future updates:** Bump the version key in `ce-content-sync.php` (e.g., `ce_content_sync_2_3_0`) and it will re-run on the next admin page load, syncing any new content changes.

**Execution order on admin_init:**
1. Category migration (v2.2.0) — creates terms, remaps articles
2. Content sync (v2.2.4) — updates content, creates missing articles
3. Secondary pages (v2.1.2) — creates About/Contact/etc pages

Each has its own flag and runs independently.

---

## [2.2.3] — 2026-03-25

### Added — Uthmani Quran font infrastructure + Amiri via Google Fonts

**Problem:** The uploaded OthmanyFonts are legacy 1990s fonts with custom encoding — zero Unicode Arabic characters. They cannot render standard Arabic text on the web.

**Solution:** Two-layer font system for Quranic verses:

1. **Self-hosted Uthmani font** (`@font-face` in main.css) — loads `assets/fonts/uthmani-quran.woff2` if present. Uses `unicode-range` to only activate for Arabic characters. `font-display: swap` ensures text renders immediately with fallback while font loads. See `assets/fonts/README.md` for instructions on adding the KFGQPC Uthmanic Script HAFS font.

2. **Amiri via Google Fonts** (immediate fallback) — added to the existing Google Fonts enqueue in functions.php. Amiri is designed by Khaled Hosny specifically for Arabic typesetting and handles Quranic text well. Loads weights 400 and 700.

**Font priority chain:**
- `.quran-arabic`: `'Uthmani Quran'` → `'Amiri'` → `'Traditional Arabic'` → `'Scheherazade New'` → `serif`
- `.hadith-arabic`: `'Amiri'` → `'Traditional Arabic'` → `'Scheherazade New'` → `serif` (no Uthmani — per requirement, Uthmani is Quran-only)

**Files changed:**
- `assets/css/main.css` — `@font-face` declaration + `.quran-arabic` font-family updated
- `assets/css/print.css` — split `.quran-arabic` and `.hadith-arabic` rules (were combined), Quran gets Uthmani
- `functions.php` — Amiri added to Google Fonts enqueue URL
- `assets/fonts/README.md` — instructions for adding KFGQPC font

**To complete the setup:** Download KFGQPC Uthmanic Script HAFS, convert to woff2, place as `assets/fonts/uthmani-quran.woff2`. The theme handles the rest automatically.

---

## [2.2.2] — 2026-03-25

### Added — Database migration script for category restructure

**New file: `inc/ce-migration-2-2-0.php`** — Runs ONCE on first admin page load after theme update. Handles the gap between theme files (which now reference new categories) and the WordPress database (which still has old categories from the previous version).

**What it does:**

1. **Creates 10 new taxonomy terms** if they don't exist: Does God Exist?, The Problem of Evil, Ethics Without God?, Science & Evidence, Examining the Quran, Examining the Sources, History & Context, Rights & Freedom, The Inner Journey, The Bigger Picture.

2. **Simple renames** — articles under old terms that map 1:1 to new terms get moved automatically:
   - Existence of God → Does God Exist?
   - Evil & Suffering → The Problem of Evil
   - Ethics & Morality → Ethics Without God?
   - Science & Faith + Consciousness → Science & Evidence (merged)
   - The Quran → Examining the Quran
   - Science & Islam → History & Context
   - Purpose & Meaning + The Next Question → The Bigger Picture (merged)

3. **Per-article redistribution** — 60+ articles from "Objections", "Doubts & Questions", and "Hard Questions" are individually reassigned to their correct new category based on article slug. Each assignment matches the finalised structure from v2.2.0.

4. **Slug rename** — `purpose-of-life-islam` → `purpose-of-life` via `wp_update_post`.

5. **Cleanup** — all 12 old taxonomy terms are deleted ONLY if they have no remaining articles assigned (safety check prevents data loss if migration is partial).

6. **Bridge articles** — the 3 articles in `articles-data-next.php` (Does God Communicate, Authentic Revelation, Competing Claims) are assigned to The Bigger Picture.

7. **Flagged** — uses `ce_category_migration_2_2_0` option to ensure it runs exactly once. Debug logging to `error_log` if `WP_DEBUG` is true.

**Included in `functions.php`** via `require_once`.

### Why this is necessary

WordPress stores taxonomy assignments in the database (`wp_term_relationships`), not in theme files. Updating the theme replaces the PHP/CSS/JS but does NOT change which terms exist or which articles are assigned to which terms. Without this migration, the archive page would show empty categories (new terms with no articles) while articles remain orphaned under old terms that the archive no longer looks for.

---

## [2.2.1] — 2026-03-25

### Fixed — Purpose of Life slug + Quran 51:56 citation

1. **Slug renamed:** `purpose-of-life-islam` → `purpose-of-life`. Updated across 17 references in 15 files (articles-data.php, header.php, front-page.php, functions.php, and 12 journey HTML files).

2. **Quran 51:56 citation updated** to proper Uthmanic rasm with wasla alif (ٱ): `وَمَا خَلَقْتُ ٱلْجِنَّ وَٱلْإِنسَ إِلَّا لِيَعْبُدُونِ` — "I did not create jinn and humans except to worship Me." Citation format standardised from `<span>` to `<div>` elements matching the rest of the site. Hadith Qudsi citation in the same article also standardised.

---

## [2.2.0] — 2026-03-25

### Major — Full category restructure (12 old → 10 new categories)

**The entire article taxonomy has been restructured** for neutral, investigative framing that doesn't signal a religious agenda. All 93 articles remapped. 10 top-level categories with 3 subcategories under "The Inner Journey."

#### Old → New category mapping

| Old Category | New Category | Rationale |
|---|---|---|
| Existence of God | **Does God Exist?** | Question form invites inquiry |
| Evil & Suffering | **The Problem of Evil** | Clearer, academic framing |
| Ethics & Morality | **Ethics Without God?** | Question positions it as genuine inquiry |
| Science & Faith + Consciousness | **Science & Evidence** | Merged — empirical focus |
| The Quran | **Examining the Quran** | "Examining" = investigative, not devotional |
| *(new)* | **Examining the Sources** | Hadith + Sirah combined |
| Science & Islam | **History & Context** | Broader civilisational questions |
| *(from Doubts/Objections)* | **Rights & Freedom** | Leads with what secular readers care about |
| Doubts & Questions | **The Inner Journey** | Personal/pastoral content, with subcategories |
| Purpose & Meaning + The Next Question | **The Bigger Picture** | Merged — purpose, claims, bridge articles |
| Objections | *(redistributed)* | Split across Does God Exist?, Problem of Evil, Rights & Freedom, etc |
| Hard Questions | *(redistributed)* | Content moved to Rights & Freedom |

#### New structure

| # | Category | Count | Subcategories |
|---|---|---|---|
| 1 | Does God Exist? | 13 | — |
| 2 | The Problem of Evil | 8 | — |
| 3 | Ethics Without God? | 5 | — |
| 4 | Science & Evidence | 10 | — |
| 5 | Examining the Quran | 11 | — |
| 6 | Examining the Sources | 7 | — |
| 7 | History & Context | 7 | — |
| 8 | Rights & Freedom | 10 | — |
| 9 | The Inner Journey | 16 | Understanding Doubt (6), The Emotional Cost (5), What Happens Next (5) |
| 10 | The Bigger Picture | 8 | — |

#### Files changed

- **All `articles-data*.php`** — topic fields updated to new category names
- **`articles-data-next.php`** — topic + order fields added to 3 bridge articles (were missing)
- **`archive-ce_article.php`** — topic_order, topic_descs, subcategory rendering logic + CSS for The Inner Journey
- **`functions.php`** — all topic_map arrays updated (3 instances), term slugs updated
- **`footer.php`** — fallback topic links updated to new slugs/names
- **`front-page.php`** — scroll-hint topic links updated

#### Design principle

Category names were chosen to feel like a research site, not a dawah site. An atheist browsing the archive sees: "Does God Exist?", "Science & Evidence", "Rights & Freedom" — questions and investigations, not doctrines. The word "Islam" does not appear in any category name.

---

## [2.1.6] — 2026-03-25

### Added — 2 research-gap articles (92 → 94, then deduplicated to 93)

Two articles based on findings from Cottee (2015), Richter (2025), and other ex-Muslim research:

1. **"Did Your Heart Leave Before Your Head?"** — Emotional vs intellectual apostasy. Some leave through pain first and rationalise later; others follow the evidence to a conclusion they didn't want. Which pathway determines how you engage with the evidence. Draws on Cottee's distinction and Richter's phase model (anger → reconciliation).

2. **"The Algorithm That Deconverted You"** — The internet deconversion pipeline: YouTube atheists, Reddit communities, recommendation algorithms that create self-reinforcing information diets. Acknowledges the reader's actual path to disbelief, then points out that simplified online arguments aren't the strongest versions. "You've seen the trailer. Now examine the evidence."

### Removed — Duplicate "Purpose of Life" article

"What Is The Purpose of Life?" existed in both `articles-data-2.php` (slug: `purpose-of-life`) and `articles-data.php` (slug: `purpose-of-life-islam`). Removed the shorter version from `articles-data-2.php`. The longer version in `articles-data.php` is the canonical one.

**Article count: 93 unique articles, 0 duplicates.**

---

## [2.1.5] — 2026-03-25

### Changed — Articles archive: bigger fonts + accordion topic sections

**Font size increases** (all in `archive-ce_article.php` inline styles):

| Element | Before | After |
|---|---|---|
| Topic section name | 1.2rem | **1.5rem** |
| Topic description | 0.88rem | **1rem** |
| Article title | 0.95rem | **1.1rem** |
| Article excerpt | 0.82rem | **0.92rem** |
| Article number | 0.62rem | **0.72rem** |
| Read time | 0.62rem | **0.72rem** |
| Topic count badge | 0.65rem | **0.72rem** |
| Article row padding | 0.75rem | **0.9rem** |

**Accordion topic sections:** Each topic group is now collapsible. Click the topic header to expand/collapse the article list beneath it. All sections start expanded (class `open`). The header shows a chevron (▼) that rotates on toggle. Smooth `max-height` + `opacity` transition (0.35s). Header gets a subtle hover background. The description, article list, and "show all" button are all inside the collapsible body.

---

## [2.1.4] — 2026-03-25

### Fixed — Progress page shows only active paths + persona names everywhere

1. **Progress page now gated:** If no paths have been started, shows a quiz CTA ("You haven't started a journey yet") instead of all 14 empty cards. Only paths the user has actually started or completed are shown.

2. **"Path 01/02/03..." replaced with real persona names everywhere.** All 6 files updated:
   - `page-progress.php` — "The New Atheist", "The Agnostic", etc.
   - `front-page.php` — returning visitor stats
   - `functions.php` — journey registration
   - `single-ce_article.php` — article sidebar "Revisit journey" link
   - `single.php` — same as above

3. **Missing personas added:** Freethinker and True Muslim were missing from PATH_NAMES in `single-ce_article.php`, `single.php`, and the clearProgress function. Added to all.

4. **Stale SCREENS array fixed** in `single-ce_article.php`, `single.php`, and `front-page.php` — was missing Resonance and Transmission chapters (8 screens instead of 10). Now all 10 screens present.

---

## [2.1.3] — 2026-03-25

### Fixed — Page creation now works on already-activated themes

Changed `ce_create_secondary_pages()` from `after_switch_theme` hook (only fires on theme switch) to `admin_init` with an option flag (`ce_secondary_pages_created`). Now creates all 5 secondary pages (About, Editorial Policy, Privacy Policy, Contact, Ask a Question) on the first admin page load after the update. Won't re-run once the flag is set.

### Added — 5 gap articles closing all remaining coverage holes

Total articles: 87 → **92**. All 5 genuine gaps from the comprehensive objection audit are now filled:

1. **"Did Islam Spread by the Sword?"** (Objections) — Conquest vs conversion distinction. Bulliet's conversion rate data (200–300 year lag). Dhimmi system as evidence against forced conversion. Quran 2:256 ("no compulsion"). Rules of engagement. Honest: some forced conversions happened, but they were not the norm and contradicted the Quran's own teaching.

2. **"Islam and Same-Sex Attraction: The Question That Can't Be Avoided"** (Objections) — Quran's position stated directly (no revisionism). Act vs attraction distinction. The ibtila' (trial) framework. "God does not burden a soul beyond its capacity" (2:286). No justification for hatred or violence. Bottom line: the question is whether you trust an omniscient God's judgment where it differs from yours.

3. **"Honour Killings: Culture, Not Islam"** (Objections) — Pre-Islamic practice predating Islam by centuries. Occurs across religions and cultures. Islamic law explicitly prohibits extrajudicial killing (17:33). No school of jurisprudence permits it. Even capital punishment requires state judicial process, not family vigilantism.

4. **"The Quran Has Variant Readings — Does That Disprove Preservation?"** (The Quran) — Qira'at explained: variant vocalisations of the same consonantal rasm. Hafs vs Warsh differences are minor (vowels, grammar). No theological contradictions. Preservation of variants is evidence FOR meticulous transmission, not against it. Sana'a palimpsest addressed.

5. **"Flat Earth, Seven Heavens, and Shooting Stars: Does the Quran's Cosmology Disprove It?"** (Science & Faith) — Dahaha etymology (expansion, not flattening). Yukawwiru (wrapping night/day = spherical rotation). Seven heavens: multiple interpretations in classical tradition. Phenomenological language vs scientific claims. The Quran is not a science textbook.

**Arabic citations added:** 10 new Quranic citations across all 5 articles (2:256, 7:80, 2:286, 17:33, 79:30, 39:5, 67:5 and others) in Uthmanic rasm with translation.

---

## [2.1.2] — 2026-03-25

### Fixed — Ask a Question page + slug corrections

1. **Ask a Question page** now auto-created on theme activation with the `page-ask-a-question.php` template assigned. The template already existed (form with name, email, topic selector, question textarea, consent checkbox, search sidebar, popular questions) with full AJAX form handling in functions.php — but the WordPress page itself was never being created, so the URL returned 404.

2. **URL slug corrections:**
   - `/about` → `/about-compelling-evidence`
   - `/contact` → `/contact-compelling-evidence`
   - Updated in: `footer.php` (4 links), `front-page.php` (1 link), `ce-secondary-pages.php` (page creation + internal links)

3. **Template assignment** — the page creation loop now supports a `template` key. Ask a Question page gets `page-ask-a-question.php` set via `_wp_page_template` post meta on creation.

### Secondary pages summary (all 5)

| Page | Slug | Template |
|---|---|---|
| About This Site | `/about-compelling-evidence` | Default (`page.php`) |
| Editorial Policy | `/editorial-policy` | Default (`page.php`) |
| Privacy Policy | `/privacy-policy` | Default (`page.php`) |
| Contact | `/contact-compelling-evidence` | Default (`page.php`) |
| Ask a Question | `/ask-a-question` | `page-ask-a-question.php` |

---

## [2.1.1] — 2026-03-25

### Added — Secondary pages (About, Editorial Policy, Privacy Policy, Contact)

**New file: `inc/ce-secondary-pages.php`** — 4 WordPress pages auto-created on theme activation:

1. **About This Site** (`/about`) — What the site is, what it isn't, who it's for, how to engage. Clearly states: built for atheists, evidence-first, no emotional manipulation, not affiliated with any organisation.

2. **Editorial Policy** (`/editorial-policy`) — Intellectual honesty commitment: strongest version of every objection, peer-reviewed sources, Arabic citations with Uthmanic rasm, no strawmanning, no fear-based arguments. Correction policy.

3. **Privacy Policy** (`/privacy-policy`) — Quiz/journey progress stored in localStorage (never sent to server). Engagement votes use anonymised IP hash. No advertising trackers, no social media pixels, no third-party analytics, no data monetisation. Essential cookies only.

4. **Contact** (`/contact`) — Routes to Ask a Question page for content questions. Error reporting. General enquiries via email.

### Changed — Footer updated

- **4th column added:** "About" column with links to all 4 secondary pages
- **Footer-bottom links updated:** Now shows About, Editorial Policy, Privacy Policy, Contact (was: Find my path, Articles)
- **Footer grid:** Updated from `1.6fr 1fr 1fr` to `1.6fr 1fr 1fr 1fr` (collapses to 2-col at 760px, 1-col at 480px as before)

---

## [2.1.0] — 2026-03-25

### Changed — Front page CTA stats updated to real numbers

| Stat | Before | After |
|---|---|---|
| Top-left | 31× Ar-Rahman repeated | **87** Articles addressing the hardest questions |
| Top-right | 6+ Core questions explored | **14** Personalised journey paths |
| Bottom-left | 1.9B Muslims | 1.9B Muslims (unchanged) |
| Bottom-right | ∞ Reasons | ∞ Reasons (unchanged) |

The old "31× Ar-Rahman" and "6+ core questions" were leftover from the initial build when the site had 6 articles. Now reflects the actual scale: 87 articles, 14 journey paths.

---

## [2.0.9] — 2026-03-25

### Fixed — Nav visibility and journey-entry section polish

1. **Nav bar now has a subtle border when scrolled** — `border-bottom: 1px solid rgba(107,47,160,0.2)` on `.ce-nav.scrolled` so the nav is clearly separated from dark content sections beneath it. Featured strip also gets a bottom border.

2. **Journey-entry section tightened** — Reduced vertical padding from 5rem/4rem to 4rem/3.5rem. Background opacity increased from 0.6 to 0.85 for better contrast against the nav/strip above.

3. **Stats row polished** — The "3 started · 3 completed · View all →" progress stats now use proper CSS classes (`.je-stats`, `.je-stats-sep`) instead of inline styles. Dot separator, consistent muted colour, proper font sizing. Removed all inline styles from the container div and the JS-generated HTML.

---

## [2.0.8] — 2026-03-25

### Added — New article: "If God Answers Prayer, Why Can't You Demonstrate It?"

**Gap identified:** A Malay ex-Muslim atheist's objections were mapped against all 14 journey paths. 7 of 8 objections were directly addressed. The one gap: the dua/prayer challenge based on Quran 40:60 ("Call upon Me; I will respond to you") — a common ex-Muslim objection that demands empirical demonstration of prayer's efficacy.

**New article** in `articles-data-4.php` (article #87):
- **Slug:** `if-god-answers-prayer-why-cant-you-prove-it`
- **Topic:** Objections
- **Arabic citations:** 3 Quranic verses (Ghafir 40:60, Al-Baqarah 2:186, Al-Anbiya 21:23) + 1 hadith (Sahih Muslim 2735)

**Core argument structure:**
1. The objection assumes prayer is a causal mechanism (input → output). But "istijāba" (respond) is relationship language, not transactional language.
2. Category error: demanding laboratory conditions for a conscious relationship. Love, trust, moral conviction — none are demonstrable under controlled conditions either.
3. The STEP trial tested third-party intercessory prayer as a remote causal mechanism — not personal supplication in a relationship context.
4. The deeper epistemological issue: if the only valid knowledge is empirically testable under controlled conditions, then logic, morality, consciousness, the reliability of reason, and the reality of the past all fall outside existence too.
5. The Quran's claim is responsiveness, not performance on demand.

**Linked from 4 journey paths** (Horizon chapter go-deeper): new-atheist, ex-believer, muslim-doubts, antitheist — the personas most likely to raise this objection.

---

## [2.0.7] — 2026-03-25

### Fixed — No duplicate articles between featured strip and front page grid

The 5 featured strip articles (Purpose of Life, Does God Exist, Why Does Anything Exist, Fine-Tuning, Problem of Evil) are now excluded from the front page "Key Questions" grid via `post__not_in`. The grid pulls the next 6 articles by menu_order that aren't already in the strip — so the reader sees 11 unique articles on the front page instead of seeing the same ones twice.

---

## [2.0.6] — 2026-03-25

### Changed — Featured strip expanded from 2 to 5 articles

"Read first" strip now shows 5 key entry-point articles following the Volume I argument flow:
1. What is the Purpose of Life?
2. Does God Exist?
3. Why Does Anything Exist? (cosmological)
4. The Universe Is Absurdly Specific (fine-tuning)
5. If God Is Good, Why Suffering? (theodicy)

Strip layout changed from `flex-wrap: wrap` to horizontal scroll with hidden scrollbar, so all 5 items stay on one clean line with swipe/scroll on narrower viewports.

### Fixed — Logo colours in quiz.html

Quiz page logo was still white+purple. Fixed to red+white matching all other pages.

---

## [2.0.5] — 2026-03-25

### Fixed — Logo colour consistency + footer overflow

1. **Logo now red-white everywhere.** "Compelling" = `#e05252` (coral red), "Evidence" = white. Previously the nav text fallback used white + purple and the footer used white + purple — inconsistent with the custom logo image. Now both `.nav-logo-ce`/`.nav-logo-ev` and `.footer-logo-ce`/`.footer-logo-ev` match the brand: red "Compelling", white "Evidence".

2. **Footer grid overflow.** The Topics column text was being cut off at the right edge on some viewport widths. Added `overflow: hidden` to `.footer-grid` to contain the columns.

---

## [2.0.4] — 2026-03-25

### Fixed — Removed scroll-hint overlay on front page

The scroll-hint tiles ("Existence of God", "Evil & Suffering", "Objections") were positioned absolutely at the bottom of the hero and bled through behind the prompt chips on desktop. The prompt chips already serve the same exploratory purpose, so the scroll-hint is now hidden globally with `display: none`. The HTML remains in the template for potential future use but is no longer rendered.

---

## [2.0.3] — 2026-03-25

### Fixed — Sidebar sticky with invisible scrollbar

The sidebar is taller than the viewport on most articles (reading progress + find your path + more questions). Plain `position: sticky` with `top` can't work — it pins the top, but the bottom hangs off-screen and the user loses access to lower widgets.

Fix: `max-height: calc(100vh - offset)` + `overflow-y: auto` (so the sidebar content is scrollable within its sticky container) + **hidden scrollbar** via `scrollbar-width: none` (Firefox) + `::-webkit-scrollbar { display: none }` (Chrome/Safari/Edge). Users can still scroll the sidebar with mousewheel/trackpad — the scrollbar is just invisible.

Applied in `main.css`, `single-ce_article.php`, and `single.php`.

---

## [2.0.2] — 2026-03-25

### Fixed — Reverted sidebar inner scrollbar

The v2.0.1 fix for "reading progress disappears on scroll" added `max-height` + `overflow-y: auto` to the sidebar, which created an ugly inner scrollbar. Reverted to plain `position: sticky; top: offset` — CSS sticky natively handles tall elements correctly: the sidebar scrolls with the page until its bottom edge reaches the viewport bottom, then sticks. No inner scrollbar needed.

---

## [2.0.1] — 2026-03-24

### Fixed — Sidebar scroll, front page overlap, stale quiz references

1. **Reading progress disappears on scroll** — Sidebar was sticky but taller than viewport. Added `max-height: calc(100vh - offset)` and `overflow-y: auto` to `.article-sidebar` on desktop (min-width: 901px). Now the sidebar scrolls independently within its sticky container. Applied in `main.css`, `single-ce_article.php`, and `single.php`.

2. **Front page prompt chips overlapping explore tiles** — Scroll-hint tiles (Existence of God, Evil & Suffering, Objections) bled into the search area on desktop. Increased hero bottom padding from 4rem to 7rem, lowered scroll-hint z-index to 0 (search-wrap remains at 100).

3. **"Five questions. Ninety seconds." stale in 4 places** — Updated to "Ten questions. Two minutes." in `front-page.php`, `page-journeys.php` (×2), and `quiz.html`. Also fixed "90 seconds" → "2 minutes" in journeys page CTA.

---

## [2.0.0] — 2026-03-24

### Added — Volume I → II bridge (three-volume architecture)

**Major structural milestone:** The site now explicitly presents itself as a three-volume argument. Volume I (Does God Exist?) is complete. Volume II (Which God? One or Many?) and Volume III (Why Islam? Why Not Others?) are signposted with full chapter previews.

**Changes to all 13 standard journey files:**

1. **Volume II bridge section** replaces the old article-link "next question" block in the conclusion screen. Each persona gets a tailored hook:
   - Agnostic: "You followed the evidence to a Creator. Now: has this Creator spoken?"
   - New Atheist: "You accepted the convergence — reluctantly. Now the hardest question remains."
   - Antitheist: "You accepted God exists despite the harm. Is there a tradition that matches the God the evidence points to?"
   - Ex-Believer: "You left a tradition that failed you. But was it the right tradition?"
   - (and 9 more persona-specific hooks)

2. **Volume II preview screen** (`s-v2-preview`) added after the conclusion in every standard journey. Shows:
   - 8 planned chapters in a 2-column card grid (Criterion, Convergence, Preservation, Coherence, Address, Character, Verification, Reckoning)
   - "In development — coming soon" status badge
   - Bridge articles for immediate reading (Does God communicate?, What would authentic revelation look like?, How do we evaluate competing claims?)
   - Back-to-conclusion navigation

3. **Navigation updated**: A purple separator dot and "Volume II" nav dot added after the conclusion dot, with distinct purple styling to differentiate from Volume I's teal dots.

4. **JS screens array** extended: `v2-preview` added as the 11th screen with "Volume II" label. All goTo() navigation, screen switching, and progress tracking work seamlessly with the new screen.

5. **Volume bridge CSS** added to all 13 journey files: `.vol-bridge`, `.v2-preview`, `.v2-chapters-grid`, `.v2-ch-card`, `.btn-vol2`, `.vol-coming`, `.np-vol-sep`, `.np-dot.v2-dot` — all responsive with 480px breakpoint for chapter grid.

**True Muslim journey** unchanged — it is a self-contained pastoral/equipping path that does not participate in the three-volume structure.

**No content changes** to any existing chapters, choices, or articles. This is purely a structural bridge.

### Technical
- 13 journey HTML files modified (all except true-muslim)
- Each file gains: ~200 lines CSS, ~80 lines bridge HTML, ~60 lines preview screen HTML, nav dot, JS array update
- Average file size increase: ~15KB per journey
- Total new screens: 13 (one per standard journey)

---

## [1.9.3] — 2026-03-24

### Fixed — Quiz scoring matrix rebalanced for all 14 personas

**Problem identified:** Mathematical analysis revealed 5 personas were severely underrepresented in the newer questions (Q6-Q10), which were built primarily around true-muslim detection. Secular-humanist had ZERO scoring in 4 out of 10 questions (max achievable score: 12, winning margin: only 2 points). New-atheist had ZERO in 3 questions (max: 14, margin: 4). Both were at risk of being misclassified.

**31 scoring adjustments applied** — adding weights to existing answer options where the logical mapping was clear but the scoring was missing:

| Persona | Before (max/margin/zeros) | After (max/margin/zeros) |
|---|---|---|
| secular-humanist | 12 / 2 / 4 zeros | **19 / 4 / 0 zeros** |
| new-atheist | 14 / 4 / 3 zeros | **19 / 6 / 0 zeros** |
| materialist | 15 / 3 / 2 zeros | **18 / 5 / 0 zeros** |
| antitheist | 18 / 6 / 3 zeros | **22 / 6 / 0 zeros** |
| classical-atheist | 18 / 8 / 3 zeros | **21 / 9 / 0 zeros** |
| ex-believer | 18 / 7 / 2 zeros | **21 / 10 / 0 zeros** |
| spiritual-seeker | 19 / 7 / 3 zeros | **22 / 9 / 0 zeros** |
| deist | 18 / 9 / 3 zeros | **20 / 9 / 1 zero** |

**Collision test:** All 14 personas confirmed to win when their ideal answers are selected. Minimum winning margin increased from 2 to 4 points.

**Remaining thematic zeros (5 total, all intentional):**
- apatheist zero in Q3 ("What would change your mind?") — apatheists by definition don't engage
- deist zero in Q4 ("What drove you from religion?") — deists didn't leave religion
- freethinker zero in Q6 ("Current relationship with faith?") — freethinkers span belief/non-belief
- muslim-doubts zero in Q9 ("Strongest case for God?") — they're questioning, not evaluating
- true-muslim zero in Q5 ("Most convincing about your position?") — believers, not defending non-belief

---

## [1.9.2] — 2026-03-24

### Fixed — Stale references and progress page consistency

**Stale "12 paths" references:** Found and fixed 4 remaining occurrences across `page-journeys.php`, `page-progress.php`, `single-ce_article.php`, and `single.php`. All now say "14 paths."

**Progress page missing paths:** `page-progress.php` PATHS array only had 12 entries — missing freethinker (Path 13) and true-muslim (Path 14). Both added.

**Progress page missing screens:** SCREENS array was missing `resonance` and `transmission` (added in v1.6.0 journey restructure) and had no awareness of true-muslim's different screen names. Fixed:
- SCREENS now includes all 10: horizon → singularity → calibration → emergence → entropy → constant → signal → resonance → transmission → conclusion
- Added TM_SCREENS for true-muslim: foundation → understanding → honesty → equipping → compassion → knowledge → mission → conclusion
- `screenPct()` function now takes `pathKey` parameter and uses TM_SCREENS for true-muslim paths

**functions.php valid_screens:** The conditional for true-muslim's different screen names was missing. Re-added the `if ($path === 'true-muslim')` check that routes to the correct valid_screens array.

---

## [1.9.1] — 2026-03-24

### Added — Complete SEO & Schema Markup System

**New file: `inc/ce-seo.php` (295 lines)** — comprehensive SEO module hooked into `wp_head`:

**JSON-LD Structured Data (`@graph` format):**
- `WebSite` schema on every page — with `SearchAction` for Google sitelinks search box
- `Organization` schema — name, URL, logo
- `Article` schema on single articles — headline, description, wordCount, datePublished, dateModified, articleSection, author, publisher, image, mainEntityOfPage
- `BreadcrumbList` schema on articles — Home → Articles → Topic → Article Title
- `CollectionPage` schema on archive/taxonomy pages
- `FAQPage` schema on front page — top 6 articles as FAQ Q&A pairs

**Open Graph meta tags** (9 properties): og:type, og:title, og:description, og:url, og:site_name, og:locale, og:image (1200×630), article:section, article:published_time, article:modified_time

**Twitter Card meta tags** (4 properties): summary_large_image card, title, description, image

**Meta description**: context-aware for articles, archives, taxonomy, front page, search, pages

**Canonical URL**: on all page types

**Hreflang**: en + x-default

**Document title filter**: articles include topic name in title tag ("Article Title — Existence of God")

**Security**: Removed WordPress version meta tag (`wp_generator`)

### Added — Print Stylesheet

**New file: `assets/css/print.css` (277 lines)** — enqueued with `media="print"`:

- Hides all non-content elements: nav, sidebar, engagement footer, buttons, search, footer, progress bars, CTAs
- Clean Georgia/serif typography at 11pt with proper orphan/widow control
- `@page` setup: A4 with 2cm/2.5cm margins
- Quran/hadith citation boxes preserved with borders and RTL Arabic text
- Links show URLs in parentheses (except internal/anchor links)
- Page break control: headers avoid breaks after, citations avoid breaks inside
- Site URL footer automatically appended to printed articles
- All backgrounds forced white, all text forced dark

### Fixed — Front page heading hierarchy
- Promoted hero subtitle from `<p>` to `<h1>` with class `hero-sub` — styled identically via CSS (Cormorant Garamond italic, subtitle weight/size) so visual appearance is unchanged but SEO receives proper h1
- Fixed empty `alt=""` on card thumbnails → now uses `get_the_title()` for descriptive alt text

### Files
- **New:** `inc/ce-seo.php` (295 lines), `assets/css/print.css` (277 lines)
- **Modified:** `functions.php` (added `require_once ce-seo.php`), `front-page.php` (h1 + alt text), `main.css` (h1.hero-sub styling)

---

## [1.9.0] — 2026-03-24

### Changed — Quiz expanded from 9→10 questions, each with 7 options (was 5-6)

**New question (Q10):** "What is your biggest unresolved question?" — 7 options covering the problem of evil, epistemology, cosmic scale ("Why would God care about humans?"), Islam-specific textual/historical claims, science vs religion, moral harm done in religion's name, and "I don't have an unresolved question" (true-muslim/settled believer catch).

**New options added to Q1-Q9 (19 total):**
- Q1: "I was raised religious but drifted gradually" (apatheist signal), "I'm here because someone I care about is questioning" (pastoral/true-muslim)
- Q2: "I believe but want better reasons than inheritance" (true-muslim/muslim-doubts bridge)
- Q3: "Seeing the tradition produce genuinely good people" (compassion-driven conversion)
- Q4: "Nothing drove me away — I'm a believer exploring" (true-muslim reinforcement)
- Q5: "The moral case — religion has done more harm than good" (antitheist moral flavour)
- Q6: "I want to believe but can't yet" (agnostic-seeker bridge), "I practice outwardly but I'm empty inside" (muslim-doubts concealment — the Catch-22)
- Q7: "Feel grateful someone is being honest" (freethinker), "Want to point them to resources" (equipped true-muslim)
- Q8: "I left and want to see if arguments improved" (returning ex-believer), "I'm a believer but specific objections trouble me" (muslim-doubts specific)
- Q9: "The moral argument — without God nothing is truly wrong" (ethics-driven), "Personal experience of millions" (spiritual-seeker experiential)

**Scoring:** 70 total scoring entries (was 51). All new options score appropriately across 14 personas. Q10 scoring differentiates: evil/suffering → ex-believer+muslim-doubts, epistemology → agnostic+scientist, cosmic scale → deist+apatheist, Islam-specific → muslim-doubts, science → scientist+materialist, moral harm → antitheist, no question → true-muslim.

### Changed — Journey reflection choices expanded from 3→6 per chapter

Every chapter in all 14 journeys now offers 6 reflection choices (was 3). The original 3 persona-specific choices are preserved; 3 new universal-but-theme-appropriate choices added per chapter:

- **"I need to think about this more"** — the honest middle ground
- **"This confirms/challenges what I expected"** — meta-cognitive awareness  
- **"I'm skeptical but continuing"** — engaged scepticism

Chapter-specific variations: Calibration adds "The numbers are striking"; Emergence adds "This is the argument I find hardest to dismiss"; Entropy adds "I hadn't considered the moral realism presupposition"; Transmission adds "I'm more open to examining Islam specifically."

**366 new choices** added across 14 journeys. new-atheist journey required full rebuild due to pre-existing structural differences.

### Technical
- `quiz.html`: 10 question blocks × 7 options each = 70 total options, 70 scoring entries, all counter references updated 9→10
- All 14 journey HTML files: reflection sections expanded from 3→6 choices per chapter
- JS validated across all files

---

## [1.8.6] — 2026-03-24

### Fixed — Critical mobile rendering (sidebar overlap, hidden header, hero overlap)

**Root cause identified:** Three separate CSS cascade bugs were making the v1.8.5 responsive fixes ineffective:

1. **Sidebar `position: sticky` was GLOBAL with no media query** — In both `main.css` (line 836) and the inline `<style>` blocks of `single-ce_article.php` and `single.php`, `.article-sidebar { position: sticky }` was declared outside any `@media` block. Since it came AFTER the `@media (max-width: 860px) { position: static }` rule, it overrode the mobile fix on every screen size. **Fix:** Wrapped all sticky rules in `@media (min-width: 901px)` and added `!important` to the mobile `position: static` rule as a safety net.

2. **Page top padding used `--nav-h` (72px) instead of `--total-offset` (116px)** — `.ce-single`, `.ce-archive`, and `.ce-page` all used `padding-top: var(--nav-h)`, which only accounts for the navigation bar height, not the featured strip (44px). Content started 44px too high, hiding the header/logo behind the mobile browser chrome. **Fix:** Changed all three to `var(--total-offset, 116px)`.

3. **Hero scroll-hint tiles overlapped explore section on mobile** — The absolutely-positioned scroll-hint preview tiles (Existence of God, Evil & Suffering, Objections) bled out of the hero section into the quick-access grid below. **Fix:** Hidden scroll-hint on ≤600px with `display: none`.

4. **Search dropdown showed `&AMP;` instead of `&`** — Topic names from WordPress DB contain `&amp;` entities. The AJAX handler returned them raw, then `escHtml()` in JS double-encoded them to `&amp;amp;`, which rendered as literal `&AMP;` (uppercased by CSS `text-transform`). **Fix:** Added `html_entity_decode()` to topic names in the search AJAX handler.

### Files changed
- `main.css`: Sidebar sticky desktop-only, page padding --total-offset, scroll-hint mobile hide, sidebar static !important
- `single-ce_article.php` + `single.php`: Inline sticky → desktop-only, padding → --total-offset
- `functions.php`: Search AJAX topic name entity decoding

---

## [1.8.5] — 2026-03-24

### Fixed — Comprehensive mobile & tablet responsive audit (13 issues)

**CRITICAL fixes:**
1. **Hero `100vh` overflow on mobile** — Added `min-height: 100dvh` with `100vh` fallback. Mobile browsers (especially iOS Safari) calculate 100vh including the address bar area, causing content to be pushed below the visible viewport. `100dvh` uses the dynamic viewport height which accounts for browser chrome.
2. **Article sidebar above content on mobile** — Changed `order: -1` to `order: 1; position: static;` in both `single-ce_article.php` and `single.php`. Previously, on screens ≤860px the reading progress widget, "More Questions" list, and journey return widget all rendered *above* the article text, forcing readers to scroll past widgets to reach content.
3. **Front page said "Browse all 12 paths"** — Updated to "14 paths" (was stale since v1.8.0 True Muslim addition).

**HIGH priority fixes:**
4. **Front page card grid — no tablet step** — Added 2-column layout at ≤900px (was jumping from 3-column directly to 1-column). Now: 3-col (desktop) → 2-col (tablet) → 1-col (≤600px mobile).
5. **Nav actions inaccessible on mobile** — Random Article and Progress links were `display: none` at ≤900px with no alternative. Now injected into hamburger dropdown via JS (`nav-mobile-action` class) on first menu open. CSS styles added for mobile action items.
6. **Featured strip text overflow** — Added `white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 45vw` to `.featured-link` at ≤600px to prevent awkward wrapping on narrow screens.

**MEDIUM priority fixes:**
7. **Engagement share bar (9 buttons) on small screens** — Added 860px breakpoint (share/vote row stacks vertically), 640px (resonance feedback compact), and 420px (smaller icons 34→34px, tighter gaps) to `ce-engagement.css`. Now 3 responsive tiers.
8. **Card text long on mobile** — Added `-webkit-line-clamp: 3` to `.card-text` at ≤600px and responsive title sizing.
9. **Journeys page tablet** — Added 900px breakpoint (padding reduction) and 768px (how-it-works grid collapses to 1-col). Previously only had 640px.
10. **Articles archive tablet** — Added 900px breakpoint (padding reduction, 2-line excerpt clamp). Previously only had 640px.
11. **Search page responsive** — Added 640px breakpoint with stacked search form, tighter padding, hidden result numbers. Fixed top padding to use `--total-offset` instead of `--nav-h` (was not accounting for featured strip height).

**LOW priority (no fix needed):**
12. **new-atheist extra @media blocks** — Confirmed as intentional: that journey has custom content components (constants-grid, gap-table, distinction, two-col) with their own responsive rules. Not a bug.

### Technical
- `main.css`: 100dvh fallback, grid 2-col tablet step, card text clamp, nav-mobile-action styles, featured-link truncation
- `main.js`: Hamburger toggle now injects nav-action links into mobile dropdown
- `single-ce_article.php` + `single.php`: Sidebar order fixed from -1 to 1
- `front-page.php`: "12 paths" → "14 paths"
- `ce-engagement.css`: 3-tier responsive (860/640/420px)
- `page-journeys.php`: Added 900px + 768px breakpoints
- `archive-ce_article.php`: Added 900px breakpoint
- `search.php`: Added 640px breakpoint, fixed top padding

---

## [1.8.4] — 2026-03-24

### Changed — Share bar expanded from 3 to 9 platforms, icon-only design
Replaced the 3-button share bar (X, WhatsApp, Copy) with a comprehensive 9-button icon-only design.

**Platforms (in order):** WhatsApp, Telegram, Facebook, X / Twitter, Reddit, Threads, Email, Copy Link, Native Share (Web Share API — shows automatically on supporting devices).

**Design:** Circular 38px icon-only buttons with platform-specific hover colours (WhatsApp green, Facebook blue, Reddit orange, etc.). Copy link shows a checkmark animation on success. Native share button appears via JS feature detection (not PHP mobile check).

**Why these platforms:** WhatsApp and Telegram are dominant in Muslim-majority countries. Facebook is the largest global platform. X is where intellectual/religious debate happens. Reddit hosts r/islam, r/exmuslim, r/philosophy. Email for personal sharing. Threads for Meta's growing platform.

---

## [1.8.3] — 2026-03-24

### Fixed — Duplicate reading progress indicator on article pages
Removed the inline progress bar (`meta-progress-wrap`) from both `single-ce_article.php` and `single.php`. This element duplicated the sidebar reading progress widget, creating two progress displays on every article page. The thin fixed top bar and sidebar widget remain as the two complementary indicators (ambient awareness + detailed stats).

Removed: inline progress HTML, CSS (6 rules), and JS references to `meta-progress-fill` / `meta-progress-pct` in both templates.

---

## [1.8.2] — 2026-03-24

### Changed — Journey-to-article links completely rebuilt
Replaced the generic `CHAPTER_ARTICLES` object (same 19 articles across all personas, 3 chapters missing links) with **persona-specific article recommendations** covering all chapters.

**Before**: 19 articles linked, same for all 13 personas. Horizon, Resonance, and Transmission had zero links. 67 articles orphaned.

**After**: 77 articles linked (90% coverage). Every chapter in every journey has relevant "Go deeper" links. Article recommendations are persona-specific:
- **Muslim with Doubts**: Horizon links to "Your Doubts Are Not a Disease," "The Good Muslim Paradox," "Is Doubt Permitted?" — not generic epistemology articles
- **Ex-Believer**: Horizon links to "The Anger Is Real," "When Religion Was Imposed," "The Islam I Was Defending"
- **Antitheist**: Horizon links to "Hasn't Religion Caused Harm?," "No Compulsion In Religion," "The Anger Is Real"
- **Scientist**: Horizon links to "What Science Cannot Tell You," "Doesn't Evolution Explain Design?," "Islamic Scientific Tradition"
- **Freethinker**: Horizon links to "The Freethinkers Islam Produced," plus epistemology
- **True Muslim**: 7 chapter mappings covering foundation through mission — links to apologetics toolkit articles in Knowledge chapter, pastoral articles in Compassion chapter
- All other personas: persona-appropriate article selections

### Technical
- `CHAPTER_ARTICLES` rebuilt in all 14 journey files
- True Muslim journey: go-deeper script added (was missing entirely)
- All 14 files pass JS syntax validation
- 9 remaining orphaned articles are niche (apostasy international law, Satanic Verses incident) — discoverable through topic-grouped articles page

---

## [1.8.1] — 2026-03-24

### Changed — Articles archive page completely redesigned
Replaced the masonry grid layout with a **topic-sectioned design** that organises all 86 articles into logical reading order.

**Before**: Articles displayed in a 3-column masonry grid sorted by `menu_order`, paginated at 12 per page with infinite scroll. "Does God Communicate" appeared next to "Why Does Anything Exist" — no topical grouping, no intellectual flow.

**After**: Articles grouped by topic in deliberate order that mirrors the intellectual journey: Existence of God → Consciousness → Ethics & Morality → Evil & Suffering → Science & Faith → Science & Islam → The Quran → Purpose & Meaning → Objections → Doubts & Questions → Hard Questions → The Next Question.

Each section has: topic name, article count, one-line description of the topic's focus, and a numbered list of articles with title, excerpt, and reading time. Sections with 8+ articles collapse with a "Show all" button. Global article counter provides a sense of scale (86 articles total).

- **Duplicate prevention**: articles assigned to multiple topics (e.g. "Hard Questions" cross-tag) only appear in their first section
- **Topic filter strip**: still present at top, clicking a topic shows only that section
- **`taxonomy-ce_topic.php`**: now delegates to `archive-ce_article.php` for consistent rendering
- **Removed**: masonry grid, infinite scroll, jQuery dependencies on articles page
- **Mobile**: single-column, excerpt truncated to 1 line, reading time hidden

---

## [1.8.0] — 2026-03-24

### Added — 14th Journey: The True Muslim
New self-contained persona for believing Muslims who take the quiz out of curiosity. 7 chapters + conclusion (8 screens), covering:
1. **Foundation** — Your faith has rational ground (evidence base underneath inherited belief)
2. **Understanding** — What doubters actually experience (drawing on Cottee, Ibn Warraq, Leaving Faith Behind)
3. **Honesty** — The questions you should be asking too (complacency is the real enemy, not doubt)
4. **Equipping** — How to engage questioners without shutting them down (listen first, validate the question, admit what you don't know)
5. **Compassion** — Be the Muslim who makes people stay (character as the most powerful argument)
6. **Knowledge** — The strongest objections, addressed honestly (evil, apostasy, women, science)
7. **Mission** — Your role in the conversation (be available, share resources, pray for clarity)
8. **Conclusion** — Affirmation: your faith is grounded, not just inherited

### Changed — Quiz expanded from 5 to 9 questions
Four new questions (Q6-Q9) differentiate believing Muslims from doubters. A believing Muslim answering naturally accumulates 15-20+ true-muslim points, far exceeding any other persona.

### Completed — Arabic citation coverage: 100%
All 64 articles that reference the Quran or hadith now include Arabic text in Uthmanic rasm with Amiri font. Final 11 articles enhanced this release:
- The Anger Phase — 41:34 (repel evil with what is better)
- How Do We Know The Quran Wasn't Written By Humans? — 10:38 (produce a chapter like it)
- "There Is No Compulsion In Religion" — 2:256 (full verse with context)
- Why Hasn't Islam Had Its Enlightenment? — 16:125 (invite with wisdom)
- The Quran's Meccan/Medinan Distinction — 2:106 (abrogation verse)
- The Satanic Verses Incident — 22:52 (Satan throws into recitation)
- If God Listens, Why Doesn't He Answer? — 2:186 (I am near)
- Why Doesn't God Just Save Everyone? — 2:62 (those who believed and did good)
- Why Is God's Final Message In Arabic? — 12:2 + 54:17 (Arabic Quran + made easy)
- The Political History of Apostasy Law — 2:256 + 18:29 (no compulsion + let him believe)
- Apostasy Law and Human Rights — 18:29 (whoever wills, let him believe)

### Technical
- `quiz.html`: 9 question blocks, progress bar updated, calcScores loop expanded, true-muslim added to PERSONAS and SCORING
- `true-muslim-journey.html`: 661 lines, 8 screens, independent chapter structure (foundation→mission, not the physics-themed names used by other personas)
- `page-journey.php`: true-muslim added to valid paths
- `functions.php`: Path 14 added, conditional valid_screens for true-muslim's different chapter names
- `page-journeys.php`: 14th card added, hero updated to "14 Paths"
- All 13 existing journey files: ALL_RESONANCES, PATH_NAMES, PATH_FILES updated to include true-muslim

---

## [1.7.1] — 2026-03-24

### Enhanced — Complete Arabic citation coverage for articles
Expanded Arabic citation system to 51 total citation blocks across all article data files (up from 31 in v1.7.0). 20 additional articles enhanced with Uthmanic rasm Arabic text.

**New citations added:**
- The Quran as Literary Argument — 17:88 (inimitability challenge)
- Islam and the Scientific Tradition — 96:1 (first revelation: "Read")
- Why Islam and Not Another Religion? — 3:64 (common word)
- I Left Because Of Specific Problems — 47:24 (reflecting on Quran)
- What Happens To Good People Who Never Heard? — 17:15 (no punishment without messenger)
- Living Two Lives — Bukhari 1 / Muslim 1907 (actions by intentions)
- Scientific Miracles in the Quran — 21:30 (heavens and earth joined then separated)
- Can You Leave Islam And Still Be Yourself? — 2:256
- You Are Not Alone: Scale Of Doubt — 50:16 (closer than jugular vein)
- Fasting Without Faith — 2:183 (fasting prescribed for taqwa)
- The Age of Aisha — Bukhari 5134 / Muslim 1422 (the hadith itself in Arabic)
- Women, God, and Islamic Law — 2:228 (rights similar + degree)
- Slavery in Islamic Sources — 90:13 (freeing a slave among noblest deeds)
- The Violence of Early Islam — 5:32 (killing one soul = killing all)
- How Reliable Is Hadith Science? — Bukhari 107 (lying about the Prophet)
- Religious Trauma and the God Question — 2:286 (God does not burden beyond capacity)
- Why Create Knowing Suffering? — Hidden treasure hadith qudsi
- How Muslims Actually Leave — 7:179 (hearts that do not understand)
- The Islam I Was Defending — 4:135 (standing firm in justice even against yourself)
- Reading the Quran for the First Time — 12:2 (sent down as Arabic Quran to understand)

---

## [1.7.0] — 2026-03-17

### Added — Engagement System (replaces Social Dropdown + Vote It Up plugins)
Fully integrated article engagement system built natively into the theme. No plugins required.

**Social Share Bar** — X/Twitter, WhatsApp, Copy Link buttons + native Web Share API on mobile. Renders after article content on both `single-ce_article.php` and `single.php`.

**Up/Down Voting** — Compact pill widget with optimistic UI, AJAX persistence, one vote per visitor (SHA-256 IP hash for guests, user_id for logged-in). Score animation on vote.

**Resonance Feedback** — Three-option panel: "This addressed my question" / "I still have questions" / "I want to discuss this". One response per visitor. Shows aggregate results after responding.

**Most Resonant Widget** — WordPress widget (`CE_Most_Resonant_Widget`) showing top articles by engagement. Configurable title and count. Transient-cached.

### Added — 6 new articles (informed by academic research on doubt and apostasy)
New articles drawing on Cottee (2015), Ibn Warraq (2003), and Leaving Faith Behind (2018):
1. **Your Doubts Are Not a Disease** — Reframes doubt as legitimate inquiry, cites Sahih Muslim 132 (Prophet telling companions their distress at doubting is "clear faith"), + 4:82 in Arabic
2. **The Good Muslim Paradox** — Studying Islam MORE devoutly triggers doubt. Cites 39:9 in Arabic.
3. **When the Presence Fades** — Spiritual alienation: "my faith just flickered out." Cites 2:186 in Arabic.
4. **When Religion Was Imposed, Not Discovered** — Coerced faith vs genuine encounter. Cites 2:256 in Arabic.
5. **The Freethinkers Islam Produced** — Al-Rawandi, al-Razi, al-Ma'arri, Omar Khayyam. Cites 29:20 in Arabic.
6. **The Anger Is Real — And It Deserves an Answer** — Engages ex-Muslim anger honestly: lost time, missed experiences, imposed identity.

### Enhanced — 6 additional articles with Arabic Quranic/Hadith citations (Tier 2)
Bringing total to 31 Arabic citation blocks across all article data files:
- **If God Is Merciful, Why Are Some Passages So Harsh?** — 6:54
- **Was My Faith Just Conditioning?** — Fitrah hadith (Bukhari 1385 / Muslim 2658) in Arabic
- **If God Wants To Be Known, Why Is God Hidden?** — 41:53
- **Why Would God Send Anyone To Hellfire?** — 17:15
- **Finite Sins, Infinite Punishment** — 4:40
- **If God Sends Prophets, Why Do Messages Contradict?** — 42:13

### Technical
- **New files**: `inc/ce-engagement.php` (497 lines), `assets/js/ce-engagement.js` (201 lines), `assets/css/ce-engagement.css` (338 lines), `inc/articles-data-4.php` (294 lines, 6 articles)
- **Storage**: Hybrid — custom DB table (`wp_ce_engagement`) for individual records, post meta for aggregates, transients for cached reads
- **Security**: WordPress nonces, `check_ajax_referer()`, `$wpdb->prepare()`, SHA-256 IP hashing with WordPress salt
- **Templates modified**: `single-ce_article.php`, `single.php` — engagement footer added after article content
- **functions.php**: Added engagement system include + `ce_create_articles_4()` for new articles

---

## [1.6.0] — 2026-03-17

### Changed — MAJOR: Journey structure expanded from 7+1 to 9+1 chapters
All 13 journey paths restructured from 8 screens (7 chapters + conclusion) to 10 screens (9 chapters + conclusion). This is a breaking change to the journey experience.

**New chapter order (physics-themed naming preserved):**
1. Horizon — Epistemology / personal hook (unchanged position)
2. Singularity — Cosmological argument (unchanged)
3. Calibration — Fine-tuning (unchanged)
4. Emergence — Consciousness / Hard Problem (unchanged)
5. **Entropy** — Problem of evil (**moved earlier** from position 6)
6. **Constant** — Moral argument (**moved later** from position 5)
7. Signal — Argument from reason (unchanged)
8. **Resonance** — **NEW** — Cumulative convergence chapter with persona-specific framing
9. **Transmission** — **NEW** — "Has the Creator spoken?" bridge chapter with revelation criteria
10. Conclusion — One God Exists + invitation

### Enhanced — Horizon chapters with research insights
All 13 Horizon chapters enhanced with psychological specificity from three academic/testimonial sources:
- **Muslim with Doubts**: "good Muslim paradox" (studying Islam MORE devoutly triggers doubt), three doubt types (epistemological, moral, instrumental), phenomenology of doubt (guilt/fear/loneliness)
- **Ex-Believer**: Catch-22 of disclosure, exit wounds, identity loss, anger at lost time
- **Antitheist**: moral doubt as primary driver, political catalysts, institutional harm
- **Agnostic**: "I never recovered from that thought" — the single doubt that changes everything
- **Apatheist**: spiritual alienation — "the presence just flickered out"
- **Scientist**: evolution as the primary epistemological trigger
- **Freethinker**: historical Islamic freethinkers (al-Rawandi, al-Razi, al-Ma'arri, Khayyam)
- **New Atheist**: strongest version of theism vs the version actually rejected
- **Secular Humanist**: values are real — the question is their foundation
- **Materialist**: challenges from within the materialist tradition itself
- **Classical Atheist**: conceptual vs naive objections
- **Deist**: the personal gap — from "something" to "someone"
- **Spiritual Seeker**: can all experiences be true simultaneously?

### Added — Arabic citation system for articles
New CSS classes in `main.css`: `.quran-citation`, `.quran-arabic`, `.quran-translation`, `.quran-ref`, `.hadith-citation`, `.hadith-arabic`, `.hadith-translation`, `.hadith-ref`. Styled with Amiri font (Uthmanic rasm), RTL direction, gold/purple theming.

### Enhanced — 12 articles with Arabic Quranic/Hadith citations
Articles enhanced with full Arabic text in Uthmanic script + translation + source reference:
1. **Is Doubt Permitted in Islam?** — 4:82, 2:44, 3:190
2. **Why Is Associating Partners With God The One Unforgivable Sin?** — 4:48
3. **The Hadith "Kill Him Who Changes His Religion"** — Bukhari 6922 (Arabic), 3:72, 2:256
4. **What Is the Purpose of Life?** — 51:56, Hidden Treasure hadith qudsi
5. **How the Quran Was Preserved** — 15:9
6. **Why Would God Need To Be Worshipped?** — 51:56-57, 39:7
7. **Why Can't People Leave Islam?** — 2:256
8. **Does Human Evolution Contradict Islamic Theology?** — 22:5, 39:6
9. **If God Knew Everything, How Is My Choice Free?** — 81:29, 76:3
10. **If God Is Good, Why Is There Suffering?** — 2:155
11. **Does God Communicate With Humanity?** — 42:51

### Technical changes
- JS updated across all 13 journey files: screens/labels arrays, nav dots, roman numeral maps, progress scripts
- `functions.php`: valid_screens updated
- All bracket/PHP balance verified across article data files

---

## [1.5.6] — 2026-03-16

### Added
- **13th journey path: The Freethinker** — a complete 7-chapter + conclusion standalone HTML journey (`freethinker-journey.html`, ~1500 lines) written for readers who identify primarily with intellectual independence rather than any specific philosophical position. Chapters: Horizon (freethinking as method vs conclusion), Singularity (cosmological argument from reason alone), Calibration (fine-tuning without appeal to authority), Emergence (consciousness and the limits of materialism), Constant (moral realism and its requirements), Entropy (problem of evil examined honestly), Signal (argument from reason — can materialism trust itself?), and Conclusion. All CSS, mobile responsive breakpoints, progress tracking, resume banner, resonance widget, go-deeper links, and social sharing badge included.
- **Quiz integration** — `freethinker` added to `PERSONAS` object and `SCORING` matrix in `quiz.html`. Freethinker scores on answers emphasising independent reasoning (1B, 2B, 3D), evidence-based evaluation (4B, 5B), and openness without commitment (2C, 3A, 3B, 4E, 4F, 5E). Primary resonances: Scientist, Agnostic, Classical Atheist.
- **WordPress routing** — `freethinker` added to valid paths in `page-journey.php`, auto-page-creation array in `functions.php`, and journey card grid in `page-journeys.php`. Hero updated from "12 Paths" to "13 Paths".
- **Cross-journey resonance** — `ALL_RESONANCES`, `PATH_NAMES`, and `PATH_FILES` updated in all 13 journey HTML files to include freethinker.

---

## [1.5.5] — 2026-03-16

### Fixed
- **Search dropdown still hidden behind page content (stacking context fix)** — v1.5.4 set `overflow: visible` and `z-index: 10` on `.hero`, but the sections below (`.quick-access`, `.articles`, `.journey-entry`, `.cta`, `.ce-divider`, `.ce-footer`) had no `position` or `z-index`, so they painted over the hero's overflowing dropdown in DOM order. Fixed by giving all sections below the hero `position: relative; z-index: 1` — since the hero has `z-index: 10`, its dropdown now paints above everything below it. Also set `.scroll-hint` (the "Existence of God / Evil & Suffering / Objections" tiles at the bottom of the hero) to `z-index: 1` so the dropdown (z-index 150 inside the hero-search-wrap stacking context) renders above those tiles.

---

## [1.5.4] — 2026-03-16

### Fixed
- **Logo italic missing across all pages** — `font-style: italic` was absent from `.nav-logo` in `main.css` and `.footer-logo` in the footer. Also injected into all 13 journey/quiz HTML files' inline styles (`.nav-logo`, `.nav-logo-text`, `.cf-brand`). Logo now renders in italic Playfair Display consistently everywhere, matching the design.
- **Quiz Begin button and all interactions broken** — `quiz.html` had unescaped apostrophes in the `PERSONAS` JavaScript object (`isn't`, `it's`, `You're` inside single-quoted strings). This killed the entire `<script>` block silently — `startQuiz()`, `goNext()`, `showResult()`, and all click handlers never loaded. Escaped all three occurrences with `\'`.
- **All 12 journey choice/continue buttons non-functional** — every journey HTML file had an extra closing parenthesis `)` in the `updateNav()` function (`}));` instead of `});`), creating a `SyntaxError` that killed the main `<script>` block. `goTo()`, `updateNav()`, and the click handler for `.choice`, `.btn-teal`, and `.btn-gold` never loaded. Fixed across all 12 files.
- **`new-atheist-journey.html` missing 80+ CSS rules** — the file was the only journey missing styles for `.btn-teal`, `.next-chapter`, `.nc-*`, `.chapter-recap`, `.cr-*`, `.objection-block`, `.ob-*`, `.response-block`, `.rb-*`, `.argument-box`, `.ab-*`, `.constants-grid`, `.cg-*`, `.gap-table`, `.gt-*`, `.thought-experiment`, `.te-*`, `.distinction`, `.dist-*`, `.scenario`, `.reversal`, `.self-defeat`, `.journey-summary`, `.js-*`, `.final-cta`, `.fc-*`, `.two-forms`, `.tf-*`, `.acknowledgement`, and more. All 80+ rules added, matching the design language of the other 11 journeys.
- **Search dropdown hidden behind page content** — three compounding issues: (1) `.hero` had `overflow: hidden` which clipped the dropdown; (2) `.hero` had no `z-index` so later DOM sections stacked on top; (3) `.hero-search-wrap` had no stacking context. Fixed: hero `overflow: visible`, `z-index: 10`; search wrap `position: relative; z-index: 100`; dropdown boosted to `z-index: 150`.

### Changed
- **Quiz converted to multi-select** — users can now select multiple answers per question. CSS changed from radio circles to square checkboxes with ✓. Added "Select all that apply" hint to each question. JS rewritten: `answers` stores arrays, click handler toggles selection, `calcScores()` sums weights from all selected answers, `disableNext()` dims the button if all deselected.

### Added
- **Mobile responsive CSS across entire site** — prior to this version, 12 of 13 journey HTML files and the quiz had zero `@media` queries. Added comprehensive breakpoints:
  - **All 12 journey HTML files**: `@media (max-width: 900px)` — nav padding reduced, breadcrumb path hidden, `art-body` 2-column grid collapses to single column, sidebar hidden, `.next-chapter` stacks vertically (fixes "Continue" button overlap), all 2-column grids (`.two-col`, `.constants-grid`, `.gap-table`, `.distinction`, `.two-forms`) collapse to 1 column, conclusion padding reduced. `@media (max-width: 480px)` — further padding/font reductions for small phones.
  - **`quiz.html`**: `@media (max-width: 600px)` — quiz wrapper padding reduced, option cards tighter, score bar labels shrink (140px → 100px), result buttons stack. `@media (max-width: 380px)` — further tightening.
  - **`main.css`**: Enhanced existing 900px breakpoint; added `@media (max-width: 600px)` — hero no longer forces `min-height: 100vh`, articles/CTA/footer/article pages reduced padding, card bodies tighter, featured strip wraps. Added `@media (max-width: 420px)` — nav/logo shrink, featured strip wraps fully.
  - **`page-journeys.php`**: Added `@media (max-width: 640px)` — hero/grid/cards reduced padding, path grid goes single-column.
  - **`single-ce_article.php` + `single.php`**: Added `@media (max-width: 600px)` — hero/content/sidebar padding reduced.
  - **`page-progress.php`**: Added `@media (max-width: 600px)` — page padding reduced, stat cards shrink.

---

## [1.5.3] — 2026-03-16

### Fixed
- **Inconsistent logo across quiz and journey pages** — the main site nav (rendered via `header.php`) uses a two-tone logo: `<span class="nav-logo-ce">Compelling</span><span class="nav-logo-ev"> Evidence</span>` — "Compelling" in white/900 weight, "Evidence" in purple/700 weight, with a gradient underline. The 12 journey HTML files and `quiz.html` all had plain `Compelling Evidence` text with no two-tone treatment. Patched all 13 files: injected `.nav-logo-ce` / `.nav-logo-ev` CSS into each file's style block, and replaced the plain text logo node with the matching two-span markup.

---

## [1.5.2] — 2026-03-16

### Fixed
- **All 12 journey pages broken** — two compounding issues:
  1. All journey HTML files contained `PATH_FILES` JS objects and secondary resonance widget links that referenced bare `.html` filenames (e.g. `"agnostic-journey.html"`). These were never being replaced by the PHP template because the replacement only targeted specific string patterns — it missed the JS object values and the dynamic `item.href = file` assignments. Patched all 12 journey HTML files and `quiz.html` directly: every `"slug-journey.html"` value is now a WP-relative path (`"/journey/slug/"`) at source.
  2. `page-quiz.php` also had the same `compelling-evidence-v2.html` nav logo href that would 404. Patched in `quiz.html` source.
- Both `page-quiz.php` and `page-journey.php` simplified — since the HTML files are now pre-patched, the PHP templates only need to make relative paths absolute via `home_url()`.

---

## [1.5.1] — 2026-03-16

### Fixed
- **Critical error on `/quiz/` and journey pages (root cause)** — `page-quiz.php` and `page-journey.php` were using `preg_match` with `/<body[^>]*>(.*?)<\/body>/s` to extract the body content from the standalone HTML files. On many shared hosting PHP configs, this regex against a 35KB+ file triggers PCRE backtrack limit exhaustion, returning `false` and causing a fatal downstream. The entire approach was wrong — the quiz and journey HTML files are self-contained documents; wrapping them in a WP header/footer served no purpose and only added fragility.

  Both templates rewritten to serve the HTML file directly: `file_get_contents()` → `str_replace()` for URL patching → `echo` → `exit`. No `get_header()`, no `get_footer()`, no regex. The quiz and journey pages now render as standalone documents exactly as they were designed.

---

## [1.5.0] — 2026-03-16

### Fixed
- **Entities still raw in search dropdown** (`Hasn&#8217;t`, `for&hellip;`) — two compounding bugs:
  1. `ce_ajax_search()` was sending `wp_trim_words(get_the_excerpt())` which returns HTML-encoded text. Added `html_entity_decode(..., ENT_QUOTES, 'UTF-8')` on both `title` and `excerpt` before JSON encoding, so the JS receives clean UTF-8 strings.
  2. `renderResults()` in `main.js` was wrapping `item.title` and `item.excerpt` in `escHtml()`, which re-encoded the already-encoded output. Removed `escHtml()` from both fields (URL and topic remain escaped as they are not user-visible rich text).
- **Chips and EXPLORE tiles overlapping the search dropdown** — `renderResults()` now hides the `.scroll-hint` the moment results open. `closeDropdown()` restores it (if the user hasn't scrolled past the hero). Chips were already hidden via `hero-prompts--hidden`; scroll-hint was missing the same treatment.

---

## [1.4.9] — 2026-03-16

### Changed
- **Search area redesigned** — search bar and chips are now wrapped in a unified `.hero-search-wrap` card: frosted glass background, purple-glow border, inner highlight, and soft box-shadow. Feels like a distinct UI panel rather than floating elements.
- **Prompt chips overhauled** — three colour variants (purple `--a`, teal `--b`, coral `--c`) rotate across the six chips, each with matching background tint, border, text, and hover glow. Added a small `chip-icon` symbol (✦ ◈ ◎ ◇) to the left of each label. Added a "Try asking —" label above the chip row, separated from the search bar by a subtle divider line.
- **EXPLORE section expanded** — was just a word and a vertical line. Now shows three topic pill links (Existence of God · Evil & Suffering · Objections) above the label and line, giving the reader a tangible preview of what's below rather than an abstract scroll prompt.

---

## [1.4.8] — 2026-03-16

### Fixed
- **Broken featured strip links** — `header.php` had two hardcoded bare URLs (`/what-is-the-purpose-of-life`, `/does-god-exist`) that never existed. `ce_article` posts live at `/articles/{slug}/` due to the CPT rewrite slug. Updated both links to the correct paths: `/articles/purpose-of-life/` and `/articles/does-god-exist/`.
- **301 redirects for legacy bare URLs** — added `ce_legacy_redirects()` on `template_redirect` to permanently redirect both old paths to their correct `/articles/` equivalents, covering any external links or bookmarks pointing to the old URLs.

---

## [1.4.7] — 2026-03-16

### Fixed
- **HTML entities rendering as raw text** (`&amp;#8217;`, `&amp;hellip;`, `&amp;` etc.) — WordPress's `wptexturize()` and `wpautop()` already encode smart quotes and ellipses to HTML entities when posts are saved. Wrapping that output in `esc_html()` was double-encoding the ampersands, producing visible entity strings in the browser. Fixed in four templates:
  - `front-page.php` — `esc_html(get_the_excerpt())` → `get_the_excerpt()`
  - `single-ce_article.php` — `esc_html(get_the_title($r))` → `get_the_title($r)`
  - `single.php` — same fix
  - `page-ask-a-question.php` — same fix
- **"EXPLORE" scroll-hint bleeding over article cards** — the `.scroll-hint` is `position: absolute` at the bottom of the hero section, but had no JS to dismiss it once the user scrolled away. Added a scroll listener in `main.js` that fades the hint out at >80px scroll depth and restores it if the user scrolls back to the top.

---

## [1.4.6] — 2026-03-16

### Fixed
- **Critical error on `/quiz/`** — `functions.php` had two functions with PHP 7.0+ return type hints (`function ce_get_journey_file( string $journey_key ): string` and `function ce_get_quiz_file(): string`). If the host runs PHP < 7.0 or a strict compatibility mode, these cause a fatal parse error that kills the entire page. Removed both return type hints — functions are now plain untyped declarations, compatible with PHP 5.6+.
- **404 falling through to parent theme** — no `404.php` existed in the child theme, so WordPress fell back to Twenty Twenty-Five's default 404 (the tree photo with no branding or navigation). Created `404.php` with a fully styled page matching the site design: large ghost-text "404", title, sub-copy, three action buttons (Go home / Find your path / Browse articles), and an inline search form. Matches the hero colour palette and animation sequence.

### Added
- `404.php` — branded not-found page

---

## [1.4.5] — 2026-03-16

### Added
- **Ornamental flourishes around verse citation** — the Surah Ar-Rahman (55:13) citation line now has decorative SVG ornaments on each side: a receding line ending in a diamond, inner rotated square, and fading tail — mirrored left/right. Uses the purple accent colour and fades in with the hero animation sequence.
- **Floating prompt chips** — six clickable question chips appear in the gap between the search bar and the EXPLORE scroll hint (`hero-prompts`). Each chip gently bobs at its own speed/phase (`chipFloat` keyframe). Clicking any chip: pre-fills the search input, triggers the live-search AJAX dropdown, highlights the chip (`chip-fired` state), and focuses the input. Questions: *Does God exist? · Why does evil exist? · Islam & science · Is faith rational? · What happens after death? · Who wrote the Quran?*

---

## [1.4.4] — 2026-03-16

### Fixed
- **"EXPLORE" scroll hint overlapping search bar** — `.hero` had no `min-height`, so its height was only as tall as its content. The `.scroll-hint` element (`position: absolute; bottom: 2.5rem`) ended up sitting directly on top of the search bar instead of at the bottom of the viewport. Added `min-height: 100vh` to `.hero` so the section fills the full viewport and the scroll hint sits well clear of the search bar.

---

## [1.4.3] — 2026-03-16

### Fixed
- **Search bar missing from homepage / hero styling broken** — The 1.2.9→current CSS merge left stale duplicate rules in the hero section. The original 1.2.9 `.hero-bismillah` block (with `margin-bottom: 2rem` and `opacity:0`) survived between `.hero::before` and the newer Hero verse image section. This caused two problems: (1) the stale `opacity:0 + fadeUp` animation was overriding the correct one, and (2) the `margin-bottom: 2rem` was pushing content — including `.hero-search` — far enough down to push it out of view or break the layout. Removed the stale block and the now-redundant `.hero-verse { display:none }` override. Hero section now reads cleanly: `.hero` → `::before` → verse image wrap → translation → bismillah → sub → search.
- `main.css` reduced from 1392 lines (post-dedup) with clean single definitions throughout.

---

## [1.4.3] — 2026-03-16

### Fixed
- **Journey entry section unstyled/broken layout** — `.je-inner` was declared as a 2-column grid (`grid-template-columns: 1fr 1fr`) but only ever contained one child (`.je-text`). The text was being constrained to half the container width, left-aligned, with an empty right column. Fixed: `.je-inner` changed to single-column centred layout (`max-width: 760px; margin: 0 auto; text-align: center`). `.je-actions` centred with `justify-content: center`. `.je-sub` given `max-width: 560px; margin: auto` for comfortable line length. Removed the now-redundant responsive grid override.

---

## [1.4.2] — 2026-03-16

### Fixed
- **PHP 7.3 compatibility** — Two `fn() =>` arrow functions in `functions.php` (excerpt_length and excerpt_more filters) replaced with standard anonymous functions. Arrow functions require PHP 7.4+.
- **Integrity audit passed clean** — All 12 checks pass with 0 issues, 0 warnings.

### Audit results (v1.4.2)
- PHP tag balance: ✓ 17 files
- Header/footer pairs: ✓ all active templates (archive-ce_topic.php is a redirect shim — intentional)
- CSS sections: ✓ 14 sections, 1424 lines / 40KB
- Asset files: ✓ 10 files present
- JS integrity: ✓ single IIFE, all AJAX actions present
- Journey files: ✓ 12 paths, all 4 scripts, correct conclusion links
- Article data: ✓ 80 articles, 80 unique slugs, no missing fields
- Private names: ✓ none in articles
- Persona names: ✓ none in public templates
- Activation hooks: ✓ 5 hooks all defined
- PHP compatibility: ✓ no arrow functions
- Version: ✓ 1.4.2 consistent

---

## [1.4.1] — 2026-03-16

### Fixed
- **Archive/topic pages single-column, article cards unstyled** — `main.css` had been progressively stripped of large CSS sections across multiple edits since 1.2.9. The following sections were entirely missing: `ce-archive`, `archive-header`, `archive-hero`, `topic-filter`, `topic-page`, `articles-archive`, single article page styles, animations, responsive breakpoints, featured strip, pagination, ask-a-question page, masonry clearfix. Total: ~20KB of CSS gone.
- Restored by merging complete 1.2.9 baseline (1301 lines) with all post-1.2.9 improvements: updated hero (no min-height, correct padding), hero verse image, two-tone nav logo, redesigned footer, nav-actions/random button, quick-access cards.
- Final `main.css`: 1424 lines, 41KB — all sections verified present.

---

## [1.4.0] — 2026-03-16

### Fixed
- **Random button lost all styling** — `.nav-actions`, `.nav-random`, and `.nav-progress-link` CSS was missing from `main.css`. The Random button rendered as a plain bare link with no pill shape, border, background, or hover state. All three rules restored: nav-actions flex container, nav-random pill with purple border/background and teal hover, nav-progress-link icon button.

---

## [1.3.9] — 2026-03-16

### Fixed
- **Homepage Quick Access cards completely unstyled** — `.quick-access` grid and `.qa-card` CSS was missing from `main.css`. The section rendered as plain unstyled links with no layout, icons, colour, or hover state. CSS added for the 3-column grid, card padding, icon colour, heading, description, and CTA with hover transitions.

---

## [1.3.8] — 2026-03-16

### Added
- **Universal Islamic Declaration of Human Rights (1981)** integrated into two articles:
  - `apostasy-international-law` — new section analysing the UIDHR's structural treatment of religious freedom: Article XIII guarantees freedom of worship "in accordance with existing beliefs" but not freedom to change belief; Article X applies 2:256 only to non-Muslim minorities; all rights subject to "the Law" defined as Sharia; result is a human rights framework whose architecture preserves the death penalty for apostasy intact
  - `no-compulsion-in-religion` — UIDHR cited as direct evidence of how thoroughly the conviction/allegiance conflation had been institutionalised — the same year Islamic states were resisting Article 18 at the UN, the Islamic Council of Europe was producing a rights framework that used the language of freedom while definitionally foreclosing it

### Fixed
- **Named private individuals removed from all articles.** Only well-known public personalities may be named. All references to testimony contributors (private individuals from academic studies and published collections) replaced with anonymised descriptions:
  - `shirk-unforgivable` — "Ali Sina" → "a common account concerns Gandhi…"
  - `the-islam-i-was-defending` — "Ali Sina" and "Irfan Ahmad Khawaja" → anonymised patterns
  - `how-muslims-leave-the-sociology` — "Nubia" → anonymised
  - `reading-the-quran-for-the-first-time` — "Ali Sina" → anonymised

---

## [1.3.7] — 2026-03-16

### Added (Ibn Warraq integration)
Source: Ibn Warraq (ed.), *Leaving Islam: Apostates Speak Out* (Prometheus, 2003). Three distinct contributions:

**1. UDHR Article 18 dimension (Chapter 9 — Apostasy, Human Rights, and Islam)**
- Muslim-majority delegations actively fought to remove "freedom to change religion" from the Universal Declaration of Human Rights (1948)
- Egypt, Saudi Arabia, Iraq: specific documented resistance across 1948, 1966, and 1981 UN negotiations
- Final compromise: "freedom to change his religion" replaced with "freedom to have or adopt a religion"
- Sudan (1991) and Mauritania (1984) subsequently codified death for apostasy in penal codes
- Mahmud Muhammad Taha hanged for apostasy in Sudan 1985 under a code that didn't name apostasy as a crime

**2. The "reform Muslim" pattern (testimonies: Sina, Khawaja, and many anonymous)**
- Consistent pattern: people defending a moderate, humanistic Islam → careful text reading → finding it didn't match what they were defending
- Ali Sina: years arguing for "real Islam" as tolerant and scientific → systematic Quran reading → specific verses undid that image
- Irfan Khawaja: went to library to find material vindicating Islam from critics → concluded it couldn't be vindicated

**3. Specific textual objections (across testimonies)**
- Shirk/unforgivability: Gandhi in hell, Muslim murderer possibly forgiven — the moral asymmetry objection
- Commands regarding non-believers and the gap between lived relational experience and Quranic instruction
- The role of translation: Muslims raised reciting Arabic without understanding it, then encountering the text in their own language

### New articles added to `inc/articles-data-3.php`:
- `the-islam-i-was-defending` — "The Islam I Was Defending Did Not Exist" — the reform Muslim's dilemma and honest response
- `apostasy-international-law` — "Apostasy Law and the Universal Declaration of Human Rights" — the full institutional record
- `reading-the-quran-for-the-first-time` — "What Happens When You Read the Quran for the First Time" — the text encounter, what's actually there, how to think about it

### Enhanced articles:
- `no-compulsion-in-religion` — now includes the full UDHR Article 18 politics, the penal code codifications, AbuSulayman's jurisprudential argument in counterpoint
- `shirk-unforgivable` — now includes the Ali Sina Gandhi argument as a precise formulation of the moral asymmetry objection, with two genuine responses and honest assessment of their weight

### Stats
- Total articles on activation: **80** (was 77)
- articles-data-3.php: **35** articles (was 32)

---

## [1.3.6] — 2026-03-16

### Added (Source material integration)
Three primary sources used to deepen the apostasy and doubt content:
- **AbuSulayman, *Apostates, Islam & Freedom of Faith*** (IIIT, 2013) — detailed jurisprudential argument that the Quran prescribes no earthly punishment for apostasy; the death penalty entered classical law through conflation of religious conviction with political allegiance in the early Muslim state
- **Cottee, *The Apostates: When Muslims Leave Islam*** (Hurst, 2015) — qualitative sociological study of ex-Muslims in Britain; specific typologies of doubt, the solitude of the pre-apostasy phase, the costs of concealment, exit wounds
- Two new articles added to `inc/articles-data-3.php`:
  - `how-muslims-leave-the-sociology` — "How Muslims Actually Leave: The Inner Journey" — Cottee's phenomenology of leaving: epistemological/moral/spiritual doubt, the role of reading, the solitude of doubt, the social costs of disclosure
  - `apostasy-political-history` — "The Political History of Apostasy Law in Islam" — AbuSulayman's argument: the Quranic position, the origin of the death penalty in the early political state, the conviction/allegiance distinction, what it means for those who have left

### Changed
- `kill-him-who-changes-religion` article significantly enhanced: now includes AbuSulayman's political/doctrinal distinction in full, the hirabah framing, the specific classical scholars who held the non-execution position, the structural argument from 2:256
- `religious-trauma` article significantly enhanced: now includes Cottee's specific sociological findings — the documented solitude of doubt, patterns of concealment, the double-life experience, the distinction between harm caused by institutional religion and harm caused by specifically bad teaching

### Stats
- Total articles on activation: **77** (was 75)
- articles-data-3.php: **32** articles (was 30)

---

## [1.3.5] — 2026-03-16

### Fixed (Integrity Audit)
Full theme audit run. All issues found and resolved:

- **`.footer-brand` CSS missing** — class used in `footer.php` but had no definition in `main.css`. Added flex-column definition.
- **Duplicate article slug `problem-of-evil`** — appeared in both `articles-data.php` ("If God Is Good, Why Is There Suffering?") and `articles-data-2.php` ("But What About The Problem Of Evil?"). Renamed the Set 2 version to `problem-of-evil-response`. All 75 slugs now unique.
- **`single.php` diverged from `single-ce_article.php`** — `single.php` still contained the removed prev/next nav CSS and HTML, and was missing the journey return widget. Re-synced to match `single-ce_article.php` exactly.
- **Duplicate `has_archive` key in CPT registration** — `register_post_type('ce_article')` had `has_archive => true` listed twice. Removed the duplicate (harmless but untidy).
- **Hardcoded `/quiz` hrefs in JS** — two `href="/quiz"` strings inside JS in `single-ce_article.php` replaced with `CE.homeUrl + 'quiz'` for domain-independence.
- **`archive-ce_topic.php` was dead code** — superseded by `taxonomy-ce_topic.php` but contained 9KB of conflicting markup. Replaced with a one-line redirect shim: `get_template_part('taxonomy-ce_topic')`.

### Verified Clean
- All 12 journey files present with all 4 scripts (ce-progress, ce-resonance-init, ce-go-deeper, ce-social-badge)
- All 12 journey conclusion links correct (11 paths → /articles/, muslim-doubts → bismikaallahuma.org)
- All activation hooks registered and functions defined
- All enqueued assets exist on disk
- No duplicate add_action registrations
- PHP tag balance correct across all 17 template files
- get_header()/get_footer() present in all templates
- CSS classes used in templates all have definitions (inline or in main.css)
- No stale CSS for removed features (network-bar, article-nav, footer-top, footer-cols)

---

## [1.3.4] — 2026-03-16

### Fixed
- **Topic pages completely unstyled** — the template was named `archive-ce_topic.php` which is not in WordPress's template hierarchy for custom taxonomies. WordPress was ignoring it entirely and falling back to the default theme template, rendering the raw taxonomy archive with no CSS or layout. Created `taxonomy-ce_topic.php` (correct naming: `taxonomy-{taxonomy}.php`) which WordPress now correctly loads for all `/topic/[slug]/` URLs.

### Added
- `taxonomy-ce_topic.php` — fully styled topic archive with: eyebrow + topic name hero, article count, Hard Questions banner for the doubts/hard-questions terms, topic filter pill row, Masonry card grid, Infinite Scroll, fallback pagination, bottom quiz CTA

### Changed
- `archive-ce_topic.php` retained but now superseded by the correctly named template

---

## [1.3.3] — 2026-03-16

### Changed
- **Footer redesigned** — three-column grid layout replacing the old two-column with excessive gaps:
  - Column 1 (wider): Brand name, tagline, "Find my path" CTA link
  - Column 2: Topics — top 7 topic taxonomy terms
  - Column 3: Navigate — Quiz, All Paths, Articles, Ask a Question, My Progress
- Max-width `1060px`, padding `3.5rem 2rem`, grid gap `3rem` — tight, proportional, not sprawling
- Two-tone logo mark ("Compelling" white / "Evidence" purple) matching the nav
- Bottom bar: copyright left, two quick links right — minimal, one line
- Responsive: 3-col → 2-col at 760px → 1-col at 480px
- Removed empty network-bar section entirely

---

## [1.3.2] — 2026-03-16

### Fixed
- **Verse too close to header** — hero `padding-top` was `3rem` (≈48px), not accounting for the fixed nav (72px) + fixed featured strip (44px) = 116px total. Content was rendering underneath both bars. Fixed to `calc(var(--total-offset, 116px) + 2.5rem)` so the verse sits cleanly below the nav with appropriate breathing room.

---

## [1.3.1] — 2026-03-16

### Removed
- Prev/Next article navigation from `single-ce_article.php` — the "← Previous / Next →" block at the bottom of every article page is removed. Navigation is handled via the sidebar related articles widget and the journey return widget.

---

## [1.3.0] — 2026-03-16

### Fixed
- **Hero gap — structural fix.** `min-height: 100vh` + `justify-content: center` was centering the verse content in the full viewport height. With the nav and featured strip consuming ~90px at the top, this pushed the verse to the visual centre of the remaining ~750px — creating a ~375px gap above the verse. Removed `min-height: 100vh` and `justify-content: center` entirely. Hero now flows naturally from top with `padding: 3rem 2rem 4rem`. The total-offset override that was adding additional `padding-top` on top of the base padding is also removed.

---

## [1.2.9] — 2026-03-16

### Added
- **Masonry grid layout** on `/articles/` and `/topic/[slug]/` — cards cascade into a Pinterest-style variable-height grid using Masonry v4.2.2
- **Infinite scroll** on `/articles/`, `/topic/[slug]/`, and `/search/` — next page loads automatically as reader scrolls, appended items integrated with Masonry layout. Status indicators: loading spinner, "All articles loaded", error state
- Three JS libraries bundled in `assets/js/`:
  - `jquery.masonry.min.js` v4.2.2
  - `jquery.imagesloaded.min.js` v5.0.0
  - `jquery.infinitescroll.min.js` v5.0.0
- Scripts registered in `functions.php` with proper jQuery dependencies, enqueued conditionally on archive/search/tax pages only — not loaded on every page

### Changed
- `archive-ce_article.php` — rewritten with `.article-card` Masonry items, `.grid-sizer` column width reference, Infinite Scroll bound to `.pagination .next`, fallback pagination hidden when JS active
- `archive-ce_topic.php` — same Masonry + Infinite Scroll integration
- `search.php` — Infinite Scroll added to results list (list layout preserved, not masonry)
- `assets/css/main.css` — masonry clearfix, `.grid-sizer` responsive widths, `.inf-scroll-spinner` keyframe added

---

## [1.2.8] — 2026-03-16

### Fixed
- **Hero spacing** — excessive vertical gaps between the nav, the Ar-Rahman verse image, the translation, and the search bar. Root cause: two conflicting `padding-top` rules on `.hero` were stacking (~8rem total above the verse). Fixed values:
  - Hero base padding: `4rem` → `1.5rem`
  - Total-offset override: `+ 4rem` → `+ 1rem`
  - Verse image wrap gap: `0.5rem` → `0.25rem`
  - Verse image wrap margin-bottom: `1.8rem` → `0.8rem`
  - Verse image max-width: `580px` → `500px`
  - Translation top margin: `0.3rem` → `0.1rem`
  - Hero sub top margin: `1.2rem` → `0.8rem`
  - Search top margin: `3rem` → `1.8rem`

---

## [1.2.7] — 2026-03-16

### Fixed
- **Search results page dysfunctional** — no `search.php` template existed. WordPress was falling back to `index.php` which rendered search results in a generic card grid with no search-specific UI.

### Added
- `search.php` — dedicated search results template. Features:
  - Hero block: "Results for: [term]" with result count
  - Inline search bar pre-filled with current query for easy refinement
  - Results list: numbered rows with topic tag, reading time, title, excerpt
  - Pagination for results spanning multiple pages
  - Empty state: clear messaging, 10 suggested search terms, quiz CTA and browse-all link
  - Properly queries both `post` and `ce_article` post types

---

## [1.2.6] — 2026-03-16

### Fixed
- **Persona names exposed on homepage** — the journey entry section displayed all 12 path names as clickable pills (The New Atheist, The Agnostic, etc.) before the visitor had taken any action. This directly undermined the site's core design: the identity of the 12 paths should only be revealed through the quiz. All pills removed.
- `je-paths` PHP block removed from `front-page.php`
- `.je-paths`, `.je-path-pill`, `.ce-resumed`, `.ce-completed` CSS rules removed from `main.css`
- Pill-highlighting code removed from `main.js`
- Pill-highlighting code removed from the returning visitor JS in `front-page.php`

---

## [1.4.3] — 2026-03-16

### Fixed
- **Search bar missing from homepage / hero styling broken** — The 1.2.9→current CSS merge left stale duplicate rules in the hero section. The original 1.2.9 `.hero-bismillah` block (with `margin-bottom: 2rem` and `opacity:0`) survived between `.hero::before` and the newer Hero verse image section. This caused two problems: (1) the stale `opacity:0 + fadeUp` animation was overriding the correct one, and (2) the `margin-bottom: 2rem` was pushing content — including `.hero-search` — far enough down to push it out of view or break the layout. Removed the stale block and the now-redundant `.hero-verse { display:none }` override. Hero section now reads cleanly: `.hero` → `::before` → verse image wrap → translation → bismillah → sub → search.
- `main.css` reduced from 1392 lines (post-dedup) with clean single definitions throughout.

---

## [1.4.3] — 2026-03-16

### Fixed
- **Journey entry section unstyled/broken layout** — `.je-inner` was declared as a 2-column grid (`grid-template-columns: 1fr 1fr`) but only ever contained one child (`.je-text`). The text was being constrained to half the container width, left-aligned, with an empty right column. Fixed: `.je-inner` changed to single-column centred layout (`max-width: 760px; margin: 0 auto; text-align: center`). `.je-actions` centred with `justify-content: center`. `.je-sub` given `max-width: 560px; margin: auto` for comfortable line length. Removed the now-redundant responsive grid override.

---

## [1.4.2] — 2026-03-16

### Fixed
- **PHP 7.3 compatibility** — Two `fn() =>` arrow functions in `functions.php` (excerpt_length and excerpt_more filters) replaced with standard anonymous functions. Arrow functions require PHP 7.4+.
- **Integrity audit passed clean** — All 12 checks pass with 0 issues, 0 warnings.

### Audit results (v1.4.2)
- PHP tag balance: ✓ 17 files
- Header/footer pairs: ✓ all active templates (archive-ce_topic.php is a redirect shim — intentional)
- CSS sections: ✓ 14 sections, 1424 lines / 40KB
- Asset files: ✓ 10 files present
- JS integrity: ✓ single IIFE, all AJAX actions present
- Journey files: ✓ 12 paths, all 4 scripts, correct conclusion links
- Article data: ✓ 80 articles, 80 unique slugs, no missing fields
- Private names: ✓ none in articles
- Persona names: ✓ none in public templates
- Activation hooks: ✓ 5 hooks all defined
- PHP compatibility: ✓ no arrow functions
- Version: ✓ 1.4.2 consistent

---

## [1.4.1] — 2026-03-16

### Fixed
- **Archive/topic pages single-column, article cards unstyled** — `main.css` had been progressively stripped of large CSS sections across multiple edits since 1.2.9. The following sections were entirely missing: `ce-archive`, `archive-header`, `archive-hero`, `topic-filter`, `topic-page`, `articles-archive`, single article page styles, animations, responsive breakpoints, featured strip, pagination, ask-a-question page, masonry clearfix. Total: ~20KB of CSS gone.
- Restored by merging complete 1.2.9 baseline (1301 lines) with all post-1.2.9 improvements: updated hero (no min-height, correct padding), hero verse image, two-tone nav logo, redesigned footer, nav-actions/random button, quick-access cards.
- Final `main.css`: 1424 lines, 41KB — all sections verified present.

---

## [1.4.0] — 2026-03-16

### Fixed
- **Random button lost all styling** — `.nav-actions`, `.nav-random`, and `.nav-progress-link` CSS was missing from `main.css`. The Random button rendered as a plain bare link with no pill shape, border, background, or hover state. All three rules restored: nav-actions flex container, nav-random pill with purple border/background and teal hover, nav-progress-link icon button.

---

## [1.3.9] — 2026-03-16

### Fixed
- **Homepage Quick Access cards completely unstyled** — `.quick-access` grid and `.qa-card` CSS was missing from `main.css`. The section rendered as plain unstyled links with no layout, icons, colour, or hover state. CSS added for the 3-column grid, card padding, icon colour, heading, description, and CTA with hover transitions.

---

## [1.3.8] — 2026-03-16

### Added
- **Universal Islamic Declaration of Human Rights (1981)** integrated into two articles:
  - `apostasy-international-law` — new section analysing the UIDHR's structural treatment of religious freedom: Article XIII guarantees freedom of worship "in accordance with existing beliefs" but not freedom to change belief; Article X applies 2:256 only to non-Muslim minorities; all rights subject to "the Law" defined as Sharia; result is a human rights framework whose architecture preserves the death penalty for apostasy intact
  - `no-compulsion-in-religion` — UIDHR cited as direct evidence of how thoroughly the conviction/allegiance conflation had been institutionalised — the same year Islamic states were resisting Article 18 at the UN, the Islamic Council of Europe was producing a rights framework that used the language of freedom while definitionally foreclosing it

### Fixed
- **Named private individuals removed from all articles.** Only well-known public personalities may be named. All references to testimony contributors (private individuals from academic studies and published collections) replaced with anonymised descriptions:
  - `shirk-unforgivable` — "Ali Sina" → "a common account concerns Gandhi…"
  - `the-islam-i-was-defending` — "Ali Sina" and "Irfan Ahmad Khawaja" → anonymised patterns
  - `how-muslims-leave-the-sociology` — "Nubia" → anonymised
  - `reading-the-quran-for-the-first-time` — "Ali Sina" → anonymised

---

## [1.3.7] — 2026-03-16

### Added (Ibn Warraq integration)
Source: Ibn Warraq (ed.), *Leaving Islam: Apostates Speak Out* (Prometheus, 2003). Three distinct contributions:

**1. UDHR Article 18 dimension (Chapter 9 — Apostasy, Human Rights, and Islam)**
- Muslim-majority delegations actively fought to remove "freedom to change religion" from the Universal Declaration of Human Rights (1948)
- Egypt, Saudi Arabia, Iraq: specific documented resistance across 1948, 1966, and 1981 UN negotiations
- Final compromise: "freedom to change his religion" replaced with "freedom to have or adopt a religion"
- Sudan (1991) and Mauritania (1984) subsequently codified death for apostasy in penal codes
- Mahmud Muhammad Taha hanged for apostasy in Sudan 1985 under a code that didn't name apostasy as a crime

**2. The "reform Muslim" pattern (testimonies: Sina, Khawaja, and many anonymous)**
- Consistent pattern: people defending a moderate, humanistic Islam → careful text reading → finding it didn't match what they were defending
- Ali Sina: years arguing for "real Islam" as tolerant and scientific → systematic Quran reading → specific verses undid that image
- Irfan Khawaja: went to library to find material vindicating Islam from critics → concluded it couldn't be vindicated

**3. Specific textual objections (across testimonies)**
- Shirk/unforgivability: Gandhi in hell, Muslim murderer possibly forgiven — the moral asymmetry objection
- Commands regarding non-believers and the gap between lived relational experience and Quranic instruction
- The role of translation: Muslims raised reciting Arabic without understanding it, then encountering the text in their own language

### New articles added to `inc/articles-data-3.php`:
- `the-islam-i-was-defending` — "The Islam I Was Defending Did Not Exist" — the reform Muslim's dilemma and honest response
- `apostasy-international-law` — "Apostasy Law and the Universal Declaration of Human Rights" — the full institutional record
- `reading-the-quran-for-the-first-time` — "What Happens When You Read the Quran for the First Time" — the text encounter, what's actually there, how to think about it

### Enhanced articles:
- `no-compulsion-in-religion` — now includes the full UDHR Article 18 politics, the penal code codifications, AbuSulayman's jurisprudential argument in counterpoint
- `shirk-unforgivable` — now includes the Ali Sina Gandhi argument as a precise formulation of the moral asymmetry objection, with two genuine responses and honest assessment of their weight

### Stats
- Total articles on activation: **80** (was 77)
- articles-data-3.php: **35** articles (was 32)

---

## [1.3.6] — 2026-03-16

### Added (Source material integration)
Three primary sources used to deepen the apostasy and doubt content:
- **AbuSulayman, *Apostates, Islam & Freedom of Faith*** (IIIT, 2013) — detailed jurisprudential argument that the Quran prescribes no earthly punishment for apostasy; the death penalty entered classical law through conflation of religious conviction with political allegiance in the early Muslim state
- **Cottee, *The Apostates: When Muslims Leave Islam*** (Hurst, 2015) — qualitative sociological study of ex-Muslims in Britain; specific typologies of doubt, the solitude of the pre-apostasy phase, the costs of concealment, exit wounds
- Two new articles added to `inc/articles-data-3.php`:
  - `how-muslims-leave-the-sociology` — "How Muslims Actually Leave: The Inner Journey" — Cottee's phenomenology of leaving: epistemological/moral/spiritual doubt, the role of reading, the solitude of doubt, the social costs of disclosure
  - `apostasy-political-history` — "The Political History of Apostasy Law in Islam" — AbuSulayman's argument: the Quranic position, the origin of the death penalty in the early political state, the conviction/allegiance distinction, what it means for those who have left

### Changed
- `kill-him-who-changes-religion` article significantly enhanced: now includes AbuSulayman's political/doctrinal distinction in full, the hirabah framing, the specific classical scholars who held the non-execution position, the structural argument from 2:256
- `religious-trauma` article significantly enhanced: now includes Cottee's specific sociological findings — the documented solitude of doubt, patterns of concealment, the double-life experience, the distinction between harm caused by institutional religion and harm caused by specifically bad teaching

### Stats
- Total articles on activation: **77** (was 75)
- articles-data-3.php: **32** articles (was 30)

---

## [1.3.5] — 2026-03-16

### Fixed (Integrity Audit)
Full theme audit run. All issues found and resolved:

- **`.footer-brand` CSS missing** — class used in `footer.php` but had no definition in `main.css`. Added flex-column definition.
- **Duplicate article slug `problem-of-evil`** — appeared in both `articles-data.php` ("If God Is Good, Why Is There Suffering?") and `articles-data-2.php` ("But What About The Problem Of Evil?"). Renamed the Set 2 version to `problem-of-evil-response`. All 75 slugs now unique.
- **`single.php` diverged from `single-ce_article.php`** — `single.php` still contained the removed prev/next nav CSS and HTML, and was missing the journey return widget. Re-synced to match `single-ce_article.php` exactly.
- **Duplicate `has_archive` key in CPT registration** — `register_post_type('ce_article')` had `has_archive => true` listed twice. Removed the duplicate (harmless but untidy).
- **Hardcoded `/quiz` hrefs in JS** — two `href="/quiz"` strings inside JS in `single-ce_article.php` replaced with `CE.homeUrl + 'quiz'` for domain-independence.
- **`archive-ce_topic.php` was dead code** — superseded by `taxonomy-ce_topic.php` but contained 9KB of conflicting markup. Replaced with a one-line redirect shim: `get_template_part('taxonomy-ce_topic')`.

### Verified Clean
- All 12 journey files present with all 4 scripts (ce-progress, ce-resonance-init, ce-go-deeper, ce-social-badge)
- All 12 journey conclusion links correct (11 paths → /articles/, muslim-doubts → bismikaallahuma.org)
- All activation hooks registered and functions defined
- All enqueued assets exist on disk
- No duplicate add_action registrations
- PHP tag balance correct across all 17 template files
- get_header()/get_footer() present in all templates
- CSS classes used in templates all have definitions (inline or in main.css)
- No stale CSS for removed features (network-bar, article-nav, footer-top, footer-cols)

---

## [1.3.4] — 2026-03-16

### Fixed
- **Topic pages completely unstyled** — the template was named `archive-ce_topic.php` which is not in WordPress's template hierarchy for custom taxonomies. WordPress was ignoring it entirely and falling back to the default theme template, rendering the raw taxonomy archive with no CSS or layout. Created `taxonomy-ce_topic.php` (correct naming: `taxonomy-{taxonomy}.php`) which WordPress now correctly loads for all `/topic/[slug]/` URLs.

### Added
- `taxonomy-ce_topic.php` — fully styled topic archive with: eyebrow + topic name hero, article count, Hard Questions banner for the doubts/hard-questions terms, topic filter pill row, Masonry card grid, Infinite Scroll, fallback pagination, bottom quiz CTA

### Changed
- `archive-ce_topic.php` retained but now superseded by the correctly named template

---

## [1.3.3] — 2026-03-16

### Changed
- **Footer redesigned** — three-column grid layout replacing the old two-column with excessive gaps:
  - Column 1 (wider): Brand name, tagline, "Find my path" CTA link
  - Column 2: Topics — top 7 topic taxonomy terms
  - Column 3: Navigate — Quiz, All Paths, Articles, Ask a Question, My Progress
- Max-width `1060px`, padding `3.5rem 2rem`, grid gap `3rem` — tight, proportional, not sprawling
- Two-tone logo mark ("Compelling" white / "Evidence" purple) matching the nav
- Bottom bar: copyright left, two quick links right — minimal, one line
- Responsive: 3-col → 2-col at 760px → 1-col at 480px
- Removed empty network-bar section entirely

---

## [1.3.2] — 2026-03-16

### Fixed
- **Verse too close to header** — hero `padding-top` was `3rem` (≈48px), not accounting for the fixed nav (72px) + fixed featured strip (44px) = 116px total. Content was rendering underneath both bars. Fixed to `calc(var(--total-offset, 116px) + 2.5rem)` so the verse sits cleanly below the nav with appropriate breathing room.

---

## [1.3.1] — 2026-03-16

### Removed
- Prev/Next article navigation from `single-ce_article.php` — the "← Previous / Next →" block at the bottom of every article page is removed. Navigation is handled via the sidebar related articles widget and the journey return widget.

---

## [1.3.0] — 2026-03-16

### Fixed
- **Hero gap — structural fix.** `min-height: 100vh` + `justify-content: center` was centering the verse content in the full viewport height. With the nav and featured strip consuming ~90px at the top, this pushed the verse to the visual centre of the remaining ~750px — creating a ~375px gap above the verse. Removed `min-height: 100vh` and `justify-content: center` entirely. Hero now flows naturally from top with `padding: 3rem 2rem 4rem`. The total-offset override that was adding additional `padding-top` on top of the base padding is also removed.

---

## [1.2.9] — 2026-03-16

### Added
- **Masonry grid layout** on `/articles/` and `/topic/[slug]/` — cards cascade into a Pinterest-style variable-height grid using Masonry v4.2.2
- **Infinite scroll** on `/articles/`, `/topic/[slug]/`, and `/search/` — next page loads automatically as reader scrolls, appended items integrated with Masonry layout. Status indicators: loading spinner, "All articles loaded", error state
- Three JS libraries bundled in `assets/js/`:
  - `jquery.masonry.min.js` v4.2.2
  - `jquery.imagesloaded.min.js` v5.0.0
  - `jquery.infinitescroll.min.js` v5.0.0
- Scripts registered in `functions.php` with proper jQuery dependencies, enqueued conditionally on archive/search/tax pages only — not loaded on every page

### Changed
- `archive-ce_article.php` — rewritten with `.article-card` Masonry items, `.grid-sizer` column width reference, Infinite Scroll bound to `.pagination .next`, fallback pagination hidden when JS active
- `archive-ce_topic.php` — same Masonry + Infinite Scroll integration
- `search.php` — Infinite Scroll added to results list (list layout preserved, not masonry)
- `assets/css/main.css` — masonry clearfix, `.grid-sizer` responsive widths, `.inf-scroll-spinner` keyframe added

---

## [1.2.8] — 2026-03-16

### Fixed
- **Hero spacing** — excessive vertical gaps between the nav, the Ar-Rahman verse image, the translation, and the search bar. Root cause: two conflicting `padding-top` rules on `.hero` were stacking (~8rem total above the verse). Fixed values:
  - Hero base padding: `4rem` → `1.5rem`
  - Total-offset override: `+ 4rem` → `+ 1rem`
  - Verse image wrap gap: `0.5rem` → `0.25rem`
  - Verse image wrap margin-bottom: `1.8rem` → `0.8rem`
  - Verse image max-width: `580px` → `500px`
  - Translation top margin: `0.3rem` → `0.1rem`
  - Hero sub top margin: `1.2rem` → `0.8rem`
  - Search top margin: `3rem` → `1.8rem`

---

## [1.2.7] — 2026-03-16

### Fixed
- **Search results page dysfunctional** — no `search.php` template existed. WordPress was falling back to `index.php` which rendered search results in a generic card grid with no search-specific UI.

### Added
- `search.php` — dedicated search results template. Features:
  - Hero block: "Results for: [term]" with result count
  - Inline search bar pre-filled with current query for easy refinement
  - Results list: numbered rows with topic tag, reading time, title, excerpt
  - Pagination for results spanning multiple pages
  - Empty state: clear messaging, 10 suggested search terms, quiz CTA and browse-all link
  - Properly queries both `post` and `ce_article` post types

---

## [1.2.6] — 2026-03-16

### Changed
**Persona names removed from all public-facing surfaces.** Persona labels (The New Atheist, The Agnostic, etc.) are part of the gamification experience — they are revealed inside the journey itself, not before it. They now appear only within the journey HTML files.

- `page-journeys.php` — completely redesigned. 12 cards now describe each path by intellectual/emotional starting point only. No persona labels. Cards show "Path 01" through "Path 12" with a question and description that speaks to where the reader is, not what they are called. Hero section rewritten with "Where you start is everything." How-it-works three-step block added. Progress state (started/complete) shown via badge without naming the persona.
- `page-progress.php` — journey cards now show "Path 01" through "Path 12" instead of persona names
- `single-ce_article.php` — return widget shows "Path 01" etc. instead of persona name
- `front-page.php` — returning visitor CTA now reads "Resume your path · [Screen]" and "Begin your path" instead of revealing persona name
- `functions.php` — WordPress journey page titles changed from persona names to "Path 01" through "Path 12"

### Fixed
- Persona names were leaking into the article return widget, the progress dashboard, the homepage CTA, and the journeys overview page — all four surfaces now clean

---

## [1.2.5] — 2026-03-16

### Fixed
- **Site name missing from nav** — `bloginfo('name')` relies on the WordPress Settings → General site title being set correctly. If blank or default it rendered nothing. Nav logo now hardcodes "Compelling Evidence" as two styled spans (`<span class="nav-logo-ce">Compelling</span><span class="nav-logo-ev"> Evidence</span>`) with a purple/teal gradient underline. Custom logo image still takes priority if set in Customizer.

### Changed
- `header.php` — nav logo block updated with explicit brand name spans
- `assets/css/main.css` — `.nav-logo-ce` and `.nav-logo-ev` styles added; two-tone treatment: "Compelling" in white, "Evidence" in purple

---

## [1.2.4] — 2026-03-16

### Fixed
- **Hero verse image** — now uses `al-rahman-transparent.png` (background removed) instead of the original with dark rectangle. Verse calligraphy floats cleanly over the deep-space hero background with purple drop-shadow glow.
- **Live search broken** — `main.js` had a premature `})();` IIFE closure after the search block, leaving the random button, featured strip, and all subsequent code outside the function scope and non-functional. Entire `main.js` rewritten as a single clean IIFE.
- **Random article button broken** — same IIFE breakage. Also added missing nonce to the AJAX call.
- **PHP compatibility** — replaced arrow function `fn($p) => [...]` in `ce_ajax_search()` with `function($p) { return [...]; }` for PHP 7.3 compatibility.

### Added
- `hero-translation` element — italic translation "Then which of the favours of your Lord will you deny?" displayed below the Arabic calligraphy
- `hero-bismillah` updated — now shows "Surah Ar-Rahman (55:13)" as formal citation
- `screenshot.png` updated to reflect corrected hero

### Changed
- `front-page.php` — hero now uses `al-rahman-transparent.png`, adds `.hero-translation` paragraph
- `assets/css/main.css` — hero verse styles updated for transparent PNG compositing
- `assets/js/main.js` — fully rewritten, 219 lines, single IIFE, ES5-compatible
- `functions.php` — arrow function replaced with standard anonymous function

---

## [1.2.3] — 2026-03-16

### Fixed
- **Critical: quiz and journey pages returning PHP fatal error.** Root cause: `ce_create_next_articles()` had 20KB of article content (three heredoc strings) embedded directly inside `functions.php`. This caused WordPress to load ~20KB of article HTML into memory on every page request — including the quiz and journey pages — triggering PHP memory exhaustion.
- `ce_create_next_articles()` refactored: article content moved to `inc/articles-data-next.php`. `functions.php` now loads all article data only during theme activation via `require_once` inside activation hooks, never on normal page loads.
- All four article data sets (next, set 1, set 2, set 3) confirmed load only inside activation functions — verified by static analysis.
- All PHP template files verified for balanced PHP open/close tags.
- `functions.php` reduced from 877 lines to 723 lines.

### Added
- `inc/articles-data-next.php` — the three next-question articles extracted from `functions.php`

---

## [1.2.2] — 2026-03-16

### Added
- `assets/images/al-rahman.png` — the Ar-Rahman verse calligraphy image
- `assets/images/al-rahman-transparent.png` — background-extracted version for CSS compositing
- Hero section of front-page.php now displays the actual Arabic calligraphy (`al-rahman.png`) with a CSS drop-shadow glow animation, replacing the plain text hero-verse heading
- `screenshot.png` updated — verse calligraphy now composited directly into the hero area of the thumbnail

### Changed
- `front-page.php` — hero restructured: `<img class="hero-verse-img">` replaces `<h1 class="hero-verse">` text
- `assets/css/main.css` — added `.hero-verse-img-wrap`, `.hero-verse-img` (with `verseGlow` keyframe animation), updated `.hero-bismillah`

---

## [1.2.1] — 2026-03-16

### Added
- `screenshot.png` (880×660px) — WordPress theme thumbnail shown in Appearance → Themes. Dark editorial design showing nav, hero with CTA buttons, 7-card persona journey row with per-card progress bars and completion badge, and 4-column article grid.

---

## [1.2.0] — 2026-03-16

### Added

**Social sharing + completion badge**
- Journey completion badge appears on conclusion screen — gold ✦ with shimmer animation, persona name, "tap to share"
- Share row: X/Twitter, WhatsApp, Copy link, native device share (if supported)
- Share text is persona-specific: "I followed the evidence honestly on the [name] path — Compelling Evidence"
- Injected via `ce-social-badge` script into all 12 journey files

**My Progress dashboard** (`/my-progress`, template: `page-progress.php`)
- Personal journey dashboard driven entirely by localStorage
- Stats row: paths started, paths completed, matched persona from quiz
- 12 journey cards showing state (not started / in progress / completed), mini progress bar, current screen, and action button
- Quiz CTA shown if no quiz taken
- Clear all progress button with confirmation
- Auto-created on theme activation

**Returning visitor homepage state**
- Homepage hero CTA dynamically updates based on localStorage:
  - Completed path: gold "Journey complete — revisit" button
  - In-progress path: "Resume — [name] · [screen]" button
  - Quiz taken, not started: "Begin your journey — [name]" button
- Matched path pill highlighted in journey entry section
- Stats row "X started · X completed" with link to progress dashboard
- My Progress icon in nav (clock icon, hidden until quiz taken)

**Navigation updates**
- Nav fallback menu: Journeys, Articles, Ask a Question
- My Progress clock icon appears in nav-actions once quiz is taken

### Changed
- `header.php` — updated fallback nav, added My Progress icon
- `front-page.php` — returning visitor JS + je-stats div
- `functions.php` — My Progress page created on activation

---

## [1.1.0] — 2026-03-16

### Added

**Content layers — article ecosystem**
- 26 additional articles (Set 3) addressing the hardest Islamic and philosophical objections: Aisha's age, Banu Qurayza, Gharaniq incident, slavery in Islamic sources, Meccan/Medinan split, hadith reliability, Euthyphro dilemma, divine hiddenness, qadar (free will/predestination), evolution and Islam, religious trauma, unanswered prayer, universal salvation, burden of proof, God as psychological projection, and more
- "Hard Questions" taxonomy tag applied to all Set 3 articles — accessible at `/topic/hard-questions/`
- Total articles on activation: 75 (up from 45 in 1.0.0)

**In-journey go-deeper links**
- Dynamic "Go deeper" sidebar widget injected into all 12 journey files via JavaScript
- Chapter → article mapping: Singularity → cosmological articles, Calibration → fine-tuning, Emergence → consciousness, Constant → moral argument, Entropy → evil and suffering, Signal → argument from reason
- Widget updates automatically as reader progresses through screens
- No journey flow interrupted — widget is additive to existing sidebar

**Article → journey return widget**
- Every article sidebar now shows a journey return widget driven by localStorage
- States: "Resume your journey" (saved progress), "Begin journey" (quiz taken), "Find your path" (quiz CTA)
- Closes the loop between article reading and journey completion

**New archive templates**
- `archive-ce_article.php` — full article listing at `/articles/` with topic filter tabs
- `archive-ce_topic.php` — topic archive pages at `/topic/[slug]/` with Hard Questions banner
- All archive templates include journey return CTA

**Documentation**
- Content architecture documentation added to README.md (three-ring model)
- CHANGELOG.md updated
- Version bumped to 1.1.0

### Changed
- `single-ce_article.php` — added journey return widget to sidebar
- `single.php` — same update applied
- `functions.php` — ce_create_articles_3() tags all Set 3 articles with "Hard Questions" term; ce_article CPT has_archive confirmed
- All 12 journey files — go-deeper script injected

### Fixed
- Islamic framing removed from general theism articles (replaced with tradition-agnostic language)
- Reading time and progress bars active on all article pages

---

## [1.0.0] — 2026-03-15

Initial release.

### Added

**Theme core**
- Child theme of Twenty Twenty-Five
- Custom Post Type: `ce_article` with archive at `/articles`
- Custom Taxonomy: `ce_topic` (hierarchical, attached to `ce_article` and `post`)
- AJAX endpoints: `ce_search`, `ce_random`, `ce_save_progress`, `ce_get_progress`, `ce_save_quiz`
- Auto-creation of all 12 journey pages + quiz page + journeys overview on theme activation
- Rewrite rules for `/journey/[slug]/` URLs
- Progress sync: localStorage (guests) + WP user meta (logged-in users)
- Ask a Question form handler with email notification to admin

**Design system**
- Full CSS design system in `assets/css/main.css` — 1,207 lines
- Colour palette: deep purple/charcoal (60%), white/near-white (30%), teal #0cd4e0 + gold #f5c518 (10%)
- Typography: Playfair Display (headings), DM Sans (body), Cormorant Garamond (reading), Amiri (Quranic Arabic display)
- Physics labelling system: Horizon, Singularity, Calibration, Emergence, Constant, Entropy, Signal

**Journey system**
- 12 persona paths, each with 7 chapters + conclusion (96 screens total)
- The New Atheist — Dawkins/Hitchens/Harris named; EAAN; quantum vacuum rebuttal
- The Agnostic — "What would evidence look like"; reflection choices never penalise disagreement
- The Secular Humanist — "Inward and around, not upward"; Humanist Operating System
- The Antitheist — Opens with agreement; Hitchens challenge addressed
- The Materialist — Zombie argument; physicalism self-defeats in Signal
- Muslim with Doubts — 7 named doubts including Mecca/Medina split; hospital story; dual life acknowledgement; anger phase; scale data (85% of Iranians less religious; Arab Barometer); post-Muslim framework; Ar-Rahman conclusion; taqiyya / permanent dual existence
- The Apatheist — Coffee/13.8 billion years framing; life sufficiency argument; Maldivian quote
- The Deist — Watchmaker analogy dismantled chapter by chapter; gap narrows to personal God
- The Scientist — Scientism self-refutes; Fred Hoyle quote; Haldane quote; Farhan; inference to best explanation throughout
- The Classical Atheist — Folk-religious vs classical theist's God; four incoherence objections with styled pairs; modal logic; UK cousin binary in conclusion
- The Ex-Believer — "You were right to leave" opening; life sentence anchor; social infrastructure / mechanisms; Aaliyah's financial independence advice; "died standing up" poem; baby/bathwater conclusion
- The Spiritual Seeker — Tradition convergence grid (Buddhism/Hinduism/Sufism/Classical Theism); raft metaphor; hidden treasure hadith; Tat tvam asi; Namira's eclectic testimony

**Persona connection system**
- Multi-dimensional intake quiz: 5 questions, 29 answer weightings, primary + secondary resonances
- localStorage progress saving with resume banner (bottom-right, appears if left mid-journey)
- Secondary resonance sidebar widget (reads quiz secondaries from localStorage, falls back to curated defaults)
- Path marked as started/completed in separate localStorage arrays

**Content integrity**
- All apostate testimonies obscured: no identifying names, roles, or location+role combinations in running text
- Philosopher/scientist names retained (Dawkins, Hitchens, Chalmers, Haldane, Hoyle, Penrose, Nagel, Plantinga, C.S. Lewis, Carl Sagan) — these are cited for published arguments, not personal testimony
- Universal content inserts across all 12 paths: corrupt judge argument, "Suffering is not currency", argument from reason

**Documentation**
- `readme.txt` — WordPress.org format
- `README.md` — Developer reference
- `UPGRADING.md` — Upgrade and migration procedures
- `CHANGELOG.md` — This file

---

## Planned — [1.1.0]

### To Add
- Three `ce_article` posts: Does God Communicate? / What Would Revelation Look Like? / Evaluating Competing Claims
- Article ecosystem: conclusion next-question links resolve to actual WP article URLs
- Social sharing buttons on conclusion pages
- Journey completion state: visual indicator on journey cards in `/journeys`
- Quiz version key in localStorage to invalidate stale results on scoring changes

### To Add — Localisation (ms-MY)
- ms-MY journey HTML files — 12 translated journey files + quiz in `/journeys/ms-MY/`
- Compile `ms_MY.mo` from `ms_MY.po`: `msgfmt languages/ms_MY.po -o languages/ms_MY.mo`
- Locale switching: set WordPress language to Malay (Malaysia) in Settings → General
- `page-journey.php` and `page-quiz.php` already handle locale detection and fallback

### To Add — User Accounts
- User registration flow (optional — progress works without accounts)
- Cross-device progress sync for logged-in users
- Journey completion history in user profile

### To Fix
- Resume banner z-index conflict on mobile viewport when keyboard is open
- Quiz result screen animation timing on iOS Safari

---

## Notes on Versioning

**PATCH** (1.0.x) — Journey content updates, copy edits, bug fixes, CSS tweaks. No PHP changes. Safe to deploy by replacing files directly.

**MINOR** (1.x.0) — New journey paths, new templates, new AJAX endpoints, new quiz questions or scoring changes. Requires permalink flush after deployment.

**MAJOR** (x.0.0) — Breaking changes to URL structure, database schema changes, slug renames, or parent theme change. Requires migration procedure per UPGRADING.md.
