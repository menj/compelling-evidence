/**
 * CE Theme: persona quiz logic, loaded by page-quiz.php after the data
 * (SCORING, PERSONAS, READ_NEXT) that journeys/quiz.html sets inline.
 *
 * @since 2.8.6
 */
var answers = {};
function startQuiz(){if(window.ceTrack)ceTrack('quiz_start',{});document.getElementById('intro-screen').style.display='none';document.getElementById('progress-bar').style.display='block';document.getElementById('questions-container').style.display='block';showQ(1);}
function showQ(n){document.querySelectorAll('.q-block').forEach(function(b){b.classList.remove('active');});var block=document.getElementById('q'+n);if(block){block.classList.add('active');if(answers[n]){answers[n].forEach(function(v){var opt=block.querySelector('[data-v="'+v+'"]');if(opt)opt.classList.add('selected');});enableNext(n);}}document.getElementById('qp-fill').style.width=((n-1)/10*100)+'%';document.getElementById('qp-label').textContent='Question '+n+' of 10';}
function goNext(n){if(answers[n]&&answers[n].length>0)showQ(n+1);}
function goPrev(n){if(n>1)showQ(n-1);}
function enableNext(n){var btn=document.getElementById('next'+n);if(btn)btn.classList.add('enabled');}
function disableNext(n){var btn=document.getElementById('next'+n);if(btn)btn.classList.remove('enabled');}

document.addEventListener('click',function(e){
  var opt=e.target.closest('.q-option');
  if(!opt)return;
  var q=parseInt(opt.dataset.q,10),v=opt.dataset.v;
  if(!answers[q])answers[q]=[];
  var idx=answers[q].indexOf(v);
  if(idx>=0){answers[q].splice(idx,1);opt.classList.remove('selected');opt.setAttribute('aria-selected','false');}
  else{
    if(answers[q].length>=MAX_SELECTIONS){
      var hint=document.querySelector('#q'+q+' .q-multi-hint');
      if(hint){hint.textContent='Choose up to 3 for the closest result';hint.style.color='rgba(12,212,224,0.78)';clearTimeout(hint._t);hint._t=setTimeout(function(){hint.textContent='Select up to 3 that fit best';hint.style.color='';},1600);}
      return;
    }
    answers[q].push(v);opt.classList.add('selected');opt.setAttribute('aria-selected','true');
  }
  if(answers[q].length>0)enableNext(q);else disableNext(q);if(window.ceTrack)ceTrack('quiz_answer',{question:q,answer:v});try{localStorage.setItem('ce_quiz_progress',JSON.stringify(answers));localStorage.setItem('ce_quiz_current_q',q.toString());}catch(e){}
});

function calcScores(){
  var scores={};Object.keys(PERSONAS).forEach(function(p){scores[p]=0;});
  for(var q=1;q<=10;q++){
    var selected=answers[q]||[];if(!selected.length)continue;
    var count=selected.length;
    var divisor=count===1?1:(CORE_QUESTIONS.has(q)?(count===2?2.2:3.35):(count===2?2.35:3.65));
    var qw=QUESTION_WEIGHTS[q]||1;
    selected.forEach(function(v){var w=SCORING[q+v]||{};Object.keys(w).forEach(function(p){scores[p]+=(w[p]*qw)/divisor;});});
  }

  // Signal boosts
  function has(q,v){return(answers[q]||[]).indexOf(v)>=0;}
  function boost(p,a){scores[p]=(scores[p]||0)+a;}
  function dampen(p,f){scores[p]=(scores[p]||0)*f;}

  // Muslim cluster
  if(has(6,'A')&&(has(8,'B')||has(9,'C')))boost('true-muslim',5);
  if(has(6,'A')||has(6,'E'))boost('true-muslim',3);
  if(has(6,'E')&&(has(8,'A')||has(10,'D')))boost('muslim-doubts',5);
  if((has(1,'C')||has(1,'G'))&&has(6,'E'))boost('muslim-doubts',3);
  if(has(6,'E')&&has(10,'G'))boost('muslim-doubts',2);

  // Another faith, comparing with Islam
  if(has(6,'G')&&(has(8,'G')||has(10,'G')))boost('spiritual-seeker',3);

  // Ex-believer
  if(has(6,'C')&&(has(4,'A')||has(4,'C')))boost('ex-believer',4);
  if(has(8,'E')&&has(6,'C'))boost('ex-believer',3);

  // Antitheist
  if(has(2,'E')&&has(4,'A'))boost('antitheist',4);
  if(has(1,'E')&&has(6,'C'))boost('antitheist',2);

  // New atheist
  if(has(4,'B')&&has(5,'B')&&has(6,'D'))boost('new-atheist',3);

  // Classical atheist
  if(has(2,'A')&&(has(3,'A')||has(9,'D')))boost('classical-atheist',3);

  // Materialist
  if(has(5,'A')&&has(4,'E'))boost('materialist',3);
  if(has(3,'B')&&has(2,'A')&&has(9,'B'))boost('materialist',2);

  // Scientist
  if(has(3,'B')&&has(7,'E')&&has(6,'D'))boost('scientist',3);
  if(has(10,'E')&&has(5,'A'))boost('scientist',2);

  // Freethinker
  if(has(3,'D')&&has(7,'C'))boost('freethinker',3);
  if(has(1,'B')&&has(9,'A'))boost('freethinker',2);

  // Agnostic
  if(has(2,'B')&&has(4,'D')&&has(5,'E'))boost('agnostic',4);
  if(has(3,'D')&&has(10,'B'))boost('agnostic',2);

  // Deist vs Seeker
  if(has(2,'C')&&has(5,'E'))boost('deist',3);
  if(has(4,'F')&&has(6,'B'))boost('deist',2);
  if(has(2,'C')&&has(5,'F'))boost('spiritual-seeker',3);
  if(has(3,'E')&&has(9,'F'))boost('spiritual-seeker',3);

  // Apatheist vs SecHum
  if(has(2,'D')&&has(4,'D')&&has(9,'B'))boost('apatheist',4);
  if(has(5,'C')&&has(3,'F')&&has(10,'F'))boost('secular-humanist',4);
  if(has(4,'D')&&has(5,'C')&&has(6,'D'))boost('secular-humanist',2);

  // Conflict dampening
  if(has(6,'A')||has(6,'E')){Object.keys(PERSONAS).forEach(function(p){if(p!=='true-muslim'&&p!=='muslim-doubts'&&p!=='ex-believer')dampen(p,0.25);});}
  if(has(6,'D')||has(6,'F')){dampen('true-muslim',0.15);dampen('muslim-doubts',0.2);}
  if(has(6,'C')){dampen('true-muslim',0.1);}
  if(has(6,'G')&&!has(6,'A')&&!has(6,'E')){dampen('true-muslim',0.3);dampen('muslim-doubts',0.3);}

  return scores;
}

