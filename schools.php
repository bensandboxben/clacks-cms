<?php
/**
* Manage schools
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("schools", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage schools";
$table = "schools";
$dbkey = "schoolid";
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
include "includes/header.inc.php";
// Print table of records
$query = "SELECT * FROM schools WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT schoolid) AS numrecs FROM $table WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (name LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (name LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";

$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>

<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("School", "name", "70%");
print_column_heading("Sector", "sector", "15%");
?>
<th width="15%">Actions</th>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr>';
		echo '<td width="70%"><a href="/learning/schools/' . $record->schoolurl . '/">' . $record->name . '</a></td>';
		echo '<td width="15%">' . $record->sector . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newschool.php?action=edit&schoolid=' . $record->$dbkey . '\', \'school_edit\', \'width=800,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">edit</span></a> ';
		echo '<a href="#" onClick="window.open(\'schooldocs.php?schoolid=' . $record->$dbkey . '\', \'school_docs\', \'width=600,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-documents" aria-hidden="true" focusable="false"><use href="images/icons.svg#folder-open"></use></svg><span class="sr-only">related documents</span></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>