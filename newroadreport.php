<?php

error_reporting(E_ALL);
error_reporting(-1);
ini_set('error_reporting', E_ALL);

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$roadreport_array = $db->get_row("SELECT * FROM roadreport.info WHERE id='$_GET[id]'", ARRAY_A);
	extract($roadreport_array);
}
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script>
  $(function() {
    $( "#datepicker" ).datepicker();
    $( "#datepicker1" ).datepicker();
  });
  </script>
</head>

<body bgcolor="#fff" class="popup">
<?php if ($edit == 1) { ?>
<h3>Edit road report</h3>
<?php } else { ?>
<h3>Add new road report</h3>
<?php
}
if ($_POST["save"]) {


	$_POST["stdate"] = date("Y-m-d", strtotime($_POST["stdate"]));
	$_POST["endate"] = date("Y-m-d", strtotime($_POST["endate"]));

	$errors = array();
	if (!$_POST["road"]) $errors[] = "Please enter a road";
	if (!$_POST["town"]) $errors[] = "Please enter a town";
	if (!$_POST["stdate"]) $errors[] = "Please enter a start date";
	if (!$_POST["endate"]) $errors[] = "Please enter an end date";

	if (($_POST["stdate"] == '0000-00-00')||($_POST["stdate"] == '1970-01-01')||($_POST["stdate"] == '')||(!isset($_POST["stdate"]))) $_POST["stdate"] = "0000-00-00";
	if (($_POST["endate"] == '0000-00-00')||($_POST["endate"] == '1970-01-01')||($_POST["endate"] == '')||(!isset($_POST["endate"]))) $_POST["endate"] = "0000-00-00";


	if (($_POST["stdate"])&&($_POST["endate"])&&($_POST["stdate"] > $_POST["endate"])) $errors[] = "End date is before start date";
	if (!$_POST["information"]) $errors[] = "Please enter some information";

		$road = addslashes($_POST["road"]);
		$town = addslashes($_POST["town"]);
		$information = addslashes($_POST["information"]);

	if (count($errors) == 0) {
		if ($_POST["id"]) {
			$db->query("UPDATE roadreport.info SET road='$road', stdate='$_POST[stdate]', endate='$_POST[endate]', town='$town', information='$information', rclass='$_POST[rclass]' WHERE id='$_POST[id]'");
		} else {
			$db->query("INSERT INTO roadreport.info (road, stdate, endate, town, information, rclass) VALUES('$road', '$_POST[stdate]', '$_POST[endate]', '$town', '$information', '$_POST[rclass]')");

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
		echo '<p>The road report was not saved. Please correct these errors and resubmit the form:</p><ul>';
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
<form name="roadreport" method="post" action="newroadreport.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Road:</td>
<td><input type="text" name="road" value="<?php echo $road; ?>" maxlength="40" style="width: 250px;" /></td></tr>
<tr><td class="label">Town:</td>
<td><input type="text" name="town" value="<?php echo $town; ?>" maxlength="40" style="width: 250px;" /></td></tr>
<tr><td class="label">Information:</td>
<td><input type="text" name="information" value="<?php echo htmlentities($information); ?>" maxlength="250" style="width: 350px;" /></td></tr>
<tr><td class="label">Rclass:</td>
<td><input type="text" name="rclass" value="<?php echo $rclass; ?>" maxlength="1" style="width: 30px;" /></td></tr>


<tr>
	<td class="label">Start Date:</td>
		<td>
			<input id="datepicker" type="text" name="stdate" value="<?php echo $stdate; ?>" maxlength="10" style="width: 100px;" />
		</td>
</tr>
<tr>
	<td class="label">End Date:</td>
		<td>
			<input id="datepicker1" type="text" name="endate" value="<?php echo $endate; ?>" maxlength="10" style="width: 100px;" />
		</td>
</tr>

<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($id) echo '<input type="hidden" name="id" value="' . $id . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.roadreport.road.focus();
</script>
</body>
</html>