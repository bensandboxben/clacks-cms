<?php

include_once "includes/prepend.inc.php";

header("Cache-Control: private");

/*
|--------------------------------------------------------------------------
| CONFIG
|--------------------------------------------------------------------------
*/

$pageDir = "/home/site/wwwroot/section";
$liveSyncUrl = "https://www.clacks.gov.uk/includes/section-menu-sync.php";
$syncSecret = "clacks_menu_sync_2026_secure_9f3kL2x8Qp";

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

function clean($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function isEmptyRow($link)
{
    return trim($link['text'] ?? '') === ''
        && trim($link['href'] ?? '') === ''
        && trim($link['title'] ?? '') === '';
}

function validateLink($link)
{
    $text = trim($link['text'] ?? '');
    $href = trim($link['href'] ?? '');

    if ($text === '' || $href === '') {
        return false;
    }

    if (!preg_match('#^(https?://|/)#i', $href)) {
        return false;
    }

    if (stripos($href, 'javascript:') === 0) {
        return false;
    }

    return true;
}

function getPanelBlocks($html)
{
    $blocks = [];

    /*
    |--------------------------------------------------------------------------
    | Find SGDS menu panels
    |--------------------------------------------------------------------------
    |
    | Expected structure:
    |
    | <div class="clacks-col-panel">
    |     <h2>...</h2>
    |     <ul class="ds_no-bullets">
    |         <li><a href="...">...</a></li>
    |     </ul>
    | </div>
    |
    */

    $pattern = '#<div\s+class=["\']clacks-col-panel["\']\s*>(.*?)</div>#is';

    if (preg_match_all($pattern, $html, $matches, PREG_OFFSET_CAPTURE)) {

        foreach ($matches[0] as $i => $match) {

            $blockHtml = $match[0];
            $start = $match[1];

            $blocks[] = [
                'start' => $start,
                'end' => $start + strlen($blockHtml),
                'html' => $blockHtml
            ];
        }
    }

    return $blocks;
}

function parsePanel($blockHtml)
{
    $group = [
        'title' => '',
        'title_html' => '',
        'links' => []
    ];

    /*
    |--------------------------------------------------------------------------
    | Get heading
    |--------------------------------------------------------------------------
    */

    if (preg_match('#<h2\b[^>]*>.*?</h2>#is', $blockHtml, $headingMatch)) {

        $group['title_html'] = $headingMatch[0];

        if (
            preg_match(
                '#<h2\b[^>]*>(?:\s*<a\b[^>]*>)?\s*(.*?)\s*(?:</a>\s*)?</h2>#is',
                $headingMatch[0],
                $titleMatch
            )
        ) {
            $group['title'] = trim(
                html_entity_decode(
                    strip_tags($titleMatch[1]),
                    ENT_QUOTES,
                    'UTF-8'
                )
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Get links
    |--------------------------------------------------------------------------
    */

    if (
        preg_match(
            '#<ul\b[^>]*class=["\'][^"\']*\bds_no-bullets\b[^"\']*["\'][^>]*>(.*?)</ul>#is',
            $blockHtml,
            $ulMatch
        )
    ) {

        if (
            preg_match_all(
                '#<li\b[^>]*>\s*(<a\b[^>]*>.*?</a>)\s*</li>#is',
                $ulMatch[1],
                $linkMatches
            )
        ) {

            foreach ($linkMatches[1] as $linkHtml) {

                $href = '';
                $title = '';
                $text = '';

                if (preg_match('#\bhref=["\']([^"\']*)["\']#i', $linkHtml, $hrefMatch)) {
                    $href = html_entity_decode(
                        $hrefMatch[1],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                }

                if (preg_match('#\btitle=["\']([^"\']*)["\']#i', $linkHtml, $titleMatch)) {
                    $title = html_entity_decode(
                        $titleMatch[1],
                        ENT_QUOTES,
                        'UTF-8'
                    );
                }

                if (preg_match('#<a\b[^>]*>(.*?)</a>#is', $linkHtml, $textMatch)) {
                    $text = html_entity_decode(
                        trim(strip_tags($textMatch[1])),
                        ENT_QUOTES,
                        'UTF-8'
                    );
                }

                $group['links'][] = [
                    'href' => $href,
                    'title' => $title,
                    'text' => $text
                ];
            }
        }
    }

    return $group;
}

function buildPanel($titleHtml, $links)
{
    $html = '<div class="clacks-col-panel">' . PHP_EOL;

    /*
    |--------------------------------------------------------------------------
    | Preserve the original H2 exactly
    |--------------------------------------------------------------------------
    */

    $html .= '    ' . trim($titleHtml) . PHP_EOL;

    $html .= '    <ul class="ds_no-bullets">' . PHP_EOL;

    foreach ($links as $link) {

        if (isEmptyRow($link)) {
            continue;
        }

        if (!validateLink($link)) {
            continue;
        }

        $text = trim($link['text']);
        $href = trim($link['href']);
        $title = trim($link['title']);

        $html .= '        <li>';

        $html .= '<a href="' . clean($href) . '"';

        if ($title !== '') {
            $html .= ' title="' . clean($title) . '"';
        }

        $html .= '>' . clean($text) . '</a>';

        $html .= '</li>' . PHP_EOL;
    }

    $html .= '    </ul>' . PHP_EOL;
    $html .= '</div>';

    return $html;
}

/*
|--------------------------------------------------------------------------
| INIT
|--------------------------------------------------------------------------
*/

$errors = [];
$success = '';

$groups = [];
$files = [];
$validBlockIndexes = [];

$realPageDir = realpath($pageDir);

/*
|--------------------------------------------------------------------------
| CHECK SECTION DIRECTORY
|--------------------------------------------------------------------------
*/

if ($realPageDir === false || !is_dir($realPageDir)) {

    $errors[] = 'Section directory not found: ' . $pageDir;

} else {

    foreach (scandir($realPageDir) as $file) {

        if (
            $file !== '.'
            && $file !== '..'
            && substr($file, -4) === '.php'
            && is_file($realPageDir . DIRECTORY_SEPARATOR . $file)
        ) {
            $files[] = $file;
        }
    }

    sort($files);
}

/*
|--------------------------------------------------------------------------
| LOAD SELECTED FILE
|--------------------------------------------------------------------------
*/

$filename = $_GET['file'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $filename = $_POST['filename'] ?? '';
}

if ($filename !== '') {

    /*
    |--------------------------------------------------------------------------
    | Only allow files from the section directory
    |--------------------------------------------------------------------------
    */

    if (
        basename($filename) !== $filename
        || !in_array($filename, $files, true)
    ) {

        $errors[] = 'Invalid section file selected.';

    } else {

        $filepath = realpath($realPageDir . DIRECTORY_SEPARATOR . $filename);

        if (
            $filepath === false
            || !is_file($filepath)
            || dirname($filepath) !== $realPageDir
        ) {

            $errors[] = 'Unable to locate the selected section file.';

        } else {

            $html = file_get_contents($filepath);

            if ($html === false) {

                $errors[] = 'Unable to read the selected section file.';

            } else {

                $blocks = getPanelBlocks($html);

                foreach ($blocks as $i => $block) {

                    $group = parsePanel($block['html']);

                    /*
                    |--------------------------------------------------------------------------
                    | Only treat genuine SGDS menu panels as editable
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $group['title_html'] !== ''
                        && preg_match(
                            '#<ul\b[^>]*class=["\'][^"\']*\bds_no-bullets\b[^"\']*["\']#i',
                            $block['html']
                        )
                    ) {

                        $groups[] = $group;
                        $validBlockIndexes[] = $i;
                    }
                }

                if (empty($groups)) {
                    $errors[] = 'No SGDS menu panels were found in this file.';
                }
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| SAVE + SYNC
|--------------------------------------------------------------------------
*/

if (isset($_POST['save_menu'])) {

    $filename = $_POST['filename'] ?? '';

    if (
        $filename === ''
        || basename($filename) !== $filename
        || !in_array($filename, $files, true)
    ) {

        $errors[] = 'Invalid file selected for saving.';

    } else {

        $filepath = realpath($realPageDir . DIRECTORY_SEPARATOR . $filename);

        if (
            $filepath === false
            || !is_file($filepath)
            || dirname($filepath) !== $realPageDir
        ) {

            $errors[] = 'Unable to locate the file for saving.';

        } else {

            $html = file_get_contents($filepath);

            if ($html === false) {

                $errors[] = 'Unable to read the file before saving.';

            } else {

                $blocks = getPanelBlocks($html);

                $postedBlockIndexes = $_POST['block_indexes'] ?? [];
                $postedGroups = $_POST['group'] ?? [];

                /*
                |--------------------------------------------------------------------------
                | Validate block indexes
                |--------------------------------------------------------------------------
                */

                $saveBlocks = [];

                foreach ($postedBlockIndexes as $groupPosition => $blockIndex) {

                    if (!is_numeric($blockIndex)) {
                        continue;
                    }

                    $blockIndex = (int)$blockIndex;

                    if (!isset($blocks[$blockIndex])) {
                        continue;
                    }

                    if (!isset($postedGroups[$groupPosition])) {
                        $postedGroups[$groupPosition] = [];
                    }

                    $saveBlocks[] = [
                        'blockIndex' => $blockIndex,
                        'groupPosition' => $groupPosition
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | Replace from bottom to top
                |--------------------------------------------------------------------------
                |
                | This prevents earlier replacements from changing the offsets
                | of blocks that still need to be replaced.
                |
                */

                usort(
                    $saveBlocks,
                    function ($a, $b) {
                        return $b['blockIndex'] <=> $a['blockIndex'];
                    }
                );

                foreach ($saveBlocks as $saveBlock) {

                    $blockIndex = $saveBlock['blockIndex'];
                    $groupPosition = $saveBlock['groupPosition'];

                    $originalBlock = $blocks[$blockIndex]['html'];

                    /*
                    |--------------------------------------------------------------------------
                    | Preserve original H2
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !preg_match(
                            '#<h2\b[^>]*>.*?</h2>#is',
                            $originalBlock,
                            $titleMatch
                        )
                    ) {
                        continue;
                    }

                    $titleHtml = $titleMatch[0];

                    $links = [];

                    if (isset($postedGroups[$groupPosition])) {

                        foreach ($postedGroups[$groupPosition] as $link) {

                            if (!is_array($link)) {
                                continue;
                            }

                            if (isEmptyRow($link)) {
                                continue;
                            }

                            if (!validateLink($link)) {
                                continue;
                            }

                            $links[] = [
                                'text' => trim($link['text'] ?? ''),
                                'href' => trim($link['href'] ?? ''),
                                'title' => trim($link['title'] ?? '')
                            ];
                        }
                    }

                    $newBlock = buildPanel($titleHtml, $links);

                    $html = substr_replace(
                        $html,
                        $newBlock,
                        $blocks[$blockIndex]['start'],
                        $blocks[$blockIndex]['end'] - $blocks[$blockIndex]['start']
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Create backup
                |--------------------------------------------------------------------------
                */

                $backupFile = $filepath . '.bak';

                if (!copy($filepath, $backupFile)) {

                    $errors[] = 'Warning: unable to create backup file. Save was not performed.';

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | Save locally
                    |--------------------------------------------------------------------------
                    */

                    $bytesWritten = file_put_contents(
                        $filepath,
                        $html,
                        LOCK_EX
                    );

                    if ($bytesWritten === false) {

                        $errors[] = 'Unable to save the file locally.';

                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | Sync to LIVE
                        |--------------------------------------------------------------------------
                        */

                        $postData = [
                            'secret' => $syncSecret,
                            'filename' => $filename,
                            'content' => $html
                        ];

                        $ch = curl_init($liveSyncUrl);

                        curl_setopt_array(
                            $ch,
                            [
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_POST => true,
                                CURLOPT_POSTFIELDS => $postData,
                                CURLOPT_TIMEOUT => 30,
                                CURLOPT_CONNECTTIMEOUT => 10,
                                CURLOPT_FAILONERROR => false
                            ]
                        );

                        $response = curl_exec($ch);
                        $curlError = curl_error($ch);
                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                        curl_close($ch);

                        $body = trim((string)$response);

                        /*
                        |--------------------------------------------------------------------------
                        | Log sync result
                        |--------------------------------------------------------------------------
                        */

                        file_put_contents(
                            "/home/site/wwwroot/sync-debug.log",
                            date('Y-m-d H:i:s')
                            . " | FILE:" . $filename
                            . " | HTTP:" . $httpCode
                            . " | BODY:" . $body
                            . " | CURL:" . $curlError
                            . PHP_EOL,
                            FILE_APPEND
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | Result handling
                        |--------------------------------------------------------------------------
                        */

                        if ($curlError) {

                            $errors[] = 'Local save succeeded, but LIVE sync failed: ' . $curlError;

                        } elseif ($httpCode !== 200) {

                            $errors[] = 'Local save succeeded, but LIVE sync returned HTTP ' . $httpCode . '.';

                        } elseif ($body !== 'OK') {

                            $errors[] = 'Local save succeeded, but LIVE sync returned: ' . $body;

                        } else {

                            $success = 'Saved locally and synced to live successfully.';
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Reload saved file
                        |--------------------------------------------------------------------------
                        */

                        $filename = $filename;

                        $savedHtml = file_get_contents($filepath);

                        if ($savedHtml !== false) {

                            $html = $savedHtml;
                            $blocks = getPanelBlocks($html);

                            $groups = [];
                            $validBlockIndexes = [];

                            foreach ($blocks as $i => $block) {

                                $group = parsePanel($block['html']);

                                if (
                                    $group['title_html'] !== ''
                                    && preg_match(
                                        '#<ul\b[^>]*class=["\'][^"\']*\bds_no-bullets\b[^"\']*["\']#i',
                                        $block['html']
                                    )
                                ) {

                                    $groups[] = $group;
                                    $validBlockIndexes[] = $i;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Section Menu Editor</title>

    <link
        rel="stylesheet"
        href="/libraries/css/design-system.css"
    >

    <link
        rel="stylesheet"
        href="/libraries/css/custom.css"
    >

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <style>

        .menu-editor {
            padding-top: 30px;
            padding-bottom: 60px;
        }

        .menu-editor__heading {
            margin-bottom: 10px;
        }

        .menu-editor__intro {
            margin-bottom: 30px;
        }

        .menu-panel {
            margin-bottom: 35px;
            border: 1px solid #d8dde3;
            background: #fff;
        }

        .menu-panel__heading {
            margin: 0;
            padding: 16px 20px;
            background: #f3f4f5;
            border-bottom: 1px solid #d8dde3;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .menu-panel__body {
            padding: 20px;
        }

        .menu-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .menu-table th {
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #d8dde3;
            font-weight: 700;
        }

        .menu-table td {
            padding: 8px;
            border-bottom: 1px solid #e5e5e5;
            vertical-align: middle;
        }

        .menu-table input {
            width: 100%;
            box-sizing: border-box;
        }

        .drag-handle {
            width: 45px;
            text-align: center;
            cursor: grab;
            font-size: 22px;
            user-select: none;
        }

        .drag-handle:active {
            cursor: grabbing;
        }

        .menu-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .save-bar {
            position: sticky;
            bottom: 0;
            z-index: 100;
            padding: 15px 0;
            margin-top: 30px;
            background: #fff;
            border-top: 1px solid #d8dde3;
        }

        .save-bar__inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .save-bar__status {
            margin: 0;
        }

        .empty-message {
            padding: 20px;
            background: #f3f4f5;
            border-left: 4px solid #6f8fad;
        }

        @media (max-width: 768px) {

            .menu-table {
                display: block;
                overflow-x: auto;
            }

            .menu-table th,
            .menu-table td {
                min-width: 180px;
            }

            .menu-table th:first-child,
            .menu-table td:first-child {
                min-width: 45px;
            }

            .save-bar__inner {
                display: block;
            }

            .save-bar__inner .ds_button {
                margin-top: 10px;
            }
        }

    </style>

</head>

<body>

<div class="ds_page">

    <div class="ds_page__inner">

        <div class="ds_page__middle">

            <main id="main-content">

                <div class="ds_wrapper">

                    <div class="menu-editor">

                        <h1 class="menu-editor__heading">
                            Edit section menus
                        </h1>

                        <p class="menu-editor__intro">
                            Select a section page below to edit its menu links.
                            You can drag links to change their order, add new links,
                            delete existing links, and update URLs and link titles.
                        </p>

                        <?php if (!empty($errors)): ?>

                            <div
                                class="ds_notification ds_notification--error"
                                role="alert"
                            >

                                <div class="ds_notification__content">

                                    <div class="ds_notification__text">

                                        <?php foreach ($errors as $error): ?>

                                            <p><?= clean($error) ?></p>

                                        <?php endforeach; ?>

                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>


                        <?php if (!empty($success)): ?>

                            <div
                                class="ds_notification ds_notification--success"
                                role="status"
                            >

                                <div class="ds_notification__content">

                                    <div class="ds_notification__text">

                                        <p><?= clean($success) ?></p>

                                    </div>

                                </div>

                            </div>

                        <?php endif; ?>


                        <form
                            method="GET"
                            action=""
                        >

                            <div class="ds_field-group">

                                <label
                                    class="ds_label"
                                    for="section-file"
                                >
                                    Select page
                                </label>

                                <select
                                    class="ds_select"
                                    id="section-file"
                                    name="file"
                                    onchange="this.form.submit()"
                                >

                                    <option value="">
                                        -- Select page --
                                    </option>

                                    <?php foreach ($files as $file): ?>

                                        <option
                                            value="<?= clean($file) ?>"
                                            <?= ($file === $filename ? 'selected' : '') ?>
                                        >
                                            <?= clean($file) ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>

                        </form>


                        <?php if ($filename && !empty($groups)): ?>

                            <form
                                method="POST"
                                action=""
                                id="menu-editor-form"
                            >

                                <input
                                    type="hidden"
                                    name="filename"
                                    value="<?= clean($filename) ?>"
                                >


                                <?php foreach ($validBlockIndexes as $blockIndex): ?>

                                    <input
                                        type="hidden"
                                        name="block_indexes[]"
                                        value="<?= (int)$blockIndex ?>"
                                    >

                                <?php endforeach; ?>


                                <?php foreach ($groups as $g => $group): ?>

                                    <section class="menu-panel">

                                        <h2 class="menu-panel__heading">
                                            <?= clean($group['title']) ?>
                                        </h2>

                                        <div class="menu-panel__body">

                                            <table class="menu-table">

                                                <thead>

                                                    <tr>

                                                        <th
                                                            scope="col"
                                                            aria-label="Order"
                                                        >
                                                        </th>

                                                        <th scope="col">
                                                            Link text
                                                        </th>

                                                        <th scope="col">
                                                            URL
                                                        </th>

                                                        <th scope="col">
                                                            Title
                                                        </th>

                                                        <th
                                                            scope="col"
                                                            aria-label="Actions"
                                                        >
                                                        </th>

                                                    </tr>

                                                </thead>

                                                <tbody
                                                    class="sortable"
                                                    id="g<?= (int)$g ?>"
                                                    data-group="<?= (int)$g ?>"
                                                >

                                                    <?php foreach ($group['links'] as $l => $link): ?>

                                                        <tr>

                                                            <td class="drag-handle">
                                                                <span aria-hidden="true">☰</span>
                                                                <span class="visually-hidden">
                                                                    Drag to reorder
                                                                </span>
                                                            </td>

                                                            <td>

                                                                <label
                                                                    class="visually-hidden"
                                                                    for="text_<?= (int)$g ?>_<?= (int)$l ?>"
                                                                >
                                                                    Link text
                                                                </label>

                                                                <input
                                                                    class="ds_input"
                                                                    type="text"
                                                                    id="text_<?= (int)$g ?>_<?= (int)$l ?>"
                                                                    name="group[<?= (int)$g ?>][<?= (int)$l ?>][text]"
                                                                    value="<?= clean($link['text']) ?>"
                                                                >

                                                            </td>

                                                            <td>

                                                                <label
                                                                    class="visually-hidden"
                                                                    for="href_<?= (int)$g ?>_<?= (int)$l ?>"
                                                                >
                                                                    URL
                                                                </label>

                                                                <input
                                                                    class="ds_input"
                                                                    type="text"
                                                                    id="href_<?= (int)$g ?>_<?= (int)$l ?>"
                                                                    name="group[<?= (int)$g ?>][<?= (int)$l ?>][href]"
                                                                    value="<?= clean($link['href']) ?>"
                                                                >

                                                            </td>

                                                            <td>

                                                                <label
                                                                    class="visually-hidden"
                                                                    for="title_<?= (int)$g ?>_<?= (int)$l ?>"
                                                                >
                                                                    Title
                                                                </label>

                                                                <input
                                                                    class="ds_input"
                                                                    type="text"
                                                                    id="title_<?= (int)$g ?>_<?= (int)$l ?>"
                                                                    name="group[<?= (int)$g ?>][<?= (int)$l ?>][title]"
                                                                    value="<?= clean($link['title']) ?>"
                                                                >

                                                            </td>

                                                            <td>

                                                                <button
                                                                    type="button"
                                                                    class="ds_button ds_button--secondary"
                                                                    onclick="deleteRow(this)"
                                                                >
                                                                    Delete
                                                                </button>

                                                            </td>

                                                        </tr>

                                                    <?php endforeach; ?>

                                                </tbody>

                                            </table>


                                            <div class="menu-actions">

                                                <button
                                                    type="button"
                                                    class="ds_button ds_button--secondary"
                                                    onclick="addRow(<?= (int)$g ?>)"
                                                >
                                                    Add link
                                                </button>

                                            </div>

                                        </div>

                                    </section>

                                <?php endforeach; ?>


                                <div class="save-bar">

                                    <div class="ds_wrapper">

                                        <div class="save-bar__inner">

                                            <p class="save-bar__status">
                                                Changes are saved locally and then synchronised to the live website.
                                            </p>

                                            <button
                                                type="submit"
                                                class="ds_button"
                                                name="save_menu"
                                                value="1"
                                            >
                                                Save changes
                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </form>

                        <?php elseif ($filename && empty($errors)): ?>

                            <div class="empty-message">

                                <p>
                                    No editable SGDS menu panels were found in
                                    <strong><?= clean($filename) ?></strong>.
                                </p>

                                <p>
                                    The page needs to contain panels using the
                                    <code>clacks-col-panel</code> and
                                    <code>ds_no-bullets</code> structure.
                                </p>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            </main>

        </div>

    </div>

</div>


<script>

function addRow(group)
{
    const table = document.getElementById('g' + group);

    if (!table) {
        return;
    }

    const index = table.querySelectorAll('tr').length;

    const row = document.createElement('tr');

    row.innerHTML = `
        <td class="drag-handle">
            <span aria-hidden="true">☰</span>
            <span class="visually-hidden">Drag to reorder</span>
        </td>

        <td>
            <label class="visually-hidden" for="text_${group}_${index}">
                Link text
            </label>
            <input
                class="ds_input"
                type="text"
                id="text_${group}_${index}"
                name="group[${group}][${index}][text]"
                value=""
            >
        </td>

        <td>
            <label class="visually-hidden" for="href_${group}_${index}">
                URL
            </label>
            <input
                class="ds_input"
                type="text"
                id="href_${group}_${index}"
                name="group[${group}][${index}][href]"
                value=""
            >
        </td>

        <td>
            <label class="visually-hidden" for="title_${group}_${index}">
                Title
            </label>
            <input
                class="ds_input"
                type="text"
                id="title_${group}_${index}"
                name="group[${group}][${index}][title]"
                value=""
            >
        </td>

        <td>
            <button
                type="button"
                class="ds_button ds_button--secondary"
                onclick="deleteRow(this)"
            >
                Delete
            </button>
        </td>
    `;

    table.appendChild(row);
}


function deleteRow(button)
{
    const row = button.closest('tr');

    if (!row) {
        return;
    }

    row.remove();
}


document.querySelectorAll('.sortable').forEach(function (element) {

    new Sortable(
        element,
        {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost'
        }
    );

});


document.getElementById('menu-editor-form')?.addEventListener(
    'submit',
    function () {

        const button = this.querySelector(
            'button[name="save_menu"]'
        );

        if (button) {

            button.disabled = true;
            button.textContent = 'Saving...';

        }

    }
);

</script>

</body>

</html>