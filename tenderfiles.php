<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$tenderid = $_REQUEST["tenderid"];
$tender=$db->get_var("SELECT tender FROM tenders WHERE tenderid='$tenderid'");
// Should we output a table of files?
$print_table = 0;
function show_tenderfile_form($values=array(), $errors=array()) {
	global $db;
	echo '<form action="tenderfiles.php" method="POST" name="tenderfilefrm" enctype="multipart/form-data">';
	echo '<table class="table table-striped table-bordered">';
	echo '<tr><td class="label">File: </td><td><input type="file" name="tenderfile">';
	if ($errors["tenderfile"]) echo '<span class="error">&nbsp;' . $errors["tenderfile"] . '</span></td></tr>';
	echo '<tr><td class="label">Description: </td><td><textarea cols="80" rows="3" class="search" name="description">' . $values["description"] . '</textarea>';
	if ($errors["description"]) echo '<span class="error">&nbsp;' . $errors["description"] . '</span>';
	echo '</td></tr>';
	if ($values["tenderid"]) echo '<input type="hidden" name="tenderid" value="' . $values["tenderid"] . '">';
	if ($values["tenderfileid"]) echo '<input type="hidden" name="tenderfileid" value="' . $values["tenderfileid"] . '">';
	echo '</td></tr>';
	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" name="submit" value="Save File">&nbsp;<input type="submit" class="btn btn-md btn-secondary" name="cancel" value="Cancel" onClick="history.go(-1);"></p>';
	echo '</form>';
}
function confirm_tenderfile_deletion($tenderfileid, $tenderid) {
	global $db;
	$del=$db->get_row("SELECT description FROM tenderfiles WHERE tenderfileid=$tenderfileid");
	echo 'Are you sure you want to delete file \'' . $del->description . '\'?<p><strong>NB: This file will be removed from the system completely, and all links to tenders removed.</strong></p>';
	echo '<form action="tenderfiles.php" method="post">';
	echo '<input type="hidden" name="del" value="' . $tenderfileid . '">';
	echo '<input type="hidden" name="tenderid" value="' . $tenderid . '">';
	echo '<p class="formbtns"><input class="search" type="submit" name="confdel" value="Yes">&nbsp;';
	echo '<input class="search" type="submit" name="canceldel" value="No" onClick="history.go(-1);"></p>';
	echo '</form>';
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
<div id="formarea">
<h3>Tender Files</h3>
<?php
switch ($_REQUEST["action"]) {
	case "add":
		echo "<p>Add File to Tender: <strong>$tender</strong></p>";
		break;
	case "edit":
		echo "<p>Edit File for Tender: <strong>$tender</strong></p>";
		break;
	case "delete":
		echo "<p>Delete File from Tender: <strong>$tender</strong></p>";
		break;
	default:
		echo '<p>Manage for files for Tender: <strong>' . $tender . '</strong> <a href="tenderfiles.php?action=add&tenderid=' . $tenderid . '"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new file</span></a></p>';
		break;
}
if ($_POST["confdel"]) {
	$deltenderfile=$db->get_row("SELECT filename, description, ext FROM tenderfiles WHERE tenderfileid='$_POST[del]'");
	// delete physical file if required
	if ($deltenderfile->filename) unlink(UPLOAD_DIR . "/document/tenders/" . $_POST["del"] . $deltenderfile->ext);
	$db->query("DELETE FROM tenderfiles WHERE tenderfileid='$_POST[del]'");
	log_entry($_SESSION[userid],"Deleted tender file $deltenderfile->description, key=$_POST[del]");
	echo "<p>File '$deltenderfile->description' deleted.</p>";
	$print_table = 1;
} else if ($_POST["submit"]) {
	$errors=array();
	if (!$_POST["description"]) $errors["description"] = "No description entered for file";
	if ((!$_POST["tenderfileid"])&&($_FILES['tenderfile']['name'] == "")&&(!$_POST["url"])) $errors["tenderfile"] = "No file selected for upload";
	if (count($errors) == 0) {
		$print_table = 1;
		if ($_POST["tenderfileid"]) {
			if ($_FILES["tenderfile"]["name"] > "") {
				$uploaddir = UPLOAD_DIR . "/document/tenders/";
				$ext = substr($_FILES['tenderfile']['name'], -4);
				$mime = $_FILES['tenderfile']['type'];
				$filename = strtolower(strtr($_FILES['tenderfile']['name'], " ?*#$%^:+=<>/\"", "______________"));
				$destination_filename = $_POST["tenderfileid"] . $ext;
				move_uploaded_file($_FILES['tenderfile']['tmp_name'], $uploaddir . $destination_filename);
				chmod($uploaddir . $destination_filename, 0644);
				$db->query("UPDATE tenderfiles SET filename='$filename', ext='$ext', mime='$mime', description='$_POST[description]', modified=UNIX_TIMESTAMP() WHERE tenderfileid='$_POST[tenderfileid]'");
			} else {
				$db->query("UPDATE tenderfiles SET description='$_POST[description]', modified=UNIX_TIMESTAMP() WHERE tenderfileid='$_POST[tenderfileid]'");
			}
			log_entry($_SESSION["userid"],"Updated Tender file $_POST[description], key $_POST[tenderfileid]");
			echo "<p>Tender file '$_POST[description]' updated</p>";
		} else {
			// Set upload dir, get extension and original filename
			if ($_FILES['tenderfile']['name']) {
				$uploaddir = UPLOAD_DIR . "/document/tenders/";
				$ext = substr($_FILES['tenderfile']['name'], -4);
				$mime = $_FILES['tenderfile']['type'];
				$filename = strtolower(strtr($_FILES['tenderfile']['name'], " ?*#$%^:+=<>/\"", "______________"));
			}
			// Insert new docfile record
			$db->query("INSERT INTO tenderfiles (tenderid, filename, ext, mime, description, modified, created)
						VALUES('$_POST[tenderid]', '$filename', '$ext', '$mime', '$_POST[description]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			$newtenderfileid=$db->insert_id;
			if ($_FILES['tenderfile']['name']) {
				// Move uploaded file with name = tenderfileid + extension
				$destination_filename = $newtenderfileid . $ext;
				move_uploaded_file($_FILES['tenderfile']['tmp_name'], $uploaddir . $destination_filename);
				chmod($uploaddir . $destination_filename, 0644);
			}
			// Log file upload
			log_entry($_SESSION["userid"],"Added Tender file $_POST[description], key $newtenderfileid");
			echo "<p>Added file '$_POST[description]'</p>";
		}
	} else {
		show_tenderfile_form($_POST, $errors);
	}
} else if ($_GET["action"]) {
	switch ($_GET["action"]) {
		case "add":
			// set values for addition
			$values = array();
			$values["tenderid"] = $tenderid;
			show_tenderfile_form($values);
			break;
		case "edit":
			// set values for edit
			$tenderfile=$db->get_row("SELECT * FROM tenderfiles WHERE tenderfileid='$_GET[tenderfileid]'");
			$values = array();
			$values["tenderid"] = $tenderid;
			$values["tenderfileid"] = $_GET["tenderfileid"];
			$values["description"] = $tenderfile->description;
			show_tenderfile_form($values);
			break;
		case "delete":
			confirm_tenderfile_deletion($_GET["tenderfileid"], $tenderid);
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
if (($tenderfiles = $db->get_results("SELECT * FROM tenderfiles WHERE tenderid='$tenderid'"))&&($print_table == 1)) {
	echo '<table width="100%">';
	foreach ($tenderfiles as $tenderfile) {
		echo '<tr><td>';
		echo '<img src="images/';
		switch ($tenderfile->mime) {
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
		echo '" width="20" height="16" alt="' . $tenderfile->mime . '"></td>';
		echo '<td class="detail">';
		echo '<a target="_blank" href="/document/tenders/' . $tenderfile->tenderfileid . $tenderfile->ext . '">' . $tenderfile->filename . '</a>';
		echo '</td>';
		echo '<td class="detail">' . $tenderfile->description . '</td>';
		echo '<td width="100" align="right">';
		echo '<a href="tenderfiles.php?action=edit&tenderfileid=' . $tenderfile->tenderfileid . '&tenderid=' . $tenderid . '"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">Edit File</span></a> ';
		echo '<a href="tenderfiles.php?action=delete&tenderfileid=' . $tenderfile->tenderfileid . '&tenderid=' . $tenderid . '"><svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">Delete File</span></a>';
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
}
if ($print_table == 1) 	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-secondary" value="Close" onclick="self.close();"></p>';
?>
</div>
</body>
</html>