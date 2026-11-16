<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$councillor_array = $db->get_row("SELECT * FROM councillors WHERE emid='$_GET[emid]'", ARRAY_A);
	extract($councillor_array);
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
<h3>Edit councillor</h3>
<?php } else { ?>
<h3>Add new Councillor</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["name"]) $errors[] = "Please enter the name of the councillor";
	$query = "SELECT emid FROM councillordetails WHERE emid='" . addslashes($_POST["emid"]) . "'";

	//if ($_POST["docid"]) $query .= " AND docid != " . $_POST["docid"];
	//if ($exists = $db->get_var($query)) $errors[] = "A document already exists with this name";
	//if (!$_POST["doctypeid"]) $errors[] = "Please select a document type for this document";
	//if (!$_POST["description"]) $errors[] = "Please enter a brief description for this document";
	//if ((!$_POST["reviewdate"])||($_POST["reviewdate"] == '0000-00-00')||($_POST["reviewdate"] == '')) $_POST["reviewdate"] = date('Y-m-d', mktime (0,0,0,date("m")+3,date("d"),  date("Y")));


	if (count($errors) == 0) {


		if ($_POST["emid"]) {
			if ($_POST['vacant'] == "yes")
				$vacant = 1;
			else
				$vacant = 0;


			$username = strtolower($_POST[username]);

			$tempparty = $db->get_var("SELECT partyname FROM parties WHERE partyid = '$_POST[partyid]'"); // get the new docid to put into the instances table

			$db->query("UPDATE councillors SET name='$_POST[name]', wardid='$_POST[wardid]', surgery='$_POST[surgery]', training='$_POST[training]', additionalinfo='$_POST[additionalinfo]', vacant='$vacant', phone='$_POST[phone]', email='$_POST[email]', fax='$_POST[fax]', party='$tempparty', partyid='$_POST[partyid]', modified=UNIX_TIMESTAMP() WHERE emid='$_POST[emid]'");

			$temploginid = $db->get_var("SELECT loginid FROM councillors WHERE emid = '$_POST[emid]'"); // get the new docid to put into the instances table

			$temppass = md5($_POST[password]);
			$db->query("UPDATE councillorsLogin SET username='$username', password='$temppass' WHERE loginid = '$temploginid'");


		} else {


			$username = strtolower($_POST[username]);

			if ($_POST['vacant'] == "yes")
				$vacant = 1;
			else
				$vacant = 0;

			$tempparty = $db->get_var("SELECT partyname FROM parties WHERE partyid = '$_POST[partyid]'"); // get the new docid to put into the instances table

			$db->query("INSERT INTO councillors (vacant, wardid, surgery, training, additional info, name, party, partyid, phone, fax, email, modified) VALUES('$vacant', '$_POST[wardid]', '$_POST[surgery]',  '$_POST[training]', '$_POST[additionalinfo]', '$_POST[name]', '$tempparty', '$_POST[partyid]', '$_POST[phone]', '$_POST[fax]', '$_POST[email]', UNIX_TIMESTAMP())");
			$newcouncid = $db->insert_id;

			$tempemid = $db->get_var("SELECT emid FROM councillors WHERE name = '$_POST[name]' AND partyid = '$_POST[partyid]' "); // get the new docid to put into the instances table

			$db->query("UPDATE councillors SET loginid='$tempemid' WHERE emid = $tempemid");

			$temppass = md5($_POST[password]);
			$db->query("INSERT INTO councillorsLogin (loginid, username, password) VALUES('$tempemid', '$username', '$temppass')");

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
		echo '<p class="form-error-intro" role="alert">The document was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
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
function doAddCat (label, value) {
var s = document.doc.elements["catid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>
<form name="doc" method="post" action="newcouncillordetails.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Vacant:</td>
<td><input type="radio" name="vacant" checked="checked" value="no"> No <input type="radio" name="vacant" value="yes"> Yes</td></tr>
<tr><td><input type="hidden" name="emid" value="<?php echo $emid; ?>" /></td></tr>

<tr><td class="label">Councillor name:</td>
<td><input type="text" name="name" value="<?php echo $name; ?>" maxlength="200" style="width: 300px;" /></td></tr>
<tr><td class="label">Phone:</td>
<td><input type="text" name="phone" value="<?php echo $phone; ?>" maxlength="200" style="width: 300px;" /></td></tr>
<tr><td class="label">Fax:</td>
<td><input type="text" name="fax" value="<?php echo $fax; ?>" maxlength="200" style="width: 300px;" /></td></tr>
<tr><td class="label">Email:</td>
<td><input type="text" name="email" value="<?php echo $email; ?>" maxlength="200" style="width: 300px;" /></td></tr>
<tr><td class="label">Party:</td>
<td>
<?php
$parties = $db->get_results("SELECT partyid, partyname FROM clacksweb2.parties ", ARRAY_N);
build_select_box($parties, "partyid", $partyid, '-');
?>
</td></tr>
<tr><td class="label">Ward:</td>
<td>
<?php
$wards = $db->get_results("SELECT wardid, wardarea FROM clacksweb2.ward ", ARRAY_N);
build_select_box($wards, "wardid", $wardid, '-');
?>
</td></tr>
<tr><td class="label">Surgery:</td>
<td><textarea rows="4" cols="20" name="surgery" maxlength="200" style="width: 300px;" ><?php echo $surgery; ?></textarea></td></tr>

<tr><td class="label">Training:</td>
<td><textarea rows="4" cols="20" name="training" maxlength="200" style="width: 300px;" ><?php echo $training; ?></textarea></td></tr>

<tr><td class="label">Additional Info:</td>
<td><textarea rows="4" cols="20" name="additionalinfo" maxlength="200" style="width: 300px;" ><?php echo $additionalinfo; ?></textarea></td></tr>

<tr><td class="label">Username:</td>
<td><input type="text" name="username" value="<?php echo $username; ?>" maxlength="16" style="width: 300px;" /></td></tr>

<tr><td class="label">Password:</td>
<td><input type="password" name="password" value="<?php echo $password; ?>" maxlength="16" style="width: 300px;" /></td></tr>

<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-secondary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($emid) echo '<input type="hidden" name="docid" value="' . $emid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.doc.serviceid.focus();
</script>
</body>
</html>