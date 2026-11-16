<?php
include_once "includes/prepend.inc.php";
$db = new db("emergency", "32xN!7b", "emergency", "164.177.128.68");
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$en_array = $db->get_row("SELECT * FROM emergency.notices WHERE enid='$_GET[enid]'", ARRAY_A);
	extract($en_array);
}
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<script language="JavaScript">
<!--
function doAddContact (label, value) {
var s = document.notice.elements["contactid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
function editContact() {
var contactid = document.notice.contactid.value;
window.open('newencontact.php?action=edit&contactid=' + contactid, 'editcencontact', 'width=660,height=500,top=50,left=50,resizable=yes,scrollbars=yes');
}
//-->
</script><?php if ($edit == 1) { ?>
<h3>Edit emergency notice</h3>
<?php } else { ?>
<h3>Add new emergency notice</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	$_POST["title"] = stripslashes($_POST["title"]);
	if (!$_POST["title"]) $errors[] = "Please enter a title for this notice";
	if (!$_POST["password"]) $errors[] = "Please enter a password for access to confidential material";
	if (!$_POST["contactid"]) $errors[] = "Please select a contact for this emergency notice";
	if (!$_POST["enid"]) {
		if (!$_POST["description"]) $errors[] = "Please enter a description for the first bulletin";
		if (!$_POST["body"]) $errors[] = "Please enter some body text for the first bulletin";
	}
	if (count($errors) == 0) {
		// ensure only one notice can be live at any one time
		if ($_POST["live"] == 1) $db->query("UPDATE emergency.notices SET live=0");
		$_POST["title"] = addslashes($_POST["title"]);
		if ($_POST["enid"]) {
			$db->query("UPDATE emergency.notices SET title='$_POST[title]', contactid='$_POST[contactid]', live='$_POST[live]', password='$_POST[password]', modified=UNIX_TIMESTAMP() WHERE enid='$_POST[enid]'");
		} else {
			$db->query("INSERT INTO emergency.notices (title, contactid, created, live, password, modified) VALUES('$_POST[title]', '$_POST[contactid]', NOW(), '$_POST[live]', '$_POST[password]', UNIX_TIMESTAMP())");
			$newenid = $db->insert_id;
			$db->query("INSERT INTO emergency.bulletins (enid, description, body, confidential, updated) VALUES('$newenid', '$_POST[description]', '$_POST[body]', '$_POST[confidential]', NOW())");
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
		echo '<p>The notice was not saved. Please correct these errors and resubmit the form:</p><ul>';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
extract ($_POST);
?>
<div id="formarea">
<form name="notice" method="post" action="newemergencynotice.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Title:</td>
<td><input type="text" name="title" value="<?php echo $title; ?>" maxlength="250" style="width: 300px;" /></td></tr>
<tr><td class="label">Contact:</td>
<td>
<?php
$contacts = $db->get_results("SELECT contactid, name FROM emergency.contacts ORDER BY name", ARRAY_N);
build_select_box($contacts, "contactid", $contactid);
?>
<a href="#" onClick="window.open('newencontact.php', 'encontact_new', 'width=500,height=350,resizable=yes,scrollbars=yes,top=50,left=50')"><img src="images/add.gif" alt="Add new contact" border="0" /></a>
<a href="#" onClick="editContact()"><img src="images/edit.gif" alt="Edit contact" border="0" /></a>
</td></tr>
<tr><td class="label">Live?:</td>
<td>
<?php
build_yes_no_checkbox("live", $live);
?>
</td></tr>
<tr><td class="label">Password:</td>
<td><input type="text" name="password" value="<?php echo $password; ?>" maxlength="24" style="width: 100px;" /></td></tr>
<?phpif (!$enid) {?>
<tr><td colspan="2"><h3>First bulletin:</h3></td></tr>
<tr><td class="label">Description:</td>
<td><input type="text" name="description" value="<?php echo $description; ?>" maxlength="250" style="width: 300px;" /></td></tr>
<tr><td class="label">Body text:</td>
<td><textarea name="body" style="width: 300px; height: 80px;"><?php echo $body; ?></textarea></td></tr>
<tr><td class="label">Confidential text:</td>
<td><textarea name="confidential" style="width: 300px; height: 80px;"><?php echo $confidential; ?></textarea></td></tr>
<?php}?>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($enid) echo '<input type="hidden" name="enid" value="' . $enid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.event.event.focus();
</script>
</body>
</html>