<?php
/**
* Manage jobpack files
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("jobpacks", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage jobpack files";
$table = "hr.files";
$dbkey = "fileid";
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
include "includes/header.inc.php";
// Print table of records
$query = "SELECT * FROM hr.files LEFT JOIN hr.filetypes USING(typeid) WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT fileid) AS numrecs FROM $table LEFT JOIN hr.filetypes USING(typeid) WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (title  LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR type LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (title LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR type LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if ($_SESSION[$table . "_category"]){
	$query .= "AND files.typeid='" . $_SESSION[$table . '_category'] . "' ";
	$count_query .= "AND files.typeid='" . $_SESSION[$table . '_category'] . "' ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";
$types = $db->get_results("SELECT DISTINCT filetypes.typeid, type FROM hr.filetypes RIGHT JOIN hr.files USING(typeid) ORDER BY type", ARRAY_N);
$records = print_nav($table, $query, $count_query, $types);
if ($user_message) echo $user_message;
?>

<table class="cmslist" width="100%" cellspacing="0">
<tr class="cmslisthead">
<?php
print_column_heading("Title", "title", "35%");
print_column_heading("File name", "filename", "30%");
print_column_heading("Category", "category", "20%");
?>
<td width="15%">Actions <a href="#" onClick="window.open('newjobpackfile.php', 'jp_new', 'width=500,height=300,top=50,left=50,resizable=yes,scrollbars=yes')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add record</span></a></td>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr onMouseOver="this.style.background=\'#EEEEAA\'" onMouseOut="this.style.background=\'#F0F2FF\'">';
		echo '<td width="35%">' . $record->title . '</a></td>';
		echo '<td width="30%"><a target="_blank" href="/jobpacks/' . $record->filename . '">' . $record->filename . '</a></td>';
		echo '<td width="20%">' . $record->type . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newjobpackfile.php?action=edit&id=' . $record->$dbkey . '\', \'jp_edit\', \'width=500,height=300,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">edit</span></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=hr.files&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">delete</span></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>