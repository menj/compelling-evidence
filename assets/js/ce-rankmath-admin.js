/**
 * CE Theme: give Rank Math's content analysis the article as published.
 *
 * Figures, the lead image and internal links are added when the article is
 * rendered, so the raw editor text under-reports them. While the editor text
 * is unchanged from the saved article, Rank Math analyses the published
 * article body; once the editor text changes, Rank Math analyses the editor
 * text as usual. This is the same mechanism Rank Math uses for custom fields.
 *
 * @since 2.6.36
 */
(function () {
  'use strict';
  if (!window.wp || !wp.hooks || !window.ceRankMath) return;

  var rendered = null;
  // Compare text only: the block editor re-serialises markup and entities
  // (for example &amp; inside tables), so raw strings can differ even when
  // nothing was edited.
  function norm(s) {
    var d = new DOMParser().parseFromString('<body>' + String(s || '') + '</body>', 'text/html');
    return (d.body.textContent || '').replace(/\s+/g, ' ').trim();
  }
  var raw = norm(ceRankMath.raw);

  var lastIn = null, lastOut = null;
  wp.hooks.addFilter('rank_math_content', 'compelling-evidence/rendered-article', function (content) {
    if (rendered === null) return content;
    if (content === lastIn) return lastOut;
    lastIn = content;
    lastOut = norm(content) === raw ? rendered : content;
    return lastOut;
  }, 11);

  fetch(ceRankMath.url, { credentials: 'same-origin' })
    .then(function (r) { return r.ok ? r.text() : ''; })
    .then(function (html) {
      if (!html) return;
      var doc = new DOMParser().parseFromString(html, 'text/html');
      var body = doc.querySelector('#article-body');
      if (!body) return;
      rendered = body.innerHTML;
      if (window.rankMathEditor && typeof rankMathEditor.refresh === 'function') {
        rankMathEditor.refresh('content');
      }
    })
    .catch(function () {});
})();
