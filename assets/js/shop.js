/**
 * ZarinCoach — فروشگاه
 * سبد کشویی، افزودن به سبد بدون بارگذاری مجدد، فیلتر کشویی موبایل، شمارش معکوس،
 * نوار چسبان خرید، ناوبری بخش‌های محصول، دکمه‌های ± تعداد و انتخاب گونه با دکمه.
 *
 * @package ZarinCoach
 */
(function () {
  'use strict';

  var doc = document;
  var root = doc.documentElement;
  var cfg = window.ZarinShop || {};
  var i18n = cfg.i18n || {};
  var jq = window.jQuery;
  var FA = '۰۱۲۳۴۵۶۷۸۹';

  function $(sel, ctx) { return (ctx || doc).querySelector(sel); }
  function $$(sel, ctx) { return Array.prototype.slice.call((ctx || doc).querySelectorAll(sel)); }
  function fa(n) { return String(n).replace(/\d/g, function (d) { return FA[d]; }); }
  function pad(n) { return fa(n < 10 ? '0' + n : n); }
  function onBody(evt, fn) { if (jq) { jq(doc.body).on(evt, fn); } }
  function lockScroll(on) { root.classList.toggle('zc-lock', !!on); doc.body.style.overflow = on ? 'hidden' : ''; }

  /* ---------------------------------------------------------------------
   * سبد کشویی
   * ------------------------------------------------------------------- */
  var drawer = $('[data-zc-cart-drawer]');
  var lastFocus = null;

  function drawerOpen() {
    if (!drawer) { return false; }
    lastFocus = doc.activeElement;
    drawer.hidden = false;
    // اجرای transition پس از نمایش
    requestAnimationFrame(function () {
      drawer.classList.add('is-open');
      var panel = $('.zc-cartdrawer__panel', drawer);
      if (panel) { panel.focus({ preventScroll: true }); }
    });
    $$('[data-zc-cart-open]').forEach(function (b) { b.setAttribute('aria-expanded', 'true'); });
    lockScroll(true);
    return true;
  }

  function drawerClose() {
    if (!drawer || !drawer.classList.contains('is-open')) { return; }
    drawer.classList.remove('is-open');
    $$('[data-zc-cart-open]').forEach(function (b) { b.setAttribute('aria-expanded', 'false'); });
    lockScroll(false);
    setTimeout(function () { if (!drawer.classList.contains('is-open')) { drawer.hidden = true; } }, 420);
    if (lastFocus && lastFocus.focus) { lastFocus.focus({ preventScroll: true }); }
  }

  if (drawer && cfg.drawer === '1') {
    doc.addEventListener('click', function (e) {
      var open = e.target.closest('[data-zc-cart-open]');
      if (open) { e.preventDefault(); drawerOpen(); return; }
      if (e.target.closest('[data-zc-cart-close]')) { e.preventDefault(); drawerClose(); }
    });
    doc.addEventListener('keydown', function (e) {
      if (!drawer.classList.contains('is-open')) { return; }
      if (e.key === 'Escape') { drawerClose(); return; }
      if (e.key === 'Tab') {
        var f = $$('a[href], button:not([disabled]), input:not([type="hidden"]), [tabindex]:not([tabindex="-1"])', drawer).filter(function (el) { return el.offsetParent !== null; });
        if (!f.length) { return; }
        var first = f[0], last = f[f.length - 1];
        if (e.shiftKey && doc.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && doc.activeElement === last) { e.preventDefault(); first.focus(); }
      }
    });
    onBody('added_to_cart', function () {
      if (cfg.autoOpen === '1') { drawerOpen(); }
    });
  }

  function bumpCount() {
    $$('.zc-cart-count').forEach(function (el) {
      el.classList.remove('is-bump');
      void el.offsetWidth; // eslint-disable-line no-void
      el.classList.add('is-bump');
    });
  }
  onBody('added_to_cart', bumpCount);

  /* ---------------------------------------------------------------------
   * افزودن به سبد در صفحه‌ی محصول بدون بارگذاری مجدد (ساده و متغیر)
   * «خرید فوری» و محصولات گروهی/خارجی مسیر عادی فرم را طی می‌کنند.
   * ------------------------------------------------------------------- */
  var atcParams = window.wc_add_to_cart_params || {};
  function ajaxSingleEnabled(form) {
    if (!drawer || cfg.drawer !== '1' || !window.fetch || !window.FormData || !jq) { return false; }
    if (atcParams.cart_redirect_after_add === 'yes') { return false; }
    var product = form.closest('.product');
    if (product && (product.classList.contains('product-type-grouped') || product.classList.contains('product-type-external'))) { return false; }
    return !form.querySelector('input[type="file"]');
  }

  doc.addEventListener('submit', function (e) {
    var form = e.target;
    if (!form.matches || !form.matches('form.cart') || !form.closest('.zc-sp, .elementor-widget-woocommerce-product-add-to-cart, .zc-product-spot')) { return; }
    var submitter = e.submitter || doc.activeElement;
    if (submitter && submitter.name === 'zc_buy_now') { return; } // خرید فوری ← تسویه‌حساب
    if (!ajaxSingleEnabled(form)) { return; }
    var btn = form.querySelector('.single_add_to_cart_button');
    if (!btn || btn.classList.contains('disabled')) { return; }

    e.preventDefault();
    var data;
    try { data = new FormData(form, submitter && submitter.form === form ? submitter : undefined); } catch (err) { data = new FormData(form); }
    if (!data.has('add-to-cart') && btn.value) { data.append('add-to-cart', btn.value); }

    btn.classList.add('loading');
    btn.disabled = true;
    fetch(form.action || window.location.href, { method: 'POST', body: data, credentials: 'same-origin' })
      .then(function (r) { return r.text(); })
      .then(function (html) {
        var parsed = new DOMParser().parseFromString(html, 'text/html');
        var err = parsed.querySelector('.woocommerce-error');
        var wrap = $('.woocommerce-notices-wrapper');
        if (err) {
          if (wrap) { wrap.innerHTML = ''; wrap.appendChild(err); wrap.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
          return;
        }
        if (wrap) { wrap.innerHTML = ''; }
        jq(doc.body).one('wc_fragments_refreshed wc_fragments_loaded', function () {
          jq(doc.body).trigger('added_to_cart', [{}, '', jq(btn)]);
        });
        jq(doc.body).trigger('wc_fragment_refresh');
      })
      .catch(function () { form.submit(); })
      .then(function () { btn.classList.remove('loading'); btn.disabled = false; });
  });

  /* ---------------------------------------------------------------------
   * فیلتر کشویی موبایل
   * ------------------------------------------------------------------- */
  var filters = $('[data-zc-filters]');
  if (filters) {
    var backdrop = $('.zc-shop-backdrop');
    var toggles = $$('[data-zc-filters-open]');
    var setFilters = function (open) {
      filters.classList.toggle('is-open', open);
      if (backdrop) { backdrop.hidden = !open; }
      toggles.forEach(function (b) { b.setAttribute('aria-expanded', open ? 'true' : 'false'); });
      lockScroll(open);
      if (open) { var f = filters.querySelector('button, a, input'); if (f) { f.focus({ preventScroll: true }); } }
    };
    doc.addEventListener('click', function (e) {
      if (e.target.closest('[data-zc-filters-open]')) { e.preventDefault(); setFilters(true); return; }
      if (e.target.closest('[data-zc-filters-close]') && filters.classList.contains('is-open')) { e.preventDefault(); setFilters(false); }
    });
    doc.addEventListener('keydown', function (e) { if (e.key === 'Escape' && filters.classList.contains('is-open')) { setFilters(false); } });
    window.addEventListener('resize', function () { if (window.innerWidth >= 1024 && filters.classList.contains('is-open')) { setFilters(false); } }, { passive: true });

    // ارقام فارسی قیمت ← لاتین پیش از ارسال؛ فیلدهای خالی از نشانی حذف می‌شوند.
    $$('form.zc-filters').forEach(function (form) {
      form.addEventListener('submit', function () {
        $$('input[name="min_price"], input[name="max_price"]', form).forEach(function (inp) {
          inp.value = inp.value.replace(/[۰-۹]/g, function (d) { return FA.indexOf(d); }).replace(/[٠-٩]/g, function (d) { return d.charCodeAt(0) - 1632; }).replace(/[^\d]/g, '');
        });
        $$('input, select', form).forEach(function (el) {
          if ((el.type === 'radio' || el.type === 'checkbox') ? !el.checked || el.value === '' : el.value === '') { el.disabled = true; }
        });
      });
    });
  }

  // مرتب‌سازی: ارسال خودکار (بدون اسکریپت ووکامرس هم کار کند).
  $$('.woocommerce-ordering select.orderby').forEach(function (sel) {
    sel.addEventListener('change', function () { if (sel.form) { sel.form.submit(); } });
  });

  /* ---------------------------------------------------------------------
   * شمارش معکوس
   * ------------------------------------------------------------------- */
  function initCountdowns(ctx) {
    $$('[data-zc-countdown]', ctx).forEach(function (el) {
      if (el.dataset.zcCdInit) { return; }
      el.dataset.zcCdInit = '1';
      var end = parseInt(el.getAttribute('data-zc-countdown'), 10) * 1000;
      if (!end) { return; }
      var cells = {};
      ['d', 'h', 'm', 's'].forEach(function (k) { cells[k] = el.querySelector('[data-cd="' + k + '"]'); });
      var timer;
      var tick = function () {
        var left = Math.max(0, end - Date.now());
        if (left <= 0) { el.classList.add('is-done'); clearInterval(timer); return; }
        var s = Math.floor(left / 1000);
        var v = { d: Math.floor(s / 86400), h: Math.floor(s % 86400 / 3600), m: Math.floor(s % 3600 / 60), s: s % 60 };
        Object.keys(v).forEach(function (k) { if (cells[k]) { cells[k].textContent = pad(v[k]); } });
      };
      tick();
      timer = setInterval(tick, 1000);
    });
  }
  initCountdowns();

  /* ---------------------------------------------------------------------
   * دکمه‌های ± تعداد
   * ------------------------------------------------------------------- */
  function initQty(ctx) {
    $$('.quantity', ctx).forEach(function (q) {
      var input = q.querySelector('input.qty');
      if (!input || input.type === 'hidden' || q.classList.contains('has-steps') || q.closest('.woocommerce-mini-cart')) { return; }
      q.classList.add('has-steps');
      var mk = function (dir) {
        var b = doc.createElement('button');
        b.type = 'button';
        b.className = 'zc-qty-btn';
        b.textContent = dir > 0 ? '+' : '−';
        b.setAttribute('aria-label', dir > 0 ? (i18n.inc || '+') : (i18n.dec || '-'));
        b.addEventListener('click', function () {
          var step = parseFloat(input.step) || 1;
          var min = input.min !== '' ? parseFloat(input.min) : 0;
          var max = input.max !== '' ? parseFloat(input.max) : Infinity;
          var val = (parseFloat(input.value) || 0) + dir * step;
          val = Math.min(max, Math.max(min, val));
          if (String(val) !== input.value) {
            input.value = val;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
            if (jq) { jq(input).trigger('change'); }
          }
          sync();
        });
        return b;
      };
      var minus = mk(-1);
      var plus = mk(1);
      var sync = function () {
        var v = parseFloat(input.value) || 0;
        minus.disabled = input.min !== '' && v <= parseFloat(input.min);
        plus.disabled = input.max !== '' && v >= parseFloat(input.max);
      };
      // در راست‌به‌چپ: «+» سمت راست، «−» سمت چپ
      q.insertBefore(plus, input);
      q.appendChild(minus);
      input.addEventListener('input', sync);
      input.addEventListener('change', sync);
      sync();
    });
  }
  initQty();
  onBody('updated_wc_div updated_cart_totals wc_fragments_refreshed', function () { initQty(); });
  onBody('found_variation', function () { setTimeout(function () { $$('.quantity.has-steps input.qty').forEach(function (i) { i.dispatchEvent(new Event('change')); }); }, 0); });

  /* ---------------------------------------------------------------------
   * انتخاب گونه با دکمه (به‌جای فهرست کشویی)
   * ------------------------------------------------------------------- */
  function initPills(ctx) {
    if (cfg.pills !== '1' || !jq) { return; }
    $$('form.variations_form', ctx).forEach(function (form) {
      if (form.dataset.zcPills) { return; }
      form.dataset.zcPills = '1';
      var table = form.querySelector('table.variations');
      if (!table) { return; }
      table.classList.add('has-pills');
      var groups = [];
      $$('select', table).forEach(function (sel) {
        var wrap = doc.createElement('div');
        wrap.className = 'zc-pills';
        wrap.setAttribute('role', 'group');
        var label = form.querySelector('label[for="' + sel.id + '"]');
        if (label) { wrap.setAttribute('aria-label', label.textContent.trim()); }
        sel.parentNode.insertBefore(wrap, sel);
        sel.setAttribute('tabindex', '-1');
        sel.setAttribute('aria-hidden', 'true');
        var render = function () {
          wrap.innerHTML = '';
          Array.prototype.forEach.call(sel.options, function (opt) {
            if (!opt.value) { return; }
            var b = doc.createElement('button');
            b.type = 'button';
            b.className = 'zc-pill';
            b.textContent = opt.textContent;
            b.dataset.value = opt.value;
            b.setAttribute('aria-pressed', sel.value === opt.value ? 'true' : 'false');
            if (opt.disabled) { b.classList.add('is-disabled'); }
            b.addEventListener('click', function () {
              jq(sel).val(sel.value === opt.value ? '' : opt.value).trigger('change');
            });
            wrap.appendChild(b);
          });
        };
        render();
        groups.push(render);
        sel.addEventListener('change', render);
      });
      // ووکامرس پس از هر انتخاب گزینه‌های ناموجود را غیرفعال/حذف می‌کند.
      jq(form).on('woocommerce_update_variation_values reset_data', function () {
        setTimeout(function () { groups.forEach(function (r) { r(); }); }, 0);
      });
    });
  }
  initPills();

  /* ---------------------------------------------------------------------
   * ناوبری بخش‌های صفحه‌ی محصول
   * ------------------------------------------------------------------- */
  var spNav = $('.zc-sp-nav');
  if (spNav) {
    var links = $$('a[href^="#"]', spNav);
    var sections = links.map(function (a) { return doc.getElementById(decodeURIComponent(a.getAttribute('href').slice(1))); }).filter(Boolean);
    var setActive = function (id) {
      links.forEach(function (a) {
        var on = a.getAttribute('href') === '#' + id;
        a.classList.toggle('is-active', on);
        if (on) { a.setAttribute('aria-current', 'true'); } else { a.removeAttribute('aria-current'); }
      });
    };
    links.forEach(function (a) {
      a.addEventListener('click', function (e) {
        var target = doc.getElementById(decodeURIComponent(a.getAttribute('href').slice(1)));
        if (!target) { return; }
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        history.replaceState(null, '', a.getAttribute('href'));
        setActive(target.id);
      });
    });
    if ('IntersectionObserver' in window && sections.length) {
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) { if (en.isIntersecting) { setActive(en.target.id); } });
      }, { rootMargin: '-35% 0px -55% 0px' });
      sections.forEach(function (s) { io.observe(s); });
    }
    // پیوند «دیدگاه‌ها» در خلاصه‌ی محصول
    $$('.woocommerce-review-link').forEach(function (a) {
      a.addEventListener('click', function (e) {
        var t = doc.getElementById('tab-reviews') || doc.getElementById('reviews');
        if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
      });
    });
  }

  /* ---------------------------------------------------------------------
   * نوار چسبان خرید
   * ------------------------------------------------------------------- */
  var bar = $('[data-zc-stickybar]');
  var mainForm = $('.zc-sp form.cart');
  if (bar && mainForm && 'IntersectionObserver' in window) {
    var mainBtn = mainForm.querySelector('.single_add_to_cart_button') || mainForm;
    var passed = false;
    var show = function (on) {
      bar.hidden = false;
      bar.classList.toggle('is-visible', on);
      doc.body.classList.toggle('zc-has-stickybar', on);
      bar.setAttribute('aria-hidden', on ? 'false' : 'true');
      $$('a, button', bar).forEach(function (el) { el.tabIndex = on ? 0 : -1; });
    };
    show(false);
    new IntersectionObserver(function (entries) {
      var en = entries[0];
      passed = !en.isIntersecting && en.boundingClientRect.top < 0;
      show(passed);
    }, { rootMargin: '-' + (parseInt(getComputedStyle(root).getPropertyValue('--zc-header-h'), 10) || 70) + 'px 0px 0px 0px' }).observe(mainBtn);

    var buy = $('[data-zc-stickybar-buy]', bar);
    if (buy) {
      buy.addEventListener('click', function () {
        var isVariable = mainForm.classList.contains('variations_form');
        mainForm.scrollIntoView({ behavior: 'smooth', block: 'center' });
        if (isVariable) {
          var first = mainForm.querySelector('.zc-pill, select');
          if (first) { setTimeout(function () { first.focus({ preventScroll: true }); }, 450); }
          return;
        }
        var btn = mainForm.querySelector('.single_add_to_cart_button');
        if (btn && !btn.disabled) { btn.click(); }
      });
    }
  }

  /* ---------------------------------------------------------------------
   * کپی کد تخفیف
   * ------------------------------------------------------------------- */
  doc.addEventListener('click', function (e) {
    var b = e.target.closest('[data-zc-copy]');
    if (!b) { return; }
    e.preventDefault();
    var text = b.getAttribute('data-zc-copy');
    var done = function () {
      var label = b.querySelector('[data-zc-copy-label]') || b;
      var old = label.textContent;
      b.classList.add('is-copied');
      label.textContent = i18n.copied || '✓';
      setTimeout(function () { label.textContent = old; b.classList.remove('is-copied'); }, 1800);
    };
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text).then(done, done);
    } else {
      var ta = doc.createElement('textarea');
      ta.value = text; ta.setAttribute('readonly', ''); ta.style.position = 'fixed'; ta.style.opacity = '0';
      doc.body.appendChild(ta); ta.select();
      try { doc.execCommand('copy'); } catch (err) { /* ignore */ }
      doc.body.removeChild(ta); done();
    }
  });

  /* ---------------------------------------------------------------------
   * ویجت‌های المنتور (ویرایشگر)
   * ------------------------------------------------------------------- */
  window.addEventListener('elementor/frontend/init', function () {
    if (!window.elementorFrontend || !window.elementorFrontend.hooks) { return; }
    window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
      var el = $scope && $scope[0];
      if (!el) { return; }
      initCountdowns(el);
      initQty(el);
      initPills(el);
    });
  });
})();
