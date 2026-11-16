<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<h3>Add new estblishment</h3>
<?php
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["establishment"]) $errors[] = "Please enter an establishment name";
	if (count($errors) == 0) {
		$db->query("INSERT INTO hiestablishments (establishment, settlementid, address, postcode, uprn) VALUES('$_POST[establishment]', '$_POST[settlementid]', '$_POST[address]', '$_POST[postcode]', '$_POST[uprn]')");
		$newid = $db->insert_id;
?>
<script language="JavaScript">
<!--
self.opener.doAddEstablishment('<?php echo $_POST["establishment"]; ?>', '<?php echo $newid; ?>');
self.close();
//-->
</script>
<?php
	} else {
		echo '<p>The establishment was not saved. Please correct these errors and resubmit the form:</p><ul>';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
?>
<div id="formarea">
<script language="JavaScript">
<!--
function doAddLocation (address, uprn, postcode) {
document.forms['estfrm'].address.value = address;
document.forms['estfrm'].uprn.value = uprn;
document.forms['estfrm'].postcode.value = postcode;
}
//-->
</script>
<form name="estfrm" method="post" action="newestablishment.php"><table class="table table-striped table-bordered">
<tr><td class="label">Establishment:</td>
<td><input type="text" name="establishment" value="<?php echo $_POST["establishment"]; ?>" maxlength="200" style="width: 250px;" /></td></tr>
<tr><td class="label">Settlement:</td>
<td>
<?php
$settlements = $db->get_results("SELECT settlementid, settlement FROM settlements ORDER BY settlement", ARRAY_N);
build_select_box($settlements, "settlementid", $_POST["settlementid"]);
?>
</td></tr>
<tr><td class="label">Address: <a href="#" onClick="window.open('newlocation.php', 'loc_new', 'width=500,height=300,resizable=yes,scrollbars=yes,top=50,left=50')"><img src="images/add.gif" alt="Add new location" border="0" /></a></td>
<td><textarea name="address" style="width: 250px; height: 80px;"><?php echo $_POST["address"]; ?></textarea></td></tr>
<tr><td class="label">Postcode:</td>
<td><input type="text" name="postcode" value="<?php echo $_POST["postcode"]; ?>" maxlength="10" style="width: 80px;" /></td></tr>
<tr><td class="label">UPRN (read only):</td>
<td><input type="text" name="uprn" value="<?php echo $_POST["uprn"]; ?>" readonly="readonly" style="width: 80px;" /></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" />  <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onclick="self.close();" />
</td></tr>
</table>
</form>
</div>
<script>
document.estfrm.establishment.focus();
</script>
</body>
</html>