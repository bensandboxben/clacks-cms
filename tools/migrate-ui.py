#!/usr/bin/env python3
"""
One-off migration used for the 2026 interface redesign. Kept for reference.
Presentation-only rewrites of legacy markup in CMS pages:
  * replace Bootstrap/screen.css links with includes/head.inc.php
  * add an HTML5 doctype to popup pages that had none
  * drop <body bgcolor>
  * replace legacy GIF icons with Lucide sprite icons (alt text kept for screen readers)
  * give Cancel/Close/No buttons secondary styling and delete confirmations danger styling
  * mark validation error lists so they render as alerts
No PHP logic, field names, IDs, URLs or JavaScript handlers are changed.
"""
import re, sys, pathlib

ROOT = pathlib.Path(__file__).resolve().parent.parent
ICONS = {
    'add': ('plus', 'icon-add', 'Add'), 'edit': ('pencil', 'icon-edit', 'Edit'),
    'delete': ('trash-2', 'icon-delete', 'Delete'), 'trash': ('trash-2', 'icon-delete', 'Delete'),
    'files': ('paperclip', 'icon-files', 'Files'), 'info': ('info', 'icon-info', 'Information'),
    'editpage': ('file-pen', 'icon-edit', 'Edit'), 'up': ('arrow-up', 'icon-up', 'Move up'),
    'down': ('arrow-down', 'icon-down', 'Move down'), 'upoff': ('arrow-up', 'icon-up is-off', ''),
    'downoff': ('arrow-down', 'icon-down is-off', ''), 'select': ('mouse-pointer-click', 'icon-select', 'Select'),
    'copy': ('copy', 'icon-copy', 'Copy'), 'items': ('list', 'icon-items', 'Items'),
    'preview': ('eye', 'icon-preview', 'Preview'), 'documents': ('folder-open', 'icon-documents', 'Documents'),
    'bodies': ('users', 'icon-bodies', 'Bodies'), 'interests': ('briefcase', 'icon-interests', 'Interests'),
    'images': ('images', 'icon-images', 'Images'), 'image': ('image', 'icon-image', 'Image'),
    'export': ('file-down', 'icon-export', 'Export'), 'bulletins': ('newspaper', 'icon-bulletins', 'Bulletins'),
    'arrow_left': ('chevron-left', 'icon-prev', 'Previous'), 'arrow_right': ('chevron-right', 'icon-next', 'Next'),
    'arrow_leftoff': ('chevron-left', 'icon-prev is-off', ''), 'arrow_rightoff': ('chevron-right', 'icon-next is-off', ''),
}

# attribute value: "..." | '...' | \"...\" | \'...\' | bare
VAL = r'''(?:\\"(?:(?!\\").)*\\"|\\'(?:(?!\\').)*\\'|"[^"]*"|'[^']*'|[^\s>"'\\]+)'''
ATTR = r'\s+[a-zA-Z_:-]+(?:\s*=\s*' + VAL + ')?'
IMG_RE = re.compile(r'<img(?P<attrs>(?:' + ATTR + r')*)\s*/?>', re.S)
ATTR_RE = re.compile(r'([a-zA-Z_:-]+)(?:\s*=\s*(' + VAL + '))?', re.S)

def parse_attrs(s):
    out = {}
    for m in ATTR_RE.finditer(s):
        out[m.group(1).lower()] = m.group(2)
    return out

def unq(v):
    if v is None: return '', '"'
    for q in ('\\"', "\\'", '"', "'"):
        if v.startswith(q) and v.endswith(q) and len(v) >= 2 * len(q):
            return v[len(q):-len(q)], q
    return v, '"'

def img_repl(m):
    attrs = parse_attrs(m.group('attrs'))
    src, q = unq(attrs.get('src'))
    mm = re.fullmatch(r'images/([a-z_]+)\.gif', src)
    if not mm or mm.group(1) not in ICONS:
        return m.group(0)
    name, cls, default_label = ICONS[mm.group(1)]
    alt, _ = unq(attrs.get('alt')) if attrs.get('alt') is not None else ('', q)
    title, _ = unq(attrs.get('title')) if attrs.get('title') is not None else ('', q)
    label = alt.strip() or title.strip() or default_label
    if label in ('-',):
        label = ''
    svg = ('<svg class=' + q + 'icon ' + cls + q + ' aria-hidden=' + q + 'true' + q + ' focusable=' + q + 'false' + q +
           '><use href=' + q + 'images/icons.svg#' + name + q + '></use></svg>')
    onclick = attrs.get('onclick')
    if onclick is not None:
        # clickable image -> real button (keyboard accessible); handler unchanged
        t = (' title=' + q + label + q) if label and '<?' not in label else ''
        return ('<button type=' + q + 'button' + q + ' class=' + q + 'icon-action' + q + t + ' onclick=' + onclick + '>' +
                svg + ('<span class=' + q + 'sr-only' + q + '>' + label + '</span>' if label else '') + '</button>')
    return svg + ('<span class=' + q + 'sr-only' + q + '>' + label + '</span>' if label else '')

