<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$license_array = $db->get_row("SELECT * FROM licenses WHERE licid='$_GET[licid]'", ARRAY_A);
	extract($license_array);
}
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<?php if ($edit == 1) { ?>
<h3>Edit license</h3>
<?php } else { ?>
<h3>Add new license</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["name"]) $errors[] = "Please enter a name";
	if (!$_POST["shortdesc"]) $errors[] = "Please enter a short description";
	if (!$_POST["description"]) $errors[] = "Please enter a description";
	if (count($errors) == 0) {
		$licurl = strtolower(preg_replace("/[^[:alnum:]]/", "", $_POST["name"]));

		$name = addslashes($_POST["name"]);
		$shortdesc = addslashes($_POST["shortdesc"]);
		$description = addslashes($_POST["description"]);


		if ($_POST["licid"]) {
			$db->query("UPDATE licenses SET name='$name', licurl='$licurl', shortdesc='$shortdesc', description='$description', catid='$_POST[catid]', modified=UNIX_TIMESTAMP() WHERE licid='$_POST[licid]'");
		} else {
			$db->query("INSERT INTO licenses (name, licurl, shortdesc, description, catid, created, modified) VALUES('$name', '$licurl', '$shortdesc', '$description', '$_POST[catid]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
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
		echo '<p>The license was not saved. Please correct these errors and resubmit the form:</p><ul>';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
extract ($_POST);
?>
<div id="formarea">
<script language="JavaScript">
<!--
function doAddCat (label, value) {
var s = document.license.elements["catid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>
<form name="license" method="post" action="newlicense.php">
<table class="table table-striped table-bordered">
<tr><td class="label">License name:</td>
<td><input type="text" name="name" value="<?php echo $name; ?>" maxlength="200" style="width: 200px;" /></td></tr>
<tr><td class="label">Short description:</td>
<td><textarea name="shortdesc" style="width: 300px; height: 40px;"><?php echo $shortdesc; ?></textarea></td></tr>
<tr><td class="label">Long description:</td>
<td><textarea name="description" style="width: 300px; height: 400px;"><?php echo $description; ?></textarea></td></tr>
<tr><td class="label">Category:</td>
<td>
<?php
$liccats = $db->get_results("SELECT catid, category FROM liccats ORDER BY category", ARRAY_N);
build_select_box($liccats, "catid", $catid);
?>
<a href="#" onClick="window.open('newliccat.php', 'liccat_new', 'width=500,height=300,resizable=yes,scrollbars=yes,top=50,left=50')"><img src="images/add.gif" alt="Add new category" border="0" /></a>
</td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($licid) echo '<input type="hidden" name="licid" value="' . $licid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.license.name.focus();
</script>
</body>
</html>