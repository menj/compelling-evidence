# CE Theme (Compelling Evidence)

| | |
|---|---|
| Version | 2.18.16 |
| Licence | GNU GPL v3 or later; the full text is in `LICENSE` |
| Parent | Twenty Twenty-Five, tested with 1.5 (`CE_PARENT_TESTED_VERSION`) |
| Requires | WordPress 6.7 (tested to 7.1), PHP 8.0 (tested to 8.4) |
| Hosting | Apache 2.4 with mod_rewrite for the managed `.htaccess` rules; Nginx works with the rules below added by hand. Multisite works with the limits below |
| Site | https://compelling-evidence.com |
| Repository | https://github.com/menj/compelling-evidence |
| Text domain | `compelling-evidence` |

A child theme for one site: 14 journeys, a weighted intake quiz, 144 articles in 11 topics, reader questions, a glossary, first-party analytics (off by default) and an engagement suite. It is not a general-purpose theme.

`readme.txt` is for site owners. This file is for developers and hosts. `doc/ssot.md` holds the fixed facts and wins on matters of fact; `doc/changelog.md` is the history and `doc/upgrading.md` the upgrade notes and roadmap.

---

## Install, build, test and release

```
# Install
1. Install Twenty Twenty-Five (must be on disk, need not be active)
2. Upload the zip under Appearance > Themes > Add New > Upload Theme, and activate
3. Load any admin page: the topics, articles and secondary pages are created
4. Settings > Permalinks > Save Changes
5. Settings > Reading: pick a static front page if wanted
```

```
set -e                                               # stop at the first failure
for t in tests/*.php; do php "$t" || exit 1; done   # PHP self-tests, WordPress stubbed
node tests/analytics-path-test.js                    # the analytics page type and slug
for f in functions.php inc/*.php; do php -l "$f" > /dev/null || exit 1; done
php bin/build-error-pages.php                        # rebuild errors/*.html from inc/ce-error-page-html.php
node bin/quiz-sim.js                                 # quiz scoring simulation
python3 bin/build-murtadd-counterparts.py            # rebuild inc/data/murtadd-counterparts.json
python3 bin/build-quiz-murtadd-links.py              # rebuild inc/data/quiz-murtadd-links.json
git archive --format=zip --prefix=compelling-evidence/ -o compelling-evidence-2.18.16.zip v2.18.16   # after the release is committed and tagged
```

Every PHP script in `tests/` and `bin/` exits unless it runs from the command line. `.gitattributes` keeps `.gitattributes`, `.gitignore`, `.claude/`, `phpcs.xml.dist`, `bin/` and `tests/` out of the zip; `doc/` and `tools/` ship, and the live-server clean-up removes them.

Deploy by the dashboard ("Replace active with uploaded", never a switch to another theme first, which takes the error pages and the `.htaccess` sections away) or by FTP: show hidden files, upload to a temporary folder inside `wp-content/themes/` with the `.htaccess` files first, then rename the old folder away and the new one into place, one straight after the other. Load one admin page afterwards: the version change runs the content sync.

A release bumps `Version` in `style.css` (the only version source; PHP reads it with `wp_get_theme()`), `Version` and `Stable tag` in `readme.txt`, this file and `doc/ssot.md`, adds an entry to `doc/changelog.md` and the short form to `readme.txt`, and updates the batch checksums in `inc/articles/manifest.json` when a batch changed.

---

## Architecture

### Boot sequence

