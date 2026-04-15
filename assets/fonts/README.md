# Self-Hosted Fonts — Compelling Evidence

All fonts are self-hosted (zero external Google Fonts requests).

## Font inventory (17 woff2 files, 419KB total)

| Font | Weights | Usage | Size |
|---|---|---|---|
| Playfair Display | 400, 700, 900, 400i | Headings, titles | ~22KB each |
| DM Sans | 300, 400, 500, 600 | UI, labels, body | ~14KB each |
| Cormorant Garamond | 300, 400, 600, 300i, 400i | Article reading body | ~22KB each |
| Amiri | 400, 700, 400i | Arabic text fallback (Latin subset) | ~20KB each |
| Uthmani Quran | 400 | Quranic verses (KFGQPC HAFS Uthmanic Script, 608 glyphs) | 107KB |

## Arabic font rendering chain

Quranic text uses this font stack:
1. **Uthmani Quran** — Primary. 608 glyphs covering U+0600-06FF + U+FB50-FDFF.
2. **Amiri** — Fallback. Currently Latin-subset only.
3. **System fonts** — Noto Naskh Arabic (Android/Linux), Traditional Arabic (Windows), Geeza Pro (macOS/iOS).

### Known limitation
Ornate brackets ﴿﴾ (U+FD3E-FD3F) are not in the Uthmani font. They render via system Arabic fonts.
To fix: re-download Amiri with arabic+latin subsets from https://gwfh.mranftl.com/fonts/amiri?subsets=arabic,latin

## Setup
Fonts are bundled in the theme zip. No download script needed.
