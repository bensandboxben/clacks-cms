<?php
/**
* Manage Council meetings diary
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("meetings", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage meetings";
$table = "events.meetings";
$dbkey = "meetingid";
$keyfield = "bodies.body";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "meetingdate");
if (!$_SESSION[$table_st]) set_sort_type($table, "desc");
// setup environment variables - sort order, filter, offset
setup_env($table, "meetingdate"); // table and default sort order
// Process new content save
include "includes/header.inc.php";
// Print table of records
$query = "SELECT meetingid, body, meetingdate, venue, published FROM events.meetings LEFT JOIN clacksweb2.bodies USING(bodyid), events.venues WHERE meetings.venueid=venues.venueid ";
$count_query="SELECT COUNT(DISTINCT meetingid) AS numrecs FROM $table LEFT JOIN clacksweb2.bodies USING(bodyid) WHERE 1=1 ";
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
print_column_heading("Body", "body", "35%");
print_column_heading("Date", "meetingdate", "15%");
print_column_heading("Agenda live?", "published", "15%");
print_column_heading("Venue", "venue", "20%");
?>
<th width="15%">Actions <a href="#" onClick="window.open('newmeeting.php?noreturn=1', 'meeting_new', 'width=650,height=540,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add meeting" border="0"></a></th>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr>';
		echo '<td width="35%"><a href="/council/meetings/?meeting=' . $record->meetingid . '" target="_blank">' . $record->body . '</a></td>';
		echo '<td width="15%">' . $record->meetingdate . '</td>';
		echo '<td width="15%">';
		if ($record->published) {
			echo 'Yes';
		} else {
			echo 'No';
		}
		echo '</td>';
		echo '<td width="20%">' . $record->venue . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newmeeting.php?noreturn=1&action=edit&id=' . $record->$dbkey . '\', \'event_edit\', \'width=650,height=540,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit meeting" border="0"></a> ';
//		echo '<a href="#" onClick="window.open(\'meetingfiles.php?meetingid=' . $record->$dbkey . '\', \'meeting_files\', \'width=700,height=400,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/files.gif" alt="files" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'agenda.php?meetingid=' . $record->$dbkey . '\', \'agenda\', \'width=700,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/items.gif" alt="agenda" border="0"></a> ';
		echo '<a target="_blank" href="http://clacksweb/council/newmeetings/agenda/' . $record->$dbkey . '/?preview=1"><img src="images/preview.gif" alt="preview" border="0"></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=events.meetings&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<img src="images/delete.gif" alt="delete meeting" border="0"></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>