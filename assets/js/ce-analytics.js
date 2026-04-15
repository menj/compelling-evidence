/**
 * Compelling Evidence — Analytics Tracker
 * Privacy-first: no cookies, no PII, no fingerprinting.
 * Session ID stored in sessionStorage (clears on tab close).
 */
(function() {
  'use strict';

  // Session ID — unique per browser tab session, no persistence
  var SESSION_KEY = 'ce_analytics_sid';
  var session = sessionStorage.getItem(SESSION_KEY);
  if (!session) {
    session = Math.random().toString(36).substr(2, 12) + Date.now().toString(36);
    sessionStorage.setItem(SESSION_KEY, session);
  }

  // Throttle: don't fire same event type more than once per N seconds
  var lastFired = {};
  function throttled(type, seconds) {
    var now = Date.now();
    if (lastFired[type] && now - lastFired[type] < seconds * 1000) return true;
    lastFired[type] = now;
    return false;
  }

  // Send event to server
  function track(type, data) {
    if (!window.CE || !CE.ajaxUrl || !CE.nonce) return;
    var body = new FormData();
    body.append('action', 'ce_analytics');
    body.append('nonce', CE.nonce);
    body.append('event_type', type);
    body.append('event_data', JSON.stringify(data || {}));
    body.append('session_id', session);

    // Use sendBeacon for unload events, fetch otherwise
    if (navigator.sendBeacon && (type === 'article_scroll' || type === 'quiz_abandon')) {
      var params = new URLSearchParams();
      params.append('action', 'ce_analytics');
      params.append('nonce', CE.nonce);
      params.append('event_type', type);
      params.append('event_data', JSON.stringify(data || {}));
      params.append('session_id', session);
      navigator.sendBeacon(CE.ajaxUrl, params);
    } else {
      fetch(CE.ajaxUrl, { method: 'POST', body: body, credentials: 'same-origin' }).catch(function() {});
    }
  }

  // Expose for use by quiz/journey inline scripts
  window.ceTrack = track;

  // ── PAGEVIEW ──
  var path = window.location.pathname;
  var pageType = 'other';
  if (path === '/' || path === '') pageType = 'home';
  else if (path.indexOf('/articles/') === 0) pageType = 'article';
  else if (path.indexOf('/journey/') === 0) pageType = 'journey';
  else if (path.indexOf('/quiz') === 0) pageType = 'quiz';
  else if (path.indexOf('/journeys') === 0) pageType = 'journeys_overview';
  else if (path.indexOf('/topic/') === 0) pageType = 'topic';
  else if (path.indexOf('/faq') === 0) pageType = 'faq';
  else if (path.indexOf('/glossary') === 0) pageType = 'glossary';

  track('pageview', { page: pageType, path: path });

  // ── ARTICLE TRACKING ──
  if (pageType === 'article') {
    var articleBody = document.getElementById('article-body');
    var articleSlug = path.replace('/articles/', '').replace(/\/$/, '');

    track('article_view', { slug: articleSlug });

    // Scroll depth tracking (25%, 50%, 75%, 100%)
    if (articleBody) {
      var depthsFired = {};
      var depths = [25, 50, 75, 100];

      function checkScrollDepth() {
        var rect = articleBody.getBoundingClientRect();
        var articleTop = rect.top + window.pageYOffset;
        var articleHeight = articleBody.offsetHeight;
        var scrolled = window.pageYOffset + window.innerHeight - articleTop;
        var pct = Math.min(100, Math.max(0, Math.round(scrolled / articleHeight * 100)));

        depths.forEach(function(d) {
          if (pct >= d && !depthsFired[d]) {
            depthsFired[d] = true;
            track('article_scroll', { slug: articleSlug, depth: d });
          }
        });

        if (pct >= 95 && !depthsFired['complete']) {
          depthsFired['complete'] = true;
          track('article_complete', { slug: articleSlug });
        }
      }

      window.addEventListener('scroll', function() {
        if (!throttled('scroll_check', 2)) checkScrollDepth();
      }, { passive: true });
    }
  }

  // ── SEARCH TRACKING ──
  var searchInput = document.getElementById('ce-search-input');
  if (searchInput) {
    var searchTimer;
    searchInput.addEventListener('input', function() {
      clearTimeout(searchTimer);
      var query = searchInput.value.trim();
      if (query.length >= 3) {
        searchTimer = setTimeout(function() {
          track('search_query', { query: query });
        }, 1500); // Only log after 1.5s pause (not every keystroke)
      }
    });
  }
})();
