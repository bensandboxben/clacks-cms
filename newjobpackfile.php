<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$file_array = $db->get_row("SELECT * FROM hr.files WHERE fileid='$_GET[id]'", ARRAY_A);
	extract($file_array);
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
<h3>Edit file</h3>
<?php } else { ?>
<h3>Add new file</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	$_POST["title"] = stripslashes($_POST["title"]);
	$_POST["docdesc"] = stripslashes($_POST["docdesc"]);
	if (!$_POST["typeid"]) $errors[] = "Please select the file type";
	if ((!$_POST["fileid"])&&(!$_FILES["jpfile"]["name"])) {
		$errors[] = "Please select a file to upload";
	}
	if ($_FILES["jpfile"]["name"] > "") {
		if (($_FILES["jpfile"]["type"] != 'application/pdf')&&($_FILES["jpfile"]["type"] != 'application/msword')) {
			$errors[] = "File not a Word document or pdf";
		}
	}
	if (count($errors) == 0) {
		$_POST["title"] = addslashes($_POST["title"]);
		$_POST["docdesc"] = addslashes($_POST["docdesc"]);
		if ($_FILES["jpfile"]["name"] > "") {
			if ($_FILES["jpfile"]["type"] == 'application/pdf') {
				$ext = "pdf";
			} else {
				$ext = "doc";
			}
			$filename = strtolower(strtr($_FILES['jpfile']['name'], " ?*#$%^:+=<>/\"", "______________"));
			$destination = UPLOAD_DIR . "jobpacks/" . $filename;
			if (move_uploaded_file($_FILES['jpfile']['tmp_name'], $destination)) {
				chmod($destination, 0644);
				$msg[] = "File uploaded successfully";
			} else {
				echo "eek";
				$msg[] = "Problem uploading file";
			}
		}
		if ($_POST["fileid"]) {
			if ($filename) {
				$db->query("UPDATE hr.files SET title='$_POST[title]', filename='$filename', format='$ext', docdesc='$_POST[docdesc]', typeid='$_POST[typeid]' WHERE fileid='$_POST[fileid]'");
			} else {
				$db->query("UPDATE hr.files SET title='$_POST[title]', docdesc='$_POST[docdesc]', typeid='$_POST[typeid]' WHERE fileid='$_POST[fileid]'");
			}
		} else {
			$db->query("INSERT INTO hr.files (title, filename, format, docdesc, typeid) VALUES('$_POST[title]', '$filename', '$ext', '$_POST[docdesc]', '$_POST[typeid]')");
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
		echo '<p class="form-error-intro" role="alert">The file was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
extract ($_POST);
?>
<div id="formarea">
<form name="jpfile" method="post" action="newjobpackfile.php" enctype="multipart/form-data">
<table class="table table-striped table-bordered">
<tr><td class="label">File type:</td>
<td>
<?php
$types = $db->get_results("SELECT typeid, type FROM hr.filetypes ORDER BY type", ARRAY_N);
build_select_box($types, "typeid", $typeid, "--");
?>
</td></tr>
<tr><td class="label">Title:</td>
<td><input type="text" name="title" value="<?php echo $title; ?>" maxlength="200" style="width: 300px;" /></td></tr>
<tr><td class="label">File:</td>
<td><input type="file" name="jpfile" />
<?php
if (file_exists(UPLOAD_DIR . "jobpacks/" . $filename)) {
	echo ' <span style="font-size:80%;"> <a href="' . SITE_HOST . '/jobpacks/' . $filename . '" target="_blank">View existing</a></span>';
}
?>
</td></tr>
<tr><td class="label">Description:</td>
<td><textarea name="docdesc" style="width: 300px; height: 80px;"><?php echo $docdesc; ?></textarea></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-secondary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($fileid) echo '<input type="hidden" name="fileid" value="' . $fileid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.jpfile.typeid.focus();
</script>
</body>
</html>