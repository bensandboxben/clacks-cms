<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$tro_array = $db->get_row("SELECT * FROM tros WHERE troid='$_GET[troid]'", ARRAY_A);
	extract($tro_array);
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
<?php include "includes/head.inc.php"; ?>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script>
  $(function() {
    $( "#datepicker" ).datepicker();
    $( "#datepicker1" ).datepicker();
  });
  </script>
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
				document.getElementById('proposal').value = document.getElementById('editor1').value;
			}
		}
		catch(er) {
			document.getElementById('proposal').value = document.getElementById('proposal').value;
		}
	}
//]]>
</script>

<?php if ($edit == 1) { ?>
<h3>Edit order</h3>
<?php } else { ?>
<h3>Add new order</h3>
<?php
}
if ($_POST["save"]) {

	$_POST["propdate"] = date("Y-m-d", strtotime($_POST["propdate"]));
	$_POST["madedate"] = date("Y-m-d", strtotime($_POST["madedate"]));

	$errors = array();
	$proposal = stripslashes($_POST["proposal"]);
	if (!$_POST["title"]) $errors[] = "Please enter a title";
	if (!$_POST["ref"]) $errors[] = "Please enter a reference";

	if (($_POST["propdate"] == '0000-00-00')||($_POST["madedate"] == '00/00/0000')||($_POST["propdate"] == '')||(!isset($_POST["propdate"]))) $_POST["propdate"] = "1970-01-01";
	if (($_POST["madedate"] == '0000-00-00')||($_POST["madedate"] == '00/00/0000')||($_POST["madedate"] == '')||(!isset($_POST["madedate"]))) $_POST["madedate"] = "1970-01-01";

	if (!$_POST["proposal"]) $errors[] = "Please enter some text";
	if (count($errors) == 0) {
		$proposal = addslashes($_POST["proposal"]);
		$title = addslashes($_POST["title"]);

		if ($_POST["troid"]) {
			$db->query("UPDATE tros SET title='$title', ref='$_POST[ref]', settlementid='$_POST[settlementid]', propdate='$_POST[propdate]', madedate='$_POST[madedate]', proposal='$proposal', modified=UNIX_TIMESTAMP() WHERE troid='$_POST[troid]'");
		} else {
			$db->query("INSERT INTO tros (title, ref, settlementid, propdate, madedate, proposal, created, modified) VALUES('$title', '$_POST[ref]', '$_POST[settlementid]', '$_POST[propdate]', '$_POST[madedate]', '$proposal', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
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
		echo '<p class="form-error-intro" role="alert">The order was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
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
<form name="tro" method="post" action="newtro.php" onsubmit="myOnSubmitEventHandler()">

	<script src="ckeditor/ckeditor.js"></script>
	<script type="text/javascript">
	//<![CDATA[
		function myOnSubmitEventHandler() {
		var content = CKEDITOR.instances.editor1.getData();

			try {

				document.getElementById('proposal').value = content;

			}
			catch(er) {
				document.getElementById('proposal').value = content;

			}
		}
	//]]>
	</script>



<table class="table table-striped table-bordered" width="100%">
<tr><td class="label">Title:</td>
<td><input type="text" name="title" value="<?php echo $title; ?>" maxlength="250" style="width: 200px;" /></td></tr>
<tr><td class="label">Ref:</td>
<td><input type="text" name="ref" value="<?php echo $ref; ?>" maxlength="50" style="width: 100px;" /></td></tr>
<tr><td class="label">Settlement:</td>
<td>
<?php
$settlements = $db->get_results("SELECT settlementid, settlement FROM settlements ORDER BY settlement", ARRAY_N);
build_select_box($settlements, "settlementid", $settlementid, '--');
?>
</td></tr>
<tr><td colspan="2" class="label">Proposal:</td>
</tr>
	<tr>
		<td colspan="2">

			<textarea name="editor1" id="editor1" cols="80" rows="15">
				<?php echo htmlspecialchars($proposal, ENT_COMPAT) ?>
			</textarea>

            <script>
                // Replace the <textarea id="editor1"> with a CKEditor
                // instance, using default configuration.
                CKEDITOR.replace( 'editor1' );
            </script>
			<input type="hidden" name="proposal" id="proposal" value="" />
		</td>
	</tr>


<tr>
	<td class="label">Date Proposed</td>
		<td>
			<input id="datepicker" type="text" name="propdate" value="<?php echo $propdate; ?>" maxlength="10" style="width: 100px;" />
		</td>
</tr>
<tr>
	<td class="label">Date Made (Default is 1970-01-01)</td>
		<td>
			<input id="datepicker1" type="text" name="madedate" value="<?php echo $madedate; ?>" maxlength="10" style="width: 100px;" />
		</td>
</tr>

<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-secondary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($troid) echo '<input type="hidden" name="troid" value="' . $troid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.tro.title.focus();
</script>
</body>
</html>