<?php
/**
* Manage ClacksWeb content
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("contacts", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage contacts";
$table = "contacts";
$dbkey = "contactid";
$keyfield = "jobtitle";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "jobtitle");
if (!$_SESSION[$table_st]) set_sort_type($table, "asc");
// setup environment variables - sort order, filter, offset
setup_env($table, "jobtitle"); // table and default sort order
include "includes/header.inc.php";
// Print table of records
$query = "SELECT contactid, jobtitle, name, service, parentserviceid FROM contacts LEFT JOIN cms.services USING(serviceid) WHERE 1=1 ";
if ($_SESSION["userlevel"] < 4) $query .= "AND (contacts.serviceid='$_SESSION[userserviceid]' OR parentserviceid='$_SESSION[userserviceid]') ";
$count_query="SELECT COUNT(DISTINCT contactid) AS numrecs FROM contacts, cms.services WHERE services.serviceid=contacts.serviceid ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (jobtitle LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR name LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (jobtitle LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR name LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if ($_SESSION["userlevel"] < 4) $count_query .= "AND (contacts.serviceid='$_SESSION[userserviceid]' OR parentserviceid='$_SESSION[userserviceid]') ";
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";

$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>

<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Job title", "jobtitle", "30%");
print_column_heading("Name", "name", "35%");
print_column_heading("Service", "service", "20%");
?>
<th width="15%">Actions <a href="#" onClick="window.open('newcontact.php?noreturn=1', 'contact_new', 'width=600,height=620,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add record" border="0"></a></th>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr>';
		echo '<td width="30%">' . $record->jobtitle . '</td>';
		echo '<td width="35%">' . $record->name . '</td>';
		echo '<td width="20%">' . $record->service . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newcontact.php?noreturn=1&action=edit&id=' . $record->$dbkey . '\', \'contact_edit\', \'width=600,height=620,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit" border="0"></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=contacts&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<img src="images/delete.gif" alt="delete" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'contactinfo.php?contactid=' . $record->$dbkey . '\', \'contact_info\', \'width=600,height=620,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/info.gif" alt="contact information" border="0"></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>