<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$consultation_array = $db->get_row("SELECT * FROM consultations WHERE conid='$_GET[conid]'", ARRAY_A);
	extract($consultation_array);
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
<script type="text/javascript">
//<![CDATA[
	function myOnSubmitEventHandler() {
		try {
			if(typeof(document.getElementById('editor1').EscapeUnicode) == 'undefined') {
				throw "Error"
			} else {
				document.getElementById('editor1').EscapeUnicode = true;
				document.getElementById('body').value = document.getElementById('editor1').value;
			}
		}
		catch(er) {
			document.getElementById('body').value = document.getElementById('alternate1').value;
		}
	}
//]]>
</script>
<?php if ($edit == 1) { ?>
<h3>Edit consultation</h3>
<?php } else { ?>
<h3>Add new consultation</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	$body = $_POST["body"];
	if (!$_POST["subject"]) $errors[] = "Please enter a subject";
	if (!$_POST["startdate"]) $errors[] = "Please enter the start date of this consultation";
	if (!$_POST["enddate"]) $errors[] = "Please enter the end date of this consultation";
	if (($_POST["startdate"])&&($_POST["enddate"])&&($_POST["startdate"] > $_POST["enddate"])) $errors[] = "End date is before start date";
	if (!$_POST["body"]) $errors[] = "Please enter some text";
	if (count($errors) == 0) {
		if ($_POST["conid"]) {
			$db->query("UPDATE consultations SET subject='$_POST[subject]', startdate='$_POST[startdate]', enddate='$_POST[enddate]', body='$body', catid='$_POST[catid]', modified=UNIX_TIMESTAMP() WHERE conid='$_POST[conid]'");
		} else {
			$db->query("INSERT INTO consultations (subject, startdate, enddate, body, catid, created, modified) VALUES('$_POST[subject]', '$_POST[startdate]', '$_POST[enddate]', '$body', '$_POST[catid]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
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
		echo '<p class="form-error-intro" role="alert">The consultation was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
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
<script language="JavaScript">
<!--
function doAddCat (label, value) {
var s = document.consultation.elements["catid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>
<form name="consultation" method="post" action="newconsultation.php" onsubmit="myOnSubmitEventHandler()">
<table width="100%">
<tr><td class="label">Subject:</td>
<td><input type="text" name="subject" value="<?php echo $subject; ?>" maxlength="250" style="width: 200px;" /></td></tr>
<tr><td class="label">Category:</td>
<td>
<?php
$consultationcats = $db->get_results("SELECT catid, category FROM categories ORDER BY category", ARRAY_N);
build_select_box($consultationcats, "catid", $catid);
?>
<a href="#" onClick="window.open('newcat.php', 'cat_new', 'width=500,height=300,resizable=yes,scrollbars=yes,top=50,left=50')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new category</span></a>
</td></tr>
<tr><td class="label">Body:</td>
<td>&nbsp;</td></tr>
<tr><td colspan="2">
<object type="application/x-xstandard" id="editor1" width="100%" height="400" codebase="http://clacksweb/cms/XStandard.cab#Version=1,7,1,0">
	<param name="Value" value="<?php echo stripslashes(htmlspecialchars($body, ENT_COMPAT)) ?>" />
	<param name="Base" value="http://clacksweb/" />
	<param name="CSS" value="http://clacksweb/cms/css/devedit.css" />
	<param name="Styles" value="http://clacksweb/cms/styles.xml" />
	<param name="ImageLibraryURL" value="http://clacksweb/cms/xs/imagelibrary.php" />
	<param name="SpellCheckerURL" value="http://clacksweb/cms/xs/spell.php" />
	<param name="DirectoryURL" value="http://clacksweb/cms/xs/directory.php" />
	<param name="License" value="http://clacksweb/cms/license.txt" />
	<param name="ClassImageFloatLeft" value="left" />
	<param name="ClassImageFloatRight" value="right" />
	<param name="EnablePasteMarkup" value="yes" />
	<param name="Toolbar" value="numbering, bullets, , data-table, image, line, hyperlink, directory, spellchecker, undo, , wysiwyg, source, preview, screen-reader" />
	<param name="EnableTimestamp" value="no" />
	<param name="Options" value="160" />
	<textarea name="alternate1" id="alternate1" cols="60" rows="15"><?php echo stripslashes(htmlspecialchars($body, ENT_COMPAT)) ?></textarea>
</object>
<input type="hidden" name="body" id="body" value="" />
</td></tr>
<tr><td class="label">Dates:</td>
<td><input type="text" name="startdate" value="<?php echo $startdate; ?>" maxlength="10" style="width: 100px;" />
<button type="reset" id="f_trigger_b">...</button>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "startdate",      // id of the input field
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        showsTime      :    false,            // will display a time selector
        button         :    "f_trigger_b",   // trigger for the calendar (button ID)
        singleClick    :    true,           // double-click mode
        step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    });
</script>
<input type="text" name="enddate" value="<?php echo $enddate; ?>" maxlength="10" style="width: 100px;" />
<button type="reset" id="f_trigger_c">...</button>
</td></tr>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "enddate",      // id of the input field
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        showsTime      :    false,            // will display a time selector
        button         :    "f_trigger_c",   // trigger for the calendar (button ID)
        singleClick    :    true,           // double-click mode
        step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    });
</script>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-secondary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($conid) echo '<input type="hidden" name="conid" value="' . $conid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.contact.serviceid.focus();
</script>
</body>
</html>