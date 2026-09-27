/**
 * Compelling Evidence — main.js
 * Single IIFE — all features contained within one scope.
 */

(function () {
  'use strict';

  // ── HELPERS ─────────────────────────────────────────────────────────────────
  function escHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str || ''));
    return div.innerHTML;
  }

  // ── NAV: scroll class + mobile toggle ───────────────────────────────────────
  var nav    = document.getElementById('ce-nav');
  var toggle = document.querySelector('.nav-toggle');
  var links  = document.querySelector('.nav-links');

  if (nav) {
    window.addEventListener('scroll', function () {
      nav.classList.toggle('scrolled', window.scrollY > 40);
    }, { passive: true });
  }

  if (toggle && links) {
    var navActions = document.querySelector('.nav-actions');
    var mobileActionsAdded = false;

    toggle.addEventListener('click', function () {
      var open = links.classList.toggle('open');
      toggle.classList.toggle('open', open);
      toggle.setAttribute('aria-expanded', String(open));

      // Inject nav action links into mobile dropdown on first open
      if (open && !mobileActionsAdded && navActions && window.innerWidth <= 900) {
        var actionLinks = navActions.querySelectorAll('a');
        actionLinks.forEach(function (a) {
          var li = document.createElement('li');
          var clone = a.cloneNode(true);
          clone.classList.add('nav-mobile-action');
          clone.style.display = '';  // override any inline display:none
          li.appendChild(clone);
          links.appendChild(li);
        });
        mobileActionsAdded = true;
      }
    });
    function closeMenu() {
      links.classList.remove('open');
      toggle.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
    }
    links.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', closeMenu);
    });
    // Escape closes the mobile menu and returns focus to the toggle.
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && links.classList.contains('open')) {
        closeMenu();
        toggle.focus();
      }
    });
    // Rotating a tablet to landscape (above 900px) must not leave the menu stuck open.
    window.addEventListener('resize', function () {
      if (window.innerWidth > 900 && links.classList.contains('open')) closeMenu();
    }, { passive: true });
  }

  // ── FEATURED STRIP: hide on scroll down ─────────────────────────────────────
  var strip      = document.getElementById('featured-strip');
  var lastScroll = 0;
  if (strip) {
    window.addEventListener('scroll', function () {
      var current = window.scrollY;
      strip.classList.toggle('hidden', current > lastScroll && current > 120);
      lastScroll = current <= 0 ? 0 : current;
    }, { passive: true });
  }

  // ── SCROLL REVEAL ────────────────────────────────────────────────────────────
  var reveals = document.querySelectorAll('.reveal');
  if (reveals.length && 'IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry, idx) {
        if (entry.isIntersecting) {
          setTimeout(function () { entry.target.classList.add('visible'); }, idx * 80);
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add('visible'); });
  }

  // ── LIVE SEARCH ──────────────────────────────────────────────────────────────
  var searchInput   = document.getElementById('ce-search-input');
  var searchBtn     = document.getElementById('ce-search-btn');
  var searchResults = document.getElementById('ce-search-results');
  var debounceTimer;

  function closeDropdown() {
    if (searchResults) {
      searchResults.classList.remove('open');
      searchResults.innerHTML = '';
    }
    var hp = document.querySelector('.hero-prompts');
    if (hp) hp.classList.remove('hero-prompts--hidden');
    // Restore scroll-hint if still in hero viewport
    var sh = document.querySelector('.scroll-hint');
    if (sh && window.scrollY <= 80) sh.style.opacity = '';
  }

  function renderResults(items) {
    if (!searchResults) return;
    // Every interpolated value must be escaped — earlier versions escaped
    // url and topic but inlined title and excerpt unescaped, which let any
    // post with HTML in its title or excerpt execute script when shown in
    // search results. The server uses html_entity_decode() on these fields
    // before JSON-encoding them, so we cannot rely on them being pre-escaped.
    searchResults.innerHTML = items.map(function (item) {
      return '<a href="' + escHtml(item.url) + '" class="search-result-item">' +
        (item.topic ? '<span class="search-result-tag">' + escHtml(item.topic) + '</span>' : '') +
        '<span class="search-result-title">' + escHtml(item.title) + '</span>' +
        (item.excerpt ? '<span class="search-result-excerpt">' + escHtml(item.excerpt) + '</span>' : '') +
        '</a>';
    }).join('');
    searchResults.classList.add('open');
    // Hide scroll-hint tiles so they don't bleed through
    var sh = document.querySelector('.scroll-hint');
    if (sh) sh.style.opacity = '0';
  }

  function renderNoResults() {
    if (!searchResults) return;
    searchResults.innerHTML = '<p class="search-no-results">No results found. Try a different term.</p>';
    searchResults.classList.add('open');
  }

  function doSearch(term) {
    if (!term || term.length < 2) { closeDropdown(); return; }
    var hp = document.querySelector('.hero-prompts');
    if (hp) hp.classList.add('hero-prompts--hidden');
    if (typeof CE !== 'undefined' && CE.ajaxUrl) {
      fetch(CE.ajaxUrl + '?action=ce_search&nonce=' + CE.nonce + '&term=' + encodeURIComponent(term))
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.success && data.data && data.data.length) {
            renderResults(data.data);
          } else {
            renderNoResults();
          }
        })
        .catch(function () { closeDropdown(); });
    } else {
      window.location.href = '/?s=' + encodeURIComponent(term);
    }
  }

  if (searchInput) {
    searchInput.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(function () { doSearch(searchInput.value.trim()); }, 300);
    });
    searchInput.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        var term = searchInput.value.trim();
        if (term) {
          var base = (typeof CE !== 'undefined' && CE.homeUrl) ? CE.homeUrl : '/';
          window.location.href = base + '?s=' + encodeURIComponent(term);
        }
      }
      if (e.key === 'Escape') closeDropdown();
    });
  }

  if (searchBtn) {
    searchBtn.addEventListener('click', function () {
      var term = searchInput ? searchInput.value.trim() : '';
      if (term) {
        var base = (typeof CE !== 'undefined' && CE.homeUrl) ? CE.homeUrl : '/';
        window.location.href = base + '?s=' + encodeURIComponent(term);
      } else {
        doSearch('');
      }
    });
  }

  document.addEventListener('click', function (e) {
    if (searchResults && !searchResults.contains(e.target) && e.target !== searchInput) {
      closeDropdown();
    }
  });

  // ── HERO PROMPT CHIPS ────────────────────────────────────────────────────────
  var promptChips = document.querySelectorAll('.hero-prompts-chips .hero-prompt-chip');

  promptChips.forEach(function (chip) {
    chip.addEventListener('click', function () {
      var query = chip.getAttribute('data-query');
      if (!query || !searchInput) return;
      searchInput.value = query;
      searchInput.dispatchEvent(new Event('input'));
      promptChips.forEach(function (c) { c.classList.remove('chip-fired'); });
      chip.classList.add('chip-fired');
      searchInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
      searchInput.focus();
    });
  });

  // ── RANDOM ARTICLE ────────────────────────────────────────────────────────────
  var randomBtn = document.getElementById('ce-random-btn');
  if (randomBtn) {
    randomBtn.addEventListener('click', function (e) {
      e.preventDefault();
      randomBtn.classList.add('loading');
      if (typeof CE !== 'undefined' && CE.ajaxUrl) {
        fetch(CE.ajaxUrl + '?action=ce_random&nonce=' + CE.nonce)
          .then(function (r) { return r.json(); })
          .then(function (data) {
            if (data.success && data.data && data.data.url) {
              window.location.href = data.data.url;
            } else {
              randomBtn.classList.remove('loading');
            }
          })
          .catch(function () { randomBtn.classList.remove('loading'); });
      }
    });
  }

  // Journey entry pill highlighting removed — personas not exposed on homepage

  // ── QUIZ RESULT SYNC ─────────────────────────────────────────────────────────
  window.CE_syncQuizResult = function (primary, secondaries) {
    if (!window.CE || !CE.ajaxUrl) return;
    var data = new FormData();
    data.append('action', 'ce_save_quiz');
    data.append('nonce', CE.nonce);
    data.append('primary', primary);
    if (secondaries) {
      secondaries.forEach(function (s) { data.append('secondaries[]', s); });
    }
    fetch(CE.ajaxUrl, { method: 'POST', body: data }).catch(function () {});
  };

  // ── SCROLL HINT: hide once user scrolls ─────────────────────────────────────
  var scrollHint = document.querySelector('.scroll-hint');
  if (scrollHint) {
    var hintHidden = false;
    window.addEventListener('scroll', function () {
      if (!hintHidden && window.scrollY > 80) {
        hintHidden = true;
        scrollHint.style.transition = 'opacity 0.4s';
        scrollHint.style.opacity   = '0';
        scrollHint.style.pointerEvents = 'none';
      } else if (hintHidden && window.scrollY <= 40) {
        hintHidden = false;
        scrollHint.style.opacity = '';
        scrollHint.style.pointerEvents = '';
      }
    }, { passive: true });
  }

  // ── PROGRESS SYNC ────────────────────────────────────────────────────────────
  window.CE_syncProgress = function (path, screen) {
    if (!window.CE || !CE.ajaxUrl) return;
    var data = new FormData();
    data.append('action', 'ce_save_progress');
    data.append('nonce', CE.nonce);
    data.append('path', path);
    data.append('screen', screen);
    fetch(CE.ajaxUrl, { method: 'POST', body: data }).catch(function () {});
  };

})(); // end IIFE