1. `functions.php` reads its own `require_once` list and checks that every `inc/` file is readable before it loads any (`ce_incomplete_theme()`). A missing file stops the load.
2. The files load inside `try`, so a half-written file (a `ParseError`) stops it too. Either way visitors get a 503 "Back shortly" page with `Retry-After: 300`, `no-store` and noindex; the log gets one line per problem every ten minutes. The login screen, AJAX, cron and the command line go on without it. `functions.php` cannot guard its own parse.
3. The modules load in this order: ce-db, ce-gone, ce-security-detector, ce-lock, ce-security, ce-housekeeping, ce-engagement, ce-seo, ce-schema, ce-journey-assets, ce-journey-template, ce-standalone-faq, ce-aeo, ce-article-anatomy, ce-secondary-pages, ce-migration-2-2-0, ce-migration-2-2-74, ce-analytics, ce-parent-compat, ce-fonts, ce-icons, ce-404, ce-media, ce-image-sitemap, ce-media-auto, ce-search-permalinks, ce-social-icons, ce-reading-progress, ce-unlist, ce-plugin-compat, ce-login, ce-rankmath, ce-theme-options, ce-error-pages, class-ce-article-loader, class-ce-feed-redirector, ce-content-sync, ce-quran-text, ce-contextual-links, ce-crosslinks, ce-murtadd-counterparts, ce-glossary, ce-question-cpt.
4. On `init` (priority 99) the first request of a new version runs the dev-file clean-up and the `.htaccess` sync, each under the lock in `inc/ce-lock.php`. On `admin_init` the content sync and the one-time page and migration modules run.

### File map

```
compelling-evidence/
├── style.css, functions.php, theme.json, screenshot.png, readme.txt
├── .htaccess                  Refuses dot entries, theme PHP, tests/bin/tools/doc and Markdown
├── header.php, footer.php, index.php, page.php, single.php, search.php, 404.php
├── front-page.php             Home page, one story (doc/ssot.md, section 29)
├── single-ce_article.php, archive-ce_article.php, archive-ce_topic.php, taxonomy-ce_topic.php
├── single-ce_question.php, archive-ce_question.php
├── page-quiz.php, page-journey.php, page-journeys.php, page-progress.php
├── page-faq.php, page-glossary.php, page-ask-a-question.php
├── sitemap.xml                Pointer to /wp-sitemap.xml
├── inc/                       43 modules (load order above)
├── inc/articles/              batch-001.json to batch-010.json (144 articles), manifest.json
│                              (SHA-256 per batch), media.json (figure credits), seo.json
├── inc/data/                  seed-pages.json, network-links.json, murtadd-counterparts.json,
│                              quiz-murtadd-links.json, standalone-faq.json, seo-titles.json,
│                              rankmath-legacy-titles.json
├── template-parts/journey/    document, nav, screen, scripts, v2-preview
├── journeys/                  quiz.html, data/<14 personas>/ (journey.json, style.css, screens/),
│                              i18n/en.php, ms-MY/
├── assets/css, assets/js      One file per concern; journey and quiz files printed by inc/ce-journey-assets.php
├── assets/fonts/              Self-hosted WOFF2 files and licenses/
├── assets/images/             Icons, favicons, social marks, calligraphy, diagrams
├── errors/                    Static 403, 500, 502, 503, 504 pages for a site at the domain root
├── languages/                 compelling-evidence.pot, ms_MY.po, ms_MY.mo
├── bin/                       Build scripts (own .htaccess; export-ignored)
├── tests/                     Self-tests (own .htaccess; export-ignored)
├── tools/                     Own .htaccess; removed on a live site
├── doc/                       readme.md, changelog.md, upgrading.md, ssot.md (own .htaccess)
└── .claude/                   Notes for contributors (export-ignored, refused, removed on a live site)
```

### Content model

- `ce_article`: public, archive at `/articles/`, supports title, editor, excerpt, thumbnail and page attributes; `menu_order` orders articles within a topic.
- `ce_topic`: flat taxonomy on `ce_article` and `post`, 11 terms in canonical order (`doc/ssot.md`, section 5). Never rename a slug.
- `ce_question`: reader questions from Ask a Question, held as pending posts; an answered one gets a QAPage.
- Articles live in the JSON batches. `inc/ce-content-sync.php` writes them to the database once per version (`ce_content_sync_{version}` option, `ce_content_sync_lock`), so the database copy is a cache and an edit made in WordPress is overwritten by the next sync.

