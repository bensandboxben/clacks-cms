<?php
include_once "includes/prepend.inc.php";
include_once "/www/cwv2/includes/gis.inc.php";
error_reporting(0);
header("Cache-Control: private");
function show_search_form() {
	echo '<form name="locsearch" method="post" action="newbuslocation.php">
	<table class="table table-striped table-bordered">
	<tr><td class="label">Postcode:</td>
	<td><input type="text" name="postcode" value="' .  $_POST["postcode"] . '" maxlength="8" style="width: 100px;" /></td></tr>
	<tr><td colspan="2" class="formbtns">
	<input type="submit" class="btn btn-md btn-primary" name="search" value="Search" />  <input type="submit" class="btn btn-md btn-secondary" name="cancel" value="Cancel" onclick="self.close();" />
	</td></tr>
	</table>
	</form>';
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>Business Directory</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include "includes/head.inc.php"; ?>
</head>
<body class="popup">
<h3>Find address</h3>
<div id="formarea">

<?php
if ($_POST["confloc"]) {
	$data = explode('|', $_POST["confloc"]);
	$address = explode('.', trim($data[1]));
	$address = $address[0];
	$uprn = $data[0];
	$postcode = $data[2];
	echo '
<script language="JavaScript">
<!--
self.opener.doAddAddress(\'' . $address . '\', \'' . $uprn . '\', \'' . $postcode . '\');
self.close();
//-->
</script>';
} else if ($_POST["postcode"]) {
	if ((strlen($_POST["postcode"]) == 7) &! (preg("/ /", $_POST["postcode"]))) {
		$postcode = strtoupper(substr($_POST["postcode"], 0, 4) . " " . substr($_POST["postcode"], 4, 3)); //convert spaceless postcode
	} else {
		$postcode = strtoupper($_POST["postcode"]);
	}
	$query = array("rtn" => "long", "uprn" => "", "pcode" => $_POST["postcode"], "houseno" => "", "housename" => "", "street" => "", "town" => "");
	$result = property_search($query);
	if (is_array($result["data"][0])) {
		echo '<p>Please select the address for this business:</p><form name="loclist" method="post" action="newbuslocation.php"><select name="confloc">';
		foreach ($result["data"] as $add) {
			echo '<option value="' . $add[0] . '|' . $add[3] . '|' . $postcode . '">' . $add[3];
			echo '</option>';
		}
		echo '</select><input type="submit" class="btn btn-md btn-primary" name="uprnbtn" value="Go" /></form>';
	} else {
		echo '<p>No addresses found. Please search again using different values or close this window and enter the address manually:</p>';
		show_search_form();
	}
} else {
	show_search_form();
?>
</div>
<script>
document.locsearch.postcode.focus();
</script>
<?php } ?>
</body>
</html>