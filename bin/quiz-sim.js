#!/usr/bin/env node
/**
 * Simulate the persona quiz: random visitors answer every question, and the
 * script reports how the results spread in each mode.
 *
 * Usage: node bin/quiz-sim.js [runs=500000] [--pick-quick]
 *   --pick-quick  also try every 5-question subset of the gentle questions
 *                 and print the one whose result agrees most with Standard.
 *
 * Each visitor skips a question 10% of the time; on a multi-select question
 * they pick 1 option 70%, 2 options 20% and 3 options 10% of the time.
 */
const fs = require('fs');
const path = require('path');
const root = path.join(__dirname, '..');
const html = fs.readFileSync(path.join(root, 'journeys/quiz.html'), 'utf8');
const data = html.match(/<script>\s*\/\* Quiz data[\s\S]*?<\/script>/)[0].replace(/<\/?script>/g, '');
globalThis.CE_MURTADD = JSON.parse(fs.readFileSync(path.join(root, 'inc/data/quiz-murtadd-links.json'), 'utf8'));
(0, eval)(data.replace(/\bvar (\w+) =/g, 'globalThis.$1 ='));
(0, eval)(fs.readFileSync(path.join(root, 'assets/js/quiz.js'), 'utf8'));
const Q = globalThis.CEQuiz, G = globalThis;
const N = parseInt(process.argv[2], 10) || 500000;
const P = Object.keys(G.PERSONAS);
let seed = 20261103;
const rnd = () => { seed = (seed * 1103515245 + 12345) % 2147483648; return seed / 2147483648; };
const ids = G.ORDER.full.map(String);

function visitor() {
  const a = {};
  ids.forEach(id => {
    const q = G.QUESTIONS[id], o = q.options.map(x => x.v);
    if (rnd() < 0.1) { a[id] = ['skip']; return; }
    const r = rnd(), k = q.single ? 1 : (r < 0.7 ? 1 : r < 0.9 ? 2 : 3);
    const s = [];
    while (s.length < k) { const v = o[Math.floor(rnd() * o.length)]; if (!s.includes(v)) s.push(v); }
    a[id] = s;
  });
  return a;
}
const subset = (a, list) => { const o = {}; list.forEach(id => { if (a[id]) o[id] = a[id]; }); return o; };
const result = a => Q.rank(Q.calcScores(a))[0];

const visitors = [];
for (let i = 0; i < N; i++) visitors.push(visitor());

if (process.argv.includes('--pick-quick')) {
  const gentle = [8, 10, 3, 5, 9, 7, 6];
  const std = visitors.slice(0, 100000).map(a => result(subset(a, G.ORDER.standard)));
  const combos = [];
  (function walk(start, pick) {
    if (pick.length === 5) { combos.push(pick.slice()); return; }
    for (let i = start; i < gentle.length; i++) { pick.push(gentle[i]); walk(i + 1, pick); pick.pop(); }
  })(0, []);
  const scored = combos.map(c => {
    let agree = 0; const seen = new Set();
    visitors.slice(0, 100000).forEach((a, i) => { const r = result(subset(a, c)); seen.add(r); if (r === std[i]) agree++; });
    return { c, agree: agree / 1000, reach: seen.size };
  }).sort((x, y) => y.agree - x.agree);
  console.log('Quick candidates (agreement with Standard, personas reached):');
  scored.slice(0, 6).forEach(s => console.log('  ' + JSON.stringify(s.c) + ' ' + s.agree.toFixed(1) + '% ' + s.reach + '/14'));
}

const modes = { quick: G.ORDER.quick, standard: G.ORDER.standard, full: G.ORDER.full };
const res = {};
Object.entries(modes).forEach(([m, list]) => {
  const c = {}; let mus = 0, musEx = 0;
  res[m] = visitors.map(a => {
    const r = result(subset(a, list));
    c[r] = (c[r] || 0) + 1;
    const q6 = a['6'] || [];
    if (list.includes(6) && (q6[0] === 'A' || q6[0] === 'E')) { mus++; if (r === 'ex-believer') musEx++; }
    return r;
  });
  const all = P.map(p => [p, c[p] || 0]).sort((x, y) => y[1] - x[1]);
  console.log('\n' + m + ' (' + list.length + ' questions, ' + N + ' runs)');
  console.log('  ' + all.map(([k, v]) => k + ' ' + (100 * v / N).toFixed(1) + '%').join(', '));
  console.log('  Ex-Believer among visitors who chose a Muslim answer on Q6: ' + (mus ? (100 * musEx / mus).toFixed(2) + '% of ' + mus : 'n/a'));
  const none = all.filter(x => !x[1]).map(x => x[0]);
  console.log('  personas never reached: ' + (none.join(', ') || 'none'));
});
const agree = (x, y) => (100 * res[x].filter((r, i) => r === res[y][i]).length / N).toFixed(1) + '%';
console.log('\nAgreement Quick vs Standard: ' + agree('quick', 'standard'));
console.log('Agreement Full vs Standard: ' + agree('full', 'standard'));

// Ideal answers: for each persona and mode, pick each question's best option for it.
const miss = [];
Object.entries(modes).forEach(([m, list]) => P.forEach(p => {
  const a = {};
  list.forEach(id => {
    let best = null, bw = -1;
    G.QUESTIONS[id].options.forEach(o => { const w = (G.SCORING[id + o.v] || {})[p] || 0; if (w > bw) { bw = w; best = o.v; } });
    a[id] = [best];
  });
  if (result(a) !== p) miss.push(m + ':' + p + '->' + result(a));
}));
console.log('Ideal-answer check (persona reachable by its own best answers): ' + (miss.length ? miss.join(', ') : 'all 14 in every mode'));