| # | Topic | Slug | Articles |
|---|---|---|---|
| 1 | Does God Exist? | `does-god-exist` | 12 |
| 2 | The Problem of Evil | `the-problem-of-evil` | 9 |
| 3 | Ethics Without God? | `ethics-without-god` | 5 |
| 4 | Science & Evidence | `science-evidence` | 11 |
| 5 | The Quran & Its Sources | `quran-and-sources` | 30 |
| 6 | History, Context & Comparison | `history-context-comparison` | 15 |
| 7 | Divine Justice & Fairness | `divine-justice-fairness` | 7 |
| 8 | Islamic Practice & Ritual | `islamic-practice-ritual` | 8 |
| 9 | Rights & Freedom | `rights-freedom` | 17 |
| 10 | The Inner Journey | `the-inner-journey` | 18 |
| 11 | Revelation & Meaning | `revelation-meaning` | 12 |

Total: 144 articles (manifest 2.8.4).

### Run-once modules on `admin_init`

| File | Option flag | Runs when |
|---|---|---|
| `ce-content-sync.php` | `ce_content_sync_{version}` | The version changes |
| `ce-migration-2-2-0.php` | `ce_category_migration_2_2_0` | The flag is absent |
| `ce-migration-2-2-74.php` | `ce_migration_2_2_74` | The flag is absent |
| `ce-secondary-pages.php` | `ce_secondary_pages_created`, `ce_v2_pages_created`, `ce_about_v2_updated` | Each flag is absent |

To force a sync without a version bump: Tools & Sync, Run Content Sync Now, or `wp eval 'ce_sync_article_content( true );'`.

---

## Settings

Appearance > CE Theme Options (`themes.php?page=ce-theme-options`), built in `inc/ce-theme-options.php`. Each setting is its own `ce_*` option. Twelve tabs:

| Tab | Holds | Code |
|---|---|---|
| General | Hero title and subtitle, the Arabic verse, its number, translation and reference, footer text | `inc/ce-theme-options.php` |
| Author & Identity | Byline, Person and Organization schema, profiles, footer social icons | `inc/ce-seo.php`, `inc/ce-social-icons.php` |
| Colors | 7 brand colours as CSS custom properties | `inc/ce-theme-options.php` (front end), `inc/ce-login.php` (login screen) |
| Typography | Heading, reading and label faces, size scale | `inc/ce-fonts.php` |
| Links & Tooltips | Crosslinks, links to other sites, murtadd.org counterparts, glossary tooltips, search addresses | `inc/ce-crosslinks.php`, `inc/ce-contextual-links.php`, `inc/ce-murtadd-counterparts.php`, `inc/ce-search-permalinks.php` |
| Media | Lightbox, automatic Pexels lead images (off, needs a key), image import | `inc/ce-media.php`, `inc/ce-media-auto.php` |
| Quiz & Journeys | Quiz switch, answers per question, cards per row | `page-quiz.php`, `inc/ce-journey-template.php` |
| Engagement | Votes, resonance, home search, archive page size, reading progress bar | `inc/ce-engagement.php`, `inc/ce-reading-progress.php` |
| Analytics | Opt-in tracking, scroll and search events, retention | `inc/ce-analytics.php` |
| Performance | Parallax, font preloading, the 410 Gone path list | `inc/ce-gone.php` |
| Security | `ce_sec_xmlrpc_off`, `ce_sec_app_passwords_off`, `ce_sec_rest_lock`, `ce_sec_spam_gate`, `ce_sec_gone`, login screen design, scan and clean-up tools | `inc/ce-security.php`, `inc/ce-login.php` |
| Tools & Sync | Content sync, batch diagnostics, error pages | `inc/ce-content-sync.php`, `inc/ce-error-pages.php` |

The analytics report sits under CE Analytics in the admin menu.

---

## Hooks, filters and constants

Filters the theme provides:

