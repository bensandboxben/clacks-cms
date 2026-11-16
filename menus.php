<?php
/**
 * Manage ClacksWeb edit contextual menus
 * @author Daniel Champion <dchampion@clacks.gov.uk>
 * @package ClacksWeb v2 CMS
 * @version 0.1
 */
include_once "includes/prepend.inc.php";

if (check_auth("extlinks", $_SESSION["userid"]) == 0) {
    header("Location: index.php");
}

header("Cache-Control: private");

$CMStitle = "Manage menus";
include "includes/header.inc.php";
?>
<table class="table table-striped table-bordered" width="100%" cellspacing="0">
    <tr>
        <th width="80%">Menu file</th>
        <th width="15%">Actions <a href="#" onClick="window.open('editmenu.php?menufile=new', 'menu_new', 'width=700,height=500,top=50,left=50,resizable=yes,scrollbars=yes')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add external link</span></a></th>
    </tr>
    <?php

    $menusDirectory = '/home/site/wwwroot/cms/menus'; // Define the directory path
    // Check if the directory exists
    if (is_dir($menusDirectory)) {
        // Attempt to open the directory
        if ($handle = opendir($menusDirectory)) {
            $menus = array();

            // Read directory contents
            while (false !== ($file = readdir($handle))) {
                if (($file != ".") && ($file != "..")) {
                    $menus[] = $file;
                }
            }
            closedir($handle);

            // If files are found, proceed with displaying the table
            if (!empty($menus)) {
                sort($menus);
                // Output the table
                foreach ($menus as $menu) {
                    echo '<tr>';
                    echo '<td width="80%">' . $menu . '</td>';
                    echo '<td width="20%">';
                    echo '<a href="#" onClick="window.open(\'editmenu.php?menufile=' . $menu . '\', \'menu_edit\', \'width=700,height=500,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">edit</span></a></td></tr>';
                }
            } else {
                echo "<tr><td colspan='2'>No files found in the directory.</td></tr>";
            }
        } else {
            echo "<tr><td colspan='2'>Unable to open the directory.</td></tr>";
        }
    } else {
        echo "<tr><td colspan='2'>Directory does not exist.</td></tr>";
    }
    ?>
</table>
<?php
include "includes/footer.inc.php";
?>
