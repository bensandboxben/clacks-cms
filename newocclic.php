<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$occ_array = $db->get_row("SELECT * FROM occasionallicences WHERE id='$_GET[id]'", ARRAY_A);
	extract($occ_array);
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
<?php include "includes/head.inc.php"; ?>
</head>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script>
  $(function() {
    $( "#datepicker" ).datepicker();
    $( "#datepicker1" ).datepicker();
    $( "#datepicker2" ).datepicker();
  });
  </script>
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

	$_POST["eventdate"] = date("Y-m-d", strtotime($_POST["eventdate"]));
	$_POST["propdate"] = date("Y-m-d", strtotime($_POST["propdate"]));
	$_POST["granteddate"] = date("Y-m-d", strtotime($_POST["granteddate"]));

	$errors = array();
	$proposal = stripslashes($_POST["proposal"]);
	if (!$_POST["applicant"]) $errors[] = "Please enter an applicant";
	if (!$_POST["location"]) $errors[] = "Please enter a location";
	if (!$_POST["reference"]) $errors[] = "Please enter a reference";
	if (!$_POST["postcode"]) $errors[] = "Please enter a postcode";
	if (!$_POST["status"]) $errors[] = "Please enter a status";
	if (!$_POST["proposal"]) $errors[] = "Please enter some text";
	if (($_POST["eventdate"] == '0000-00-00')||($_POST["eventdate"] == '1970-01-01')||($_POST["eventdate"] == '')||(!isset($_POST["eventdate"]))) $_POST["eventdate"] = "0000-00-00";
	if (($_POST["proposeddate"] == '0000-00-00')||($_POST["proposeddate"] == '1970-01-01')||($_POST["proposeddate"] == '')||(!isset($_POST["proposeddate"]))) $_POST["proposeddate"] = "0000-00-00";
	if (($_POST["granteddate"] == '0000-00-00')||($_POST["granteddate"] == '1970-01-01')||($_POST["granteddate"] == '')||(!isset($_POST["granteddate"]))) $_POST["granteddate"] = "0000-00-00";

	if (count($errors) == 0) {
		$proposal = addslashes($_POST["proposal"]);
		if ($_POST["id"]) {
			$db->query("UPDATE occasionallicences SET
			applicant='$_POST[applicant]',
			location='$_POST[location]',
			reference='$_POST[reference]',
			postcode='$_POST[postcode]',
			statusid='$_POST[status]',
			eventdate='$_POST[eventdate]',
			propdate='$_POST[propdate]',
			granteddate='$_POST[granteddate]',
			proposal='$proposal',
			modified=UNIX_TIMESTAMP() WHERE id='$_POST[id]'");
		}
		else {
			$db->query("INSERT INTO occasionallicences (
			applicant,
			location,
			reference,
			postcode,
			statusid,
			eventdate,
			propdate,
			granteddate,
			proposal,
			created,
			modified) VALUES(
			'$_POST[applicant]',
			'$_POST[location]',
			'$_POST[reference]',
			'$_POST[postcode]',
			'$_POST[status]',
			'$_POST[eventdate]',
			'$_POST[propdate]',
			'$_POST[granteddate]',
			'$proposal',
			UNIX_TIMESTAMP(),
			UNIX_TIMESTAMP())");


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


<form name="occlic" method="post" action="newocclic.php" onsubmit="myOnSubmitEventHandler()">

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

<tr>
	<td class="label">Applicant:</td>
	<td><input type="text" name="applicant" value="<?php echo $applicant; ?>" maxlength="100" style="width: 140px;" /></td>
</tr>
<tr>
	<td class="label">Location:</td>
	<td><input type="text" name="location" value="<?php echo $location; ?>" maxlength="250" style="width: 250px;" /></td>
</tr>
<tr>
	<td class="label">Reference:</td>
	<td><input type="text" name="reference" value="<?php echo $reference; ?>" maxlength="20" style="width: 80px;" /></td>
</tr>
<tr>
	<td class="label">Postcode:</td>
	<td><input type="text" name="postcode" value="<?php echo $postcode; ?>" maxlength="10" style="width: 80px;" /></td>
</tr>
<tr>
	<td class="label">Status:</td>
	<td>
	<?php
		$status = $db->get_results("SELECT statusid, status FROM status ORDER BY statusid", ARRAY_N);
		build_select_box($status, "status", $statusid, '--');
	?>
	</td>
</tr>
<tr>
	<td class="label">Proposal:</td>
	<td>&nbsp;</td>
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
	<td class="label">Event date:</td>
		<td>
			<input id="datepicker" type="text" name="eventdate" value="<?php echo $eventdate; ?>" maxlength="10" style="width: 100px;" />
		</td>
</tr>
<tr>
	<td class="label">Proposal date:</td>
		<td>
			<input id="datepicker1" type="text" name="propdate" value="<?php echo $propdate; ?>" maxlength="10" style="width: 100px;" />
		</td>
</tr>
<tr>
	<td class="label">Granted date:</td>
		<td>
			<input id="datepicker2" type="text" name="granteddate" value="<?php echo $granteddate; ?>" maxlength="10" style="width: 100px;" />
		</td>
</tr>


<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-secondary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($id) echo '<input type="hidden" name="id" value="' . $id . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.occlic.location.focus();
</script>
</body>
</html>