<?php
/**
* Manage press licenses
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("licenses", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage licenses";
$table = "licenses";
$dbkey = "licid";
$keyfield = "name";
$table_so = $table . "_so";
$table_st = $table . "_st";
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "name");
if (!$_SESSION[$table_st]) set_sort_type($table, "asc");
// setup environment variables - sort order, filter, offset
setup_env($table, "name"); // table and default sort order
include "includes/header.inc.php";
// Print table of records
$query = "SELECT * FROM licenses LEFT JOIN liccats USING(catid) WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT licid) AS numrecs FROM $table LEFT JOIN liccats USING(catid) WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (name LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR category LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (name LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR category LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if ($_SESSION[$table . "_category"]){
	$query .= "AND licenses.catid='" . $_SESSION[$table . '_category'] . "' ";
	$count_query .= "AND licenses.catid='" . $_SESSION[$table . '_category'] . "' ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";
$categories = $db->get_results("SELECT DISTINCT liccats.catid, category, caturl FROM liccats RIGHT JOIN licenses USING(catid) ORDER BY category", ARRAY_N);
$records = print_nav($table, $query, $count_query, $categories);
if ($user_message) echo $user_message;
?>

<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Name", "name", "55%");
print_column_heading("Category", "category", "30%");
?>
<th width="15%">Actions <a href="#" onClick="window.open('newlicense.php', 'lic_new', 'width=650,height=500,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add record" border="0"></a></th>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr>';
		echo '<td width="55%"><a href="/regulation/licensing/' . $record->caturl . '/' . $record->licurl . '/">' . $record->name . '</a></td>';
		echo '<td width="30%">' . $record->category . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newlicense.php?action=edit&licid=' . $record->$dbkey . '\', \'lic_edit\', \'width=650,height=500,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit" border="0"></a>  ';
		echo '<a href="#" onClick="window.open(\'licenseforms.php?licid=' . $record->$dbkey . '\', \'lic_forms\', \'width=700,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/documents.gif" alt="related forms" border="0"></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=licenses&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<img src="images/delete.gif" alt="delete" border="0"></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>