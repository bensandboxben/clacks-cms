<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

/**
* Manage ClacksWeb external links database
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("extlinks", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage external links database";
$table = "extlinks";
$dbkey = "extlinkid";
$keyfield = "title";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "title");
if (!$_SESSION[$table_st]) set_sort_type($table, "asc");
// setup environment variables - sort order, filter, offset
setup_env($table, "title"); // table and default sort order
// Process new content save
include "includes/header.inc.php";
// Print table of records
$query = "SELECT extlinkid, title, url, created, modified FROM extlinks WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT extlinkid) AS numrecs FROM $table WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (title LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR description LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (title LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR description LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";

$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>
<script language="JavaScript">
<!--
function doAddExtLink (label, value) {
}
//-->
</script>
<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Title", "title", "45%");
print_column_heading("Created", "created", "20%");
print_column_heading("Modified", "modified", "20%");
?>
<th width="15%">Actions <a href="#" onClick="window.open('newextlink.php?noreturn=1', 'extlink_new', 'width=500,height=500,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add external link" border="0"></a></th>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr>';
		echo '<td width="45%">' . $record->title . '</td>';
		echo '<td width="20%">' . timestamp_to_short_date($record->created) . '</td>';
		echo '<td width="20%">' . timestamp_to_short_date($record->modified) . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newextlink.php?noreturn=1&action=edit&id=' . $record->$dbkey . '\', \'doc_edit\', \'width=500,height=400,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit" border="0"></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=extlinks&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<img src="images/delete.gif" alt="delete" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'extlinkinfo.php?extlinkid=' . $record->$dbkey . '\', \'extlink_info\', \'width=500,height=500,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/info.gif" alt="information" border="0"></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>