<?php
include_once "includes/prepend.inc.php";
if (check_auth("busdir", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
$db = new db("admin", "HbCLlVH", "busdir", "164.177.128.68");
header("Cache-Control: private");
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<h3>Mass-email businesses</h3>
<?php
if ($_POST["emailbtn"]) {
	$errors = array();
	if (!$_POST["subject"]) $errors[] = "Please enter a subject";
	if (!$_POST["body"]) $errors[] = "Please enter a message";
	if (count($errors) == 0) {
		// send emails
		$query = "SELECT email, business FROM busdir.details WHERE email > ''";
		if ($_POST["sectionid"] > 0) $query .= " AND sectionid='" . $_POST["sectionid"] . "'";
		if ($_POST["subsectionid"] > 0) $query .= " AND subsectionid='" . $_POST["subsectionid"] . "'";
		if ($recipients = $db->get_results($query)) {
			$mail = new PHPMailer();
			$mail->From = "busdir@clacks.gov.uk";
			$mail->FromName = "Clackmannanshire Business Directory";
			$mail->AddAddress("busdir@clacks.gov.uk");
			if ($_FILES["attachfile"]["name"] > "") {
				$attachpath = UPLOAD_DIR . "tmp/" . $_FILES["attachfile"]["name"];
				move_uploaded_file($_FILES['attachfile']['tmp_name'], $attachpath);
			}
			foreach ($recipients as $busemail) {
				$mail->AddAddress("$busemail->email");
				$mail->Subject = $_POST["subject"];
				$mail->Body   = $_POST["body"];
				if ($attachpath) $mail->AddAttachment($attachpath);
				$mail->Host = MAIL_HOST;
				$mail->Mailer="smtp";
				if ($mail->Send()) {
					echo '<br>Success: ' . $busemail->email;
				} else {
					echo '<br>Failed: ' . $busemail->email;
				}
				$mail->ClearAddresses();
				$mail->ClearAttachments();
			}
			echo '<p><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Close" onclick="javascript: window.close();" /></p>';
		} else {
			echo '<p class="error">The email was not sent - no businesses with email addresses matched your selection.</p>';
			$errors[] = 'No matches';
		}
	} else {
		echo '<p>The email was not sent. Please correct these errors and resubmit the form:</p><ul>';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
if ((!$_POST)||(count($errors) > 0)) {
	extract ($_POST);
	?>
	<div id="formarea">
	<form name="bdexport" method="post" action="busdiremail.php" enctype="multipart/form-data">
	<table width="100%">
	<tr><td class="label">Section:</td>
	<td>
	<select name="sectionid"><option value="all">All businesses</option>
	<?php
	$sections = $db->get_results("SELECT sectionid, section FROM busdir.sections ORDER BY section");
	foreach ($sections as $section) {
		echo '<option value="' . $section->sectionid . '"';
		if ($_POST["sectionid"] == $section->sectionid) echo ' selected';
		echo '>' . $section->section . '</option>';
	}
	?>
	</select>
	</td></tr>
	<tr><td class="label">Subsection:</td>
	<td>
	<select name="subsectionid"><option value="all">All subsections</option>
	<?php
	$subsections = $db->get_results("SELECT subsectionid, subsection FROM busdir.subsections ORDER BY subsection");
	foreach ($subsections as $subsection) {
		echo '<option value="' . $subsection->subsectionid . '"';
		if ($_POST["subsectionid"] == $subsection->subsectionid) echo ' selected';
		echo '>' . $subsection->subsection . '</option>';
	}
	?>
	</select>
	</td></tr>
	<tr><td class="label">Subject:</td>
	<td><input type="text" name="subject" value="<?php echo $subject; ?>" maxlength="200" style="width: 200px;" /></td></tr>
	<tr><td class="label">Message:</td>
	<td><textarea name="body" rows="15" cols="40"><?php echo $body; ?></textarea></td></tr>
	<tr><td class="label">Attachment:</td>
	<td><input type="file" name="attachfile"></td></tr>
	<tr><td colspan="2" class="formbtns">
	<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="emailbtn" value="Send email" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
	</td></tr>
	</table>
	</form>
<?php } ?>
</div>
</body>
</html>
