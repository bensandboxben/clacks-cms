<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$curr_atoz = get_np_var('atoz_entries');
if (!is_array($curr_atoz)) $curr_atoz = array();
if (isset($_GET["del"])) {
	unset($curr_atoz[$_GET["del"]]);
	set_np_var('atoz_entries', $curr_atoz);
}
if ($_POST["addbtn"]) {
	$curr_atoz[$_POST["alpha"]] = array("entry"=>$_POST["entry"], "element"=>"c");
	set_np_var('atoz_entries', $curr_atoz);
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
<h3>Add A to Z entries</h3>
<?php
if (isset($error)) {
	echo '<p style="color: red; font-weight: bold;">' . $error . '</p>';
}
?>
<p>Adding A to Z entries for page: <?php echo get_np_var('title'); ?></p>
<form method="post" action="addatoz.php">
<table class="table table-striped table-bordered">
<tr><td>Alpha</td><td>Entry</td><td>Action</td></tr>
<tr><td>
<select name="alpha">
<?php
for ($x=0; $x < 26; $x++) {
	$alpha = chr($x+65);
	if (!array_key_exists($alpha, $curr_atoz)) echo '<option value="' . $alpha . '">' . $alpha . '</option>';
}
?>
</select>
</td>
<td><input type="text" name="entry" value="<?php echo get_np_var('title'); ?>" /></td>
<td><input type="submit" class="btn btn-md btn-primary" name="addbtn" value="+" /></td>
</tr>
<?php
if (is_array($curr_atoz) && count($curr_atoz) > 0) {
	foreach ($curr_atoz as $key=>$curr_entry) {
		echo '<tr><td>' . $key . '</td><td>' . $curr_entry["entry"] . '</td><td><a href="addatoz.php?del=' . $key . '">Delete</a></td></tr>';
	}
}
?>
</table>
<p><input type="submit" class="btn btn-md btn-secondary" name="saveatoz" value="Close" onclick="javascript:opener.location.reload(true);window.close();" />  <input type="submit" class="btn btn-md btn-secondary" name="cancel" value="Cancel" onclick="self.close();" /></p>
</form>
</body>
</html>