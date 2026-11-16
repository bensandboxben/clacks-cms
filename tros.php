<?php
/**
* Manage traffic regulation orders
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("tros", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage traffic regulation orders";
$table = "tros";
$dbkey = "troid";
$keyfield = "title";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "propdate");
if (!$_SESSION[$table_st]) set_sort_type($table, "desc");
// setup environment variables - sort order, filter, offset
setup_env($table, "propdate"); // table and default sort order
include "includes/header.inc.php";
// Print table of records
$query = "SELECT * FROM tros WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT troid) AS numrecs FROM $table WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (title LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR proposal LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (title LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR proposal LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";
$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>

<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Title", "title", "55%");
print_column_heading("Date propsed", "propdate", "15%");
print_column_heading("Date made", "madedate", "15%");
?>
<th width="15%">Actions <a href="#" onClick="window.open('newtro.php', 'tro_new', 'width=800,height=550,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add record" border="0"></a></td>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr>';
		echo '<td width="55%"><a href="/transport/roadorders/?order=' . $record->$dbkey . '">' . $record->title . '</a></td>';
		echo '<td width="15%">' . $record->propdate . '</td>';
		echo '<td width="15%">' . $record->madedate . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newtro.php?action=edit&troid=' . $record->$dbkey . '\', \'tro_edit\', \'width=800,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit" border="0"></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=tros&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<img src="images/delete.gif" alt="delete" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'trofiles.php?troid=' . $record->$dbkey . '\', \'tro_files\', \'width=700,height=400,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/files.gif" alt="files" border="0"></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>