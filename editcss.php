<?php
include_once "includes/prepend.inc.php";
if (check_auth("content", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
if ($_POST["submit"]) {
	if ($_POST["newcss"]) {
		$_REQUEST["cssfile"] = strtolower(ereg_replace("[^[:alnum:]]", "", $_POST["newcss"])) . ".css";
	}
	$handle = fopen(UPLOAD_DIR . "css/user/" . $_REQUEST["cssfile"], "w");
	fputs($handle, $_POST["css"]);
	fclose($handle);
}
$csstxt = file_get_contents(UPLOAD_DIR . "css/user/" . $_REQUEST["cssfile"]);
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
<h3>Edit CSS file</h3>
<p><strong>Editing CSS file:
<?php
if ($_GET["cssfile"] == "new") {
	echo 'Creating new style sheet';
} else {
	echo $_REQUEST["cssfile"];
}
?>
</strong></p>
<form name="editcss" action="editcss.php" method="post">
<?php
if ($_GET["cssfile"] == "new") {
	echo 'File name (no spaces, lower case): <input type="text" name="newcss" style="width: 150px;" />';
}
?>
<textarea style="width: 600px; height: 300px;" name="css">
<?php echo $csstxt; ?>
</textarea>
<input type="hidden" name="cssfile" value="<?php echo $_REQUEST["cssfile"];?>" />
<p style="text-align: center;"><input type="submit" class="btn btn-md btn-primary" name="submit" value="Save CSS" /> <input type="submit" class="btn btn-md btn-secondary" value="Close" onclick="javascript:opener.location.reload(true);window.close()" /></p>
</form>
</div>
</body>
</html>