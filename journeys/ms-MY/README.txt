MALAY (ms-MY) JOURNEY FILES
============================

This folder holds Malay translations of the 12 journey HTML files and quiz.

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

TRANSLATION NOTES
-----------------
- Each file is a self-contained HTML application. Copy the English source file
  and translate all visible text within the HTML.
- The JavaScript logic (goTo, progress saving, resonance widgets) requires no
  translation. Do not modify any <script> blocks.
- The CSS embedded in <style> blocks requires no translation.
- Physics chapter labels (Horizon, Singularity, Calibration, Emergence,
  Constant, Entropy, Signal) should be translated consistently across all 12 files.
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
