<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$meetingid = $_REQUEST["meetingid"];
$meeting=$db->get_row("SELECT meetingid, meetings.bodyid, body, meetingdate FROM events.meetings LEFT JOIN clacksweb2.bodies USING(bodyid) WHERE meetingid='$meetingid'");
// Should we output a table of files?
$print_table = 0;
function show_meetingfile_form($values=array(), $errors=array()) {
	global $db;
	echo '<form action="meetingfiles.php" method="POST" name="meetingfilefrm" enctype="multipart/form-data">';
	echo '<table class="table table-striped table-bordered">';
	echo '<tr><td class="label">File: </td><td><input type="file" name="meetingfile">';
	if ($errors["meetingfile"]) echo '<span class="error">&nbsp;' . $errors["meetingfile"] . '</span></td></tr>';
	echo '<tr><td class="label">File type: </td><td>';
	$filetypes = $db->get_results("SELECT typeid, type FROM events.meetingfiletypes ORDER BY type", ARRAY_N);
	build_select_box($filetypes, "typeid", $values["typeid"]);
	echo '</td></tr>';
	echo '<tr><td class="label">Description: </td><td><textarea cols="80" rows="3" class="search" name="description">' . $values["description"] . '</textarea>';
	if ($errors["description"]) echo '<span class="error">&nbsp;' . $errors["description"] . '</span>';
	echo '</td></tr>';
	if ($values["meetingid"]) echo '<input type="hidden" name="meetingid" value="' . $values["meetingid"] . '">';
	if ($values["meetingfileid"]) echo '<input type="hidden" name="meetingfileid" value="' . $values["meetingfileid"] . '">';
	echo '</td></tr>';
	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="submit" value="Save File">&nbsp;<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onClick="history.go(-1);"></p>';
	echo '</form>';
}
function confirm_meetingfile_deletion($meetingfileid, $meetingid) {
	global $db;
	$del=$db->get_row("SELECT type, filename, ext FROM events.meetingfiles LEFT JOIN events.meetingfiletypes USING(typeid) WHERE meetingfileid=$meetingfileid");
	echo 'Are you sure you want to delete ' . $del->type . ' file ' . $del->filename . '?<p><strong>NB: This file will be removed from the system completely, and all links to meetings removed.</strong></p>';
	echo '<form action="meetingfiles.php" method="post">';
	echo '<input type="hidden" name="del" value="' . $meetingfileid . '">';
	echo '<input type="hidden" name="meetingid" value="' . $meetingid . '">';
	echo '<p class="formbtns"><input class="search" type="submit" name="confdel" value="Yes">&nbsp;';
	echo '<input class="search" type="submit" name="canceldel" value="No" onClick="history.go(-1);"></p>';
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
<h3>Meeting Files</h3>
<?php
switch ($_REQUEST["action"]) {
	case "add":
		echo "<p>Add File to Meeting: <strong>$meeting->body ($meeting->meetingdate)</strong></p>";
		break;
	case "edit":
		echo "<p>Edit File for Meeting: <strong>$meeting->body ($meeting->meetingdate)</strong></p>";
		break;
	case "delete":
		echo "<p>Delete File from Meeting: <strong>$meeting->body ($meeting->meetingdate)</strong></p>";
		break;
	default:
		echo '<p>Manage for files for Meeting: <strong>' . $meeting->body . ' (' . $meeting->meetingdate . ')' . '</strong> <a href="meetingfiles.php?action=add&meetingid=' . $meeting->meetingid . '"><img src="images/add.gif" alt="Add new file" border="0" /></a></p>';
		break;
}
if ($_POST["confdel"]) {
	$delmeetingfile=$db->get_row("SELECT filename, description, ext FROM events.meetingfiles WHERE meetingfileid='$_POST[del]'");
	// delete physical file if required
	if ($delmeetingfile->filename) unlink(UPLOAD_DIR . "/document/meeting/" . $meeting->bodyid . "/" . $meeting->meetingid . "/" . $_POST["del"] . $delmeetingfile->ext);
	$db->query("DELETE FROM events.meetingfiles WHERE meetingfileid='$_POST[del]'");
	log_entry($_SESSION[userid],"Deleted meeting file $delmeetingfile->filename, key=$_POST[del]");
	echo "<p>File '$delmeetingfile->filename' deleted.</p>";
	$print_table = 1;
} else if ($_POST["submit"]) {
	$errors=array();
	if ((!$_POST["meetingfileid"])&&($_FILES['meetingfile']['name'] == "")) $errors["meetingfile"] = "No file selected for upload";
	if (count($errors) == 0) {
		$print_table = 1;
		if ($_POST["meetingfileid"]) {
			if ($_FILES["meetingfile"]["name"] > "") {
				if (!file_exists(UPLOAD_DIR . "/document/meeting/" . $meeting->bodyid . "/")) {
					mkdir(UPLOAD_DIR . "/document/meeting/" . $meeting->bodyid);
				}
				if (!file_exists(UPLOAD_DIR . "/document/meeting/" . $meeting->bodyid . "/" . $meeting->meetingid . "/")) {
					mkdir(UPLOAD_DIR . "/document/meeting/" . $meeting->bodyid . "/" . $meeting->meetingid);
				}
				$uploaddir = UPLOAD_DIR . "/document/meeting/" . $meeting->bodyid . "/" . $meeting->meetingid . "/";
				$ext = substr($_FILES['meetingfile']['name'], -4);
				$mime = $_FILES['meetingfile']['type'];
				$filename = strtolower(strtr($_FILES['meetingfile']['name'], " ?*#$%^:+=<>/\"", "______________"));
				$destination_filename = $_POST["meetingfileid"] . $ext;
				move_uploaded_file($_FILES['meetingfile']['tmp_name'], $uploaddir . $destination_filename);
				chmod($uploaddir . $destination_filename, 0644);
				$db->query("UPDATE events.meetingfiles SET filename='$filename', ext='$ext', mime='$mime', typeid='$_POST[typeid]', description='$_POST[description]', modified=UNIX_TIMESTAMP() WHERE meetingfileid='$_POST[meetingfileid]'");
			} else {
				$db->query("UPDATE events.meetingfiles SET description='$_POST[description]', typeid='$_POST[typeid]', modified=UNIX_TIMESTAMP() WHERE meetingfileid='$_POST[meetingfileid]'");
			}
			log_entry($_SESSION["userid"],"Updated Meeting file $filename.$ext, key $_POST[meetingfileid]");
			echo "<p>Meeting file updated</p>";
		} else {
			// Set upload dir, get extension and original filename
			if ($_FILES['meetingfile']['name']) {
				if (!file_exists(UPLOAD_DIR . "/document/meeting/" . $meeting->bodyid . "/")) {
					mkdir(UPLOAD_DIR . "/document/meeting/" . $meeting->bodyid);
				}
				if (!file_exists(UPLOAD_DIR . "/document/meeting/" . $meeting->bodyid . "/" . $meeting->meetingid . "/")) {
					mkdir(UPLOAD_DIR . "/document/meeting/" . $meeting->bodyid . "/" . $meeting->meetingid);
				}
				$uploaddir = UPLOAD_DIR . "/document/meeting/" . $meeting->bodyid . "/" . $meeting->meetingid . "/";
				$ext = substr($_FILES['meetingfile']['name'], -4);
				$mime = $_FILES['meetingfile']['type'];
				$filename = strtolower(strtr($_FILES['meetingfile']['name'], " ?*#$%^:+=<>/\"", "______________"));
			}
			// Insert new docfile record
			$db->query("INSERT INTO events.meetingfiles (meetingid, filename, ext, mime, typeid, description, modified, created)
						VALUES('$_POST[meetingid]', '$filename', '$ext', '$mime', '$_POST[typeid]', '$_POST[description]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			$newmeetingfileid=$db->insert_id;
			if ($_FILES['meetingfile']['name']) {
				// Move uploaded file with name = meetingfileid + extension
				$destination_filename = $newmeetingfileid . $ext;
				move_uploaded_file($_FILES['meetingfile']['tmp_name'], $uploaddir . $destination_filename);
				chmod($uploaddir . $destination_filename, 0644);
			}
			// Log file upload
			log_entry($_SESSION["userid"],"Added Meeting file, key $newmeetingfileid");
			echo "<p>Added file to meeting</p>";
		}
	} else {
		show_meetingfile_form($_POST, $errors);
	}
} else if ($_GET["action"]) {
	switch ($_GET["action"]) {
		case "add":
			// set values for addition
			$values = array();
			$values["meetingid"] = $meetingid;
			show_meetingfile_form($values);
			break;
		case "edit":
			// set values for edit
			$meetingfile=$db->get_row("SELECT * FROM events.meetingfiles WHERE meetingfileid='$_GET[meetingfileid]'");
			$values = array();
			$values["meetingid"] = $meetingid;
			$values["meetingfileid"] = $_GET["meetingfileid"];
			$values["typeid"] = $meetingfile->typeid;
			$values["description"] = $meetingfile->description;
			show_meetingfile_form($values);
			break;
		case "delete":
			confirm_meetingfile_deletion($_GET["meetingfileid"], $meetingid);
			$print_table = 0;
			break;
		default:
			// unknown action
			echo "Unknown action";
			break;
	}
} else {
	$print_table = 1;
}
if (($meetingfiles = $db->get_results("SELECT meetingfiles.*, meetingfiletypes.type FROM events.meetingfiles LEFT JOIN events.meetingfiletypes USING(typeid) WHERE meetingid='$meetingid'"))&&($print_table == 1)) {
	echo '<table width="100%">';
	foreach ($meetingfiles as $meetingfile) {
		echo '<tr><td>';
		echo '<img src="images/';
		switch ($meetingfile->mime) {
			case "application/msword":
				echo 'msword.gif';
				$mimetype="MS Word";
				break;
			case "application/rtf":
				echo 'rtf.gif';
				$mimetype="Rich Text Format";
				break;
			case "application/pdf":
				echo 'pdf.gif';
				$mimetype="Adobe Acrobat";
				break;
			case "text/plain":
				echo 'doc.gif';
				$mimetype="Text";
				break;
			default:
				echo 'doc.gif';
				$mimetype="Other";
		}
		echo '" width="20" height="16" alt="' . $meetingfile->mime . '"></td>';
		echo '<td class="detail">';
		echo '<a target="_blank" href="/document/meeting/' . $meeting->bodyid . '/' . $meeting->meetingid . '/' . $meetingfile->meetingfileid . $meetingfile->ext . '">' . $meetingfile->filename . '</a>';
		echo '</td>';
		echo '<td class="detail">' . $meetingfile->type . '</td>';
		echo '<td class="detail">' . $meetingfile->description . '</td>';
		echo '<td width="100" align="right">';
		echo '<a href="meetingfiles.php?action=edit&meetingfileid=' . $meetingfile->meetingfileid . '&meetingid=' . $meeting->meetingid . '"><img src="images/edit.gif" alt="Edit File" border="0"></a> ';
		echo '<a href="meetingfiles.php?action=delete&meetingfileid=' . $meetingfile->meetingfileid . '&meetingid=' . $meeting->meetingid . '"><img src="images/delete.gif" alt="Delete File" border="0"></a>';
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