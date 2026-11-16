<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "includes/prepend.inc.php";

//$db = new db("admin", "HbCLlVH", "busdir", "51.104.239.104");

header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$bus_array = $db->get_row("SELECT * FROM busdir.details WHERE busid='$_GET[busid]'", ARRAY_A);
	extract($bus_array);
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include "includes/head.inc.php"; ?>
</head>
<body class="popup">
<?php if ($edit == 1) { ?>
<h3>Edit business</h3>
<?php } else { ?>
<h3>Add new business</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["business"]) $errors[] = "Please enter the name of this business";
	if (!$_POST["settlementid"]) $errors[] = "Please select a settlement for this business";
	if (count($errors) == 0) {


// Assuming $fairtrade comes from $_POST or database retrieval
$fairtradeValue = isset($_POST['fairtrade']) ? $_POST['fairtrade'] : $fairtrade;
// If $fairtradeValue is null or an empty string, set it to 0 (false)
if ($fairtradeValue === null || $fairtradeValue === '') {
    $fairtradeValue = 0;
}


		if ($_POST["busid"]) {
			$db->query("UPDATE busdir.details SET business='$_POST[business]', title='$_POST[title]', firstname='$_POST[firstname]', surname='$_POST[surname]', position='$_POST[position]', uprn='$_POST[uprn]', address='$_POST[address]', postcode='$_POST[postcode]', settlementid='$_POST[settlementid]', tel='$_POST[tel]', mobile='$_POST[mobile]', fax='$_POST[fax]', email='$_POST[email]', web='$_POST[web]', type='$_POST[type]', sectionid='$_POST[sectionid]', subsectionid='$_POST[subsectionid]', otherservice='$_POST[otherservice]',  fairtrade='$fairtradeValue', modified=UNIX_TIMESTAMP() WHERE busid='$_POST[busid]'");
		} else {
			$db->query("INSERT INTO busdir.details (business, title, firstname, surname, position, uprn, address, postcode, settlementid, tel, mobile, fax, email, web, type, sectionid, subsectionid, otherservice, fairtrade, modified, created) VALUES('$_POST[business]', '$_POST[title]', '$_POST[firstname]', '$_POST[surname]', '$_POST[position]', '$_POST[uprn]', '$_POST[address]', '$_POST[postcode]', '$_POST[settlementid]', '$_POST[tel]', '$_POST[mobile]', '$_POST[fax]', '$_POST[email]', '$_POST[web]', '$_POST[type]', '$_POST[sectionid]', '$_POST[subsectionid]', '$_POST[otherservice]', '$fairtradeValue', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
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
		echo '<p class="form-error-intro" role="alert">The business was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
extract ($_POST);
?>
<div id="formarea">
<script language="JavaScript">
<!--
function doAddAddress (address, uprn, postcode) {
document.forms['business'].address.value = address;
document.forms['business'].uprn.value = uprn;
document.forms['business'].postcode.value = postcode;
}
//-->
</script>
<form name="business" method="post" action="newbusiness.php">
<table width="100%" class="table table-striped table-bordered">
<tr><td class="label">Business:</td>
<td><input type="text" name="business" value="<?php echo $business; ?>" maxlength="250" style="width: 300px;" /></td></tr>
<tr><td class="label">Contact title:</td>
<td><input type="text" name="title" value="<?php echo $title; ?>" maxlength="10" style="width: 50px;" /></td></tr>
<tr><td class="label">Contact forename:</td>
<td><input type="text" name="firstname" value="<?php echo $firstname; ?>" maxlength="250" style="width: 300px;" /></td></tr>
<tr><td class="label">Contact surname:</td>
<td><input type="text" name="surname" value="<?php echo $surname; ?>" maxlength="250" style="width: 300px;" /></td></tr>
<tr><td class="label">Contact position:</td>
<td><input type="text" name="position" value="<?php echo $position; ?>" maxlength="250" style="width: 300px;" /></td></tr>
<tr><td class="label">Address: <!-- <a href="#" onClick="window.open('newbuslocation.php', 'loc_new', 'width=700,height=250,resizable=yes,scrollbars=yes,top=50,left=50')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new location</span></a> --></td>
<td><textarea name="address" rows="5" cols="30"><?php echo $address; ?></textarea></td></tr>
<tr><td class="label">Postcode:</td>
<td><input type="text" name="postcode" value="<?php echo $postcode; ?>" maxlength="20" style="width: 100px;" /></td></tr>
<tr><td class="label">UPRN:</td>
<td><input type="text" name="uprn" value="<?php echo $uprn; ?>" maxlength="20" style="width: 100px;" /></td></tr>
<tr><td class="label">Settlement:</td>
<td>
<?php
$settlements = $db->get_results("SELECT settlementid, settlement FROM clacksweb2.settlements ORDER BY settlement", ARRAY_N);
build_select_box($settlements, "settlementid", $settlementid, '--');
?>
</td></tr>
<tr><td class="label">Telephone:</td>
<td><input type="text" name="tel" value="<?php echo $tel; ?>" maxlength="200" style="width: 200px;" /></td></tr>
<tr><td class="label">Mobile:</td>
<td><input type="text" name="mobile" value="<?php echo $mobile; ?>" maxlength="200" style="width: 200px;" /></td></tr>
<tr><td class="label">Fax:</td>
<td><input type="text" name="fax" value="<?php echo $fax; ?>" maxlength="200" style="width: 200px;" /></td></tr>
<tr><td class="label">Email:</td>
<td><input type="text" name="email" value="<?php echo $email; ?>" maxlength="200" style="width: 200px;" /></td></tr>
<tr><td class="label">Website:</td>
<td><input type="text" name="web" value="<?php echo $web; ?>" maxlength="250" style="width: 300px;" /></td></tr>
<tr><td class="label">Nature of business:</td>
<td><input type="text" name="type" value="<?php echo $type; ?>" maxlength="255" style="width: 300px;" /></td></tr>
<tr><td class="label">Other services:</td>
<td><input type="text" name="otherservice" value="<?php echo $otherservice; ?>" maxlength="255" style="width: 300px;" /></td></tr>
<tr><td class="label">Section:</td>
<td>
<?php
$sections = $db->get_results("SELECT sectionid, section FROM busdir.sections ORDER BY section", ARRAY_N);
build_select_box($sections, "sectionid", $sectionid, '--');
?>
</td></tr>
<tr><td class="label">Sub-section:</td>
<td>
<?php
$subsections = $db->get_results("SELECT subsectionid, subsection FROM busdir.subsections ORDER BY subsection", ARRAY_N);
build_select_box($subsections, "subsectionid", $subsectionid, '--');
?>
</td></tr>

<tr>
	<td class="label">Fairtrade:</td>
	<td>
		<?php build_yes_no_checkbox("fairtrade", $fairtrade); ?>
	</td>
</tr>


<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-secondary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($busid) echo '<input type="hidden" name="busid" value="' . $busid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.business.business.focus();
</script>
</body>
</html>