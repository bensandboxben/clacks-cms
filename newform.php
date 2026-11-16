<?php


//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$form_array = $db->get_row("SELECT * FROM forms WHERE formid='$_GET[id]'", ARRAY_A);
	extract($form_array);
}
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>


  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script>
  $(function() {
    $( "#datepicker" ).datepicker();
  });
  </script>


<body bgcolor="#FFFFFF" class="popup">
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="calendar-win2k-cold-1.css" title="win2k-cold-1" />
<!-- main calendar program -->
<script type="text/javascript" src="js/calendar.js"></script>
<!-- language for the calendar -->
<script type="text/javascript" src="js/calendar-en.js"></script>
<!-- the following script defines the Calendar.setup helper function, which makes adding a calendar a matter of 1 or 2 lines of code. -->
<script type="text/javascript" src="js/calendar-setup.js"></script>
<?php if ($edit == 1) { ?>
<h3>Edit form</h3>
<?php } else { ?>
<h3>Add new form</h3>
<?php
}
if ($_POST["save"]) {


	$_POST["reviewdate"] = date("Y-m-d", strtotime($_POST["reviewdate"]));

if (($_POST["reviewdate"] == '0000-00-00')||($_POST["reviewdate"] == '1970-01-01')||($_POST["reviewdate"] == '')||(!isset($_POST["reviewdate"]))) $_POST["reviewdate"] = "0000-00-00";

echo $_POST["reviewdate"];

	$errors = array();
	if (!$_POST["name"]) $errors[] = "Please enter the name of this form";
	$query = "SELECT formid FROM forms WHERE name='" . addslashes($_POST["name"]) . "'";
	if ($_POST["formid"]) $query .= " AND formid != " . $_POST["formid"];
	if ($exists = $db->get_var($query)) $errors[] = "A form already exists with this name";
	if (!$_POST["catid"]) $errors[] = "Please select a category for this document";
	if (!$_POST["description"]) $errors[] = "Please enter a brief description for this form";
	if ((!$_POST["reviewdate"])||($_POST["reviewdate"] == '0000-00-00')||($_POST["reviewdate"] == '')) $_POST["reviewdate"] = date('Y-m-d', mktime (0,0,0,date("m")+3,date("d"),  date("Y")));


	if (count($errors) == 0) {
		$nameurl = strtolower(preg_replace("/[^\p{L}\p{N}]/u", "", $_POST["name"]));

		$htmldesc = $_POST['htmldesc'] ?? ''; // Set default value if not set		
		if ($htmldesc === null || $htmldesc === '') {
		    $htmldesc = '0'; // Replace null or empty string with '0'
		}
		$online = $_POST['online'] ?? ''; // Set default value if not set		
		if ($online === null || $online === '') {
		    $online = '0'; // Replace null or empty string with '0'
		}
		$internal = $_POST['internal'] ?? ''; // Set default value if not set		
		if ($internal === null || $internal === '') {
		    $internal = '0'; // Replace null or empty string with '0'
		}
		$live = $_POST['live'] ?? ''; // Set default value if not set		
		if ($live === null || $live === '') {
		    $live = '0'; // Replace null or empty string with '0'
		}

		if ($_POST["formid"]) {

			$shortdesc = addslashes($_POST["shortdesc"]);
			$description = addslashes($_POST["description"]);

			$db->query("UPDATE forms SET serviceid='$_POST[serviceid]', sectionid='$_POST[sectionid]', catid='$_POST[catid]', name='$_POST[name]', nameurl='$nameurl', shortdesc='$shortdesc', description='$description', htmldesc='$htmldesc', online='$online', internal='$internal', live='$live', reviewdate='$_POST[reviewdate]', modified=UNIX_TIMESTAMP() WHERE formid='$_POST[formid]'");
			update_atoz($_POST["atoz"], "F", $_POST["formid"], $live);
		} else {

			$shortdesc = addslashes($_POST["shortdesc"]);
			$description = addslashes($_POST["description"]);

			$db->query("INSERT INTO forms (serviceid, sectionid, catid, name, nameurl, shortdesc, description, htmldesc, online, internal, live, reviewdate, created, modified) VALUES('$_POST[serviceid]', '$_POST[sectionid]', '$_POST[catid]', '$_POST[name]', '$nameurl', '$shortdesc', '$description', '$htmldesc', '$online', '$internal', '$live', '$_POST[reviewdate]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			$newformid = $db->insert_id;
			update_atoz($_POST["atoz"], "F", $newformid, $live);
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
		echo '<p>The form was not saved. Please correct these errors and resubmit the form:</p><ul>';
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
var s = document.form.elements["catid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>
<form name="form" method="post" action="newform.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Service:</td>
<td>
<?php
if ($_SESSION["userlevel"] < 4) {
	$services = $db->get_results("SELECT serviceid, service FROM cms.services WHERE serviceid='$_SESSION[userserviceid]' ORDER BY service", ARRAY_N);
} else {
	$services = $db->get_results("SELECT serviceid, service FROM cms.services ORDER BY service", ARRAY_N);
}
build_select_box($services, "serviceid", $serviceid);
?>
</td></tr>
<tr><td class="label">ClacksWeb Section:</td>
<td>
<?php
$sections = $db->get_results("SELECT sectionid, sectiontitle FROM clacksweb2.sections ORDER BY sectiontitle", ARRAY_N);
build_select_box($sections, "sectionid", $sectionid, '-');
?>
</td></tr>
<tr><td class="label">Category:</td>
<td>
<?php
$categories = $db->get_results("SELECT catid, category FROM clacksweb2.categories ORDER BY category", ARRAY_N);
build_select_box($categories, "catid", $catid, '-');
?>
<a href="#" onClick="window.open('newcat.php', 'cat_new', 'width=500,height=300,resizable=yes,scrollbars=yes,top=50,left=50')"><img src="images/add.gif" alt="Add new category" border="0" /></a>
</td></tr>
<tr><td class="label">Form name:</td>
<td><input type="text" name="name" value="<?php echo $name; ?>" maxlength="200" style="width: 300px;" /></td></tr>
<tr><td class="label">Short description:</td>
<td><textarea name="shortdesc" style="width: 300px; height: 80px;"><?php echo $shortdesc; ?></textarea></td></tr>
<tr><td class="label">Description:</td>
<td><textarea name="description" style="width: 300px; height: 120px;"><?php echo $description; ?></textarea></td></tr>
<tr><td class="label">HTML description:</td>
<td>
<?php
build_yes_no_checkbox("htmldesc", $htmldesc);
?>
</td></tr>
<tr><td class="label">Online service:</td>
<td><input type="text" name="online" value="<?php echo $online; ?>" maxlength="200" style="width: 300px;" /></td></tr>
<tr><td class="label">Internal only:</td>
<td>
<?php
build_yes_no_checkbox("internal", $internal);
?>
</td></tr>
<tr><td class="label">Live:</td>
<td>
<?php
build_yes_no_checkbox("live", $live);
?>
</td></tr>

<tr>
	<td class="label">Review Date:</td>
		<td>
			<input id="datepicker" type="text" name="reviewdate" value="<?php echo $reviewdate; ?>" maxlength="10" style="width: 100px;" />
		</td>
</tr>

<tr><td class="label">A-Z entries: </td><td>
<?php
$entries = array();
$entries = $db->get_results("SELECT alpha FROM atoz WHERE id='$formid' AND element='F'");
build_atoz_box($entries);
?>
</td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($formid) echo '<input type="hidden" name="formid" value="' . $formid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.form.serviceid.focus();
</script>
</body>
</html>