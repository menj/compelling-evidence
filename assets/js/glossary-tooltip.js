/**
 * Glossary Tooltip System
 * Shows definition rollovers for Islamic/Arabic terms
 */

(function() {
    'use strict';

    // Create tooltip element
    let tooltip = null;
    let hideTimeout = null;

    function createTooltip() {
        if (tooltip) return tooltip;

        tooltip = document.createElement('div');
        tooltip.className = 'ce-glossary-tooltip';
        tooltip.setAttribute('role', 'tooltip');
        tooltip.innerHTML = `
            <div class="glossary-tooltip-header">
                <span class="glossary-tooltip-term"></span>
                <span class="glossary-tooltip-arabic" lang="ar" dir="rtl"></span>
            </div>
            <div class="glossary-tooltip-definition"></div>
            <div class="glossary-tooltip-footer">
                <span>Click to read more</span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </div>
        `;
        document.body.appendChild(tooltip);
        return tooltip;
    }

    function showTooltip(target) {
        const definition = target.getAttribute('data-definition');
        const term = target.textContent;
        const arabic = target.getAttribute('data-arabic');

        if (!definition) return;

        const tip = createTooltip();
        
        // Set content
        tip.querySelector('.glossary-tooltip-term').textContent = term;
        tip.querySelector('.glossary-tooltip-definition').textContent = definition;
        
        const arabicEl = tip.querySelector('.glossary-tooltip-arabic');
        if (arabic) {
            arabicEl.textContent = arabic;
            arabicEl.style.display = 'inline';
        } else {
            arabicEl.style.display = 'none';
        }

        // Position tooltip
        const rect = target.getBoundingClientRect();
        const scrollY = window.scrollY || window.pageYOffset;
        const scrollX = window.scrollX || window.pageXOffset;

        // Calculate position (centered above the term)
        let top = rect.top + scrollY - tip.offsetHeight - 12;
        let left = rect.left + scrollX + (rect.width / 2) - (tip.offsetWidth / 2);

        // Boundary checks
        const viewportWidth = window.innerWidth;
        const tipWidth = Math.min(320, viewportWidth - 32);
        tip.style.maxWidth = tipWidth + 'px';

        // Prevent going off left edge
        if (left < 16) {
            left = 16;
        }
        // Prevent going off right edge
        if (left + tipWidth > viewportWidth - 16) {
            left = viewportWidth - tipWidth - 16;
        }

        // If too close to top, show below instead
        if (top < scrollY + 16) {
            top = rect.bottom + scrollY + 12;
            tip.classList.add('tooltip-below');
        } else {
            tip.classList.remove('tooltip-below');
        }

        tip.style.top = top + 'px';
        tip.style.left = left + 'px';
        tip.classList.add('visible');

        // Clear hide timeout
        if (hideTimeout) {
            clearTimeout(hideTimeout);
            hideTimeout = null;
        }
    }

    function hideTooltip() {
        if (!tooltip) return;
        
        hideTimeout = setTimeout(() => {
            tooltip.classList.remove('visible');
        }, 150);
    }

    function keepTooltipVisible() {
        if (hideTimeout) {
            clearTimeout(hideTimeout);
            hideTimeout = null;
        }
    }

    // Event delegation for glossary terms
    document.addEventListener('mouseover', function(e) {
        const target = e.target.closest('.ce-glossary-term');
        if (target) {
            showTooltip(target);
        }
    });

    document.addEventListener('mouseout', function(e) {
        const target = e.target.closest('.ce-glossary-term');
        if (target) {
            hideTooltip();
        }
    });

    // Keep tooltip visible when hovering over it
    document.addEventListener('mouseover', function(e) {
        if (e.target.closest('.ce-glossary-tooltip')) {
            keepTooltipVisible();
        }
    });

    document.addEventListener('mouseout', function(e) {
        if (e.target.closest('.ce-glossary-tooltip')) {
            hideTooltip();
        }
    });

    // Hide on scroll or resize
    window.addEventListener('scroll', function() {
        if (tooltip) {
            tooltip.classList.remove('visible');
        }
    }, { passive: true });

    window.addEventListener('resize', function() {
        if (tooltip) {
            tooltip.classList.remove('visible');
        }
    });

    // Touch support for mobile
    document.addEventListener('touchstart', function(e) {
        const target = e.target.closest('.ce-glossary-term');
        const tooltipEl = e.target.closest('.ce-glossary-tooltip');
        
        if (target) {
            e.preventDefault();
            showTooltip(target);
            
            // Auto-hide after 4 seconds on mobile
            setTimeout(() => {
                hideTooltip();
            }, 4000);
        } else if (!tooltipEl && tooltip) {
            tooltip.classList.remove('visible');
        }
    }, { passive: false });

})();
