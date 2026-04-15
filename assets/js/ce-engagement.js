/**
 * Compelling Evidence — Engagement System
 * Handles voting, resonance feedback, and share bar interactions.
 *
 * @since 1.7.0
 */
(function () {
  'use strict';

  var AJAX = (typeof CE_Engage !== 'undefined') ? CE_Engage : {};

  /* ───────────────────────────────────────────────
     VOTING
     ─────────────────────────────────────────────── */

  function initVoting() {
    var widgets = document.querySelectorAll('.ce-vote-widget');
    widgets.forEach(function (widget) {
      if (widget.classList.contains('ce-voted')) return;

      var postId = widget.dataset.postId;
      var btns   = widget.querySelectorAll('.ce-vote-btn');

      btns.forEach(function (btn) {
        btn.addEventListener('click', function () {
          if (widget.classList.contains('ce-voted')) return;

          var direction = btn.dataset.direction;
          var scoreEl   = widget.querySelector('.ce-vote-score');

          // Optimistic UI
          btn.classList.add('active');
          widget.classList.add('ce-voted');
          btns.forEach(function (b) { b.disabled = true; });

          var currentScore = parseInt(scoreEl.textContent, 10) || 0;
          scoreEl.textContent = direction === 'up' ? currentScore + 1 : currentScore - 1;
          scoreEl.classList.add('ce-score-bump');
          setTimeout(function () { scoreEl.classList.remove('ce-score-bump'); }, 400);

          // AJAX
          var body = new FormData();
          body.append('action', 'ce_vote');
          body.append('nonce', AJAX.nonce);
          body.append('post_id', postId);
          body.append('direction', direction);

          fetch(AJAX.ajaxUrl, { method: 'POST', body: body })
            .then(function (r) { return r.json(); })
            .then(function (res) {
              if (res.success) {
                scoreEl.textContent = res.data.score;
              }
            })
            .catch(function () {
              // Revert on failure
              scoreEl.textContent = currentScore;
              btn.classList.remove('active');
              widget.classList.remove('ce-voted');
              btns.forEach(function (b) { b.disabled = false; });
            });
        });
      });
    });
  }


  /* ───────────────────────────────────────────────
     RESONANCE FEEDBACK
     ─────────────────────────────────────────────── */

  function initResonance() {
    var panels = document.querySelectorAll('.ce-resonance');
    panels.forEach(function (panel) {
      if (panel.classList.contains('ce-resonated')) return;

      var postId = panel.dataset.postId;
      var btns   = panel.querySelectorAll('.ce-res-btn');

      btns.forEach(function (btn) {
        btn.addEventListener('click', function () {
          if (panel.classList.contains('ce-resonated')) return;

          var type = btn.dataset.type;

          // Optimistic UI
          btn.classList.add('ce-res-selected');
          panel.classList.add('ce-resonated');
          btns.forEach(function (b) {
            if (b !== btn) b.style.opacity = '0.3';
            b.style.pointerEvents = 'none';
          });

          // AJAX
          var body = new FormData();
          body.append('action', 'ce_resonance');
          body.append('nonce', AJAX.nonce);
          body.append('post_id', postId);
          body.append('type', type);

          fetch(AJAX.ajaxUrl, { method: 'POST', body: body })
            .then(function (r) { return r.json(); })
            .then(function (res) {
              if (res.success) {
                // Replace with thank you + results
                var optionsEl = panel.querySelector('.ce-resonance-options');
                if (optionsEl) {
                  optionsEl.innerHTML =
                    '<p class="ce-resonance-thanks">Thank you for your feedback.</p>' +
                    '<div class="ce-resonance-results">' +
                      '<div class="ce-res-stat"><span class="ce-res-count">' + res.data.addressed + '</span><span class="ce-res-label">found it helpful</span></div>' +
                      '<div class="ce-res-stat"><span class="ce-res-count">' + res.data.questions + '</span><span class="ce-res-label">still have questions</span></div>' +
                      '<div class="ce-res-stat"><span class="ce-res-count">' + res.data.discuss + '</span><span class="ce-res-label">want to discuss</span></div>' +
                    '</div>';
                }
              }
            })
            .catch(function () {
              // Revert on failure
              panel.classList.remove('ce-resonated');
              btns.forEach(function (b) {
                b.style.opacity = '';
                b.style.pointerEvents = '';
              });
              btn.classList.remove('ce-res-selected');
            });
        });
      });
    });
  }


  /* ───────────────────────────────────────────────
     SHARE BAR
     ─────────────────────────────────────────────── */

  function initShareBar() {
    // Show native share button if Web Share API is available
    if (navigator.share) {
      var nativeBtns = document.querySelectorAll('.ce-share-native');
      nativeBtns.forEach(function (btn) { btn.style.display = 'flex'; });
    }

    // Copy link
    var copyBtns = document.querySelectorAll('.ce-share-copy');
    copyBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var url = btn.dataset.url || window.location.href;

        if (navigator.clipboard) {
          navigator.clipboard.writeText(url).then(function () {
            btn.classList.add('copied');
            // Swap icon to checkmark briefly
            var svg = btn.querySelector('svg');
            var origHTML = svg.outerHTML;
            svg.outerHTML = '<svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>';
            setTimeout(function () {
              btn.classList.remove('copied');
              btn.querySelector('svg').outerHTML = origHTML;
            }, 2000);
          });
        } else {
          // Fallback
          var inp = document.createElement('input');
          inp.value = url;
          document.body.appendChild(inp);
          inp.select();
          document.execCommand('copy');
          document.body.removeChild(inp);
          btn.classList.add('copied');
          setTimeout(function () { btn.classList.remove('copied'); }, 2000);
        }
      });
    });

    // Native share
    var nativeBtns = document.querySelectorAll('.ce-share-native');
    nativeBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var bar = btn.closest('.ce-share-bar');
        if (navigator.share && bar) {
          navigator.share({
            title: bar.dataset.title || document.title,
            url:   bar.dataset.url || window.location.href,
          }).catch(function () {});
        }
      });
    });
  }


  /* ───────────────────────────────────────────────
     INIT
     ─────────────────────────────────────────────── */

  function init() {
    initVoting();
    initResonance();
    initShareBar();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
