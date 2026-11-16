<?php
/**
* Manage tenders
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("tenders", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage tenders";
$table = "tenders";
$dbkey = "tenderid";
$keyfield = "tender";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "tender");
if (!$_SESSION[$table_st]) set_sort_type($table, "asc");
// setup environment variables - sort order, filter, offset
setup_env($table, "tender"); // table and default sort order
// Process new content save
include "includes/header.inc.php";
// Print table of records
$query = "SELECT tenderid, tender, startdate, enddate FROM tenders WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT tenderid) AS numrecs FROM $table WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (tender LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR description LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (tender LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR description LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";

$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>

<table class="cmslist" width="100%" cellspacing="0">
<tr class="cmslisthead">
<?php
print_column_heading("Tender", "tender", "45%");
print_column_heading("Start date", "startdate", "20%");
print_column_heading("End date", "enddate", "20%");
?>
<td width="15%">Actions <a href="#" onClick="window.open('newtender.php', 'tender_new', 'width=500,height=500,top=50,left=50,resizable=yes,scrollbars=yes')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new tender</span></a></td>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		$current = 0;
		$archive = 0;
		if (((mktime(0,0,0,substr($record->startdate, 5, 2), substr($record->startdate, 8, 2),substr($record->startdate, 0, 4))) < time()) && ((mktime(0,0,0,substr($record->enddate, 5, 2), substr($record->enddate, 8, 2),substr($record->enddate, 0, 4))) > time())) $current = 1;
		if (mktime(0,0,0,substr($record->enddate, 5, 2), substr($record->enddate, 8, 2),substr($record->enddate, 0, 4)) < time()) $archive = 1;
		echo '<tr onMouseOver="this.style.background=\'#EEEEAA\'" onMouseOut="this.style.background=\'#F0F2FF\'"';
		if ($current == 1) {
			echo ' class="indate"';
		} else if ($archive == 1) {
			echo ' class="past"';
		} else {
			echo ' class="future"';
		}
		echo '>';
		echo '<td width="45%">' . $record->tender . '</td>';
		echo '<td width="20%">' . $record->startdate . '</td>';
		echo '<td width="20%"';
		echo '>' . $record->enddate . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newtender.php?action=edit&id=' . $record->$dbkey . '\', \'tender_edit\', \'width=500,height=500,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">edit</span></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=tenders&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">delete</span></a> ';
		echo '<a href="#" onClick="window.open(\'tenderfiles.php?tenderid=' . $record->$dbkey . '\', \'tender_files\', \'width=700,height=400,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-files" aria-hidden="true" focusable="false"><use href="images/icons.svg#paperclip"></use></svg><span class="sr-only">files</span></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>