function showResult(){
  if(!answers[10]||!answers[10].length)return;
  var scores=calcScores();
  var sorted=Object.entries(scores).sort(function(a,b){return b[1]-a[1];});
  var primary=sorted[0][0],maxScore=sorted[0][1];
  var secondaries=sorted.slice(1,5).filter(function(e){return e[1]>0&&e[1]>=maxScore*0.78;}).slice(0,2).map(function(e){return e[0];});
  try{localStorage.setItem('ce_primary_path',primary);localStorage.setItem('ce_secondary_paths',JSON.stringify(secondaries));localStorage.setItem('ce_quiz_scores',JSON.stringify(scores));localStorage.setItem('ce_quiz_taken','1');if(window.ceTrack)ceTrack('quiz_complete',{persona:primary});localStorage.removeItem('ce_quiz_progress');localStorage.removeItem('ce_quiz_current_q');}catch(e){}
  document.getElementById('questions-container').style.display='none';document.getElementById('progress-bar').style.display='none';
  var rs=document.getElementById('result-screen');rs.style.display='block';setTimeout(function(){rs.classList.add('active');},10);
  document.getElementById('res-path').textContent=PERSONAS[primary].name;
  document.getElementById('res-desc').textContent=PERSONAS[primary].desc;
  var second=sorted[1]?sorted[1][0]:null,sScore=sorted[1]?sorted[1][1]:0,ratio=maxScore>0?(sScore/maxScore):0;
  document.getElementById('res-label').textContent=ratio>=0.92?'Closest match, with strong overlap':'Closest match';
  var resNote=document.getElementById('res-note');
  if(second&&ratio>=0.92){resNote.style.display='block';resNote.textContent='Your answers sit close to the boundary between '+PERSONAS[primary].name+' and '+PERSONAS[second].name+'. Start with this path; the adjacent one will also feel familiar.';}
  else if(second&&ratio>=0.84){resNote.style.display='block';resNote.textContent='Your answers cluster around a main path, with noticeable overlap with '+PERSONAS[second].name+'.';}
  else{resNote.style.display='none';}
  var rr=document.getElementById('res-resonances');
  if(secondaries.length>0){rr.style.display='block';document.getElementById('rr-items').innerHTML=secondaries.map(function(p){return'<div class="rr-item">'+PERSONAS[p].name+'</div>';}).join('');}else{rr.style.display='none';}
  var rn=document.getElementById('res-read'),links=READ_NEXT[primary]||[];
  if(links.length){rn.style.display='block';document.getElementById('rn-items').innerHTML=links.map(function(l){var ext=/^https?:/.test(l[0]);return'<a class="rn-item" href="'+l[0]+'"'+(ext?' target="_blank" rel="noopener"':'')+'>'+l[1]+'</a>';}).join('');}else{rn.style.display='none';}
  document.getElementById('sb-rows').innerHTML=sorted.slice(0,6).map(function(e){var p=e[0],s=e[1],pct=maxScore>0?Math.round((s/maxScore)*100):0;return'<div class="sb-row"><div class="sb-name">'+PERSONAS[p].name+'</div><div class="sb-track"><div class="sb-fill'+(p===primary?' primary':'')+'" style="width:'+pct+'%"></div></div></div>';}).join('');
}

