# Upgrading — Compelling Evidence Theme

This document describes every real migration path required when upgrading the theme.
Only paths explicitly identified in development are documented here.
"Not specified in context" is written where migration details are unknown.

---

## Upgrading to v2.6.33 from v2.6.32

**After deploying:**
1. Deactivate and delete the **Lightbox2** and **Pretty Search Permalinks** plugins; the theme now provides both. Then visit Settings → Permalinks once to refresh rewrite rules.
2. Open Theme Options → Media and press **Import pending images now** so the four registered images enter the Media Library.
3. Keep Contact Form 7, CFDB7 and WPS Hide Login installed.

**Note:** the article sync will update the eight batch-007 articles; their checksums in `manifest.json` have changed accordingly.

---

## Upgrading to v2.6.32 from v2.6.31

**Required actions:** None. Behaviour is unchanged apart from the Ask a Question form's placeholder email.

---

## Upgrading to v2.6.31 from v2.6.30

**Required actions:** None. Purge page caches.

**With Rank Math active:** the theme no longer prints its own meta description, canonical or Open Graph tags. Check that Rank Math has descriptions for the Journeys, Quiz, FAQ, Glossary and Q&A pages, and that its topic (taxonomy) description template does not use "Compelling Evidence article topic".

**Existing topic terms** may still hold the old placeholder description. The theme ignores it, but you can clear it under Articles → Topics.

**After deploying:** run a few URLs through Google's Rich Results Test (an article, an answered question, a journey), and request re-indexing of one journey in Search Console so the corrected canonical is picked up.

---

## Upgrading to v2.6.30 from v2.6.29

**Required actions:** None. Purge any page or asset cache so the new `journey-base.css` and the updated article templates are served.

**Check after upgrading:** on a phone, open a journey (one column, no sideways scrolling) and the menu (nothing showing when closed). On a desktop, scroll an article with the pointer over the sidebar: the contents list and reading progress should stay in view.

---

## Upgrading to v2.6.29 from v2.6.28

**Required actions:** None. Visit any nonexistent URL to see the new page. If a page cache stores 404 responses, purge it so the random excuse varies between visits.

---

## Upgrading to v2.6.28 from v2.6.27

**Required actions:** Keep Twenty Twenty-Five installed and at 1.5 or later, and run WordPress 6.7 or later.

**Check after upgrading:** open a plain page (About or Contact), a search results page and a nonexistent URL (404). All three should now use CE's header, colours and fonts.

**Site Editor:** if anyone previously customised a template in Appearance → Editor, that saved template still takes priority over CE's PHP template for its view. Reset it in the Site Editor to return that view to CE.

---

## Upgrading to v2.6.27 from v2.6.26

**Required actions:** None. If a caching or optimisation plugin combines JavaScript, purge its cache so `assets/js/ce-toc.js` is picked up.

---

## Upgrading to v2.6.26 from v2.6.25

**Required actions:** None. Clear any page cache so the homepage picks up the new card markup.

**Featured images:** article cards with a featured image now crop it to 16:9 (previously 4:3). Check any image whose subject sits near the top or bottom edge.

---

## Upgrading to v2.6.25 from v2.6.24

**Required actions:** None. The theme appears as "CE Theme" under Appearance → Themes. WordPress identifies an active theme by its folder, which is still `compelling-evidence`, so activation, settings and child-theme links are unaffected.

---

## Upgrading to v2.6.24 from v2.6.23

**Required actions:** None. Sabon Next LT appears as a new choice under Theme Options → Typography; existing selections are unchanged.

**If you upgrade straight from 2.6.22,** read the 2.6.23 notes below as well: the Typography settings take effect for the first time in that release.

---

## Upgrading to v2.6.23 from v2.6.22

**Required actions:** None. Upload and activate the new package.

**Check the Typography tab after upgrading.** The Heading Font and Reading Body Font settings now take effect for the first time. A site where either was changed in the past, while it had no effect, will switch to that saved choice on upgrade. Open Theme Options → Typography and confirm both selections are the ones intended.

