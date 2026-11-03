JOURNEY FILES
=============

This folder holds the data for the 14 persona journeys and the intake quiz.
The journeys run outside WordPress templating (no wp_head or wp_footer).
Since 2.9.0 one template builds all 14 from their data; the quiz is still
one HTML file. Shared CSS and JS live in assets/css and assets/js.

FILES
-----
quiz.html                     10-question weighted intake quiz
data/<key>/journey.json       SEO fields, JSON-LD, header label and dots,
                              chapter ids and names, Go deeper links, badge
                              name, resonance switch, Volume II preview
data/<key>/style.css          The rules only that persona uses
data/<key>/screens/<id>.html  One chapter body each, as written
i18n/en.php                   Interface text for the template and journey.js
ms-MY/                        Malay translations (none yet)

The 14 keys: new-atheist, agnostic, secular-humanist, antitheist,
materialist, muslim-doubts, apatheist, scientist, deist, classical-atheist,
ex-believer, spiritual-seeker, freethinker, true-muslim (8 chapters, no
Volume II, no resonance widget, no badge).

ARCHITECTURE
------------
page-journey.php asks ce_journey_source() (inc/ce-journey-template.php) for
the journey and renders it with ce_journey_render(). The parts are in
template-parts/journey/:
- document.php    head (title, meta, JSON-LD from journey.json), the
                  persona style.css, body
- nav.php         fixed header, persona name, one dot per "nav.dots" entry
- screen.php      chapter wrapper; prints screens/<id>.html, or a shared
                  part when the chapter has "template" (v2-preview)
- v2-preview.php  Volume II preview: icon, title, text and links from
                  journey.json, chapter cards from i18n/en.php
- scripts.php     window.PATH and window.CE_JOURNEY, then the journey.js marker

The document carries the markers <!-- ce:journey-css --> and
<!-- ce:journey-js -->, which page-journey.php swaps for assets/css/journey.css
and assets/js/journey.js. journey-base.css goes last in the head.

The New Atheist style.css overrides 16 shared selectors with its own design
(nav logo, hero, quote box, evidence block, two-column cards and others).
See doc/changelog.md, 2.9.0.

To change a chapter, edit its file in screens/. To add a chapter, add the
file, add it to "screens" and "nav.dots" in journey.json, and add the
sidebar steps by hand to the other chapters.

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
There are no Malay translations yet. To add one, copy data/<key>/ to
ms-MY/data/<key>/ and translate journey.json and screens/, and copy
i18n/en.php to i18n/ms-MY.php and translate its values. ms-MY visitors then
get the Malay journey; any key missing from ms-MY.php falls back to English.
A hand-made ms-MY/<key>-journey.html still works where no data folder exists.

QUIZ OPTIONS
------------
Questions 1, 6, 8 and 10 have a seventh option (G) since 2.6.47: arguments met
online (1G), another faith comparing with Islam (6G), comparing religions (8G)
and whether Islam is true (10G). The result screen shows one or two "Read next"
links per persona from READ_NEXT in quiz.html; each must be an existing slug in
inc/articles/batch-*.json or a murtadd.org page linked from those articles.

DO NOT MODIFY
-------------
- assets/js/journey.js (navigation, progress, resonance), from a data file
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

Last updated: v2.9.0
