/*!
 * ClacksWeb CMS interface script
 * Progressive enhancements only - every page still works without it.
 *  - light / dark theme toggle (remembered in localStorage, synced across windows)
 *  - collapsible sidebar, mobile navigation drawer, collapsible nav groups
 *  - "Jump to" navigation filter (press "/" to focus)
 *  - accessible account menu
 *  - responsive wrappers + empty states for data tables
 *  - non-colour status cues and label associations for legacy form tables
 */
(function () {
  'use strict';

  var root = document.documentElement;
  var store = {
    get: function (k) { try { return window.localStorage.getItem(k); } catch (e) { return null; } },
    set: function (k, v) { try { window.localStorage.setItem(k, v); } catch (e) { /* storage unavailable */ } }
  };
  var uid = 0;
  var $$ = function (sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); };

  function iconSvg(name, cls) {
    return '<svg class="icon ' + (cls || '') + '" aria-hidden="true" focusable="false"><use href="images/icons.svg#' + name + '"></use></svg>';
  }

  /* ------------------------------------------------------------------ theme */
  function isDark() { return root.classList.contains('dark'); }
  function syncThemeButtons() {
    $$('[data-theme-toggle]').forEach(function (btn) {
      btn.setAttribute('aria-pressed', isDark() ? 'true' : 'false');
      var label = isDark() ? 'Switch to light mode' : 'Switch to dark mode';
      btn.setAttribute('aria-label', label);
      btn.setAttribute('title', label);
    });
  }
  function setTheme(theme, persist) {
    root.classList.toggle('dark', theme === 'dark');
    if (persist) store.set('cms-theme', theme);
    syncThemeButtons();
  }
  document.addEventListener('click', function (e) {
    var btn = e.target.closest && e.target.closest('[data-theme-toggle]');
    if (btn) setTheme(isDark() ? 'light' : 'dark', true);
  });
  // Keep popup windows and other tabs in step with the chosen theme
  window.addEventListener('storage', function (e) {
    if (e.key === 'cms-theme' && (e.newValue === 'dark' || e.newValue === 'light')) setTheme(e.newValue, false);
  });
  if (window.matchMedia) {
    var mq = window.matchMedia('(prefers-color-scheme: dark)');
    var onSystemChange = function (ev) {
      var saved = store.get('cms-theme');
      if (saved !== 'light' && saved !== 'dark') setTheme(ev.matches ? 'dark' : 'light', false);
    };
    if (mq.addEventListener) mq.addEventListener('change', onSystemChange);
    else if (mq.addListener) mq.addListener(onSystemChange);
  }

  function ready(fn) {
    if (document.readyState !== 'loading') fn();
    else document.addEventListener('DOMContentLoaded', fn);
  }

  ready(function () {
    syncThemeButtons();
    initSidebar();
    initNavGroups();
    initNavFilter();
    initPopupLinks();
    initMenus();
    enhanceTables();
    enhanceStatuses();
    enhanceFormLabels();
  });

  /* ---------------------------------------------------------------- sidebar */
  function initSidebar() {
    var sidebar = document.getElementById('app-sidebar');
    if (!sidebar) return;
    var toggle = document.querySelector('[data-sidebar-toggle]');
    var openBtn = document.querySelector('[data-nav-open]');
    var lastFocus = null;

    function syncCollapse() {
      var collapsed = root.classList.contains('sidebar-collapsed');
      if (toggle) {
        toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        toggle.setAttribute('title', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
        var label = toggle.querySelector('.nav-label');
        if (label) label.textContent = collapsed ? 'Expand sidebar' : 'Collapse sidebar';
      }
    }
    syncCollapse();
    if (toggle) {
      toggle.addEventListener('click', function () {
        var collapsed = root.classList.toggle('sidebar-collapsed');
        store.set('cms-sidebar', collapsed ? 'collapsed' : 'expanded');
        syncCollapse();
      });
    }
    // Clicking a group while collapsed expands the sidebar so its items are visible
    $$('.nav-group > summary', sidebar).forEach(function (summary) {
      summary.addEventListener('click', function () {
        if (root.classList.contains('sidebar-collapsed') && window.innerWidth >= 1024) {
          root.classList.remove('sidebar-collapsed');
          store.set('cms-sidebar', 'expanded');
          syncCollapse();
        }
      });
    });

    function openNav() {
      lastFocus = document.activeElement;
      root.classList.add('nav-open');
      if (openBtn) openBtn.setAttribute('aria-expanded', 'true');
      var first = sidebar.querySelector('input, a, button');
      if (first) first.focus();
    }
    function closeNav() {
      if (!root.classList.contains('nav-open')) return;
      root.classList.remove('nav-open');
      if (openBtn) openBtn.setAttribute('aria-expanded', 'false');
      if (lastFocus && lastFocus.focus) lastFocus.focus();
    }
    if (openBtn) openBtn.addEventListener('click', openNav);
    $$('[data-nav-close]').forEach(function (el) { el.addEventListener('click', closeNav); });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && root.classList.contains('nav-open')) closeNav();
      // keep focus inside the drawer while it is open on small screens
      if (e.key === 'Tab' && root.classList.contains('nav-open') && window.innerWidth < 1024) {
        var items = $$('a[href], button:not([disabled]), input:not([disabled]), summary', sidebar).filter(function (el) { return el.offsetParent !== null; });
        if (!items.length) return;
        var first = items[0], last = items[items.length - 1];
        if (e.shiftKey && document.activeElement === first) { last.focus(); e.preventDefault(); }
        else if (!e.shiftKey && document.activeElement === last) { first.focus(); e.preventDefault(); }
      }
    });
    window.addEventListener('resize', function () { if (window.innerWidth >= 1024) closeNav(); });
  }

  /* -------------------------------------------------------- nav group state */
  function initNavGroups() {
    var groups = $$('details[data-group]');
    if (!groups.length) return;
    var state = {};
    try { state = JSON.parse(store.get('cms-nav-groups') || '{}') || {}; } catch (e) { state = {}; }
    groups.forEach(function (d) {
      var key = d.getAttribute('data-group');
      if (!d.hasAttribute('data-active') && Object.prototype.hasOwnProperty.call(state, key)) d.open = !!state[key];
      d.addEventListener('toggle', function () {
        if (d.getAttribute('data-filtering') === '1') return;
        state[key] = d.open;
        store.set('cms-nav-groups', JSON.stringify(state));
      });
    });
  }

  /* ------------------------------------------------------------- nav filter */
  function initNavFilter() {
    var input = document.querySelector('[data-nav-filter]');
    if (!input) return;
    var empty = document.querySelector('[data-nav-empty]');
    var items = $$('.nav-sub .nav-item');
    var groups = $$('details[data-group]');
    var saved = null;

    function apply() {
      var q = input.value.trim().toLowerCase();
      if (q && saved === null) saved = groups.map(function (g) { return g.open; });
      var matches = 0;
      items.forEach(function (a) {
        var hay = (a.getAttribute('data-keywords') || a.textContent).toLowerCase();
        var hit = !q || hay.indexOf(q) !== -1;
        a.parentNode.classList.toggle('hidden', !hit);
        if (hit) matches++;
      });
      groups.forEach(function (g, i) {
        var visible = g.querySelectorAll('.nav-sub li:not(.hidden)').length > 0;
        g.parentNode.classList.toggle('hidden', !!q && !visible);
        g.setAttribute('data-filtering', '1');
        if (q) g.open = visible;
        else if (saved) g.open = saved[i];
        g.removeAttribute('data-filtering');
      });
      if (!q) saved = null;
      if (empty) empty.classList.toggle('hidden', !q || matches > 0);
    }
    input.addEventListener('input', apply);
    input.addEventListener('keydown', function (e) {
      if (e.key === 'Enter') {
        var first = items.filter(function (a) { return !a.parentNode.classList.contains('hidden'); })[0];
        if (first && input.value.trim()) { e.preventDefault(); first.click(); }
      } else if (e.key === 'Escape') {
        input.value = ''; apply(); input.blur();
      }
    });
    document.addEventListener('keydown', function (e) {
      if (e.key !== '/' || e.ctrlKey || e.metaKey || e.altKey) return;
      var t = e.target, tag = t && t.tagName;
      if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT' || (t && t.isContentEditable)) return;
      e.preventDefault();
      if (root.classList.contains('sidebar-collapsed')) root.classList.remove('sidebar-collapsed');
      if (window.innerWidth < 1024) { var ob = document.querySelector('[data-nav-open]'); if (ob) ob.click(); }
      input.focus();
    });
  }

  /* ------------------------------------------- areas that open as popups */
  function initPopupLinks() {
    $$('a[data-popup-width]').forEach(function (a) {
      a.addEventListener('click', function (e) {
        e.preventDefault();
        window.open(a.getAttribute('href'), 'school_sess',
          'width=' + a.getAttribute('data-popup-width') + ',height=' + a.getAttribute('data-popup-height') +
          ',top=50,left=50,resizable=yes,scrollbars=yes');
      });
    });
  }

  /* ------------------------------------------------------------------ menus */
  function initMenus() {
    $$('[data-menu]').forEach(function (wrap) {
      var btn = wrap.querySelector('[data-menu-button]');
      var panel = wrap.querySelector('[data-menu-panel]');
      if (!btn || !panel) return;
      var links = function () { return $$('a, button', panel); };
      function open(focusFirst) {
        panel.hidden = false;
        btn.setAttribute('aria-expanded', 'true');
        if (focusFirst) { var l = links()[0]; if (l) l.focus(); }
      }
      function close(returnFocus) {
        if (panel.hidden) return;
        panel.hidden = true;
        btn.setAttribute('aria-expanded', 'false');
        if (returnFocus) btn.focus();
      }
      btn.addEventListener('click', function () { panel.hidden ? open(false) : close(false); });
      btn.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowDown') { e.preventDefault(); open(true); }
      });
      panel.addEventListener('keydown', function (e) {
        var l = links(), i = l.indexOf(document.activeElement);
        if (e.key === 'ArrowDown') { e.preventDefault(); l[(i + 1) % l.length].focus(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); l[(i - 1 + l.length) % l.length].focus(); }
        else if (e.key === 'Escape') { e.preventDefault(); close(true); }
      });
      document.addEventListener('click', function (e) { if (!wrap.contains(e.target)) close(false); });
      wrap.addEventListener('focusout', function (e) {
        if (e.relatedTarget && !wrap.contains(e.relatedTarget)) close(false);
      });
    });
  }

  /* ----------------------------------------------------------------- tables */
  function isFormTable(t) {
    return !!(t.querySelector('td.label') || t.closest('#formarea') || t.classList.contains('form-table'));
  }
  function enhanceTables() {
    $$('table.table, table.cmslist').forEach(function (t) {
      if (isFormTable(t)) { t.classList.add('form-table'); return; }
      if (t.parentNode && !t.parentNode.classList.contains('table-wrap')) {
        var wrap = document.createElement('div');
        wrap.className = 'table-wrap';
        t.parentNode.insertBefore(wrap, t);
        wrap.appendChild(t);
      }
      // Empty state for list pages (header row only)
      var rows = t.rows;
      if (rows.length === 1 && rows[0].querySelector('th')) {
        var cols = 0;
        $$('th, td', rows[0]).forEach(function (c) { cols += c.colSpan || 1; });
        var tr = t.insertRow(-1);
        var td = tr.insertCell(0);
        td.colSpan = cols;
        td.innerHTML = '<div class="empty-state">' + iconSvg('inbox') +
          '<p class="m-0 font-medium text-fg">No records found</p>' +
          '<p class="m-0 text-xs">Try clearing the filter, or add a new record.</p></div>';
      }
    });
  }

  /* ----------------------------------------------- non-colour status cues */
  function enhanceStatuses() {
    var map = {
      overdue: ['circle-alert', 'overdue'],
      imminent: ['clock', 'due soon'],
      future: ['clock', 'upcoming'],
      indate: ['circle-check', 'in date'],
      past: ['circle-dashed', 'past']
    };
    Object.keys(map).forEach(function (cls) {
      $$('td.' + cls + ', span.' + cls + ', div.' + cls).forEach(function (el) {
        if (el.getAttribute('data-status-enhanced') || !el.textContent.trim()) return;
        el.setAttribute('data-status-enhanced', '1');
        el.insertAdjacentHTML('afterbegin', iconSvg(map[cls][0], 'status-icon'));
        el.insertAdjacentHTML('beforeend', '<span class="sr-only"> (' + map[cls][1] + ')</span>');
      });
    });
    // Content wizard steps
    var steps = { complete: 'complete', incomplete: 'required, not yet complete', optional: 'optional' };
    $$('#sections ul a').forEach(function (a) {
      Object.keys(steps).forEach(function (cls) {
        if (a.classList.contains(cls)) a.insertAdjacentHTML('beforeend', '<span class="sr-only"> (' + steps[cls] + ')</span>');
      });
      if (a.id === 'current') a.setAttribute('aria-current', 'step');
    });
  }

  /* ------------------------------------- label associations in form tables */
  function enhanceFormLabels() {
    $$('td.label').forEach(function (td) {
      var cell = td.nextElementSibling;
      if (!cell) return;
      var control = cell.querySelector('input:not([type=hidden]):not([type=submit]):not([type=button]), select, textarea');
      if (!control || control.hasAttribute('aria-label') || control.hasAttribute('aria-labelledby')) return;
      if (control.id && document.querySelector('label[for="' + control.id + '"]')) return;
      if (control.closest('label')) return;
      if (!td.id) td.id = 'cms-lbl-' + (++uid);
      control.setAttribute('aria-labelledby', td.id);
    });
  }
})();
