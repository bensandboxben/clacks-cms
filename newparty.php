<?php

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include "includes/head.inc.php"; ?>
</head>
<body class="popup">
<h3>Add new party</h3>
<?php
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["party"]) $errors[] = "Please enter a party";
	if (count($errors) == 0) {
		$db->query("INSERT INTO elections.parties (party, url, colour) VALUES('$_POST[party]', '$_POST[url]', '$_POST[colour]')");
		$newid = $db->insert_id;
?>
<script language="JavaScript">
<!--
self.opener.doAddParty('<?php echo $_POST["party"]; ?>', '<?php echo $newid; ?>');
self.close();
//-->
</script>
<?php
	} else {
		echo '<p class="form-error-intro" role="alert">The party was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
?>
<div id="formarea">
<form name="party" method="post" action="newparty.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Party:</td>
<td><input type="text" name="party" value="<?php echo $_POST["party"]; ?>" maxlength="200" style="width: 250px;" /></td></tr>
<tr><td class="label">URL:</td>
<td><input type="text" name="url" value="<?php echo $_POST["url"]; ?>" maxlength="200" style="width: 250px;" /></td></tr>
<tr><td class="label">Colour:</td>
<td><input type="text" name="colour" value="<?php echo $_POST["colour"]; ?>" maxlength="6" style="width: 50px;" /></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" />  <input type="submit" class="btn btn-md btn-secondary" name="cancel" value="Cancel" onclick="self.close();" />
</td></tr>
</table>
</form>
</div>
<script>
document.party.party.focus();
</script>
</body>
</html>