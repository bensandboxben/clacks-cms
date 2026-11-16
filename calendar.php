<?php
/**
* Manage Council meetings via a calendar
* @author Ben Shory
* @package ClacksWeb v2 CMS
* @version 0.1
*/

include_once "includes/prepend.inc.php";
//if (check_auth("intranetcontent", $_SESSION["userid"]) == 0) {
//	header("Location: index.php");
//}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Browsable Agenda Calendar";
include "includes/header.inc.php";




$year = date("o");
$month = date("m"); // give the month to be fed in originally ie this months
$noOfDays = 1;


if ($_GET['prev'] != ''){ // when previous button is hit, makes sure the month changes but the year is kept the same

	$month = $_GET['calMonth'] - 1;
	$year = $_GET['calYear'];

	if ($month < 10) // make sure month is double figures
		$length=strlen($month);
			if ($length == 1)
				$month = "0".$month;
}


if ($_GET['next'] != ''){ // next button, to change month but not year

	$month = $_GET['calMonth'] + 1;
	$year = $_GET['calYear'];

	if ($month < 10) // make sure month is double figures
		$length=strlen($month);
			if ($length == 1)
				$month = "0".$month;
}

if ($_GET['prevYear'] != ''){ // prev Year button, to change year but not month

	$year = $_GET['calYear'] - 1;
	$month = $_GET['calMonth'];

	if ($month < 10) // make sure month is double figures
		$length=strlen($month);
			if ($length == 1)
				$month = "0".$month;
}

if ($_GET['nextYear'] != ''){ // next Year button, to change year but not month

	$year = $_GET['calYear'] + 1;
	$month = $_GET['calMonth'];

	if ($month < 10) // make sure month is double figures
		$length=strlen($month);
			if ($length == 1)
				$month = "0".$month;
}

	$firstDayOfMonth = strftime(date("N", mktime(0, 0, 0, $month, 1, $year)));
	$lastDay = strftime(date("d", mktime(0, 0, 0, $month+1 , 0, $year))); // day zero of next month ie amount of days in this month
	$lastDayNumber = strftime(date("N", mktime(0, 0, 0, $month +1 , 0, $year))); // to get the day of the last day in the month


			// set up previous year button form to change the timestamp
			?>
			<!--form action="<?php $PHP_SELF; ?>" method="get" id="previousY" >
			<input type="hidden" name="prevYear" value="prevYear">
			<input type="hidden" name="calYear" value="<?php print $year ?>">
			<input type="hidden" name="calMonth" value="<?php print $month ?>">
			<table class="calendar">
			<tr class="calendarhead"><th colspan='1'>
			<input type="submit" value="<<" >
			</th>
			</form>

			<?php
			// set up previous month button form to change the timestamp
			?>
			<form action="<?php $PHP_SELF; ?>" method="get" id="previous" >
			<input type="hidden" name="prev" value="prev">
			<input type="hidden" name="calMonth" value="<?php print $month ?>">
			<input type="hidden" name="calYear" value="<?php print $year ?>">
			<th colspan='1'>
			<input type="submit" value="<" >
			</th>
			</form>


			<?php
			echo "<th colspan='3'>".date("M, Y", mktime(0,0,0,$month,1,$year))."</th>";


			// set up next month button form to change the timestamp
			?>
			<form action="<?php $PHP_SELF; ?>" method="get" id="next">
			<input type="hidden" name="next" value="next">
			<input type="hidden" name="calMonth" value="<?php print $month ?>">
			<input type="hidden" name="calYear" value="<?php print $year ?>">
			<th colspan="1">
			<input type='submit' value=">" >
			</th>
			</form>




			<?php
			//set up next year button
			?>

			<form action="<?php $PHP_SELF; ?>" method="get" id="nextY" >
			<input type="hidden" name="nextYear" value="nextYear">
			<input type="hidden" name="calYear" value="<?php print $year ?>">
			<input type="hidden" name="calMonth" value="<?php print $month ?>">
			<th colspan='1'>
			<input type="submit" value=">>" >
			</th>
			</form-->



			<?php
			//echo "</tr>";
			//echo "<tr class='calendardays'>";
				//echo "<th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th class='weekend'>Sat</th><th class='weekend'>Sun</th>";
			//echo "</tr><tr>";



			for($dayCell = $firstDayOfMonth; $dayCell > 1; $dayCell--){ // if the first day is not on a monday add a cell for each day until the month begins
				echo "<td>&nbsp;</td>";
				$noOfDays += 1 ; // add 1 to the number of cells used

			}


			for ($i = 1 ; $i <= $lastDay ; $i++){

							$noOfDays += 1 ; // add one to the count of cells for each day

							$timestamp = mktime( 0 , 0 , 0 , $month, $i, $year );

							$day = date("d", $timestamp);

							if (strftime(date("N" , $timestamp)) == 6 || strftime(date("N" , $timestamp)) == 7 ) // if it is the weekend - use weekend colours
								echo "<!--td class='calendarWeekend'><a href='calendar.php?day=$day&month=$month&year=$year'>".$i."</a></td-->";
							else // otherwise use default colours
								echo "<!--td class='calendarDates'><a href='calendar.php?day=$day&month=$month&year=$year'>".$i."</a></td-->";



							if(is_int((($noOfDays - 1)/7))){ // if the number of cells is divisible by 7 (wholly) then put on a new row.
								echo "<!--/tr><tr-->";
							}

			}



			for($dayCell = $lastDayNumber; $dayCell <7; $dayCell++){
							echo "<!--td class='display'>&nbsp;</td-->";
			}

			echo "<!--/tr-->";



