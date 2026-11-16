<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include_once "includes/prepend.inc.php";
header("Cache-Control: private");

require_once '/home/site/wwwroot/includes/project/vendor/autoload.php';

use WindowsAzure\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\DeleteBlobOptions;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=clacksweb2023;AccountKey=usiOS9rAMaCeA0XM2ByUw0Q8zTn8ZkiCQsKu8boXbZImy3UtM3L1eYmNGqvltMIdbwo6uL460fVL+AStuYw9Sw==;";
$blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);


$itemid = $_REQUEST["itemid"];
$item=$db->get_row("SELECT itemid, meetings.meetingid, title, meetings.bodyid, body, meetingdate FROM events.agendas LEFT JOIN events.meetings USING(meetingid) LEFT JOIN clacksweb2.bodies USING(bodyid) WHERE itemid='$itemid'");
// Should we output a table of files?
$print_table = 0;
function show_agendafile_form($values=array(), $errors=array()) {
	global $db;
	echo '<form action="agendafiles.php" method="POST" name="agendafilefrm" enctype="multipart/form-data">';
	echo '<table class="table table-striped table-bordered">';
	echo '<tr><td class="label">File: </td><td><input type="file" name="agendafile">';
	if ($errors["agendafile"]) echo '<span class="error">&nbsp;' . $errors["agendafile"] . '</span></td></tr>';
	echo '<tr><td class="label">File type: </td><td>';
	$filetypes = $db->get_results("SELECT typeid, type FROM events.meetingfiletypes ORDER BY type", ARRAY_N);
	build_select_box($filetypes, "typeid", $values["typeid"]);
	echo '</td></tr>';
	echo '<tr><td class="label">Minute for meeting: </td><td>';
	$meetings = $db->get_results("SELECT meetingid, CONCAT(meetingdate, ' - ', body) FROM events.meetings LEFT JOIN clacksweb2.bodies USING(bodyid) ORDER BY meetingdate DESC LIMIT 100", ARRAY_N);
	build_select_box($meetings, "minutemeetingid", $values["minutemeetingid"], "--");
	echo '</td></tr>';
	echo '<tr><td class="label">Title: </td><td><input type="text" maxlength="250" size="50" name="title" value="' . $values["title"] . '" />';
	echo '</td></tr>';
	if ($values["itemid"]) echo '<input type="hidden" name="itemid" value="' . $values["itemid"] . '">';
	if ($values["agendafileid"]) echo '<input type="hidden" name="agendafileid" value="' . $values["agendafileid"] . '">';
	echo '</td></tr>';
	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="submit" value="Save File">&nbsp;<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onClick="history.go(-1);"></p>';
	echo '</form>';
}
function confirm_agendafile_deletion($agendafileid, $itemid) {
	global $db;
	$del=$db->get_row("SELECT type, filename, ext, title FROM events.agendafiles LEFT JOIN events.meetingfiletypes USING(typeid) WHERE agendafileid=$agendafileid");
	echo 'Are you sure you want to delete file ' . $del->title . '?<p><strong>NB: This file will be removed from the system completely, and all links to meetings removed.</strong></p>';
	echo '<form action="agendafiles.php" method="post">';
	echo '<input type="hidden" name="del" value="' . $agendafileid . '">';
	echo '<input type="hidden" name="itemid" value="' . $itemid . '">';
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
<h3>Agenda Item Files</h3>
<?php
switch ($_REQUEST["action"]) {
	case "add":
		echo "<p>Add File to item: <strong>$item->title ($item->body, $item->meetingdate)</strong></p>";
		break;
	case "edit":
		echo "<p>Edit File for item: <strong>$item->title ($item->body, $item->meetingdate)</strong></p>";
		break;
	case "delete":
		echo "<p>Delete File from item: <strong>$item->title ($item->body, $item->meetingdate)</strong></p>";
		break;
	default:
		echo '<p>Manage files for Item: <strong>' . $item->title  . '</strong> <a href="agendafiles.php?action=add&itemid=' . $item->itemid . '"><img src="images/add.gif" alt="Add new file" border="0" /></a></p>';
		break;
}
if ($_POST["confdel"]) {
	$delagendafile=$db->get_row("SELECT filename, title, ext FROM events.agendafiles WHERE agendafileid='$_POST[del]'");
	// delete physical file if required
	
	
	
	$deletedFileName = "meeting/" . $item->bodyid . "/" . $item->meetingid . "/" . $_POST["del"] . $delagendafile->ext;

	echo "Deleted File Name: " . $deletedFileName;

    // Delete the file from Azure Blob Storage
    try {
        $blobRestProxy->deleteBlob("document", $deletedFileName);
        
        echo 'File deleted successfully from Azure Blob Storage.';
    } catch (ServiceException $e) {
        echo 'Error deleting file from Azure Blob Storage: ' . $e->getCode() . ': ' . $e->getMessage();
    }	

	$db->query("DELETE FROM events.agendafiles WHERE agendafileid='$_POST[del]'");
	echo "<p>File '$delagendafile->title' deleted.</p>";
	$print_table = 1;
	
	
	
	
	
	
} else if ($_POST["submit"]) {
	$errors=array();
	if ((!$_POST["agendafileid"])&&($_FILES['agendafile']['name'] == "")) $errors["agendafile"] = "No file selected for upload";
	if (count($errors) == 0) {
		$print_table = 1;
		if ($_POST["agendafileid"]) {
			if ($_FILES["agendafile"]["name"] > "") {
				$uploaddir = "meeting/" . $item->bodyid . "/" . $item->meetingid . "/";
				$ext = substr($_FILES['agendafile']['name'], -4);
				$mime = $_FILES['agendafile']['type'];
				$filename = strtolower(strtr($_FILES['agendafile']['name'], " ?*#$%^:+=<>/\"", "______________"));
				$destination_filename = $_POST["agendafileid"] . $ext;		

		// Update agendafile record
		$minutemeetingid = $_POST['minutemeetingid'] ?? ''; // Set default value if not set		
		if ($minutemeetingid === null || $minutemeetingid === '') {
		    $minutemeetingid = '0'; // Replace null or empty string with '0'
		}
		$typeid = $_POST['typeid'] ?? ''; // Set default value if not set		
		if ($typeid === null || $typeid === '') {
		    $typeid = '0'; // Replace null or empty string with '0'
		}
		$db->query("UPDATE events.agendafiles SET filename='$filename', ext='$ext', mime='$mime', typeid='$typeid', minutemeetingid='$minutemeetingid', title='$_POST[title]', modified=UNIX_TIMESTAMP() WHERE agendafileid='$_POST[agendafileid]'");
			} else {
				$db->query("UPDATE events.agendafiles SET title='$_POST[title]', typeid='$typeid', minutemeetingid='$minutemeetingid', modified=UNIX_TIMESTAMP() WHERE agendafileid='$_POST[agendafileid]'");
			}
			echo "<p>Item file updated</p>";
		} else {
			// Set upload dir, get extension and original filename
			if ($_FILES['agendafile']['name']) {
				$uploaddir = "meeting/" . $item->bodyid . "/" . $item->meetingid . "/";
				$ext = substr($_FILES['agendafile']['name'], -4);
				$mime = $_FILES['agendafile']['type'];
				$filename = strtolower(strtr($_FILES['agendafile']['name'], " ?*#$%^:+=<>/\"", "______________"));
			}
			
			
		// Insert new agendafile record
		$minutemeetingid = $_POST['minutemeetingid'] ?? ''; // Set default value if not set		
		if ($minutemeetingid === null || $minutemeetingid === '') {
		    $minutemeetingid = '0'; // Replace null or empty string with '0'
		}
		$typeid = $_POST['typeid'] ?? ''; // Set default value if not set		
		if ($typeid === null || $typeid === '') {
		    $typeid = '0'; // Replace null or empty string with '0'
		}
		$db->query("INSERT INTO events.agendafiles (itemid, filename, ext, mime, typeid, minutemeetingid, title, modified, created) VALUES('$_POST[itemid]', '$filename', '$ext', '$mime', '$typeid', '$minutemeetingid', '$_POST[title]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			
			
			
			
$newagendafileid=$db->insert_id;
if ($_FILES['agendafile']['name']) {
    // Get file contents
    $fileContent = fopen($_FILES['agendafile']['tmp_name'], "r");

    // Define the directory path within the container
    $directoryPath = $uploaddir;
    //$directoryPath = "meeting/test/test/";  

    // Define the name of the blob (file) in Azure Blob Storage including the directory path
    $blobName = $directoryPath . $newagendafileid . $ext;  
    try {
        // Upload the file to Azure Blob Storage
        $blobRestProxy->createBlockBlob("document", $blobName, $fileContent);
        fclose($fileContent);

        echo "<p>Added file to agenda item</p>";
    } catch (ServiceException $e) {
        echo "Error uploading file to Azure Blob Storage: " . $e->getMessage();
    }    
}

		}
	} else {
		show_agendafile_form($_POST, $errors);
	}
	
} else if ($_GET["action"]) {
	switch ($_GET["action"]) {
		case "add":
			// set values for addition
			$values = array();
			$values["itemid"] = $itemid;
			$values["bodyid"] = $item->bodyid;
			show_agendafile_form($values);
			break;
		case "edit":
			// set values for edit
			$agendafile=$db->get_row("SELECT * FROM events.agendafiles WHERE agendafileid='$_GET[agendafileid]'");
			$values = array();
			$values["itemid"] = $itemid;
			$values["bodyid"] = $item->bodyid;
			$values["agendafileid"] = $_GET["agendafileid"];
			$values["typeid"] = $agendafile->typeid;
			$values["minutemeetingid"] = $agendafile->minutemeetingid;
			$values["title"] = $agendafile->title;
			show_agendafile_form($values);
			break;
		case "delete":
			confirm_agendafile_deletion($_GET["agendafileid"], $itemid);
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
if (($itemfiles = $db->get_results("SELECT agendafiles.*, meetingfiletypes.type, meetings.meetingid, bodyid FROM events.agendas LEFT JOIN events.agendafiles USING(itemid) LEFT JOIN events.meetingfiletypes USING(typeid), events.meetings WHERE meetings.meetingid=agendas.meetingid AND agendafiles.itemid='$itemid'"))&&($print_table == 1)) {
	echo '<table width="100%">';
	foreach ($itemfiles as $itemfile) {
		echo '<tr><td>';
		echo '<img src="images/';
		switch ($itemfile->mime) {
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
		echo '" width="20" height="16" alt="' . $itemfile->mime . '"></td>';
		echo '<td class="detail">';
		echo '<a target="_blank" href="/document/meeting/' . $itemfile->bodyid . '/' . $itemfile->meetingid . '/' . $itemfile->agendafileid . $itemfile->ext . '">' . $itemfile->filename . '</a>';
		echo '</td>';
		echo '<td class="detail">' . $itemfile->type . '</td>';
		echo '<td class="detail">' . $itemfile->title . '</td>';
		echo '<td width="100" align="right">';
		echo '<a href="agendafiles.php?action=edit&agendafileid=' . $itemfile->agendafileid . '&itemid=' . $item->itemid . '"><img src="images/edit.gif" alt="Edit File" border="0"></a> ';
		echo '<a href="agendafiles.php?action=delete&agendafileid=' . $itemfile->agendafileid . '&itemid=' . $item->itemid . '"><img src="images/delete.gif" alt="Delete File" border="0"></a>';
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