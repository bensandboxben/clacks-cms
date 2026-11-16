<?php

$secret = "clacks_menu_sync_2026_secure_9f3kL2x8Qp";
$targetDir = "/home/site/wwwroot/section";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request");
}

if (!isset($_POST['secret']) || $_POST['secret'] !== $secret) {
    die("Invalid secret");
}

if (empty($_POST['filename']) || empty($_POST['content'])) {
    die("Missing data");
}

$filename = basename($_POST['filename']);
$content = $_POST['content'];

$filepath = $targetDir . "/" . $filename;

if (!is_dir($targetDir)) {
    die("Target directory missing");
}

if (!is_writable($targetDir)) {
    die("Directory not writable");
}

if (file_exists($filepath)) {
    copy($filepath, $filepath . ".bak");
}

if (file_put_contents($filepath, $content, LOCK_EX) === false) {
    die("Write failed");
}

echo "OK";

?>