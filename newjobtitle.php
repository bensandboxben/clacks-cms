<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$jobtitle_array = $db->get_row("SELECT * FROM hr.jobtitles WHERE titleid='$_GET[titleid]'", ARRAY_A);
	extract($jobtitle_array);
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
<h3>Edit job title</h3>
<?php } else { ?>
<h3>Add new job title</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["jobtitle"]) {
		$errors[] = "Please enter a job title";
	} else {
		if (($found = $db->get_var("SELECT titleid FROM hr.jobtitles WHERE jobtitle='$_POST[jobtitle]'"))&!($edit)) {
			$errors[] = "That job title already exists";
		}
	}
	if (count($errors) == 0) {
		if ($_POST["titleid"]) {
			$db->query("UPDATE hr.jobtitles SET jobtitle='$_POST[jobtitle]' WHERE titleid='$_POST[titleid]'");
		} else {
			$db->query("INSERT INTO hr.jobtitles (jobtitle) VALUES('$_POST[jobtitle]')");
			$newtitletid = $db->insert_id;
		}
		if ($_POST["titleid"]) {
?>
<script language="JavaScript">
<!--
self.opener.updateJobTitle('<?php echo $_POST["index"]; ?>', '<?php echo $_POST["jobtitle"]; ?>');
self.close();
//-->
</script>
<?php } else { ?>
<script language="JavaScript">
<!--
self.opener.doAddJobTitle('<?php echo $_POST["jobtitle"]; ?>', '<?php echo $newtitletid; ?>');
self.close();
//-->
</script>
<?php
	}
		} else {
			echo '<p>The job title was not saved. Please correct these errors and resubmit the form:</p><ul>';
			foreach ($errors as $error) {
				echo '<li>' . $error . '</li>';
			}
			echo '</ul>';
		}
	}
extract ($_POST);
?>
<div id="formarea">
<form name="frmjobtitle" method="post" action="newjobtitle.php">
<table class="table table-striped table-bordered">
<tr><td class="label"><span class="required">*</span> Job title:</td>
<td><input type="text" name="jobtitle" value="<?php echo $jobtitle; ?>" maxlength="100" style="width: 350px;" /></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($titleid) echo '<input type="hidden" name="titleid" value="' . $titleid . '" />'; ?>
<?php if ($_REQUEST["index"]) echo '<input type="hidden" name="index" value="' . $_REQUEST["index"] . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.frmjobtitle.jobtitle.focus();
</script>
</body>
</html>