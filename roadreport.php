<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

/**
* Manage Road Works Report
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("roadreport", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage Road Report";
$table = "roadreport.info";
$dbkey = "id";
$keyfield = "road";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "stdate");
if (!$_SESSION[$table_st]) set_sort_type($table, "desc");
// setup environment variables - sort order, filter, offset
setup_env($table, "stdate"); // table and default sort order
include "includes/header.inc.php";
// Print table of records
$query = "SELECT * FROM roadreport.info WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT id) AS numrecs FROM $table WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (road LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR town LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (road LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR town LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";
$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>

<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Road", "road", "20%");
print_column_heading("Information", "information", "50%");
print_column_heading("Start Date", "stdate", "15%");
?>
<th width="15%">Actions <a href="#" onClick="window.open('newroadreport.php', 'roadreport_new', 'width=600,height=550,top=50,left=50,resizable=yes,scrollbars=yes')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add record</span></a></th>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr>';
		echo '<td width="20%">' . $record->road . '</td>';
		echo '<td width="50%">' . $record->information . '</td>';
		echo '<td width="15%">' . $record->stdate . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newroadreport.php?action=edit&id=' . $record->$dbkey . '\', \'roadreport_edit\', \'width=600,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">edit</span></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=roadreport.info&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">delete</span></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>