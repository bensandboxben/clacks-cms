<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");

?>



<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include "includes/head.inc.php"; ?>
</head>
<body class="popup">



<h3>Amend Homepage Content</h3>

<?php
if ($_POST["save"])
	{
	$errors = array();
	if (!$_POST["title"]) $errors[] = "Please enter a title";
	if (!$_POST["text1"]) $errors[] = "Please enter some text";
	if (!$_POST["image"]) $errors[] = "Please enter an image";

	if (count($errors) == 0)
		{
		$db->query("UPDATE mainfeature SET
		title='$_POST[title]',
		text1='$_POST[text1]',
		text2='$_POST[text2]',
		image='$_POST[image]',
		modified=UNIX_TIMESTAMP() WHERE id='1'");

echo '<p>The content was sucessfully saved. Please review what you have submitted <a href="/">here</a>:</p>';


?>


		<script language="JavaScript">
		<!--
		self.close();
		window.close();
		//-->
		</script>
<?php
		}
	else
		{
		echo '<p class="form-error-intro" role="alert">The content was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
		}
}
?>






<?php
if ($mf = $db->get_row("SELECT * FROM mainfeature WHERE id='1'")) {
?>
<div id="formarea">
<form name="feature" method="post" action="mainfeature.php" onsubmit="myOnSubmitEventHandler()">
<table width="100%">
<tr>
	<td class='label'>Title:</td>
	<td><input type='text' name='title' value='<?php echo $mf->title; ?>' maxlength='50' style='width: 150px;' /></td>
</tr>
<tr>
	<td class='label'>Text Field 1:</td>
	<td><input type='text' name='text1' value='<?php echo $mf->text1; ?>' maxlength='250' style='width: 280px;' /></td>
</tr>
<tr>
	<td class='label'>Text Field 2:</td>
	<td><input type='text' name='text2' value='<?php echo $mf->text2; ?>' maxlength='250' style='width: 280px;' /></td>
</tr>
<tr>
	<td class='label'>Image:</td>
	<td><input type='text' name='image' value='<?php echo $mf->image; ?>' maxlength='250' style='width: 280px;' /></td>
</tr>



<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-secondary" value="Cancel" onclick="javascript: window.close();" />

</td></tr>
</table>
</form>
</div>

<?php
}
?>

<script>
document.feature.title.focus();
</script>
</body>
</html>