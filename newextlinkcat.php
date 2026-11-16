<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<h3>Add new external link category</h3>
<?php
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["extlinkcat"]) $errors[] = "Please enter a category";
	if (count($errors) == 0) {
		$extlinkcaturl = strtolower(preg_replace("/[^[:alnum:]]/", "", $_POST["extlinkcat"]));
		$db->query("INSERT INTO extlinkcats (extlinkcat, extlinkcaturl) VALUES('$_POST[extlinkcat]', '$extlinkcaturl')");
		$newid = $db->insert_id;
?>
<script language="JavaScript">
<!--
self.opener.doAddExtLinkCat('<?php echo $_POST["extlinkcat"]; ?>', '<?php echo $newid; ?>');
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
<form name="extlinkcat" method="post" action="newextlinkcat.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Category:</td>
<td><input type="text" name="extlinkcat" value="<?php echo $_POST["extlinkcat"]; ?>" maxlength="200" style="width: 250px;" /></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" />  <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onclick="self.close();" />
</td></tr>
</table>
</form>
</div>
<script>
document.extlinkcat.extlinkcat.focus();
</script>
</body>
</html>