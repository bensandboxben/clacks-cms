<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$license = $db->get_row("SELECT * FROM licenses WHERE licid='$_REQUEST[licid]'");
if (isset($_GET["del"])) {
	// delete form relationship
	$db->query("DELETE FROM pageXform WHERE pageid='" . LICENSE_SCRIPT_ID . "' AND formid='$_GET[del]' AND qualifier='$license->licurl'");
}
if ($_POST["addbtn"]) {
	// save form relationship
	$db->query("INSERT INTO pageXform (pageid, formid, qualifier) VALUES('" . LICENSE_SCRIPT_ID . "', '$_POST[relform]', '$license->licurl')");
}
$curr_forms = $db->get_results("SELECT pageXform.*, forms.name FROM pageXform LEFT JOIN forms USING(formid) WHERE pageid='" . LICENSE_SCRIPT_ID . "' AND qualifier='$license->licurl' ORDER BY name");
$instr = "";
if (is_array($curr_forms)) {
	foreach ($curr_forms as $form) {
		$instr .= $form->formid . ',';
	}
}
$instr .= '0';
$query = 'SELECT formid, name FROM forms WHERE formid NOT IN (' . $instr . ')';
if ($_POST["clearfilter"]) unset($_SESSION["filters"]["licenseforms"]);
if ($_POST["filter"]) $_SESSION["filters"]["licenseforms"] = $_POST["filter"];
if (isset($_SESSION["filters"]["licenseforms"])) $query .= ' AND name LIKE "%' . $_SESSION["filters"]["licenseforms"] . '%"';
$query .= ' ORDER BY name';
$forms = $db->get_results($query, ARRAY_N);
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<h3>Add related form</h3>
<p>Adding related forms to license: <?php echo $license->name; ?></p>
<form method="post" action="licenseforms.php">
<p>Filter: <input type="text" name="filter" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="filterbtn" value="Filter" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="clearfilter" value="Clear" /></p>
<table class="table table-striped table-bordered">
<tr><td>Form to link to</td><td>Action</td></tr>
<tr><td>
<?php build_select_box($forms, "relform", "", " "); ?>
</td>
<td><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="addbtn" value="+" /></td>
</tr>
<?php
if (is_array($curr_forms) && count($curr_forms) > 0) {
    echo '<tr><td><h4>Forms currently linked:</h4></td></tr>';
    foreach ($curr_forms as $curr_form) {
        // Sanitize and encode the output to prevent HTML injection
        $formName = isset($curr_form->name) ? htmlspecialchars($curr_form->name) : 'Undefined Name';
        $formId = isset($curr_form->formid) ? htmlspecialchars($curr_form->formid) : '';

        echo '<tr><td>' . $formName . '</td>';
        echo '<td><a href="licenseforms.php?del=' . $formId . '&licid=' . htmlspecialchars($_REQUEST["licid"]) . '">Unlink</a></td></tr>';
    }
}

?>
</table>
<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="close" value="Close" onclick="window.close();" /></p>
<input type="hidden" name="licid" value="<?php echo $_REQUEST["licid"]; ?>" />
</form>
</body>
</html>