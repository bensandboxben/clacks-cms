<?php

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$school_array = $db->get_row("SELECT * FROM schools WHERE schoolid='$_GET[schoolid]'", ARRAY_A);
	extract($school_array);
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
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
				document.getElementById('bodytext').value = document.getElementById('editor1').value;
			}
		}
		catch(er) {
			document.getElementById('bodytext').value = document.getElementById('alternate1').value;
		}
	}
//]]>
</script>
<?php if ($edit == 1) { ?>
<h3>Edit school</h3>
<?php } else { ?>
<h3>Add new school</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();

	if (!$_POST["name"]) $errors[] = "Please enter a school name";
	if (!$_POST["headteacher"]) $errors[] = "Please enter the headteacher's name";
	if (!$_POST["tel"]) $errors[] = "Please enter a telephone number";
	if (!$_POST["bodytext"]) $errors[] = "Please enter some text";
	if (count($errors) == 0) {

		$bodytext = addslashes ($_POST["bodytext"]);
		$name = addslashes ($_POST["name"]);

		$schoolurl = strtolower(preg_replace("/[^[:alnum:]]/", "", $_POST["name"]));

		$schoolboard = $_POST['schoolboard'] ?? ''; // Set default value if not set		
		if ($schoolboard === null || $schoolboard === '') {
		    $schoolboard = '0'; // Replace null or empty string with '0'
		}
		$suspended = $_POST['suspended'] ?? ''; // Set default value if not set		
		if ($suspended === null || $suspended === '') {
		    $suspended = '0'; // Replace null or empty string with '0'
		}


		if ($_POST["schoolid"]) {
			$db->query("UPDATE schools SET schoolurl='$schoolurl', name='$name', sector='$_POST[sector]', headteacher='$_POST[headteacher]', uprn='$_POST[uprn]', roll='$_POST[roll]', schoolboard='$schoolboard', tel='$_POST[tel]', fax='$_POST[fax]', url='$_POST[url]', email='$_POST[email]', bodytext='$bodytext', suspended='$suspended', modified=UNIX_TIMESTAMP() WHERE schoolid='$_POST[schoolid]'");
		} else {
			$db->query("INSERT INTO schools (schoolurl, name, sector, headteacher, uprn, roll, schoolboard, tel, fax, email, bodytext, suspended, created, modified) VALUES('$schoolurl', '$name', '$_POST[sector]', '$_POST[headteacher]', '$_POST[uprn]', '$_POST[roll]', '$schoolboard', '$_POST[tel]', '$_POST[fax]', '$_POST[email]', '$_POST[url]', '$bodytext', '$suspended', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
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
		echo '<p class="form-error-intro" role="alert">The school information was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
extract ($_POST);
?>
<form name="school" method="post" action="newschool.php" onsubmit="myOnSubmitEventHandler()">

	<script src="ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
	//<![CDATA[
		function myOnSubmitEventHandler() {
		var content = CKEDITOR.instances.editor1.getData();

			try {

				document.getElementById('bodytext').value = content;

			}
			catch(er) {
				document.getElementById('bodytext').value = content;

			}
		}
	//]]>
	</script>


<table class="table table-striped table-bordered">
<tr><td class="label">Name:</td>
<td><input type="text" name="name" value="<?php echo $name; ?>" maxlength="60" style="width: 200px;" /></td></tr>
<tr><td class="label">Sector:</td>
<td><select name="sector">
<?php
foreach($sectors as $key=>$value) {
	echo '<option value="' . $key . '"';
	if ($sector == $key) echo ' selected="selected"';
	echo '>' . $value . '</option>';
}
?>
</select></td></tr>
<tr><td class="label">Headteacher:</td>
<td><input type="text" name="headteacher" value="<?php echo $headteacher; ?>" maxlength="50" style="width: 200px;" /></td></tr>
<tr><td class="label">Location:</td>
<td><input type="text" name="uprn" value="<?php echo $uprn; ?>" readonly="readonly" /></td></tr>
<tr><td class="label">Roll:</td>
<td><input type="text" name="roll" value="<?php echo $roll; ?>" maxlength="16" style="width: 50px;" /></td></tr>
<tr><td class="label">School Board:</td>
<td>
<?php
build_yes_no_checkbox("schoolboard", $schoolboard);
?>
</td></tr>
<tr><td class="label">Telephone:</td>
<td><input type="text" name="tel" value="<?php echo $tel; ?>" maxlength="30" style="width: 150px;" /></td></tr>
<tr><td class="label">Fax:</td>
<td><input type="text" name="fax" value="<?php echo $fax; ?>" maxlength="30" style="width: 150px;" /></td></tr>
<tr><td class="label">Web address:</td>
<td><input type="text" name="url" value="<?php echo $url; ?>" maxlength="100" style="width: 200px;" /></td></tr>
<tr><td class="label">Email address:</td>
<td><input type="text" name="email" value="<?php echo $email; ?>" maxlength="100" style="width: 200px;" /></td></tr>
<tr><td class="label">Content:</td>
<td>&nbsp;</td></tr>
	<tr>
		<td colspan="2">

			<textarea name="editor1" id="editor1" cols="80" rows="15">
				<?php echo htmlspecialchars($bodytext, ENT_COMPAT) ?>
			</textarea>

            <script>
                // Replace the <textarea id="editor1"> with a CKEditor
                // instance, using default configuration.
                CKEDITOR.replace( 'editor1' );
            </script>
			<input type="hidden" name="bodytext" id="bodytext" value="" />
		</td>
	</tr>
<tr><td class="label">Suspend content:</td>
<td>
<?php
build_yes_no_checkbox("suspended", $suspended);
?>
</td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-secondary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($schoolid) echo '<input type="hidden" name="schoolid" value="' . $schoolid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.school.name.focus();
</script>
</body>
</html>