| Filter | Effect |
|---|---|
| `ce_contextual_links_enabled`, `ce_contextual_links_map`, `ce_contextual_links_post_types` | Switch links to other sites off for one post, change the shared phrase map, choose the post types |
| `ce_dev_paths` | The paths the live-server clean-up removes |
| `ce_image_sitemap_enabled` | Whether `/image-sitemap.xml` is served (off while an SEO plugin is active) |
| `ce_login_plugin_active` | Whether a login plugin owns the login screen |
| `ce_murtadd_base_url` | The murtadd.org base address of the quiz's "You are not alone" links (`page-quiz.php`) |
| `ce_media_auto_banned_words`, `ce_media_auto_sensitive_words` | Words that keep an automatic image off an article |
| `ce_rpb_applies` | Whether the reading progress bar shows on a view |
| `ce_search_reserved_bases` | Words the search base may not take |
| `ce_sec_rest_allow` | REST route prefixes that may write with non-cookie authentication, for example `/jetpack/` |
| `ce_sec_sister_themes` | Theme folders that keep the shared xmlrpc.php section, so a switch between them leaves it alone |
| `ce_unlist_enabled` | False turns the unlist feature off |

`grep -n "add_action\|add_filter" inc/*.php functions.php` lists every hook used.

Constants for `wp-config.php`, each optional:

| Constant | Effect |
|---|---|
| `CE_SECURITY_LOCK` | `false` lifts the three Security locks (XML-RPC, application passwords, REST writes) without changing the options |
| `CE_KEEP_DEV_FILES` | `true` keeps the development files on a live server. A `local` or `development` `WP_ENVIRONMENT_TYPE` keeps them too |
| `CE_BLOCK_DIRECT_FILES` | `false` leaves the "Compelling Evidence: block direct file access" section out of `.htaccess` |
| `CE_SPAM_THRESHOLD` | Spam score at which a post is held (default 3) |
| `CE_TITLE_SITE`, `CE_TITLE_SEP`, `CE_TITLE_MAX` | The title builder's site name, separator and limit (Compelling Evidence, `|`, 59) |

Fixed in code: `CE_SITE_KEY` (`ce`), `CE_ERR_MARKER`, `CE_STATIC_ERRORS` (`compelling-evidence-errors`), `CE_SEC_FILES_MARKER`, `CE_PARENT_TESTED_VERSION` (1.5), `CE_MEDIA_AUTO_PER_RUN` (5) and the `CE_SEC_*` option names.

---

## Security

- Upload guard: see the boot sequence.
- REST users: visitors who are not logged in get no `/wp/v2/users` routes and no `?author=N` lookup (`ce_sec_hide_user_routes()`, `ce_sec_block_author_query()`).
- XML-RPC is off while `ce_sec_xmlrpc_off` is on (the default); a correct password sent through it is refused even when it is back on. Application passwords are off, and an authenticated REST write without a valid browser session and nonce is refused (`ce_sec_rest_guard()`). Anonymous requests are left to each route's own permission check.
- The spam gate holds a post that reads as gambling spam as Pending; seeded content is marked and exempt. Removed spam answers 410 Gone, and `inc/ce-gone.php` answers 410 for the path prefixes listed on the Performance tab.
- The theme folder's `.htaccess` refuses every dot entry but `.well-known`, every theme PHP file, the `tests`, `bin`, `tools` and `doc` folders, and every Markdown file. It works with mod_rewrite (`RewriteOptions Inherit`), mod_alias, neither (`FilesMatch`), and on Apache 2.4 with `<If>`. `tests/`, `bin/`, `tools/` and `doc/` carry their own deny file.
- The live-server clean-up (`inc/ce-housekeeping.php`) removes `.claude`, `tests`, `bin`, `tools`, `doc` and every entry that starts with `.git` on activation, on the first admin page and on the first front-end request of a version, under a lock it renews while it works.

### Root .htaccess sections

On Apache the theme keeps two sections in the site's `.htaccess` through `insert_with_markers()`, synced on the first request of a version and from the admin. A theme switch removes both. When the switch finds another request holding the lock, the removal waits for that request, which takes out the direct-file section and keeps the shared xmlrpc.php section if the new theme is one that writes the same block (`ce_sec_sister_themes`).

```
# BEGIN Block xmlrpc.php
<Files xmlrpc.php>
<IfModule mod_authz_core.c>
Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
Order Deny,Allow
Deny from all
</IfModule>
</Files>
# END Block xmlrpc.php
```

