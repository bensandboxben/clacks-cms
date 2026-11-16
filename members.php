<?php
/**
* Manage Councillor records
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("members", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage Councillors";
$table = "councillors";
$dbkey = "emid";
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
$_SESSION['displaynum'] = 18;
$query = "SELECT emid, name, vacant, councillors.wardid, wardname, wardurl FROM councillors LEFT JOIN cag.newwardlt USING(wardid) WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT emid) AS numrecs FROM $table WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (name LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR wardname LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (name LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR wardname LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";

$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>

<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Name", "name", "40%");
print_column_heading("Ward", "wardname", "45%");
?>
<th width="15%">Actions</th>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr>';
		echo '<td width="40%"><a href="/council/wards/' . $record->wardurl . '/' . $record->$dbkey . '/" . target="_blank">';
		if ($record->vacant) {
			echo 'Vacancy';
		} else {
			echo $record->name;
		}
		echo '</a></td>';
		echo '<td width="45%">' . $record->wardname . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newmember.php?action=edit&id=' . $record->$dbkey . '\', \'member_edit\', \'width=600,height=600,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit member" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'memberbodies.php?emid=' . $record->$dbkey . '\', \'memberbodies_edit\', \'width=700,height=440,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/bodies.gif" alt="edit body membership" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'memberinterests.php?emid=' . $record->$dbkey . '\', \'memberinterests_edit\', \'width=700,height=440,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/interests.gif" alt="edit register of interests" border="0"></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>