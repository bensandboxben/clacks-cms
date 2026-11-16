/**
 * Tailwind configuration for the ClacksWeb CMS interface.
 *
 * Colours are defined as CSS custom properties (see src/tailwind.css) so that
 * light and dark themes are deliberately designed palettes rather than
 * inverted colours. Dark mode uses Tailwind's class strategy: the `dark`
 * class is set on <html> by the theme script in includes/head.inc.php.
 *
 * Rebuild after changing PHP markup or styles:  npm run build:css
 */
const token = (name) => `rgb(var(--c-${name}) / <alpha-value>)`;

/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    './*.php',
    './includes/**/*.php',
    './js/cms.js',
  ],
  theme: {
    extend: {
      colors: {
        // Clackmannanshire Council blue (#336699) scale
        brand: {
          50: '#f2f6fa',
          100: '#e3ecf5',
          200: '#c7d8ea',
          300: '#9dbbda',
          400: '#6d98c4',
          500: '#4a7cae',
          600: '#336699',
          700: '#2b5580',
          800: '#26476a',
          900: '#233d59',
          950: '#16273a',
        },
        // Semantic theme tokens
        canvas: token('canvas'),
        sidebar: token('sidebar'),
        surface: token('surface'),
        'surface-2': token('surface-2'),
        'surface-3': token('surface-3'),
        raised: token('raised'),
        line: token('line'),
        'line-strong': token('line-strong'),
        field: token('field'),
        'field-line': token('field-line'),
        fg: token('fg'),
        'fg-muted': token('fg-muted'),
        'fg-subtle': token('fg-subtle'),
        primary: token('primary'),
        'primary-hover': token('primary-hover'),
        'primary-fg': token('primary-fg'),
        'primary-soft': token('primary-soft'),
        'primary-text': token('primary-text'),
        ring: token('ring'),
        success: token('success'),
        'success-soft': token('success-soft'),
        'success-line': token('success-line'),
        warning: token('warning'),
        'warning-soft': token('warning-soft'),
        'warning-line': token('warning-line'),
        danger: token('danger'),
        'danger-soft': token('danger-soft'),
        'danger-line': token('danger-line'),
        'danger-solid': token('danger-solid'),
        'danger-solid-hover': token('danger-solid-hover'),
        info: token('info'),
        'info-soft': token('info-soft'),
        'info-line': token('info-line'),
      },
      fontFamily: {
        sans: ['"Inter Variable"', 'Inter', 'ui-sans-serif', 'system-ui', '-apple-system', '"Segoe UI"', 'Roboto', '"Helvetica Neue"', 'Arial', 'sans-serif'],
        mono: ['ui-monospace', 'SFMono-Regular', 'Menlo', 'Consolas', '"Liberation Mono"', 'monospace'],
      },
      fontSize: {
        '2xs': ['0.6875rem', { lineHeight: '1rem' }],
      },
      boxShadow: {
        xs: '0 1px 2px 0 rgb(15 23 42 / 0.05)',
        card: '0 1px 2px 0 rgb(15 23 42 / 0.04), 0 1px 3px 0 rgb(15 23 42 / 0.04)',
        pop: '0 10px 30px -10px rgb(15 23 42 / 0.25), 0 4px 10px -4px rgb(15 23 42 / 0.12)',
      },
      spacing: {
        sidebar: '16rem',
        'sidebar-collapsed': '4rem',
      },
      maxWidth: {
        content: '90rem',
      },
    },
  },
  plugins: [],
};
