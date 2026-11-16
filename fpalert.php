<?php

include_once "includes/prepend.inc.php";
$db = new db("admin", "HbCLlVH", "clacksweb2data", "164.177.128.68");
/*if (check_auth("fpalert", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}*/
header("Cache-Control: private");
if ($_POST["save"]) {
	$db->query("UPDATE clacksweb2data.fpalert SET title='$_POST[title]', alert='$_POST[alert]', status='$_POST[status]', showtimestamp='$_POST[showtimestamp]', modified=UNIX_TIMESTAMP()");
}
$fpalert = $db->get_row("SELECT * FROM clacksweb2data.fpalert");
?>
<head>
<title>ClacksWeb CMS</title>
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<script type="text/javascript">
//<![CDATA[
	function myOnSubmitEventHandler() {
		try {
			if(typeof(document.getElementById('editor1').EscapeUnicode) == 'undefined') {
				throw "Error"
			} else {
				document.getElementById('editor1').EscapeUnicode = true;
				document.getElementById('alert').value = document.getElementById('editor1').value;
			}
		}
		catch(er) {
			document.getElementById('alert').value = document.getElementById('alternate1').value;
		}
	}
//]]>
</script>
<div id="formarea">
<h3>Edit Frontpage Alert</h3>
<form name="editfpalert" action="fpalert.php" method="post" onsubmit="myOnSubmitEventHandler()">
<table width="100%">
<tr><td class="label">Title:</td>
<td><input type="text" name="title" value="<?php echo $fpalert->title; ?>" maxlength="200" style="width: 300px;" /></td></tr>
<tr><td class="label">Status:</td>
<td><select name="status" id="status">
<?php
$status_array=array("Draft", "Test", "Live");
foreach ($status_array as $status) {
	echo '<option value="' . $status . '"';
	if ($fpalert->status == $status) echo ' selected="selected"';
	echo '>' . $status . '</option>';
}
?>
</select> <span style="font-size: 0.8em;">(Draft = invisible; Test = visible on local ClacksWeb; Live = visible to public)</span>
</td></tr>

<tr>
	<td class="label">Show Timestamp:</td>
	<td>
		<?php build_yes_no_checkbox("showtimestamp", $showtimestamp); ?>
	</td>
</tr>




<tr><td class="label">Alert:</td>
<td>
<object type="application/x-xstandard" id="editor1" width="100%" height="400" codebase="http://clacksweb/cms/XStandard.cab#Version=1,7,1,0">
	<param name="Value" value="<?php echo stripslashes(htmlspecialchars($fpalert->alert, ENT_COMPAT, 'iso-8859-1')) ?>" />
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
	<textarea name="alternate1" id="alternate1" cols="60" rows="15"><?php echo stripslashes(htmlspecialchars($fpalert->alert, ENT_COMPAT, 'iso-8859-1')) ?></textarea>
</object>
<input type="hidden" name="alert" id="alert" value="" />
</td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Close" onclick="javascript: window.close();" />
</td></tr>
</table>
</form>
</div>
</body>
</html>