It follows the XML-RPC switch. The marker is shared, so a switch to another theme that writes the same block (`ce_sec_sister_themes`) never writes it twice.

The direct-file section, for a site at the domain root with WordPress in the same folder:

```
# BEGIN Compelling Evidence: block direct file access
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule (^|/)\.(git|claude) - [F]
RewriteRule ^wp-content/themes/compelling-evidence/(tests|bin|tools|doc)(/|$) - [F]
RewriteRule ^wp-includes/(?!(js/tinymce/wp-tinymce|ms-files)\.php$).+\.php(/|$) - [F]
RewriteRule ^wp-admin/includes/.+\.php(/|$) - [F]
RewriteRule ^(wp-config\.php|wp-settings\.php|wp-config-sample\.php|wp-admin/install\.php|readme\.html)(/|$) - [F]
RewriteRule ^wp-content/themes/.+\.php(/|$) - [F]
</IfModule>
# END Compelling Evidence: block direct file access
```

The rules carry the site's own folder prefixes, and add a `wp-config.php` rule for the folder above WordPress when WordPress sits in a subfolder. When `wp-config.php` sits above a folder the rules cannot reach, the Security tab says so.

### Nginx

Nginx ignores `.htaccess`, so neither section is written and the theme folder's file does nothing. The locks, the upload guard and the clean-up still work in PHP. Add the equivalents to the server block, ahead of the `location ~ \.php$` block:

```
location ~ /\.(?!well-known/) { deny all; }
location = /xmlrpc.php { deny all; }
location ~ ^/wp-content/themes/compelling-evidence[^/]*/(tests|bin|tools|doc)/ { deny all; }
location ~* ^/wp-content/themes/compelling-evidence[^/]*/.+\.md$ { deny all; }
location ~ ^/wp-includes/(?!(js/tinymce/wp-tinymce|ms-files)\.php$).+\.php { deny all; }
location ~ ^/wp-admin/includes/.+\.php { deny all; }
location ~ (^|/)wp-config\.php$ { deny all; }
location ~ ^/(wp-settings|wp-config-sample)\.php { deny all; }
location ~ ^/(wp-admin/install\.php|readme\.html) { deny all; }
location ~ ^/wp-content/themes/.+\.php { deny all; }
error_page 403 /wp-content/compelling-evidence-errors/403.html;
error_page 500 /wp-content/compelling-evidence-errors/500.html;
error_page 502 /wp-content/compelling-evidence-errors/502.html;
error_page 503 /wp-content/compelling-evidence-errors/503.html;
error_page 504 /wp-content/compelling-evidence-errors/504.html;
```

The `compelling-evidence[^/]*` in the theme lines also covers the `compelling-evidence-new` and `compelling-evidence-old` folders of an FTP update, so add these rules before the first one.

Nginx serves `error_page` for PHP's own 500 and 503 answers only with `fastcgi_intercept_errors on;` in the FastCGI location; without it those answers reach visitors as PHP sent them. Jetpack needs XML-RPC: leave out the `xmlrpc.php` line and also switch "Turn off XML-RPC" off on the Security tab, since the theme refuses XML-RPC in PHP too. Where the client logs in to XML-RPC, with a password or any other credential, switch the REST lock off as well, since `ce_sec_refuse_remote_password()` refuses every XML-RPC login while either switch is on. These rules do not follow the Security switches or `CE_SECURITY_LOCK`: to let XML-RPC through, take the `xmlrpc.php` line out by hand as well. The `wp-config.php` rule matches at any depth, so it also covers a `wp-config.php` in the folder above a WordPress that lives in a subfolder. Prefix the other paths when WordPress lives in a subfolder.

---

## Error pages

WordPress reads three files from `wp-content` when it cannot build a page. The theme writes them on activation, again on an administrator's next admin page when one is missing or outdated, and at once with Install error pages on Tools & Sync, which shows each as not installed, installed, outdated, left behind by another theme or another file left alone. A copy another theme left behind (marked "Written by the ... theme; safe to delete") is replaced; a file without such a mark is never overwritten. On a single site, a theme switch deletes the files that carry the theme's marker, `Compelling Evidence error page drop-in`; on a network they stay (see Multisite).

