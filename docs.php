<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);


session_start();

$susername = $_SESSION['username'];
$suserid = $_SESSION['userid'];
$suserlevel = $_SESSION['userlevel'];
$suserserviceid = $_SESSION['userserviceid'];

/**
* Manage ClacksWeb content
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";

header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage documents";
$table = "docs";
$dbkey = "docid";
$keyfield = "name";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "name");
if (!$_SESSION[$table_st]) set_sort_type($table, "asc");
// setup environment variables - sort order, filter, offset
setup_env($table, "name"); // table and default sort order
// Process new content save
include "includes/header.inc.php";

// Print table of records
$query = "SELECT docid, name, live, reviewdate, category, parentserviceid FROM docs LEFT JOIN categories USING(catid), cms.services WHERE docs.serviceid=services.serviceid ";
if ($_SESSION["userlevel"] < 4) $query .= "AND (docs.serviceid='$_SESSION[userserviceid]' OR parentserviceid='$_SESSION[userserviceid]') ";


$count_query="SELECT COUNT(DISTINCT docid) AS numrecs FROM $table, cms.services WHERE docs.serviceid=services.serviceid ";


if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (name LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR description LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (name LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR description LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}

if ($_SESSION[$table . "_category"]){
	$query .= "AND docs.catid='" . $_SESSION[$table . '_category'] . "' ";
	$count_query .= "AND docs.catid='" . $_SESSION[$table . '_category'] . "' ";
}

if ($_SESSION["userlevel"] < 4) $count_query .= "AND (docs.serviceid='$_SESSION[userserviceid]' OR services.parentserviceid='$_SESSION[userserviceid]') ";


if (isset($_SESSION[$table_so])) 
{
	$query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
}

if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";

$categories = $db->get_results("SELECT DISTINCT categories.catid, category FROM categories RIGHT JOIN docs USING(catid) ORDER BY category", ARRAY_N);

$records = print_nav($table, $query, $count_query, $categories);


?>

<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Document", "name", "37%");
print_column_heading("Category", "category", "30%");
print_column_heading("Review date", "reviewdate", "15%");
?>
<th width="18%">Actions <a href="#" onClick="window.open('newdoc.php&quest;noreturn=1', 'doc_new', 'width=600,height=550,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add document" border="0"></a></th>
</tr>


<?php
if (!is_null($records) && (is_array($records) || $records instanceof Countable)) {
    if (count($records) > 0) {
        foreach ($records as $record) {
            echo '<tr>';
            echo '<td width="37%">' . $record->name . '</td>';
            echo '<td width="35%">' . $record->category . '</td>';
            echo '<td width="10%"';
            if ((mktime(0,0,0,substr($record->reviewdate, 5, 2), substr($record->reviewdate, 8, 2),substr($record->reviewdate, 0, 4))) < time()) {
                echo ' class="overdue"';
            } else if ((mktime(0,0,0,substr($record->reviewdate, 5, 2), substr($record->reviewdate, 8, 2),substr($record->reviewdate, 0, 4))-604800) < time()) {
                echo ' class="imminent"';
            } else {
                echo ' class="indate"';
            }
            echo '>' . $record->reviewdate;
            echo '</td>';
            echo '<td width="18%">';
            echo '<a href="#" onClick="window.open(\'newdoc.php?noreturn=1&action=edit&id=' . $record->$dbkey . '\', \'doc_edit\', \'width=600,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit" border="0"></a> ';
            echo "<a href=\"#\" onClick=\"window.open('delete.php?table=docs&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
            echo '<img src="images/delete.gif" alt="delete" border="0"></a> ';
            echo '<a href="#" onClick="window.open(\'docfiles.php?docid=' . $record->$dbkey . '\', \'doc_files\', \'width=850,height=500,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/files.gif" alt="files" border="0"></a> ';
            echo '<a href="#" onClick="window.open(\'docinfo.php?docid=' . $record->$dbkey . '\', \'doc_info\', \'width=500,height=500,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/info.gif" alt="information" border="0"></a> ';
            echo '</td></tr>';
        }
    } else {
        echo '<tr><td colspan="4">No records found.</td></tr>';
    }
} else {
    echo '<tr><td colspan="4">No records found.</td></tr>';
}


echo "</table>";
include "includes/footer.inc.php";
?>