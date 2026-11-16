<?php
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
	$db->query("UPDATE formfiles SET sortorder='$_GET[toso]' WHERE formfileid='$_GET[fromid]'");
	$db->query("UPDATE formfiles SET sortorder='$_GET[fromso]' WHERE formfileid='$_GET[toid]'");
}
$formid = $_REQUEST["formid"];
$form=$db->get_var("SELECT name FROM forms WHERE formid='$formid'");
// Should we output a table of files?
$print_table = 0;
function show_formfile_form($values=array(), $errors=array()) {
	global $db;
	echo '<form action="formfiles.php" method="POST" name="frmfilefrm" enctype="multipart/form-data">';
	echo '<table class="table table-striped table-bordered">';
	

	echo '<tr><td class="label">File: </td><td><input type="file" name="formfile">';
	if ($errors["formfile"]) echo '<span class="error">&nbsp;' . $errors["formfile"] . '</span>';
	echo ' or:</td></tr>';
	


	echo '<tr><td class="label">URL: </td><td><input type="text" name="url" value="' . $values["url"] . '" style="width: 400px;" />';
	if ($errors["url"]) echo '<span class="error">&nbsp;' . $errors["url"] . '</span>';
	echo '</td></tr>';
	echo '<tr><td class="label">Description: </td><td><textarea cols="80" rows="3" class="search" name="description">' . $values["description"] . '</textarea>';
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
	if ($values["formid"]) echo '<input type="hidden" name="formid" value="' . $values["formid"] . '">';
	if ($values["formfileid"]) echo '<input type="hidden" name="formfileid" value="' . $values["formfileid"] . '">';
	echo '</td></tr>';
	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" name="submit" value="Save File">&nbsp;<input type="submit" class="btn btn-md btn-secondary" name="cancel" value="Cancel" onClick="history.go(-1);"></p>';
	echo '</form>';
}
function confirm_formfile_deletion($formfileid, $formid) {
	global $db;
	$del=$db->get_row("SELECT description FROM formfiles WHERE formfileid=$formfileid");
	echo 'Are you sure you want to delete file \'' . $del->description . '\'?<p><strong>NB: This file will be removed from the system completely, and all links to forms removed.</strong></p>';
	echo '<form action="formfiles.php" method="post">';
	echo '<input type="hidden" name="del" value="' . $formfileid . '">';
	echo '<input type="hidden" name="formid" value="' . $formid . '">';
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
<h3>Form Files</h3>
<?php
switch ($_REQUEST["action"]) {
	case "add":
		echo "<p>Add File to Form: <strong>$form</strong></p>";
		break;
	case "edit":
		echo "<p>Edit File for Form: <strong>$form</strong></p>";
		break;
	case "delete":
		echo "<p>Delete File from Form: <strong>$form</strong></p>";
		break;
	default:
		echo '<p>Manage for files for Form: <strong>' . $form . '</strong> <a href="formfiles.php?action=add&formid=' . $formid . '"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new file</span></a></p>';
		break;
}

if ($_POST["confdel"]) {
    $delformfile = $db->get_row("SELECT filename, description, ext FROM formfiles WHERE formfileid='$_POST[del]'");
    // Delete the file from Azure Blob Storage
    try {
        $blobRestProxy->deleteBlob("form", $_POST['del'] . $delformfile->ext);
        echo 'File deleted successfully from Azure Blob Storage.';
    } catch (ServiceException $e) {
        echo 'Error deleting file from Azure Blob Storage: ' . $e->getCode() . ': ' . $e->getMessage();
    }
    
    // Delete record from the database
    $db->query("DELETE FROM formfiles WHERE formfileid='$_POST[del]'");
    
    log_entry($_SESSION["userid"], "Deleted form file $delformfile->description, key=$_POST[del]");
    echo "<p>Form '$delformfile->description' deleted.</p>";
    $print_table = 1;
} 

else if ($_POST["submit"]) {
	$errors=array();
	if (!$_POST["description"]) $errors["description"] = "No description entered for file";
	if ((!$_POST["formfileid"])&&($_FILES['formfile']['name'] == "")&&(!$_POST["url"])) $errors["formfile"] = "No file selected for upload or URL entered";
	// if no review date specified make it 3 months from today
	if (count($errors) == 0) {
		$print_table = 1;

	if ($_POST["formfileid"]) {
		// Updating existing file
			if ($_FILES["formfile"]["name"] > "") {
				$ext = substr($_FILES['formfile']['name'], -4);
				if ($ext == "xlsx") {
					$ext = ".xlsx";
				}
				$mime = $_FILES['formfile']['type'];
				$filename = strtolower(strtr($_FILES['formfile']['name'], " ?*#$%^:+=<>/\"", "______________"));
				$newformfileid =  $_POST["formfileid"];	
				$description = addslashes($_POST["description"]);
			
			//Upload to Azure
			if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['formfile'])) {
			    $file = $_FILES['formfile'];
			
			    // Check for errors
			    if ($file['error'] !== UPLOAD_ERR_OK) {
			        echo 'Error uploading file. Please try again.';
			        exit;
			    }
			
				
			    // Get file name and content
			    $newFileName = $newformfileid . $ext;
			    $fileContent = fopen($file['tmp_name'], 'r');
			
			    try {
			        // Upload blob to the "images" container
			        $blobRestProxy->createBlockBlob("form", $newFileName, $fileContent);
			        echo 'File uploaded successfully.';
			    } catch (ServiceException $e) {
			        echo 'Error uploading file: ' . $e->getCode() . ': ' . $e->getMessage();
			    } finally {
			        fclose($fileContent);
			    }
			}			
			
		$db->query("UPDATE formfiles SET filename='$filename', ext='$ext', mime='$mime', url='$url', description='$description', internal='$_POST[internal]', modified=UNIX_TIMESTAMP() WHERE formfileid='$_POST[formfileid]'");
			} else {
				$description = addslashes($_POST["description"]);
				$db->query("UPDATE formfiles SET url='$_POST[url]', description='$description', internal='$_POST[internal]', modified=UNIX_TIMESTAMP() WHERE formfileid='$_POST[formfileid]'");
			}
			echo "<p>Form file '$description' updated</p>";
		}






else {
			// Set upload dir, get extension and original filename
			if ($_FILES['formfile']['name']) {
				$uploaddir = UPLOAD_DIR . "/form/";
				$ext = substr($_FILES['formfile']['name'], -4);
				if ($ext == "xlsx") {
					$ext = ".xlsx";
				}
				$mime = $_FILES['formfile']['type'];
				$filename = strtolower(strtr($_FILES['formfile']['name'], " ?*#$%^:+=<>/\"", "______________"));
			}
			// Insert new formfile record
			$description = addslashes($_POST["description"]);			
			$db->query("INSERT INTO formfiles (formid, filename, ext, mime, url, description, internal, modified, created)
						VALUES('$_POST[formid]', '$filename', '$ext', '$mime', '$_POST[url]', '$_POST[description]','$_POST[internal]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			
			$newformfileid=$db->insert_id;
			
			//Upload to Azure
			if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['formfile'])) {
			    $file = $_FILES['formfile'];

			    // Check for errors
			    if ($file['error'] !== UPLOAD_ERR_OK) {
				echo 'Error uploading file. Please try again.';
				exit;
			    }

			    // Get file name and content
			    $fileName = $newformfileid . $ext;
			    $fileContent = fopen($file['tmp_name'], 'r');

			    try {
				// Upload blob to the "images" container
				$blobRestProxy->createBlockBlob("form", $fileName, $fileContent);
				echo 'File uploaded successfully.';
			    } catch (ServiceException $e) {
				echo 'Error uploading file: ' . $e->getCode() . ': ' . $e->getMessage();
			    } finally {
				fclose($fileContent);
			    }
			}
	
			$db->query("UPDATE formfiles SET sortorder='$newformfileid' WHERE formfileid='$newformfileid'");
			
			if ($_FILES['formfile']['name']) {
				// Move uploaded file with name = formfileid + extension
			
			}
			
			
			// Log file upload
			log_entry($_SESSION["userid"],"Added Form file $_POST[description], key $newformfileid");
			echo "<p>Added file '$_POST[description]'</p>";
		}
	} else {
		show_formfile_form($_POST, $errors);
	}
} else if ($_GET["action"]) {
	switch ($_GET["action"]) {
		case "add":
			// set values for addition
			$values = array();
			$values["formid"] = $formid;
			show_formfile_form($values);
			break;
		case "edit":
			// set values for edit
			$formfile=$db->get_row("SELECT * FROM formfiles WHERE formfiles.formfileid='$_GET[formfileid]'");
			$values = array();
			$values["formid"] = $formid;
			$values["formfileid"] = $_GET["formfileid"];
			$values["url"] = $formfile->url;
			$values["description"] = $formfile->description;
			$values["internal"] = $formfile->internal;
			show_formfile_form($values);
			break;
		case "delete":
			confirm_formfile_deletion($_GET["formfileid"], $formid);
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
if (($formfiles = $db->get_results("SELECT * FROM formfiles WHERE formid='$formid' ORDER BY sortorder DESC"))&&($print_table == 1)) {
	echo '<table width="100%" class="table table-striped table-bordered">';
	for ($x=0; $x < count($formfiles); $x++) {
		echo '<tr><td>';
		echo '<img src="images/';
		switch ($formfiles[$x]->mime) {
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
				echo 'form.gif';
				$mimetype="Text";
				break;
			default:
				echo 'form.gif';
				$mimetype="Other";
		}
		echo '" alt="' . $formfiles[$x]->mime . '"></td>';
		echo '<td class="detail">';
		if ($formfiles[$x]->ext) {
			echo '<a target="_blank" href="download.php?ft=form&file=' . $formfiles[$x]->formfileid . $formfiles[$x]->ext . '">' . $formfiles[$x]->filename . '</a>';
		} else {
			echo '<a target="_blank" href="' . $formfiles[$x]->url . '">' . $formfiles[$x]->url . '</a>';
		}
		echo '</td>';
		echo '<td class="detail"><a target="_blank" href="download.php?ft=form&file=' . $formfiles[$x]->formfileid . $formfiles[$x]->ext . '">' . $formfiles[$x]->description . '</a></td>';
		echo '<td width="140" align="right">';
		echo '<a href="formfiles.php?action=edit&formfileid=' . $formfiles[$x]->formfileid . '&formid=' . $formid . '"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">Edit File</span></a> ';
		echo '<a href="formfiles.php?action=delete&formfileid=' . $formfiles[$x]->formfileid . '&formid=' . $formid . '"><svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">Delete File</span></a>';
		if ($x > 0) {
			echo ' <a href="formfiles.php?formid=' . $formid . '&fromid=' . $formfiles[$x]->formfileid . '&fromso=' . $formfiles[$x]->sortorder . '&toid=' . $formfiles[$x-1]->formfileid . '&toso=' . $formfiles[$x-1]->sortorder . '"><svg class="icon icon-up" aria-hidden="true" focusable="false"><use href="images/icons.svg#arrow-up"></use></svg><span class="sr-only">Move file up</span></a>';
		} else {
			echo ' <svg class="icon icon-up is-off" aria-hidden="true" focusable="false"><use href="images/icons.svg#arrow-up"></use></svg><span class="sr-only">Top file</span> ';
		}
		if ($x < (count($formfiles)-1)) {
			echo ' <a href="formfiles.php?formid=' . $formid . '&fromid=' . $formfiles[$x]->formfileid . '&fromso=' . $formfiles[$x]->sortorder . '&toid=' . $formfiles[$x+1]->formfileid . '&toso=' . $formfiles[$x+1]->sortorder . '"><svg class="icon icon-down" aria-hidden="true" focusable="false"><use href="images/icons.svg#arrow-down"></use></svg><span class="sr-only">Move file down</span></a>';
		} else {
			echo ' <svg class="icon icon-down is-off" aria-hidden="true" focusable="false"><use href="images/icons.svg#arrow-down"></use></svg><span class="sr-only">Bottom file</span> ';
		}
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