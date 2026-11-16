<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$extlink_array = $db->get_row("SELECT * FROM extlinks WHERE extlinkid='$_GET[id]'", ARRAY_A);
	extract($extlink_array);
	$curr_extlinkcats = $db->get_col("SELECT extlinkcatid FROM extlinkXextlinkcat WHERE extlinkid = '$_GET[id]'");
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
<h3>Edit external link</h3>
<?php } else { ?>
<h3>Add external link</h3>
<?php
}
if ($_POST["save"]) {


  $errors = [];

    // Validate input fields
    $url = $_POST["url"] ?? '';
    $title = $_POST["title"] ?? '';
    $description = $_POST["description"] ?? '';
    $dblink = $_POST["dblink"] ?? '';
    $extlinkcats = $_POST["extlinkcats"] ?? [];

    if (empty($url)) {
        $errors[] = "Please enter a URL";
    }
	
	
	if (!$_POST["title"]) {
		$errors[] = "Please enter a title";
	} else 	if ($exists = $db->get_row("SELECT title, extlinkid FROM extlinks WHERE title='$_POST[title]'")) {
		if ($exists->extlinkid != $_POST["extlinkid"]) $errors[] = "A link to that site already exists in the links database";
	}
	if (!$_POST["description"]) $errors[] = "Please enter a brief description";
	if (!isset($_POST["extlinkcats"]) || count($_POST["extlinkcats"]) == 0) {
    		$errors[] = "Please select at least one category";
	}

	if ($errors !== null && (is_array($errors) || $errors instanceof Countable) && count($errors) == 0) {
		$dblink = $_POST['dblink'] ?? ''; // Set default value if not set		
		if ($dblink === null || $dblink === '') {
		    $dblink = '0'; // Replace null or empty string with '0'
		}
		
		if ($_POST["extlinkid"]) {
			$db->query("UPDATE extlinks SET url='$_POST[url]', title='$_POST[title]', description='$_POST[description]', dblink='$dblink', modified=UNIX_TIMESTAMP() WHERE extlinkid='$_POST[extlinkid]'");
			$db->query("DELETE FROM extlinkXextlinkcat WHERE extlinkid='$_POST[extlinkid]'");
			foreach ($_POST["extlinkcats"] as $extlinkcat) {
				$db->query("INSERT INTO extlinkXextlinkcat (extlinkid, extlinkcatid) VALUES('$_POST[extlinkid]', '$extlinkcat')");
			}
		} else {
			$db->query("INSERT INTO extlinks (url, title, description, dblink, created, modified) VALUES('$_POST[url]', '$_POST[title]', '$_POST[description]', '$dblink', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			$newid = $db->insert_id;
			foreach ($_POST["extlinkcats"] as $extlinkcat) {
				$db->query("INSERT INTO extlinkXextlinkcat (extlinkid, extlinkcatid) VALUES('$newid', '$extlinkcat')");
			}
		}
?>
<script language="JavaScript">
<!--
self.opener.doAddExtLink('<?php echo $_POST["title"]; ?>', '<?php echo $newid; ?>');
<?php if (!$noreturn) {?>
opener.location.reload(true);
<?php } ?>
self.close();
//-->
</script>
<?php
	} else {
		echo '<p class="form-error-intro" role="alert">The link was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
		extract ($_POST);
		$curr_extlinkcats = $extlinkcats;
	}
}
?>
<div id="formarea">
<script language="JavaScript">
<!--
function doAddExtLinkCat (label, value) {
var s = document.extlink.elements["extlinkcats[]"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>
<form name="extlink" method="post" action="newextlink.php">
<table class="table table-striped table-bordered">
<tr><td class="label">URL:</td>
<td><input type="text" name="url" value="<?php echo $url; ?>" maxlength="200" style="width: 250px;" /></td></tr>
<tr><td class="label">Title:</td>
<td><input type="text" name="title" value="<?php echo $title; ?>" maxlength="200" style="width: 200px;" /></td></tr>
<tr><td class="label">Description:</td>
<td><textarea name="description" style="height: 100px; width:200px"><?php echo $description; ?></textarea></td></tr>
<tr><td class="label">Include in ClacksWeb links database:</td>
<td>
<?php build_yes_no_checkbox("dblink", $dblink); ?>
</td></tr>
<tr><td class="label"><a href="#" onClick="window.open('newextlinkcat.php', 'extlinkcat_new', 'width=400,height=200,resizable=yes,scrollbars=yes,top=50,left=50')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new category</span></a> Categories:</td>
<td>

<?php
$extlinkcats = $db->get_results("SELECT extlinkcatid, extlinkcat FROM extlinkcats ORDER BY extlinkcat", ARRAY_N);
build_multiple_select_box($extlinkcats, "extlinkcats[]", $curr_extlinkcats, 16);
?>


</td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" />  <input type="submit" class="btn btn-md btn-secondary" name="cancel" value="Cancel" onclick="self.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($extlinkid) echo '<input type="hidden" name="extlinkid" value="' . $extlinkid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.extlink.url.focus();
</script>
</body>
</html>