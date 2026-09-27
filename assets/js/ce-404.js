/**
 * CE Theme: 404 excuse rotator.
 *
 * "Hear another excuse" swaps in the next excuse without a reload. The
 * link keeps its ?excuse=N href as the no-JavaScript fallback.
 *
 * @since 2.6.29
 */
(function () {
  'use strict';

  var box  = document.getElementById('nf-excuse');
  var btn  = document.getElementById('nf-another');
  var data = document.getElementById('nf-excuses');
  if (!box || !btn || !data) return;

  var excuses;
  try { excuses = JSON.parse(data.textContent); } catch (e) { return; }
  if (!excuses || excuses.length < 2) return;

  var idx = parseInt(box.getAttribute('data-index'), 10) || 0;
  var reduceMotion = window.matchMedia &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var el = {
    title: document.getElementById('nf-title'),
    body:  document.getElementById('nf-body'),
    label: document.getElementById('nf-label'),
    link:  document.getElementById('nf-link'),
    cta:   document.getElementById('nf-cta')
  };

  function render(e) {
    el.title.textContent = e.title;
    el.body.textContent  = e.body;
    el.label.textContent = e.label;
    el.cta.textContent   = e.cta;
    el.link.href         = e.url;
  }

  btn.setAttribute('role', 'button');
  btn.addEventListener('click', function (ev) {
    ev.preventDefault();
    idx = (idx + 1) % excuses.length;
    if (reduceMotion) { render(excuses[idx]); return; }
    box.classList.add('is-swapping');
    window.setTimeout(function () {
      render(excuses[idx]);
      box.classList.remove('is-swapping');
    }, 180);
  });
})();
