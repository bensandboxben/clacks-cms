<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$meetingid = $_REQUEST["meetingid"];
$meeting=$db->get_row("SELECT meetingdate, body FROM events.meetings LEFT JOIN clacksweb2.bodies USING(bodyid) WHERE meetingid='$meetingid'");
// Should we output a table of agendaitems?
$print_table = 0;
function show_item_form($values=array()) {
	global $db;
	echo '<form action="agenda.php" method="POST" name="agendafrm">';
	echo '<table class="table table-striped table-bordered">';
	echo '<tr><td class="label">Agenda No: </td><td><input type="text" maxlength="10" name="itemno" value="' . $values["itemno"] . '" />';
	echo '</td></tr>';
	echo '<tr><td class="label">Title: </td><td><input type="text" maxlength="250" size="50" name="title" value="' . $values["title"] . '" />';
	echo '</td></tr>';
	echo '<tr><td class="label">Prepared by: </td><td>';
	$names = $db->get_results("SELECT DISTINCT preparedby FROM events.agendas ORDER BY preparedby");
	echo '<select name="preparedby">';
	foreach ($names as $name) {
		echo '<option value="' . $name->preparedby . '"';
		if ($values["preparedby"] == $name->preparedby) echo ' selected="selected"';
		echo '>' . $name->preparedby . '</option>';
	}
	echo '</select><br />';
	echo '<input type="text" maxlength="200" name="newpreparedby" value="' . $values["newpreparedby"] . '" />';
	echo '</td></tr>';
	echo '<tr><td class="label">Other authors: </td><td><input type="text" size="50" maxlength="250" name="otherauthors" value="' . $values["otherauthors"] . '" />';
	echo '</td></tr>';
	echo '<tr><td class="label">Service: </td><td>';
	$services = $db->get_results("SELECT serviceid, service FROM cms.services ORDER BY service", ARRAY_N);
	build_select_box($services, "serviceid", $values["serviceid"]);
	echo '</td></tr>';
	echo '<tr><td class="label">Category: </td><td>';
	$cats = $db->get_results("SELECT catid, category FROM clacksweb2.categories ORDER BY category", ARRAY_N);
	build_select_box($cats, "catid", $values["catid"], "--");
	echo '</td></tr>';
	echo '</td></tr>';
	echo '<tr><td class="label">Planning Application Keyval: </td><td><input type="text" maxlength="16" name="planningref" value="' . $values["planningref"] . '" />';
	echo '</td></tr>';
	echo '<tr><td class="label">2nd Planning Application Keyval: </td><td><input type="text" maxlength="16" name="planningref2" value="' . $values["planningref2"] . '" />';
	echo '</td></tr>';
	echo '<tr><td class="label">3rd Planning Application Keyval: </td><td><input type="text" maxlength="16" name="planningref3" value="' . $values["planningref3"] . '" />';
	echo '</td></tr>';
	echo '<tr><td class="label">4th Planning Application Keyval: </td><td><input type="text" maxlength="16" name="planningref4" value="' . $values["planningref4"] . '" />';
	echo '</td></tr>';
	echo '<tr><td class="label">5th Planning Application Keyval: </td><td><input type="text" maxlength="16" name="planningref5" value="' . $values["planningref5"] . '" />';
	echo '</td></tr>';
	echo '<tr><td class="label">Comments: </td><td><textarea name="comments" style="width: 300px; height: 80px;">' . $values["comments"] . '</textarea>';
	echo '</td></tr>';
	echo'<tr><td class="label">Exempt?</td><td>';
	build_yes_no_checkbox("exempt", $values["exempt"]);
	echo ' under paras: <input type="text" name="exemptreason" value="' . $values["exemptreason"] . '">';
	echo '</td></tr>';
	echo'<tr><td class="label">Confidential?</td><td>';
	build_yes_no_checkbox("confidential", $values["confidential"]);
	echo '</td></tr>';
	echo'<tr><td class="label">Highlight report?</td><td>';
	build_yes_no_checkbox("highlight", $values["highlight"]);
	echo '</td></tr>';
	echo'<tr><td class="label">Exclude from Sitemorse Audit</td><td>';
	build_yes_no_checkbox("sitemorseexclude", $values["sitemorseexclude"]);
	echo '</td></tr>';

	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="submit" value="Save">&nbsp;<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onClick="history.go(-1);"></p>';
	echo '<input type="hidden" name="meetingid" value="' . $values["meetingid"] . '" />';
	if ($_REQUEST["action"] == 'edit') echo '<input type="hidden" name="itemid" value="' . $values["itemid"] . '" />';
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
<h3>Agenda items</h3>
<?php
switch ($_REQUEST["action"]) {
	case "add":
		echo "<p>Add item to agenda: <strong>$meeting->body, $meeting->meetingdate</strong></p>";
		break;
	case "edit":
		echo "<p>Edit item for agenda: <strong>$meeting->body, $meeting->meetingdate</strong></p>";
		break;
	default:
		echo '<p>Manage agenda items: <strong>' . $meeting->body . ', ' . $meeting->meetingdate . '</strong> <a href="agenda.php?action=add&meetingid=' . $meetingid . '"><img src="images/add.gif" alt="Add new item" border="0" /></a></p>';
		break;
}
if ($_POST["submit"]) {
	$print_table = 1;
	if ($_POST["newpreparedby"]) {
		$preparedby = $_POST["newpreparedby"];
	} else {
		$preparedby = $_POST["preparedby"];
	}



		$catid = $_POST['catid'] ?? ''; // Set default value if not set		
		if ($catid === null || $catid === '') {
		    $catid = '0'; // Replace null or empty string with '0'
		}

		$exempt = $_POST['exempt'] ?? ''; // Set default value if not set		
		if ($exempt === null || $exempt === '') {
		    $exempt = '0'; // Replace null or empty string with '0'
		}
		$confidential = $_POST['confidential'] ?? ''; // Set default value if not set		
		if ($confidential === null || $confidential === '') {
		    $confidential = '0'; // Replace null or empty string with '0'
		}
		$highlight = $_POST['highlight'] ?? ''; // Set default value if not set		
		if ($highlight === null || $highlight === '') {
		    $highlight = '0'; // Replace null or empty string with '0'
		}
		$sitemorseexclude = $_POST['sitemorseexclude'] ?? ''; // Set default value if not set		
		if ($sitemorseexclude === null || $sitemorseexclude === '') {
		    $sitemorseexclude = '0'; // Replace null or empty string with '0'
		}


	if (($_POST["meetingid"])&&($_POST["itemid"])) {
		$db->query("UPDATE events.agendas SET itemno='$_POST[itemno]', title='$_POST[title]', preparedby='$preparedby', otherauthors='$_POST[otherauthors]', serviceid='$_POST[serviceid]', catid='$catid', planningref='$_POST[planningref]', planningref2='$_POST[planningref2]', planningref3='$_POST[planningref3]', planningref4='$_POST[planningref4]', planningref5='$_POST[planningref5]', comments='$_POST[comments]', exempt='$exempt', exemptreason='$_POST[exemptreason]', confidential='$confidential', highlight='$highlight', sitemorseexclude='$sitemorseexclude' WHERE meetingid='$_POST[meetingid]' AND itemid='$_POST[itemid]'");
		create_zip($_POST["itemid"]);
	} else {
		$db->query("INSERT INTO events.agendas (meetingid, itemno, title, preparedby, otherauthors, serviceid, catid, planningref, planningref2, planningref3, planningref4, planningref5, comments, exempt, exemptreason, confidential, highlight, sitemorseexclude) VALUES('$_POST[meetingid]', '$_POST[itemno]', '$_POST[title]', '$preparedby', '$_POST[otherauthors]', '$_POST[serviceid]', '$catid', '$_POST[planningref]', '$_POST[planningref2]', '$_POST[planningref3]', '$_POST[planningref4]', '$_POST[planningref5]', '$_POST[comments]', '$exempt', '$_POST[exemptreason]', '$confidential', '$highlight', '$sitemorseexclude')");
	}
} else if ($_GET["action"]) {
	switch ($_GET["action"]) {
		case "add":
			// set values for addition
			$values = array();
			$values["meetingid"] = $meetingid;
			show_item_form($values);
			break;
		case "edit":
			// set values for edit
			$item=$db->get_row("SELECT * FROM events.agendas WHERE meetingid='$_GET[meetingid]' AND itemid='$_GET[itemid]'");
			$values = array();
			$values["meetingid"] = $meetingid;
			$values["itemid"] = $_GET["itemid"];
			$values["itemno"] = $item->itemno;
			$values["title"] = $item->title;
			$values["preparedby"] = $item->preparedby;
			$values["otherauthors"] = $item->otherauthors;
			$values["serviceid"] = $item->serviceid;
			$values["catid"] = $item->catid;
			$values["planningref"] = $item->planningref;
			$values["planningref2"] = $item->planningref2;
			$values["planningref3"] = $item->planningref3;
			$values["planningref4"] = $item->planningref4;
			$values["planningref5"] = $item->planningref5;
			$values["comments"] = $item->comments;
			$values["exempt"] = $item->exempt;
			$values["exemptreason"] = $item->exemptreason;
			$values["confidential"] = $item->confidential;
			$values["highlight"] = $item->highlight;
			$values["sitemorseexclude"] = $item->sitemorseexclude;
			show_item_form($values);
			break;
		case "delete":
			// find and delete any files for this item
			$meetinginfo = $db->get_row("SELECT bodyid FROM events.meetings WHERE meetingid='$_GET[meetingid]'");
			if ($files = $db->get_results("SELECT * FROM events.agendafiles WHERE itemid='$_GET[itemid]'")) {
				foreach ($files as $file) {
					unlink(UPLOAD_DIR . "document/meeting/" . $meetinginfo->bodyid . "/" . $_GET["meetingid"] . "/" . $file->agendafileid . $file->ext);
				}
				create_zip($_GET["itemid"]);
				$db->query("DELETE FROM events.agendafiles WHERE itemid='$_GET[itemid]'");
			}
			$db->query("DELETE FROM events.agendas WHERE meetingid='$_GET[meetingid]' AND itemid='$_GET[itemid]'");
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
if (($items = $db->get_results("SELECT * FROM events.agendas WHERE meetingid='$meetingid' ORDER BY itemno"))&&($print_table == 1)) {
	echo '<table width="100%" class="table table-striped table-bordered"><thead><tr><th>Item No</th><th>Title</th><th>Tools</th></tr></thead><tbody>';
	foreach ($items as $item) {
		echo '<tr><td class="detail" align="center">' . $item->itemno . '</td>';
		echo '<td class="detail">' . $item->title . '</td>';
		echo '<td width="100" align="right">';
		echo '<a href="agenda.php?action=edit&meetingid=' . $item->meetingid . '&itemid=' . $item->itemid . '"><img src="images/edit.gif" alt="Edit item" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'agendafiles.php?itemid=' . $item->itemid . '\', \'agendafiles\', \'width=700,height=500,top=70,left=70,resizable=yes,scrollbars=yes\')"><img src="images/files.gif" alt="Item files" border="0"></a> ';
		echo '<a href="agenda.php?action=delete&meetingid=' . $item->meetingid . '&itemid=' . $item->itemid . '"><img src="images/delete.gif" alt="Delete item" border="0"></a>';
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
}
if ($print_table == 1) 	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Close" onclick="self.close();"></p>';
?>
</div>
</body>
</html>