<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$doc_array = $db->get_row("SELECT * FROM docs WHERE docid='$_GET[id]'", ARRAY_A);
	extract($doc_array);
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include "includes/head.inc.php"; ?>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script>
  $(function() {
    $( "#datepicker" ).datepicker();
  });
  </script>
</head>

<body class="popup">
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="calendar-win2k-cold-1.css" title="win2k-cold-1" />
<!-- main calendar program -->
<script type="text/javascript" src="js/calendar.js"></script>
<!-- language for the calendar -->
<script type="text/javascript" src="js/calendar-en.js"></script>
<!-- the following script defines the Calendar.setup helper function, which makes adding a calendar a matter of 1 or 2 lines of code. -->
<script type="text/javascript" src="js/calendar-setup.js"></script>
<?php if ($edit == 1) { ?>
<h3>Edit document</h3>
<?php } else { ?>
<h3>Add new document</h3>
<?php
}
if ($_POST["save"]) {

	$_POST["reviewdate"] = date("Y-m-d", strtotime($_POST["reviewdate"]));

if (($_POST["reviewdate"] == '0000-00-00')||($_POST["reviewdate"] == '1970-01-01')||($_POST["reviewdate"] == '')||(!isset($_POST["reviewdate"]))) $_POST["reviewdate"] = "0000-00-00";




	$errors = array();
	if (!$_POST["name"]) $errors[] = "Please enter the name of this document";
	if (!$_POST["doctypeid"]) $errors[] = "Please enter a document type";

	$query = "SELECT docid FROM docs WHERE name='" . addslashes($_POST["name"]) . "'";
	if ($_POST["docid"]) $query .= " AND docid != " . $_POST["docid"];
	if ($exists = $db->get_var($query)) $errors[] = "A document already exists with this name";
	if (!$_POST["catid"]) $errors[] = "Please select a category for this document";
	if (!$_POST["description"]) $errors[] = "Please enter a brief description for this document";
	if ((!$_POST["reviewdate"])||($_POST["reviewdate"] == '0000-00-00')||($_POST["reviewdate"] == '')) $_POST["reviewdate"] = date('Y-m-d', mktime (0,0,0,date("m")+3,date("d"),  date("Y")));
	if (count($errors) == 0) {
		$nameurl = strtolower(preg_replace("/[^\p{L}\p{N}]/u", "", $_POST["name"]));

	$htmldesc = $_POST['htmldesc'] ?? ''; // Set default value if not set
	$live = $_POST['live'] ?? ''; // Set default value if not set
	$internal = $_POST['internal'] ?? ''; // Set default value if not set

	if ($htmldesc === null || $htmldesc === '') {
	    $htmldesc = '0'; // Replace null or empty string with '0'
	}
	if ($live === null || $live === '') {
	    $live = '0'; // Replace null or empty string with '0'
	}
	if ($internal === null || $internal === '') {
	    $internal = '0'; // Replace null or empty string with '0'
	}

    $name = addslashes($_POST["name"]);
    $nameurl = addslashes($nameurl);
    $shortdesc = addslashes($_POST["shortdesc"]);
    $description = addslashes($_POST["description"]);
    $htmldesc = addslashes($htmldesc);
    $live = addslashes($live);
    $internal = addslashes($internal);
    $reviewdate = addslashes($_POST["reviewdate"]);

	if ($_POST["docid"]) {
    		$query = "UPDATE docs SET serviceid='{$_POST["serviceid"]}', sectionid='{$_POST["sectionid"]}', catid='{$_POST["catid"]}', doctypeid='{$_POST["doctypeid"]}', name='$name', nameurl='$nameurl', shortdesc='$shortdesc', description='$description', htmldesc='$htmldesc', live='$live', internal='$internal', reviewdate='$reviewdate', modified=UNIX_TIMESTAMP() WHERE docid='{$_POST["docid"]}'";
    		$db->query($query);
	   	update_atoz($_POST["atoz"], "D", $_POST["docid"], $live);
	} else {
    		$query = "INSERT INTO docs (serviceid, sectionid, catid, doctypeid, name, nameurl, shortdesc, description, internal, htmldesc, live, reviewdate, created, modified) VALUES('{$_POST["serviceid"]}', '{$_POST["sectionid"]}', '{$_POST["catid"]}', '{$_POST["doctypeid"]}', '$name', '$nameurl', '$shortdesc', '$description', '$internal', '$htmldesc', '$live', '$reviewdate', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())";
    		$db->query($query);
	    	$newdocid = $db->insert_id;
	    	update_atoz($_POST["atoz"], "D", $newdocid, $live);
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
		echo '<p class="form-error-intro" role="alert">The document was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
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
var s = document.doc.elements["catid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>
<form name="doc" method="post" action="newdoc.php">
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
$sections = $db->get_results("SELECT sectionid, sectiontitle FROM sections WHERE `sectiontitle` NOT LIKE '%intranet%' ORDER BY sectiontitle", ARRAY_N);
build_select_box($sections, "sectionid", $sectionid, '-');
?>
</td></tr>
<tr><td class="label">Category:</td>
<td>
<?php
$categories = $db->get_results("SELECT catid, category FROM clacksweb2.categories ORDER BY category", ARRAY_N);
build_select_box($categories, "catid", $catid, '-');
?>
<a href="#" onClick="window.open('newcat.php', 'cat_new', 'width=500,height=300,resizable=yes,scrollbars=yes,top=50,left=50')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new category</span></a>
</td></tr>
<tr><td class="label">Document type:</td>
<td>
<?php
$doctypes = $db->get_results("SELECT doctypeid, doctype FROM clacksweb2.doctypes ORDER BY doctype", ARRAY_N);
build_select_box($doctypes, "doctypeid", $doctypeid, '-');
?>
</td></tr>
<tr><td class="label">Document name:</td>
<td><input type="text" name="name" value="<?php echo $name; ?>" maxlength="200" style="width: 300px;" /></td></tr>
<tr><td class="label">Short description:</td>
<td><textarea name="shortdesc" style="width: 300px; height: 80px;"><?php echo $shortdesc; ?></textarea></td></tr>
<tr><td class="label">Description:</td>
<td><textarea name="description" style="width: 300px; height: 80px;"><?php echo $description; ?></textarea></td></tr>
<tr><td class="label">HTML description:</td>
<td>
<?php
build_yes_no_checkbox("htmldesc", $htmldesc);
?>
</td></tr>
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
$entries = $db->get_results("SELECT alpha FROM atoz WHERE id='$docid' AND element='D'");
build_atoz_box($entries);
?>
</td></tr>
<tr><td colspan="2" class="formbtns">
<input class="btn btn-md btn-primary" type="submit" name="save" value="Save" /> <input class="btn btn-md btn-secondary" type="submit" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($docid) echo '<input type="hidden" name="docid" value="' . $docid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.doc.serviceid.focus();
</script>
</body>
</html>