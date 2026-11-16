<?php

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$contact_array = $db->get_row("SELECT * FROM contacts WHERE contactid='$_GET[id]'", ARRAY_A);
	extract($contact_array);
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
<h3>Edit contact</h3>
<?php } else { ?>
<h3>Add new contact</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["name"]) $errors[] = "Please enter the contact's name";
	if (!$_POST["jobtitle"]) $errors[] = "Please enter the contact's job title";
	if (!$_POST["telephone"]) $errors[] = "Please enter the contact's telephone number";
	if (!$_POST["email"]) $errors[] = "Please enter the contact's email address";
	if (count($errors) == 0) {
		if ($_POST["contactid"]) {
			$db->query("UPDATE contacts SET serviceid='$_POST[serviceid]', servicestring='$_POST[servicestring]', name='$_POST[name]', jobtitle='$_POST[jobtitle]', uprn='$_POST[uprn]', address='$_POST[address]', telephone='$_POST[telephone]', fax='$_POST[fax]', email='$_POST[email]', modified=UNIX_TIMESTAMP() WHERE contactid='$_POST[contactid]'");
		} else {
			$db->query("INSERT INTO contacts (serviceid, servicestring, name, jobtitle, uprn, address, telephone, fax, email, created, modified) VALUES('$_POST[serviceid]', '$_POST[servicestring]', '$_POST[name]', '$_POST[jobtitle]', '$_POST[uprn]', '$_POST[address]', '$_POST[telephone]', '$_POST[fax]', '$_POST[email]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			$newcontactid = $db->insert_id;
		}
		if ($_POST["noreturn"] == 1) {
?>
<script language="JavaScript">
<!--
opener.location.reload(true);
self.close();
//-->
</script>
<?php } else { ?>
<script language="JavaScript">
<!--
self.opener.doAddContact('<?php echo $_POST["jobtitle"] . " - " . $_POST["name"]; ?>', '<?php echo $newcontactid; ?>');
self.close();
//-->
</script>
<?php
	}
		} else {
			echo '<p class="form-error-intro" role="alert">The contact was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
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
function doAddLocation (label, value) {
var s = document.contact.elements["uprn"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>
<form name="contact" method="post" action="newcontact.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Service:</td>
<td>
<?php
if ($_SESSION["userlevel"] < 4) {
	$services = $db->get_results("SELECT serviceid, service FROM cms.services WHERE (serviceid='$_SESSION[userserviceid]') OR (parentserviceid='$_SESSION[userserviceid]') ORDER BY service", ARRAY_N);
} else {
	$services = $db->get_results("SELECT serviceid, service FROM cms.services ORDER BY service", ARRAY_N);
}
build_select_box($services, "serviceid", $serviceid);
?>
</td></tr>
<tr><td class="label">Service string:</td>
<td><input type="text" name="servicestring" value="<?php echo $servicestring; ?>" maxlength="250" style="width: 350px;" /></td></tr>
<tr><td class="label"><span class="required">*</span> Name:</td>
<td><input type="text" name="name" value="<?php echo $name; ?>" maxlength="50" style="width: 150px;" /></td></tr>
<tr><td class="label"><span class="required">*</span> Job title:</td>
<td><input type="text" name="jobtitle" value="<?php echo $jobtitle; ?>" maxlength="100" style="width: 200px;" /></td></tr>
<tr><td class="label">Location:</td>
<td>
<?php
$locations = $db->get_results("SELECT DISTINCT cag.properties.uprn, CONCAT(properties.building, ', ', properties.bnum, ' ', streets.street, ', ', towns.town) 
                                FROM clacksweb2.contacts, cag.properties, cag.streets, cag.towns 
                                WHERE cag.properties.townid = cag.towns.townid 
                                      AND contacts.uprn = properties.uprn 
                                      AND properties.usrn = streets.usrn", ARRAY_N);	
build_select_box($locations, "uprn", $uprn);
?>
 <a href="#" onClick="window.open('newlocation.php', 'loc_new', 'width=500,height=300,resizable=yes,scrollbars=yes,top=50,left=50')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new location</span></a>
</td></tr>
<tr><td class="label">Address:<br />(if no location)</td>
<td><input type="text" name="address" value="<?php echo $address; ?>" maxlength="250" style="width: 350px;" /></td></tr>
<tr><td class="label"><span class="required">*</span> Telephone:</td>
<td><input type="text" name="telephone" value="<?php echo $telephone; ?>" maxlength="40" style="width: 100px;" /></td></tr>
<!--tr><td class="label">Fax:</td>
<td><input type="text" name="fax" value="<?php echo $fax; ?>" maxlength="40" style="width: 100px;" /></td></tr-->
<tr><td class="label"><span class="required">*</span> Email:</td>
<td><input type="text" name="email" value="<?php echo $email; ?>" maxlength="100" style="width: 200px;" /></td></tr>
<tr><td colspan="2" class="formbtns">
<input class="btn btn-md btn-primary" type="submit" name="save" value="Save" /> <input class="btn btn-md btn-secondary" type="submit" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($contactid) echo '<input type="hidden" name="contactid" value="' . $contactid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.contact.serviceid.focus();
</script>
</body>
</html>