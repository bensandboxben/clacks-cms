<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
function show_search_form() {
	echo '<form name="locsearch" method="post" action="newlocation.php">
	<table>
	<tr><td class="label">Postcode:</td>
	<td><input type="text" name="postcode" value="' .  $_POST["postcode"] . '" maxlength="8" style="width: 100px;" /></td></tr>
	<tr><td class="label">Street:</td>
	<td><input type="text" name="street" value="' . $_POST["street"] . '" maxlength="50" style="width: 250px;" /></td></tr>
	<tr><td colspan="2" class="formbtns">
	<input class="btn btn-lg btn-primary" type="submit" name="search" value="Search" />
	<input class="btn btn-lg btn-primary" type="submit" name="cancel" value="Cancel" onclick="self.close();" />
	</td></tr>
	</table>
	</form>';
}
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<h3>Add new location</h3>
<div id="formarea">
<?php
if ($_POST["confloc"]) {
	$newloc = explode("|", $_POST["confloc"]);
	$uprn = $newloc[0];
	$addstr = $newloc[1];
	$postcode = $newloc[2]
?>
<script language="JavaScript">
<!--
self.opener.doAddLocation('<?php echo $addstr; ?>', '<?php echo $uprn; ?>', '<?php echo $postcode; ?>');
self.close();
//-->
</script>
<?php
} else if ($_POST["postcode"] || $_POST["street"]) {
	if ($_POST["postcode"]) {
		if ((strlen($_POST["postcode"]) == 7) &! (preg_match("/ /", $_POST["postcode"]))) {
			$postcode = strtoupper(substr($_POST["postcode"], 0, 4) . " " . substr($_POST["postcode"], 4, 3)); //convert spaceless postcode
		} else {
			$postcode = strtoupper($_POST["postcode"]);
		}
		$query = "SELECT building, bnum, street, town, uprn, postcode FROM cag.properties LEFT JOIN cag.streets USING(usrn), cag.towns WHERE towns.townid=properties.townid AND properties.postcode='$postcode' ORDER BY building, bnum, street, town";
	} else {
		$query = "SELECT building, bnum, street, town, uprn, postcode FROM cag.properties LEFT JOIN cag.streets USING(usrn), cag.towns WHERE towns.townid=properties.townid AND streets.street LIKE '%$_POST[street]%' ORDER BY building, bnum, street, town";
	}
	if ($addresses = $db->get_results($query)) {
		echo '<p>' . $db->num_rows . ' addresses found. Please select the address for this record:</p><form name="loclist" method="post" action="newlocation.php"><select name="confloc">';
		foreach ($addresses as $address) {
			if ($address->building) {
				$addstr = $address->building . ', ' . $address->street . ', ' . $address->town;
			} else {
				$addstr = $address->bnum . ', ' . $address->street . ', ' . $address->town;
			}
			echo '<option value="' . $address->uprn . '|' . $addstr . '|' . $address->postcode . '">' . $addstr;
			echo '</option>';
		}
		echo '</select><input class="btn btn-lg btn-primary"  type="submit" name="uprnbtn" value="Go" /></form>';
	} else {
		echo '<p>No addresses found. Please search again using different values:</p>';
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