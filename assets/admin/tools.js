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
