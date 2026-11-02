JOURNEY FILES
=============

This folder contains the 14 persona journey HTML files and the intake quiz.
These are single-page applications that run outside WordPress templating (they bypass
wp_head/wp_footer). Their shared CSS and JS live in assets/css and assets/js.

FILES
-----
quiz.html                    - 10-question weighted intake quiz
new-atheist-journey.html     - Journey for convinced atheists
agnostic-journey.html        - Journey for agnostics
secular-humanist-journey.html - Journey for secular humanists
antitheist-journey.html      - Journey for antitheists
materialist-journey.html     - Journey for materialists/physicalists
muslim-doubts-journey.html   - Journey for Muslims carrying doubts
apatheist-journey.html       - Journey for apatheists
scientist-journey.html       - Journey for empiricists
deist-journey.html           - Journey for deists
classical-atheist-journey.html - Journey for classical atheists
ex-believer-journey.html     - Journey for ex-believers
spiritual-seeker-journey.html - Journey for spiritual but not religious
freethinker-journey.html     - Journey for freethinkers
true-muslim-journey.html     - Journey for committed Muslims (8-screen variant)

ARCHITECTURE
------------
Each journey file is a complete HTML document with:
- The styles only that persona uses, in one <style> block
- A window.CE_JOURNEY object: chapter ids and names, the sidebar symbol for the
  conclusion, the "Go deeper" links, and whether the page shows the resonance
  widget and the completion badge
- Markers that page-journey.php swaps for the shared files:
  <!-- ce:journey-css --> for assets/css/journey.css, <!-- ce:journey-js --> for
  assets/js/journey.js (navigation, progress, resonance, go deeper, share).
  The quiz has <!-- ce:quiz-css --> and <!-- ce:quiz-js --> for quiz.css and quiz.js.
- new-atheist-journey.html keeps all its styles inline. Its nav, hero, sidebar and
  cards use other declarations for 22 of the shared selectors.
- 9-15 screens per journey, each with unique content
- Arabic verses with Uthmani script rendering
- Conclusion screen with CTA to explore articles

CONTENT STRUCTURE
-----------------
Typical journey flow:
1. Opening      - Hook statement for the persona
2. Foundations  - 2-3 screens establishing common ground
3. Physics      - 7 screens (Horizon, Singularity, Calibration, Emergence, Constant, Entropy, Signal)
4. Conclusion   - Summary + next steps

JAVASCRIPT API
--------------
goTo(screenId)              - Navigate to specific screen
updateProgress(screenId)    - Save progress to localStorage
openResonance()             - Open resonance widget modal

LOCALISATION
------------
There are no Malay translations yet. The English files in this folder are the
only copies; /journeys/ms-MY/ holds a README only. When a reviewed Malay file
is added there, functions.php serves it to ms-MY visitors.

QUIZ OPTIONS
------------
Questions 1, 6, 8 and 10 have a seventh option (G) since 2.6.47: arguments met
online (1G), another faith comparing with Islam (6G), comparing religions (8G)
and whether Islam is true (10G). The result screen shows one or two "Read next"
links per persona from READ_NEXT in quiz.html; each must be an existing slug in
inc/articles/batch-*.json or a murtadd.org page linked from those articles.

DO NOT MODIFY
-------------
- JavaScript logic blocks (navigation, progress, resonance)
- CSS variable names (used by theme injection)
- Arabic verse text (class="arabic-large")
- Persona routing in quiz.html PERSONAS object keys

CHAPTER LABELS (use consistently across all files)
--------------------------------------------------
Horizon      - Introduction to cosmological argument
Singularity  - Big Bang / universe beginning
Calibration  - Fine-tuning of constants
Emergence    - Consciousness from matter
Constant     - Mathematical constants
Entropy      - Second law of thermodynamics
Signal       - Information / design detection
Conclusion   - Summary and call to action

UPDATES
-------
These files are not synced by ce-content-sync.php.
Manual edits here persist across theme updates.

Last updated: v2.3.0
