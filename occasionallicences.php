<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

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
$CMStitle = "Occasional licences";
$table = "occasionallicences";
$dbkey = "id";
$keyfield = "location";
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
$query = "SELECT * FROM clacksweb2.occasionallicences WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT id) AS numrecs FROM $table WHERE 1=1 ";

if ($_SESSION[$table . "_filter"]) {
	$query .= "AND proposal LIKE '%" . $_SESSION[$table . '_filter'] . "%' ";
	$count_query .= "AND proposal LIKE '%" . $_SESSION[$table . '_filter'] . "%' ";
}

if (isset($_SESSION[$table_so]))
{
	$table_so = $_SESSION[$table_so];
	if ($table_so == "status")
	{
		$table_so = "statusid";
	}
	$query .= " ORDER BY $table_so $_SESSION[$table_st]";
}

if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";
$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;


?>





<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Reference", "reference", "10%");
print_column_heading("Location", "location", "35%");
print_column_heading("Status", "status", "10%");
print_column_heading("Event Date", "eventdate", "10%");
print_column_heading("Proposed Date", "propdate", "12%");
print_column_heading("Granted Date", "granteddate", "13%");
?>
<th width="15%">Actions <a href="#" onClick="window.open('newocclic.php', 'occlic_new', 'width=800,height=550,top=50,left=50,resizable=yes,scrollbars=yes')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add record</span></a></th>
</tr>
<?php
if (is_array($records) && count($records) > 0) {
    foreach ($records as $record) {

	if ($record->statusid == '1'){
		$status = "Draft";
		}
	else if ($record->statusid == '3'){
		$status = "Live";
		}
	else if ($record->statusid == '4'){
		$status = "Expired";
		}
	else{
		$status = "Not working";
		}

		echo '<tr>';
		echo '<td width="10%">' . $record->reference . '</td>';
		echo '<td width="35%"><a href="/regulation/occasionallicensing/?id=' . $record->$dbkey . '">' . $record->location . '</a></td>';
		echo '<td width="10%">' . $status . '</td>';
		echo '<td width="10%">' . $record->eventdate . '</td>';
		echo '<td width="10%">' . $record->propdate . '</td>';
		echo '<td width="10%">' . $record->granteddate . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newocclic.php?action=edit&id=' . $record->$dbkey . '\', \'tro_edit\', \'width=800,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">edit</span></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=occasionallicences&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">delete</span></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>