INPUT_RE = re.compile(r'<input(?P<attrs>(?:' + ATTR + r')*)\s*/?>', re.S)
SECONDARY_VALUES = {'cancel', 'close', 'no', 'clear', 'back', 'reset', 'cancel & reset', 'cancel &amp; reset'}

def input_repl(m):
    tag = m.group(0)
    attrs = parse_attrs(m.group('attrs'))
    # collapse duplicated class attributes (class="btn .." class="btn ..")
    classes = re.findall(r'\sclass\s*=\s*(' + VAL + ')', tag)
    if len(classes) > 1 and len(set(classes)) == 1:
        first = tag.find(' class=')
        second = tag.find(' class=', first + 1)
        tag = tag[:second] + tag[second + len(' class=') + len(classes[1]):]
    cls_v = attrs.get('class')
    if cls_v is None:
        return tag
    cls, q = unq(cls_v)
    if 'btn' not in cls.split():
        return tag
    value, _ = unq(attrs.get('value'))
    name, _ = unq(attrs.get('name'))
    v = value.strip().lower()
    newcls = cls
    if name == 'confirm' and v == 'yes':
        newcls = re.sub(r'\bbtn-primary\b', 'btn-danger', cls)
    elif v in SECONDARY_VALUES or name in ('cancel', 'clearfilter', 'resetnp'):
        newcls = re.sub(r'\bbtn-primary\b', 'btn-secondary', cls)
    newcls = re.sub(r'\bbtn-lg\b', 'btn-md', newcls)
    if newcls != cls:
        tag = tag.replace('class=' + cls_v, 'class=' + q + newcls + q, 1)
    return tag

LINK_PATTERNS = [
    r'[ \t]*<link href="/?css/bootstrap\.css" rel="stylesheet">[ \t]*\r?\n?',
    r'[ \t]*<link rel="stylesheet" href="css/screen\.css"(?: type="text/css")?>[ \t]*\r?\n?',
    r'[ \t]*<link rel="stylesheet" href="css/intranetscreen\.css" type="text/css">[ \t]*\r?\n?',
]
HEAD_INCLUDE = '<?php include "includes/head.inc.php"; ?>\n'

def migrate(path):
    s = path.read_bytes().decode('latin-1')
    orig = s
    # stylesheet links -> shared head include (only inserted once, where the first link was)
    first = None
    for pat in LINK_PATTERNS:
        m = re.search(pat, s)
        if m and (first is None or m.start() < first):
            first = m.start()
    if first is not None and 'includes/head.inc.php' not in s:
        s = s[:first] + '\x00HEAD\x00' + s[first:]
        for pat in LINK_PATTERNS:
            s = re.sub(pat, '', s)
        s = s.replace('\x00HEAD\x00', HEAD_INCLUDE)
    # doctype for popup documents which start with a bare <head>
    if 'includes/head.inc.php' in s and not re.search(r'<!DOCTYPE', s, re.I):
        s = re.sub(r'(\?>\s*\n)(\s*)<head>', r'\1<!DOCTYPE html>\n<html lang="en-GB">\n<head>', s, count=1)
    # body
    s = re.sub(r'<body bgcolor="#(?:FFFFFF|fff)" class="popup">', '<body class="popup">', s, flags=re.I)
    # icons
    s = IMG_RE.sub(img_repl, s)
    # buttons
    s = INPUT_RE.sub(input_repl, s)
    # validation error intro + list
    s = re.sub(r"echo '<p>((?:(?!</p>).)*?(?:not saved|not sent)\. Please correct these errors and resubmit the form:?)</p><ul>';",
               r"""echo '<p class="form-error-intro" role="alert">\1</p><ul class="error">';""", s)
    if s != orig:
        path.write_bytes(s.encode('latin-1'))
        return True
    return False

if __name__ == '__main__':
    files = [pathlib.Path(p) for p in sys.argv[1:]]
    changed = [str(f) for f in files if migrate(f)]
    print(len(changed), 'files changed')
