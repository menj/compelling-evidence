/**
 * Compelling Evidence: article table of contents.
 *
 * Builds the sidebar TOC from the article's H2 headings, scrolls to a
 * section on click, and highlights the section in view.
 *
 * Earlier versions (inline in single-ce_article.php and single.php) read
 * --total-offset with getPropertyValue(). Custom properties are returned
 * unresolved ("calc(var(--nav-h) + var(--strip-h))"), so parseInt() gave
 * NaN, window.scrollTo() received NaN and jumped to the top of the page,
 * and the scroll spy never matched. They also used offsetTop, which is
 * relative to the nearest positioned ancestor, not the document.
 *
 * The header clearance now lives in CSS (scroll-margin-top on the
 * headings, main.css) and is read back as a resolved pixel value.
 *
 * @since 2.6.27
 */
(function () {
  'use strict';

  var body = document.getElementById('article-body');
  var toc  = document.getElementById('ce-toc');
  var list = document.getElementById('ce-toc-list');
  if (!body || !toc || !list) return;

  var headings = body.querySelectorAll('h2');
  if (headings.length < 2) return;

  var reduceMotion = window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Resolved clearance under the fixed header, in px (from scroll-margin-top).
  function clearance() {
    var v = parseFloat(getComputedStyle(headings[0]).scrollMarginTop);
    return isNaN(v) ? 136 : v;
  }

  function goTo(heading, updateHash) {
    heading.scrollIntoView({ behavior: reduceMotion ? 'instant' : 'smooth', block: 'start' });
    // Move keyboard and screen-reader focus to the section without a second jump.
    heading.focus({ preventScroll: true });
    if (updateHash && history.pushState) {
      history.pushState(null, '', '#' + heading.id);
    }
  }

  headings.forEach(function (h, i) {
    var id = h.id || 'section-' + (i + 1);
    h.id = id;
    h.setAttribute('tabindex', '-1');

    var li = document.createElement('li');
    var a  = document.createElement('a');
    a.href = '#' + id;
    a.textContent = h.textContent;
    a.addEventListener('click', function (e) {
      e.preventDefault();
      goTo(h, true);
    });
    li.appendChild(a);
    list.appendChild(li);
  });

  toc.style.display = 'block';

  // Deep links (#section-3 in shared URLs and structured data) arrive before
  // the IDs exist, so the browser cannot honour them on its own.
  if (location.hash) {
    var initial = document.getElementById(decodeURIComponent(location.hash.slice(1)));
    if (initial && body.contains(initial)) {
      window.requestAnimationFrame(function () { goTo(initial, false); });
    }
  }

  // Scroll spy.
  var links = list.querySelectorAll('a');
  var ticking = false;
  function updateActive() {
    ticking = false;
    var line = clearance() + 8;
    var current = '';
    headings.forEach(function (h) {
      if (h.getBoundingClientRect().top <= line) current = h.id;
    });
    links.forEach(function (a) {
      var on = a.getAttribute('href') === '#' + current;
      a.classList.toggle('toc-active', on);
      if (on) a.setAttribute('aria-current', 'location');
      else a.removeAttribute('aria-current');
    });
  }
  window.addEventListener('scroll', function () {
    if (!ticking) { ticking = true; window.requestAnimationFrame(updateActive); }
  }, { passive: true });
  window.addEventListener('resize', updateActive, { passive: true });
  updateActive();
})();
