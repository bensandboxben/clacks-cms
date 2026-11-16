<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$div_array = $db->get_row("SELECT * FROM hr.divisions WHERE divid='$_GET[divid]'", ARRAY_A);
	extract($div_array);
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
<h3>Edit division</h3>
<?php } else { ?>
<h3>Add new division</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	if (($found = $db->get_var("SELECT divid FROM hr.divisions WHERE servicecode='$_POST[servicecode]' AND division='$_POST[division]'")) &!($edit)) {
			$errors[] = "That division already exists";
	}
	if (count($errors) == 0) {
		$service = $db->get_var("SELECT service FROM hr.services WHERE servicecode='$_POST[servicecode]'");
		if ($_POST["divid"]) {
			$db->query("UPDATE hr.divisions SET servicecode='$_POST[servicecode]', division='$_POST[division]', url='$_POST[url]' WHERE divid='$_POST[divid]'");
		} else {
			$db->query("INSERT INTO hr.divisions (servicecode, division, url) VALUES('$_POST[servicecode]', '$_POST[division]', '$_POST[url]')");
			$newdivid = $db->insert_id;
		}
		if ($_POST["divid"]) {
?>
<script language="JavaScript">
<!--
self.opener.updateDivision('<?php echo $_POST["index"]; ?>', '<?php echo $service . ' -> ' . $_POST["division"]; ?>');
self.close();
//-->
</script>
<?php } else { ?>
<script language="JavaScript">
<!--
self.opener.doAddDivision('<?php echo $service . ' -> ' . $_POST["division"]; ?>', '<?php echo $newdivid; ?>');
self.close();
//-->
</script>
<?php
	}
		} else {
			echo '<p>The division was not saved. Please correct these errors and resubmit the form:</p><ul>';
			foreach ($errors as $error) {
				echo '<li>' . $error . '</li>';
			}
			echo '</ul>';
		}
	}
extract ($_POST);
?>
<div id="formarea">
<form name="frmdivision" method="post" action="newhrdivision.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Service:</td>
<td>
<?php
$services = $db->get_results("SELECT servicecode, service FROM hr.services ORDER BY service", ARRAY_N);
build_select_box($services, "servicecode", $servicecode);
?>
</td></tr>
<tr><td class="label">Division:</td>
<td><input type="text" name="division" value="<?php echo $division; ?>" maxlength="50" style="width: 300px;" /></td></tr>
<tr><td class="label">URL:</td>
<td><input type="text" name="url" value="<?php echo $url; ?>" maxlength="200" style="width: 300px;" /></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($divid) echo '<input type="hidden" name="divid" value="' . $divid . '" />'; ?>
<?php if ($_REQUEST["index"]) echo '<input type="hidden" name="index" value="' . $_REQUEST["index"] . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.frmdivision.service.focus();
</script>
</body>
</html>