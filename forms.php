<?php

session_start();

$susername = $_SESSION['username'];
$suserid = $_SESSION['userid'];
$suserlevel = $_SESSION['userlevel'];
$suserserviceid = $_SESSION['userserviceid'];

/**
* Manage ClacksWeb forms
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("forms", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage forms";
$table = "forms";
$dbkey = "formid";
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

$query = "SELECT formid, name, service, live, parentserviceid FROM forms LEFT JOIN cms.services USING(serviceid) WHERE 1=1 ";
if ($_SESSION["userlevel"] < 4) $query .= "AND (forms.serviceid='$_SESSION[userserviceid]' OR parentserviceid='$_SESSION[userserviceid]') ";

$count_query="SELECT COUNT(DISTINCT formid) AS numrecs FROM $table, cms.services WHERE forms.serviceid=services.serviceid ";


if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (name LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR description LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (name LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR description LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}

if ($_SESSION["userlevel"] < 4) $count_query .= "AND (forms.serviceid='$_SESSION[userserviceid]' OR parentserviceid='$_SESSION[userserviceid]') ";


if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";

$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>

<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Form", "name", "40%");
print_column_heading("Service", "service", "35%");
print_column_heading("Live?", "live", "10%");
?>
<th width="15%">Actions <a href="#" onClick="window.open('newform.php?noreturn=1', 'form_new', 'width=500,height=500,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add form" border="0"></a></th>
</tr>
<?php

if (isset($records) && (is_array($records) || $records instanceof Countable) && count($records) > 0) {
    foreach ($records as $record) {
		echo '<tr>';
		echo '<td width="40%">' . $record->name . '</td>';
		echo '<td width="35%">' . $record->service . '</td>';
		echo '<td width="10%">';
		if ($record->live == 1) { echo 'Yes'; } else { echo 'No'; }
		echo '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newform.php?noreturn=1&action=edit&id=' . $record->$dbkey . '\', \'form_edit\', \'width=500,height=400,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit" border="0"></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=forms&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,top=50,left=50,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<img src="images/delete.gif" alt="delete" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'formfiles.php?formid=' . $record->$dbkey . '\', \'form_files\', \'width=700,height=400,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/files.gif" alt="files" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'forminfo.php?formid=' . $record->$dbkey . '\', \'form_info\', \'width=500,height=500,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/info.gif" alt="information" border="0"></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>