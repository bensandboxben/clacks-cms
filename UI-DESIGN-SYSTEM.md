# ClacksWeb CMS interface

The CMS front end uses a Tailwind CSS design system with light and dark themes.
Only presentation changed in the redesign; PHP logic, queries, URLs, form fields
and JavaScript handlers are unchanged.

## No build step on the server

Compiled files are committed, so deploying is still a plain file copy:

| File | What it is |
| --- | --- |
| `css/app.css` | Compiled, minified Tailwind stylesheet (the only CMS stylesheet) |
| `css/fonts/*.woff2` | Self-hosted Inter variable font |
| `images/icons.svg` | Sprite of the [Lucide](https://lucide.dev) icons used |
| `js/cms.js` | Theme toggle, sidebar, navigation filter, menus, table enhancements |

## Changing styles

Node.js is only needed on a developer machine:

```bash
npm install
npm run build        # icons + CSS
npm run watch:css    # rebuild CSS while editing
```

Commit the rebuilt `css/app.css` with your change. Tailwind scans `*.php`,
`includes/**/*.php` and `js/cms.js`, so new utility classes in PHP markup need a rebuild.

## Where things live

- `src/tailwind.css` - design tokens (colour variables for light and dark), base
  element styles, components (`.btn`, `.card`, `.badge`, `.alert`, ...) and the
  legacy-markup layer that styles existing class names such as `table.table`,
  `table.cmslist`, `td.label`, `#formarea` and `body.popup`.
- `tailwind.config.js` - maps the tokens to Tailwind colour names (`bg-surface`,
  `text-fg-muted`, `border-line`, `bg-primary`, ...). Dark mode uses the `class` strategy.
- `includes/head.inc.php` - shared `<head>` assets for every page and popup window,
  including the inline script that applies the saved theme before first paint.
- `includes/header.inc.php` / `includes/footer.inc.php` - the application shell
  (sidebar, top bar, breadcrumbs, account menu).
- `includes/ui.inc.php` - presentation helpers: `cms_icon()`, `cms_alert()`,
  `cms_list_toolbar()` (used by `print_nav()`), navigation groups and area icons.

## Icons

```php
<?php echo cms_icon('pencil'); ?>                 // decorative
<?php echo cms_icon('trash-2', '', 'Delete'); ?>  // with an accessible label
```

To add an icon, add its Lucide name to `ICONS` in `tools/build-icons.mjs` and run
`npm run build:icons`.

## Navigation

The sidebar lists every area in `$areas` (`includes/constants.inc.php`) that the
user can access, using the same `cms.useraccess` check as before. Areas are grouped
by `cms_nav_groups()` in `includes/ui.inc.php`; an area not assigned to a group appears
under "Other", so a new area is never hidden. Areas in `$popups` still open in a popup window.

## Theme

Users switch theme from the top bar. The choice is stored in `localStorage`
(`cms-theme`) and shared with popup windows. Until a user chooses, the theme follows
their operating system setting.
