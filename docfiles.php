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
//

if (($_GET["fromid"])&&($_GET["fromso"])&&($_GET["toid"])&&($_GET["toso"])) {
	$db->query("UPDATE docfiles SET sortorder='$_GET[toso]' WHERE docfileid='$_GET[fromid]'");
	$db->query("UPDATE docfiles SET sortorder='$_GET[fromso]' WHERE docfileid='$_GET[toid]'");
}
$docid = $_REQUEST["docid"];
$doc=$db->get_var("SELECT name FROM docs WHERE docid='$docid'");
// Should we output a table of files?
$print_table = 0;
function show_docfile_form($values=array(), $errors=array()) {
	global $db;
	echo '<form action="docfiles.php" method="POST" name="frmfilefrm" enctype="multipart/form-data" onsubmit="document.frmfilefrm.submitbtn.value=\'Please wait\';document.frmfilefrm.submitbtn.disabled=true;">';
	echo '<table class="table table-striped table-bordered">';
//	echo '<tr><td class="label">File: </td><td><input type="file" name="docfile" onchange="document.frmfilefrm.html.disabled=false;document.frmfilefrm.text.disabled=false;">';
	echo '<tr><td class="label">File: </td><td><input type="file" name="docfile" />';
	if ($errors["docfile"]) echo '<span class="error">&nbsp;' . $errors["docfile"] . '</span>';
	echo ' or:</td></tr>';
	echo '<tr><td class="label">URL: </td><td><input type="text" name="url" value="' . $values["url"] . '" style="width: 400px;" />';
	if ($errors["url"]) echo '<span class="error">&nbsp;' . $errors["url"] . '</span>';
	echo '</td></tr>';
/*
	echo '<tr><td class="label">Conversions<br />(PDF only): </td><td>HTML: ';
	echo '<select name="html"><option value="0"';
	if (!$values["html"]) echo ' selected';
	echo '>No</option>';
	echo '<option value="1"';
	if ($values["html"]) echo ' selected';
	echo '>Yes</option></select>';
	echo ' Text: <select name="text"><option value="0"';
	if (!$values["text"]) echo ' selected';
	echo '>No</option>';
	echo '<option value="1"';
	if ($values["text"]) echo ' selected';
	echo '>Yes</option></select>';
	echo '</td></tr>';
*/
	echo '<tr><td class="label">Description: </td><td><textarea cols="50" rows="3" class="search" name="description">' . $values["description"] . '</textarea>';
	if ($errors["description"]) echo '<span class="error">&nbsp;' . $errors["description"] . '</span>';
	echo '</td></tr>';
	echo '<tr><td class="label">Internal Only? </td><td><select name="internal">';
	echo '<option value="0"';
	if (!$values["internal"]) echo ' selected';
	echo '>No</option>';
	echo '<option value="1"';
	if ($values["internal"]) echo ' selected';
	echo '>Yes</option>';
	echo '</td></tr>';


	if ($values["docid"]) echo '<input type="hidden" name="docid" value="' . $values["docid"] . '">';
	if ($values["docfileid"]) echo '<input type="hidden" name="docfileid" value="' . $values["docfileid"] . '">';
	echo '<input type="hidden" name="submitted" value="1">';
	echo '</td></tr>';
	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" name="submitbtn" value="Save File" />&nbsp;<input type="submit" class="btn btn-md btn-secondary" name="cancel" value="Cancel" onClick="history.go(-1);" /></p>';
	echo '</form>';
}
function confirm_docfile_deletion($docfileid, $docid) {
	global $db;
	$del=$db->get_row("SELECT description FROM docfiles WHERE docfileid=$docfileid");
	echo 'Are you sure you want to delete file \'' . $del->description . '\'?<p><strong>NB: This file will be removed from the system completely, and all links to documents removed.</strong></p>';
	echo '<form action="docfiles.php" method="post">';
	echo '<input type="hidden" name="del" value="' . $docfileid . '">';
	echo '<input type="hidden" name="docid" value="' . $docid . '">';
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
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="calendar-win2k-cold-1.css" title="win2k-cold-1" />
<!-- main calendar program -->
<script type="text/javascript" src="js/calendar.js"></script>
<!-- language for the calendar -->
<script type="text/javascript" src="js/calendar-en.js"></script>
<!-- the following script defines the Calendar.setup helper function, which makes adding a calendar a matter of 1 or 2 lines of code. -->
<script type="text/javascript" src="js/calendar-setup.js"></script>
<div id="formarea">
<h3>Document Files</h3>
<?php
switch ($_REQUEST["action"]) {
	case "add":
		echo "<p>Add File to Document: <strong>$doc</strong></p>";
		break;
	case "edit":
		echo "<p>Edit File for Document: <strong>$doc</strong></p>";
		break;
	case "delete":
		echo "<p>Delete File from Document: <strong>$doc</strong></p>";
		break;
	default:
		echo '<p>Manage for files for Document: <strong>' . $doc . '</strong> <a href="docfiles.php?action=add&docid=' . $docid . '"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new file</span></a></p>';
		break;
}

if ($_POST["confdel"]) {
    $deldocfile = $db->get_row("SELECT filename, description, ext FROM docfiles WHERE docfileid='$_POST[del]'");
    // Delete the file from Azure Blob Storage
    try {
        $blobRestProxy->deleteBlob("document", $_POST['del'] . $deldocfile->ext);
        echo 'File deleted successfully from Azure Blob Storage.';
    } catch (ServiceException $e) {
        echo 'Error deleting file from Azure Blob Storage: ' . $e->getCode() . ': ' . $e->getMessage();
    }
    
    // Delete record from the database
    $db->query("DELETE FROM docfiles WHERE docfileid='$_POST[del]'");
    
    log_entry($_SESSION["userid"], "Deleted doc file $deldocfile->description, key=$_POST[del]");
    echo "<p>File '$deldocfile->description' deleted.</p>";
    $print_table = 1;
} 

else if ($_POST["submitted"]) {

	$errors=array();
	if (!$_POST["description"]) $errors["description"] = "No description entered for file";
	if ((!$_POST["docfileid"])&&($_FILES['docfile']['name'] == "")&&(!$_POST["url"])) $errors["docfile"] = "No file selected for upload or URL entered";
	// if no review date specified make it 3 months from today
	if (count($errors) == 0) {
		$print_table = 1;

		if ($_POST["docfileid"]) {
			// Updating existing file
				if ($_FILES["docfile"]["name"] > "") {
					$ext = strtolower(strrchr($_FILES['docfile']['name'], '.'));
					$mime = $_FILES['docfile']['type'];
					$filename = strtolower(strtr($_FILES['docfile']['name'], " ?*#$%^:+=<>/\"", "______________"));
					$newdocfileid =  $_POST["docfileid"];	
					$description = addslashes($_POST["description"]);
				
				//Upload to Azure
				if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['docfile'])) {
				    $file = $_FILES['docfile'];
				
				    // Check for errors
				    if ($file['error'] !== UPLOAD_ERR_OK) {
				        echo 'Error uploading file. Please try again.';
				        exit;
				    }
				
					
				    // Get file name and content
				    $newFileName = $newdocfileid . $ext;
				    $fileContent = fopen($file['tmp_name'], 'r');
	
				
				    try {
				        // Upload blob to the "images" container
				        $blobRestProxy->createBlockBlob("document", $newFileName, $fileContent);
				        echo 'File uploaded successfully.';
				    } catch (ServiceException $e) {
				        echo 'Error uploading file: ' . $e->getCode() . ': ' . $e->getMessage();
				    } finally {
				        fclose($fileContent);
				    }
				}			
				
			$db->query("UPDATE docfiles SET filename='$filename', ext='$ext', mime='$mime', url='$url', description='$description', internal='$_POST[internal]', modified=UNIX_TIMESTAMP() WHERE docfileid='$_POST[docfileid]'");
				} else {
					$description = addslashes($_POST["description"]);
					$db->query("UPDATE docfiles SET url='$_POST[url]', description='$description', internal='$_POST[internal]', modified=UNIX_TIMESTAMP() WHERE docfileid='$_POST[docfileid]'");
				}
				echo "<p>Document file '$description' updated</p>";
			}





 else {

			// Set upload dir, get extension and original filename
			if ($_FILES['docfile']['name']) {
				$ext = strtolower(strrchr($_FILES['docfile']['name'], '.'));
				$mime = $_FILES['docfile']['type'];
				$filename = strtolower(strtr($_FILES['docfile']['name'], " ?*#$%^:+=<>/\"", "______________"));
			}
			// Insert new docfile record
			$description = addslashes($_POST["description"]);
			$db->query("INSERT INTO docfiles (docid, filename, ext, mime, url, description, internal, modified, created)
						VALUES('$_POST[docid]', '$filename', '$ext', '$mime', '$_POST[url]', '$description','$_POST[internal]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			$newdocfileid=$db->insert_id;
			
			//Upload to Azure
			if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['docfile'])) {
			    $file = $_FILES['docfile'];
			
			    // Check for errors
			    if ($file['error'] !== UPLOAD_ERR_OK) {
			        echo 'Error uploading file. Please try again.';
			        exit;
			    }
			
			    // Get file name and content
			    $fileName = $newdocfileid . $ext;
			    $fileContent = fopen($file['tmp_name'], 'r');
			
			    try {
			        // Upload blob to the "images" container
			        $blobRestProxy->createBlockBlob("document", $fileName, $fileContent);
			        echo 'File uploaded successfully.';
			    } catch (ServiceException $e) {
			        echo 'Error uploading file: ' . $e->getCode() . ': ' . $e->getMessage();
			    } finally {
			        fclose($fileContent);
			    }
			}
			
			$db->query("UPDATE docfiles SET sortorder='$newdocfileid' WHERE docfileid='$newdocfileid'");
			if ($_FILES['docfile']['name']) {

			}
			// Log file upload
			log_entry($_SESSION["userid"],"Added Document file $description, key $newdocfileid");
			echo "<p>Added file '$description'</p>";
		}
	} else {
		show_docfile_form($_POST, $errors);
	}
} else if ($_GET["action"]) {
	switch ($_GET["action"]) {
		case "add":
			// set values for addition
			$values = array();
			$values["docid"] = $docid;
			show_docfile_form($values);
			break;
		case "edit":
			// set values for edit
			$docfile=$db->get_row("SELECT * FROM docfiles WHERE docfiles.docfileid='$_GET[docfileid]'");
			$values = array();
			$values["docid"] = $docid;
			$values["docfileid"] = $_GET["docfileid"];
			$values["url"] = $docfile->url;
			$values["description"] = $docfile->description;
			$values["internal"] = $docfile->internal;
			show_docfile_form($values);
			break;
		case "delete":
			confirm_docfile_deletion($_GET["docfileid"], $docid);
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
if (($docfiles = $db->get_results("SELECT * FROM docfiles WHERE docid='$docid' ORDER BY sortorder DESC"))&&($print_table == 1)) {
	echo '<table class="table table-striped table-bordered" width="100%">';
	for ($x=0; $x < count($docfiles); $x++) {
		echo '<tr><td>';
		echo '<img src="images/';
		switch ($docfiles[$x]->mime) {
			case "application/msword":
				echo 'msword.gif';
				$mimetype="MS Word";
				break;
			case "application/vnd.ms-excel":
				echo 'excel.gif';
				$mimetype="MS Excel";
				break;
			case "application/vnd.openxmlformats-officedocument.wordprocessingml.document":
				echo 'msword.gif';
				$mimetype="MS Word";
				break;
			case "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet":
				echo 'excel.gif';
				$mimetype="MS Excel";
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
		echo '" alt="' . $docfiles[$x]->mime . '"></td>';
		echo '<td class="detail">';
		if ($docfiles[$x]->ext) {
			echo '<a target="_blank" href="download.php?ft=document&file=' . $docfiles[$x]->docfileid . $docfiles[$x]->ext . '">' . $docfiles[$x]->filename . '</a>';
		} else {
			echo '<a target="_blank" href="' . $docfiles[$x]->url . '">' . $docfiles[$x]->url . '</a>';
		}
		echo '</td>';
		echo '<td class="detail">' . $docfiles[$x]->description . '</td>';
		echo '<td width="140" align="right">';
		echo '<a href="docfiles.php?action=edit&docfileid=' . $docfiles[$x]->docfileid . '&docid=' . $docid . '"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">Edit File</span></a> ';
		echo '<a href="docfiles.php?action=delete&docfileid=' . $docfiles[$x]->docfileid . '&docid=' . $docid . '"><svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">Delete File</span></a>';
		if ($x > 0) {
			echo ' <a href="docfiles.php?docid=' . $docid . '&fromid=' . $docfiles[$x]->docfileid . '&fromso=' . $docfiles[$x]->sortorder . '&toid=' . $docfiles[$x-1]->docfileid . '&toso=' . $docfiles[$x-1]->sortorder . '"><svg class="icon icon-up" aria-hidden="true" focusable="false"><use href="images/icons.svg#arrow-up"></use></svg><span class="sr-only">Move file up</span></a>';
		} else {
			echo ' <svg class="icon icon-up is-off" aria-hidden="true" focusable="false"><use href="images/icons.svg#arrow-up"></use></svg><span class="sr-only">Top file</span> ';
		}
		if ($x < (count($docfiles)-1)) {
			echo ' <a href="docfiles.php?docid=' . $docid . '&fromid=' . $docfiles[$x]->docfileid . '&fromso=' . $docfiles[$x]->sortorder . '&toid=' . $docfiles[$x+1]->docfileid . '&toso=' . $docfiles[$x+1]->sortorder . '"><svg class="icon icon-down" aria-hidden="true" focusable="false"><use href="images/icons.svg#arrow-down"></use></svg><span class="sr-only">Move file down</span></a>';
		} else {
			echo ' <svg class="icon icon-down is-off" aria-hidden="true" focusable="false"><use href="images/icons.svg#arrow-down"></use></svg><span class="sr-only">Bottom file</span> ';
		}
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>';
}
if ($print_table == 1) 	echo '<p class="formbtns"><input class="btn btn-md btn-secondary" type="submit" value="Close" onclick="self.close();"></p>';
?>
</div>
</body>
</html>