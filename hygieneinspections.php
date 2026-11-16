<?php
/**
* Manage Hygiene Inspections
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("hygieneinspections", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage Hygiene Inspections";
$table = "hygieneinspections";
$dbkey = "inspectionid";
$keyfield = "establishment";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "inspectiondate");
if (!$_SESSION[$table_st]) set_sort_type($table, "desc");
// setup environment variables - sort order, filter, offset
setup_env($table, "inspectiondate"); // table and default sort order
// Process new content save
include "includes/header.inc.php";
// Print table of records
$query = "SELECT hygieneinspections.*, establishment FROM hygieneinspections LEFT JOIN hiestablishments USING(establishmentid) WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT inspectionid) AS numrecs FROM $table WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (summary LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR establishment LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (summary LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR establishment LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";

$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>

<table class="cmslist" width="100%" cellspacing="0">
<tr class="cmslisthead">
<?php
print_column_heading("Establishment", "establishment", "45%");
print_column_heading("Inspection date", "inspectiondate", "20%");
print_column_heading("Grade", "grade", "20%");
?>
<td width="15%">Actions <a href="#" onClick="window.open('newhygieneinspection.php', 'hi_new', 'width=500,height=500,top=50,left=50,resizable=yes,scrollbars=yes')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new inspection</span></a></td>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr><td width="45%">' . $record->establishment . '</td>';
		echo '<td width="20%">' . $record->inspectiondate . '</td>';
		echo '<td width="20%"';
		echo '>' . $record->grade . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newhygieneinspection.php?action=edit&id=' . $record->$dbkey . '\', \'hi_edit\', \'width=500,height=500,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">edit</span></a> ';
		echo '<a href="#" onClick="window.open(\'newhygieneinspection.php?noreturn=1&action=copy&id=' . $record->$dbkey . '\', \'hi_copy\', \'width=500,height=500,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-copy" aria-hidden="true" focusable="false"><use href="images/icons.svg#copy"></use></svg><span class="sr-only">copy inspection</span></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=hygieneinspections&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">delete</span></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>