<?php
/**
* Manage election records
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("elections", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage elections";
$table = "elections.elections";
$dbkey = "electionid";
$keyfield = "body";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "electiondate");
if (!$_SESSION[$table_st]) set_sort_type($table, "desc");
// setup environment variables - sort order, filter, offset
setup_env($table, "electiondate"); // table and default sort order
// Process new content save
include "includes/header.inc.php";
// Print table of records
$_SESSION['displaynum'] = 10;
$query = "SELECT electionid, body, electiondate FROM elections.elections LEFT JOIN elections.bodies USING(bodyid) WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT electionid) AS numrecs FROM $table WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (body LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (body LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";

$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>

<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Type", "body", "60%");
print_column_heading("Date", "electiondate", "25%");
?>
<th width="15%">Actions <a href="#" onClick="window.open('newelection.php?noreturn=1', 'form_new', 'width=500,height=600,top=50,left=50,resizable=yes,scrollbars=yes')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add election</span></a></th>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr>';
		echo '<td width="60%"><a href="/council/electionresults/?election=' . $record->electionid . '" target="_blank">';
		echo $record->body;
		echo '</a></td>';
		echo '<td width="25%">' . $record->electiondate . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newelection.php?action=edit&id=' . $record->$dbkey . '\', \'election_edit\', \'width=500,height=600,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">edit member</span></a> ';
		echo '<a href="#" onClick="window.open(\'electioncandidates.php?electionid=' . $record->$dbkey . '\', \'candidates_edit\', \'width=700,height=440,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-bodies" aria-hidden="true" focusable="false"><use href="images/icons.svg#users"></use></svg><span class="sr-only">edit candidates</span></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=elections.elections&id=" . $record->$dbkey . "', 'delete', 'width=400,height=600,top=50,left=50,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">delete</span></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>