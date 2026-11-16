<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$cat_array = $db->get_row("SELECT * FROM hicategories WHERE catid='$_GET[id]'", ARRAY_A);
	extract($cat_array);
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
<h3>Edit inspection category</h3>
<?php } else { ?>
<h3>Add new inspection category</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["category"]) $errors[] = "Please enter a category";
	if (count($errors) == 0) {
		$caturl = strtolower(preg_replace("/[^[:alnum:]]/", "", $_POST["category"]));
		if ($_POST["catid"]) {
			$db->query("UPDATE hicategories SET category='$_POST[category]', caturl= '$caturl' WHERE catid='$_POST[catid]'");
		} else {
			$db->query("INSERT INTO hicategories (category, caturl) VALUES('$_POST[category]', '$caturl')");
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
self.opener.doAddCat('<?php echo $_POST["category"]; ?>', '<?php echo $newid; ?>');
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
?>
<div id="formarea">
<form name="cat" method="post" action="newhicat.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Category:</td>
<td><input type="text" name="category" value="<?php echo $category; ?>" maxlength="200" style="width: 250px;" /></td></tr>
<tr><td colspan="2" class="formbtns">
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($catid) echo '<input type="hidden" name="catid" value="' . $catid . '" />'; ?>
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" />  <input type="submit" class="btn btn-md btn-secondary" name="cancel" value="Cancel" onclick="self.close();" />
</td></tr>
</table>
</form>
</div>
<script>
document.cat.category.focus();
</script>
</body>
</html>