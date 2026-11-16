<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
if (($_GET["fromid"])&&($_GET["fromso"])&&($_GET["toid"])&&($_GET["toso"])) {
	$db->query("UPDATE galleries.images SET sortorder='$_GET[toso]' WHERE imageid='$_GET[fromid]'");
	$db->query("UPDATE galleries.images SET sortorder='$_GET[fromso]' WHERE imageid='$_GET[toid]'");
}
$galleryid = $_REQUEST["galleryid"];
$gallery=$db->get_row("SELECT galleryid, name FROM galleries.galleries WHERE galleryid='$galleryid'");
// Should we output a table of images?
$print_table = 0;
function show_image_form($values=array(), $errors=array()) {
	global $db;
	echo <<<HTML
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="css/calendar-win2k-cold-1.css" title="win2k-cold-1" />
<!-- main calendar program -->
<script type="text/javascript" src="js/calendar.js"></script>
<!-- language for the calendar -->
<script type="text/javascript" src="js/calendar-en.js"></script>
<!-- the following script defines the Calendar.setup helper function, which makes adding a calendar a matter of 1 or 2 lines of code. -->
<script type="text/javascript" src="js/calendar-setup.js"></script>
HTML;
	echo '<form action="galleryimages.php" method="POST" name="galleryimagefrm" enctype="multipart/form-data"><input type="hidden" name="MAX_FILE_SIZE" value="2000000" />';
	echo '<table class="table table-striped table-bordered">';
	echo '<tr><td class="label">Image title: </td><td><input type="text" maxlength="100" size="40" name="title" value="' . $values["title"] . '" />';
	if ($errors["title"]) echo '<span class="error">&nbsp;' . $errors["title"] . '</span>';
	echo '</td></tr>';
	echo '<tr><td class="label">Image caption: </td><td><input type="text" maxlength="250" size="50" name="description" value="' . $values["description"] . '" />';
	if ($errors["description"]) echo '<span class="error">&nbsp;' . $errors["description"] . '</span>';
	echo '</td></tr>';
	echo <<<HTML
<tr><td class="label">Date taken:</td>
<td><input type="text" name="taken" value="$values[taken]" maxlength="10" style="width: 100px;" />
<button type="reset" id="f_trigger_a">...</button>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "taken",      // id of the input field
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        showsTime      :    false,            // will display a time selector
        button         :    "f_trigger_a",   // trigger for the calendar (button ID)
        singleClick    :    true,           // double-click mode
        step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    });
