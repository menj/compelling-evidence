/**
 * CE Theme: persona quiz logic, loaded by page-quiz.php after the data that
 * journeys/quiz.html sets inline (QUESTIONS, ORDER, SCORING, PERSONAS,
 * READ_NEXT, TIE_ORDER) and the murtadd link data (CE_MURTADD).
 *
 * Three modes: quick (5), standard (10), full (15). Answers are keyed by
 * question id and stored as arrays of option values; "skip" means the visitor
 * chose "Not sure, or rather not say", and a skipped question is left out of
 * the scores and of the normalisation. Scores are divided by the weight of the
 * questions answered, so a 5-question result and a 15-question result sit on
 * the same scale.
 *
 * Only quiz_start (mode), quiz_complete (persona, mode) and quiz_abandon go to
 * analytics. Answers never leave the browser.
 *
 * @since 2.8.6
 * @since 2.10.0 Modes, skip option, native inputs, murtadd follow-up.
 * @since 2.18.15 Follow-up answers pick articles on this site (READ_W); safety note.
 * @since 2.18.17 The follow-up supplies two articles at most, so the path's own reading still shows.
 */
(function (root) {
  'use strict';

  var SKIP = 'skip';
  var STORE = 'ce_quiz_v' + root.QUIZ_VERSION;
  var REF_WEIGHT = 0;

  /* ---------- scoring (pure; the simulation in bin/ runs this) ---------- */

  function refWeight() {
    if (!REF_WEIGHT) {
      root.ORDER.standard.forEach(function (id) { REF_WEIGHT += root.QUESTION_WEIGHTS[id] || 1; });
    }
    return REF_WEIGHT;
  }

  function calcScores(answers) {
    var P = root.PERSONAS, scores = {}, used = 0;
    Object.keys(P).forEach(function (p) { scores[p] = 0; });
    var core = root.CORE_QUESTIONS;

    Object.keys(answers).forEach(function (id) {
      if (id === 'w') return;
      var sel = (answers[id] || []).filter(function (v) { return v !== SKIP; });
      if (!sel.length) return;
      var n = sel.length, qn = parseInt(id, 10);
      var isCore = core.indexOf(qn) >= 0;
      var div = n === 1 ? 1 : (isCore ? (n === 2 ? 2.2 : 3.35) : (n === 2 ? 2.35 : 3.65));
      var qw = root.QUESTION_WEIGHTS[id] || 1;
      used += qw;
      sel.forEach(function (v) {
        var w = root.SCORING[id + v] || {};
        Object.keys(w).forEach(function (p) { scores[p] += (w[p] * qw) / div; });
      });
    });
    if (used > 0) {
      var k = refWeight() / used;
      Object.keys(scores).forEach(function (p) { scores[p] *= k; });
    }

    function has(q, v) {
      var a = answers[q] || [];
      if (a.indexOf(v) >= 0) return true;
      return q === 6 && (v === 'D' || v === 'F') && a.indexOf('DF') >= 0;
    }
    function answered(q) { var a = answers[q] || []; return a.length && a[0] !== SKIP; }
    function boost(p, a) { scores[p] += a; }
    function dampen(p, f) { scores[p] *= f; }

    // Muslim cluster
    if (has(6, 'A') && (has(8, 'B') || has(9, 'C'))) boost('true-muslim', 5);
    if (has(6, 'A') || has(6, 'E')) boost('true-muslim', 3);
    if (has(6, 'E') && (has(8, 'A') || has(10, 'D'))) boost('muslim-doubts', 5);
    if ((has(1, 'C') || has(1, 'G')) && has(6, 'E')) boost('muslim-doubts', 3);
    if (has(6, 'E') && has(10, 'G')) boost('muslim-doubts', 2);
    if (has(6, 'E')) boost('muslim-doubts', 2);

    // Another faith, comparing with Islam
    if (has(6, 'G') && (has(8, 'G') || has(10, 'G'))) boost('spiritual-seeker', 3);

    // Ex-believer: boosted only when faith "used to matter" (6C)
    if (has(6, 'C')) boost('ex-believer', 2);
    if (has(6, 'C') && (has(4, 'A') || has(4, 'C'))) boost('ex-believer', 4);
    if (has(6, 'C') && has(8, 'E')) boost('ex-believer', 3);

    // Antitheist
    if (has(2, 'E') && has(4, 'A')) boost('antitheist', 4);
    if (has(1, 'E') && has(6, 'C')) boost('antitheist', 2);
    if (has(6, 'C') && has(5, 'B') && has(3, 'F')) boost('antitheist', 11);

    // New atheist
    if (has(4, 'B') && has(5, 'B') && has(6, 'D')) boost('new-atheist', 3);
    if (has(5, 'B') && has(6, 'D')) boost('new-atheist', 2);

    // Classical atheist
    if (has(2, 'A') && (has(3, 'A') || has(9, 'D'))) boost('classical-atheist', 3);
    if (has(3, 'A') && has(6, 'D')) boost('classical-atheist', 4);

    // Materialist and scientist
    if (has(5, 'A') && has(4, 'E')) boost('materialist', 3);
    if (has(3, 'B') && has(2, 'A') && has(9, 'B')) boost('materialist', 2);
    if (has(15, 'C') && has(13, 'C')) boost('materialist', 2);
    if (has(3, 'B') && has(7, 'E') && has(6, 'D')) boost('scientist', 3);
    if (has(10, 'E') && has(5, 'A')) boost('scientist', 2);
    if (has(11, 'B') && has(15, 'B')) boost('scientist', 2);

    // Freethinker and agnostic
    if (has(3, 'D') && has(7, 'C')) boost('freethinker', 3);
    if (has(1, 'B') && has(9, 'A')) boost('freethinker', 2);
    if (has(3, 'D') && has(8, 'C')) boost('freethinker', 2);
    if (has(14, 'C') && has(11, 'A')) boost('freethinker', 2);
    if (has(2, 'B') && has(4, 'D') && has(5, 'E')) boost('agnostic', 4);
    if (has(3, 'D') && has(10, 'B')) boost('agnostic', 2);
    if (has(11, 'E') && has(15, 'B')) boost('agnostic', 2);

    // Deist and seeker
    if (has(2, 'C') && has(5, 'E')) boost('deist', 3);
    if (has(4, 'F') && has(6, 'B')) boost('deist', 2);
    if (has(2, 'C') && has(5, 'F')) boost('spiritual-seeker', 3);
    if (has(3, 'E') && has(9, 'F')) boost('spiritual-seeker', 3);

    // Apatheist and secular humanist
    if (has(2, 'D') && has(4, 'D') && has(9, 'B')) boost('apatheist', 4);
    if (has(15, 'D') && has(12, 'D')) boost('apatheist', 2);
    if (has(5, 'C') && has(3, 'F') && has(10, 'F')) boost('secular-humanist', 4);
    if (has(4, 'D') && has(5, 'C') && has(6, 'D')) boost('secular-humanist', 2);
    if (has(13, 'A') && has(5, 'C')) boost('secular-humanist', 2);

    // Conflict dampening. Q6 is single choice, so these never stack.
    if (has(6, 'A') || has(6, 'E')) {
      Object.keys(P).forEach(function (p) { if (p !== 'true-muslim' && p !== 'muslim-doubts') dampen(p, 0.25); });
    }
    if (has(6, 'D') || has(6, 'F')) { dampen('true-muslim', 0.15); dampen('muslim-doubts', 0.2); dampen('ex-believer', 0.3); }
    if (has(6, 'C')) dampen('true-muslim', 0.1);
    if (has(6, 'G')) { dampen('true-muslim', 0.3); dampen('muslim-doubts', 0.3); dampen('ex-believer', 0.3); }
    if (has(6, 'B')) dampen('ex-believer', 0.5);

    return scores;
  }

  /** Personas sorted by score; ties follow TIE_ORDER. */
  function rank(scores) {
    var t = root.TIE_ORDER;
    return Object.keys(scores).sort(function (a, b) {
      var d = scores[b] - scores[a];
      if (Math.abs(d) > 1e-9) return d;
      return t.indexOf(a) - t.indexOf(b);
    });
  }

  /** The murtadd entries to show, from the follow-up and other answers. */
  function murtaddPicks(persona, answers) {
    var M = root.CE_MURTADD;
    if (!M || !M.items || M.personas.indexOf(persona) < 0) return [];
    var keys = [], w = (answers.w || [])[0];
    function add(list) { (list || []).forEach(function (k) { if (keys.indexOf(k) < 0 && M.items[k]) keys.push(k); }); }
    if (persona === 'true-muslim') add(M.picks['true-muslim']);
    else {
      if (w && w !== SKIP) add(M.picks[w]);
      if ((answers[1] || []).indexOf('G') >= 0) add(M.picks.online.slice(0, 1));
      add(M.picks['default-' + persona]);
    }
    return keys.slice(0, 3).map(function (k) { return { url: M.base + M.items[k].path, title: M.items[k].title }; });
  }

  /**
   * The "Read next" list: articles picked by the follow-up answer come first
   * (READ_W, two at most, so one slot is left for the path), then the path's own reading (READ_NEXT). No repeats, three in all.
   */
  function readNext(persona, answers) {
    var w = (answers.w || [])[0], out = [], seen = {};
    function add(list) {
      (list || []).forEach(function (l) { if (!seen[l[0]]) { seen[l[0]] = 1; out.push(l); } });
    }
    if (w && w !== SKIP && root.READ_W) add((root.READ_W[w] || []).slice(0, 2));
    add((root.READ_NEXT || {})[persona]);
    return out.slice(0, 3);
  }

  /** True when at least one main question has an answer other than skip. */
  function hasUsable(answers) {
    return Object.keys(answers).some(function (id) {
      return id !== 'w' && (answers[id] || []).some(function (v) { return v !== SKIP; });
    });
  }

  root.CEQuiz = { calcScores: calcScores, rank: rank, murtaddPicks: murtaddPicks, readNext: readNext, hasUsable: hasUsable, SKIP: SKIP };
  if (typeof document === 'undefined') return;

  /* ---------- page ---------- */

  var $ = function (id) { return document.getElementById(id); };
  // cont: a Quick result carried on into Standard. The Quick questions come
  // first, in their Quick order, and the rest of Standard follows, so no
  // question is asked twice.
  var state = { mode: 'standard', answers: {}, pos: 0, seq: [], done: false, cont: false };
  var fromMurtadd = /(?:^|[?&])from=murtadd(?:&|$)/.test(location.search);
  var COUNT_WORD = { 5: 'Five', 10: 'Ten', 15: 'Fifteen' };

  function track(t, d) { if (root.ceTrack) { try { root.ceTrack(t, d); } catch (e) {} } }
  function save() { try { localStorage.setItem(STORE, JSON.stringify({ mode: state.mode, answers: state.answers, pos: state.pos, cont: state.cont })); } catch (e) {} }
  function clearSaved() { try { localStorage.removeItem(STORE); } catch (e) {} }

  function leader() { return rank(calcScores(state.answers))[0]; }

  /** The main question ids for the current mode, in the order they are asked. */
  function mainSequence() {
    if (state.mode === 'standard' && state.cont) {
      var quick = root.ORDER.quick;
      return quick.concat(root.ORDER.standard.filter(function (id) { return quick.indexOf(id) < 0; }));
    }
    return root.ORDER[state.mode].slice();
  }

  function leaderWantsFollowUp() { var l = leader(); return l === 'muslim-doubts' || l === 'ex-believer'; }

  /**
   * The question ids for the current mode, with the follow-up when it applies.
   * Eligibility is worked out again on every call: when a revised answer moves
   * the leader away from the two paths, the follow-up and its answer go.
   */
  function sequence() {
    var base = mainSequence();
    if (state.mode !== 'quick') {
      var a = state.answers[base[base.length - 1]];
      if (((a && a.length) || state.answers.w) && leaderWantsFollowUp()) base.push('w');
      else delete state.answers.w;
    }
    return base;
  }

  function modeCount(m) { return root.ORDER[m].length; }

  function updateIntro() {
    var n = modeCount(state.mode);
    $('qi-sub').textContent = COUNT_WORD[n] + ' quick questions. No right answers. Pick what is closest and skip anything you would rather not say.';
    $('mode-note').textContent = n + ' questions in this mode. Your progress is kept in this browser until you finish or restart.';
  }

  function el(tag, cls, text) { var e = document.createElement(tag); if (cls) e.className = cls; if (text != null) e.textContent = text; return e; }

  function render(focus) {
    state.seq = sequence();
    if (state.pos > state.seq.length - 1) state.pos = state.seq.length - 1;
    var id = String(state.seq[state.pos]);
    var q = root.QUESTIONS[id];
    var total = state.seq.length;
    var multi = !q.single;
    var form = $('q-form');
    form.textContent = '';

    var fs = el('fieldset', 'q-block');
    var lg = el('legend', 'q-legend');
    var meta = el('span', 'q-meta');
    meta.appendChild(el('span', 'q-topic', q.topic));
    lg.appendChild(meta);
    var h = el('h2', 'q-text', q.text); h.id = 'q-heading'; h.tabIndex = -1;
    lg.appendChild(h);
    fs.appendChild(lg);
    var hint = el('p', 'q-hint', q.hint + (multi ? ' Choose up to ' + root.MAX_SELECTIONS + '.' : ''));
    hint.id = 'q-hint';
    fs.setAttribute('aria-describedby', 'q-hint');
    fs.appendChild(hint);

    var list = el('div', 'q-options');
    var cur = state.answers[id] || [];
    var opts = q.options.concat([{ v: SKIP, text: 'Not sure, or rather not say', sub: '', skip: true }]);
    opts.forEach(function (o, i) {
      var lab = el('label', 'q-option' + (o.skip ? ' q-skip' : ''));
      var inp = document.createElement('input');
      inp.type = multi && !o.skip ? 'checkbox' : 'radio';
      if (multi && o.skip) inp.type = 'checkbox';
      inp.name = 'q' + id; inp.value = o.v; inp.id = 'q' + id + '-' + i;
      inp.checked = cur.indexOf(o.v) >= 0;
      lab.htmlFor = inp.id;
      lab.appendChild(inp);
      var txt = el('span', 'opt-body');
      txt.appendChild(el('span', 'opt-text', o.text));
      if (o.sub) txt.appendChild(el('span', 'opt-sub', o.sub));
      lab.appendChild(txt);
      list.appendChild(lab);
    });
    fs.appendChild(list);

    var limit = el('p', 'q-limit'); limit.id = 'q-limit'; limit.setAttribute('aria-live', 'polite');
    fs.appendChild(limit);
    form.appendChild(fs);

    var nav = el('div', 'q-nav');
    var back = el('button', 'btn-prev', 'Back'); back.type = 'button';
    if (state.pos === 0) back.hidden = true;
    back.addEventListener('click', function () { if (state.pos > 0) { state.pos--; save(); render(true); } });
    var isLast = state.pos === total - 1;
    var next = el('button', 'btn-next', isLast ? 'See my path' : 'Next'); next.type = 'submit'; next.id = 'btn-next';
    next.disabled = !cur.length;
    nav.appendChild(back); nav.appendChild(next);
    form.appendChild(nav);

    $('qp-fill').style.width = (state.pos / total * 100) + '%';
    $('qp-label').textContent = 'Question ' + (state.pos + 1) + ' of ' + total;
    if (focus) h.focus();
  }

  function onChange(e) {
    var inp = e.target;
    if (!inp || inp.tagName !== 'INPUT' || !inp.name || inp.name.charAt(0) !== 'q') return;
    var id = inp.name.slice(1);
    var boxes = Array.prototype.slice.call(document.querySelectorAll('#q-form input[name="' + inp.name + '"]'));
    if (inp.type === 'checkbox' && inp.checked) {
      if (inp.value === SKIP) boxes.forEach(function (b) { if (b !== inp) b.checked = false; });
      else {
        boxes.forEach(function (b) { if (b.value === SKIP) b.checked = false; });
        var on = boxes.filter(function (b) { return b.checked; });
        if (on.length > root.MAX_SELECTIONS) {
          inp.checked = false;
          $('q-limit').textContent = 'You can choose up to ' + root.MAX_SELECTIONS + '. Clear one to pick this.';
        } else $('q-limit').textContent = '';
      }
    }
    var vals = boxes.filter(function (b) { return b.checked; }).map(function (b) { return b.value; });
    if (vals.length) state.answers[id] = vals; else delete state.answers[id];
    var next = $('btn-next');
    next.disabled = !vals.length;
    state.seq = sequence();
    next.textContent = state.pos === state.seq.length - 1 ? 'See my path' : 'Next';
    save();
  }

  function onSubmit(e) {
    e.preventDefault();
    var id = String(state.seq[state.pos]);
    if (!state.answers[id] || !state.answers[id].length) return;
    state.seq = sequence();
    if (state.pos < state.seq.length - 1) { state.pos++; save(); render(true); }
    else showResult();
  }

  function begin(mode, keep) {
    state.mode = mode;
    if (!keep) { state.answers = {}; state.pos = 0; state.cont = false; }
    state.done = false;
    $('intro-screen').hidden = true;
    $('result-screen').hidden = true;
    $('quiz-run').hidden = false;
    render(true);
    save();
  }

  function li(href, text, ext) {
    var item = el('li'), a = el('a', 'res-link', text);
    a.href = href;
    if (ext) { a.target = '_blank'; a.rel = 'noopener'; }
    item.appendChild(a);
    return item;
  }

  function fill(blockId, listId, items) {
    var list = $(listId); list.textContent = '';
    items.forEach(function (x) { list.appendChild(x); });
    $(blockId).hidden = !items.length;
  }

  /** Every question was skipped: no path to suggest, so point to the overview. */
  function showEmpty() {
    state.done = true;
    clearSaved();
    track('quiz_complete', { persona: 'none', mode: state.mode });
    $('quiz-run').hidden = true;
    $('result-screen').hidden = false;
    $('res-eyebrow').textContent = 'No path yet';
    $('res-path').textContent = 'Nothing to match yet';
    $('res-desc').textContent = 'You skipped every question, and that is fine. Browse all 14 paths and start from the one that reads closest, or answer a few questions and we will suggest one.';
    var open = $('res-open');
    open.textContent = 'Browse all paths';
    open.href = $('res-journeys').href;
    ['res-also', 'res-read', 'res-murtadd', 'res-safety'].forEach(function (b) { $(b).hidden = true; });
    var more = $('res-more');
    more.hidden = false;
    more.textContent = 'Answer a few questions';
    more.dataset.action = 'answer';
    $('res-path').focus();
  }

  function showResult() {
    if (!hasUsable(state.answers)) { showEmpty(); return; }
    var scores = calcScores(state.answers);
    var order = rank(scores);
    var primary = order[0], P = root.PERSONAS;
    var also = order.slice(1, 3).filter(function (p) { return scores[p] > 0; });
    state.done = true;
    try {
      localStorage.setItem('ce_primary_path', primary);
      localStorage.setItem('ce_secondary_paths', JSON.stringify(also));
      localStorage.setItem('ce_quiz_scores', JSON.stringify(scores));
      localStorage.setItem('ce_quiz_taken', '1');
    } catch (e) {}
    clearSaved();
    track('quiz_complete', { persona: primary, mode: state.mode });

    $('quiz-run').hidden = true;
    $('result-screen').hidden = false;
    $('res-eyebrow').textContent = 'Closest path';
    $('res-open').textContent = 'Open this path';
    $('res-path').textContent = P[primary].name;
    $('res-desc').textContent = P[primary].desc;
    $('res-open').href = P[primary].file;
    fill('res-also', 'res-also-items', also.map(function (p) { return li(P[p].file, P[p].name); }));
    fill('res-read', 'res-read-items', readNext(primary, state.answers).map(function (l) { return li(l[0], l[1], /^https?:/.test(l[0])); }));
    fill('res-murtadd', 'res-murtadd-items', murtaddPicks(primary, state.answers).map(function (m) { return li(m.url, m.title, true); }));
    $('res-safety').hidden = (state.answers.w || [])[0] !== 'home';
    var more = $('res-more');
    more.hidden = state.mode !== 'quick';
    more.dataset.action = 'continue';
    if (state.mode === 'quick') {
      var left = root.ORDER.standard.filter(function (id) { return !state.answers[id]; }).length;
      if (leaderWantsFollowUp()) left++;
      more.textContent = 'Answer ' + left + ' more question' + (left === 1 ? '' : 's') + ' to sharpen this';
    }
    $('res-path').focus();
  }

  function continueStandard() {
    state.mode = 'standard';
    state.cont = true;
    var seq = mainSequence(), first = 0;
    while (first < seq.length - 1 && state.answers[seq[first]]) first++;
    state.pos = first;
    track('quiz_start', { mode: 'standard' });
    begin('standard', true);
  }

  /** "Answer a few questions" after an all-skipped result: the same mode from the start. */
  function answerAgain() {
    track('quiz_start', { mode: state.mode });
    begin(state.mode, false);
  }

  function retake() {
    clearSaved();
    state.answers = {}; state.pos = 0; state.done = false; state.cont = false;
    try { ['ce_quiz_taken', 'ce_primary_path', 'ce_secondary_paths', 'ce_quiz_scores'].forEach(function (k) { localStorage.removeItem(k); }); } catch (e) {}
    $('result-screen').hidden = true;
    $('quiz-run').hidden = true;
    $('intro-screen').hidden = false;
    $('qi-previous').hidden = true;
    $('btn-resume').hidden = true;
    $('btn-start').focus();
  }

  function share() {
    var url = location.origin + location.pathname;
    var status = $('res-status');
    if (navigator.share) { navigator.share({ title: document.title, url: url }).catch(function () {}); return; }
    if (navigator.clipboard) navigator.clipboard.writeText(url).then(function () { status.textContent = 'Link copied.'; }, function () { status.textContent = url; });
    else status.textContent = url;
  }

  function init() {
    // Progress from the 10-question quiz (2.9 and earlier) uses another format: drop it.
    try { localStorage.removeItem('ce_quiz_progress'); localStorage.removeItem('ce_quiz_current_q'); } catch (e) {}

    if (fromMurtadd) { state.mode = 'quick'; $('qi-welcome').hidden = false; }
    var saved = null;
    try { saved = JSON.parse(localStorage.getItem(STORE) || 'null'); } catch (e) {}
    if (saved && root.ORDER[saved.mode] && saved.answers && typeof saved.answers === 'object' && Object.keys(saved.answers).length) {
      var r = $('btn-resume');
      r.hidden = false;
      r.textContent = 'Resume where you left off';
      r.addEventListener('click', function () {
        state.answers = saved.answers;
        state.cont = saved.mode === 'standard' && saved.cont === true;
        state.pos = Math.max(0, Math.min(parseInt(saved.pos, 10) || 0, root.ORDER[saved.mode].length));
        begin(saved.mode, true);
      });
    }
    try {
      var prev = localStorage.getItem('ce_primary_path');
      if (prev && localStorage.getItem('ce_quiz_taken') && root.PERSONAS[prev]) {
        var p = $('qi-previous');
        p.textContent = 'Last time your closest path was ';
        var a = el('a', '', root.PERSONAS[prev].name); a.href = root.PERSONAS[prev].file;
        p.appendChild(a); p.appendChild(document.createTextNode('.'));
        p.hidden = false;
      }
    } catch (e) {}

    var radios = document.querySelectorAll('input[name="mode"]');
    Array.prototype.forEach.call(radios, function (r) {
      r.checked = r.value === state.mode;
      r.addEventListener('change', function () { if (r.checked) { state.mode = r.value; updateIntro(); } });
    });
    updateIntro();

    $('btn-start').addEventListener('click', function () {
      clearSaved();
      track('quiz_start', { mode: state.mode });
      begin(state.mode, false);
    });
    $('q-form').addEventListener('change', onChange);
    $('q-form').addEventListener('submit', onSubmit);
    $('res-more').addEventListener('click', function () {
      if ($('res-more').dataset.action === 'answer') answerAgain(); else continueStandard();
    });
    $('res-retake').addEventListener('click', retake);
    $('res-share').addEventListener('click', share);

    root.addEventListener('pagehide', function () {
      var n = Object.keys(state.answers).length;
      if (n && !state.done) track('quiz_abandon', { last_question: state.pos + 1, answers_given: n, mode: state.mode });
    });
  }

  init();
})(typeof window !== 'undefined' ? window : globalThis);
