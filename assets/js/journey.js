/**
 * CE Theme: shared runtime for the persona journeys (journeys/*-journey.html).
 *
 * Each journey page sets window.PATH and window.CE_JOURNEY inline, then
 * page-journey.php loads this file in place of the scripts that used to be
 * pasted into all 14 pages. CE_JOURNEY holds:
 *   screens    chapter ids in order (s-<id> elements)
 *   labels     chapter names for the header
 *   star       sidebar symbol for the conclusion
 *   articles   "Go deeper" links per chapter
 *   resonance  true to fill the "paths that resonate" widget
 *   shareName  persona name on the completion badge; empty for no badge
 * window.ceHomeUrl (printed by the theme) is the site root for path links.
 *
 * The parts run in the order the inline copies did, because each one wraps
 * goTo() on window load: navigation, progress, resonance, go deeper, share.
 *
 * @since 2.8.6
 */

/* ── Navigation ── */
const screens=CE_JOURNEY.screens;const labels=CE_JOURNEY.labels;let cur=0;
// The gold dot and the star in the sidebar both mark the conclusion.
const ceGold=screens.indexOf('conclusion');
const ceSidebarMap=(function(){var r=['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'],m={};for(var i=0;i<ceGold;i++)m[r[i]]=i;m[CE_JOURNEY.star]=ceGold;return m;})();
function goTo(id){const i=screens.indexOf(id);if(i<0)return;if(window.ceTrack){ceTrack('journey_screen',{persona:PATH,screen:id});if(id==='conclusion')ceTrack('journey_complete',{persona:PATH});}screens.forEach(s=>{const el=document.getElementById('s-'+s);if(el)el.classList.remove('active');});const nx=document.getElementById('s-'+id);const ae=document.activeElement;nx.classList.add('active');cur=i;window.scrollTo(0,0);updateNav();if(ae&&ae!==document.body&&!nx.contains(ae)&&ae.closest('.screen')){if(!nx.hasAttribute('tabindex'))nx.setAttribute('tabindex','-1');nx.focus({preventScroll:true});}}
function updateNav(){document.getElementById('nav-current').textContent=labels[cur];document.querySelectorAll('.np-dot').forEach((d,i)=>{d.classList.remove('active','done');if(i<cur){d.classList.add('done');d.style.background=i===ceGold?'rgba(245,197,24,0.5)':'rgba(12,212,224,0.4)';}else if(i===cur){d.classList.add('active');d.style.background=i===ceGold?'rgba(245,197,24,0.8)':'var(--teal)';}else{d.style.background=i===ceGold?'rgba(245,197,24,0.12)':'rgba(255,255,255,0.1)';}}); }
document.addEventListener('click',function(e){if(e.target.closest('.choice')){goTo(screens[Math.min(cur+1,screens.length-1)]);return;}if(e.target.closest('.btn-teal')){goTo(screens[Math.min(cur+1,screens.length-1)]);return;}if(e.target.closest('.btn-gold')){goTo('conclusion');return;}const ps=e.target.closest('.ps:not(.locked)');if(ps){const n=ps.querySelector('.ps-n');if(!n)return;const m=ceSidebarMap;const t=n.textContent.trim();if(t in m){cur=m[t];goTo(screens[m[t]]);}}}); updateNav();