**Preloading.** If Performance → Preload critical fonts was unticked, the site stops preloading fonts after this upgrade, because the setting is now honoured.

---

## Upgrading to v2.6.22 from v2.6.21

**Required actions:** None. Upload and activate the new package.

**What happens on the first admin page load:** the version-keyed content sync runs once for 2.6.22 and inserts the eight batch-007 articles as new `ce_article` posts with their topics. Existing articles are re-synced as with every release.

**Check after upgrading:** Theme Options → Tools & Sync should report seven batches and 131 articles. If sync reports a checksum mismatch for `batch-007.json`, the file was altered in transit (FTP line-ending conversion is the usual cause); re-upload it in binary mode.

**Prerequisite from 2.6.21:** restore the missing core file `wp-admin/includes/schema.php` before upgrading if that repair has not yet been done.

---

## Upgrading to v2.6.21 from v2.6.20

**Required actions:** None for the theme. Upload and activate the new package; all changes take effect immediately.

**What happens on the first admin page load:**

- `ce_seed_question_statuses()` confirms the five question-status terms exist and sets `ce_qstatus_seeded`. Existing terms are left untouched.
- The analytics and engagement table checks run through the new `ce_create_table()` helper. Existing tables are left untouched.
- The version-keyed content sync runs once for 2.6.21, as with every release.

**New settings (Theme Options → Performance → Retired URLs):** 410 Gone is enabled by default for `/shop/manufacturer-site`, `/product-similar-image` and `/product/category/`. Disable the checkbox or edit the list if any of these prefixes is ever needed for real content.

**Server-side repairs this release does not perform (see the 2.6.21 changelog background):**

- Restore the missing core file `wp-admin/includes/schema.php` by reinstalling WordPress core from outside the dashboard (the dashboard reinstall itself loads `upgrade.php` and will fail).
- Recreate the damaged Rank Math tables (`compel_rank_math_404_logs`, `compel_rank_math_analytics_objects`).

---

## Upgrading to v2.6.10 from v2.6.9

**Required actions:** None. The upgrade is automatic on the next `admin_init`.

**Architectural change — front-page mission content moved to About page:**

The "What this site is" mission section (~650 words) that previously rendered on the home page between the quick-access cards and the article grid has been removed from `front-page.php`. The same content, in a stronger and corrected form, now lives on the auto-created About page (`/about-compelling-evidence/`).

**For end users:** the home page is now lighter and lets articles surface above the fold. The full editorial framing (premise, method, what the work covers, who it is for, note on tone) remains accessible via the About page, linked from the site footer and main navigation.

**For existing installs (theme previously activated before v2.6.10):**

The auto-page-creation routine in `inc/ce-secondary-pages.php` only runs once per site (using the `ce_secondary_pages_created` flag), which means simply updating the theme file does NOT refresh an existing About page in the database. v2.6.10 adds a new one-time upgrade hook to handle this:

- New function: `ce_update_about_page_v2()` in `inc/ce-secondary-pages.php`
- New option flag: `ce_about_v2_updated`
- Fires on `admin_init` exactly once
- Locates the existing About page by slug (`about-compelling-evidence`)
- Refreshes its `post_content` via `wp_update_post()`
- Sets the flag so it never overwrites again

**What this means in practice:** the first time any admin loads any wp-admin page after the upgrade, the About page content is silently refreshed. No manual action required. If no About page exists yet (fresh install), the function exits early and the standard auto-creation routine handles things on the same hook.

**If you have customised the About page in the WP admin and want to keep your customisations:** set the flag manually before loading wp-admin:

```bash
wp option update ce_about_v2_updated 1
```

This skips the one-time refresh entirely and your custom content is preserved.

**If you want to run the refresh again** (e.g. you accepted the new content but later want to apply further refinements that ship in a future version):

```bash
wp option delete ce_about_v2_updated
```

Next admin page load will re-fire the refresh.

**No database schema changes. No content sync re-trigger. No plugin dependencies.**

---

## Upgrading to v2.5.0 from v2.4.9

**Required actions:** None beyond the general upgrade procedure.

**Q&A system — important behaviour change:**

