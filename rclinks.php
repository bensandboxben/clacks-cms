<?php
/**
* Manage Reference Central links database
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("rclinks", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage Reference Central links database";
$table = "rclinks";
$dbkey = "rclinkid";
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
$query = "SELECT rclinkid, title, url, created, modified FROM rclinks WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT rclinkid) AS numrecs FROM $table WHERE 1=1 ";
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
function doAddRCLink (label, value) {
}
//-->
</script>
<table class="cmslist" width="100%" cellspacing="0">
<tr class="cmslisthead">
<?php
print_column_heading("Title", "title", "45%");
print_column_heading("Created", "created", "20%");
print_column_heading("Modified", "modified", "20%");
?>
<td width="15%">Actions <a href="#" onClick="window.open('newrclink.php?noreturn=1', 'rclink_new', 'width=500,height=550,top=50,left=50,resizable=yes,scrollbars=yes')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add reference central link</span></a></td>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr onMouseOver="this.style.background=\'#EEEEAA\'" onMouseOut="this.style.background=\'#F0F2FF\'">';
		echo '<td width="45%"><a href="' . $record->url . '" target="_blank">' . $record->title . '</a></td>';
		echo '<td width="20%">' . timestamp_to_short_date($record->created) . '</td>';
		echo '<td width="20%">' . timestamp_to_short_date($record->modified) . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newrclink.php?noreturn=1&action=edit&id=' . $record->$dbkey . '\', \'doc_edit\', \'width=500,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">edit</span></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=rclinks&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">delete</span></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>