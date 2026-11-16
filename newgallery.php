<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: nocache");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$gallery_array = $db->get_row("SELECT * FROM galleries.galleries WHERE galleryid='$_GET[id]'", ARRAY_A);
	extract($gallery_array);
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
<h3>Edit gallery</h3>
<?php } else { ?>
<h3>Add new gallery</h3>
<?php
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["name"]) {
		$errors[] = "Please enter a name for this gallery";
	} else {
		if ((!$_POST["galleryid"])&&($exists = $db->get_var("SELECT name FROM galleries.galleries WHERE name='$_POST[name]'"))) {
			$errors[] = "A gallery with that name already exists";
		}
	}
	if (!$_POST["description"]) {
		$errors[] = "Please enter a description for this gallery";
	}
	if (count($errors) == 0) {
		$_POST["name"] =  $db->escape($_POST["name"]);
		$_POST["description"] = $db->escape($_POST["description"]);
		$galleryurl = strtolower(preg_replace("/[^[:alnum:]]/", "", $_POST["name"]));
		if ($_POST["galleryid"]) {
			$db->query("UPDATE galleries.galleries SET name='$_POST[name]', galleryurl='$galleryurl', description='$_POST[description]', serviceid='$_POST[serviceid]', live='$_POST[live]' WHERE galleryid='$_POST[galleryid]'");
		} else {
			$db->query("INSERT INTO galleries.galleries (name, galleryurl, description, serviceid, live, created, modified) VALUES('$_POST[name]', '$galleryurl', '$_POST[description]', '$_POST[serviceid]', '$_POST[live]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
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
		echo '<p class="form-error-intro" role="alert">The gallery was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
}
extract ($_POST);
?>
<div id="formarea">
<form name="gallery" method="post" action="newgallery.php">
<table class="table table-striped table-bordered">
<tr><td class="label"><span class="required">*</span> Gallery name:</td><td><input type="text" name="name" value="<?php echo $name; ?>" maxlength="150" style="width: 250px;" /></td></tr>
<tr><td class="label"><span class="required">*</span> Description:</td><td><input type="text" name="description" value="<?php echo $description; ?>" maxlength="250" style="width: 300px;" /></td></tr>
<tr><td class="label">Service:</td>
<td>
<?php
if ($_SESSION["userlevel"] < 4) {
	$services = $db->get_results("SELECT serviceid, service FROM cms.services WHERE (serviceid='$_SESSION[userserviceid]') OR (parentserviceid='$_SESSION[userserviceid]') ORDER BY service", ARRAY_N);
} else {
	$services = $db->get_results("SELECT serviceid, service FROM cms.services ORDER BY service", ARRAY_N);
}
build_select_box($services, "serviceid", $serviceid);
?>
</td></tr>
<tr><td class="label">Live:</td>
<td>
<?php build_yes_no_checkbox("live", $live); ?>
</td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" />  <input type="submit" class="btn btn-md btn-secondary" name="cancel" value="Cancel" onclick="self.close();" />
<?php if ($galleryid) echo '<input type="hidden" name="galleryid" value="' . $galleryid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.gallery.name.focus();
</script>
</body>
</html>