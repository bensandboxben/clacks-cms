<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

include_once "includes/prepend.inc.php";

function isSafeFile($filename) {
    // Validate and sanitize the filename
    $sanitizedFilename = basename($filename);
    return $filename === $sanitizedFilename;
}

$today = gmdate("M d Y H:i:s");
$download_file = '/home/site/wwwroot/' . $_GET["ft"] . '/' . $_GET["file"];

if (isset($_GET['file']) && isSafeFile($_GET['file']) && file_exists($download_file)) {
    // Log the download attempt
    // Consider using appropriate logging mechanisms (e.g., syslog, database, file)
    // logDownload($_GET['file'], $_SERVER['REMOTE_ADDR'], $today);

    // Send headers for file download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($download_file) . '"');
    header('Content-Length: ' . filesize($download_file));

    // Output the file
    readfile($download_file);
    //exit;
} else {
    // Log invalid download attempt
    // logInvalidDownload($_GET['file'], $_SERVER['REMOTE_ADDR'], $today);

    // Display an error message
    echo "<p>Oops! This download link is invalid.</p>";
    print_r($_GET);
}
?>
