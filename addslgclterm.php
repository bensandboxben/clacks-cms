<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$parent = $db->get_row("SELECT * FROM slgcl.terms WHERE termid='$_REQUEST[parentid]'");
if ($_GET["termid"]) {
	$term = $db->get_row("SELECT * FROM slgcl.terms WHERE termid='$_REQUEST[termid]'");
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
<h3><?php if ($_REQUEST["parentid"]) { echo 'Add'; } else { echo 'Edit'; }?> SLGCL Term</h3>
<?php
if ($_POST["submit"]) {
	$errors = array();
	if (!$_POST["term"]) $errors[] = "Please enter a term";
	if (is_array($errors) && count($errors) == 0) { {
		if ($_POST["termid"]) {
			$db->query("UPDATE slgcl.terms SET term='$_POST[term]' WHERE termid='$_POST[termid]'");
		} else {
			$db->query("INSERT INTO slgcl.terms (term, preferred, category) VALUES('$_POST[term]', 1, 1)");
			if ($_POST["parentid"] != "top") {
				$newtermid = $db->insert_id;
				$db->query("INSERT INTO slgcl.broaderterms (termid, broadertermid) VALUES('$newtermid', '$_POST[parentid]')");
			}
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
		echo '<p class="form-error-intro" role="alert">The term was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
?>
<p><?php if ($_REQUEST["parentid"]) { echo 'Adding term to parent'; } else { echo 'Editing term'; }?>: <?php if ($_REQUEST["parentid"] == "top") { echo 'Top level'; } else { echo $parent->term; } ?></p>
<div id="formarea">
<form name="slgclterm" method="post" action="addslgclterm.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Term:</td>
<td><input type="text" id="term" name="term" value="<?php echo $term->term;?>" maxlength="250" style="width: 200px;" /><input type="text" id="a" name="a" value="" maxlength="1" style="width: 0;" /></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="submit" value="Save" /> <input type="submit" class="btn btn-md btn-secondary" value="Cancel" onclick="javascript: window.close();" />
<input type="hidden" name="parentid" value="<?php echo $_REQUEST["parentid"]?>" />
<input type="hidden" name="termid" value="<?php echo $_REQUEST["termid"]?>" />
</td></tr>
</table>
</form>
</div>
<script>
document.slgclterm.term.focus();
</script>
</body>
</html>