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

$troid = $_REQUEST["troid"];
$tro=$db->get_var("SELECT title FROM tros WHERE troid='$troid'");
// Should we output a table of files?
$print_table = 0;
function show_trofile_form($values=array(), $errors=array()) {
	global $db;
	echo '<form action="trofiles.php" method="POST" name="trofilefrm" enctype="multipart/form-data">';
	echo '<table class="table table-striped table-bordered">';
	echo '<tr><td class="label">File: </td><td><input type="file" name="trofile">';
	if ($errors["trofile"]) echo '<span class="error">&nbsp;' . $errors["trofile"] . '</span></td></tr>';
	echo '<tr><td class="label">Description: </td><td><textarea cols="80" rows="3" class="search" name="description">' . $values["description"] . '</textarea>';
	if ($errors["description"]) echo '<span class="error">&nbsp;' . $errors["description"] . '</span>';
	echo '</td></tr>';
	if ($values["troid"]) echo '<input type="hidden" name="troid" value="' . $values["troid"] . '">';
	if ($values["trofileid"]) echo '<input type="hidden" name="trofileid" value="' . $values["trofileid"] . '">';
	echo '</td></tr>';
	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" name="submit" value="Save File">&nbsp;<input type="submit" class="btn btn-md btn-secondary" name="cancel" value="Cancel" onClick="history.go(-1);"></p>';
	echo '</form>';
}
function confirm_trofile_deletion($trofileid, $troid) {
	global $db;
	$del=$db->get_row("SELECT description FROM trofiles WHERE trofileid=$trofileid");
	echo 'Are you sure you want to delete file \'' . $del->description . '\'?<p><strong>NB: This file will be removed from the system completely, and all links to orders removed.</strong></p>';
	echo '<form action="trofiles.php" method="post">';
	echo '<input type="hidden" name="del" value="' . $trofileid . '">';
	echo '<input type="hidden" name="troid" value="' . $troid . '">';
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
<h3>tro Files</h3>
<?php
switch ($_REQUEST["action"]) {
	case "add":
		echo "<p>Add File to TRO: <strong>$tro</strong></p>";
		break;
	case "edit":
		echo "<p>Edit File for TRO: <strong>$tro</strong></p>";
		break;
	case "delete":
		echo "<p>Delete File from TRO: <strong>$tro</strong></p>";
		break;
	default:
		echo '<p>Manage for files for TRO: <strong>' . $tro . '</strong> <a href="trofiles.php?action=add&troid=' . $troid . '"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new file</span></a></p>';
		break;
}
if ($_POST["confdel"]) {
	$deltrofile=$db->get_row("SELECT filename, description, ext FROM trofiles WHERE trofileid='$_POST[del]'");
    // Delete the file from Azure Blob Storage
    try {
        $blobRestProxy->deleteBlob("document", "tros/" . $_POST['del'] . $deltrofile->ext);
        echo 'File deleted successfully from Azure Blob Storage.';
    } catch (ServiceException $e) {
        echo 'Error deleting file from Azure Blob Storage: ' . $e->getCode() . ': ' . $e->getMessage();
    }
	
	$db->query("DELETE FROM trofiles WHERE trofileid='$_POST[del]'");
	//log_entry($_SESSION[userid],"Deleted tro file $deltrofile->description, key=$_POST[del]");
	echo "<p>File '$deltrofile->description' deleted.</p>";
	$print_table = 1;
} else if ($_POST["submit"]) {
	$errors=array();
	if (!$_POST["description"]) $errors["description"] = "No description entered for file";
	if ((!$_POST["trofileid"])&&($_FILES['trofile']['name'] == "")&&(!$_POST["url"])) $errors["trofile"] = "No file selected for upload";
	
	
	
	if (count($errors) == 0) {
		$print_table = 1;
		if ($_POST["trofileid"]) {
			if ($_FILES["trofile"]["name"] > "") {


	if ($_FILES["trofile"]["name"] > "") {
					$ext = substr($_FILES['trofile']['name'], -4);
					$mime = $_FILES['trofile']['type'];
					$filename = strtolower(strtr($_FILES['trofile']['name'], " ?*#$%^:+=<>/\"", "______________"));
					$newtrofileid =  $_POST["trofileid"];					
					$description = addslashes($_POST["description"]);

					//Upload to Azure
					if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['trofile'])) 
					{
					    $file = $_FILES['trofile'];

					    // Check for errors
					    if ($file['error'] !== UPLOAD_ERR_OK) {
						echo 'Error uploading file. Please try again.';
						exit;
					    }
					   echo "------------IN";
					    // Get file name and content
					    $nfn = "tros/" . $newtrofileid . $ext;
					    $fileContent = fopen($file['tmp_name'], 'r');
			
					   echo "OUT------------" . $nfn;

					    try {
						// Upload blob to the "images" container
						$blobRestProxy->createBlockBlob("document", $nfn, $fileContent);
						echo 'File uploaded successfully.';
					    } catch (ServiceException $e) {
						echo 'Error uploading file: ' . $e->getCode() . ': ' . $e->getMessage();
					    } finally {
						fclose($fileContent);
					    }
					}
	}				








				$db->query("UPDATE trofiles SET filename='$filename', ext='$ext', mime='$mime', description='$_POST[description]', modified=UNIX_TIMESTAMP() WHERE trofileid='$_POST[trofileid]'");
			} else {
				$db->query("UPDATE trofiles SET description='$_POST[description]', modified=UNIX_TIMESTAMP() WHERE trofileid='$_POST[trofileid]'");
			}
			echo "<p>tro file '$_POST[description]' updated</p>";
		} else {
			// Set upload dir, get extension and original filename
			if ($_FILES['trofile']['name']) {
				$ext = substr($_FILES['trofile']['name'], -4);
				$mime = $_FILES['trofile']['type'];
				$filename = strtolower(strtr($_FILES['trofile']['name'], " ?*#$%^:+=<>/\"", "______________"));
			}
			// Insert new docfile record
			$db->query("INSERT INTO trofiles (troid, filename, ext, mime, description, modified, created)
						VALUES('$_POST[troid]', '$filename', '$ext', '$mime', '$_POST[description]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			$newtrofileid=$db->insert_id;
			
			//Upload to Azure
			if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['trofile'])) {
			    $file = $_FILES['trofile'];
			
			    // Check for errors
			    if ($file['error'] !== UPLOAD_ERR_OK) {
			        echo 'Error uploading file. Please try again.';
			        exit;
			    }
			
			    // Get file name and content
			    $fileName = "tros/" . $newtrofileid . $ext;
			    $fileContent = fopen($file['tmp_name'], 'r');
			
			echo "------------" . $fileName;
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
			// Log file upload
			//log_entry($_SESSION["userid"],"Added tro file $_POST[description], key $newtrofileid");
			echo "<p>Added file '$_POST[description]'</p>";
		}
	} else {
		show_trofile_form($_POST, $errors);
	}
} else if ($_GET["action"]) {
	switch ($_GET["action"]) {
		case "add":
			// set values for addition
			$values = array();
			$values["troid"] = $troid;
			show_trofile_form($values);
			break;
		case "edit":
			// set values for edit
			$trofile=$db->get_row("SELECT * FROM trofiles WHERE trofileid='$_GET[trofileid]'");
			$values = array();
			$values["troid"] = $troid;
			$values["trofileid"] = $_GET["trofileid"];
			$values["description"] = $trofile->description;
			show_trofile_form($values);
			break;
		case "delete":
			confirm_trofile_deletion($_GET["trofileid"], $troid);
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
if (($trofiles = $db->get_results("SELECT * FROM trofiles WHERE troid='$troid'"))&&($print_table == 1)) {
	echo '<table width="100%" class="table table-striped table-bordered">';
	foreach ($trofiles as $trofile) {
		echo '<tr><td>';
		echo '<img src="images/';
		switch ($trofile->mime) {
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
		echo '" width="20" height="16" alt="' . $trofile->mime . '"></td>';
		echo '<td class="detail">';
		echo '<a target="_blank" href="/document/tros/' . $trofile->trofileid . $trofile->ext . '">' . $trofile->filename . '</a>';
		echo '</td>';
		echo '<td class="detail">' . $trofile->description . '</td>';
		echo '<td width="100" align="right">';
		echo '<a href="trofiles.php?action=edit&trofileid=' . $trofile->trofileid . '&troid=' . $troid . '"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">Edit File</span></a> ';
		echo '<a href="trofiles.php?action=delete&trofileid=' . $trofile->trofileid . '&troid=' . $troid . '"><svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">Delete File</span></a>';
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