| File | Shown when | Status |
|---|---|---|
| `maintenance.php` | WordPress is updating | 503, `Retry-After: 600` |
| `php-error.php` | a fatal PHP error | 500 |
| `db-error.php` | the database cannot be reached | 503, `Retry-After: 600` |

Static 403, 500, 502, 503 and 504 pages cover the errors the web server answers before WordPress starts. The theme writes a copy of each into `wp-content/compelling-evidence-errors/` with the site's name and home address, at the same times as the drop-ins, so the home link works when WordPress sits in a subfolder. `errors/` holds the copies for a site at the domain root; rebuild them with `php bin/build-error-pages.php`. Tools & Sync gives the lines for the site; at the domain root:

```
ErrorDocument 403 /wp-content/compelling-evidence-errors/403.html
ErrorDocument 500 /wp-content/compelling-evidence-errors/500.html
ErrorDocument 502 /wp-content/compelling-evidence-errors/502.html
ErrorDocument 503 /wp-content/compelling-evidence-errors/503.html
ErrorDocument 504 /wp-content/compelling-evidence-errors/504.html
```

Remove the `ErrorDocument` lines when you move to another theme.

Every page carries the CE mark as its tab icon, as a `data:` address built from the page's own inline mark, so it needs no path and shows in a subfolder install and while WordPress is down.

## Browser icons

`ce_site_icon_url()` (`functions.php`) fills `get_site_icon_url()` with the theme's files while the owner has set no Site Icon, so WordPress prints the 32px, 192px, touch and tile tags on the site, the login screen and the admin. `ce_favicon_tags()` adds what WordPress does not print: `favicon.svg`, `favicon-16x16.png`, `favicon.ico`, `site.webmanifest` and `theme-color` #180d2e, on `header.php`, `login_head` and `admin_head`. WordPress prints its own tags in all three places. With a Site Icon set, the filter passes the owner's address through and `ce_favicon_tags()` prints nothing. The quiz and journey pages never call `wp_head()`, so `ce_standalone_assets()` prints `wp_site_icon()` and `ce_favicon_tags()` before their `</head>`.

`/favicon.ico` reaches WordPress when no such file sits in the site root. `ce_serve_favicon()` answers it with `assets/images/favicon.ico` (16, 32, 48), `Cache-Control: public, max-age=86400` and an ETag, with a 304 on a match; `template_redirect` at priority 0 covers HEAD. With a Site Icon, WordPress redirects to it. The manifest is a static file with relative icon paths. The theme `.htaccess` refuses dot files, PHP, Markdown and the development folders only, and `tests/htaccess-files-test.php` checks the icon files stay open. On a site in a subfolder, such as `example.com/blog/`, browsers still ask for `example.com/favicon.ico`, which WordPress never sees: put a copy of `assets/images/favicon.ico` at the domain root there. The head tags cover the pages either way. If a Site Icon is set, put that icon there as favicon.ico instead.

## Multisite

Settings, articles, analytics and the Security records are per site. On a network:

- Only the main site writes the drop-ins and the static copies, which every site shares, so they carry the main site's name and address. A subsite's Tools & Sync says so. A theme switch never removes them, since another site may still use the theme.
- The lock in `inc/ce-lock.php` lives in the main site's options table, so every site that runs the theme shares one lock.
- The root `.htaccess` is one file. A theme switch on any site removes the sections (the xmlrpc.php one can stay, as described under Root .htaccess sections) until another site's next sync writes them back.

---

## SEO and structured data

