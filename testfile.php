<?php

include_once "includes/prepend.inc.php";

echo "<h3>Areas (for review)</h3>";

$areas = $db->get_results("SELECT * FROM cms.areas ORDER BY name ASC");

if (!$areas) {
    echo "<p>No areas found or table doesn't exist.</p>";
    exit;
}

echo "<table class='table table-striped table-bordered'>";
echo "<tr>";

// Table headers (dynamic)
foreach ($areas[0] as $key => $value) {
    echo "<th>" . htmlspecialchars($key) . "</th>";
}

echo "</tr>";

// Rows
foreach ($areas as $area) {
    echo "<tr>";
    foreach ($area as $value) {
        echo "<td>" . htmlspecialchars($value) . "</td>";
    }
    echo "</tr>";
}

echo "</table>";
