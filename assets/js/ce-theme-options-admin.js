/**
 * CE Theme Options: tabs, live slider values, colour pickers, confirmations.
 *
 * Loaded only on Appearance > CE Theme Options. Uses core jQuery and
 * wp-color-picker. Defines no globals. Without JavaScript the tabs are
 * plain links (…&tab=<key>) and each tab still saves on its own.
 *
 * @since 2.15.0
 */
(function ($) {
	'use strict';

	var root = document.querySelector('.ce-theme-opts');
	if (!root) {
		return;
	}

	// ── Tabs: WAI-ARIA tab pattern with automatic activation ────────────
	var tabs = Array.prototype.slice.call(root.querySelectorAll('[role="tab"]'));

	function panelFor(tab) {
		return document.getElementById(tab.getAttribute('aria-controls'));
	}

	function activate(tab, moveFocus) {
		tabs.forEach(function (t) {
			var on = t === tab;
			var panel = panelFor(t);
			t.setAttribute('aria-selected', on ? 'true' : 'false');
			t.tabIndex = on ? 0 : -1;
			t.classList.toggle('is-active', on);
			if (panel) {
				panel.hidden = !on;
			}
		});
		if (moveFocus) {
			tab.focus();
		}
		if (tab.scrollIntoView) {
			tab.scrollIntoView({ block: 'nearest', inline: 'nearest' });
		}
		if (window.history && window.history.replaceState && window.URL) {
			var url = new URL(window.location.href);
			url.searchParams.set('tab', tab.getAttribute('data-tab'));
			window.history.replaceState(null, '', url.toString());
		}
	}

	tabs.forEach(function (tab, i) {
		tab.addEventListener('click', function (e) {
			e.preventDefault();
			activate(tab, false);
		});
		tab.addEventListener('keydown', function (e) {
			var next = null;
			switch (e.key) {
				case 'ArrowRight':
				case 'ArrowDown':
					next = tabs[(i + 1) % tabs.length];
					break;
				case 'ArrowLeft':
				case 'ArrowUp':
					next = tabs[(i - 1 + tabs.length) % tabs.length];
					break;
				case 'Home':
					next = tabs[0];
					break;
				case 'End':
					next = tabs[tabs.length - 1];
					break;
			}
			if (next) {
				e.preventDefault();
				activate(next, true);
			}
		});
	});

	// ── Range sliders: show the value that will be saved ─────────────────
	Array.prototype.forEach.call(root.querySelectorAll('input[type="range"]'), function (range) {
		var out = document.getElementById(range.id + '-value');
		if (!out) {
			return;
		}
		var update = function () {
			out.textContent = range.value;
		};
		range.addEventListener('input', update);
		update();
	});

	// ── Colour pickers (core wp-color-picker) ─────────────────────────────
	if ($ && $.fn && $.fn.wpColorPicker) {
		$(root).find('.ce-theme-opts-color').wpColorPicker();
	}

	// ── Buttons that ask first ────────────────────────────────────────────
	root.addEventListener('click', function (e) {
		var btn = e.target.closest ? e.target.closest('button[data-ce-confirm]') : null;
		if (btn && !window.confirm(btn.getAttribute('data-ce-confirm'))) {
			e.preventDefault();
		}
	});
})(window.jQuery);
