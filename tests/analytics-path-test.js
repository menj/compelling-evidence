#!/usr/bin/env node
/**
 * Self-test for the page type and article slug that assets/js/ce-analytics.js
 * sends. Run: node tests/analytics-path-test.js
 *
 * Runs the real script in a sandbox with a stub window, document and fetch,
 * once per address, for a site at the domain root and for one in a /blog/
 * subfolder, and checks the pageview and article_view events it sends.
 */
'use strict';

const fs = require('fs');
const path = require('path');
const vm = require('vm');

const src = fs.readFileSync(path.join(__dirname, '..', 'assets', 'js', 'ce-analytics.js'), 'utf8');

/**
 * Run the tracker for one address and return the events it sent.
 *
 * @param {string}      href    Page address.
 * @param {string|null} homeUrl CE.homeUrl, or null to leave it out.
 * @return {Object[]} Events as { type, data }.
 */
function run(href, homeUrl) {
  const sent = [];
  const url = new URL(href);
  const CE = { ajaxUrl: url.origin + '/wp-admin/admin-ajax.php', nonce: 'n' };
  if (homeUrl !== null) {
    CE.homeUrl = homeUrl;
  }
  const window = {
    CE: CE,
    CE_ANALYTICS: { scroll: false, search: false },
    location: { pathname: url.pathname, href: url.href },
    sessionStorage: { getItem: () => null, setItem: () => {} },
    addEventListener: () => {},
    pageYOffset: 0,
    innerHeight: 800,
  };
  const sandbox = {
    window: window,
    CE: CE,
    URL: URL,
    URLSearchParams: URLSearchParams,
    FormData: FormData,
    document: { getElementById: () => null },
    navigator: {},
    fetch: (to, opts) => {
      sent.push({ type: opts.body.get('event_type'), data: JSON.parse(opts.body.get('event_data')) });
      return Promise.resolve();
    },
  };
  vm.runInNewContext(src, sandbox);
  return sent;
}

let fail = 0;
function check(ok, label) {
  console.log((ok ? 'ok   ' : 'FAIL ') + label);
  fail += ok ? 0 : 1;
}

function event(events, type) {
  const e = events.find((x) => x.type === type);
  return e ? e.data : null;
}

const sites = {
  'at the domain root': { origin: 'https://example.test', home: 'https://example.test/' },
  'in a /blog/ subfolder': { origin: 'https://example.test/blog', home: 'https://example.test/blog/' },
  'in a /blog subfolder, home address without the slash': { origin: 'https://example.test/blog', home: 'https://example.test/blog' },
  'in a nested /sites/ce/ subfolder': { origin: 'https://example.test/sites/ce', home: 'https://example.test/sites/ce/' },
};

for (const [label, site] of Object.entries(sites)) {
  let ev = run(site.origin + '/articles/does-god-exist/', site.home);
  check(event(ev, 'pageview') && event(ev, 'pageview').page === 'article', `${label}: an article page is typed "article"`);
  check(event(ev, 'article_view') && event(ev, 'article_view').slug === 'does-god-exist', `${label}: the article slug is "does-god-exist"`);

  ev = run(site.origin + '/articles/does-god-exist', site.home);
  check(event(ev, 'article_view') && event(ev, 'article_view').slug === 'does-god-exist', `${label}: the slug without a trailing slash`);

  ev = run(site.origin + '/articles/a/articles/b/', site.home);
  check(event(ev, 'article_view') && event(ev, 'article_view').slug === 'a/articles/b', `${label}: only the leading /articles/ is cut from the slug`);

  ev = run(site.origin + '/', site.home);
  check(event(ev, 'pageview').page === 'home', `${label}: the home page is typed "home"`);
  check(null === event(ev, 'article_view'), `${label}: the home page sends no article_view`);

  ev = run(site.origin + '/journey/agnostic/', site.home);
  check(event(ev, 'pageview').page === 'journey', `${label}: a journey is typed "journey"`);

  ev = run(site.origin + '/quiz/', site.home);
  check(event(ev, 'pageview').page === 'quiz', `${label}: the quiz is typed "quiz"`);

  ev = run(site.origin + '/topic/the-problem-of-evil/', site.home);
  check(event(ev, 'pageview').page === 'topic', `${label}: a topic is typed "topic"`);

  ev = run(site.origin + '/glossary/', site.home);
  check(event(ev, 'pageview').page === 'glossary', `${label}: the glossary is typed "glossary"`);
  check(event(ev, 'pageview').path === '/glossary/', `${label}: the pageview path is the path below the home address`);
}

// The home address without its trailing slash still counts as home.
let ev = run('https://example.test/blog', 'https://example.test/blog/');
check(event(ev, 'pageview').page === 'home', 'in a /blog/ subfolder: /blog without the slash is typed "home"');

// A path outside the home folder is not cut.
ev = run('https://example.test/blogroll/articles/x/', 'https://example.test/blog/');
check(event(ev, 'pageview').page === 'other' && null === event(ev, 'article_view'), 'in a /blog/ subfolder: /blogroll/articles/x/ is not taken for an article');

// Without CE.homeUrl the full path is used, as before.
ev = run('https://example.test/articles/does-god-exist/', null);
check(event(ev, 'article_view') && event(ev, 'article_view').slug === 'does-god-exist', 'without CE.homeUrl: a root article still gives its slug');

// The old code took the subfolder for part of the address.
ev = run('https://example.test/blog/articles/does-god-exist/', 'https://example.test/blog/');
check(!ev.some((x) => x.type === 'article_view' && x.data.slug !== 'does-god-exist'), 'in a /blog/ subfolder: no article_view carries the folder in its slug');

console.log(fail ? `\n${fail} check(s) failed` : '\nAll checks passed');
process.exit(fail ? 1 : 0);
