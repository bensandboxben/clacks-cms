<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$curr_forms = get_np_var('relforms');
if (isset($_GET["del"])) {
	unset($curr_forms[$_GET["del"]]);
	set_np_var('relforms', $curr_forms);
}
if ($_POST["addbtn"]) {
	$curr_forms[] = array("formid"=>$_POST["relform"]);
	set_np_var('relforms', $curr_forms);
}
$instr = "";
if (is_array($curr_forms)) {
	foreach ($curr_forms as $form) {
		$instr .= $form["formid"] . ',';
	}
}
$instr .= '0';
$query = 'SELECT formid, name FROM forms WHERE formid NOT IN (' . $instr . ')';
if ($_POST["clearfilter"]) unset($_SESSION["filters"]["relforms"]);
if ($_POST["filter"]) $_SESSION["filters"]["relforms"] = $_POST["filter"];
if (isset($_SESSION["filters"]["relforms"])) $query .= ' AND name LIKE "%' . $_SESSION["filters"]["relforms"] . '%"';
$query .= ' ORDER BY name';
$relforms = $db->get_results($query, ARRAY_N);
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include "includes/head.inc.php"; ?>
</head>
<body class="popup">
<h3>Add related form</h3>
<p>Adding related forms to page: <?php echo get_np_var('title'); ?></p>
<form method="post" action="addrelform.php">
<p>Filter: <input type="text" name="filter" /> <input type="submit" class="btn btn-md btn-primary" name="filterbtn" value="Filter" /> <input type="submit" class="btn btn-md btn-secondary" name="clearfilter" value="Clear" /></p>
<table class="table table-striped table-bordered">
<tr><td>Form to link to</td><td>Action</td></tr>
<tr><td>
<?php build_select_box($relforms, "relform", ""); ?>
</td>
<td><input type="submit" class="btn btn-md btn-primary" name="addbtn" value="+" /></td>
</tr>
<?php
if (is_array($curr_forms) && count($curr_forms) > 0) {
	foreach ($curr_forms as $key=>$curr_form) {
		$name = $db->get_var("SELECT name FROM forms WHERE formid='{$curr_form["formid"]}'");
		echo '<tr><td>' . $name . '</td><td><a href="addrelform.php?del=' . $key . '"><svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">Delete</span></a></td></tr>';
	}
}
?>
</table>
<p class="formbtns"><input type="submit" class="btn btn-md btn-secondary" name="saveforms" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p></form>
</body>
</html>