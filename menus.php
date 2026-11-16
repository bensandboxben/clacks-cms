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
        <th width="15%">Actions <a href="#" onClick="window.open('editmenu.php?menufile=new', 'menu_new', 'width=700,height=500,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add external link" border="0"></a></th>
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
                    echo '<a href="#" onClick="window.open(\'editmenu.php?menufile=' . $menu . '\', \'menu_edit\', \'width=700,height=500,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit" border="0"></a></td></tr>';
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