/* ── Progress and resume ── */
(function() {
  var PATH = window.PATH;
  // Chapters that can be resumed: everything up to and including the conclusion.
  var SCREENS = CE_JOURNEY.screens.slice(0, CE_JOURNEY.screens.indexOf("conclusion") + 1);

  // Save progress on screen change
  function saveProgress(screenId) {
    try {
      var key = "ce_progress_" + PATH;
      localStorage.setItem(key, screenId);
    } catch(e) {}
  }

  // Load progress
  function loadProgress() {
    try {
      return localStorage.getItem("ce_progress_" + PATH);
    } catch(e) { return null; }
  }

  // Mark path as started/completed
  function markPathStatus(screenId) {
    try {
      var started = JSON.parse(localStorage.getItem("ce_started_paths") || "[]");
      if (started.indexOf(PATH) === -1) {
        started.push(PATH);
        localStorage.setItem("ce_started_paths", JSON.stringify(started));
      }
      if (screenId === "conclusion") {
        var completed = JSON.parse(localStorage.getItem("ce_completed_paths") || "[]");
        if (completed.indexOf(PATH) === -1) {
          completed.push(PATH);
          localStorage.setItem("ce_completed_paths", JSON.stringify(completed));
        }
      }
    } catch(e) {}
  }

  // Wrap the existing goTo function to save progress
  window.addEventListener("load", function() {
    var origGoTo = window.goTo;
    if (origGoTo) {
      window.goTo = function(id) {
        origGoTo(id);
        saveProgress(id);
        markPathStatus(id);
      };
    }

    // Show resume banner if returning
    var saved = loadProgress();
    if (saved && saved !== SCREENS[0] && SCREENS.indexOf(saved) > 0) {
      var banner = document.createElement("div");
      banner.id = "ce-resume-banner";
      banner.style.cssText = "position:fixed;bottom:1.5rem;right:1.5rem;z-index:999;background:rgba(13,8,32,0.95);border:1px solid rgba(12,212,224,0.25);border-radius:12px;padding:0.9rem 1.2rem;display:flex;align-items:center;gap:1rem;font-family:\'DM Sans\',sans-serif;font-size:0.82rem;color:rgba(242,238,255,0.7);box-shadow:0 8px 32px rgba(0,0,0,0.4);backdrop-filter:blur(8px);max-width:300px;";
      var screenLabel = saved.charAt(0).toUpperCase() + saved.slice(1);
      banner.innerHTML = "<div><div style=\"font-size:0.68rem;letter-spacing:0.14em;text-transform:uppercase;color:rgba(12,212,224,0.5);margin-bottom:0.2rem;\">Resume where you left off</div><div style=\"color:rgba(242,238,255,0.9);\">" + screenLabel + "</div></div>" +
        "<button onclick=\"window.goTo(\'" + saved + "\');document.getElementById(\'ce-resume-banner\').remove();\" style=\"padding:0.5rem 1rem;border-radius:50px;background:rgba(12,212,224,0.15);border:1px solid rgba(12,212,224,0.3);color:var(--teal,#0cd4e0);font-family:inherit;font-size:0.8rem;cursor:pointer;white-space:nowrap;\">Resume</button>" +
        "<button onclick=\"document.getElementById(\'ce-resume-banner\').remove();\" style=\"background:none;border:none;color:rgba(242,238,255,0.3);cursor:pointer;font-size:1.2rem;line-height:1;padding:0;\">&times;</button>";
      document.body.appendChild(banner);
    }
  });
})();

