/**
 * ZarinCoach — Tailwind configuration
 * قالب وردپرس کوچینگ و توسعه فردی — ویژه مریم جمالی (Maryam-Jamali.ir)
 * ساخته شده در زرین‌کد (Zarincode.com)
 *
 * نکته: رنگ‌ها از متغیرهای CSS خوانده می‌شوند تا پنل تنظیمات (Redux)
 * بتواند به صورت زنده پالت رنگی سایت را تغییر دهد.
 */

const path = require('path');

/** @type {import('tailwindcss').Config} */
module.exports = {
  // مسیرها نسبت به همین فایل محاسبه می‌شوند تا نام پوشه‌ی قالب اهمیتی نداشته باشد.
  content: [
    path.join(__dirname, '*.php'),
    path.join(__dirname, 'template-parts/**/*.php'),
    path.join(__dirname, 'inc/*.php'),
    path.join(__dirname, 'inc/elementor/**/*.php'),
    path.join(__dirname, 'assets/js/**/*.js'),
  ],
  // شبکه‌ی ایمنی: کلاس‌های اختصاصی قالب حتی اگر در زمان ساخت شناسایی نشوند، حفظ می‌شوند.
  // (بدون variants برای جلوگیری از افزایش حجم خروجی)
  safelist: [{ pattern: /^zc-/ }],
  darkMode: ['class', '[data-theme="dark"]'],
  theme: {
    extend: {
      colors: {
        primary: 'rgb(var(--zc-primary-rgb) / <alpha-value>)',
        secondary: 'rgb(var(--zc-secondary-rgb) / <alpha-value>)',
        accent: 'rgb(var(--zc-accent-rgb) / <alpha-value>)',
        ink: 'rgb(var(--zc-ink-rgb) / <alpha-value>)',
        muted: 'rgb(var(--zc-muted-rgb) / <alpha-value>)',
        base: 'rgb(var(--zc-base-rgb) / <alpha-value>)',
        surface: 'rgb(var(--zc-surface-rgb) / <alpha-value>)',
        surface2: 'rgb(var(--zc-surface2-rgb) / <alpha-value>)',
        line: 'rgb(var(--zc-line-rgb) / <alpha-value>)',
        info: 'rgb(var(--zc-info-rgb) / <alpha-value>)',
      },
      fontFamily: {
        // «Arad VF» عمداً در زنجیره‌ی جایگزین تکرار نمی‌شود: پوشش نویسه‌ی دو نسخه یکسان است و
        // تکرارش باعث دانلود بی‌فایده‌ی فایل دوم (~۴۷KB) برای نویسه‌هایی مثل «—» و «…» می‌شد.
        sans: ['var(--zc-font, "Arad VF")', 'Tahoma', 'system-ui', 'sans-serif'],
        display: ['var(--zc-font, "Arad VF")', 'Tahoma', 'serif'],
      },
      fontSize: {
        '2xs': ['0.6875rem', { lineHeight: '1.6' }],
        display: ['clamp(2.5rem, 6.4vw, 5rem)', { lineHeight: '1.12', letterSpacing: '0' }],
        h1: ['clamp(2.1rem, 4.6vw, 3.35rem)', { lineHeight: '1.2', letterSpacing: '0' }],
        h2: ['clamp(1.75rem, 3.4vw, 2.5rem)', { lineHeight: '1.26', letterSpacing: '0' }],
        h3: ['clamp(1.3rem, 2.2vw, 1.65rem)', { lineHeight: '1.35', letterSpacing: '0' }],
      },
      maxWidth: {
        container: '1200px',
        narrow: '760px',
        prose: '68ch',
      },
      borderRadius: {
        xl2: '1.25rem',
        '3xl': '1.75rem',
        '4xl': '2.25rem',
      },
      boxShadow: {
        soft: '0 1px 2px rgba(15,23,42,.04), 0 8px 30px -12px rgba(15,23,42,.14)',
        lift: '0 2px 4px rgba(15,23,42,.04), 0 22px 48px -22px rgba(15,23,42,.28)',
        gold: '0 18px 44px -20px rgb(var(--zc-primary-rgb) / .55)',
        inset: 'inset 0 1px 0 rgba(255,255,255,.6)',
      },
      spacing: {
        18: '4.5rem',
        22: '5.5rem',
        30: '7.5rem',
      },
      transitionTimingFunction: {
        soft: 'cubic-bezier(.22,.61,.36,1)',
        spring: 'cubic-bezier(.34,1.56,.64,1)',
      },
      keyframes: {
        'zc-up': {
          '0%': { opacity: '0', transform: 'translate3d(0,26px,0)' },
          '100%': { opacity: '1', transform: 'translate3d(0,0,0)' },
        },
        'zc-fade': {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        'zc-float': {
          '0%,100%': { transform: 'translateY(0) rotate(0deg)' },
          '50%': { transform: 'translateY(-14px) rotate(2deg)' },
        },
        'zc-marquee': {
          '0%': { transform: 'translateX(0)' },
          '100%': { transform: 'translateX(-50%)' },
        },
        'zc-ring': {
          '0%': { transform: 'scale(.85)', opacity: '.7' },
          '70%': { transform: 'scale(1.35)', opacity: '0' },
          '100%': { transform: 'scale(1.35)', opacity: '0' },
        },
        'zc-draw': {
          '0%': { strokeDashoffset: '600' },
          '100%': { strokeDashoffset: '0' },
        },
        'zc-blob': {
          '0%,100%': { borderRadius: '46% 54% 62% 38% / 48% 42% 58% 52%', transform: 'rotate(0deg) scale(1)' },
          '50%': { borderRadius: '58% 42% 40% 60% / 55% 58% 42% 45%', transform: 'rotate(12deg) scale(1.06)' },
        },
        'zc-shimmer': {
          '100%': { transform: 'translateX(-200%)' },
        },
      },
      animation: {
        'zc-up': 'zc-up .8s cubic-bezier(.22,.61,.36,1) both',
        'zc-fade': 'zc-fade 1s ease both',
        'zc-float': 'zc-float 7s ease-in-out infinite',
        'zc-marquee': 'zc-marquee 38s linear infinite',
        'zc-ring': 'zc-ring 2.4s cubic-bezier(.22,.61,.36,1) infinite',
        'zc-blob': 'zc-blob 16s ease-in-out infinite',
      },
      backgroundImage: {
        'zc-grain':
          "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='140' height='140'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.85' numOctaves='3'/%3E%3C/filter%3E%3Crect width='140' height='140' filter='url(%23n)' opacity='.42'/%3E%3C/svg%3E\")",
        'zc-dots':
          "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='22' height='22'%3E%3Ccircle cx='2' cy='2' r='1.4' fill='%23000' opacity='.14'/%3E%3C/svg%3E\")",
        'zc-grid':
          "url(\"data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Cpath d='M60 0H0v60' fill='none' stroke='%23000' stroke-opacity='.07' stroke-width='1'/%3E%3C/svg%3E\")",
      },
      typography: () => ({
        DEFAULT: {
          css: {
            '--tw-prose-body': 'rgb(var(--zc-ink-rgb))',
            '--tw-prose-headings': 'rgb(var(--zc-secondary-rgb))',
            '--tw-prose-links': 'rgb(var(--zc-secondary-rgb))',
            '--tw-prose-quotes': 'rgb(var(--zc-muted-rgb))',
            '--tw-prose-bullets': 'rgb(var(--zc-primary-rgb))',
            maxWidth: '68ch',
          },
        },
      }),
    },
  },
  corePlugins: {
    // غیرفعال‌سازی utilهایی که در RTL مشکل‌ساز می‌شوند (به جای آن‌ها از ms/me/start/end استفاده شده)
    space: false,
    divideWidth: false,
    divideStyle: false,
    divideColor: false,
  },
  plugins: [require('@tailwindcss/typography')],
};
