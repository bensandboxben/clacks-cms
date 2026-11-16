<?php
/**
* Manage consultations
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("consultations", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage consultations";
$table = "consultations";
$dbkey = "conid";
$keyfield = "subject";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "startdate");
if (!$_SESSION[$table_st]) set_sort_type($table, "desc");
// setup environment variables - sort order, filter, offset
setup_env($table, "startdate"); // table and default sort order
include "includes/header.inc.php";
// Print table of records
$query = "SELECT * FROM consultations LEFT JOIN categories USING(catid) WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT conid) AS numrecs FROM $table LEFT JOIN categories USING(catid) WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (subject LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR category LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (subject LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR category LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if ($_SESSION[$table . "_category"]){
	$query .= "AND consultations.catid='" . $_SESSION[$table . '_category'] . "' ";
	$count_query .= "AND consultations.catid='" . $_SESSION[$table . '_category'] . "' ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";
$categories = $db->get_results("SELECT DISTINCT categories.catid, category FROM categories RIGHT JOIN consultations USING(catid) ORDER BY category", ARRAY_N);
$records = print_nav($table, $query, $count_query, $categories);
if ($user_message) echo $user_message;
?>

<table class="cmslist" width="100%" cellspacing="0">
<tr class="cmslisthead">
<?php
print_column_heading("Subject", "subject", "45%");
print_column_heading("Start Date", "startdate", "15%");
print_column_heading("Category", "category", "25%");
?>
<td width="15%">Actions <a href="#" onClick="window.open('newconsultation.php', 'consultation_new', 'width=800,height=550,top=50,left=50,resizable=yes,scrollbars=yes')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add record</span></a></td>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr onMouseOver="this.style.background=\'#EEEEAA\'" onMouseOut="this.style.background=\'#F0F2FF\'">';
		echo '<td width="45%">' . $record->subject . '</td>';
		echo '<td width="15%">' . $record->startdate . '</td>';
		echo '<td width="25%">' . $record->category . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newconsultation.php?action=edit&conid=' . $record->$dbkey . '\', \'consultation_edit\', \'width=800,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">edit</span></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=consultations&id=" . $record->$dbkey . "', 'delete', 'width=800,height=550,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">delete</span></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>