<?php
np_display_errors();
?>
<script language="JavaScript">
<!--
function editMenu() {
var menufile = document.content.np_menufile.value;
window.open('editmenu.php?menufile=' + menufile, 'editmenu', 'width=660,height=500,top=50,left=50,resizable=yes,scrollbars=yes');
}
function addMenu() {
window.open('editmenu.php?menufile=new', 'addmenu', 'width=660,height=500,top=50,left=50,resizable=yes,scrollbars=yes');
}
function editUserCSS() {
var cssfile = document.content.np_usercss.value;
window.open('editcss.php?cssfile=' + cssfile, 'editcss', 'width=660,height=500,top=50,left=50,resizable=yes,scrollbars=yes');
}
function addUserCSS() {
window.open('editcss.php?cssfile=new', 'addcss', 'width=660,height=500,top=50,left=50,resizable=yes,scrollbars=yes');
}
//-->
</script>
<script type="text/javascript">
//<![CDATA[
	function myOnSubmitEventHandler() {
		try {
			if(typeof(document.getElementById('editor1').EscapeUnicode) == 'undefined') {
				throw "Error"
			} else {
				document.getElementById('editor1').EscapeUnicode = true;
				document.getElementById('np_content').value = document.getElementById('editor1').value;
			}
		}
		catch(er) {
			document.getElementById('np_content').value = document.getElementById('alternate1').value;
		}
	}
//]]>
</script>
<form method="post" action="newintranetcontent.php" name="content" onsubmit="myOnSubmitEventHandler()">
<table width="800">
<tr><td class="label">Menu:</td>
<td>
<?php
$xscontent = get_np_var("content");
if ($handle = opendir(UPLOAD_DIR . '/includes/menus/')) {
	echo '<select name="np_menufile"><option value="">Default</option><option value="None"';
	if (get_np_var("menufile") == 'None') echo ' selected="selected"';
	echo '>None</option>';
	$menus = array();
	while (false !== ($file = readdir($handle))) {
		if (($file != ".")&&($file != "..")&&(!strpos($file, ".php"))) {
			$menus[] = $file;
		}
	}
	closedir($handle);
	sort($menus);
	foreach ($menus as $menu) {
		echo '<option value="' . $menu . '"';
		if (get_np_var("menufile") == $menu) echo ' selected="selected"';
		echo '>' . $menu . '</option>';
	}
	echo '</select>';
}
?>
<a href="#" onClick="editMenu()"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">Edit menu</span></a>
<a href="#" onClick="addMenu()"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add menu</span></a>
</td></tr>
<tr><td class="label">Additional CSS:</td>
<td>
<?php
if ($handle = opendir(UPLOAD_DIR . '/css/user/')) {
	echo '<select name="np_usercss"><option value="">None</option>';
	$csss = array();
	while (false !== ($file = readdir($handle))) {
		if (($file != ".")&&($file != "..")) {
			$csss[] = $file;
		}
	}
	sort($csss);
	foreach ($csss as $css) {
		echo '<option value="' . $css . '"';
		if (get_np_var("usercss") == $css) echo ' selected="selected"';
		echo '>' . $css . '</option>';
	}
	echo '</select>';
	closedir($handle);
}
?>
<a href="#" onClick="editUserCSS()"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">Edit style sheet</span></a>
<a href="#" onClick="addUserCSS()"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add style sheet</span></a>
</td></tr>
<tr><td class="label">Include domCollapse:</td>
<td>
<?php
build_yes_no_checkbox("np_domcollapse", get_np_var("domcollapse"));
?>
</td></tr>
<tr><td class="label">Include map for UPRN:</td>
<td><input type="text" name="np_mapuprn" value="<?php echo get_np_var("mapuprn"); ?>" maxlength="8" style="width: 100px;" /></td></tr>
<tr><td class="label">Script:</td>
<td>
<?php
$scripts = $db->get_results("SELECT scriptid, script FROM scripts ORDER BY script", ARRAY_N);
build_select_box($scripts, "np_scriptid", get_np_var("scriptid"), "-");
?>
</td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" id="saveButton" name="save" value="Save" />
</td></tr>
<tr>
<td colspan="2">
<object type="application/x-xstandard" id="editor1" width="100%" height="400" codebase="http://clacksweb/cms/XStandard.cab#Version=1,7,1,0">
	<param name="Value" value="<?php echo htmlspecialchars($xscontent, ENT_COMPAT) ?>" />
	<param name="Base" value="http://clacksweb/" />
	<param name="CSS" value="http://clacksweb/cms/css/xscss.php" />
	<param name="Styles" value="http://clacksweb/cms/styles.xml" />
	<param name="ImageLibraryURL" value="http://clacksweb/cms/xs/imagelibrary.php" />
	<param name="SpellCheckerURL" value="http://clacksweb/cms/xs/spell.php" />
	<param name="DirectoryURL" value="http://clacksweb/cms/xs/directory.php" />
	<param name="License" value="http://clacksweb/cms/license.txt" />
	<param name="ClassImageFloatLeft" value="left" />
	<param name="ClassImageFloatRight" value="right" />
	<param name="EnablePasteMarkup" value="no" />
	<param name="Toolbar" value="strong, em, ,numbering, bullets, , image, line, hyperlink, directory, , cut, copy, paste, undo, , wysiwyg, source, preview, screen-reader, , help" />
	<param name="EnableTimestamp" value="no" />
	<param name="Options" value="160" />
	<textarea name="alternate1" id="alternate1" cols="60" rows="15"><?php echo htmlspecialchars($xscontent, ENT_COMPAT) ?></textarea>
</object>
<input type="hidden" name="np_content" id="np_content" value="" />
</td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" id="saveButton" name="save" value="Save" />
</td></tr>
</table>
</form>
<script>
function showSubmit() {
document.all.saveButton.disabled = false;
}
</script>
