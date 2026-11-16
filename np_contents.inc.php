<?php
np_display_errors();
?>

   <script src="ckeditor/ckeditor.js"></script>

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
	var content = CKEDITOR.instances.editor1.getData();

		try {

			document.getElementById('np_content').value = content;

		}
		catch(er) {
			document.getElementById('np_content').value = content;

		}
	}
//]]>
</script>
<form method="post" action="newcontent.php" name="content" onsubmit="myOnSubmitEventHandler()">
<table width="800" class="table table-striped table-bordered">
<tr><td class="label">Menu Choice:</td>
<td>
<?php
$xscontent = get_np_var("content");

if ($handle = opendir('/home/site/wwwroot/cms/menus')) {
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
<a href="#" onClick="editMenu()"><img src="images/edit.gif" alt="Edit menu" border="0" /></a>
<a href="#" onClick="addMenu()"><img src="images/add.gif" alt="Add menu" border="0" /></a>
</td></tr>
<tr><td class="label">Script:</td>
<td>
<?php
$scripts = $db->get_results("SELECT scriptid, script FROM scripts ORDER BY script", ARRAY_N);
build_select_box($scripts, "np_scriptid", get_np_var("scriptid"), "-");
?>
</td></tr>
<tr><td colspan="2" class="formbtns">
</td></tr>
	<tr>
		<td colspan="2">

			<textarea name="editor1" id="editor1" cols="80" rows="15">
				<?php echo htmlspecialchars($xscontent, ENT_COMPAT) ?>
			</textarea>

            <script>
                // Replace the <textarea id="editor1"> with a CKEditor
                // instance, using default configuration.
                CKEDITOR.replace( 'editor1' );
            </script>
			<input type="hidden" name="np_content" id="np_content" value="" />
		</td>
	</tr>
<tr><td colspan="2" class="formbtns">
<input class="btn btn-md btn-primary" type="submit" id="saveButton" name="save" value="Save Content" />
</td></tr>
</table>
</form>
<script>
function showSubmit() {
document.all.saveButton.disabled = false;
}
</script>
