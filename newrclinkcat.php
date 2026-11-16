<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$rclinkcat_array = $db->get_row("SELECT * FROM rclinkcats WHERE rclinkcatid='$_GET[id]'", ARRAY_A);
	extract($rclinkcat_array);
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
<h3>Edit Reference Central link category</h3>
<?php } else { ?>
<h3>Add new Reference Central link category</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["rclinkcat"]) $errors[] = "Please enter a category";
	if (count($errors) == 0) {
		$rclinkcaturl = strtolower(preg_replace("/[^[:alnum:]]/", "", strip_tags($_POST["rclinkcat"])));
		if ($_POST["rclinkcatid"]) {
			$db->query("UPDATE rclinkcats SET rclinkcat='$_POST[rclinkcat]', rclinkcaturl='$rclinkcaturl', embolden='$_POST[embolden]' WHERE rclinkcatid='$_POST[rclinkcatid]'");
		} else {
			$db->query("INSERT INTO rclinkcats (rclinkcat, rclinkcaturl, embolden) VALUES('$_POST[rclinkcat]', '$rclinkcaturl', '$_POST[embolden]')");
			$newid = $db->insert_id;
		}
		if ($_POST["noreturn"] == 1) {
?>
<script language="JavaScript">
<!--
opener.location.reload(true);
self.close();
//-->
</script>
<?php } else { ?>
<script language="JavaScript">
<!--
self.opener.doAddRCLinkCat('<?php echo $_POST["rclinkcat"]; ?>', '<?php echo $newid; ?>');
self.close();
//-->
</script>
<?php
}
	} else {
		echo '<p class="form-error-intro" role="alert">The category was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
extract ($_POST);
?>
<div id="formarea">
<form name="rclinkcat" method="post" action="newrclinkcat.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Category:</td>
<td><input type="text" name="rclinkcat" value="<?php echo $rclinkcat; ?>" maxlength="200" style="width: 250px;" /></td></tr>
<tr><td class="label">Show in bold:</td>
<td>
<?php
build_yes_no_checkbox("embolden", $embolden);
?>
</td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" />  <input type="submit" class="btn btn-md btn-secondary" name="cancel" value="Cancel" onclick="self.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($rclinkcatid) echo '<input type="hidden" name="rclinkcatid" value="' . $rclinkcatid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.rclinkcat.rclinkcat.focus();
</script>
</body>
</html>