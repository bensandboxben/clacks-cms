<?php
include_once "includes/prepend.inc.php";
$db = new db("emergency", "32xN!7b", "emergency", "164.177.128.68");
header("Cache-Control: private");
$enid = $_REQUEST["enid"];
$notice=$db->get_var("SELECT title FROM emergency.notices WHERE enid='$enid'");
// Should we output a table of bulletins?
$print_table = 0;
function show_bulletin_form($values=array()) {
	global $db;
	echo '<form action="ebulletins.php" method="POST" name="bulletinfrm">';
	echo '<table class="table table-striped table-bordered">';
	echo '<tr><td class="label">Description: </td>';
	echo '<td><input type="text" name="description" value="' . $values["description"] . '" maxlength="250" style="width: 300px;" /></td></tr>';
	echo '<tr><td class="label">Body text: </td><td><textarea name="body" style="width: 300px; height: 80px;">' . $values["body"] . '</textarea></td></tr>';
	echo '<tr><td class="label">Confidential text: </td><td><textarea name="confidential" style="width: 300px; height: 80px;">' . $values["confidential"] . '</textarea></td></tr>';
	if ($values["bid"]) {
		echo '<tr><td class="label">Date &amp; time:</td>';
		echo '<td>' . $values["updated"] . ' - update? ';
		build_yes_no_checkbox("resetdate", $values["resetdate"]);
		echo '</td></tr>';
	}
	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="submit" value="Save">&nbsp;<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onClick="history.go(-1);"></p>';
	if ($values["bid"]) echo '<input type="hidden" name="bid" value="' . $values["bid"] . '" />';
	if ($values["enid"]) echo '<input type="hidden" name="enid" value="' . $values["enid"] . '" />';
	echo '</form>';
}
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<div id="formarea">
<h3>Bulletins</h3>
<?php
switch ($_REQUEST["action"]) {
	case "add":
		echo "<p>Add Bulletin to Notice: <strong>$notice</strong></p>";
		break;
	case "edit":
		echo "<p>Edit Bulletin for Notice: <strong>$notice</strong></p>";
		break;
	default:
		echo '<p>Manage bulletins for Notice: <strong>' . $notice . '</strong> <a href="ebulletins.php?action=add&enid=' . $enid . '"><img src="images/add.gif" alt="Add new bulletin" border="0" /></a></p>';
		break;
}
if ($_POST["submit"]) {
	$print_table = 1;
	if ($_POST["bid"]) {
		if ($_POST["resetdate"] == 1) {
			$db->query("UPDATE emergency.bulletins SET description='$_POST[description]', body='$_POST[body]', confidential='$_POST[confidential]', updated=NOW() WHERE bid='$_POST[bid]'");
		} else {
			$db->query("UPDATE emergency.bulletins SET description='$_POST[description]', body='$_POST[body]', confidential='$_POST[confidential]' WHERE bid='$_POST[bid]'");
		}
	} else {
		$db->query("INSERT INTO emergency.bulletins (enid, description, body, confidential, updated) VALUES('$_POST[enid]', '$_POST[description]', '$_POST[body]', '$_POST[confidential]', NOW())");
	}
} else if ($_GET["action"]) {
	switch ($_GET["action"]) {
		case "add":
			// set values for addition
			$values = array();
			$values["enid"] = $enid;
			show_bulletin_form($values);
			break;
		case "edit":
			// set values for edit
			$bulletin=$db->get_row("SELECT * FROM emergency.bulletins WHERE bid='$_GET[bid]'");
			$values = array();
			$values["enid"] = $enid;
			$values["bid"] = $_GET["bid"];
			$values["description"] = $bulletin->description;
			$values["body"] = $bulletin->body;
			$values["confidential"] = $bulletin->confidential;
			$values["updated"] = $bulletin->updated;
			show_bulletin_form($values);
			break;
		case "delete":
			$db->query("DELETE FROM emergency.bulletins WHERE bid='$_GET[bid]'");
			$print_table = 1;
			break;
		default:
			// unknown action
			echo "Unknown action";
			break;
	}
} else {
	$print_table = 1;
}
if (($bulletins = $db->get_results("SELECT * FROM emergency.bulletins WHERE enid='$enid' ORDER BY updated DESC"))&&($print_table == 1)) {
	echo '<table width="100%">';
	foreach ($bulletins as $bulletin) {
		echo '<tr><td class="detail">' . $bulletin->description . '</td>';
		echo '<td class="detail">' . $bulletin->updated . '</td>';
		echo '<td width="100" align="right">';
		echo '<a href="ebulletins.php?action=edit&bid=' . $bulletin->bid . '&enid=' . $enid . '"><img src="images/edit.gif" alt="Edit Bulletin" border="0"></a> ';
		echo '<a href="ebulletins.php?action=delete&bid=' . $bulletin->bid . '&enid=' . $enid . '"><img src="images/delete.gif" alt="Delete Bulletin" border="0"></a>';
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
}
if ($print_table == 1) 	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Close" onclick="self.close();"></p>';
?>
</div>
</body>
</html>