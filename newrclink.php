<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$rclink_array = $db->get_row("SELECT * FROM rclinks WHERE rclinkid='$_GET[id]'", ARRAY_A);
	extract($rclink_array);
	$curr_rclinkcats = $db->get_col("SELECT rclinkcatid FROM rclinkXrclinkcat WHERE rclinkid = '$_GET[id]'");
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
<h3>Edit Reference Central link</h3>
<?php } else { ?>
<h3>Add Reference Central link</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["url"]) $errors[] = "Please enter a URL";
	if (!$_POST["title"]) $errors[] = "Please enter a title";
	if (count($_POST["rclinkcats"]) == 0) $errors[] = "Please select at least one category";
	if (count($errors) == 0) {
		if ($_POST["rclinkid"]) {
			$db->query("UPDATE rclinks SET url='$_POST[url]', title='$_POST[title]', description='$_POST[description]', modified=UNIX_TIMESTAMP() WHERE rclinkid='$_POST[rclinkid]'");
			$db->query("DELETE FROM rclinkXrclinkcat WHERE rclinkid='$_POST[rclinkid]'");
			foreach ($_POST["rclinkcats"] as $rclinkcat) {
				$db->query("INSERT INTO rclinkXrclinkcat (rclinkid, rclinkcatid) VALUES('$_POST[rclinkid]', '$rclinkcat')");
			}
		} else {
			$db->query("INSERT INTO rclinks (url, title, description, created, modified) VALUES('$_POST[url]', '$_POST[title]', '$_POST[description]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			$newid = $db->insert_id;
			foreach ($_POST["rclinkcats"] as $rclinkcat) {
				$db->query("INSERT INTO rclinkXrclinkcat (rclinkid, rclinkcatid) VALUES('$newid', '$rclinkcat')");
			}
		}
?>
<script language="JavaScript">
<!--
self.opener.doAddRCLink('<?php echo $_POST["title"]; ?>', '<?php echo $newid; ?>');
<?php if (!$noreturn) {?>
opener.location.reload(true);
<?php } ?>
self.close();
//-->
</script>
<?php
	} else {
		echo '<p>The link was not saved. Please correct these errors and resubmit the form:</p><ul>';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
		extract ($_POST);
		$curr_rclinkcats = $rclinkcats;
	}
}
?>
<div id="formarea">
<script language="JavaScript">
<!--
function doAddRCLinkCat (label, value) {
var s = document.rclink.elements["rclinkcats[]"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>
<form name="rclink" method="post" action="newrclink.php">
<table class="table table-striped table-bordered">
<tr><td class="label">URL:</td>
<td><input type="text" name="url" value="<?php echo $url; ?>" maxlength="200" style="width: 250px;" /></td></tr>
<tr><td class="label">Title:</td>
<td><input type="text" name="title" value="<?php echo $title; ?>" maxlength="200" style="width: 200px;" /></td></tr>
<tr><td class="label">Description:</td>
<td><textarea name="description" style="height: 100px; width:200px"><?php echo $description; ?></textarea></td></tr>
<tr><td class="label"><a href="#" onClick="window.open('newrclinkcat.php', 'rclinkcat_new', 'width=400,height=200,resizable=yes,scrollbars=yes,top=50,left=50')"><img src="images/add.gif" alt="Add new category" border="0" /></a> Categories:</td>
<td>
<?php
$rclinkcats = $db->get_results("SELECT rclinkcatid, rclinkcat FROM rclinkcats ORDER BY rclinkcat", ARRAY_N);
build_multiple_select_box($rclinkcats, "rclinkcats[]", $curr_rclinkcats, 16);
?>
</td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" />  <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onclick="self.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($rclinkid) echo '<input type="hidden" name="rclinkid" value="' . $rclinkid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.rclink.url.focus();
</script>
</body>
</html>