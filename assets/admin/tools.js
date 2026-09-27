/*!
 * زرین‌کوچ — صفحه‌های ابزار (نصب دمو، سربرگ و پاورقی، اطلاعات سیستم)
 * جابه‌جایی بین بخش‌ها با پشتیبانی از #hash، کپی و دانلود گزارش.
 * بدون جاوااسکریپت همه‌ی بخش‌ها زیر هم نمایش داده می‌شوند.
 */
(function () {
	'use strict';

	var i18n = window.zcTools || {};
	var root = document.querySelector('[data-zc-tool]');

	if (root) {
		var panes = Array.prototype.slice.call(root.querySelectorAll('.zc-tool-pane'));
		var links = Array.prototype.slice.call(root.querySelectorAll('.zc-tool-link[data-pane]'));
		var current = root.querySelector('[data-zc-current]');
		var keys = panes.map(function (p) { return p.getAttribute('data-pane'); });

		var fromHash = function () {
			return (window.location.hash || '').replace(/^#(zc-pane-)?/, '');
		};

		var show = function (key, scroll) {
			if (keys.indexOf(key) === -1) { key = keys[0]; }
			panes.forEach(function (pane) {
				pane.classList.toggle('is-active', pane.getAttribute('data-pane') === key);
			});
			links.forEach(function (link) {
				var on = link.getAttribute('data-pane') === key;
				link.classList.toggle('is-active', on);
				if (on) {
					link.setAttribute('aria-current', 'true');
					if (current) {
						var label = link.querySelector('span');
						current.textContent = label ? label.textContent : '';
					}
				} else {
					link.removeAttribute('aria-current');
				}
			});
			if (scroll) {
				var top = root.getBoundingClientRect().top + window.pageYOffset - 40;
				if (window.pageYOffset > top) { window.scrollTo(0, Math.max(0, top)); }
			}
		};

		if (panes.length > 1) {
			root.classList.add('is-js');

			links.forEach(function (link) {
				link.addEventListener('click', function (e) {
					e.preventDefault();
					var key = link.getAttribute('data-pane');
					show(key, true);
					if (window.history && window.history.replaceState) {
						window.history.replaceState(null, '', '#zc-pane-' + key);
					}
				});
			});

			root.addEventListener('click', function (e) {
				var go = e.target.closest ? e.target.closest('[data-zc-goto]') : null;
				if (!go) { return; }
				e.preventDefault();
				show(go.getAttribute('data-zc-goto'), true);
			});

			window.addEventListener('hashchange', function () {
				var key = fromHash();
				if (keys.indexOf(key) !== -1) { show(key, true); }
			});

			var initial = fromHash();
			show(initial, false);
			// مرورگر پیش از پنهان شدن بخش‌ها به لنگر پریده است؛ به بالای پوسته برگرد.
			if (initial && keys.indexOf(initial) !== -1) {
				var toTop = function () { window.scrollTo(0, 0); };
				window.requestAnimationFrame(toTop);
				if (document.readyState !== 'complete') {
					window.addEventListener('load', function () { window.setTimeout(toTop, 0); }, { once: true });
				}
			}
		}
	}

	/* کپی و دانلود */
	var flash = function (btn, text) {
		var label = btn.querySelector('span') || btn;
		if (!btn.hasAttribute('data-label')) { btn.setAttribute('data-label', label.textContent); }
		label.textContent = text;
		btn.classList.add('is-flash');
		window.clearTimeout(btn._zcT);
		btn._zcT = window.setTimeout(function () {
			label.textContent = btn.getAttribute('data-label');
			btn.classList.remove('is-flash');
		}, 1800);
	};

	var legacyCopy = function (field) {
		try {
			field.focus();
			field.select();
			return document.execCommand('copy');
		} catch (err) {
			return false;
		}
	};

	document.addEventListener('click', function (e) {
		if (!e.target.closest) { return; }

		var btn = e.target.closest('[data-zc-copy]');
		if (btn) {
			var field = document.querySelector(btn.getAttribute('data-zc-copy'));
			if (!field) { return; }
			var text = field.value || field.textContent || '';
			var ok = function () { flash(btn, i18n.copied || 'OK'); };
			var fail = function () { flash(btn, legacyCopy(field) ? (i18n.copied || 'OK') : (i18n.copyFail || '×')); };
			if (navigator.clipboard && window.isSecureContext) {
				navigator.clipboard.writeText(text).then(ok, fail);
			} else {
				fail();
			}
			return;
		}

		var dl = e.target.closest('[data-zc-download]');
		if (dl) {
			var src = document.querySelector(dl.getAttribute('data-zc-download'));
			if (!src || !window.Blob || !window.URL) { return; }
			// BOM برای نمایش درست فارسی در ویرایشگرهای ویندوز.
			var blob = new Blob(['\ufeff' + (src.value || src.textContent || '')], { type: 'text/plain;charset=utf-8' });
			var url = URL.createObjectURL(blob);
			var a = document.createElement('a');
			a.href = url;
			a.download = dl.getAttribute('data-filename') || 'report.txt';
			document.body.appendChild(a);
			a.click();
			a.remove();
			window.setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
		}
	});
})();

/* مدیریت ویجت‌ها: جستجو، فیلتر، عملیات گروهی (۲.۲) */
(function () {
	'use strict';
	var form = document.getElementById('zc-widgets-form');
	if (!form) { return; }

	var items = Array.prototype.slice.call(form.querySelectorAll('[data-zc-wm-item]'));
	var search = form.querySelector('[data-zc-wm-search]');
	var empty = form.querySelector('[data-zc-wm-empty]');
	var onEl = document.querySelector('[data-zc-wm-on]');
	var offEl = document.querySelector('[data-zc-wm-off]');
	var filter = 'all';
	var fa = function (n) { return String(n).replace(/\d/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'[d]; }); };
	var norm = function (s) { return (s || '').toLowerCase().replace(/[يى]/g, 'ی').replace(/ك/g, 'ک').replace(/\u200c/g, ' ').trim(); };

	var tally = function () {
		var on = items.filter(function (el) { return el.querySelector('input').checked; }).length;
		if (onEl) { onEl.textContent = fa(on); }
		if (offEl) { offEl.textContent = fa(items.length - on); }
	};

	var apply = function () {
		var q = norm(search ? search.value : '');
		var shown = 0;
		items.forEach(function (el) {
			var checked = el.querySelector('input').checked;
			var used = parseInt(el.getAttribute('data-used') || '0', 10) > 0;
			var ok = (!q || norm(el.getAttribute('data-search')).indexOf(q) !== -1) &&
				(filter === 'all' || (filter === 'on' && checked) || (filter === 'off' && !checked) || (filter === 'unused' && !used));
			el.hidden = !ok;
			if (ok) { shown++; }
		});
		Array.prototype.forEach.call(form.querySelectorAll('[data-zc-wm-group]'), function (g) {
			g.hidden = !g.querySelector('[data-zc-wm-item]:not([hidden])');
		});
		if (empty) { empty.hidden = shown > 0; }
	};

	if (search) { search.addEventListener('input', apply); }
	form.addEventListener('change', function () { tally(); if (filter !== 'all') { apply(); } });
	form.addEventListener('click', function (e) {
		var chip = e.target.closest('[data-zc-wm-filter]');
		if (chip) {
			filter = chip.getAttribute('data-zc-wm-filter');
			Array.prototype.forEach.call(form.querySelectorAll('[data-zc-wm-filter]'), function (c) { c.classList.toggle('is-active', c === chip); });
			apply();
			return;
		}
		var bulk = e.target.closest('[data-zc-wm-bulk]');
		if (bulk) {
			var mode = bulk.getAttribute('data-zc-wm-bulk');
			items.forEach(function (el) {
				var input = el.querySelector('input');
				if (mode === 'on') { input.checked = true; }
				if (mode === 'unused' && parseInt(el.getAttribute('data-used') || '0', 10) === 0) { input.checked = false; }
			});
			tally();
			apply();
		}
	});
})();
