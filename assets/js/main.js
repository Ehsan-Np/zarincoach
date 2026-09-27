/**
 * ZarinCoach — اسکریپت اصلی قالب
 * جاوااسکریپت خالص (Vanilla)، بدون وابستگی به jQuery
 *
 * @package ZarinCoach
 */
(function () {
  'use strict';

  var doc = document;
  var html = doc.documentElement;
  var config = (typeof window.ZarinCoach === 'object' && window.ZarinCoach) || {};
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var animations = config.animations !== '0' && !reduceMotion;

  /* ---------------------------------------------------------------- *
   * ابزارهای کمکی
   * ---------------------------------------------------------------- */
  function $(selector, scope) { return (scope || doc).querySelector(selector); }
  function $$(selector, scope) { return Array.prototype.slice.call((scope || doc).querySelectorAll(selector)); }

  function onReady(fn) {
    if (doc.readyState !== 'loading') { fn(); }
    else { doc.addEventListener('DOMContentLoaded', fn); }
  }

  var supportsIO = 'IntersectionObserver' in window;

  /* ---------------------------------------------------------------- *
   * منوی موبایل (Drawer)
   * ---------------------------------------------------------------- */
  function initDrawer() {
    var triggers = $$('[data-zc-drawer-open]');
    var drawer = $('[data-zc-drawer]');
    if (!drawer || !triggers.length) { return; }

    // خارج کردن منو از بسته‌بندی‌های المنتور/سربرگ چسبان (stacking context) تا بالای همه‌چیز قرار گیرد.
    if (drawer.parentNode !== doc.body && !doc.body.classList.contains('elementor-editor-active')) {
      doc.body.appendChild(drawer);
    }

    var closers = $$('[data-zc-drawer-close]', drawer);
    var lastFocus = null;

    function open() {
      lastFocus = doc.activeElement;
      drawer.classList.add('is-open');
      drawer.setAttribute('aria-hidden', 'false');
      doc.body.classList.add('zc-lock', 'is-burger-open');
      triggers.forEach(function (t) { t.setAttribute('aria-expanded', 'true'); });
      var firstLink = $('a, button', drawer);
      if (firstLink) { firstLink.focus(); }
      doc.addEventListener('keydown', onKey);
    }

    function close() {
      drawer.classList.remove('is-open');
      drawer.setAttribute('aria-hidden', 'true');
      doc.body.classList.remove('zc-lock', 'is-burger-open');
      triggers.forEach(function (t) { t.setAttribute('aria-expanded', 'false'); });
      doc.removeEventListener('keydown', onKey);
      if (lastFocus) { lastFocus.focus(); }
    }

    function onKey(e) {
      if (e.key === 'Escape') { close(); }
    }

    triggers.forEach(function (t) { t.addEventListener('click', function (e) { e.preventDefault(); open(); }); });
    closers.forEach(function (c) { c.addEventListener('click', function (e) { e.preventDefault(); close(); }); });

    var backdrop = $('[data-zc-drawer-backdrop]', drawer);
    if (backdrop) { backdrop.addEventListener('click', close); }
  }

  /* ---------------------------------------------------------------- *
   * سربرگ چسبان و نوار پیشرفت
   * ---------------------------------------------------------------- */
  function initHeader() {
    var header = $('[data-zc-header]');
    /* v2.4: قالب هدرِ ساخته‌شده با ویجت‌های المنتور — قاب (.zc-header-frame) نقش سربرگ را می‌گیرد. */
    if (!header) {
      header = $('.zc-header-frame');
      if (!header) { return; }
      header.setAttribute('data-zc-header', '');
    }

    var progress = $('[data-zc-progress]');
    var topbar = $('[data-zc-topbar]');
    var ticking = false;
    var stuck = null;

    // ارتفاع واقعی سربرگ (+ نوار مدیریت) و نوار بالایی در متغیرهای CSS؛ همه‌ی آفست‌ها
    // (فهرست مطالب، اشتراک‌گذاری، لینک‌های داخلی) از همین یک منبع خوانده می‌شوند.
    // نوار بالایی دیگر با تغییر ارتفاع جمع نمی‌شود (عامل پرش صفحه و خطای Scroll anchoring)؛
    // پوسته‌ی چسبان با top منفی، آن را مثل محتوای عادی به بیرون اسکرول می‌کند.
    function measure() {
      var bar = doc.getElementById('wpadminbar');
      var admin = bar && getComputedStyle(bar).position === 'fixed' ? bar.offsetHeight : 0;
      var tb = topbar && topbar.offsetParent !== null ? topbar.offsetHeight : 0;
      html.style.setProperty('--zc-topbar-h', tb + 'px');
      html.style.setProperty('--zc-admin-h', admin + 'px');
      html.style.setProperty('--zc-header-h', (header.offsetHeight + admin) + 'px');
    }
    measure();
    if ('ResizeObserver' in window) {
      var ro = new ResizeObserver(function () { measure(); });
      ro.observe(header);
      if (topbar) { ro.observe(topbar); }
    } else {
      window.addEventListener('resize', measure, { passive: true });
    }

    function update() {
      var y = window.pageYOffset;
      if (config.stickyHeader !== '0') {
        // فقط ظاهر (پس‌زمینه/سایه) عوض می‌شود، نه ارتفاع؛ با فاصله‌ی بازگشتی برای جلوگیری از لرزش.
        var next = stuck ? y > 4 : y > 24;
        if (next !== stuck) { stuck = next; header.classList.toggle('is-stuck', stuck); }
      }
      if (progress && config.progress !== '0') {
        var height = doc.documentElement.scrollHeight - window.innerHeight;
        var ratio = height > 0 ? Math.min(1, y / height) : 0;
        progress.style.transform = 'scaleX(' + ratio.toFixed(4) + ')';
      }
      ticking = false;
    }

    window.addEventListener('scroll', function () {
      if (!ticking) { window.requestAnimationFrame(update); ticking = true; }
    }, { passive: true });

    update();
  }

  /** ثبت کوکی کارکردی «فونت در کش است» تا پیش‌بارگذاری فونت فقط در بازدید اول انجام شود. */
  function initFontCookie() {
    if (!config.fontCookie || !doc.fonts || /(?:^|;\s*)zc_fc=/.test(doc.cookie)) { return; }
    doc.fonts.ready.then(function () {
      var ok = false;
      doc.fonts.forEach(function (f) { if (f.status === 'loaded' && /^["']?Arad/.test(f.family)) { ok = true; } });
      if (ok) { doc.cookie = 'zc_fc=1; max-age=1209600; path=' + config.fontCookie + '; SameSite=Lax'; }
    });
  }

  /** فاصله‌ی امن بالای صفحه برای اسکرول به یک هدف (سربرگ چسبان + نوار ناوبری چسبان رزومه). */
  function scrollOffset() {
    var offset = parseInt(getComputedStyle(html).getPropertyValue('--zc-header-h'), 10) || 76;
    var rnav = $('[data-zc-resume-nav]');
    if (rnav && getComputedStyle(rnav).position === 'sticky') { offset += rnav.offsetHeight; }
    return offset + 16;
  }

  /** اسکرول دقیق به یک عنصر: عنوان درست زیر سربرگ قرار می‌گیرد. */
  function scrollToTarget(target, hash) {
    var top = Math.max(0, target.getBoundingClientRect().top + window.pageYOffset - scrollOffset());
    window.scrollTo({ top: top, behavior: reduceMotion ? 'auto' : 'smooth' });
    if (hash) { history.replaceState(null, '', hash); }
    // دسترس‌پذیری: فوکوس به هدف بدون پرش دوباره.
    if (!target.hasAttribute('tabindex')) { target.setAttribute('tabindex', '-1'); }
    try { target.focus({ preventScroll: true }); } catch (err) { /* مرورگر قدیمی */ }
  }

  /* ---------------------------------------------------------------- *
   * بازگشت به بالا
   * ---------------------------------------------------------------- */
  function initBackToTop() {
    var btn = $('[data-zc-to-top]');
    if (!btn || config.backToTop === '0') {
      if (btn) { btn.remove(); }
      return;
    }
    var ticking = false;
    function update() {
      if (window.pageYOffset > 600) { btn.classList.add('is-show'); }
      else { btn.classList.remove('is-show'); }
      ticking = false;
    }
    window.addEventListener('scroll', function () {
      if (!ticking) { window.requestAnimationFrame(update); ticking = true; }
    }, { passive: true });
    btn.addEventListener('click', function () {
      window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
    update();
  }

  /* ---------------------------------------------------------------- *
   * انیمیشن‌های نمایش در اسکرول
   * ---------------------------------------------------------------- */
  function initReveal() {
    var items = $$('.zc-reveal');
    if (!items.length) { return; }

    if (!animations || !supportsIO) {
      items.forEach(function (el) { el.classList.add('is-in'); el.style.opacity = '1'; });
      return;
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) { return; }
        var el = entry.target;
        var delay = parseInt(el.getAttribute('data-zc-delay') || '0', 10);
        window.setTimeout(function () { el.classList.add('is-in'); }, isNaN(delay) ? 0 : delay);
        io.unobserve(el);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.05 });

    items.forEach(function (el) { io.observe(el); });
  }

  /* ---------------------------------------------------------------- *
   * شمارنده‌های آماری
   * ---------------------------------------------------------------- */
  function initCounters() {
    var counters = $$('[data-zc-count]');
    if (!counters.length) { return; }

    function run(el) {
      var raw = el.getAttribute('data-zc-count') || '0';
      var target = parseFloat(String(raw).replace(/[^\d.\-]/g, ''));
      if (isNaN(target)) { target = 0; }
      var suffix = el.getAttribute('data-zc-suffix') || '';
      var duration = parseInt(el.getAttribute('data-zc-duration') || '1600', 10);
      var persian = el.getAttribute('data-zc-persian') === '1';
      var digits = persian ? ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'] : null;

      function toDigits(n) {
        var s = String(n);
        return persian ? s.replace(/\d/g, function (d) { return digits[parseInt(d, 10)]; }) : s;
      }

      if (!animations) { el.textContent = toDigits(target) + suffix; return; }

      var start = performance.now();
      function frame(now) {
        var p = Math.min(1, (now - start) / duration);
        var eased = 1 - Math.pow(1 - p, 3);
        el.textContent = toDigits(Math.round(target * eased)) + suffix;
        if (p < 1) { window.requestAnimationFrame(frame); }
      }
      window.requestAnimationFrame(frame);
    }

    if (!supportsIO) { counters.forEach(run); return; }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) { return; }
        run(entry.target);
        io.unobserve(entry.target);
      });
    }, { threshold: 0.4 });

    counters.forEach(function (el) {
      // مقدار نهایی در HTML چاپ می‌شود (سئو و بدون جاوااسکریپت)؛ فقط پیش از انیمیشن صفر می‌شود.
      if (animations && el.getBoundingClientRect().top > window.innerHeight) {
        var suffix = el.getAttribute('data-zc-suffix') || '';
        el.textContent = (el.getAttribute('data-zc-persian') === '1' ? '۰' : '0') + suffix;
      }
      io.observe(el);
    });
  }

  /* ---------------------------------------------------------------- *
   * آکاردئون (پرسش‌های پرتکرار و موارد مشابه)
   * ---------------------------------------------------------------- */
  function initAccordions() {
    $$('[data-zc-acc]').forEach(function (acc) {
      var head = $('[data-zc-acc-head]', acc);
      if (!head) { return; }

      head.addEventListener('click', function () {
        var isOpen = acc.classList.contains('is-open');
        var group = acc.getAttribute('data-zc-acc-group');

        if (group) {
          $$('[data-zc-acc-group="' + group + '"]').forEach(function (other) {
            if (other !== acc) {
              other.classList.remove('is-open');
              var otherHead = $('[data-zc-acc-head]', other);
              if (otherHead) { otherHead.setAttribute('aria-expanded', 'false'); }
            }
          });
        }

        acc.classList.toggle('is-open', !isOpen);
        head.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
      });
    });
  }

  /* ---------------------------------------------------------------- *
   * اسلایدر سبک (نظرات، خدمات و ...)
   * ---------------------------------------------------------------- */
  var sliderRegistry = [];
  var sliderGlobalsBound = false;

  function toFa(n) {
    return String(n).replace(/\d/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'.charAt(+d); });
  }

  function sliderGlobals() {
    if (sliderGlobalsBound) { return; }
    sliderGlobalsBound = true;
    var rt = null;
    function refresh(method) {
      sliderRegistry = sliderRegistry.filter(function (api) {
        if (!api.el.isConnected) { api.stop(); return false; }
        api[method]();
        return true;
      });
    }
    window.addEventListener('resize', function () {
      window.clearTimeout(rt);
      rt = window.setTimeout(function () { refresh('layout'); }, 120);
    }, { passive: true });
    doc.addEventListener('visibilitychange', function () { refresh('sync'); });
  }

  function initSlider(slider) {
    if (!slider || slider.__zcSlider) { return; }
    var track = $('[data-zc-slider-track]', slider);
    if (!track) { return; }
    var slides = $$(':scope > *', track);
    if (!slides.length) { return; }

    var viewport = $('[data-zc-slider-viewport]', slider) || track.parentNode;
    var dotsWrap = $('[data-zc-slider-dots]', slider);
    var prevBtn = $('[data-zc-slider-prev]', slider);
    var nextBtn = $('[data-zc-slider-next]', slider);
    var status = $('[data-zc-slider-status]', slider);
    var rtl = window.getComputedStyle(slider).direction === 'rtl';
    var loop = slider.getAttribute('data-zc-loop') !== '0';
    var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var autoplay = slider.getAttribute('data-zc-autoplay') === '1' && !reduce;
    var interval = Math.max(2000, parseInt(slider.getAttribute('data-zc-interval') || '6000', 10) || 6000);
    var labelSlide = slider.getAttribute('data-zc-label-slide') || 'Slide %d';
    var labelStatus = slider.getAttribute('data-zc-label-status') || '%1$d / %2$d';

    var index = 0;
    var perView = 1;
    var dots = [];
    var timer = null;
    var hovered = false;
    var focused = false;
    var inView = true;

    function readPerView() {
      var v = parseInt(window.getComputedStyle(slider).getPropertyValue('--zc-spv-current'), 10);
      return Math.max(1, Math.min(isNaN(v) ? 1 : v, slides.length));
    }
    function maxIndex() { return Math.max(0, slides.length - perView); }

    function buildDots() {
      if (!dotsWrap) { return; }
      var count = maxIndex() + 1;
      if (dots.length === count) { return; }
      dotsWrap.innerHTML = '';
      dots = [];
      for (var i = 0; i < count; i++) {
        var b = doc.createElement('button');
        b.type = 'button';
        b.className = 'zc-slider-dot';
        b.setAttribute('aria-label', labelSlide.replace('%d', toFa(i + 1)));
        b.addEventListener('click', (function (n) { return function () { go(n); restart(); }; })(i));
        dotsWrap.appendChild(b);
        dots.push(b);
      }
    }

    function step() {
      if (slides.length > 1) { return Math.abs(slides[1].offsetLeft - slides[0].offsetLeft); }
      return slides[0].offsetWidth;
    }

    function go(i, instant) {
      var max = maxIndex();
      if (loop) {
        if (i > max) { i = 0; } else if (i < 0) { i = max; }
      } else {
        i = Math.max(0, Math.min(i, max));
      }
      index = i;
      var offset = step() * index;
      if (instant) { track.style.transitionDuration = '0ms'; }
      track.style.transform = 'translate3d(' + (rtl ? offset : -offset) + 'px,0,0)';
      if (instant) { void track.offsetWidth; track.style.transitionDuration = ''; }

      dots.forEach(function (d, di) {
        var on = di === index;
        d.classList.toggle('is-active', on);
        if (on) { d.setAttribute('aria-current', 'true'); } else { d.removeAttribute('aria-current'); }
      });
      slides.forEach(function (s, si) {
        var visible = si >= index && si < index + perView;
        s.classList.toggle('is-visible', visible);
        s.setAttribute('aria-hidden', visible ? 'false' : 'true');
        if ('inert' in s) { s.inert = !visible; }
      });
      if (prevBtn) { prevBtn.disabled = !loop && index === 0; }
      if (nextBtn) { nextBtn.disabled = !loop && index === max; }
      if (status && !instant) {
        status.textContent = labelStatus.replace('%1$d', toFa(index + 1)).replace('%2$d', toFa(slides.length));
      }
    }

    function next() { go(index + 1); }
    function prev() { go(index - 1); }

    function stop() { if (timer) { window.clearInterval(timer); timer = null; } }
    function sync() {
      var should = autoplay && maxIndex() > 0 && !hovered && !focused && inView && !doc.hidden;
      if (should && !timer) {
        timer = window.setInterval(function () {
          if (!slider.isConnected) { stop(); return; }
          if (!loop && index >= maxIndex()) { go(0); } else { next(); }
        }, interval);
      } else if (!should) { stop(); }
    }
    function restart() { stop(); sync(); }

    function layout() {
      perView = readPerView();
      buildDots();
      slider.classList.toggle('is-static', maxIndex() === 0);
      go(Math.min(index, maxIndex()), true);
      sync();
    }

    if (prevBtn) { prevBtn.addEventListener('click', function () { prev(); restart(); }); }
    if (nextBtn) { nextBtn.addEventListener('click', function () { next(); restart(); }); }

    slider.addEventListener('mouseenter', function () { hovered = true; sync(); });
    slider.addEventListener('mouseleave', function () { hovered = false; sync(); });
    slider.addEventListener('focusin', function () { focused = true; sync(); });
    slider.addEventListener('focusout', function (e) {
      if (!slider.contains(e.relatedTarget)) { focused = false; sync(); }
    });

    // صفحه‌کلید: در راست‌به‌چپ، کلید چپ یعنی «بعدی».
    slider.addEventListener('keydown', function (e) {
      if (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') { return; }
      var forward = rtl ? e.key === 'ArrowLeft' : e.key === 'ArrowRight';
      if (forward) { next(); } else { prev(); }
      e.preventDefault();
    });

    // کشیدن با انگشت (سوایپ).
    var startX = 0;
    var startY = 0;
    var touching = false;
    viewport.addEventListener('touchstart', function (e) {
      startX = e.touches[0].clientX;
      startY = e.touches[0].clientY;
      touching = true;
      hovered = true;
      sync();
    }, { passive: true });
    viewport.addEventListener('touchend', function (e) {
      if (!touching) { return; }
      touching = false;
      hovered = false;
      var dx = e.changedTouches[0].clientX - startX;
      var dy = e.changedTouches[0].clientY - startY;
      if (Math.abs(dx) > 40 && Math.abs(dx) > Math.abs(dy)) {
        var forward = rtl ? dx > 0 : dx < 0;
        if (forward) { next(); } else { prev(); }
      }
      sync();
    });

    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (entries) {
        inView = entries[0].isIntersecting;
        sync();
      }, { threshold: 0.25 }).observe(slider);
    }

    var api = { el: slider, layout: layout, sync: sync, stop: stop, go: go };
    slider.__zcSlider = api;
    sliderRegistry.push(api);
    sliderGlobals();

    slider.classList.add('is-ready');
    layout();
    // پس از بارگذاری فونت‌ها ابعاد دوباره محاسبه می‌شود.
    if (doc.fonts && doc.fonts.ready) { doc.fonts.ready.then(function () { if (slider.isConnected) { layout(); } }); }
    window.addEventListener('load', function () { if (slider.isConnected) { layout(); } });
  }

  function initSliders(root) {
    $$('[data-zc-slider]', root || doc).forEach(initSlider);
  }

  /* ---------------------------------------------------------------- *
   * کتابخانه‌ی طرحواره‌ها: فیلتر دسته + جستجوی فوری
   * ---------------------------------------------------------------- */
  function normFa(str) {
    return String(str || '')
      .toLowerCase()
      .replace(/[\u064A\u0649]/g, '\u06CC')   // ي ى → ی
      .replace(/\u0643/g, '\u06A9')           // ك → ک
      .replace(/[\u0623\u0625\u0622]/g, '\u0627') // أ إ آ → ا
      .replace(/\u0629/g, '\u0647')           // ة → ه
      .replace(/[\u064B-\u065F\u0670]/g, '')  // اعراب
      .replace(/[\u200D\u200F\u200E]/g, '')     // نشانه‌های جهت
      .replace(/\u200C/g, ' ')                 // نیم‌فاصله = فاصله (بی‌ارزشی ≈ بی ارزشی)
      .replace(/[\s\-_/«»"'.,،:؛()]+/g, ' ')
      .trim();
  }

  function initSchemaHub(hub) {
    if (hub.getAttribute('data-zc-sc-ready')) { return; }
    hub.setAttribute('data-zc-sc-ready', '1');

    var chips = $$('[data-zc-sc-filter]', hub);
    var input = $('[data-zc-sc-search]', hub);
    var status = $('[data-zc-sc-status]', hub);
    var empty = $('[data-zc-sc-empty]', hub);
    var sections = $$('[data-zc-sc-sec]', hub);
    var cards = $$('[data-zc-sc-card]', hub);
    var state = { filter: hub.getAttribute('data-zc-sc-default') || 'all', q: '' };
    var timer = 0;

    cards.forEach(function (c) {
      c._zcText = normFa(c.getAttribute('data-zc-sc-text'));
      c._zcFlat = c._zcText.replace(/ /g, ''); // «بیارزشی» بدون فاصله هم پیدا شود
    });

    function apply() {
      var terms = state.q ? state.q.split(' ') : [];
      var total = 0;
      sections.forEach(function (sec) {
        var inFilter = state.filter === 'all' || sec.getAttribute('data-zc-sc-sec') === state.filter;
        var secCount = 0;
        if (inFilter) {
          $$('[data-zc-sc-sub]', sec).forEach(function (sub) {
            var subCount = 0;
            $$('[data-zc-sc-card]', sub).forEach(function (card) {
              var ok = !terms.length || card._zcText.indexOf(state.q) !== -1 ||
                card._zcFlat.indexOf(state.q.replace(/ /g, '')) !== -1 ||
                terms.every(function (t) { return card._zcText.indexOf(t) !== -1; });
              card.hidden = !ok;
              if (ok) { subCount++; }
            });
            sub.hidden = subCount === 0;
            secCount += subCount;
          });
        }
        sec.hidden = !inFilter || secCount === 0;
        total += inFilter ? secCount : 0;
      });
      if (empty) { empty.hidden = total !== 0; }
      if (status) {
        var tpl = status.getAttribute('data-tpl') || '%s';
        status.textContent = state.q ? tpl.replace('%s', String(total).replace(/\d/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'[d]; })) : '';
      }
    }

    function setFilter(value, fromUser) {
      state.filter = value;
      chips.forEach(function (chip) {
        var on = chip.getAttribute('data-zc-sc-filter') === value;
        chip.classList.toggle('is-active', on);
        chip.setAttribute('aria-pressed', on ? 'true' : 'false');
      });
      apply();
      if (fromUser && window.history && history.replaceState) {
        var sec = value === 'all' ? null : $('[data-zc-sc-sec="' + value + '"]', hub);
        var hash = sec && sec.id ? '#' + sec.id : ' ';
        try { history.replaceState(null, '', hash === ' ' ? location.pathname + location.search : hash); } catch (e) { /* noop */ }
      }
    }

    chips.forEach(function (chip) {
      chip.addEventListener('click', function () { setFilter(chip.getAttribute('data-zc-sc-filter'), true); });
    });

    if (input) {
      input.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(function () { state.q = normFa(input.value); apply(); }, 120);
      });
      input.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { input.value = ''; state.q = ''; apply(); }
      });
    }

    // #ems / #modes / #coping / #distortions → فعال‌سازی همان دسته.
    if (location.hash && chips.length) {
      var target = $('[data-zc-sc-sec]' + location.hash.replace(/[^#\w-]/g, ''), hub);
      if (target) { setFilter(target.getAttribute('data-zc-sc-sec'), false); }
    }
  }

  function initSchemaHubs(root) {
    $$('[data-zc-sc]', root || doc).forEach(initSchemaHub);
  }

  /* ---------------------------------------------------------------- *
   * ویرایشگر المنتور: اجرای دوباره‌ی اسکریپت‌ها پس از هر بار رندر ویجت
   * ---------------------------------------------------------------- */
  function initElementorHooks() {
    var bound = false;
    function bind() {
      if (bound || !window.elementorFrontend || !window.elementorFrontend.hooks) { return; }
      bound = true;
      window.elementorFrontend.hooks.addAction('frontend/element_ready/global', function ($scope) {
        var el = $scope && $scope[0] ? $scope[0] : null;
        if (!el) { return; }
        initSliders(el);
        initTocs(el);
        initSchemaHubs(el);
        $$('.zc-reveal', el).forEach(function (r) { r.classList.add('is-in'); });
      });
    }
    bind();
    if (!bound) {
      window.addEventListener('elementor/frontend/init', bind);
      if (window.jQuery) { window.jQuery(window).on('elementor/frontend/init', bind); }
    }
  }

  /* ---------------------------------------------------------------- *
   * حالت تاریک
   * ---------------------------------------------------------------- */
  function initDarkMode() {
    var mode = config.darkMode || 'toggle';
    if (mode === 'off') { return; }

    $$('[data-zc-theme-toggle]').forEach(function (btn) {
      function sync() {
        var dark = html.getAttribute('data-theme') === 'dark';
        btn.setAttribute('aria-pressed', dark ? 'true' : 'false');
        var label = dark ? (config.i18n && config.i18n.light) || 'حالت روشن' : (config.i18n && config.i18n.dark) || 'حالت تاریک';
        btn.setAttribute('title', label);
        btn.setAttribute('aria-label', label);
      }

      btn.addEventListener('click', function () {
        var dark = html.getAttribute('data-theme') === 'dark';
        var next = dark ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        try { localStorage.setItem('zc-theme', next); } catch (e) {}
        sync();
      });

      if (mode === 'auto') {
        var mq = window.matchMedia('(prefers-color-scheme: dark)');
        var onScheme = function (e) { html.setAttribute('data-theme', e.matches ? 'dark' : 'light'); sync(); };
        if (mq.addEventListener) { mq.addEventListener('change', onScheme); }
      }

      sync();
    });
  }

  /* ---------------------------------------------------------------- *
   * اسکرول نرم برای لینک‌های داخلی
   * ---------------------------------------------------------------- */
  function initSmoothAnchors() {
    if (config.smoothScroll === '0') { return; }

    doc.addEventListener('click', function (e) {
      var link = e.target.closest && e.target.closest('a[href^="#"]');
      if (!link) { return; }

      var href = link.getAttribute('href');
      if (!href || href === '#' || href.length < 2) { return; }

      var id = href.slice(1);
      try { id = decodeURIComponent(id); } catch (err) { /* شناسه‌ی خام */ }
      var target = doc.getElementById(id);
      if (!target) { return; }

      e.preventDefault();
      scrollToTarget(target, href);
    });
  }

  /* ---------------------------------------------------------------- *
   * بارگذاری تنبل iframeها (نقشه، ویدیو و ...)
   * ---------------------------------------------------------------- */
  function initLazyFrames() {
    var frames = $$('iframe[data-src], [data-zc-lazy-src]');
    if (!frames.length) { return; }

    if (!supportsIO) {
      frames.forEach(function (f) {
        f.src = f.getAttribute('data-src') || f.getAttribute('data-zc-lazy-src') || '';
      });
      return;
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) { return; }
        var el = entry.target;
        var src = el.getAttribute('data-src') || el.getAttribute('data-zc-lazy-src');
        if (src) { el.src = src; }
        io.unobserve(el);
      });
    }, { rootMargin: '250px 0px' });

    frames.forEach(function (f) { io.observe(f); });
  }

  /* ---------------------------------------------------------------- *
   * پیش‌بارگذاری هوشمند صفحات (Instant Pages)
   * ---------------------------------------------------------------- */
  function initInstantPages() {
    if (config.instantPages === '0') { return; }
    // مرورگرهای مدرن: قوانین حدس‌زنی بومی وردپرس (speculationrules) کار را انجام می‌دهند.
    if (window.HTMLScriptElement && HTMLScriptElement.supports && HTMLScriptElement.supports('speculationrules')) { return; }
    if ('connection' in navigator && navigator.connection && (navigator.connection.saveData || /2g/.test(navigator.connection.effectiveType || ''))) { return; }

    var prefetched = {};

    function eligible(a) {
      if (!a || !a.href || a.target === '_blank' || a.hasAttribute('download')) { return false; }
      if (a.origin !== window.location.origin || a.hash && a.pathname === window.location.pathname) { return false; }
      return !/\/wp-(admin|login)|\?|\.(pdf|zip|jpe?g|png|webp)$/i.test(a.href);
    }

    function prefetch(url) {
      if (prefetched[url]) { return; }
      var link = doc.createElement('link');
      link.rel = 'prefetch';
      link.href = url;
      doc.head.appendChild(link);
      prefetched[url] = true;
    }

    // فقط با نشانه‌ی قصد کاربر (هاور روی دسکتاپ، لمس روی موبایل)؛ بدون پیش‌بارگذاری کور لینک‌های صفحه.
    var hoverTimer = null;
    doc.addEventListener('mouseover', function (e) {
      var a = e.target.closest && e.target.closest('a');
      if (!eligible(a)) { return; }
      hoverTimer = window.setTimeout(function () { prefetch(a.href); }, 65);
    }, { passive: true });

    doc.addEventListener('mouseout', function () {
      if (hoverTimer) { window.clearTimeout(hoverTimer); hoverTimer = null; }
    }, { passive: true });

    doc.addEventListener('touchstart', function (e) {
      var a = e.target.closest && e.target.closest('a');
      if (eligible(a)) { prefetch(a.href); }
    }, { passive: true });
  }

  /* ---------------------------------------------------------------- *
   * فرم تماس داخلی (AJAX)
   * ---------------------------------------------------------------- */
  function initForms() {
    $$('form[data-zc-form]').forEach(function (form) {
      var msg = $('[data-zc-form-msg]', form);

      form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (msg) {
          msg.className = 'zc-form-msg';
          msg.textContent = '';
        }

        var submit = $('[type="submit"]', form);
        var original = submit ? submit.textContent : '';
        if (submit) {
          submit.disabled = true;
          submit.textContent = (config.i18n && config.i18n.sending) || 'در حال ارسال…';
        }

        var data = new FormData(form);

        fetch((config.ajaxUrl || '/wp-admin/admin-ajax.php'), {
          method: 'POST',
          credentials: 'same-origin',
          body: data
        })
          .then(function (r) { return r.json(); })
          .then(function (res) {
            if (msg) {
              msg.textContent = (res && res.data && res.data.message) || ((config.i18n && config.i18n.error) || 'خطا');
              msg.className = 'zc-form-msg ' + (res && res.success ? 'is-ok' : 'is-err');
            }
            if (res && res.success) { form.reset(); }
          })
          .catch(function () {
            if (msg) {
              msg.textContent = (config.i18n && config.i18n.error) || 'خطا';
              msg.className = 'zc-form-msg is-err';
            }
          })
          .then(function () {
            if (submit) {
              submit.disabled = false;
              submit.textContent = original;
            }
          });
      });
    });
  }

  /* ---------------------------------------------------------------- *
   * نمایش/مخفی کردن سربرگ جستجو
   * ---------------------------------------------------------------- */
  function initSearch() {
    var toggles = $$('[data-zc-search-toggle]');
    if (!toggles.length) { return; }

    toggles.forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        var panel = $('[data-zc-search-panel]');
        if (!panel) { return; }
        var opened = panel.classList.toggle('is-open');
        if (opened) {
          var input = $('input', panel);
          if (input) { input.focus(); }
        }
      });
    });

    doc.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        var panel = $('[data-zc-search-panel]');
        if (panel) { panel.classList.remove('is-open'); }
      }
    });
  }

  /* ---------------------------------------------------------------- *
   * همگام‌سازی خودکار ارتفاع ستون‌های کارت‌ها (اختیاری)
   * ---------------------------------------------------------------- */
  function initEqualHeights() {
    $$('[data-zc-equal]').forEach(function (group) {
      var items = $$(':scope > *', group);
      if (!items.length) { return; }
      function reset() {
        items.forEach(function (el) { el.style.minHeight = ''; });
      }
      function align() {
        reset();
        var max = 0;
        items.forEach(function (el) { max = Math.max(max, el.offsetHeight); });
        if (max > 0) { items.forEach(function (el) { el.style.minHeight = max + 'px'; }); }
      }
      align();
      window.addEventListener('resize', align);
    });
  }

  /* ---------------------------------------------------------------- *
   * فهرست صفحات قوانین (بسته در موبایل) + دکمه شناور ارتباط
   * ---------------------------------------------------------------- */
  function initLegalToc() {
    var tocs = $$('[data-zc-legal-toc]');
    if (!tocs.length) { return; }
    if (window.matchMedia('(max-width: 1023px)').matches) {
      tocs.forEach(function (d) { d.removeAttribute('open'); });
    }
    tocs.forEach(function (d) {
      $$('a[href^="#"]', d).forEach(function (a) {
        a.addEventListener('click', function () {
          if (window.matchMedia('(max-width: 1023px)').matches) { d.removeAttribute('open'); }
        });
      });
    });
  }

  function initFloat() {
    var box = $('[data-zc-float]');
    if (!box) { return; }
    doc.addEventListener('click', function (e) {
      if (box.open && !box.contains(e.target)) { box.removeAttribute('open'); }
    });
    doc.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && box.open) { box.removeAttribute('open'); $('summary', box).focus(); }
    });
  }

  /* ---------------------------------------------------------------- *
   * رزومه: چاپ، فیلتر دوره‌ها، نوار بخش‌ها (ثابت‌شونده + بخش فعال)
   * ---------------------------------------------------------------- */
  function initResume() {
    doc.addEventListener('click', function (e) {
      var btn = e.target.closest && e.target.closest('[data-zc-print]');
      if (!btn) { return; }
      e.preventDefault();
      $$('.zc-reveal').forEach(function (el) { el.classList.add('is-in'); });
      window.print();
    });

    $$('[data-zc-filter-scope]').forEach(function (scope) {
      var buttons = $$('[data-zc-filter]', scope);
      var items = $$('[data-zc-filter-item]', scope);
      buttons.forEach(function (button) {
        button.addEventListener('click', function () {
          var value = button.getAttribute('data-zc-filter');
          buttons.forEach(function (b) {
            var on = b === button;
            b.classList.toggle('is-active', on);
            b.setAttribute('aria-pressed', on ? 'true' : 'false');
          });
          items.forEach(function (item) {
            var cat = item.getAttribute('data-zc-filter-item');
            var show = value === '*' || cat === value || cat === '*';
            item.hidden = !show;
            if (show) {
              item.classList.remove('is-fade');
              void item.offsetWidth;
              item.classList.add('is-fade');
            }
          });
        });
      });
    });

    var nav = $('[data-zc-resume-nav]');
    if (!nav) { return; }

    var links = $$('a[href^="#"]', nav);
    var targets = links.map(function (a) { return doc.getElementById(a.getAttribute('href').slice(1)); });
    var header = $('[data-zc-header]');
    var sticky = nav.classList.contains('is-sticky');
    var holder = null;
    var fixed = false;
    var active = null;
    var ticking = false;

    if (sticky) {
      holder = doc.createElement('div');
      holder.setAttribute('aria-hidden', 'true');
      nav.parentNode.insertBefore(holder, nav);
    }

    // والدهایی که «زمینه‌ی پشته‌ای» می‌سازند (مثل isolation در تم سرمه‌ای) z-index ناوبری ثابت را محبوس
    // می‌کنند و بخش‌های بعدی روی آن رسم می‌شوند؛ هنگام ثابت‌شدن، این والدها را بالاتر می‌بریم.
    var hosts = [];
    if (sticky) {
      for (var el = nav.parentElement; el && el !== doc.body; el = el.parentElement) {
        var cs = window.getComputedStyle(el);
        if (cs.isolation === 'isolate' || cs.zIndex !== 'auto') { hosts.push(el); }
      }
    }
    function raiseHosts(on) {
      hosts.forEach(function (h) { h.classList.toggle('zc-resume-nav-host', on); });
    }

    function headerBottom() {
      if (!header) { return 0; }
      var r = header.getBoundingClientRect();
      return Math.max(0, r.bottom);
    }

    function update() {
      ticking = false;
      var hb = headerBottom();
      var navH = nav.offsetHeight;
      var last = null;
      for (var i = targets.length - 1; i >= 0; i--) { if (targets[i]) { last = targets[i]; break; } }

      if (sticky && holder) {
        var ref = holder.getBoundingClientRect().top;
        var past = last ? last.getBoundingClientRect().bottom < hb + navH + 40 : false;
        if (ref <= hb && !past) {
          if (!fixed) { holder.style.height = navH + 'px'; nav.classList.add('is-fixed'); raiseHosts(true); fixed = true; }
          nav.style.top = hb + 'px';
        } else if (fixed) {
          nav.classList.remove('is-fixed'); raiseHosts(false); nav.style.top = ''; holder.style.height = ''; fixed = false;
        }
      }

      var line = hb + navH + 60;
      var current = null;
      targets.forEach(function (t, idx) {
        if (t && t.getBoundingClientRect().top <= line) { current = links[idx]; }
      });
      if (current !== active) {
        links.forEach(function (a) { a.classList.toggle('is-active', a === current); if (a === current) { a.setAttribute('aria-current', 'true'); } else { a.removeAttribute('aria-current'); } });
        active = current;
        if (current && fixed) {
          var ul = current.closest('ul');
          if (ul) {
            var cr = current.getBoundingClientRect();
            var ur = ul.getBoundingClientRect();
            if (cr.left < ur.left || cr.right > ur.right) {
              ul.scrollBy({ left: (cr.left + cr.width / 2) - (ur.left + ur.width / 2), behavior: reduceMotion ? 'auto' : 'smooth' });
            }
          }
        }
      }
    }

    window.addEventListener('scroll', function () {
      if (!ticking) { window.requestAnimationFrame(update); ticking = true; }
    }, { passive: true });
    window.addEventListener('resize', update);
    update();
  }

  /* ---------------------------------------------------------------- *
   * v1.5 — فهرست مطالب: ساخت از تیترهای صفحه، جمع‌شدن، بخش فعال، پیشرفت، دکمه شناور
   * ---------------------------------------------------------------- */
  var FA = '۰۱۲۳۴۵۶۷۸۹';
  function faNum(v) { return String(v).replace(/\d/g, function (d) { return FA[d]; }); }
  function headerH() { return parseInt(getComputedStyle(html).getPropertyValue('--zc-header-h'), 10) || 76; }
  function isEditor() { return doc.body.classList.contains('elementor-editor-active') || /elementor-preview/.test(location.search); }

  function tocSlug(text, used) {
    var s = text.trim().replace(/[۰-۹]/g, function (d) { return String(FA.indexOf(d)); });
    try {
      s = s.replace(/[^\p{L}\p{N}\u200C\s_-]+/gu, '');
    } catch (err) {
      s = s.replace(/[!-,./:-@[-^`{-~«»؟،؛]+/g, '');
    }
    s = s.replace(/[\s_-]+/g, '-').toLowerCase().replace(/^-+|-+$/g, '').slice(0, 60).replace(/-+$/, '');
    if (!s || /^\d/.test(s)) { s = ('section-' + s).replace(/-+$/, ''); }
    var base = s, i = 2;
    while (used[s] || doc.getElementById(s)) { s = base + '-' + i; i++; }
    used[s] = 1;
    return s;
  }

  function tocListHTML(items, numbering) {
    // درخت بر اساس سطح تیتر (همانند نسخه‌ی PHP)
    var root = [], stack = [];
    items.forEach(function (it) {
      var node = { level: it.level, id: it.id, text: it.text, children: [] };
      while (stack.length && stack[stack.length - 1].level >= node.level) { stack.pop(); }
      if (!stack.length) { root.push(node); } else { stack[stack.length - 1].children.push(node); }
      stack.push(node);
    });
    function esc(t) { var d = doc.createElement('div'); d.textContent = t; return d.innerHTML; }
    function render(nodes, prefix, depth) {
      var out = '<ol class="' + (depth ? 'zc-toc-sub' : 'zc-toc-list') + '">';
      nodes.forEach(function (n, idx) {
        var number = prefix ? prefix + '.' + (idx + 1) : String(idx + 1);
        var label = '';
        if (numbering === 'hierarchical') { label = faNum(number); }
        else if (numbering !== 'none' && !depth) { label = faNum(idx + 1 < 10 ? '0' + (idx + 1) : idx + 1); }
        out += '<li class="zc-toc-item"><a class="zc-toc-link' + (depth ? ' is-sub' : '') + '" href="#' + esc(n.id).replace(/"/g, '&quot;') + '" data-zc-toc-target="' + esc(n.id).replace(/"/g, '&quot;') + '">';
        out += label ? '<span class="zc-toc-num" aria-hidden="true">' + label + '</span>' : '<span class="zc-toc-dot" aria-hidden="true"></span>';
        out += '<span class="zc-toc-text">' + esc(n.text) + '</span></a>';
        if (n.children.length) { out += render(n.children, number, depth + 1); }
        out += '</li>';
      });
      return out + '</ol>';
    }
    return render(root, '', 0);
  }

  function tocScan(nav) {
    var sel = nav.getAttribute('data-zc-toc-scan');
    var levels = (nav.getAttribute('data-zc-toc-levels') || '2-3').split('-');
    var min = parseInt(levels[0], 10) || 2;
    var max = parseInt(levels[1] || levels[0], 10) || min;
    var roots = [];
    try { roots = $$(sel); } catch (err) { roots = []; }
    roots = roots.filter(function (r) { return !roots.some(function (o) { return o !== r && o.contains(r); }); });

    var seen = [], words = 0;
    roots.forEach(function (r) {
      words += (r.textContent || '').trim().split(/\s+/).length;
      $$('h1,h2,h3,h4,h5,h6', r).forEach(function (h) {
        var lv = parseInt(h.tagName.charAt(1), 10);
        if (lv < min || lv > max || seen.indexOf(h) > -1) { return; }
        if (h.closest('.zc-toc, .zc-toc-fab, .zc-header, .zc-footer, footer, [aria-hidden="true"], .zc-more-read') || h.matches('.zc-toc-skip, .no-toc')) { return; }
        if (!(h.textContent || '').trim()) { return; }
        seen.push(h);
      });
    });
    seen.sort(function (a, b) { return a.compareDocumentPosition(b) & 4 ? -1 : 1; });

    var used = {};
    var items = seen.map(function (h) {
      if (!h.id) { h.id = tocSlug(h.textContent, used); } else { used[h.id] = 1; }
      return { level: parseInt(h.tagName.charAt(1), 10), id: h.id, text: h.textContent.trim().replace(/\s+/g, ' ') };
    });
    return { items: items, minutes: Math.max(1, Math.ceil(words / 220)) };
  }

  var tocRegistry = [];
  var tocFab = null;

  function tocSetOpen(nav, open) {
    nav.classList.toggle('is-open', open);
    var btn = $('[data-zc-toc-toggle]', nav);
    if (!btn) { return; }
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    var label = $('.zc-toc-toggle-label', btn);
    if (label) { label.textContent = btn.getAttribute(open ? 'data-label-open' : 'data-label-closed'); }
  }

  function initToc(nav) {
    if (nav.__zcToc) { return; }
    nav.__zcToc = true;

    // ویجت مستقل: فهرست از تیترهای صفحه ساخته می‌شود.
    if (nav.hasAttribute('data-zc-toc-scan')) {
      var res = tocScan(nav);
      var need = parseInt(nav.getAttribute('data-zc-toc-min'), 10) || 1;
      var inner = $('.zc-toc-inner', nav);
      if (res.items.length < need) {
        if (!isEditor()) { nav.hidden = true; return; }
      } else if (inner) {
        inner.innerHTML = tocListHTML(res.items, nav.getAttribute('data-zc-toc-numbering') || 'decimal');
        var meta = $('[data-zc-toc-meta]', nav);
        if (meta) {
          meta.textContent = [
            (meta.getAttribute('data-tpl-count') || '%s').replace('%s', faNum(res.items.length)),
            (meta.getAttribute('data-tpl-read') || '%s').replace('%s', faNum(res.minutes))
          ].join(' · ');
        }
      }
    }

    var mobile = window.matchMedia('(max-width: 1023px)').matches;
    if (nav.getAttribute('data-zc-toc-start') === 'closed' || (mobile && nav.getAttribute('data-zc-toc-mobile') === 'closed')) {
      tocSetOpen(nav, false);
    }
    nav.classList.add('zc-toc-ready');

    var btn = $('[data-zc-toc-toggle]', nav);
    if (btn) {
      btn.addEventListener('click', function () { tocSetOpen(nav, !nav.classList.contains('is-open')); });
    }
    if (nav.getAttribute('data-zc-toc-mobile') === 'closed') {
      nav.addEventListener('click', function (e) {
        if (e.target.closest('a') && window.matchMedia('(max-width: 1023px)').matches) { tocSetOpen(nav, false); }
      });
    }

    var links = $$('[data-zc-toc-target]', nav);
    if (!links.length) { return; }
    var entry = {
      nav: nav,
      links: links,
      targets: links.map(function (a) { return doc.getElementById(a.getAttribute('data-zc-toc-target')); }),
      article: nav.closest('[data-zc-article]') || $('[data-zc-article]'),
      active: null
    };
    tocRegistry.push(entry);

    if (nav.getAttribute('data-zc-toc-float') === '1' && !tocFab && !isEditor()) {
      tocBuildFab(entry);
    }
    tocUpdate();
  }

  function tocBuildFab(entry) {
    var list = $('.zc-toc-list', entry.nav);
    if (!list) { return; }
    var title = $('.zc-toc-title', entry.nav);
    var meta = $('.zc-toc-meta', entry.nav);
    var icon = $('.zc-toc-icon svg', entry.nav);
    var wrap = doc.createElement('div');
    wrap.className = 'zc-toc-fab';
    wrap.innerHTML = '<div class="zc-toc-fab-panel" role="dialog"><div class="zc-toc-fab-head"><span></span><span></span></div></div>' +
      '<button type="button" class="zc-toc-fab-btn" aria-expanded="false"></button>';
    var panel = $('.zc-toc-fab-panel', wrap);
    var head = $$('.zc-toc-fab-head span', wrap);
    var fabBtn = $('.zc-toc-fab-btn', wrap);
    head[0].textContent = title ? title.textContent : '';
    head[1].textContent = meta ? meta.textContent.split(' · ')[0] : '';
    panel.setAttribute('aria-label', head[0].textContent);
    fabBtn.setAttribute('aria-label', head[0].textContent);
    if (icon) { fabBtn.appendChild(icon.cloneNode(true)); }
    var clone = list.cloneNode(true);
    panel.appendChild(clone);
    doc.body.appendChild(wrap);

    function setOpen(open) {
      wrap.classList.toggle('is-open', open);
      fabBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
    fabBtn.addEventListener('click', function () { setOpen(!wrap.classList.contains('is-open')); });
    panel.addEventListener('click', function (e) { if (e.target.closest('a')) { setOpen(false); } });
    doc.addEventListener('click', function (e) { if (wrap.classList.contains('is-open') && !wrap.contains(e.target)) { setOpen(false); } });
    doc.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && wrap.classList.contains('is-open')) { setOpen(false); fabBtn.focus(); }
    });

    entry.links = entry.links.concat($$('[data-zc-toc-target]', clone));
    entry.targets = entry.links.map(function (a) { return doc.getElementById(a.getAttribute('data-zc-toc-target')); });
    tocFab = { el: wrap, entry: entry, close: function () { setOpen(false); } };
  }

  var tocTicking = false;
  function tocUpdate() {
    tocTicking = false;
    var line = headerH() + 90;
    var vh = window.innerHeight;
    tocRegistry.forEach(function (en) {
      var current = null;
      en.targets.forEach(function (t) {
        if (t && t.getBoundingClientRect().top <= line) { current = t.id; }
      });
      if (current !== en.active) {
        en.active = current;
        en.links.forEach(function (a) {
          var on = a.getAttribute('data-zc-toc-target') === current;
          a.classList.toggle('is-active', on);
          if (on) { a.setAttribute('aria-current', 'location'); } else { a.removeAttribute('aria-current'); }
        });
      }

      // پیشرفت مطالعه‌ی محدوده‌ی فهرست
      var first = en.targets[0];
      var region = en.article;
      if (!region && first) { region = first.closest('.zc-prose, .elementor-widget-text-editor, #zc-main') || first.parentElement; }
      if (!region || !first) { return; }
      var rr = region.getBoundingClientRect();
      var start = first.getBoundingClientRect().top - line;
      var end = rr.bottom - vh * 0.6;
      var p = start >= 0 ? 0 : Math.min(1, Math.max(0, -start / Math.max(1, end - start)));
      en.nav.style.setProperty('--zc-toc-p', p.toFixed(3));

      if (tocFab && tocFab.entry === en) {
        var past = en.nav.getBoundingClientRect().bottom < line - 90; // پشت سربرگ رفته
        var show = past && rr.bottom > vh * 0.35;
        tocFab.el.classList.toggle('is-visible', show);
        tocFab.el.style.setProperty('--zc-toc-p', p.toFixed(3));
        if (!show) { tocFab.close(); }
      }
    });
  }

  function initTocs(root) {
    $$('[data-zc-toc]', root || doc).forEach(initToc);
  }

  window.addEventListener('scroll', function () {
    if (tocRegistry.length && !tocTicking) { tocTicking = true; window.requestAnimationFrame(tocUpdate); }
  }, { passive: true });
  window.addEventListener('resize', function () { if (tocRegistry.length) { tocUpdate(); } });

  /* ---------------------------------------------------------------- *
   * v1.5 — نوار پیشرفت مطالعه
   * ---------------------------------------------------------------- */
  function initReadProgress() {
    var bar = $('[data-zc-progress]');
    var article = $('[data-zc-article]');
    if (!bar || !article) { return; }
    var ticking = false;
    function update() {
      ticking = false;
      var r = article.getBoundingClientRect();
      var total = r.height - window.innerHeight * 0.5;
      var p = Math.min(1, Math.max(0, (headerH() - r.top) / Math.max(1, total)));
      bar.style.setProperty('--zc-read', p.toFixed(4));
    }
    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; window.requestAnimationFrame(update); }
    }, { passive: true });
    window.addEventListener('resize', update);
    update();
  }

  /* ---------------------------------------------------------------- *
   * v1.5 — اشتراک‌گذاری: کپی پیوند، اشتراک دستگاه، پیام کوتاه
   * ---------------------------------------------------------------- */
  var toastTimer = null;
  function zcToast(msg) {
    var t = $('.zc-toast');
    if (!t) {
      t = doc.createElement('div');
      t.className = 'zc-toast';
      t.setAttribute('role', 'status');
      t.setAttribute('aria-live', 'polite');
      doc.body.appendChild(t);
    }
    t.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg><span></span>';
    t.lastChild.textContent = msg;
    t.classList.add('is-in');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { t.classList.remove('is-in'); }, 2200);
  }

  function copyText(text) {
    function legacy() {
      return new Promise(function (resolve, reject) {
        var ta = doc.createElement('textarea');
        ta.value = text;
        ta.setAttribute('readonly', '');
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        doc.body.appendChild(ta);
        ta.select();
        var ok = false;
        try { ok = doc.execCommand('copy'); } catch (err) { ok = false; }
        doc.body.removeChild(ta);
        if (ok) { resolve(); } else { reject(new Error('copy')); }
      });
    }
    if (navigator.clipboard && window.isSecureContext) { return navigator.clipboard.writeText(text).catch(legacy); }
    return legacy();
  }

  function initShare() {
    if (navigator.share) {
      $$('[data-zc-share-native-item]').forEach(function (li) { li.hidden = false; });
    }
    doc.addEventListener('click', function (e) {
      var copy = e.target.closest && e.target.closest('[data-zc-share-copy]');
      if (copy) {
        e.preventDefault();
        copyText(copy.getAttribute('data-zc-share-copy')).then(function () {
          zcToast(copy.getAttribute('data-zc-done') || 'OK');
        }, function () { window.prompt('', copy.getAttribute('data-zc-share-copy')); });
        return;
      }
      var nat = e.target.closest && e.target.closest('[data-zc-share-native]');
      if (nat && navigator.share) {
        e.preventDefault();
        navigator.share({ title: nat.getAttribute('data-title') || doc.title, url: nat.getAttribute('data-url') || location.href }).catch(function () {});
      }
    });
  }

  /* ---------------------------------------------------------------- *
   * v1.5 — «مطالب بیشتر» در ویجت نوشته‌ها (بدون بارگذاری مجدد صفحه)
   * ---------------------------------------------------------------- */
  function initLoadMore() {
    doc.addEventListener('click', function (e) {
      var btn = e.target.closest && e.target.closest('[data-zc-loadmore]');
      if (!btn || !window.fetch || !window.DOMParser || btn.classList.contains('is-loading')) { return; }
      var uid = btn.getAttribute('data-zc-loadmore');
      var grid = $('[data-zc-posts-grid="' + uid + '"]');
      if (!grid) { return; }
      e.preventDefault();
      var label = $('.zc-loadmore-label', btn);
      var orig = label ? label.textContent : '';
      btn.classList.add('is-loading');
      btn.setAttribute('aria-busy', 'true');
      if (label) { label.textContent = btn.getAttribute('data-zc-loading') || orig; }

      fetch(btn.href, { credentials: 'same-origin' }).then(function (r) {
        if (!r.ok) { throw new Error(r.status); }
        return r.text();
      }).then(function (text) {
        var d = new DOMParser().parseFromString(text, 'text/html');
        var src = d.querySelector('[data-zc-posts-grid="' + uid + '"]');
        var firstNew = null;
        if (src) {
          Array.prototype.slice.call(src.children).forEach(function (child) {
            var node = doc.importNode(child, true);
            $$('.zc-reveal', node).concat(node.classList && node.classList.contains('zc-reveal') ? [node] : []).forEach(function (r) { r.classList.add('is-in'); });
            grid.appendChild(node);
            if (!firstNew) { firstNew = node; }
          });
          grid.hidden = false;
        }
        var next = d.querySelector('[data-zc-loadmore="' + uid + '"]');
        if (next) {
          btn.href = next.getAttribute('href');
          btn.classList.remove('is-loading');
          btn.removeAttribute('aria-busy');
          if (label) { label.textContent = orig; }
        } else {
          (btn.parentElement || btn).remove();
        }
        var focusable = firstNew && firstNew.querySelector('a[href]');
        if (focusable) { focusable.focus({ preventScroll: true }); }
      }).catch(function () {
        window.location.href = btn.href;
      });
    });
  }

  /* ---------------------------------------------------------------- *
   * اجرا
   * ---------------------------------------------------------------- */
  onReady(function () {
    initDrawer();
    initHeader();
    initFontCookie();
    initBackToTop();
    initReveal();
    initCounters();
    initAccordions();
    initSliders();
    initElementorHooks();
    initDarkMode();
    initSmoothAnchors();
    initLazyFrames();
    initForms();
    initSearch();
    initEqualHeights();
    initLegalToc();
    initFloat();
    initResume();
    initTocs();
    initSchemaHubs();
    initReadProgress();
    initShare();
    initLoadMore();
    initInstantPages();
  });
})();