function goJourney(){try{var p=localStorage.getItem('ce_primary_path');if(p&&PERSONAS[p]){window.location.href=PERSONAS[p].file;return;}}catch(e){}var path=document.getElementById('res-path').textContent;var m=Object.entries(PERSONAS).find(function(e){return e[1].name===path;});if(m)window.location.href=m[1].file;}
function retake(){answers={};document.querySelectorAll('.q-option').forEach(function(o){o.classList.remove('selected');});document.querySelectorAll('.btn-next').forEach(function(b){b.classList.remove('enabled');});document.getElementById('result-screen').classList.remove('active');document.getElementById('result-screen').style.display='none';document.getElementById('res-note').style.display='none';document.getElementById('intro-screen').style.display='block';document.getElementById('questions-container').style.display='none';document.getElementById('progress-bar').style.display='none';try{localStorage.removeItem('ce_quiz_taken');localStorage.removeItem('ce_primary_path');localStorage.removeItem('ce_secondary_paths');localStorage.removeItem('ce_quiz_scores');localStorage.removeItem('ce_quiz_progress');localStorage.removeItem('ce_quiz_current_q');}catch(e){}}

try{var prev=localStorage.getItem('ce_primary_path');if(prev&&localStorage.getItem('ce_quiz_taken')&&PERSONAS[prev]){var btn=document.querySelector('.btn-start');if(btn){var resume=document.createElement('div');resume.style.cssText='margin-top:1rem;font-size:0.8rem;color:rgba(242,238,255,0.35);';resume.innerHTML='Previously matched: <span style="color:rgba(12,212,224,0.7)">'+PERSONAS[prev].name+'</span>: <a href="'+PERSONAS[prev].file+'" style="color:rgba(12,212,224,0.5);text-decoration:underline;font-size:0.78rem;">go there directly</a>';btn.parentNode.insertBefore(resume,btn.nextSibling);}}}catch(e){}


// Quiz save/resume: restore progress if tab was closed mid-quiz
try{
  var savedProgress=localStorage.getItem('ce_quiz_progress');
  var savedQ=localStorage.getItem('ce_quiz_current_q');
  if(savedProgress&&savedQ&&!localStorage.getItem('ce_quiz_taken')){
    var parsed=JSON.parse(savedProgress);
    if(parsed&&typeof parsed==='object'&&Object.keys(parsed).length>0){
      // Show resume prompt
      var btn=document.querySelector('.btn-start');
      if(btn){
        var resumeDiv=document.createElement('div');
        resumeDiv.style.cssText='margin-top:1rem;';
        resumeDiv.innerHTML='<button onclick="resumeQuiz()" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 1.8rem;border-radius:50px;background:rgba(12,212,224,0.12);border:1px solid rgba(12,212,224,0.3);color:#0cd4e0;font-family:var(--body);font-size:0.88rem;font-weight:600;cursor:pointer;transition:all 0.2s;">Resume from question '+savedQ+' <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg></button>';
        btn.parentNode.insertBefore(resumeDiv,btn.nextSibling.nextSibling||null);
      }
    }
  }
}catch(e){}


// Keyboard accessibility: Enter/Space selects options
document.addEventListener('keydown',function(e){
  if(e.key==='Enter'||e.key===' '){
    var opt=e.target.closest('.q-option');
    if(opt){e.preventDefault();opt.click();}
  }
});

function resumeQuiz(){
  try{
    var saved=JSON.parse(localStorage.getItem('ce_quiz_progress'));
    var savedQ=parseInt(localStorage.getItem('ce_quiz_current_q'),10)||1;
    if(saved){
      answers=saved;
      // Restore visual selections
      Object.keys(answers).forEach(function(q){
        var block=document.getElementById('q'+q);
        if(block&&answers[q]){
          answers[q].forEach(function(v){
            var opt=block.querySelector('[data-v="'+v+'"]');
            if(opt)opt.classList.add('selected');
          });
          enableNext(parseInt(q,10));
        }
      });
      document.getElementById('intro-screen').style.display='none';
      document.getElementById('progress-bar').style.display='block';
      document.getElementById('questions-container').style.display='block';
      showQ(savedQ);
    }
  }catch(e){startQuiz();}
}


// Track quiz abandonment on page unload
window.addEventListener('beforeunload', function() {
  if (Object.keys(answers).length > 0 && !document.getElementById('result-screen').classList.contains('active')) {
    var lastQ = Math.max.apply(null, Object.keys(answers).map(Number));
    if (window.ceTrack) ceTrack('quiz_abandon', {last_question: lastQ, answers_given: Object.keys(answers).length});
  }
});

