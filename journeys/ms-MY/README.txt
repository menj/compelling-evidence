MALAY (ms-MY) JOURNEY FILES
============================

This folder holds Malay translations of the 14 journeys and the quiz.

Since 2.9.0 a journey is a data folder. Copy journeys/data/<key>/ to
journeys/ms-MY/data/<key>/, translate the text in journey.json and
screens/*.html, and copy journeys/i18n/en.php to journeys/i18n/ms-MY.php
with the values translated. A hand-made <key>-journey.html in this folder
still works where no data folder exists. The file list below dates from
the HTML journeys; the quiz is still quiz.html.

STATUS (2.6.43): no translations yet. The English copies that sat here, labelled
as Malay, were removed, so every Malay-locale visitor is served the English
files. Add a file here only once it is a real, reviewed Malay translation.

When WordPress is set to Malay (Malaysia), page-journey.php and page-quiz.php
will automatically serve files from this folder instead of the English defaults
in the parent /journeys/ directory.

FILES REQUIRED
--------------
quiz.html
new-atheist-journey.html
agnostic-journey.html
secular-humanist-journey.html
antitheist-journey.html
materialist-journey.html
muslim-doubts-journey.html
apatheist-journey.html
deist-journey.html
scientist-journey.html
classical-atheist-journey.html
ex-believer-journey.html
spiritual-seeker-journey.html
freethinker-journey.html
true-muslim-journey.html

TRANSLATION NOTES
-----------------
- Translate the text in journey.json and screens/*.html. Keep the keys,
  chapter ids, class names and links as they are.
- The JavaScript (assets/js/journey.js) takes its text from i18n/ms-MY.php
  and needs no change. style.css needs no translation.
- Physics chapter labels (Horizon, Singularity, Calibration, Emergence,
  Constant, Entropy, Signal) should be translated consistently across all 14 files.
- Quranic verses: the Arabic text (class="arabic-large") should be kept as-is.
  Translate the English translation line below each verse.
- Persona names in quiz.html PERSONAS object: use the Malay names from ms_MY.po
  (e.g. "Ateis Baharu", "Agnostik", "Muslim Beraguaan" etc.)

CHAPTER LABEL TRANSLATIONS (use consistently across all files)
--------------------------------------------------------------
Horizon      → Ufuk
Singularity  → Ketuggalan
Calibration  → Penentukuran
Emergence    → Kemunculan
Constant     → Tetapan
Entropy      → Entropi
Signal       → Isyarat
Conclusion   → Kesimpulan

FALLBACK BEHAVIOUR
------------------
If a file is missing from this folder, the English version is served automatically.
You can translate files incrementally — start with the highest-traffic paths
(muslim-doubts, agnostic, ex-believer) and add the rest over time.
