#!/bin/bash
# ═══════════════════════════════════════════════════════════════
# Download self-hosted fonts for Compelling Evidence
# Run this script in the assets/fonts/ directory
# Requires: curl
# ═══════════════════════════════════════════════════════════════

set -e
echo "Downloading fonts..."

# Playfair Display
curl -sL "https://fonts.gstatic.com/s/playfairdisplay/v37/nuFvD-vYSZviVYUb_rj3ij__anPXJzDwcbmjWBN2PKd3vXDE.woff2" -o playfair-display-400.woff2
curl -sL "https://fonts.gstatic.com/s/playfairdisplay/v37/nuFvD-vYSZviVYUb_rj3ij__anPXJzDwcbmjWBN2PKdFu3DE.woff2" -o playfair-display-700.woff2
curl -sL "https://fonts.gstatic.com/s/playfairdisplay/v37/nuFvD-vYSZviVYUb_rj3ij__anPXJzDwcbmjWBN2PKebunDE.woff2" -o playfair-display-900.woff2
curl -sL "https://fonts.gstatic.com/s/playfairdisplay/v37/nuFRD-vYSZviVYUb_rj3ij__anPXDTnCjmHKM4nYO7KN_qiTbtbK.woff2" -o playfair-display-400-italic.woff2

# DM Sans
curl -sL "https://fonts.gstatic.com/s/dmsans/v15/rP2Yp2ywxg089UriI5-g4vlH9VoD8Cmcqbu6-K6zew.woff2" -o dm-sans-300.woff2
curl -sL "https://fonts.gstatic.com/s/dmsans/v15/rP2Yp2ywxg089UriI5-g4vlH9VoD8CmcqZG6-K6zew.woff2" -o dm-sans-400.woff2
curl -sL "https://fonts.gstatic.com/s/dmsans/v15/rP2Yp2ywxg089UriI5-g4vlH9VoD8CmcqZW6-K6zew.woff2" -o dm-sans-500.woff2
curl -sL "https://fonts.gstatic.com/s/dmsans/v15/rP2Yp2ywxg089UriI5-g4vlH9VoD8Cmcqdm6-K6zew.woff2" -o dm-sans-600.woff2

# Cormorant Garamond
curl -sL "https://fonts.gstatic.com/s/cormorantgaramond/v16/co3YmX5slCNuHLi8bLeY9MK7whWMhyjYqXtK.woff2" -o cormorant-garamond-300.woff2
curl -sL "https://fonts.gstatic.com/s/cormorantgaramond/v16/co3bmX5slCNuHLi8bLeY9MK7whWMhyjornFp.woff2" -o cormorant-garamond-400.woff2
curl -sL "https://fonts.gstatic.com/s/cormorantgaramond/v16/co3YmX5slCNuHLi8bLeY9MK7whWMhyjYrXpK.woff2" -o cormorant-garamond-600.woff2
curl -sL "https://fonts.gstatic.com/s/cormorantgaramond/v16/co3ZmX5slCNuHLi8bLeY9MK7whWMhyjQal_c.woff2" -o cormorant-garamond-300-italic.woff2
curl -sL "https://fonts.gstatic.com/s/cormorantgaramond/v16/co3dmX5slCNuHLi8bLeY9MK7whWMhyjYrEPj.woff2" -o cormorant-garamond-400-italic.woff2

# Amiri
curl -sL "https://fonts.gstatic.com/s/amiri/v27/J7aRnpd8CGxBHpUrtLMA7w.woff2" -o amiri-400.woff2
curl -sL "https://fonts.gstatic.com/s/amiri/v27/J7acnpd8CGxBHp2VkZY4xJ9C.woff2" -o amiri-700.woff2
curl -sL "https://fonts.gstatic.com/s/amiri/v27/J7afnpd8CGxBHpUrhLQY67I.woff2" -o amiri-400-italic.woff2

echo ""
echo "✓ All fonts downloaded. $(ls *.woff2 | wc -l) files:"
ls -la *.woff2
