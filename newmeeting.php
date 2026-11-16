<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$meeting_array = $db->get_row("SELECT * FROM events.meetings WHERE meetingid='$_GET[id]'", ARRAY_A);
	extract($meeting_array);
}
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script>
  $(function() {
    $( "#datepicker" ).datepicker();
  });
  </script>

<body bgcolor="#FFFFFF" class="popup">
<script language="JavaScript">
<!--
function doAddVenue (label, value) {
var s = document.meeting.elements["venueid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>


<?php if ($edit == 1) { ?>
<h3>Edit meeting</h3>
<?php } else { ?>
<h3>Add new meeting</h3>
<?php
}
if ($_POST["save"]) {

	$_POST["meetingdate"] = date("Y-m-d", strtotime($_POST["meetingdate"]));

	$errors = array();
	$_POST["notes"] = stripslashes($_POST["notes"]);
	$_POST["cancelnotes"] = stripslashes($_POST["cancelnotes"]);

	if (($_POST["meetingdate"] == '0000-00-00')||($_POST["meetingdate"] == '1970-01-01')||($_POST["meetingdate"] == '')||(!isset($_POST["meetingdate"]))) $_POST["meetingdate"] = "0000-00-00";

	if (!$_POST["bodyid"]) $errors[] = "Please select the body which is meeting";
	if (!$_POST["venueid"]) $errors[] = "Please select a venue for this meeting";
	if (!$_POST["meetingdate"]) {
		$errors[] = "Please enter a date for this meeting";
	}

	if (count($errors) == 0) {
		$_POST["notes"] = addslashes($_POST["notes"]);
		$_POST["cancelnotes"] = addslashes($_POST["cancelnotes"]);
		if ($_POST["newclerk"]) {
			$clerk = $_POST["newclerk"];
		} else {
			$clerk = $_POST["clerk"];
		}
		if ($_POST["newminutetaker"]) {
			$minutetaker = $_POST["newminutetaker"];
		} else {
			$minutetaker = $_POST["minutetaker"];
		}

	$published = isset($_POST['published']) ? ($_POST['published'] ? 1 : 0) : 0;
	$cancelled = isset($_POST['cancelled']) ? ($_POST['cancelled'] ? 1 : 0) : 0;

		if ($_POST["meetingid"]) {
			$db->query("UPDATE events.meetings SET bodyid='$_POST[bodyid]', meetingdate='$_POST[meetingdate]', meetingtime='$_POST[meetingtime]', venueid='$_POST[venueid]', clerk='$clerk', minutetaker='$minutetaker', published='$published', videofiles='$_POST[videofiles]', additionallink='$_POST[additionallink]', cancelled='$cancelled', cancelnotes='$_POST[cancelnotes]', notes='$_POST[notes]', modified=UNIX_TIMESTAMP() WHERE meetingid='$_POST[meetingid]'");
		} else {
			$db->query("INSERT INTO events.meetings (bodyid, meetingdate, meetingtime, venueid, clerk, minutetaker, published, videofiles, additionallink, cancelled, cancelnotes, notes, modified, created) VALUES('$_POST[bodyid]', '$_POST[meetingdate]', '$_POST[meetingtime]', '$_POST[venueid]', '$clerk', '$minutetaker', '$published', '$_POST[videofiles]', '$_POST[additionallink]', '$cancelled', '$_POST[cancelnotes]', '$_POST[notes]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
		}
?>
<script language="JavaScript">
<!--
opener.location.reload(true);
self.close();
//-->
</script>
<?php
	} else {
		echo '<p>The meeting was not saved. Please correct these errors and resubmit the form:</p><ul>';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
extract ($_POST);
?>
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="calendar-win2k-cold-1.css" title="win2k-cold-1" />
<!-- main calendar program -->
<script type="text/javascript" src="js/calendar.js"></script>
<!-- language for the calendar -->
<script type="text/javascript" src="js/calendar-en.js"></script>
<!-- the following script defines the Calendar.setup helper function, which makes adding a calendar a matter of 1 or 2 lines of code. -->
<script type="text/javascript" src="js/calendar-setup.js"></script>
<div id="formarea">
<form name="meeting" method="post" action="newmeeting.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Meeting of:</td>
<td>
<?php
if ($edit == 1) {
	$bodies = $db->get_results("SELECT bodyid, body FROM clacksweb2.bodies LEFT JOIN bodytypes USING(typeid) WHERE (type='Committees' OR type='Other internal body') ORDER BY body", ARRAY_N);
} else {
	$bodies = $db->get_results("SELECT bodyid, body FROM clacksweb2.bodies LEFT JOIN bodytypes USING(typeid) WHERE (type='Committees' OR type='Other internal body') AND active=1 ORDER BY body", ARRAY_N);
}
build_select_box($bodies, "bodyid", $bodyid, '--');
?>
</td></tr>
<tr><td class="label">Venue:</td>
<td>
<?php
$venues = $db->get_results("SELECT venueid, venue FROM events.venues ORDER BY venue", ARRAY_N);
build_select_box($venues, "venueid", $venueid);
?>
<a href="#" onClick="window.open('newvenue.php', 'venue_new', 'width=500,height=350,resizable=yes,scrollbars=yes,top=50,left=50')"><img src="images/add.gif" alt="Add new venue" border="0" /></a>
</td></tr>
<tr><td class="label">Clerk:</td><td>
<?php
	$names = $db->get_results("SELECT DISTINCT clerk FROM events.meetings ORDER BY clerk");
	echo '<select name="clerk" style="width:200px;">';
	foreach ($names as $name) {
		echo '<option value="' . $name->clerk . '"';
		if ($clerk == $name->clerk) echo ' selected="selected"';
		echo '>' . $name->clerk . '</option>';
	}
	echo '</select><br />';
?>
<input type="text" name="newclerk" value="<?php echo $newclerk; ?>" maxlength="200" style="width: 170px;" /></td></tr>
<tr><td class="label">Minute taker:</td><td>
<?php
	$names = $db->get_results("SELECT DISTINCT minutetaker FROM events.meetings");
	echo '<select name="minutetaker">';
	foreach ($names as $name) {
		echo '<option value="' . $name->minutetaker . '"';
		if ($minutetaker == $name->minutetaker) echo ' selected="selected"';
		echo '>' . $name->minutetaker . '</option>';
	}
	echo '</select><br />';
?>
<input type="text" name="newminutetaker" value="<?php echo $newminutetaker; ?>" maxlength="200" style="width: 170px;" /></td></tr>


<tr>
	<td class="label">Meeting date:</td>
		<td>
			<input id="datepicker" type="text" name="meetingdate" value="<?php echo $meetingdate; ?>" maxlength="10" style="width: 100px;" />
		</td>
</tr>

<tr><td class="label">Time:</td>
<td><input type="text" name="meetingtime" value="<?php echo $meetingtime; ?>" maxlength="250" style="width: 100px;" /></td></tr>
<tr><td class="label">Publish agenda:</td>
<td>
<?php
build_yes_no_checkbox("published", $published);
?>
</td></tr>
<tr><td class="label">Cancelled:</td>
<td>
<?php
build_yes_no_checkbox("cancelled", $cancelled);
?>
</td></tr>
<tr><td class="label">Cancellation message:</td>
<td><input type="text" name="cancelnotes" value="<?php echo $cancelnotes; ?>" maxlength="200" style="width: 200px;" /></td></tr>

<tr><td class="label">Link to video files:</td>
<td><input type="text" name="videofiles" value="<?php echo $videofiles; ?>" maxlength="200" style="width: 300px;" /></td></tr>

<tr><td class="label">Attend meeting link:</td>
<td><input type="text" name="additionallink" value="<?php echo $additionallink; ?>" maxlength="300" style="width: 300px;" /></td></tr>

<tr><td class="label">Notes:</td>
<td><textarea name="notes" style="width: 300px; height: 80px;"><?php echo $notes; ?></textarea></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($meetingid) echo '<input type="hidden" name="meetingid" value="' . $meetingid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.meeting.bodyid.focus();
</script>
</body>
</html>