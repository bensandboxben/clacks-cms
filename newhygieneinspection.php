<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
$grades=array("1"=>"Good", "2"=>"Average", "3"=>"Poor");
if ($_GET["action"] == "edit") {
	$edit = 1;
	$inspection_array = $db->get_row("SELECT * FROM hygieneinspections WHERE inspectionid='$_GET[id]'", ARRAY_A);
	extract($inspection_array);
} else if ($_GET["action"] == "copy") {
	$inspection_array = $db->get_row("SELECT * FROM hygieneinspections WHERE inspectionid='$_GET[id]'", ARRAY_A);
	extract($inspection_array);
	unset($inspectionid);
}
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<script language="JavaScript">
<!--
function doAddEstablishment (label, value) {
var s = document.inspection.elements["establishmentid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
function doAddCat (label, value) {
var s = document.inspection.elements["catid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>
<?php if ($edit == 1) { ?>
<h3>Edit inspection</h3>
<?php } else { ?>
<h3>Add new inspection</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	$_POST["summary"] = stripslashes($_POST["summary"]);
	if (!$_POST["establishmentid"]) $errors[] = "Please select an establishment";
	if (!$_POST["catid"]) $errors[] = "Please select a category";
	if (!$_POST["grade"]) $errors[] = "Please grade this inspection";
	if (!$_POST["summary"]) $errors[] = "Please enter a brief summary for this inspection";
	if (!$_POST["inspectiondate"]) $errors[] = "Please enter a date for this inspection";
	if (count($errors) == 0) {
		if ($_POST["inspectionid"]) {
			$db->query("UPDATE hygieneinspections SET establishmentid='$_POST[establishmentid]', catid='$_POST[catid]', grade='$_POST[grade]', inspectiondate='$_POST[inspectiondate]', summary='$_POST[summary]', actionpending='$_POST[actionpending]', modified=UNIX_TIMESTAMP() WHERE inspectionid='$_POST[inspectionid]'");
			$fileid = $_POST["inspectionid"];
		} else {
			$db->query("INSERT INTO hygieneinspections (establishmentid, catid, grade, inspectiondate, summary, actionpending, modified, created) VALUES('$_POST[establishmentid]', '$_POST[catid]', '$_POST[grade]', '$_POST[inspectiondate]', '$_POST[summary]', '$_POST[actionpending]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			$fileid = $db->insert_id;
		}
		if ($_FILES['inspectionfile']['name']) {
			$destination_filename = $fileid . ".rtf";
			move_uploaded_file($_FILES['inspectionfile']['tmp_name'], "/www/cwv2/document/hygieneinspections/" . $destination_filename);
			chmod("/www/cwv2/document/hygieneinspections/" . $destination_filename, 0644);
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
		echo '<p>The inspection was not saved. Please correct these errors and resubmit the form:</p><ul>';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
extract ($_POST);
?>
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="calendar-win2k-cold-1.css" title="win2k-cold-1" />
<!-- main calendar program -->
<script type="text/javascript" src="js/calendar.js"></script>
<!-- language for the calendar -->
<script type="text/javascript" src="js/calendar-en.js"></script>
<!-- the following script defines the Calendar.setup helper function, which makes adding a calendar a matter of 1 or 2 lines of code. -->
<script type="text/javascript" src="js/calendar-setup.js"></script>
<div id="formarea">
<form name="inspection" method="post" action="newhygieneinspection.php" enctype="multipart/form-data">
<table class="table table-striped table-bordered">
<tr><td class="label">Establishment:</td>
<td>
<?php
$establishments = $db->get_results("SELECT establishmentid, establishment FROM hiestablishments ORDER BY establishment", ARRAY_N);
build_select_box($establishments, "establishmentid", $establishmentid);
?>
<a href="#" onClick="window.open('newestablishment.php', 'establishment_new', 'width=500,height=350,resizable=yes,scrollbars=yes,top=50,left=50')"><img src="images/add.gif" alt="Add new establishment" border="0" /></a>
</td></tr>
<tr><td class="label">Category:</td>
<td>
<?php
$hicats = $db->get_results("SELECT catid, category FROM hicategories ORDER BY category", ARRAY_N);
build_select_box($hicats, "catid", $catid);
?>
<a href="#" onClick="window.open('newhicat.php', 'hicat_new', 'width=500,height=300,resizable=yes,scrollbars=yes,top=50,left=50')"><img src="images/add.gif" alt="Add new category" border="0" /></a>
</td></tr>
<tr><td class="label">Grade:</td>
<td><select name="grade">
<?php
foreach ($grades as $key=>$value) {
	echo '<option value="' . $key . '"';
	if ($grade == $key) echo ' selected="selected"';
	echo '>' . $value . '</option>';
}
?>
</select>
</td></tr>
<tr><td class="label">Action pending (hide grade):</td>
<td>
<?php
build_yes_no_checkbox("actionpending", $actionpending);
?>
</td></tr>
<tr><td class="label">Inspection date:</td>
<td><input type="text" name="inspectiondate" value="<?php echo $inspectiondate; ?>" maxlength="10" style="width: 100px;" />
<button type="reset" id="f_trigger_b">...</button>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "inspectiondate",      // id of the input field
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        showsTime      :    false,            // will display a time selector
        button         :    "f_trigger_b",   // trigger for the calendar (button ID)
        singleClick    :    true,           // double-click mode
        step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    });
</script>
</td></tr>
<tr><td class="label">Summary:</td>
<td><textarea name="summary" style="width: 300px; height: 60px;"><?php echo $summary; ?></textarea></td></tr>
<tr><td class="label">Inspection file (RTF only): </td><td><input type="file" name="inspectionfile">
<?php
	if (($inspectionid)&&(file_exists("/www/cwv2/document/hygieneinspections/" . $inspectionid . ".rtf"))) {
		echo '<br />[ <a href="http://clacksweb/document/hygieneinspections/' . $inspectionid . '.rtf" target="_blank">View existing</a> ]';
	}
?>
</td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($inspectionid) echo '<input type="hidden" name="inspectionid" value="' . $inspectionid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.inspection.establishmentid.focus();
</script>
</body>
</html>