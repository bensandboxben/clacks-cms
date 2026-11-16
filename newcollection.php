<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$collection_array = $db->get_row("SELECT * FROM collections WHERE ccid='$_GET[id]'", ARRAY_A);
	extract($collection_array);
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
<?php if ($edit == 1) { ?>
<h3>Edit collection</h3>
<?php } else { ?>
<h3>Add new collection</h3>
<?php
}
if ($_POST["save"]) {


	$_POST["ccdate"] = date("Y-m-d", strtotime($_POST["ccdate"]));

	$errors = array();
	$_POST["organisation"] = stripslashes($_POST["organisation"]);
	if (!$_POST["organisation"]) $errors[] = "Please enter the name of the organisation";

	if (($_POST["ccdate"] == '0000-00-00')||($_POST["ccdate"] == '1970-01-01')||($_POST["ccdate"] == '')||(!isset($_POST["ccdate"]))) $_POST["ccdate"] = "0000-00-00";

	if (!$_POST["ccdate"]) {
		$errors[] = "Please enter a date for this collection";
	} else {
		if ($existing = $db->get_var("SELECT organisation FROM collections WHERE ccdate='$_POST[ccdate]'")) {
			$errors[] = "A collection for <strong>$existing</strong> is already booked on this date";
		}
	}
	if (count($errors) == 0) {
		$_POST["organisation"] = addslashes($_POST["organisation"]);
		if ($_POST["ccid"]) {
			$db->query("UPDATE collections SET organisation='$_POST[organisation]', ccdate='$_POST[ccdate]', modified=UNIX_TIMESTAMP() WHERE ccid='$_POST[ccid]'");
		} else {
			$db->query("INSERT INTO collections (organisation, ccdate, serviceid, modified, created) VALUES('$_POST[organisation]', '$_POST[ccdate]', 3, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
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
		echo '<p>The collection was not saved. Please correct these errors and resubmit the form:</p><ul>';
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
<form name="collection" method="post" action="newcollection.php">
<table width="100%">
<tr><td class="label">Organisation:</td>
<td><input type="text" name="organisation" value="<?php echo $organisation; ?>" maxlength="250" style="width: 300px;" /></td></tr>

<tr>
	<td class="label">Date:</td>
		<td>
			<input id="datepicker" type="text" name="ccdate" value="<?php echo $ccdate; ?>" maxlength="10" style="width: 100px;" />
		</td>
</tr>




<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($ccid) echo '<input type="hidden" name="ccid" value="' . $ccid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.collection.organisation.focus();
</script>
</body>
</html>