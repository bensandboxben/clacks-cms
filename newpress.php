<?php



include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$press_array = $db->get_row("SELECT * FROM press WHERE prid='$_GET[prid]'", ARRAY_A);
	extract($press_array);
}
?>

<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script>
  $(function() {
    $( "#datepicker" ).datepicker();
  });
  </script>
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
				document.getElementById('htmlbody').value = document.getElementById('editor1').value;
			}
		}
		catch(er) {
			document.getElementById('htmlbody').value = document.getElementById('alternate1').value;
		}
	}
//]]>
</script>

<?php if ($edit == 1) { ?>
<h3>Edit press release</h3>
<?php } else { ?>
<h3>Add new press release</h3>
<?php
}
if ($_POST["save"]) {

	$_POST["pressdate"] = date("Y-m-d", strtotime($_POST["pressdate"]));

	$errors = array();
	$htmlbody = stripslashes($_POST["htmlbody"]);
	$config = array('clean' => TRUE, 'drop-empty-paras'=>TRUE, 'join-styles'=>FALSE, 'drop-font-tags' => TRUE, 'wrap' => 0, 'show-body-only' => 1, 'output-xhtml' => TRUE, 'word-2000'=>TRUE);
//	$htmlbody = tidy_repair_string($htmlbody, $config);
	$htmlbody = addslashes($htmlbody);
	if (!$_POST["subject"]) $errors[] = "Please enter a subject";
	if (!$_POST["websubject"]) $errors[] = "Please enter a web subject";
	if (!$_POST["pressdate"]) $errors[] = "Please enter the date of this release";
	if (!$htmlbody) $errors[] = "Please enter some text";
	if (count($errors) == 0) {

		$subject = addslashes($_POST["subject"]);
		$websubject = addslashes($_POST["websubject"]);
		$body = addslashes($_POST["body"]);

		$intranetcommunication = $_POST['intranetcommunication'] ?? ''; // Set default value if not set		
		if ($intranetcommunication === null || $intranetcommunication === '') {
		    $intranetcommunication = '0'; // Replace null or empty string with '0'
		}
		$positionstatement = $_POST['positionstatement'] ?? ''; // Set default value if not set		
		if ($positionstatement === null || $positionstatement === '') {
		    $positionstatement = '0'; // Replace null or empty string with '0'
		}
		$twitter = $_POST['twitter'] ?? ''; // Set default value if not set		
		if ($twitter === null || $twitter === '') {
		    $twitter = '0'; // Replace null or empty string with '0'
		}

		if ($_POST["prid"]) {
			$db->query("UPDATE press SET subject='$subject', websubject='$websubject', pressdate='$_POST[pressdate]', intranetcommunication='$intranetcommunication', positionstatement='$positionstatement', twitter='$twitter', body='$body', htmlbody='$htmlbody', catid='$_POST[catid]', modified=UNIX_TIMESTAMP() WHERE prid='$_POST[prid]'");
		} else {
			$db->query("INSERT INTO press (subject, websubject, pressdate, intranetcommunication, positionstatement, twitter, body, htmlbody, catid, created, modified) VALUES('$subject', '$websubject', '$_POST[pressdate]', '$intranetcommunication', '$positionstatement', '$twitter', '$body', '$htmlbody', '$_POST[catid]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
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
		echo '<p class="form-error-intro" role="alert">The press release was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
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
var s = document.press.elements["catid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>


	<script src="ckeditor/ckeditor.js"></script>

	<script type="text/javascript">
	//<![CDATA[
		function myOnSubmitEventHandler() {
		var content = CKEDITOR.instances.editor1.getData();

			try {

				document.getElementById('htmlbody').value = content;

			}
			catch(er) {
				document.getElementById('htmlbody').value = content;

			}
		}
	//]]>
	</script>

<form name="press" method="post" action="newpress.php" onsubmit="myOnSubmitEventHandler()">
<table class="table table-striped table-bordered">
<tr><td class="label">Subject:</td>
<td><input type="text" name="subject" value="<?php echo $subject; ?>" maxlength="250" style="width: 200px;" /></td></tr>

<tr><td class="label">Web Subject:</td>
<td><input type="text" name="websubject" value="<?php echo $websubject; ?>" maxlength="40" style="width: 200px;" /></td></tr>

<tr><td class="label">Date:</td>
	<td><input type="text" id="datepicker" name="pressdate" value="<?php echo $pressdate; ?>" maxlength="10" style="width: 100px;" /></td>
</tr>
<tr>
	<td class="label">Intranet Communication:</td>
	<td>
		<?php build_yes_no_checkbox("intranetcommunication", $intranetcommunication); ?>
	</td>
</tr>
<tr>
	<td class="label">Position statement:</td>
	<td>
		<?php build_yes_no_checkbox("positionstatement", $positionstatement); ?>
	</td>
</tr>
<tr>
	<td class="label">Twitter Feed:</td>
	<td>
		<?php build_yes_no_checkbox("twitter", $twitter); ?>
	</td>
</tr>
<tr><td class="label">Category:</td>
<td>
<?php
$presscats = $db->get_results("SELECT catid, category FROM categories ORDER BY category", ARRAY_N);
build_select_box($presscats, "catid", $catid);
?>
<a href="#" onClick="window.open('newcat.php', 'cat_new', 'width=500,height=300,resizable=yes,scrollbars=yes,top=50,left=50')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new category</span></a>
</td></tr>
<tr><td class="label">Page Text:</td>
<td>&nbsp;</td></tr>
	<tr>
		<td colspan="2">

			<textarea name="editor1" id="editor1" cols="80" rows="15">
				<?php echo htmlspecialchars($htmlbody, ENT_COMPAT) ?>
			</textarea>

            <script>
                // Replace the <textarea id="editor1"> with a CKEditor
                // instance, using default configuration.
                CKEDITOR.replace( 'editor1' );
            </script>
			<input type="hidden" name="htmlbody" id="htmlbody" value="" />
		</td>
	</tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-secondary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($prid) echo '<input type="hidden" name="prid" value="' . $prid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.contact.serviceid.focus();
</script>
</body>
</html>