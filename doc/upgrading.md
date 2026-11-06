# Upgrading: Compelling Evidence theme

This document describes the update steps, the notes for each version that needs action, and the roadmap.
"Not specified in context" is written where migration details are unknown.

---

## Updating an existing site

1. Back up the database.
2. Upload the new zip under Appearance > Themes > Add New > Upload Theme and choose "Replace active with uploaded". Do not switch to another theme first: a switch removes the error pages and the two `.htaccess` sections. By FTP instead: turn on hidden files so the `.htaccess` files go up, upload the folder into `wp-content/themes/` as `compelling-evidence-new`, its `.htaccess` files first (the theme folder's, then those in `doc/`, `tools/`, `bin/` and `tests/` when the copy has them; on Nginx, add the server rules in `doc/readme.md` first), then rename `compelling-evidence` to `compelling-evidence-old` and, straight after, `compelling-evidence-new` to `compelling-evidence`, with the admin closed in between. Never upload WordPress itself without keeping `wp-config.php`.
3. Load any admin page. The version change runs the content sync, and the theme writes its error pages, its `.htaccess` sections and its run-once pages.
4. Check `/articles/` and `/journeys/`, then delete `compelling-evidence-old`.
5. Read the note below for the version you are moving to.

---

## Upgrading to v2.18.16 from v2.18.15

Replace the theme files. Nothing else changes. The new links go live with murtadd 1.56.2; a murtadd site that has not yet created its 1.56.0 entries shows them once it does.

## Upgrading to v2.18.15 from v2.18.14

Replace the theme files. The new article `why-people-leave-honour-fate-home` arrives with the content sync on the first admin page load. After changing murtadd.org pages, rebuild `inc/data/quiz-murtadd-links.json` and `inc/data/murtadd-counterparts.json` with `bin/build-quiz-murtadd-links.py` and `bin/build-murtadd-counterparts.py` and murtadd's seed data on disk.

## Upgrading to v2.18.14 from v2.18.13

Replace the theme files. Nothing else changes. On a site in a subfolder, put a copy of `assets/images/favicon.ico` in the domain root (or, with a Site Icon set, that icon as `favicon.ico`), since browsers ask for `/favicon.ico` there.

## Upgrading to v2.18.13 from v2.18.12

Replace the theme files. The new files are in `assets/images/`: `favicon.svg`, `icon-192.png`, `icon-512.png`, `icon-maskable-512.png` and `site.webmanifest`. A Site Icon you set under Appearance > Customize > Site Identity now replaces the theme's icons; before, the theme's won. If a `favicon.ico` file sits in the site root, the web server sends it and the theme never answers; delete it to use the theme's. The first admin page rewrites the error pages with the icon, and runs the content sync as with every version.

## Upgrading to v2.18.12 from v2.18.11

Documentation only, with no change to the articles. On Nginx, replace the two theme lines in the server block with those in `doc/readme.md`, which also cover the `-new` and `-old` folders of an FTP update. As with every version, the first admin page runs the content sync, so make any article edit in the batch file instead (see Content Sync Behaviour on Upgrade).

## Upgrading to v2.18.11 from v2.18.10

Documentation only, with no change to the articles: the FTP steps send the `.htaccess` files first. As with every version, the first admin page runs the content sync: it writes every article from the JSON batches over the database copy and moves to the trash any published `ce_article` whose slug is not in them. An edit made to an article in WordPress is lost, so make it in the batch file instead (see Content Sync Behaviour on Upgrade).

## Upgrading to v2.18.10 from v2.18.9

Replace the theme files. The new files are `LICENSE`, `inc/ce-article-anatomy.php`, `assets/css/ce-article-anatomy.css` and `tests/article-anatomy-test.php`.

Questions saved in an article's FAQ Items box now show at the foot of the article. Before the update, open the articles that have FAQ items and read them as a reader will. Key points go in the new Key points box; until then "In short" shows the excerpt alone.

---

## Upgrading to v2.18.9 from v2.18.8

Documentation only, with no change to the articles. As with every version, the first admin page runs the content sync: it writes every article from the JSON batches over the database copy and moves to the trash any published `ce_article` whose slug is not in them. An edit made to an article in WordPress is lost, so make it in the batch file instead (see Content Sync Behaviour on Upgrade).

---

## Upgrading to v2.18.6, v2.18.7 or v2.18.8

Replace the theme files and load any page. On a live server the theme deletes its development files on activation and once per version: every `.git*` entry, `.claude`, `tests`, `bin`, `tools` and, from 2.18.8, `doc`. To keep them on a server where you work on the theme, set `WP_ENVIRONMENT_TYPE` to `local` or `development`, or add `define( 'CE_KEEP_DEV_FILES', true );` to `wp-config.php`, before you upload.

On Apache the first request writes a "Compelling Evidence: block direct file access" section into `.htaccess` beside "Block xmlrpc.php". If anything that used to load now answers 403, add `define( 'CE_BLOCK_DIRECT_FILES', false );` and report the address. If the Security tab says `wp-config.php` sits in a folder the rules cannot reach, deny it at the server.

---

## Upgrading to v2.18.5 from v2.18.4

Replace the theme files and load any admin page: the home page tells one story in the new order (`doc/ssot.md`, section 29). Nothing to set.

---

## Upgrading to v2.18.4 from v2.18.3

Replace the theme files and open any admin page as an administrator. The theme writes its static error pages into `wp-content/compelling-evidence-errors/`. If `.htaccess` has `ErrorDocument` lines that point at `/wp-content/themes/compelling-evidence/errors/`, replace them with the lines on CE Theme Options > Tools & Sync. The old lines still work for a site at the domain root.

## Upgrading to v2.18.3 from v2.18.2

Replace the theme files and open any admin page as an administrator: the error pages are written then, with no button press. A copy another theme left in `wp-content` is replaced; a file without a theme's mark is left alone. Add the new `ErrorDocument 504` line from CE Theme Options > Tools & Sync to `.htaccess` if you use the static pages.

---

## Upgrading to v2.18.2 from v2.18.1

Replace the theme files and open any admin page: the version change runs the article sync, which carries the new sections of the article on salvation by works.

---

## Upgrading to v2.18.1 from v2.18.0

Replace the theme files and open any admin page: the version change runs the article sync, which carries the new heading of the Islamic dilemma box. Nothing else to run.

---

## Upgrading to v2.18.0 from v2.17.0

Replace the theme files. Nothing is written to `wp-content` until an administrator presses Install error pages on CE Theme Options > Tools & Sync. To use the static pages for server errors, add the `ErrorDocument` lines from that tab to `.htaccess`.

---

## Upgrading to v2.7.1 from v2.7.0

**After deploying:** load any admin page once. On Apache, with "Turn off XML-RPC" on, the theme adds a `# BEGIN Block xmlrpc.php` section to `.htaccess`; if another of the seven themes already wrote it, it is left as it is. If the Security tab says the file could not be written, ask the host to deny xmlrpc.php at the server. Switching "Turn off XML-RPC" off, or switching to a theme outside the seven themes, removes the section. Step 7 of the 2.7.0 checklist is now done by the theme on Apache.

---

## Upgrading to v2.7.0 from v2.6.49

**After deploying:** load any admin page once (the version change runs the content sync). The new Security tab under Appearance > CE Theme Options is on by default: XML-RPC, application passwords and REST writes from outside the browser stop at once.

After the October 2026 attack on another of the sites, the owner checklist:

1. Change the password of every administrator to a long random one used nowhere else, then Log Out Everywhere Else (Users > Profile), or press "Log out all sessions" on the Security tab.
2. Create a new administrator whose name is not the site name, log in as it, and delete the old account, attributing its content to the new one.
3. Press "Revoke all application passwords".
4. Check Plugins for anything not installed by you (auto-posting, AI writers, remote management) and remove it. Check Users for unknown accounts.
5. Run the spam scan, select the results, move them to the trash, then delete the empty spam categories.
6. Purge any page cache, then resubmit the sitemap in Google Search Console.
7. Optional, at the server: deny xmlrpc.php in .htaccess as well, so the requests never reach PHP. Jetpack needs XML-RPC; if Jetpack is used, switch "Turn off XML-RPC" off instead (password logins through XML-RPC stay refused).

WPS Hide Login keeps guarding the login address; nothing here changes it. If a lock breaks a tool you rely on, `define( 'CE_SECURITY_LOCK', false );` in wp-config.php turns the three locks off without changing the options. A tool that must write through REST with an application password can be let through with the `ce_sec_rest_allow` filter (route prefixes), after switching application passwords back on.

---

## Upgrading to v2.6.42 from v2.6.41

**After deploying:** `assets/fonts/kfgqpc-hafs-uthmanic-script.ttf` gives way to `kfgqpc-hafs-uthmanic-script.woff2`. An upload over the old folder leaves the TTF behind; nothing reads it, and it can be deleted. Purge page and CDN caches so the front page preloads the new file.

---

## Upgrading to v2.6.41 from v2.6.40

**After deploying:** nothing to run, and no stored content changes. New verses keep the usual (Tanzil) Uthmani encoding inside `<div class="quran-arabic">`; `inc/ce-quran-text.php` fits them as they render. Verses pasted in the King Fahd Complex's own encoding (sukun U+06E1) render as they are.

---

## Upgrading to v2.6.40 from v2.6.39

**After deploying:** nothing to run. Purge any page or CDN cache that holds `assets/fonts/uthmani-quran.woff2`, which is removed. Child themes or custom CSS that named the `'Uthmani Quran'` family should use `var(--font-quran)` instead.

---

## Upgrading to v2.6.39 from v2.6.38

**After deploying:** nothing to run. Links to the other sites are on by default (three per article); turn them off or change the cap under Theme Options → Links & Tooltips → Links to the Other Sites. When the shared map changes, copy `inc/data/network-links.json` to all seven themes.

---

## Upgrading to v2.6.38 from v2.6.37

**After deploying:** load any admin page; the sync creates five articles and updates six. The 15 new images import on the hourly schedule.

---

## Upgrading to v2.6.37 from v2.6.36

**After deploying:** load any admin page so the sync creates the six new articles and fills their Rank Math fields; their 18 images import on the hourly schedule or through Theme Options → Media.

---

## Upgrading to v2.6.36 from v2.6.35

**Before deploying:** back up the database. This release changes every article's content.

**After deploying:**
1. Load any admin page once: the content sync runs, and Rank Math's empty focus keyword, SEO title and description fields are filled from `seo.json`. Fields you have already set are left alone.
2. The 262 new images import on the hourly schedule (ten registered images per run), or press "Import next 20 images now" in Theme Options → Media about thirteen times. Allow roughly 250 MB more uploads space.
3. To reach 90 or above, switch off Rank Math's Content AI module (Rank Math → Dashboard → Modules) unless you use Content AI.
4. Rank Math stores each article's score when the article is opened or updated in the editor; the score column fills in as articles are opened.

---

## Upgrading to v2.6.35 from v2.6.34

**To switch on automatic images:** Theme Options → Media → paste the Pexels API key, tick "Find and publish lead images for new articles automatically", and save. The first run happens within the hour, or press **Run now**.

**Note:** registered images now import on the hourly schedule as well, so the "Import next 20 images now" presses after 2.6.34 are optional. WP-Cron runs on site visits; on a low-traffic site, a real server cron calling `wp-cron.php` keeps the schedule punctual.

---

## Upgrading to v2.6.34 from v2.6.33

**After deploying:** open Theme Options → Media and press **Import next 20 images now** until the count reads 131 of 131 (about seven presses). Featured images are assigned as each image arrives. Allow roughly 120 MB of uploads space.

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

Follow "Updating an existing site" at the top of this file. The older procedure here, which switched to Twenty Twenty-Five and deleted the folder first, is retired: a theme switch removes the error pages and the `.htaccess` sections, so the site went without them until the next admin page. After an update that changes addresses, save Settings > Permalinks once.

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

## Roadmap

These are plans, not promises. Each item rests on something on record: the code, the server logs of October and November 2026, a documented limit, or a feature another of the owner's themes already has. Items leave this list when they ship and go into `doc/changelog.md`. GitHub had no open issues or pull requests when the list was last checked (2026-11-05), and the code carries no TODO markers.

### Next

- A private login address. The abrahamic and bestofislam themes move the login screen to an address of the owner's choosing; here `inc/ce-login.php` styles the screen only. The October 2026 log shows 327 requests for `/wp-login.php` with an address appended and 1,328 for `/login`.
- Security headers on front-end responses (`X-Content-Type-Options`, `Referrer-Policy`, `X-Frame-Options`, HSTS as an opt-in). The bestofislam theme sends them from `inc/security-headers.php`; this theme sends only cache headers.
- Keep the root `.htaccess` sections on a network when one site leaves the theme. `ce_sec_remove_htaccess()` runs on any site's `switch_theme`, and the shared file then lacks both sections until another site's next sync.

### Later

- Export, import and reset for CE Theme Options. The abrahamic theme has all three on its Tools tab; here a new site means setting more than 60 options by hand. Waits on one settings array, since each setting is its own option today.
- Error pages on a network whose main site runs another theme. Only the main site writes the drop-ins (`ce_error_maybe_install()`), so they stop updating once it leaves the theme. The abrahamic theme hands the job to the subsite with the lowest ID that runs it.
- Malay journeys. `journeys/ms-MY/` holds only a README; `page-journey.php` already falls back to English. Waits on translated text.

### Ideas

- Key points for the 143 seeded articles, carried in the JSON batches. The box is built, but the batches hold no key points yet, so "In short" shows the excerpt alone.
- Ornate ayah brackets (U+FD3E, U+FD3F) in the Quran face. `doc/ssot.md`, section 14, records that KFGQPC lacks them and CE Hadith draws them.
- Answer the sign-in probes earlier. The October 2026 SSL log shows 3,170 requests answering 401 on `/`, and 1,328, 1,142 and 384 on `/login`, `/signin` and `/signup`. They come from the host's layer, so the answer may sit outside the theme.

---

## Rollback Procedure

1. **Back up the database** before any upgrade.
2. To rollback: restore the previous theme zip and re-upload.
3. The content sync will re-fire with the old version key, restoring article content from the older data files.
4. `wp option delete ce_content_sync_{version}` if the sync key needs manual reset.

---

## What is NOT Required on Upgrade

- No direct database schema changes after v1.0.0 (the analytics table is created once on first activation; the `ce_article` CPT and `ce_topic` taxonomy are registered on every page load via `functions.php`).
- No wp-config.php changes, unless you keep the development files on a live server (`CE_KEEP_DEV_FILES`) or need the direct-file section out of `.htaccess` (`CE_BLOCK_DIRECT_FILES`).
- No server configuration changes on Apache: the theme writes its own `.htaccess` sections. On Nginx add the rules in `doc/readme.md`, Security. The `ErrorDocument` lines are optional.
- No plugin installations (the theme is self-contained).

---

## Documentation Location

All theme documentation has been moved to the `/doc/` folder:

- `doc/readme.md`: for developers and hosts
- `doc/changelog.md`: version history
- `doc/upgrading.md`: this file, upgrade notes and roadmap
- `doc/ssot.md`: the fixed facts and rules
- `readme.txt`: for site owners, at the theme root

The live-server clean-up removes `doc/`, so read these in the zip or the repository.

---

*Last updated: November 2026 (v2.18.9)*
