<?php

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$emid = $_REQUEST["emid"];
$member=$db->get_var("SELECT name FROM councillors WHERE emid='$emid'");
// Should we output a table of bodies?
$print_table = 0;
function show_body_form($values=array()) {
	global $db;
	echo <<<HTML

<script language="JavaScript">
<!--
function doAddBody (label, value) {
var s = document.bodyfrm.elements["bodyid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>

HTML;
	echo '<form action="memberbodies.php" method="POST" name="bodyfrm">';
	echo '<table class="table table-striped table-bordered">';
	echo '<tr><td class="label">Body: </td><td>';
	$bodies = $db->get_results("SELECT bodyid, body FROM bodies ORDER BY body", ARRAY_N);
	build_select_box($bodies, "bodyid", $values["bodyid"]);
	echo '<a href="#" onClick="window.open(\'newbody.php\', \'body_new\', \'width=740,height=500,resizable=yes,scrollbars=yes,top=50,left=50\')"><img src="images/add.gif" alt="Add new body" border="0" /></a>';
	echo '</td></tr>';
	echo '<tr><td class="label">Position: </td><td><input type="text" name="position" value="' . $values["position"] . '" />';
	echo '</td></tr>';
	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="submit" value="Save">&nbsp;<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onClick="history.go(-1);"></p>';
	echo '<input type="hidden" name="emid" value="' . $values["emid"] . '" />';
	if ($_GET['action'] == "edit") echo '<input type="hidden" name="oldbodyid" value="' . $values["bodyid"] . '" />';
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
<h3>Member Body Memberships</h3>
<?php
switch ($_REQUEST["action"]) {
	case "add":
		echo "<p>Add Body to Member: <strong>$member</strong></p>";
		break;
	case "edit":
		echo "<p>Edit Body for Member: <strong>$member</strong></p>";
		break;
	default:
		echo '<p>Manage body memberships for Member: <strong>' . $member . '</strong> <a href="memberbodies.php?action=add&emid=' . $emid . '"><img src="images/add.gif" alt="Add new body" border="0" /></a></p>';
		break;
}
if ($_POST["submit"]) {
	$print_table = 1;
	if ($_POST["oldbodyid"]) {
		$db->query("DELETE FROM councillorXbody WHERE emid='$_POST[emid]' AND bodyid='$_POST[oldbodyid]'");
	}
	$db->query("INSERT INTO councillorXbody (emid, bodyid, position) VALUES('$_POST[emid]', '$_POST[bodyid]', '$_POST[position]')");
} else if ($_GET["action"]) {
	switch ($_GET['action']) {
		case "add":
			// set values for addition
			$values = array();
			$values["emid"] = $emid;
			show_body_form($values);
			break;
		case "edit":
			// set values for edit
			$body=$db->get_row("SELECT * FROM councillorXbody WHERE emid='$_GET[emid]' AND bodyid='$_GET[bodyid]'");
			$values = array();
			$values["emid"] = $emid;
			$values["bodyid"] = $_GET["bodyid"];
			$values["position"] = $body->position;
			show_body_form($values);
			break;
		case "delete":
			$db->query("DELETE FROM councillorXbody WHERE emid='$_GET[emid]' AND bodyid='$_GET[bodyid]'");
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
if (($bodies = $db->get_results("SELECT * FROM councillorXbody LEFT JOIN bodies USING(bodyid) WHERE emid='$emid' ORDER BY body"))&&($print_table == 1)) {
	echo '<table class="table table-striped table-bordered" width="100%">';
	foreach ($bodies as $body) {
		echo '<tr><td class="detail">' . $body->body . '</td>';
		echo '<td class="detail">' . $body->position . '</td>';
		echo '<td width="100" align="right">';
		echo '<a href="memberbodies.php?action=edit&bodyid=' . $body->bodyid . '&emid=' . $emid . '"><img src="images/edit.gif" alt="Edit Body" border="0"></a> ';
		echo '<a href="memberbodies.php?action=delete&bodyid=' . $body->bodyid . '&emid=' . $emid . '"><img src="images/delete.gif" alt="Delete Body" border="0"></a>';
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