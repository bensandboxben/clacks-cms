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
<h3>Add new election body</h3>
<?php
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["body"]) $errors[] = "Please enter a body";
	if (count($errors) == 0) {
		$bodyurl = strtolower(preg_replace("/[^[:alnum:]]/", "", $_POST["body"]));
		$db->query("INSERT INTO elections.bodies (body, bodyurl, bodynotes) VALUES('$_POST[body]', '$bodyurl', '$_POST[bodynotes]')");
		$newid = $db->insert_id;
?>
<script language="JavaScript">
<!--
self.opener.doAddBody('<?php echo $_POST["body"]; ?>', '<?php echo $newid; ?>');
self.close();
//-->
</script>
<?php
	} else {
		echo '<p>The body was not saved. Please correct these errors and resubmit the form:</p><ul>';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
?>
<div id="formarea">
<form name="body" method="post" action="newelectionbody.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Body:</td>
<td><input type="text" name="body" value="<?php echo $_POST["body"]; ?>" maxlength="200" style="width: 250px;" /></td></tr>
<tr><td class="label">Notes:</td>
<td><textarea name="bodynotes" style="width: 250px; height: 60px;"><?php echo $bodynotes; ?></textarea></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" />  <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onclick="self.close();" />
</td></tr>
</table>
</form>
</div>
<script>
document.body.body.focus();
</script>
</body>
</html>