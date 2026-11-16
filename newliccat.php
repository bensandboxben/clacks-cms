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
<h3>Add new category</h3>
<?php
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["category"]) $errors[] = "Please enter a category";
	if (!$_POST["description"]) $errors[] = "Please enter a description";
	if (!$_POST["shortdesc"]) $errors[] = "Please enter a short description";
	if (count($errors) == 0) {
		$caturl = strtolower(preg_replace("/[^[:alnum:]]/", "", $_POST["category"]));
		$db->query("INSERT INTO liccats (category, description, shortdesc, caturl) VALUES('$_POST[category]', '$_POST[description]', '$_POST[shortdesc]', '$caturl')");
		$newid = $db->insert_id;
?>
<script language="JavaScript">
<!--
self.opener.doAddCat('<?php echo $_POST["category"]; ?>', '<?php echo $newid; ?>');
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
<form name="liccat" method="post" action="newliccat.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Category:</td>
<td><input type="text" name="category" value="<?php echo $_POST["category"]; ?>" maxlength="200" style="width: 250px;" /></td></tr>
<tr><td class="label">Short description:</td>
<td><textarea name="shortdesc" style="width: 300px; height: 40px;"><?php echo $_POST["shortdesc"]; ?></textarea></td></tr>
<tr><td class="label">Long description:</td>
<td><textarea name="description" style="width: 300px; height: 80px;"><?php echo $_POST["description"]; ?></textarea></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" />  <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onclick="self.close();" />
</td></tr>
</table>
</form>
</div>
<script>
document.liccat.category.focus();
</script>
</body>
</html>