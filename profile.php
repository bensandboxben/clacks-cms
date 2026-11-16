<?php
/**
* Edit user profile
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Edit user profile";
// set default sort type for content (title - asc)
include "includes/header.inc.php";
$errors = array();
if ($_POST["save"]) {
	if (!$_POST["email"]) $errors["email"] = 'Please enter a contact email address';
	if (!$_POST["telephone"]) $errors["telephone"] = 'Please enter a contact telephone number';
	if (($_POST["newpass"])||($_POST["confpass"])) { // if changing password check for consistency
		if ($_POST["newpass"] != $_POST["confpass"]) $errors["newpass"] = "Password and confirmed password did not match";
	}
	if (count($errors) == 0) {
		$updatequery="UPDATE cms.users SET name='$_POST[name]', email='$_POST[email]', telephone='$_POST[telephone]'";
		if ($_POST["newpass"]) $updatequery .= ", password=MD5('$_POST[newpass]')";
		$updatequery .= " WHERE userid='$_SESSION[userid]'";
		$db->query($updatequery);
		$usermsg = "Profile updated";
	}
}
$user = $db->get_row("SELECT * FROM cms.users WHERE userid='$_SESSION[userid]'");
?>
<h3>Edit user profile</h3>
<div id="formarea">
<?php
if (count($errors) > 0) {
	echo '<ul class="error">';
	foreach ($errors as $error) {
		echo '<li>' . $error . '</li>';
	}
	echo '</ul>';
} else if ($usermsg) {
	echo '<ul class="error"><li>' . $usermsg . '</li></ul>';
}
?>
<form action="profile.php" method="post" id="profilefrm">
<table width="100%" class="table table-striped table-bordered">
<tr><td class="label">Name:</td>
<td><input type="text" name="name" style="width: 18em;" value="<?php if ($_POST["name"]) { echo $_POST["name"]; } else { echo $user->name; } ?>" /></td></tr>
<tr><td class="label">Username:</td>
<td><?php echo $_SESSION["username"]; ?></td></tr>
<tr><td class="label">Service:</td>
<td><?php echo $db->get_var("SELECT service FROM cms.services WHERE serviceid='$_SESSION[userserviceid]'"); ?></td></tr>
<tr><td class="label">Change password:</td>
<td><input type="password" name="newpass" /></td></tr>
<tr><td class="label">Confirm password:</td>
<td><input type="password" name="confpass" /></td></tr>
<tr><td class="label">Email address:</td>
<td><input type="text" name="email" style="width: 18em;" value="<?php if ($_POST["email"]) { echo $_POST["email"]; } else { echo $user->email; } ?>" /></td></tr>
<tr><td class="label">Telephone:</td>
<td><input type="text" name="telephone" style="width: 12em;" value="<?php if ($_POST["telephone"]) { echo $_POST["telephone"]; } else { echo $user->telephone; } ?>" /></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Update profile" />
</td></tr>
</table>
</form>
</div>
<?php
include "includes/footer.inc.php";
?>