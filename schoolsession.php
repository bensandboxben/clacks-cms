<?php
include_once "includes/prepend.inc.php";
if (check_auth("schoolsession", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
if ($_POST["submit"]) {
	if ($_POST["session"]) {
		$db->query("UPDATE clacksweb2.config SET schoolsession='$_POST[session]'");
	}
}
$session = $db->get_var("SELECT schoolsession FROM clacksweb2.config");
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
<h3>Edit session</h3>
<form name="editsession" action="schoolsession.php" method="post">
<input type="text" name="session" id="session" value="<?php echo $session; ?>" />
<p style="text-align: center;"><input type="submit" class="btn btn-md btn-primary" name="submit" value="Save" /> <input type="submit" class="btn btn-md btn-secondary" value="Close" onclick="javascript:opener.location.reload(true);window.close()" /></p>
</form>
</div>
</body>
</html>