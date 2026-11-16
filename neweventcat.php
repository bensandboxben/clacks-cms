<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
?>
<head>
<title>Schools CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<h3>Add new event category</h3>
<?php
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["eventcat"]) $errors[] = "Please enter a category";
	if (count($errors) == 0) {
		$db->query("INSERT INTO events.eventcats (eventcategory) VALUES('$_POST[eventcat]')");
		$newid = $db->insert_id;
?>
<script language="JavaScript">
<!--
self.opener.doAddEventCat('<?php echo $_POST["eventcat"]; ?>', '<?php echo $newid; ?>');
self.close();
//-->
</script>
<?php
	} else {
		echo '<p>The category was not saved. Please correct these errors and resubmit the form:</p><ul>';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
?>
<div id="formarea">
<form name="eventcat" method="post" action="neweventcat.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Category:</td>
<td><input type="text" name="eventcat" value="<?php echo $_POST["eventcat"]; ?>" maxlength="200" style="width: 250px;" /></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" />  <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onclick="self.close();" />
</td></tr>
</table>
</form>
</div>
<script>
document.eventcat.eventcat.focus();
</script>
</body>
</html>