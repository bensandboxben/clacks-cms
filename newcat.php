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
	if (count($errors) == 0) {
		$caturl = strtolower(preg_replace("/[^[:alnum:]]/", "", $_POST["category"]));
		$db->query("INSERT INTO categories (category, caturl) VALUES('$_POST[category]', '$caturl')");
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
<form name="cat" method="post" action="newcat.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Category:</td>
<td><input type="text" name="category" value="<?php echo $_POST["category"]; ?>" maxlength="200" style="width: 250px;" /></td></tr>
<tr><td colspan="2" class="formbtns">
<input class="btn btn-md btn-primary" type="submit" name="save" value="Save" />  <input class="btn btn-md btn-primary" type="submit" name="cancel" value="Cancel" onclick="self.close();" />
</td></tr>
</table>
</form>
</div>
<script>
document.cat.category.focus();
</script>
</body>
</html>