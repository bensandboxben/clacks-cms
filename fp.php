<?php
/**
* Manage ClacksWeb frontpage features
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("fp", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: nocache");
if ($_POST["save"]) {
	$handle = fopen(UPLOAD_DIR . "/fp.html", "w");
	fputs($handle, stripslashes($_POST["fp"]));
	fclose($handle);
}
$fphtml = file_get_contents(UPLOAD_DIR . "/fp.html");
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
				document.getElementById('fp').value = document.getElementById('editor1').value;
			}
		}
		catch(er) {
			document.getElementById('fp').value = document.getElementById('alternate1').value;
		}
	}
//]]>
</script>
<div id="formarea">
<h3>Edit Front page</h3>
<form name="editfp" action="fp.php" method="post" onsubmit="myOnSubmitEventHandler()">
<table width="100%">
<tr><td class="label">Content:</td></tr>
<tr><td>
<object type="application/x-xstandard" id="editor1" width="100%" height="400" codebase="http://clacksweb/cms/XStandard.cab#Version=1,7,1,0">
	<param name="Value" value="<?php echo stripslashes(htmlspecialchars($fphtml, ENT_COMPAT)) ?>" />
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
	<param name="Toolbar" value="bullets, hyperlink, spellchecker, undo, , wysiwyg, source, preview, screen-reader" />
	<param name="EnableTimestamp" value="no" />
	<param name="Options" value="160" />
	<textarea name="alternate1" id="alternate1" cols="60" rows="15"><?php echo stripslashes(htmlspecialchars($fphtml, ENT_COMPAT)) ?></textarea>
</object>
<input type="hidden" name="fp" id="fp" value="" />
</td></tr>
<tr><td class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-secondary" value="Close" onclick="javascript: window.close();" />
</td></tr>
</table>
</form>
</div>
</body>
</html>