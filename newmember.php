<?php




include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$member_array = $db->get_row("SELECT * FROM councillors LEFT JOIN cag.newwardlt USING(wardid) WHERE emid='$_GET[id]'", ARRAY_A);
	extract($member_array);
}
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<h3>Edit member</h3>
<?php

if ($_POST["save"]) {

		$training = addslashes($_POST["training"]);
		$surgery = addslashes($_POST["surgery"]);
		$additionalinfo = addslashes($_POST["additionalinfo"]);
		$position = addslashes($_POST["position"]);

	$errors = array();
	if (!$_POST["vacant"]) {
		if (!$_POST["name"]) $errors[] = "Please enter the member's name";
		if (!$_POST["phone"]) $errors[] = "Please enter the member's telephone number";

	}

	$vacant = $_POST['vacant'] ?? ''; // Set default value if not set
	$showpic = $_POST['showpic'] ?? ''; // Set default value if not set
	
	if ($vacant === null || $vacant === '') {
	    $vacant = '0'; // Replace null or empty string with '0'
	}
	if ($showpic  === null || $showpic === '') {
	    $showpic = '0'; // Replace null or empty string with '0'
	}

	if (count($errors) == 0) {
		$db->query("UPDATE councillors SET vacant='$vacant', showpic='$showpic', name='$_POST[name]', phone='$_POST[phone]', fax='$_POST[fax]', email='$_POST[email]', party='$_POST[party]', wardid='$_POST[wardid]', surgery='$surgery', training='$training', additionalinfo='$additionalinfo', position='$position', modified=UNIX_TIMESTAMP() WHERE emid='$_POST[emid]'");

?>
<script language="JavaScript">
<!--
opener.location.reload(true);
self.close();
//-->
</script>
<?php
	} else {
		echo '<p>The member was not saved. Please correct these errors and resubmit the form:</p><ul>';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
extract ($_POST);
?>
<div id="formarea">
<form name="member" method="post" action="newmember.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Ward:</td>
<td>
<?php
$newwards = $db->get_results("SELECT wardid, wardname FROM cag.newwardlt ORDER BY wardname", ARRAY_N);
build_select_box($newwards, "wardid", $wardid, "--");
?>
</td></tr>
<tr><td class="label">Vacant?:</td>
<td>
<?php
build_yes_no_checkbox("vacant", $vacant);
?>
</td></tr>
<tr><td class="label">Show picture?:</td>
<td>
<?php
build_yes_no_checkbox("showpic", $showpic);
?>
</td></tr>
<tr><td class="label">Name:</td>
<td><input type="text" name="name" value="<?php echo $name; ?>" maxlength="50" style="width: 200px;" /></td></tr>
<tr><td class="label">Telephone:</td>
<td><input type="text" name="phone" value="<?php echo $phone; ?>" maxlength="40" style="width: 250px;" /></td></tr>
<tr><td class="label">Fax:</td>
<td><input type="text" name="fax" value="<?php echo $fax; ?>" maxlength="40" style="width: 200px;" /></td></tr>
<tr><td class="label">Email:</td>
<td><input type="text" name="email" value="<?php echo $email; ?>" maxlength="100" style="width: 250px;" /></td></tr>
<tr><td class="label">Party:</td>
<td><input type="text" name="party" value="<?php echo $party; ?>" maxlength="100" style="width: 250px;" /></td></tr>
<tr><td class="label">Position:</td>
<td><input type="text" name="position" value="<?php echo $position; ?>" maxlength="200" style="width: 250px;" /></td></tr>
<tr><td class="label">Surgeries:</td>
<td><textarea name="surgery" style="width: 300px; height: 200px;"><?php echo $surgery; ?></textarea></td></tr>

<tr><td class="label">Training:</td>
<td><textarea rows="4" cols="20" name="training" maxlength="400" style="width: 300px;" ><?php echo $training; ?></textarea></td></tr>
<tr><td class="label">Additional Info:</td>
<td><textarea rows="4" cols="20" name="additionalinfo" maxlength="200" style="width: 300px;" ><?php echo $additionalinfo; ?></textarea></td></tr>

<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
<input type="hidden" name="emid" value="<?php echo $emid;?>" />
<input type="hidden" name="wardname" value="<?php echo $wardname;?>" />
</td></tr>
</table>
</form>
</div>
<script>
document.member.name.focus();
</script>
</body>
</html>