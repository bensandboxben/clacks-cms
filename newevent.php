<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$event_array = $db->get_row("SELECT * FROM events.events WHERE eventid='$_GET[id]'", ARRAY_A);
	extract($event_array);
	if ($eventcatids = $db->get_results("SELECT eventcatid FROM events.eventXeventcat WHERE eventid='$_GET[id]'")) {
		$count = 1;
		foreach($eventcatids as $eventcatid) {
			$varname = 'eventcatid' . $count;
			$$varname = $eventcatid->eventcatid;
			$count++;
		}
	}
} else if ($_GET["action"] == "copy") {
	$event_array = $db->get_row("SELECT * FROM events.events WHERE eventid='$_GET[id]'", ARRAY_A);
	extract($event_array);
	unset($eventid);
	if ($eventcatids = $db->get_results("SELECT eventcatid FROM events.eventXeventcat WHERE eventid='$_GET[id]'")) {
		$count = 1;
		foreach($eventcatids as $eventcatid) {
			$varname = 'eventcatid' . $count;
			$$varname = $eventcatid->eventcatid;
			$count++;
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script>
  $(function() {
    $( "#datepicker" ).datepicker();
    $( "#datepicker1" ).datepicker();
  });
  </script>
<?php include "includes/head.inc.php"; ?>
</head>
<body class="popup">
<script language="JavaScript">
<!--
function doAddVenue (label, value) {
var s = document.event.elements["venueid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>
<script type="text/javascript">
//<![CDATA[
	function myOnSubmitEventHandler() {
		try {
			if(typeof(document.getElementById('editor1').EscapeUnicode) == 'undefined') {
				throw "Error"
			} else {
				document.getElementById('editor1').EscapeUnicode = true;
				document.getElementById('description').value = document.getElementById('editor1').value;
			}
		}
		catch(er) {
			document.getElementById('description').value = document.getElementById('alternate1').value;
		}
	}
//]]>
</script>
<?php if ($edit == 1) { ?>
<h3>Edit event</h3>
<?php } else { ?>
<h3>Add new event</h3>
<?php
}
if ($_POST["save"]) {

	$_POST["startdate"] = date("Y-m-d", strtotime($_POST["startdate"]));
	$_POST["enddate"] = date("Y-m-d", strtotime($_POST["enddate"]));

	$errors = array();
	$_POST["event"] = stripslashes($_POST["event"]);
	$_POST["description"] = stripslashes($_POST["description"]);
	$_POST["contact"] = stripslashes($_POST["contact"]);
	$_POST["keywords"] = stripslashes($_POST["keywords"]);
	$_POST["keywords"] = str_replace("'","\'",$_POST["keywords"]);

	if (!$_POST["event"]) $errors[] = "Please enter the name of this event";
	if (!$_POST["description"]) $errors[] = "Please enter a brief description for this event";
	if ((!$_POST["eventcatid1"])&&(!$_POST["eventcatid1"])) $errors[] = "Please select at least one category for this event";
	if (!$_POST["startdate"]) {
		$errors[] = "Please enter a start date for this event";
	} else if (($_POST["startdate"])&&($_POST["enddate"])&&($_POST["startdate"] > $_POST["enddate"])) {
		$errors[] = "End date is before start date";
	}
//	if (!$_POST["contact"]) $errors[] = "Please enter contact details for this event";
	if (count($errors) == 0) {
		if (!$_POST["enddate"]) $_POST["enddate"] = $_POST["startdate"];
		$_POST["event"] = addslashes($_POST["event"]);
		$_POST["description"] = addslashes($_POST["description"]);
		$_POST["contact"] = addslashes($_POST["contact"]);
		if ($_POST["eventid"]) {
			$db->query("DELETE FROM events.eventXeventcat WHERE eventid='$_POST[eventid]'");
			$db->query("UPDATE events.events SET event='$_POST[event]', serviceid='$_POST[serviceid]', description='$_POST[description]', keywords='$_POST[keywords]', startdate='$_POST[startdate]', enddate='$_POST[enddate]', eventtime='$_POST[eventtime]', venueid='$_POST[venueid]', charge='$_POST[charge]', contact='$_POST[contact]', url='$_POST[url]', modified=UNIX_TIMESTAMP() WHERE eventid='$_POST[eventid]'");
			if ($_POST["eventcatid1"]) {
				$db->query("INSERT INTO events.eventXeventcat (eventid, eventcatid) VALUES('$_POST[eventid]', '$_POST[eventcatid1]')");
			}
			if ($_POST["eventcatid2"]) {
				$db->query("INSERT INTO events.eventXeventcat (eventid, eventcatid) VALUES('$_POST[eventid]', '$_POST[eventcatid2]')");
			}
		} else {
			$db->query("INSERT INTO events.events (event, serviceid, description, keywords, startdate, enddate, eventtime, venueid, charge, contact, url, modified, created) VALUES('$_POST[event]', '$_POST[serviceid]', '$_POST[description]', '$_POST[keywords]', '$_POST[startdate]', '$_POST[enddate]', '$_POST[eventtime]', '$_POST[venueid]', '$_POST[charge]', '$_POST[contact]', '$_POST[url]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			$neweventid = $db->insert_id;
			$db->query("DELETE FROM events.eventXeventcat WHERE eventid='$neweventid'");
			if ($_POST["eventcatid1"]) {
				$db->query("INSERT INTO events.eventXeventcat (eventid, eventcatid) VALUES('$neweventid', '$_POST[eventcatid1]')");
			}
			if ($_POST["eventcatid2"]) {
				$db->query("INSERT INTO events.eventXeventcat (eventid, eventcatid) VALUES('$neweventid', '$_POST[eventcatid2]')");
			}
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
		echo '<p class="form-error-intro" role="alert">The event was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
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
<form name="event" method="post" action="newevent.php" onsubmit="myOnSubmitEventHandler()">
<script language="JavaScript">
<!--
function doAddEventCat (label, value) {
var s = document.event.elements["eventcatid1"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
var s = document.event.elements["eventcatid2"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
}
//-->
</script>




 <script src="ckeditor/ckeditor.js"></script>

<script type="text/javascript">
//<![CDATA[
	function myOnSubmitEventHandler() {
	var content = CKEDITOR.instances.editor1.getData();

		try {

			document.getElementById('description').value = content;

		}
		catch(er) {
			document.getElementById('description').value = content;

		}
	}
//]]>
</script>


<table class="table table-striped table-bordered" width="100%">
<tr><td class="label">Event:</td>
<td><input type="text" name="event" value="<?php echo $event; ?>" maxlength="250" style="width: 300px;" /></td></tr>
<tr><td colspan="2" class="label">Description:</td></tr>
	<tr>
		<td colspan="2">

			<textarea name="editor1" id="editor1" cols="80" rows="15">
				<?php echo htmlspecialchars($description, ENT_COMPAT) ?>
			</textarea>

            <script>
                // Replace the <textarea id="editor1"> with a CKEditor
                // instance, using default configuration.
                CKEDITOR.replace( 'editor1' );
            </script>
			<input type="hidden" name="description" id="description" value="" />
		</td>
	</tr>
<tr><td class="label">Keywords:</td>
<td><input type="text" name="keywords" value="<?php echo $keywords; ?>" maxlength="250" style="width: 300px;" /></td></tr>
<tr><td class="label"><a href="#" onClick="window.open('neweventcat.php', 'eventcat_new', 'width=400,height=200,resizable=yes,scrollbars=yes,top=50,left=50')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new category</span></a> Categories:</td>
<td>
<?php
$categories = $db->get_results("SELECT eventcatid, eventcategory FROM events.eventcats ORDER BY eventcategory", ARRAY_N);
build_select_box($categories, "eventcatid1", $eventcatid1, '--');
echo '<br />';
build_select_box($categories, "eventcatid2", $eventcatid2, '--');
?>
</td></tr>
<tr><td class="label">Service:</td>
<td>
<?php
if ($_SESSION["userlevel"] < 4) {
	$services = $db->get_results("SELECT serviceid, service FROM cms.services WHERE serviceid='$_SESSION[userserviceid]' ORDER BY service", ARRAY_N);
} else {
	$services = $db->get_results("SELECT serviceid, service FROM cms.services ORDER BY service", ARRAY_N);
}
build_select_box($services, "serviceid", $serviceid);
?>
</td></tr>
<tr><td class="label">Venue:</td>
<td>
<?php
$venues = $db->get_results("SELECT venueid, venue FROM events.venues ORDER BY venue", ARRAY_N);
build_select_box($venues, "venueid", $venueid);
?>
<a href="#" onClick="window.open('newvenue.php', 'venue_new', 'width=500,height=480,resizable=yes,scrollbars=yes,top=50,left=50')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new venue</span></a>
</td></tr>
<tr>
	<td class="label">Dates:</td>
	<td>
		<input id="datepicker" type="text" name="startdate" value="<?php echo $startdate; ?>" maxlength="10" style="width: 100px;" />
		<input id="datepicker1" type="text" name="enddate" value="<?php echo $enddate; ?>" maxlength="10" style="width: 100px;" />
	</td>
</tr>
<tr><td class="label">Time:</td>
<td><input type="text" name="eventtime" value="<?php echo $eventtime; ?>" maxlength="250" style="width: 100px;" /> <span class="label">Charge: </span><input type="text" name="charge" value="<?php echo $charge; ?>" maxlength="250" style="width: 100px;" /></td></tr>
<tr><td class="label">Contact details:</td>
<td><textarea name="contact" style="width: 300px; height: 60px;"><?php echo $contact; ?></textarea></td></tr>
<tr><td class="label">Info URL:</td>
<td><input type="text" name="url" value="<?php echo $url; ?>" maxlength="250" style="width: 300px;" /></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-secondary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($eventid) echo '<input type="hidden" name="eventid" value="' . $eventid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.event.event.focus();
</script>
</body>
</html>