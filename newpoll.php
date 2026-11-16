<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$db = new db("admin", "HbCLlVH", "polls", "164.177.128.68");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$poll_array = $db->get_row("SELECT * FROM polls.polls WHERE pollid='$_GET[pollid]'", ARRAY_A);
	extract($poll_array);
}
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<?php if ($edit == 1) { ?>
<h3>Edit poll</h3>
<?php } else { ?>
<h3>Add new poll</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["title"]) $errors[] = "Please enter a title";
	if (!$_POST["question"]) $errors[] = "Please enter a question";
	if (!$_POST["catid"]) $errors[] = "Please select a category";
	if (!$_POST["startdate"]) $errors[] = "Please enter the date this poll starts";
	if (!$_POST["enddate"]) $errors[] = "Please enter the date this poll ends";
	if (($_POST["startdate"]) > ($_POST["enddate"])) $errors[] = "Start date is after end date";
	if (count($errors) == 0) {
		$_POST["title"] =  $db->escape($_POST["title"]);
		$_POST["question"] = $db->escape($_POST["question"]);
		$_POST["openquestion"] =  $db->escape($_POST["openquestion"]);
		if ($_POST["pollid"]) {
			$db->query("UPDATE polls.polls SET title='$_POST[title]', question='$_POST[question]', openquestion='$_POST[openquestion]', catid='$_POST[catid]', serviceid='$_POST[serviceid]', startdate='$_POST[startdate]', enddate='$_POST[enddate]', contactemail='$_POST[contactemail]', modified=UNIX_TIMESTAMP() WHERE pollid='$_POST[pollid]'");
		} else {
			$db->query("INSERT INTO polls.polls (title, question, openquestion, catid, serviceid, startdate, enddate, contactemail, created, modified) VALUES('$_POST[title]', '$_POST[question]', '$_POST[openquestion]', '$_POST[catid]', '$_POST[serviceid]', '$_POST[startdate]', '$_POST[enddate]', '$_POST[contactemail]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
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
		echo '<p>The poll was not saved. Please correct these errors and resubmit the form:</p><ul>';
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
<script language="JavaScript">
<!--
function doAddCat (label, value) {
var s = document.poll.elements["catid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>
<form name="poll" method="post" action="newpoll.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Title:</td>
<td><input type="text" name="title" value="<?php echo stripslashes($title); ?>" maxlength="250" style="width: 200px;" /></td></tr>
<tr><td class="label">Contact email:</td>
<td><input type="text" name="contactemail" value="<?php echo stripslashes($contactemail); ?>" maxlength="200" style="width: 200px;" /></td></tr>
<tr><td class="label">Question:</td>
<td><textarea name="question" style="width: 300px; height: 80px;"><?php echo stripslashes($question); ?></textarea></td></tr>
<tr><td class="label">Open question:</td>
<td><input type="text" name="openquestion" value="<?php echo stripslashes($openquestion); ?>" maxlength="250" style="width: 200px;" /> (optional)</td></tr>
<tr><td class="label">Category:</td>
<td>
<?php
$cats = $db->get_results("SELECT catid, category FROM clacksweb2.categories ORDER BY category", ARRAY_N);
build_select_box($cats, "catid", $catid);
?>
<a href="#" onClick="window.open('newcat.php', 'cat_new', 'width=500,height=300,resizable=yes,scrollbars=yes,top=50,left=50')"><img src="images/add.gif" alt="Add new category" border="0" /></a>
</td></tr>
<tr><td class="label">Service:</td>
<td>
<?php
if ($_SESSION["userlevel"] < 4) {
	$services = $db->get_results("SELECT serviceid, service FROM cms.services WHERE (serviceid='$_SESSION[userserviceid]') OR (parentserviceid='$_SESSION[userserviceid]') ORDER BY service", ARRAY_N);
} else {
	$services = $db->get_results("SELECT serviceid, service FROM cms.services ORDER BY service", ARRAY_N);
}
build_select_box($services, "serviceid", $serviceid);
?>
</td></tr>
<tr><td class="label">Start date:</td>
<td><input type="text" name="startdate" value="<?php echo $startdate; ?>" maxlength="10" style="width: 100px;" />
<button type="reset" id="f_trigger_b">...</button>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "startdate",      // id of the input field
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        showsTime      :    false,            // will display a time selector
        button         :    "f_trigger_b",   // trigger for the calendar (button ID)
        singleClick    :    true,           // double-click mode
        step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    });
</script>
</td></tr>
<tr><td class="label">End date:</td>
<td><input type="text" name="enddate" value="<?php echo $enddate; ?>" maxlength="10" style="width: 100px;" />
<button type="reset" id="f_trigger_a">...</button>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "enddate",      // id of the input field
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        showsTime      :    false,            // will display a time selector
        button         :    "f_trigger_a",   // trigger for the calendar (button ID)
        singleClick    :    true,           // double-click mode
        step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    });
</script>
</td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($pollid) echo '<input type="hidden" name="pollid" value="' . $pollid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.poll.title.focus();
</script>
</body>
</html>