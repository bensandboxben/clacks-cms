<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$q_array = $db->get_row("SELECT * FROM communications.commsqs WHERE projectid='$_GET[projectid]'", ARRAY_A);
	extract($q_array);
}
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<?php if ($edit == 1) { ?>
<h3>Edit questionnaire</h3>
<?php } else { ?>
<h3>Add new questionnaire</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["projecttitle"]) $errors[] = "Please enter a project title";
	if (!$_POST["client"]) $errors[] = "Please enter a client name";
	if (!$_POST["clientemail"]) $errors[] = "Please enter a client email address";
	if (count($errors) == 0) {
		$_POST["projecttitle"] = addslashes($_POST["projecttitle"]);
		$_POST["client"] = addslashes($_POST["client"]);
		if ($_POST["projectid"]) {
			$db->query("UPDATE communications.commsqs SET projecttitle='$_POST[projecttitle]', client='$_POST[client]', clientemail='$_POST[clientemail]' WHERE projectid='$_POST[projectid]'");
			$projectid=$_POST["projectid"];
			$confstring=$_POST["confstring"];
		} else {
			$confstring = MD5(time());
			$db->query("INSERT INTO communications.commsqs (projecttitle, client, clientemail, created, confstring) VALUES('$_POST[projecttitle]', '$_POST[client]', '$_POST[clientemail]', UNIX_TIMESTAMP(),'$confstring')");
			$projectid=$db->insert_id;
		}
		if ($_POST["invite"] == 1) {
			$mail = new PHPMailer();
			$mail->From = "press@clacks.gov.uk";
			$mail->FromName = "Communications Unit";
			$mail->AddAddress($_POST["clientemail"]);
			$mail->Subject = "Communications Unit Project Questionnaire";
			$mail->Body   = "We recently completed the following project for you:\n\n";
			$mail->Body .= "* " . $_POST["projecttitle"] . "\n\n";
			$mail->Body .= "Please follow the link below to complete a questionnaire to let us know how satisfied you were with the work we carried out. We will use the information you give us to continue to improve the services we provide.\n\n";
			$mail->Body .= "If you have time to give us your comments and suggestions as well, they will be greatly welcomed.\n\n";
			$mail->Body .= "* http://clacksweb/cms/comms.php?projectid=" . $projectid . "&confstring=" . $confstring . "\n\n";
			$mail->Body .= "Thank you for your help.";
			$mail->Host = "itssmail02";
			$mail->Mailer="smtp";
			$mail->Send();
		}
?>
<script language="JavaScript">
<!--
opener.location.reload(true);
self.close();
//-->
</script>
<?php
	} else {
		echo '<p>The questionnaire was not saved. Please correct these errors and resubmit the form:</p><ul>';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
extract ($_POST);
?>
<div id="formarea">
<form name="commsq" method="post" action="newcommsq.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Project title:</td><td><input type="text" name="projecttitle" value="<?php echo $projecttitle; ?>" maxlength="200" style="width: 200px;" /></td></tr>
<tr><td class="label">Client name:</td><td><input type="text" name="client" value="<?php echo $client; ?>" maxlength="200" style="width: 120px;" /></td></tr>
<tr><td class="label">Client email:</td><td><input type="text" name="clientemail" value="<?php echo $clientemail; ?>" maxlength="200" style="width: 200px;" /></td></tr>
<tr><td class="label">Send invite now:</td><td><input type="checkbox" name="invite" value="1" checked="checked" /></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($projectid) echo '<input type="hidden" name="projectid" value="' . $projectid . '" /><input type="hidden" name="confstring" value="' . $confstring . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.commsq.projecttitle.focus();
</script>
</body>
</html>