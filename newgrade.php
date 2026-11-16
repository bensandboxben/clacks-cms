<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$grade_array = $db->get_row("SELECT * FROM hr.grades WHERE gradeid='$_GET[gradeid]'", ARRAY_A);
	extract($grade_array);
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
<?php if ($edit == 1) { ?>
<h3>Edit grade</h3>
<?php } else { ?>
<h3>Add new gradee</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["grade"]) {
		$errors[] = "Please enter a grade";
	} else {
		if (($found = $db->get_var("SELECT gradeid FROM hr.grades WHERE grade='$_POST[grade]'")) &&(!$_POST["gradeid"])) {
			$errors[] = "That grade already exists";
		}
	}
	if ((!$_POST["minsal"])&&(!$_POST["maxsal"])&&(!$_POST["hourly"])) {
		$errors[] = "Please enter a minimum and maximum or an hourly rate";
	} else {
		if (($_POST["minsal"]) && (!is_numeric($_POST["minsal"]))) $errors[] = "Minimum salary must be numeric";
		if (($_POST["maxsal"]) && (!is_numeric($_POST["maxsal"]))) $errors[] = "Maximum salary must be numeric";
		if (($_POST["hourly"]) && (!is_numeric($_POST["hourly"]))) $errors[] = "Hourly rate must be numeric";
	}
	if (count($errors) == 0) {
		if ($_POST["gradeid"]) {
			$db->query("UPDATE hr.grades SET grade='$_POST[grade]', minsal='$_POST[minsal]', maxsal='$_POST[maxsal]', hourly='$_POST[hourly]' WHERE gradeid='$_POST[gradeid]'");
		} else {
			$db->query("INSERT INTO hr.grades (grade, minsal, maxsal, hourly) VALUES('$_POST[grade]', '$_POST[minsal]', '$_POST[maxsal]', '$_POST[hourly]')");
			$newgradeid = $db->insert_id;
		}
		if ($_POST["gradeid"]) {
?>
<script language="JavaScript">
<!--
self.opener.updateGrade('<?php echo $_POST["index"]; ?>', '<?php echo $_POST["grade"] . " - £" . $_POST["minsal"] . " - £" . $_POST["maxsal"] . " - " . $_POST["hourly"]; ?>');
self.close();
//-->
</script>
<?php } else { ?>
<script language="JavaScript">
<!--
self.opener.doAddGrade('<?php echo $_POST["grade"]; ?>', '<?php echo $newgradeid; ?>');
self.close();
//-->
</script>
<?php
	}
		} else {
			echo '<p class="form-error-intro" role="alert">The gradee was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
			foreach ($errors as $error) {
				echo '<li>' . $error . '</li>';
			}
			echo '</ul>';
		}
	}
extract ($_POST);
?>
<div id="formarea">
<form name="frmgrade" method="post" action="newgrade.php">
<table class="table table-striped table-bordered">
<tr><td class="label"><span class="required">*</span> Grade:</td>
<td><input type="text" name="grade" value="<?php echo $grade; ?>" maxlength="20" style="width: 150px;" /></td></tr>
<tr><td class="label">Min £:</td>
<td><input type="text" name="minsal" value="<?php echo $minsal; ?>" maxlength="20" style="width: 150px;" /></td></tr>
<tr><td class="label">Max £:</td>
<td><input type="text" name="maxsal" value="<?php echo $maxsal; ?>" maxlength="20" style="width: 150px;" /></td></tr>
<tr><td class="label">Hourly £:</td>
<td><input type="text" name="hourly" value="<?php echo $hourly; ?>" maxlength="20" style="width: 150px;" /></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-secondary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($gradeid) echo '<input type="hidden" name="gradeid" value="' . $gradeid . '" />'; ?>
<?php if ($_REQUEST["index"]) echo '<input type="hidden" name="index" value="' . $_REQUEST["index"] . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.frmgrade.grade.focus();
</script>
</body>
</html>