echo "<!--/table-->";




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

// if there has been a date passed through to filter the information by use this, if not, display everything
if (($_GET['day'] != "") && ($_GET['year'] != "")){
	$calDate = $_GET['year']."-".$_GET['month']."-".$_GET['day']; // either select items by filter or by date from calendar
	$query = "SELECT meetingid, body, meetingdate, venue, published FROM events.meetings LEFT JOIN clacksweb2.bodies USING(bodyid), events.venues WHERE meetings.venueid=venues.venueid AND meetings.meetingdate = '$calDate'";
}
else{
	$query = "SELECT meetingid, body, meetingdate, venue, published FROM events.meetings LEFT JOIN clacksweb2.bodies USING(bodyid), events.venues WHERE meetings.venueid=venues.venueid ";
}

// Print table of records
$count_query="SELECT COUNT(DISTINCT meetingid) AS numrecs FROM $table LEFT JOIN clacksweb2.bodies USING(bodyid) WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (body LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (body LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";

$records = print_calendar_nav($table, $query, $count_query);
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
<td width="15%">Actions <a href="#" onClick="window.open('newmeeting.php?noreturn=1&year=<?php echo $year ; ?>&calDate=<?php echo $calDate; ?>', 'meeting_new', 'width=500,height=540,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add meeting" border="0"></a></td>
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
		echo '<a href="#" onClick="window.open(\'newmeeting.php?noreturn=1&action=edit&id=' . $record->$dbkey . '\', \'event_edit\', \' width=500,height=540,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit meeting" border="0"></a> ';
//		echo '<a href="#" onClick="window.open(\'meetingfiles.php?meetingid=' . $record->$dbkey . '\', \'meeting_files\', \'width=700,height=400,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/files.gif" alt="files" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'agenda.php?meetingid=' . $record->$dbkey . '\', \'agenda\', \'width=700,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/items.gif" alt="agenda" border="0"></a> ';
		echo '<a target="_blank" href="http://clacksweb/council/meetings/agenda/' . $record->$dbkey . '/?preview=1"><img src="images/preview.gif" alt="preview" border="0"></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=events.meetings&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<img src="images/delete.gif" alt="delete meeting" border="0"></a> ';
		echo '</td></tr>';
	}
}












?>


</table>
<?php
include "includes/footer.inc.php";
?>