Existing submitted questions in your database are unaffected, but the workflow
has changed for any future submissions and for any existing questions that you
edit after upgrading:

- New questions are now created with `post_status = 'pending'` and `ce_qstatus = 'new'`.
  They are no longer publicly accessible at `/qa/{slug}/` until both states transition.
- When you mark a question's `ce_qstatus` as **Published**, the WordPress `post_status`
  now flips to `publish` automatically. You no longer need to do this in two steps.
- Reverting `ce_qstatus` to any other state (New / In Review / Answered / Archived)
  automatically demotes `post_status` back to `pending`, hiding the page from the
  public again.
- Editors and above (`edit_posts` capability) can preview at any workflow stage.

**If you have existing published questions** with `post_status='publish'` but
`ce_qstatus` not yet set to `published`: they will be 404'd to non-admins until
you set `ce_qstatus='published'`. To find them:

```
wp post list --post_type=ce_question --post_status=publish --format=ids \
  | xargs -I{} wp eval 'echo wp_get_object_terms({}, "ce_qstatus", ["fields"=>"slugs"])[0] ?? "—", "\n";'
```

Any question reporting anything other than `published` should have its `ce_qstatus`
updated in the WP admin.

**


**What changes automatically on first admin page load:**
- `ce_content_sync_2_5_0` key not yet set → sync fires
- `term_exists` cache miss recovery now active in both sync branches — topic assignments will complete correctly even on Redis/Memcached hosts where the object cache returns a stale miss mid-request
- Manual sync button (`Appearance → CE Theme Options → Tools & Sync → Run Content Sync Now`) now bypasses the object cache entirely via `$force_run = true`, making it reliable on all hosting configurations
- The `?ce_force_sync=1` URL parameter path is preserved for direct-URL admin access

**No visible changes from v2.4.9 for end users.** All fixes are internal to the sync engine.

**If topic categories were still incorrect after v2.4.9:** The `term_exists` cache miss fix in this version is the resolution. After uploading v2.5.0, the sync fires automatically and categories will be correctly assigned on first admin page load.

**To verify:** Go to `/articles/` — all 11 topic sections should be present including Islamic Practice & Ritual with 6 articles.

---

## Upgrading to v2.4.9 from v2.4.8

**Required actions:** None beyond the general upgrade procedure.

**What changes automatically:**
- WordPress RSS/Atom feeds are now suppressed site-wide. Any existing feed subscriber
  bookmarks or aggregator subscriptions will receive a 301 redirect to the canonical page.
- Feed autodiscovery `<link>` tags are removed from all `<head>` outputs.
- `robots.txt` gains 8 new `Disallow` lines for feed URL patterns.
- Q&A page (`/qa/`) is now fully styled — matching the site's design language.
- Q&A topic sidebar renders in canonical topic order.

**Regression note:** v2.4.8 dropped the Q&A CSS and canonical topic ordering that were
introduced in v2.4.7. Both are restored in v2.4.9. If you are upgrading from v2.4.8,
the Q&A page will become properly styled after this update.

**If you use an RSS plugin or email newsletter integration** that depends on WordPress feeds,
audit the integration before deploying. The redirector targets all standard feed URL formats.
Custom feed endpoints registered via `add_feed()` are unaffected.

## Upgrading to v2.4.7 from v2.4.6

**Required actions:** None.

**What happens automatically on first admin page load:**
- `ce_content_sync_2_4_7` key not yet set → sync fires
- Slug rename handling updates old-slug articles in place
- Orphan detection now properly trashes duplicate articles (old-slug versions)
- Subcategory groupings on `/articles/` now render correctly

