<?php
/**
* Manage ClacksWeb events
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("events", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage events";
$table = "events.events";
$dbkey = "eventid";
$keyfield = "event";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "startdate");
if (!$_SESSION[$table_st]) set_sort_type($table, "desc");
// setup environment variables - sort order, filter, offset
setup_env($table, "startdate"); // table and default sort order
// Process new content save
include "includes/header.inc.php";
// Print table of records
$query = "SELECT eventid, event, startdate, venue FROM events.events LEFT JOIN events.venues USING(venueid) WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT eventid) AS numrecs FROM $table WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (event LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR description LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR keywords LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (event LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR description LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR keywords LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";

$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>

<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Event", "event", "40%");
print_column_heading("Date", "startdate", "15%");
print_column_heading("Venue", "venue", "30%");
?>
<th width="15%">Actions <a href="#" onClick="window.open('newevent.php?noreturn=1', 'event_new', 'width=750,height=640,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add event" border="0"></a></th>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr>';
		echo '<td width="40%"><a href="/community/events/?event=' . $record->eventid . '" target="_blank">' . $record->event . '</a></td>';
		echo '<td width="15%">' . $record->startdate . '</td>';
		echo '<td width="30%">' . $record->venue . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newevent.php?noreturn=1&action=edit&id=' . $record->$dbkey . '\', \'event_edit\', \'width=700,height=540,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit event" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'newevent.php?noreturn=1&action=copy&id=' . $record->$dbkey . '\', \'event_copy\', \'width=700,height=540,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/copy.gif" alt="copy event" border="0"></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=events.events&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<img src="images/delete.gif" alt="delete event" border="0"></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>