- `inc/ce-seo.php` writes the meta description, canonical, Open Graph and Twitter tags and the title (`<page> | Compelling Evidence`, under 60 characters, checked by `tests/title-builder-test.php`). `inc/data/seo-titles.json` holds the short titles.
- `inc/ce-schema.php` builds one JSON-LD graph per page: the page frame, Article, FAQPage (the site FAQ, each journey and the quiz, each with a visible FAQ from `inc/data/standalone-faq.json`), Quiz, LearningResource, QAPage, ItemList, ImageObject and Quotation nodes. `tests/schema-graph-test.php` checks 189 graphs. With Rank Math active the nodes join Rank Math's graph (`inc/ce-rankmath.php`).
- An article's own FAQPage comes from its FAQ Items (`_ce_faq_items`) and is emitted only while `ce_anatomy_faq_shows()` says the FAQ shows on the page (`inc/ce-article-anatomy.php`). Its own Sources list joins the Article `citation`.
- The author in the structured data is fixed in `inc/ce-seo.php`, whatever the byline field holds.
- `class-ce-feed-redirector.php` removes feed links and sends feed addresses to the matching page. `/image-sitemap.xml` lists article images while no SEO plugin is active.

## Performance

### Asset Loading Strategy

| Asset | Condition | Size |
|---|---|---|
| `main.css` | All pages | 60.5 KiB |
| `templates.css` | Non-front-page only | 42.4 KiB |
| `ce-engagement.css` | `is_singular('ce_article')` only | |
| `glossary-tooltip.css` + `.js` | Singular article, post or answered question (`ce_ctx_post_types()`) | |
| `print.css` | All pages, media="print" | 5 KiB |
| `journey.css` + `journey.js` | Journey pages, printed by `page-journey.php` | 12.8 + 19.1 KiB |
| `journey-base.css` | Journey pages, last in the head | 2.1 KiB |
| `quiz.css` + `quiz.js` | Quiz page, printed by `page-quiz.php` | 15.0 + 12.4 KiB |

A journey page is built by `inc/ce-journey-template.php` from `journeys/data/<key>/` and the parts in `template-parts/journey/`; the template writes the same markers. The journey and quiz pages never call `wp_head()`, so `inc/ce-journey-assets.php` registers these files and prints them with `wp_print_styles()` and `wp_print_scripts()` where the HTML has a `<!-- ce:journey-css -->`, `<!-- ce:journey-js -->`, `<!-- ce:quiz-css -->` or `<!-- ce:quiz-js -->` marker. Each file's version is its modification time.

### Font Preloading

On front page only, two Arabic fonts are preloaded in `header.php`:
- `kfgqpc-hafs-uthmanic-script.woff2` (105 KiB, a woff2 conversion of the original TTF, served by the owner's decision although its licence forbids modification; see `doc/ssot.md`), the LCP element font
- `ce-hadith-400.woff2` (69 KiB)

### Cache Strategy

`Cache-Control: public, max-age=31536000, immutable` set via `send_headers` and `wp_headers` hooks for fonts and versioned CSS/JS. 30-day cache for images.

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

## Design System

### Colour Palette (CSS custom properties in `main.css :root`)

```css
--dark:    #0d0820   /* Deepest background */
--dark-2:  #1a0f38   /* Mid background */
--dark-3:  #120b2e   /* Card/surface background */
--light:   #f2eeff   /* Primary text */
--teal:    #0cd4e0   /* Accent: links, highlights */
--gold:    #f5c518   /* Conclusion screens only */
--purple:  #6b2fa0   /* Accent: secondary */
--muted:   rgba(245,240,255,0.68)  /* Secondary text */
```

All 7 are overridable via Theme Options > Colors (injected as inline CSS at `wp_head`).

---

## Localisation

**Text domain:** `compelling-evidence`

Shipped translations: `ms_MY` (Malay). Compile with:
```
msgfmt languages/ms_MY.po -o languages/ms_MY.mo
```

Journey translations: copy `journeys/data/[slug]/` to `journeys/ms-MY/data/[slug]/` and translate the text in `journey.json` and `screens/`, then copy `journeys/i18n/en.php` to `journeys/i18n/ms-MY.php` and translate the values. A hand-made `journeys/ms-MY/[slug]-journey.html` still works where no data folder exists. `page-journey.php` and `page-quiz.php` handle locale detection and fallback to English.
