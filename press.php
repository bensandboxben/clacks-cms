<?php
/**
* Manage press releases
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("press", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage press releases";
$table = "press";
$dbkey = "prid";
$keyfield = "subject";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "pressdate");
if (!$_SESSION[$table_st]) set_sort_type($table, "desc");
// setup environment variables - sort order, filter, offset
setup_env($table, "pressdate"); // table and default sort order
include "includes/header.inc.php";
// Print table of records
$query = "SELECT * FROM press LEFT JOIN categories USING(catid) WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT prid) AS numrecs FROM $table LEFT JOIN categories USING(catid) WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (subject LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR category LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (subject LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR category LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if ($_SESSION[$table . "_category"]){
	$query .= "AND press.catid='" . $_SESSION[$table . '_category'] . "' ";
	$count_query .= "AND press.catid='" . $_SESSION[$table . '_category'] . "' ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";
$categories = $db->get_results("SELECT DISTINCT categories.catid, category FROM categories RIGHT JOIN press USING(catid) ORDER BY category", ARRAY_N);
$records = print_nav($table, $query, $count_query, $categories);
if ($user_message) echo $user_message;
?>

<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Subject", "subject", "45%");
print_column_heading("Date", "pressdate", "15%");
print_column_heading("Category", "category", "25%");
?>
<th width="15%">Actions <a href="#" onClick="window.open('newpress.php', 'press_new', 'width=800,height=550,top=50,left=50,resizable=yes,scrollbars=yes')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add record</span></a></th>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr>';
		echo '<td width="45%"><a href="/council/press/?release=' . $record->$dbkey . '">' . $record->subject . '</a></td>';
		echo '<td width="15%">' . $record->pressdate . '</td>';
		echo '<td width="25%">' . $record->category . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newpress.php?action=edit&prid=' . $record->$dbkey . '\', \'press_edit\', \'width=800,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">edit</span></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=press&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">delete</span></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>