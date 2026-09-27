JOURNEY FILES
=============

This folder contains the 14 persona journey HTML files and the intake quiz.
These are self-contained single-page applications that run independently of WordPress
templating (they bypass wp_head/wp_footer for performance).

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
- Embedded CSS (no external dependencies)
- Embedded JavaScript (progress saving, navigation, resonance widgets)
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
Malay translations are in /journeys/ms-MY/ folder.
When WordPress locale is set to ms-MY, page-journey.php serves files from that folder.

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
