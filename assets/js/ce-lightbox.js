/**
 * CE Theme lightbox (since 2.6.33).
 *
 * A dependency-free replacement for Lightbox2: the same gallery behaviour
 * (grouped images, previous/next, keyboard and swipe navigation, captions,
 * "n of m" counter) without jQuery, plus support for inline SVG diagrams.
 *
 * Accessible: role="dialog" with aria-modal, focus moves into the dialog
 * and returns to the trigger on close, Tab is kept inside the dialog, and
 * Escape closes it. Motion follows prefers-reduced-motion via CSS.
 */
(function () {
  'use strict';

  var i18n = window.ceLightbox || { close: 'Close', prev: 'Previous image', next: 'Next image', count: '%1$s of %2$s' };
  var triggers = [];
  var index = 0;
  var box, stage, caption, lastFocus, touchX = null;

  function collect() {
    triggers = Array.prototype.slice.call(
      document.querySelectorAll('a[data-ce-lightbox], button[data-ce-lightbox-diagram]')
    );
  }

  function button(cls, label, path) {
    var b = document.createElement('button');
    b.type = 'button';
    b.className = 'ce-lb-btn ' + cls;
    b.setAttribute('aria-label', label);
    b.innerHTML = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="' + path + '"/></svg>';
    return b;
  }

  function build() {
    box = document.createElement('div');
    box.className = 'ce-lb';
    box.setAttribute('role', 'dialog');
    box.setAttribute('aria-modal', 'true');
    box.setAttribute('aria-label', i18n.viewer || 'Image viewer');
    box.hidden = true;

    stage = document.createElement('div');
    stage.className = 'ce-lb-stage';
    caption = document.createElement('div');
    caption.className = 'ce-lb-caption';
    caption.id = 'ce-lb-caption';
    box.setAttribute('aria-describedby', 'ce-lb-caption');

    var close = button('ce-lb-close', i18n.close, 'M18 6 6 18M6 6l12 12');
    var prev = button('ce-lb-prev', i18n.prev, 'M15 18l-6-6 6-6');
    var next = button('ce-lb-next', i18n.next, 'M9 18l6-6-6-6');
    close.addEventListener('click', hide);
    prev.addEventListener('click', function () { go(-1); });
    next.addEventListener('click', function () { go(1); });

    box.addEventListener('click', function (e) { if (e.target === box) hide(); });
    box.addEventListener('keydown', onKey);
    box.addEventListener('touchstart', function (e) { touchX = e.changedTouches[0].clientX; }, { passive: true });
    box.addEventListener('touchend', function (e) {
      if (touchX === null) return;
      var dx = e.changedTouches[0].clientX - touchX;
      touchX = null;
      if (Math.abs(dx) > 50) go(dx < 0 ? 1 : -1);
    }, { passive: true });

    box.appendChild(stage);
    box.appendChild(caption);
    box.appendChild(prev);
    box.appendChild(next);
    box.appendChild(close);
    document.body.appendChild(box);
  }

  function render() {
    var t = triggers[index];
    var fig = t.closest('figure');
    stage.innerHTML = '';
    stage.classList.remove('is-diagram');

    if (t.hasAttribute('data-ce-lightbox-diagram')) {
      var svg = fig && fig.querySelector('svg.ce-dg');
      if (svg) {
        var clone = svg.cloneNode(true);
        // Keep aria ids unique while the original stays in the page.
        clone.querySelectorAll('[id]').forEach(function (n) { n.id = n.id + '-lb'; });
        var lab = clone.getAttribute('aria-labelledby');
        if (lab) clone.setAttribute('aria-labelledby', lab.split(' ').map(function (x) { return x + '-lb'; }).join(' '));
        clone.querySelectorAll('[marker-end],[marker-start]').forEach(function (n) {
          ['marker-end', 'marker-start'].forEach(function (a) {
            var v = n.getAttribute(a);
            if (v) n.setAttribute(a, v.replace(/url\(#([^)]+)\)/, 'url(#$1-lb)'));
          });
        });
        stage.appendChild(clone);
        stage.classList.add('is-diagram');
      }
    } else {
      var img = document.createElement('img');
      var inner = t.querySelector('img');
      img.src = t.getAttribute('href');
      img.alt = inner ? inner.getAttribute('alt') || '' : '';
      stage.appendChild(img);
    }

    caption.innerHTML = '';
    var fc = fig && fig.querySelector('figcaption');
    if (fc) {
      Array.prototype.forEach.call(fc.childNodes, function (n) { caption.appendChild(n.cloneNode(true)); });
    }
    box.setAttribute('data-count', String(triggers.length));
    if (triggers.length > 1) {
      var c = document.createElement('span');
      c.className = 'ce-lb-count';
      c.textContent = i18n.count.replace('%1$s', index + 1).replace('%2$s', triggers.length);
      caption.appendChild(c);
    }
  }

  function show(i) {
    collect();
    if (!box) build();
    index = i;
    lastFocus = document.activeElement;
    render();
    box.hidden = false;
    document.documentElement.classList.add('ce-lb-lock');
    requestAnimationFrame(function () {
      box.classList.add('is-open');
      // The panel is visibility:hidden until the class lands, so focus after it.
      box.querySelector('.ce-lb-close').focus();
    });
  }

  function hide() {
    box.classList.remove('is-open');
    document.documentElement.classList.remove('ce-lb-lock');
    setTimeout(function () { box.hidden = true; stage.innerHTML = ''; }, 200);
    if (lastFocus && lastFocus.focus) lastFocus.focus();
  }

  function go(step) {
    if (triggers.length < 2) return;
    index = (index + step + triggers.length) % triggers.length;
    render();
  }

  function onKey(e) {
    if (e.key === 'Escape') { e.preventDefault(); hide(); return; }
    if (e.key === 'ArrowRight') { e.preventDefault(); go(1); return; }
    if (e.key === 'ArrowLeft') { e.preventDefault(); go(-1); return; }
    if (e.key === 'Tab') {
      var f = Array.prototype.filter.call(box.querySelectorAll('button, a[href]'), function (n) { return n.offsetParent !== null; });
      if (!f.length) return;
      var first = f[0], last = f[f.length - 1];
      if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
      else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    }
  }

  document.addEventListener('click', function (e) {
    var t = e.target.closest('a[data-ce-lightbox], button[data-ce-lightbox-diagram]');
    if (!t) return;
    // Let modified clicks open the image in a new tab as usual.
    if (e.metaKey || e.ctrlKey || e.shiftKey || e.button === 1) return;
    e.preventDefault();
    collect();
    show(Math.max(0, triggers.indexOf(t)));
  });
})();