/* ── Paths that resonate ── */
(function() {
  if (!CE_JOURNEY.resonance) return;
  var PRIMARY = window.PATH;
  var ALL_RESONANCES = {"new-atheist": ["classical-atheist", "materialist", "scientist"], "agnostic": ["deist", "scientist", "spiritual-seeker"], "secular-humanist": ["apatheist", "ex-believer", "materialist"], "antitheist": ["new-atheist", "ex-believer", "classical-atheist"], "materialist": ["classical-atheist", "scientist", "new-atheist"], "muslim-doubts": ["ex-believer", "agnostic", "deist"], "apatheist": ["secular-humanist", "agnostic", "deist"], "deist": ["agnostic", "spiritual-seeker", "scientist"], "scientist": ["classical-atheist", "materialist", "agnostic"], "classical-atheist": ["scientist", "materialist", "new-atheist"], "ex-believer": ["muslim-doubts", "agnostic", "spiritual-seeker"], "spiritual-seeker": ["deist", "agnostic", "ex-believer"], "freethinker": ["scientist", "agnostic", "classical-atheist"], "true-muslim": ["muslim-doubts", "ex-believer", "freethinker"]};
  var PATH_NAMES = {"new-atheist": "The New Atheist", "agnostic": "The Agnostic", "secular-humanist": "The Secular Humanist", "antitheist": "The Antitheist", "materialist": "The Materialist", "muslim-doubts": "The Questioning Muslim", "apatheist": "The Apatheist", "deist": "The Deist", "scientist": "The Scientist", "classical-atheist": "The Classical Atheist", "ex-believer": "The Ex-Believer", "spiritual-seeker": "The Spiritual Seeker", "freethinker": "The Freethinker", "true-muslim": "The Committed Muslim"};
  var PATH_FILES = {};
  Object.keys(PATH_NAMES).forEach(function(k) { PATH_FILES[k] = (window.ceHomeUrl || "/") + "journey/" + k + "/"; });

  window.addEventListener("load", function() {
    // Get secondary paths from quiz result or fallback to defaults
    var secondaries = [];
    try {
      var stored = localStorage.getItem("ce_secondary_paths");
      if (stored) {
        secondaries = JSON.parse(stored).filter(function(p) { return p !== PRIMARY; }).slice(0,2);
      }
    } catch(e) {}

    // Fallback to default resonances if no quiz data
    if (secondaries.length === 0) {
      secondaries = (ALL_RESONANCES[PRIMARY] || []).slice(0,2);
    }

    if (secondaries.length === 0) return;

    var widget = document.getElementById("ce-resonance-widget");
    var container = document.getElementById("ce-resonance-items");
    if (!widget || !container) return;

    secondaries.forEach(function(path) {
      var name = PATH_NAMES[path] || path;
      var file = PATH_FILES[path] || (path + "-journey.html");
      var item = document.createElement("a");
      item.href = file;
      item.style.cssText = "display:flex;align-items:center;justify-content:space-between;padding:0.6rem 0.8rem;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);border-radius:8px;text-decoration:none;transition:border-color 0.15s;";
      item.innerHTML = "<span style=\"font-size:0.8rem;color:rgba(242,238,255,0.6);\">" + name + "</span><span style=\"font-size:0.7rem;color:rgba(12,212,224,0.5);\">Explore →</span>";
      item.onmouseover = function() { this.style.borderColor = "rgba(12,212,224,0.2)"; };
      item.onmouseout  = function() { this.style.borderColor = "rgba(255,255,255,0.05)"; };
      container.appendChild(item);
    });

    widget.style.display = "block";
  });
})();

/* ── Go deeper ── */
(function() {
  var CHAPTER_ARTICLES = CE_JOURNEY.articles || {};

  function buildWidget(screenId) {
    var articles = CHAPTER_ARTICLES[screenId];
    if (!articles || !articles.length) return null;
    var w = document.createElement('div');
    w.className = 'sw go-deeper-widget';
    w.innerHTML = '<span class="sw-title" style="color:rgba(12,212,224,0.6);">Go deeper</span>';
    var list = document.createElement('div');
    list.className = 'rel-list';
    articles.slice(0,3).forEach(function(a){
      var el = document.createElement('a');
      el.className = 'rel-item';
      el.href = a.href;
      el.target = '_blank';
      el.rel = 'noopener';
      el.style.cssText = 'display:flex;flex-direction:column;gap:0.12rem;padding:0.45rem 0;border-bottom:1px solid rgba(255,255,255,0.05);text-decoration:none;';
      el.innerHTML = '<span style="font-size:0.78rem;color:rgba(242,238,255,0.7);line-height:1.35;transition:color 0.15s;">' + a.title + '</span><span style="font-size:0.68rem;color:rgba(12,212,224,0.4);letter-spacing:0.08em;">' + a.label + ' ›</span>';
      el.addEventListener('mouseover',function(){this.querySelector('span').style.color='rgba(12,212,224,0.8)';});
      el.addEventListener('mouseout', function(){this.querySelector('span').style.color='rgba(242,238,255,0.7)';});
      list.appendChild(el);
    });
    w.appendChild(list);
    return w;
  }

  function updateGoDeeper(screenId) {
    var screen = document.getElementById('s-' + screenId);
    if (!screen) return;
    var sticky = screen.querySelector('.sidebar-sticky');
    if (!sticky) return;
    var old = sticky.querySelector('.go-deeper-widget');
    if (old) old.remove();
    var widget = buildWidget(screenId);
    if (!widget) return;
    var sws = sticky.querySelectorAll('.sw');
    var last = sws[sws.length - 1];
    last ? sticky.insertBefore(widget, last) : sticky.appendChild(widget);
  }

  window.addEventListener('load', function() {
    var orig = window.goTo;
    if (orig) {
      window.goTo = function(id) {
        orig(id);
        updateGoDeeper(id);
      };
    }
    updateGoDeeper(CE_JOURNEY.screens[0]);
  });
})();

