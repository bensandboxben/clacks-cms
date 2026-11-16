<?php

include_once "includes/prepend.inc.php";
header("Cache-Control: private");

/*
|--------------------------------------------------------------------------
| CONFIG
|--------------------------------------------------------------------------
*/
$menuDir = "/home/site/wwwroot/includes/menus";
$liveSyncUrl = "https://www.clacks.gov.uk/includes/menu-sync.php";
$syncSecret = "clacks_menu_sync_2026_secure_9f3kL2x8Qp";

/*
|--------------------------------------------------------------------------
| INIT
|--------------------------------------------------------------------------
*/
$errors = [];
$success = "";
$content = "";
$files = [];
$realMenuDir = realpath($menuDir);

if (!$realMenuDir || !is_dir($realMenuDir)) {
    die("Menu directory not found");
}

/*
|--------------------------------------------------------------------------
| GET FILES
|--------------------------------------------------------------------------
*/
$allFiles = scandir($realMenuDir);

foreach ($allFiles as $file) {
    if (substr($file, -4) === ".txt") {
        $files[] = $file;
    }
}

sort($files);

/*
|--------------------------------------------------------------------------
| SELECT FILE
|--------------------------------------------------------------------------
*/
$filename = $_GET['file'] ?? '';

/*
|--------------------------------------------------------------------------
| LOAD FILE
|--------------------------------------------------------------------------
*/
if ($filename !== '') {

    $filepath = realpath($realMenuDir . "/" . $filename);

    if (!$filepath || strpos($filepath, $realMenuDir) !== 0) {
        die("Invalid file path");
    }

    if (file_exists($filepath)) {
        $content = file_get_contents($filepath);
    }
}

/*
|--------------------------------------------------------------------------
| VALIDATION
|--------------------------------------------------------------------------
*/
function validate_menu($content) {
    $errors = [];
    $lines = explode("\n", $content);

    foreach ($lines as $i => $line) {
        $line = trim($line);

        if ($line === '') continue;

        if ($i === 0 && strpos($line, '|') === false) {
            continue;
        }

        if (strpos($line, '|') === false) {
            $errors[] = "Line " . ($i + 1) . " must contain |";
        }
    }

    return $errors;
}

/*
|--------------------------------------------------------------------------
| SAVE
|--------------------------------------------------------------------------
*/
if ($_POST["save_menu"]) {

    $filename = $_POST["filename"];
    $newContent = trim($_POST["menu_content"]);

    if ($filename === '') {
        $errors[] = "No menu selected.";
    } else {

        $filepath = realpath($realMenuDir . "/" . $filename);

        if (!$filepath || strpos($filepath, $realMenuDir) !== 0) {
            die("Invalid file path");
        }

        $validationErrors = validate_menu($newContent);

        if (count($validationErrors) > 0) {

            $errors = array_merge($errors, $validationErrors);
            $content = $newContent;

        } else {

            copy($filepath, $filepath . ".bak");

            if (file_put_contents($filepath, $newContent, LOCK_EX) !== false) {

                $postData = [
                    'secret' => $syncSecret,
                    'filename' => $filename,
                    'content' => $newContent
                ];

                $ch = curl_init($liveSyncUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);

                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    $errors[] = "Live sync failed: " . curl_error($ch);
                } else {
                    if ($response !== "OK") {
                        $errors[] = "Live sync error: " . $response;
                    }
                }

                curl_close($ch);

                if (function_exists("log_entry")) {
                    log_entry($_SESSION["userid"], "Updated menu file $filename");
                }

                $success = "Menu saved and pushed to live";
                $content = $newContent;

            } else {
                $errors[] = "Failed to write file.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Menu Editor</title>

<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css">

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<style>
body {
    background: #f5f7fb;
    padding: 20px;
    font-family: Arial;
}

select {
    height: 38px;
    padding: 6px 12px;
    border-radius: 4px;
}

.card {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.menu-row {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
    align-items: center;
}

.drag-handle {
    cursor: grab;
    font-size: 18px;
    color: #777;
}

.dragging {
    opacity: 0.5;
}

.menu-row input {
    flex: 1;
}

.menu-row input {
    flex: 1;
    height: 38px;
    padding: 6px 12px;
    font-size: 14px;
    line-height: 1.42857143;
    color: #555;
    background-color: #fff;
    background-image: none;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
    transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
}

.menu-row input:focus {
    border-color: #66afe9;
    outline: 0;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102,175,233,.6);
}
.form-control {
    display: block;
    width: 100%;
    height: 34px;
    padding: 6px 6px;
    margin:0px;
 }   
</style>
</head>

<body class="popup">

<div id="formarea">

<h3>Menu Editor</h3>

<form method="GET">
    <select name="file" onchange="this.form.submit()" class="form-control" style="margin:0px;">
        <option value="">-- Select a menu --</option>
        <?php foreach ($files as $file): ?>
            <option value="<?= htmlspecialchars($file) ?>" <?= ($file == $filename ? 'selected' : '') ?>>
                <?= htmlspecialchars($file) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php
if ($errors) {
    echo '<div class="alert alert-danger">';
    foreach ($errors as $e) echo htmlspecialchars($e) . "<br>";
    echo '</div>';
}

if ($success) {
    echo '<div class="alert alert-success">' . htmlspecialchars($success) . '</div>';
}
?>

<?php if ($filename !== ''): ?>

<form method="POST">

<input type="hidden" name="filename" value="<?= htmlspecialchars($filename) ?>">

<div class="card">

<div id="menu-builder">

<?php
$lines = explode("\n", $content);
foreach ($lines as $line):
    $line = trim($line);
    if ($line === '') continue;

    $parts = explode("|", $line, 2);
    $text = $parts[0] ?? '';
    $url  = $parts[1] ?? '';
?>

<div class="menu-row">
    <span class="drag-handle">&#9776;</span>
    <input type="text" class="menu-text" value="<?= htmlspecialchars($text) ?>">
    <input type="text" class="menu-url" value="<?= htmlspecialchars($url) ?>">
    <button type="button" class="btn btn-danger remove-row">Delete</button>
</div>

<?php endforeach; ?>

</div>

<button type="button" class="btn btn-default" id="add-row">Add item</button>

<textarea name="menu_content" id="menu_content" style="display:none;"></textarea>

</div>

<p class="formbtns">
    <input type="submit" class="btn btn-primary" name="save_menu" value="Save and publish">
    <input type="button" class="btn btn-default" value="Close" onclick="self.close();">
</p>

</form>

<?php endif; ?>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const builder = document.getElementById("menu-builder");
    const textarea = document.getElementById("menu_content");

    new Sortable(builder, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'dragging'
    });

    document.getElementById("add-row").addEventListener("click", function () {
        const div = document.createElement("div");
        div.className = "menu-row";
        div.innerHTML = `
            <span class="drag-handle">&#9776;</span>
            <input type="text" class="menu-text" placeholder="Text">
            <input type="text" class="menu-url" placeholder="URL">
            <button type="button" class="btn btn-danger remove-row">Delete</button>
        `;
        builder.appendChild(div);
    });

    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-row")) {
            e.target.closest(".menu-row").remove();
        }
    });

    document.querySelector('form[method="POST"]').addEventListener("submit", function () {

        let lines = [];

        document.querySelectorAll(".menu-row").forEach(row => {
            const text = row.querySelector(".menu-text").value.trim();
            const url = row.querySelector(".menu-url").value.trim();

            if (text || url) {
                lines.push(text + "|" + url);
            }
        });

        textarea.value = lines.join("\n");
    });

});
</script>

</body>
</html>