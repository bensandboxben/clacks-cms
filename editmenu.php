<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include_once "includes/prepend.inc.php";

if (check_auth("content", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");

if ($_POST["submit"]) {
	if ($_POST["newmenu"]) {
		$_REQUEST["menufile"] = strtolower(preg_replace("/[^[:alnum:]]/", "", $_POST["newmenu"])) . ".txt";
	}

	$handle = fopen("/home/site/wwwroot/cms/menus/" . $_REQUEST["menufile"], "w");

	fputs($handle, stripslashes($_POST["menu"]));
	fclose($handle);
}


$menutxt = file_get_contents("/home/site/wwwroot/cms/menus/" . $_REQUEST["menufile"]);


?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="/css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body class="popup">

<div id="formarea">
<h3>Edit menu</h3>
<p><strong>Editing menu file:
<?php
if ($_GET["menufile"] == "new") {
	echo 'Creating new file';
} else {
	echo $_REQUEST["menufile"];
}
?>
</strong></p>
<form name="editmenu" action="editmenu.php" method="post">
<?php
if ($_GET["menufile"] == "new") {
	echo 'Menu name (no spaces, lower case): <input type="text" name="newmenu" style="width: 150px;" />';
}
?>
<textarea style="width: 600px; height: 300px;" name="menu">
<?php echo $menutxt; ?>
</textarea>
<input type="hidden" name="menufile" value="<?php echo $_REQUEST["menufile"];?>" />
<p style="text-align: center;">
	<input class="btn btn-lg btn-primary" type="submit" name="submit" value="Save menu" />
	<input class="btn btn-lg btn-primary" type="submit" value="Close" onclick="javascript:opener.location.reload(true);window.close()" />
</p>
</form>
</div>
</body>
</html>
