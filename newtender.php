<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$tender_array = $db->get_row("SELECT * FROM tenders WHERE tenderid='$_GET[id]'", ARRAY_A);
	extract($tender_array);
	$endhour = substr($enddate, 11, 2);
	$endmin = substr($enddate, 14, 2);
	$enddate = substr($enddate, 0, 10);
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include "includes/head.inc.php"; ?>
</head>
<body class="popup">
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="calendar-win2k-cold-1.css" title="win2k-cold-1" />
<!-- main calendar program -->
<script type="text/javascript" src="js/calendar.js"></script>
<!-- language for the calendar -->
<script type="text/javascript" src="js/calendar-en.js"></script>
<!-- the following script defines the Calendar.setup helper function, which makes adding a calendar a matter of 1 or 2 lines of code. -->
<script type="text/javascript" src="js/calendar-setup.js"></script>
<?php if ($edit == 1) { ?>
<h3>Edit tender</h3>
<?php } else { ?>
<h3>Add new tender</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["tender"]) $errors[] = "Please enter the name of this tender";
	if (!$_POST["reference"]) $errors[] = "Please enter a reference for this tender";
	if (!$_POST["description"]) $errors[] = "Please enter a brief description for this tender";
	if (!$_POST["startdate"]) $errors[] = "Please enter a start date for this tender";
	if (!$_POST["enddate"]) $errors[] = "Please enter an end date for this tender";
	if (($_POST["endhour"] > 23)||(!is_numeric($_POST["endhour"]))||($_POST["endmin"] > 59)||(!is_numeric($_POST["endmin"]))) $errors[] = "Please enter a valid end time for this tender";
	if (count($errors) == 0) {
		if (!$_POST["endhour"]) $_POST["endhour"] = "12";
		if (!$_POST["endmin"]) $_POST["endmin"] = "00";
		$enddatetime = $_POST["enddate"] . " " . $_POST["endhour"] . ":" . $_POST["endmin"] . ":00";
		if ($_POST["tenderid"]) {
			$db->query("UPDATE tenders SET tender='$_POST[tender]', reference='$_POST[reference]', value='$_POST[value]', description='$_POST[description]', startdate='$_POST[startdate]', enddate='$enddatetime', supplier='$_POST[supplier]', modified=UNIX_TIMESTAMP() WHERE tenderid='$_POST[tenderid]'");
		} else {
			$db->query("INSERT INTO tenders (tender, serviceid, reference, value, description, startdate, enddate, supplier, created, modified) VALUES('$_POST[tender]', 27, '$_POST[reference]', '$_POST[value]', '$_POST[description]', '$_POST[startdate]', '$enddatetime', '$_POST[supplier]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			$newtenderid = $db->insert_id;
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
		echo '<p class="form-error-intro" role="alert">The tender was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
extract ($_POST);
?>
<script language="JavaScript">
<!--
function valid(form) {
  var field = form.value;
  var tenderValue = parseInt(field.value);
  if (tenderValue >= 200000) {
      alert("Please note this tender requires a performance bond.");
  }
  return true;
}
// -->
</script>
<div id="formarea">
<form name="tender" method="post" action="newtender.php" onSubmit="return valid(this)">
<table class="table table-striped table-bordered">
<tr><td class="label">Tender name:</td>
<td><input type="text" name="tender" value="<?php echo $tender; ?>" maxlength="200" style="width: 300px;" /></td></tr>
<tr><td class="label">Reference:</td>
<td><input type="text" name="reference" value="<?php echo $reference; ?>" maxlength="100" style="width: 250px;" /></td></tr>
<tr><td class="label">Value (whole £):</td>
<td><input type="text" name="value" value="<?php echo $value; ?>" maxlength="8" style="width: 100px;" /></td></tr>
<tr><td class="label">Description:</td>
<td><textarea name="description" style="width: 300px; height: 80px;"><?php echo $description; ?></textarea></td></tr>
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
<tr><td class="label">End time (HH:MM):</td>
<td><input type="text" name="endhour" value="<?php echo $endhour; ?>" maxlength="2" style="width: 30px;" />:<input type="text" name="endmin" value="<?php echo $endmin; ?>" maxlength="2" style="width: 30px;" /></td></tr>
<tr><td class="label">Successful Supplier:</td>
<td><input type="text" name="supplier" value="<?php echo $supplier; ?>" maxlength="250" style="width: 250px;" /></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="button" value="Cancel" onclick="javascript: window.close();" />
<?php if ($tenderid) echo '<input type="hidden" name="tenderid" value="' . $tenderid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.tender.tender.focus();
</script>
</body>
</html>