**To verify:** Check `/articles/` — articles should now appear in their proper subcategories (e.g., Science & Evidence → What Science Can and Can't Do / Cosmology & Origins / Mind & Experience).

---

## Upgrading to v2.4.6 from v2.4.5

**Required actions:** None.

**What happens automatically on first admin page load:**
- `ce_content_sync_2_4_6` key not yet set → sync fires
- Slug rename handling now active — articles with old slugs are updated in place, `post_name` renamed to match JSON
- Orphan detection trashes duplicate articles created by previous slug changes
- Subcategory groupings now render correctly on `/articles/` — articles match the slug lists in `$subcategories` array

---

## Upgrading to v2.4.5 from v2.4.4

**Required actions:** None.

**What happens automatically on first admin page load:**
- `ce_content_sync_2_4_5` key not yet set → sync fires
- Missing `ce_cleanup_deprecated_topics()` function now present — retired topics properly deleted
- Topic name corrected: "Islamic Beliefs & Practice" → "Islamic Practice & Ritual" (matches JSON data)
- Topic migration logic now active — articles from retired topics reassigned to current topics
- All 11 canonical topic sections appear correctly on `/articles/`

---

## Upgrading to v2.4.4 from v2.4.3

**Required actions:** None.

**What happens automatically on first admin page load:**
- `ce_content_sync_2_4_4` key not yet set → sync fires
- Islamic Practice & Ritual taxonomy term created
- 6 articles reassigned from Rights & Freedom to Islamic Practice & Ritual
- All subcategory groupings appear on `/articles/`

---

## Upgrading to v2.4.3 from v2.4.2

**Required actions:** None.

**What happens automatically on first admin page load:**
- Sync creates `Islamic Practice & Ritual` taxonomy term (slug: `islamic-practice-ritual`).
- 6 articles moved from Rights & Freedom to Islamic Practice & Ritual in the DB.
- `/articles/` shows 11 topic sections, new subcategory groupings across Science & Evidence, Rights & Freedom, Revelation & Meaning, The Quran & Its Sources.

---

## Upgrading to v2.4.2 from v2.4.1

**Required actions:** None.

**What happens automatically:**
- Sync fires on first admin page load — all 120 articles updated with revised argument content from v2.4.1 batch files.
- `/articles/` shows all 120 articles across 10 topic sections including new subcategory groupings for Does God Exist? and The Quran & Its Sources.

---

## General Upgrade Procedure (Any Version)

1. **Back up** the WordPress database.
2. Deactivate the current theme temporarily (switch to Twenty Twenty-Five).
3. Delete the old `compelling-evidence` theme folder via FTP or File Manager.
4. Upload the new zip via **Appearance → Themes → Add New → Upload Theme**.
5. Activate the theme.
6. Load any WordPress admin page — auto-run modules fire on `admin_init`.
7. Go to **Settings → Permalinks → Save Changes**.
8. Verify articles at `/articles/` and journeys at `/journeys/`.

---

## Content Sync Behaviour on Upgrade

`ce-content-sync.php` stores a sync key derived from the theme version string. On upgrade, the new version string produces a new key. On next admin page load, the sync module detects the missing key and re-syncs all article content from the JSON data files to the database.

**Consequence:** Any manual edits to article content in the WordPress editor (`wp_posts.post_content`) will be overwritten by the sync. The JSON data files in `inc/articles/` are the authoritative source for article content. Do not use the WordPress editor as a content authoring tool.

---

## Upgrading to v2.3.9 from v2.3.8

**Required actions:** None.

**What happens on first admin page load after deploy:**
- Version key `ce_content_sync_2_3_8` is superseded by `ce_content_sync_2_3_9`
- `ce_sync_article_content()` fires via `admin_init`
- All 120 articles updated, topics reassigned, orphans trashed, retired taxonomy terms deleted
- `/articles/` should show all 120 articles across 10 topic sections

**If articles still do not appear after deploy:**
1. Go to **Appearance → CE Theme Options → Tools & Sync**
2. Check the diagnostics panel — verify all checksums pass and loader reports 120 articles
3. Click **Run Content Sync Now** — this now clears the version key and runs the full sync
4. Reload `/articles/`

---

## Upgrading to v2.3.8 from v2.3.7

**Required actions:** None. No migration steps needed.

**What changes automatically:**
- Content sync fires on first admin page load — all 120 articles updated and recategorised.

**Visible fix on `/articles/`:**
- *coming-back-after-leaving* now appears under **The Inner Journey → What Happens Next**. No DB change required — the article was already published with the correct topic; the archive template was simply not listing it.

---

## Upgrading to v2.3.7 from v2.3.6

**Required actions:** None beyond the general procedure.

**What happens automatically on first admin page load:**
1. Sync fires (new version key `ce_content_sync_2_3_7` not yet set).
2. All 120 articles updated from JSON — topics reassigned per current batch data.
3. Old slug `if-god-answers-prayer-why-cant-you-prove-it` post renamed to `does-god-answer-prayer` in place. If a duplicate new-slug post exists, it will be detected as an orphan and trashed.
4. Any other orphaned `ce_article` posts (slugs not in JSON) are trashed automatically.
5. `Islamic Beliefs & Practice` and `Rights, Freedoms & Hard Questions` taxonomy terms deleted.

**Verify after upgrade:**
- Topics list should show exactly 10 terms, all with article counts > 0.
- Articles list should show exactly 120 published articles.
- `/articles/does-god-answer-prayer/` should load correctly.
- Add a 301 redirect from `/articles/if-god-answers-prayer-why-cant-you-prove-it/` if you have external links to the old URL.

---

## Upgrading to v2.3.6 from v2.3.5

**Required actions:** None beyond the general procedure.

**What changes automatically:**
- Content sync fires on first admin page load. All 120 articles re-synced from redistributed batch files (now exactly 24 per batch).
- If any articles were assigned to the retired categories `Islamic Beliefs & Practice` or `Rights, Freedoms & Hard Questions`, the topic migration map in `ce-content-sync.php` will remap them to their new categories during sync.

**Manual action required:**
- If the article `if-god-answers-prayer-why-cant-you-prove-it` exists in your database, it will be updated in place (the slug is unchanged in the database — only the crosslink reference was updated). No redirect needed unless you have external links pointing to that slug, in which case add a 301 redirect to `/articles/does-god-answer-prayer/`.
- Favicons should appear immediately after deploy. If browser is caching old icons, clear cache or hard reload.

---

## Upgrading to v2.3.5 from v2.3.4

**Required actions:** None beyond the general procedure.

**What changes automatically:**
- `inc/articles/diagnose.php` is removed. If the old file exists in your deployment, delete it from the server manually — it should not be publicly accessible.

**New in Theme Options:**
- Diagnostics panel added to **Appearance → CE Theme Options → Tools & Sync**. Use this instead of the removed `diagnose.php` to verify manifest checksums, loader health, and sync key status.

---

## Upgrading to v2.3.4 from v2.3.3

**Required actions:** None beyond the general procedure.

**What changes automatically:**
- Content sync fires on the first admin page load after upgrade. All 120 articles will be re-synced with correct content. Previous versions had a broken sync (manifest checksum mismatch) so articles may have been empty — this upgrade resolves that.
- Deprecated topic names (`Examining the Quran`, `Examining the Sources`, `History & Context`, `The Bigger Picture`) are remapped to their v2.3.1 replacements during sync if any articles still reference them.

**If articles are still empty after upgrade:**
1. Check that the theme is fully replaced (not just updated over old files).
2. Check PHP error log for `CE Article Loader` entries — any remaining checksum failures will appear there.
3. Force a fresh sync:
   ```bash
   wp option delete ce_content_sync_2.4.4
   wp eval 'ce_sync_article_content();'
   ```

---

## Upgrading to v2.3.3 from v2.3.2

**Required actions:** None beyond the general procedure.

**What changes automatically:**
- Topic migration map applied during sync — articles referencing deprecated topic names are moved to current categories.
- Archive page updated to show current topic names and descriptions.

---

## Upgrading to v2.3.0 from v2.2.78

**CRITICAL: Article Data Migration Required**

This version introduces a **security-hardened JSON article storage system** that replaces the legacy PHP data files. You MUST convert your article data before deployment.

### Pre-Deployment: Convert Article Data (REQUIRED)

**This step is mandatory.** The theme will refuse to sync if JSON files are not properly converted.

1. **Ensure PHP is available** on your system (PHP 7.4+ required)
2. **Run the converter script:**
   ```bash
   php build-articles.php
   ```
3. **Verify the output:** Check that `inc/articles/` contains:
   - `manifest.json` (registry with checksums)
   - `batch-001.json` through `batch-005.json` (120 articles total)

**If you skip this step:** The content sync will detect incomplete data and abort with an error message in the logs.

### What the Converter Does

| Input (Old) | Output (New) |
|-------------|--------------|
| `inc/articles-data.php` | `inc/articles/batch-001.json` (Articles 1-20) |
| `inc/articles-data-2.php` | `inc/articles/batch-002.json` (Articles 21-41) |
| `inc/articles-data-3.php` | `inc/articles/batch-003.json` (Articles 42-76) |
| `inc/articles-data-4.php` | `inc/articles/batch-004.json` (Articles 77-90) |
| `inc/articles-data-5.php` | `inc/articles/batch-005.json` (Articles 91-119) |
| `inc/articles-data-6.php` | `inc/articles/batch-005.json` (Article 120 — added v2.3.1) |

### Deployment Steps

1. **Backup database** before upgrade
2. **Delete old theme folder** via FTP
3. **Upload new theme** with converted JSON files
4. **Visit /wp-admin/** to trigger content sync
5. **Go to Settings → Permalinks → Save Changes** (for Q&A CPT)

### What Changes Automatically

- All 120 articles re-synced from JSON (secure, non-executable format)
- PHP 8.4 compatibility fixes applied
- Q&A system becomes available (empty until questions submitted)
- SHA256 checksums verify article data integrity on each load

### Security Improvement

| Before (v2.2.x) | After (v2.3.0) |
|-----------------|----------------|
| PHP files loaded via `require_once` (code execution risk) | JSON files loaded via `file_get_contents` + `json_decode` (data only) |
| Parse errors crash admin | JSON errors logged gracefully, sync continues |
| No tamper detection | SHA256 checksums verify file integrity |
| Apostrophe escaping complexity | Standard JSON encoding |

---

## Upgrading to v2.2.78 from v2.2.77

**Required actions:** None beyond the general procedure above.

**What changes automatically:**
- 18 new articles created (orders 102-119)
- All 120 articles re-synced with updated content (content sync fires on version change)
- Cache-Control headers added — first deploy clears old no-cache behaviour for return visitors
- templates.css no longer loads on front page — any front-page-specific styles added there manually will stop applying on the homepage

**Manual action required:**
- Merge `.htaccess-performance` into WordPress root `.htaccess` for compression/cache rules. Open the file, copy the blocks marked `COMPRESSION` and `CACHE LIFETIMES`, paste above the `# BEGIN WordPress` marker in root `.htaccess`.

---

## Upgrading to v2.2.77 from v2.2.76

**Required actions:** None beyond the general procedure.

The unified crosslink + tooltip engine replaces two separate `the_content` filters. If any external code or plugin hooks into `ce_crosslinks_filter` or `ce_tooltips_filter` by name, those hooks no longer exist — both are now handled inside `ce_unified_link_filter`.

---

## Upgrading to v2.2.76 from v2.2.75

**Required actions:**

Amiri font files were deleted in this version. If you have manually referenced Amiri in any custom CSS or child-of-child theme:

```css
/* BEFORE — no longer valid */
font-family: 'Amiri', serif;

/* AFTER — use CE Hadith for Arabic, Cormorant for Latin */
font-family: 'CE Hadith', 'Noto Naskh Arabic', serif; /* Arabic */
font-family: 'Cormorant Garamond', serif;              /* Latin reading */
```

Theme Options were introduced in this version. All configuration previously requiring template edits is now accessible at **Appearance → CE Theme Options**. Existing hardcoded values in `main.css :root` remain as defaults; Theme Options settings take precedence via `wp_head` inline CSS injection.

---

## Upgrading to v2.2.0 from v1.x.x (Category Restructure Migration)

**This is a significant structural change.** Version 2.2.0 replaced the previous taxonomy structure with 10 canonical categories. The migration is handled automatically by `ce-migration-2-2-0.php`, but requires verification.

**What the migration does:**
1. Creates 10 new `ce_topic` taxonomy terms with canonical slugs
2. Remaps all existing `ce_article` posts to their new topic terms
3. Deletes the old taxonomy terms

**Verification steps after upgrade:**
1. Go to Articles in the WordPress admin. Confirm articles appear with correct topics.
2. Visit `/topic/does-god-exist/` — should show articles.
3. Visit `/topic/the-problem-of-evil/` — should show articles.
4. If any articles show no topic, manually assign via the WordPress editor.

**If the migration fails silently** (option flag set but articles not remapped):
```php
// In wp-admin/tools.php or via WP-CLI:
delete_option('ce_category_migration_2_2_0');
// Then reload any admin page to re-trigger the migration
```

**URL changes from v1.x to v2.2.x:**

The article CPT base slug did not change (`/articles/`). Topic archive slugs changed to match the new canonical taxonomy terms. Old topic URLs will 404 unless redirects are added:

| Old URL pattern | New URL pattern |
|---|---|
| Not specified in context | Not specified in context |

Add redirects via a plugin (Redirection, Rank Math) or via `.htaccess` if you know the old slugs.

---

## Upgrading from v1.0.0 to v1.1.0

No database migration required. The upgrade adds:
- 26 new articles (Set 3) — published automatically on next admin page load
- `archive-ce_article.php` and `taxonomy-ce_topic.php` templates
- Go-deeper sidebar widgets injected into all 12 journey HTML files

**Permalink flush required** — Settings → Permalinks → Save Changes.

---

## Adding a New Journey Volume (Future — Volume II/III)

When Volume II journeys are added:

1. Place HTML files at `/journeys/[slug]-journey.html`.
2. Add the journey page via WordPress admin (Page > Add New, assign `page-journey.php` template).
3. Set the page slug to match the journey file slug.
4. Update quiz scoring in `/journeys/quiz.html` if new personas are introduced.
5. Update the Volume I Transmission screens in all existing journeys to link to Volume II.
6. No PHP changes required for serving — `page-journey.php` resolves the journey file from the page slug.

---

## Adding Translated Journey Content (ms-MY)

1. Place translated HTML files at `/journeys/ms-MY/[slug]-journey.html`.
2. Set WordPress site language to Malay (Malaysia) in **Settings → General**.
3. `page-journey.php` and `page-quiz.php` detect the locale and load from `ms-MY/` if present, falling back to English if the translated file is absent.
4. Compile the Malay .mo file if updated:
   ```
   msgfmt languages/ms_MY.po -o languages/ms_MY.mo
   ```

---

## WP-CLI Content Sync (Manual Trigger)

To force a content sync without bumping the theme version:
```bash
wp option delete ce_content_sync_$(wp eval 'echo str_replace(".", "_", wp_get_theme()->get("Version"));')
wp eval 'ce_sync_article_content( true );'
```

The command above reads the version dynamically. Alternatively replace `{version}` with the exact string from `style.css` (e.g. `ce_content_sync_2_5_0`).

---

## Rollback Procedure

1. **Back up the database** before any upgrade.
2. To rollback: restore the previous theme zip and re-upload.
3. The content sync will re-fire with the old version key, restoring article content from the older data files.
4. `wp option delete ce_content_sync_{version}` if the sync key needs manual reset.

---

## What is NOT Required on Upgrade

- No direct database schema changes after v1.0.0 (the analytics table is created once on first activation; the `ce_article` CPT and `ce_topic` taxonomy are registered on every page load via `functions.php`).
- No wp-config.php changes.
- No server configuration changes (the `.htaccess-performance` file is additive, not required).
- No plugin installations (the theme is self-contained).

---

## Documentation Location

All theme documentation has been moved to the `/doc/` folder:

- `doc/readme.md` — Theme overview and quick start
- `doc/changelog.md` — Version history and release notes
- `doc/upgrading.md` — This file (migration instructions)
- `doc/ssot.md` — Single Source of Truth (naming conventions, system design)
- `readme.txt` — Remains in root for WordPress.org compliance

---

*Last updated: April 2026 (v2.6.10)*
