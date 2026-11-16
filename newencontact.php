<?php
include_once "includes/prepend.inc.php";
$db = new db("emergency", "32xN!7b", "emergency", "164.177.128.68");
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$contact_array = $db->get_row("SELECT * FROM emergency.contacts WHERE contactid='$_GET[contactid]'", ARRAY_A);
	extract($contact_array);
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
<h3>Edit contact</h3>
<?php } else { ?>
<h3>Add new contact</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["name"]) $errors[] = "Please enter the contact's name";
	if (!$_POST["position"]) $errors[] = "Please enter the contact's position";
	if (!$_POST["tel"]) $errors[] = "Please enter the contact's telephone number";
	if (!$_POST["email"]) $errors[] = "Please enter the contact's email address";
	if (count($errors) == 0) {
		if ($_POST["contactid"]) {
			$db->query("UPDATE emergency.contacts SET name='$_POST[name]', position='$_POST[position]', tel='$_POST[tel]', fax='$_POST[fax]', email='$_POST[email]' WHERE contactid='$_POST[contactid]'");
		} else {
			$db->query("INSERT INTO emergency.contacts (name, position, tel, fax, email) VALUES('$_POST[name]', '$_POST[position]', '$_POST[tel]', '$_POST[fax]', '$_POST[email]')");
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
self.opener.doAddContact('<?php echo $_POST["name"]; ?>', '<?php echo $newcontactid; ?>');
self.close();
//-->
</script>
<?php
	}
		} else {
			echo '<p>The contact was not saved. Please correct these errors and resubmit the form:</p><ul>';
			foreach ($errors as $error) {
				echo '<li>' . $error . '</li>';
			}
			echo '</ul>';
		}
	}
extract ($_POST);
?>
<div id="formarea">
<form name="contact" method="post" action="newencontact.php">
<table class="table table-striped table-bordered">
<tr><td class="label"><span class="required">*</span> Name:</td>
<td><input type="text" name="name" value="<?php echo $name; ?>" maxlength="50" style="width: 150px;" /></td></tr>
<tr><td class="label"><span class="required">*</span> Position:</td>
<td><input type="text" name="position" value="<?php echo $position; ?>" maxlength="100" style="width: 200px;" /></td></tr>
<tr><td class="label"><span class="required">*</span> Telephone:</td>
<td><input type="text" name="tel" value="<?php echo $tel; ?>" maxlength="40" style="width: 100px;" /></td></tr>
<tr><td class="label">Fax:</td>
<td><input type="text" name="fax" value="<?php echo $fax; ?>" maxlength="40" style="width: 100px;" /></td></tr>
<tr><td class="label"><span class="required">*</span> Email:</td>
<td><input type="text" name="email" value="<?php echo $email; ?>" maxlength="100" style="width: 200px;" /></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($contactid) echo '<input type="hidden" name="contactid" value="' . $contactid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.contact.name.focus();
</script>
</body>
</html>