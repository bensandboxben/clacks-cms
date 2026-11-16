<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$vacid = $_REQUEST["vacid"];
// If this is a nationally advertised post update the vacancy record
if ($_POST["national"] == 1) {
	$db->query("UPDATE hr.vacancies SET national=1 WHERE vacid=$vacid");
} else {
	$db->query("UPDATE hr.vacancies SET national=0 WHERE vacid=$vacid");
}
if ($_POST["external"] == 1) {
	$db->query("UPDATE hr.vacancies SET external=1 WHERE vacid=$vacid");
} else {
	$db->query("UPDATE hr.vacancies SET external=0 WHERE vacid=$vacid");
}
$vacancy=$db->get_row("SELECT vacid, jobno, jobtitle, national, teaching, social, promoted, external FROM hr.vacancies LEFT JOIN hr.jobtitles USING(titleid) WHERE vacid='$vacid'");
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<div id="formarea">
<h3>Vacancy Files</h3>
<?php
if (($_POST["save"])&&($_POST["vacid"])) {
	$docpath = UPLOAD_DIR . "jobpacks";
	$vacid = $_POST["vacid"];
	if (!file_exists(UPLOAD_DIR . "jobpacks/" . $_POST["vacid"])) {
		mkdir(UPLOAD_DIR . "jobpacks/" . $_POST["vacid"], 0755);
	}
	$msg = array();;
// Process advert
	if ($_POST["delad"]) {
		if (file_exists($docpath . "/" . $vacid . "/" . $vacid . "ad.pdf")) {
			$ext = ".pdf";
		} else {
			$ext = ".doc";
		}
		if (unlink($docpath . "/" . $vacid . "/" . $vacid . "ad" . $ext)) {
			$msg[] = "Advert deleted";
		} else {
			$msg[] = "Problem deleting existing advert";
		}
	}
	if ($_FILES["ad"]["name"] > "") {
		if (($_FILES["ad"]["type"] != 'application/pdf')&&($_FILES["ad"]["type"] != 'application/msword')) {
			$msg[] = "Advert not a Word document or pdf - please select another file";
		} else {
			if ($_FILES["ad"]["type"] == 'application/pdf') {
				$ext = ".pdf";
			} else {
				$ext = ".doc";
			}
			$destination = $docpath . "/" . $vacid . "/" . $vacid . "ad" . $ext;
			if (move_uploaded_file($_FILES['ad']['tmp_name'], $destination)) {
				chmod($destination, 0644);
				$msg[] = "Advert uploaded successfully";
			} else {
				echo "eek";
				$msg[] = "Problem uploading advert";
			}
		}
	}
// Process profile
	if ($_POST["delprof"]) {
		if (file_exists($docpath . "/" . $vacid . "/" . $vacid . "prof.pdf")) {
			$ext = ".pdf";
		} else {
			$ext = ".doc";
		}
		if (unlink($docpath . "/" . $vacid . "/" . $vacid . "prof" . $ext)) {
			$msg[] = "Profile deleted";
		} else {
			$msg[] = "Problem deleting existing profile";
		}
	}
	if ($_FILES["prof"]["name"] > "") {
		if (($_FILES["prof"]["type"] != 'application/pdf')&&($_FILES["prof"]["type"] != 'application/msword')) {
			$msg[] = "Profile not a Word document or pdf - please select another file";
		} else {
			if ($_FILES["prof"]["type"] == 'application/pdf') {
				$ext = ".pdf";
			} else {
				$ext = ".doc";
			}
			$destination = $docpath . "/" . $vacid . "/" . $vacid . "prof" . $ext;
			if (move_uploaded_file($_FILES['prof']['tmp_name'], $destination)) {
				chmod($destination, 0644);
				$msg[] = "Profile uploaded successfully";
			} else {
				$msg[] = "Problem uploading profile";
			}
		}
	}
// Process T & C
	if ($_POST["deltc"]) {
		if (file_exists($docpath . "/" . $vacid . "/" . $vacid . "tc.pdf")) {
			$ext = ".pdf";
		} else {
			$ext = ".doc";
		}
		if (unlink($docpath . "/" . $vacid . "/" . $vacid . "tc" . $ext)) {
			$msg[] = "T & C deleted";
		} else {
			$msg[] = "Problem deleting existing T & C";
		}
	}
	if ($_FILES["tc"]["name"] > "") {
		if (($_FILES["tc"]["type"] != 'application/pdf')&&($_FILES["tc"]["type"] != 'application/msword')) {
			$msg[] = "T & C not a Word document or pdf - please select another file";
		} else {
			if ($_FILES["tc"]["type"] == 'application/pdf') {
				$ext = ".pdf";
			} else {
				$ext = ".doc";
			}
			$destination = $docpath . "/" . $vacid . "/" . $vacid . "tc" . $ext;
			if (move_uploaded_file($_FILES['tc']['tmp_name'], $destination)) {
				chmod($destination, 0644);
				$msg[] = "T & C uploaded successfully";
			} else {
				$msg[] = "Problem uploading T & C";
			}
		}
	}
// Process intereview arrangements
	if ($_POST["delint"]) {
		if (file_exists($docpath . "/" . $vacid . "/" . $vacid . "int.pdf")) {
			$ext = ".pdf";
		} else {
			$ext = ".doc";
		}
		if (unlink($docpath . "/" . $vacid . "/" . $vacid . "int" . $ext)) {
			$msg[] = "Interview arrangements deleted";
		} else {
			$msg[] = "Problem deleting existing interview arrangements";
		}
	}
	if ($_FILES["int"]["name"] > "") {
		if (($_FILES["int"]["type"] != 'application/pdf')&&($_FILES["int"]["type"] != 'application/msword')) {
			$msg[] = "Interview arrangements not a Word document or pdf - please select another file";
		} else {
			if ($_FILES["int"]["type"] == 'application/pdf') {
				$ext = ".pdf";
			} else {
				$ext = ".doc";
			}
			$destination = $docpath . "/" . $vacid . "/" . $vacid . "int" . $ext;
			if (move_uploaded_file($_FILES['int']['tmp_name'], $destination)) {
				chmod($destination, 0644);
				$msg[] = "Interview arrangements uploaded successfully";
			} else {
				$msg[] = "Problem uploading interview arrangements";
			}
		}
	}
// Process school profile
	$curr_profileid = $db->get_var("SELECT vacXfile.fileid FROM hr.vacXfile LEFT JOIN hr.files USING(fileid) WHERE vacid=$vacid AND typeid=7");
	if ($_POST["schoolprofileid"] != $curr_profileid) {
		$db->query("DELETE FROM hr.vacXfile WHERE vacid='$vacid' AND fileid='$curr_profileid'");
		$db->query("INSERT INTO hr.vacXfile (vacid, fileid) VALUES('$vacid', '$_POST[schoolprofileid]')");
	}
// Process additional information
	if ($_POST["deladdinfo"]) {
		if (file_exists($docpath . "/" . $vacid . "/" . $vacid . "addinfo.pdf")) {
			$ext = ".pdf";
		} else {
			$ext = ".doc";
		}
		if (unlink($docpath . "/" . $vacid . "/" . $vacid . "addinfo" . $ext)) {
			$msg[] = "additional information deleted";
		} else {
			$msg[] = "Problem deleting existing additional information";
		}
	}
	if ($_FILES["addinfo"]["name"] > "") {
		if (($_FILES["addinfo"]["type"] != 'application/pdf')&&($_FILES["addinfo"]["type"] != 'application/msword')) {
			$msg[] = "additional information not a Word document or pdf - please select another file";
		} else {
			if ($_FILES["addinfo"]["type"] == 'application/pdf') {
				$ext = ".pdf";
			} else {
				$ext = ".doc";
			}
			$destination = $docpath . "/" . $vacid . "/" . $vacid . "addinfo" . $ext;
			if (move_uploaded_file($_FILES['addinfo']['tmp_name'], $destination)) {
				chmod($destination, 0644);
				$msg[] = "additional information uploaded successfully";
			} else {
				$msg[] = "Problem uploading additional information";
			}
		}
	}
// Process other files
	$deldocs = $db->query("DELETE FROM hr.vacXfile WHERE vacid=$vacid");
// Determine core files for this post
	$coretypes = "(1,";
	if ($vacancy->national) {
// National
		$coretypes .= "3,";
	} else {
// Local
		$coretypes .= "2,";
	}
	if ($vacancy->teaching) {
// Teaching
		$coretypes .= "4,";
		if ($vacancy->promoted) {
			$coretypes .= "6,";
		} else {
			$coretypes .= "5,";
		}
	}
	 elseif ($vacancy->social) {
			$coretypes .= "11,";
		}
	 else {
// APT&C
		$coretypes .= "9,";
	}
	$coretypes = substr($coretypes, 0, -1) . ")";
// Find all files for this post
	if ($corefiles = $db->get_results("SELECT fileid FROM hr.files WHERE typeid IN $coretypes")) {
		foreach ($corefiles as $corefile) {
			$db->query("INSERT INTO hr.vacXfile (vacid, fileid) VALUES($vacid, '$corefile->fileid')");
		}
	}



// School profile?
	if ($_POST["schoolprofileid"]) {
		$db->query("INSERT INTO hr.vacXfile (vacid, fileid) VALUES('$vacid', '$_POST[schoolprofileid]')");
	}
	if ($_POST["filelist"]) { // if file links to be saved for this page
		$numdocs = count($_POST["filelist"]);
		for ($i=0; $i < $numdocs; $i++) {
			$result = $db->query("INSERT INTO hr.vacXfile (vacid, fileid)
								VALUES ('$vacid', '{$_POST[filelist][$i]}')
								");
			if ($result == 0) echo  mysql_errno() . ": " .  mysql_error();
		}
	}
	$msg[] = "Files updated successfully";
// create zipped jobpack
/* Fuck knows what goes here... */
	if (file_exists($docpath . "/" . $vacid . "/jp" . $vacid . ".zip")) unlink($docpath . "/" . $vacid . "/jp" . $vacid . ".zip");
	if (file_exists($docpath . "/" . $vacid . "/")) {
		$filelist = "";
		if (file_exists($docpath . "/" . $vacid . "/" . $vacid . "ad.pdf")) $filelist .= $docpath . "/" . $vacid . "/" . $vacid . "ad.pdf ";
		if (file_exists($docpath . "/" . $vacid . "/" . $vacid . "prof.pdf")) $filelist .= $docpath . "/" . $vacid . "/" . $vacid . "prof.pdf ";
		if (file_exists($docpath . "/" . $vacid . "/" . $vacid . "tc.pdf")) $filelist .= $docpath . "/" . $vacid . "/" . $vacid . "tc.pdf ";
		if (file_exists($docpath . "/" . $vacid . "/" . $vacid . "int.pdf")) $filelist .= $docpath . "/" . $vacid . "/" . $vacid . "int.pdf ";
		if (file_exists($docpath . "/" . $vacid . "/" . $vacid . "addinfo.pdf")) $filelist .= $docpath . "/" . $vacid . "/" . $vacid . "addinfo.pdf ";
		if (file_exists($docpath . "/" . $vacid . "/" . $vacid . "ad.doc")) $filelist .= $docpath . "/" . $vacid . "/" . $vacid . "ad.doc ";
		if (file_exists($docpath . "/" . $vacid . "/" . $vacid . "prof.doc")) $filelist .= $docpath . "/" . $vacid . "/" . $vacid . "prof.doc ";
		if (file_exists($docpath . "/" . $vacid . "/" . $vacid . "tc.doc")) $filelist .= $docpath . "/" . $vacid . "/" . $vacid . "tc.doc ";
		if (file_exists($docpath . "/" . $vacid . "/" . $vacid . "int.doc")) $filelist .= $docpath . "/" . $vacid . "/" . $vacid . "int.doc ";
		if (file_exists($docpath . "/" . $vacid . "/" . $vacid . "addinfo.doc")) $filelist .= $docpath . "/" . $vacid . "/" . $vacid . "addinfo.doc ";
		if ($vacfiles = $db->get_results("SELECT filename FROM hr.vacXfile LEFT JOIN hr.files USING(fileid) WHERE vacXfile.vacid='$vacid'")) {
			foreach($vacfiles as $vacfile) {
				$filelist .= $docpath . "/" . $vacfile->filename . " ";
			}
		}
		// create zip (without paths) to post's jobpack folder
		$cmd = "/usr/bin/zip -j " . $docpath . "/" . $vacid . "/jp" . $vacid . ".zip " . $filelist;
		exec("$cmd");
	}
	if (count($msg) > 0) {
		echo '<ul>';
		foreach ($msg as $item) {
			echo '<li>' . $item . '</li>';
		}
		echo '</ul>';
	}
}
?>
<div id="formarea">
<form action="vacancyfiles.php" method="post" enctype="multipart/form-data">
<table class="table table-striped table-bordered">
<tr><td class="label">Core documents:</td>
<td><select name="national"><option value="0"<?php if ($vacancy->national != 1) echo ' selected="selected"';?>>Local</option><option value="1"<?php if ($vacancy->national == 1) echo ' selected="selected"';?>>National</option></select></td></tr>
<tr><td class="label">Application form:</td>
<td><select name="external"><option value="0"
<?php
if ($vacancy->external != 1) echo ' selected="selected"';
echo '>';
if ($vacancy->teaching != 1) {
	echo 'APT&C';
} else if ($vacancy->promoted == 1) {
	echo 'Teaching (promoted)';
} else {
	echo 'Teaching (unpromoted)';
}
?>
</option>
<option value="1"<?php if ($vacancy->external == 1) echo ' selected="selected"';?>>External</option></select></td></tr>
<tr><td class="label">Advert:</td>
<td><input type="file" name="ad" />
<?php
if ((file_exists(UPLOAD_DIR . "jobpacks/" . $vacancy->vacid . "/" . $vacancy->vacid . "ad.pdf"))||(file_exists(UPLOAD_DIR . "jobpacks/" . $vacancy->vacid . "/" . $vacancy->vacid . "ad.doc"))) {
	if (file_exists(UPLOAD_DIR . "jobpacks/" . $vacancy->vacid . "/" . $vacancy->vacid . "ad.pdf")) {
		$ext = ".pdf";
	} else {
		$ext = ".doc";
	}
	echo ' <span style="font-size:80%;"> <a href="' . SITE_HOST . '/jobpacks/' . $vacancy->vacid . '/' . $vacancy->vacid . 'ad' . $ext . '" target="_blank">View</a> | ';
	echo ' Delete<input type="checkbox" name="delad" style="border:none; margin:0; padding:0;"></span>';
}
?>
</td></tr>
<tr><td class="label">Profile:</td>
<td><input type="file" name="prof" />
<?php
if ((file_exists(UPLOAD_DIR . "jobpacks/" . $vacancy->vacid . "/" . $vacancy->vacid . "prof.pdf"))||(file_exists(UPLOAD_DIR . "jobpacks/" . $vacancy->vacid . "/" . $vacancy->vacid . "prof.doc"))) {
	if (file_exists(UPLOAD_DIR . "jobpacks/" . $vacancy->vacid . "/" . $vacancy->vacid . "prof.pdf")) {
		$ext = ".pdf";
	} else {
		$ext = ".doc";
	}
	echo ' <span style="font-size:80%;"> <a href="' . SITE_HOST . '/jobpacks/' . $vacancy->vacid . '/' . $vacancy->vacid . 'prof' . $ext . '" target="_blank">View</a> | ';
	echo ' Delete<input type="checkbox" name="delprof" style="border:none; margin:0; padding:0;"></span>';
}
?>
</td></tr>
<tr><td class="label">T&C:</td>
<td><input type="file" name="tc" />
<?php
if ((file_exists(UPLOAD_DIR . "jobpacks/" . $vacancy->vacid . "/" . $vacancy->vacid . "tc.pdf"))||(file_exists(UPLOAD_DIR . "jobpacks/" . $vacancy->vacid . "/" . $vacancy->vacid . "tc.doc"))) {
	if (file_exists(UPLOAD_DIR . "jobpacks/" . $vacancy->vacid . "/" . $vacancy->vacid . "tc.pdf")) {
		$ext = ".pdf";
	} else {
		$ext = ".doc";
	}
	echo ' <span style="font-size:80%;"> <a href="' . SITE_HOST . '/jobpacks/' . $vacancy->vacid . '/' . $vacancy->vacid . 'tc' . $ext . '" target="_blank">View</a> | ';
	echo ' Delete<input type="checkbox" name="deltc" style="border:none; margin:0; padding:0;"></span>';
}
?>
</td></tr>
<tr><td class="label">Interview:</td>
<td><input type="file" name="int" />
<?php
if ((file_exists(UPLOAD_DIR . "jobpacks/" . $vacancy->vacid . "/" . $vacancy->vacid . "int.pdf"))||(file_exists(UPLOAD_DIR . "jobpacks/" . $vacancy->vacid . "/" . $vacancy->vacid . "int.doc"))) {
	if (file_exists(UPLOAD_DIR . "jobpacks/" . $vacancy->vacid . "/" . $vacancy->vacid . "int.pdf")) {
		$ext = ".pdf";
	} else {
		$ext = ".doc";
	}
	echo ' <span style="font-size:80%;"> <a href="' . SITE_HOST . '/jobpacks/' . $vacancy->vacid . '/' . $vacancy->vacid . 'int' . $ext . '" target="_blank">View</a> | ';
	echo ' Delete<input type="checkbox" name="delint" style="border:none; margin:0; padding:0;"></span>';
}
?>
</td></tr>
<tr><td class="label">Additional:</td>
<td><input type="file" name="addinfo" />
<?php
if ((file_exists(UPLOAD_DIR . "jobpacks/" . $vacancy->vacid . "/" . $vacancy->vacid . "addinfo.pdf"))||(file_exists(UPLOAD_DIR . "jobpacks/" . $vacancy->vacid . "/" . $vacancy->vacid . "addinfo.doc"))) {
	if (file_exists(UPLOAD_DIR . "jobpacks/" . $vacancy->vacid . "/" . $vacancy->vacid . "addinfo.pdf")) {
		$ext = ".pdf";
	} else {
		$ext = ".doc";
	}
	echo ' <span style="font-size:80%;"> <a href="' . SITE_HOST . '/jobpacks/' . $vacancy->vacid . '/' . $vacancy->vacid . 'addinfo' . $ext . '" target="_blank">View</a> | ';
	echo ' Delete<input type="checkbox" name="deladdinfo" style="border:none; margin:0; padding:0;"></span>';
}
?>
</td></tr>
<tr><td class="label">School profile:</td>
<td>
<?php
$curr_profileid = $db->get_var("SELECT vacXfile.fileid FROM hr.vacXfile LEFT JOIN hr.files USING(fileid) WHERE vacid=$vacid AND typeid=7");
$profiles = $db->get_results("SELECT fileid, title FROM hr.files WHERE typeid=7 ORDER BY title", ARRAY_N);
build_select_box($profiles, "schoolprofileid", $curr_profileid, "--");
?>
</td></tr>
<tr><td class="label">Other files:</td>
<td>
<?php
$filelist = $db->get_results("SELECT fileid, title FROM hr.files WHERE typeid=8 ORDER BY title", ARRAY_N);
$currfiles = array();
if ($vacid) {
	$currfiles = $db->get_col("SELECT fileid FROM hr.vacXfile WHERE vacid = $vacid");
	build_multiple_select_box($filelist, "filelist[]", $currfiles, 12);
} else {
	build_multiple_select_box($filelist, "filelist[]", $currfiles, 12);
}
?>
</td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
<input type="hidden" name="vacid" value="<?php echo $vacid;?>" />
</td></tr>
</table>
</form>
</div>
</div>
</body>
</html>