/* ── Completion badge and share ── */
(function() {
  if (!CE_JOURNEY.shareName) return;
  var PATH_NAME  = CE_JOURNEY.shareName;
  var SITE_NAME  = "Compelling Evidence";
  var SHARE_TEXT = "I followed the evidence honestly on the " + PATH_NAME + " path | " + SITE_NAME;
  var SHARE_URL  = window.location.href;

  function buildShareBlock() {
    var wrap = document.createElement('div');
    wrap.id = 'ce-share-block';
    wrap.style.cssText = 'margin:2rem 0;text-align:center;';

    // Badge
    var badge = document.createElement('div');
    badge.id = 'ce-completion-badge';
    badge.style.cssText = [
      'display:inline-flex;flex-direction:column;align-items:center;gap:0.5rem;',
      'padding:1.4rem 2rem;border-radius:16px;',
      'background:rgba(245,197,24,0.06);border:1px solid rgba(245,197,24,0.2);',
      'margin-bottom:1.6rem;cursor:pointer;transition:all 0.2s;',
      'position:relative;overflow:hidden;'
    ].join('');

    // Badge shimmer
    var shimmer = document.createElement('div');
    shimmer.style.cssText = [
      'position:absolute;inset:0;',
      'background:linear-gradient(105deg,transparent 40%,rgba(245,197,24,0.08) 50%,transparent 60%);',
      'animation:badgeShimmer 3s ease-in-out infinite;'
    ].join('');
    badge.appendChild(shimmer);

    var badgeInner = document.createElement('div');
    badgeInner.style.cssText = 'position:relative;z-index:1;text-align:center;';
    badgeInner.innerHTML = [
      '<div style="font-size:2.2rem;margin-bottom:0.3rem;">✦</div>',
      '<div style="font-family:\'Playfair Display\',serif;font-size:0.9rem;font-weight:700;color:rgba(245,197,24,0.9);letter-spacing:0.05em;">Journey Complete</div>',
      '<div style="font-family:\'DM Sans\',sans-serif;font-size:0.72rem;color:rgba(245,197,24,0.5);letter-spacing:0.12em;text-transform:uppercase;margin-top:0.15rem;">' + PATH_NAME + '</div>',
      '<div style="font-family:\'DM Sans\',sans-serif;font-size:0.65rem;color:rgba(242,238,255,0.25);margin-top:0.6rem;">Tap to share</div>'
    ].join('');
    badge.appendChild(badgeInner);
    badge.addEventListener('click', openShareMenu);
    wrap.appendChild(badge);

    // Share row
    var shareRow = document.createElement('div');
    shareRow.id = 'ce-share-row';
    shareRow.style.cssText = 'display:none;flex-wrap:wrap;gap:0.6rem;justify-content:center;margin-top:0.5rem;';

    var platforms = [
      {
        name: 'X / Twitter',
        color: 'rgba(255,255,255,0.08)',
        border: 'rgba(255,255,255,0.12)',
        icon: '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        fn: function() {
          window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(SHARE_TEXT) + '&url=' + encodeURIComponent(SHARE_URL), '_blank');
        }
      },
      {
        name: 'WhatsApp',
        color: 'rgba(37,211,102,0.08)',
        border: 'rgba(37,211,102,0.18)',
        icon: '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>',
        fn: function() {
          window.open('https://wa.me/?text=' + encodeURIComponent(SHARE_TEXT + ' ' + SHARE_URL), '_blank');
        }
      },
      {
        name: 'Copy link',
        color: 'rgba(12,212,224,0.06)',
        border: 'rgba(12,212,224,0.18)',
        icon: '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>',
        fn: function(btn) {
          navigator.clipboard.writeText(SHARE_URL).then(function() {
            var orig = btn.querySelector('.share-label');
            if (orig) { orig.textContent = 'Copied!'; setTimeout(function(){ orig.textContent = 'Copy link'; }, 2000); }
          }).catch(function() {
            prompt('Copy this link:', SHARE_URL);
          });
        }
      }
    ];

    platforms.forEach(function(p) {
      var btn = document.createElement('button');
      btn.style.cssText = [
        'display:inline-flex;align-items:center;gap:0.45rem;',
        'padding:0.55rem 1.1rem;border-radius:50px;cursor:pointer;',
        'background:' + p.color + ';border:1px solid ' + p.border + ';',
        'color:rgba(242,238,255,0.7);font-family:\'DM Sans\',sans-serif;',
        'font-size:0.78rem;transition:all 0.15s;'
      ].join('');
      btn.innerHTML = p.icon + '<span class="share-label">' + p.name + '</span>';
      btn.addEventListener('mouseover', function(){ this.style.opacity='0.85'; });
      btn.addEventListener('mouseout',  function(){ this.style.opacity='1'; });
      btn.addEventListener('click', function(){ p.fn(btn); });
      shareRow.appendChild(btn);
    });

    // Native share if available
    if (navigator.share) {
      var nativeBtn = document.createElement('button');
      nativeBtn.style.cssText = platforms[0].style + ';background:rgba(107,47,160,0.08);border:1px solid rgba(107,47,160,0.2);';
      nativeBtn.style.cssText = 'display:inline-flex;align-items:center;gap:0.45rem;padding:0.55rem 1.1rem;border-radius:50px;cursor:pointer;background:rgba(107,47,160,0.08);border:1px solid rgba(107,47,160,0.2);color:rgba(242,238,255,0.7);font-family:\'DM Sans\',sans-serif;font-size:0.78rem;';
      nativeBtn.innerHTML = '<svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><polyline points="16 6 12 2 8 6"/><line x1="12" y1="2" x2="12" y2="15"/></svg><span>Share</span>';
      nativeBtn.addEventListener('click', function() {
        navigator.share({ title: SITE_NAME, text: SHARE_TEXT, url: SHARE_URL }).catch(function(){});
      });
      shareRow.insertBefore(nativeBtn, shareRow.firstChild);
    }

    wrap.appendChild(shareRow);
    return wrap;
  }

  function openShareMenu() {
    var row = document.getElementById('ce-share-row');
    if (row) {
      var isOpen = row.style.display === 'flex';
      row.style.display = isOpen ? 'none' : 'flex';
    }
  }

  // Inject share block into conclusion screen
  function injectShareBlock() {
    var conclusionScreen = document.getElementById('s-conclusion');
    if (!conclusionScreen) return;
    if (document.getElementById('ce-share-block')) return;

    var actions = conclusionScreen.querySelector('.concl-actions');
    if (!actions) return;
    actions.parentNode.insertBefore(buildShareBlock(), actions);
  }

  // Add badge shimmer keyframe
  var style = document.createElement('style');
  style.textContent = '@keyframes badgeShimmer{0%{transform:translateX(-100%);}100%{transform:translateX(200%);}}';
  document.head.appendChild(style);

  // Hook into goTo
  window.addEventListener('load', function() {
    var orig = window.goTo;
    if (orig) {
      window.goTo = function(id) {
        orig(id);
        if (id === 'conclusion') {
          setTimeout(injectShareBlock, 300);
        }
      };
    }
  });
})();
