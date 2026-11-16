<?php

$dir = "/home/site/wwwroot/section";

$files = scandir($dir);

foreach ($files as $file) {

    if (substr($file, -4) !== ".php") continue;

    $path = $dir . "/" . $file;

    echo "Fixing: $file\n";

    $html = file_get_contents($path);

    // Backup
    copy($path, $path . ".bak");

    /*
    |--------------------------------------------------------------------------
    | FIX 1: Close nested <a> tags properly
    |--------------------------------------------------------------------------
    */

    // Replace invalid nesting: </strong><a ? </strong></a><a
    $html = preg_replace(
        '#</strong>\s*<a class="list-group-item#',
        '</strong></a><a class="list-group-item',
        $html
    );

    /*
    |--------------------------------------------------------------------------
    | FIX 2: Fix cases where <a nonactive> wraps children
    |--------------------------------------------------------------------------
    */

    $html = preg_replace_callback(
        '#<a class="list-group-item nonactive">(.*?)</a>#s',
        function ($matches) {

            $content = $matches[1];

            // Extract title
            if (preg_match('#<strong>(.*?)</strong>#', $content, $titleMatch)) {

                $title = $titleMatch[0];

                // Remove nested links
                $content = preg_replace('#<a class="list-group-item.*?</a>#s', '', $content);

                return '<a class="list-group-item nonactive">' . $title . '</a>' . $matches[1];
            }

            return $matches[0];
        },
        $html
    );

    /*
    |--------------------------------------------------------------------------
    | FIX 3: Remove duplicate closing tags
    |--------------------------------------------------------------------------
    */
    $html = str_replace('</a></a>', '</a>', $html);

    /*
    |--------------------------------------------------------------------------
    | SAVE
    |--------------------------------------------------------------------------
    */
    file_put_contents($path, $html);

    echo "? Fixed $file\n";
}

echo "DONE\n";