</script>
HTML;
	echo '<tr><td class="label">Image: </td><td><input type="file" name="imagefile">';
	if ($errors["imagefile"]) echo '<span class="error">&nbsp;' . $errors["imagefile"] . '</span>';
	if ($values["galleryid"]) echo '<input type="hidden" name="galleryid" value="' . $values["galleryid"] . '">';
	if ($values["imageid"]) echo '<input type="hidden" name="imageid" value="' . $values["imageid"] . '">';
	echo '</td></tr>';
	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="submit" value="Save image">&nbsp;<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onClick="history.go(-1);"></p>';
	echo '</form>';
}
function confirm_image_deletion($imageid, $galleryid) {
	global $db;
	$del=$db->get_row("SELECT title FROM galleries.images WHERE imageid=$imageid");
	echo 'Are you sure you want to delete image ' . $del->title . '?<p><strong>NB: This image will be removed from the system completely, and all links to galleries removed.</strong></p>';
	echo '<form action="galleryimages.php" method="post">';
	echo '<input type="hidden" name="del" value="' . $imageid . '">';
	echo '<input type="hidden" name="galleryid" value="' . $galleryid . '">';
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
<h3>Gallery images</h3>
<?php
switch ($_REQUEST["action"]) {
	case "add":
		echo "<p>Add image to gallery: <strong>$gallery->name</strong></p>";
		break;
	case "edit":
		echo "<p>Edit image in gallery: <strong>$gallery->name</strong></p>";
		break;
	case "delete":
		echo "<p>Delete image from gallery: <strong>$gallery->name</strong></p>";
		break;
	default:
		echo '<p>Manage images for gallery: <strong>' . $gallery->name  . '</strong> <a href="galleryimages.php?action=add&galleryid=' . $gallery->galleryid . '"><img src="images/add.gif" alt="Add new image" border="0" /></a></p>';
		break;
}
if ($_POST["confdel"]) {
	$delimagefile=$db->get_row("SELECT imageid, title FROM galleries.images WHERE imageid='$_POST[del]'");
	// delete physical file if required
	unlink(UPLOAD_DIR . "images/gallery/" . $_POST["del"] . ".jpg");
	unlink(UPLOAD_DIR . "images/gallery/tn" . $_POST["del"] . ".jpg");
	$db->query("DELETE FROM galleries.images WHERE imageid='$_POST[del]'");
	log_entry($_SESSION[userid],"Deleted gallery image $delimagefile->title, key=$_POST[del]");
	echo "<p>File '$delimagefile->title' deleted.</p>";
	$print_table = 1;
} else if ($_POST["submit"]) {
	$errors=array();
	if (!$_POST["title"]) $errors["title"] = "Please provide a title";
	if (!$_POST["description"]) $errors["description"] = "Please provide a brief description";
	if ((!$_POST["imageid"])&&($_FILES['imagefile']['name'] == "")) $errors["imagefile"] = "No image selected for upload";
	if ($_FILES['imagefile']['name'] > "") {
		if ($_FILES['imagefile']['error'] == 2) {
			$errors["imagefile"] = "File too large - limit is 500k";
		} else {
			if (($_FILES['imagefile']['type'] != "image/pjpeg") && ($_FILES['imagefile']['type'] != "image/x-citrix-pjpeg") && ($_FILES['imagefile']['type'] != "image/jpeg") && ($_FILES['imagefile']['type'] != "image/jpg")) $errors["imagefile"] = "Wrong image type (only jpg files can be used)" . $_FILES['imagefile']['type'];
		}
	}
	if (count($errors) == 0) {
		$_POST["title"] =  $db->escape($_POST["title"]);
		$_POST["description"] =  $db->escape($_POST["description"]);
		$print_table = 1;
		if ($_POST["imageid"]) {
			if ($_FILES["imagefile"]["name"] > "") {
				// move file to main image file
				$uploaddir = UPLOAD_DIR . "images/gallery/";
				$destination_filename = $uploaddir . $_POST["imageid"] . ".jpg";
				move_uploaded_file($_FILES['imagefile']['tmp_name'], $destination_filename);
				chmod($destination_filename, 0644);
				// process image transformations
				$imagesize = getimagesize($destination_filename);
				$src_width = $imagesize[0];
				$src_height = $imagesize[1];
				// resize main image if required to max 400 pixels in largest dimension
				if (($src_width > 400)||($src_height > 400)) {
					if ($src_width > $src_height) {
						$factor = $src_width / 400;
					} else {
						$factor = $src_height / 400;
					}
					$dest_width = $src_width / $factor;
					$dest_height = $src_height / $factor;
					$src_img = imagecreatefromjpeg($destination_filename);
					$dst_img = imagecreatetruecolor($dest_width, $dest_height);
					imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
					imagejpeg($dst_img, $destination_filename, 80);
					imagedestroy($src_img);
					imagedestroy($dst_img);
				}
				// produce thumbnail
				$src_filename = $destination_filename;
				$imagesize = getimagesize($src_filename);
				$src_width = $imagesize[0];
				$src_height = $imagesize[1];
				$destination_filename = $uploaddir . "tn" . $_POST["imageid"] . ".jpg";
				$factor = $src_height / 65;
				$dest_width = $src_width / $factor;
				$dest_height = $src_height / $factor;
				$src_img = imagecreatefromjpeg($src_filename);
				$dst_img = imagecreatetruecolor($dest_width, $dest_height);
				imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
				imagejpeg($dst_img, $destination_filename, 80);
				imagedestroy($src_img);
				imagedestroy($dst_img);
			}
			$_POST["title"] = stripslashes($_POST["title"]);
			$_POST["description"] = stripslashes($_POST["description"]);
			$db->query("UPDATE galleries.images SET title='$_POST[title]', description='$_POST[description]', taken='$_POST[taken]', modified=UNIX_TIMESTAMP() WHERE imageid='$_POST[imageid]'");
			log_entry($_SESSION["userid"],"Updated gallery image " .  $db->escape($_POST["name"]) . ", key $_POST[imageid]");
			echo "<p>Image updated</p>";
		} else {
			// Insert new image record
			$_POST["title"] = stripslashes($_POST["title"]);
			$_POST["description"] = stripslashes($_POST["description"]);
			$db->query("INSERT INTO galleries.images (galleryid, title, description, taken, modified, created)
						VALUES('$_POST[galleryid]', '$_POST[title]', '$_POST[description]', '$_POST[taken]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			$newimageid=$db->insert_id;
			$db->query("UPDATE galleries.images SET sortorder=imageid WHERE imageid='$newimageid'");
			if ($_FILES['imagefile']['name']) {
				// move file to main image file
				$uploaddir = UPLOAD_DIR . "images/gallery/";
				$destination_filename = $uploaddir . $newimageid . ".jpg";
				move_uploaded_file($_FILES['imagefile']['tmp_name'], $destination_filename);
				chmod($destination_filename, 0644);
				// process image transformations
				$imagesize = getimagesize($destination_filename);
				$src_width = $imagesize[0];
				$src_height = $imagesize[1];
				// resize main image if required to max 400 pixels in largest dimension
				if (($src_width > 400)||($src_height > 400)) {
					if ($src_width > $src_height) {
						$factor = $src_width / 400;
					} else {
						$factor = $src_height / 400;
					}
					$dest_width = $src_width / $factor;
					$dest_height = $src_height / $factor;
					$src_img = imagecreatefromjpeg($destination_filename);
					$dst_img = imagecreatetruecolor($dest_width, $dest_height);
					imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
					imagejpeg($dst_img, $destination_filename, 80);
					imagedestroy($src_img);
					imagedestroy($dst_img);
				}
				// produce thumbnail
				$src_filename = $destination_filename;
				$imagesize = getimagesize($src_filename);
				$src_width = $imagesize[0];
				$src_height = $imagesize[1];
				$destination_filename = $uploaddir . "tn" . $newimageid . ".jpg";
				$factor = $src_height / 65;
				$dest_width = $src_width / $factor;
				$dest_height = $src_height / $factor;
				$src_img = imagecreatefromjpeg($src_filename);
				$dst_img = imagecreatetruecolor($dest_width, $dest_height);
				imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $dest_width, $dest_height, $src_width, $src_height);
				imagejpeg($dst_img, $destination_filename, 80);
				imagedestroy($src_img);
				imagedestroy($dst_img);
			}
			// Log file upload
			log_entry($_SESSION["userid"],"Added image, key $newimageid");
			echo "<p>Added image to gallery</p>";
		}
	} else {
		show_image_form($_POST, $errors);
	}
} else if ($_GET["action"]) {
	switch ($_GET["action"]) {
		case "add":
			// set values for addition
			$values = array();
			$values["galleryid"] = $galleryid;
			show_image_form($values);
			break;
		case "edit":
			// set values for edit
			$image=$db->get_row("SELECT * FROM galleries.images WHERE imageid='$_GET[imageid]'");
			$values = array();
			$values["galleryid"] = $galleryid;
			$values["imageid"] = $_GET["imageid"];
			$values["title"] = $image->title;
			$values["description"] = $image->description;
			$values["taken"] = $image->taken;
			show_image_form($values);
			break;
		case "delete":
			confirm_image_deletion($_GET["imageid"], $galleryid);
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
if (($images = $db->get_results("SELECT * FROM galleries.images WHERE galleryid='$galleryid'"))&&($print_table == 1)) {
	echo '<table width="100%">';
	for ($x=0; $x < count($images); $x++) {
		echo '<tr><td width="24"><img src="images/image.gif" alt="image" /></td>';
		echo '<td class="detail">';
		echo '<a target="_blank" href="http://clacksweb/images/gallery/' . $images[$x]->imageid . '.jpg">' . $images[$x]->title . '</a>';
		echo '</td><td>' . $images[$x]->description . '</td>';
		echo '<td width="120" align="right">';
		echo '<a href="galleryimages.php?action=edit&imageid=' . $images[$x]->imageid . '&galleryid=' . $images[$x]->galleryid . '"><img src="images/edit.gif" alt="Edit image" border="0"></a> ';
		echo '<a href="galleryimages.php?action=delete&imageid=' . $images[$x]->imageid . '&galleryid=' . $images[$x]->galleryid . '"><img src="images/delete.gif" alt="Delete image" border="0"></a>';
		if ($x > 0) {
			echo ' <a href="galleryimages.php?galleryid=' . $galleryid . '&fromid=' . $images[$x]->imageid . '&fromso=' . $images[$x]->sortorder . '&toid=' . $images[$x-1]->imageid . '&toso=' . $images[$x-1]->sortorder . '"><img src="images/up.gif" alt="Move image up" border="0"></a>';
		} else {
			echo ' <img src="images/upoff.gif" alt="Top file" border="0"> ';
		}
		if ($x < (count($images)-1)) {
			echo ' <a href="galleryimages.php?galleryid=' . $galleryid . '&fromid=' . $images[$x]->imageid . '&fromso=' . $images[$x]->sortorder . '&toid=' . $images[$x+1]->imageid . '&toso=' . $images[$x+1]->sortorder . '"><img src="images/down.gif" alt="Move image down" border="0"></a>';
		} else {
			echo ' <img src="images/downoff.gif" alt="Bottom file" border="0"> ';
		}
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