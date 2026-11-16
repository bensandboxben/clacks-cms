<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$wn_array = $db->get_row("SELECT * FROM worknotices WHERE id='$_GET[id]'", ARRAY_A);
	extract($wn_array);
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
    $( "#datepicker1" ).datepicker();
    $( "#datepicker2" ).datepicker();
    $( "#datepicker3" ).datepicker();
    $( "#datepicker4" ).datepicker();
    $( "#datepicker5" ).datepicker();
    $( "#datepicker6" ).datepicker();
 });
  </script>

<?php include "includes/head.inc.php"; ?>
</head>
<body class="popup">

<?php if ($edit == 1) { ?>
<h3>Edit Work Notice</h3>
<?php } else { ?>
<h3>Add new Work Notice</h3>
<?php
}
if ($_POST["save"]) {

	$_POST["datenoticeserved"] = date("Y-m-d", strtotime($_POST["datenoticeserved"]));
	$_POST["datenoticeextended"] = date("Y-m-d", strtotime($_POST["datenoticeextended"]));
	$_POST["datenoticerevoked"] = date("Y-m-d", strtotime($_POST["datenoticerevoked"]));
	$_POST["datenoticesuspended"] = date("Y-m-d", strtotime($_POST["datenoticesuspended"]));
	$_POST["worknoticeexpirydate"] = date("Y-m-d", strtotime($_POST["worknoticeexpirydate"]));
	$_POST["enforcementdate"] = date("Y-m-d", strtotime($_POST["enforcementdate"]));
	$_POST["completiondate"] = date("Y-m-d", strtotime($_POST["completiondate"]));

	$errors = array();

	if (!$_POST["refno"]) $errors[] = "Please enter a reference number";
	if (!$_POST["datenoticeserved"]) $errors[] = "Please enter the date notice was served";
	if (!$_POST["servedby"]) $errors[] = "Please enter who this notice was served by";
	if (!$_POST["address"]) $errors[] = "Please enter an address";
	if (!$_POST["workrequired"]) $errors[] = "Please give details of the work required";
	if (!$_POST["anticipatedcost"]) $errors[] = "Please enter an anticipated cost";

	$datenoticeextended = $_POST['datenoticeextended'] ?? null;
	if ($datenoticeextended === null || $datenoticeextended === '' || $datenoticeextended === '0000-00-00') {
    		$datenoticeextended = null; 
    	}
	$datenoticeserved = $_POST['datenoticeserved'] ?? null;
	if ($datenoticeserved === null || $datenoticeserved === '' || $datenoticeserved === '0000-00-00') {
    		$datenoticeserved = null; 
    	}    	
	$datenoticerevoked = $_POST['datenoticerevoked'] ?? null;
	if ($datenoticerevoked === null || $datenoticerevoked === '' || $datenoticerevoked === '0000-00-00') {
    		$datenoticerevoked = null; 
    	}
	$datenoticesuspended = $_POST['datenoticesuspended'] ?? null;
	if ($datenoticesuspended === null || $datenoticesuspended === '' || $datenoticesuspended === '0000-00-00') {
    		$datenoticesuspended = null; 
    	}
	$worknoticeexpirydate = $_POST['worknoticeexpirydate'] ?? null;
	if ($worknoticeexpirydate === null || $worknoticeexpirydate === '' || $worknoticeexpirydate === '0000-00-00') {
    		$worknoticeexpirydate = null; 
    	}
 	$enforcementdate = $_POST['enforcementdate'] ?? null;
	if ($enforcementdate === null || $enforcementdate === '' || $enforcementdate === '0000-00-00') {
    		$enforcementdate = null; 
    	}
	$completiondate = $_POST['completiondate'] ?? null;
	if ($completiondate === null || $completiondate === '' || $completiondate === '0000-00-00') {
    		$completiondate = null; 
    	}
   
	if ($_POST['noticeextended'] == "1") {
		if (!$_POST["datenoticeextended"]) $errors[] = "Please enter who the date this notice was extended";
		if (!$_POST["lengthofextension"]) $errors[] = "Please enter who the length of the extension";
	}
	if ($_POST['noticerevoked'] == "1") {
		if (!$_POST["datenoticerevoked"]) $errors[] = "Please enter who the date this notice was revoked";
	}
	if ($_POST['noticesuspended'] == "1") {
		if (!$_POST["datenoticesuspended"]) $errors[] = "Please enter who the date this notice was suspended";
	}
	if ($_POST['noticeenforced'] == "1") {
		if (!$_POST["enforcementdate"]) $errors[] = "Please enter who the date this notice was enforced";
	}



	if (count($errors) == 0) {
	
	$noticeExtended = $_POST['noticeextended'] ?? ''; // Set default value if not set
	$noticeSuspended = $_POST['noticesuspended'] ?? ''; // Set default value if not set
	$noticeRevoked = $_POST['noticerevoked'] ?? ''; // Set default value if not set
	$noticeEnforced = $_POST['noticeenforced'] ?? ''; // Set default value if not set
	
	if ($noticeExtended === null || $noticeExtended === '') {
	    $noticeExtended = '0'; // Replace null or empty string with '0'
	}
	if ($noticeSuspended === null || $noticeSuspended === '') {
	    $noticeSuspended = '0'; // Replace null or empty string with '0'
	}
	if ($noticeRevoked === null || $noticeRevoked === '') {
	    $noticeRevoked = '0'; // Replace null or empty string with '0'
	}
	if ($noticeEnforced === null || $noticeEnforced === '') {
	    $noticeEnforced = '0'; // Replace null or empty string with '0'
	}	
	
	
		if ($_POST["id"]) {
			$db->query("UPDATE worknotices SET
						refno='{$_POST['refno']}',
						datenoticeserved='{$_POST['datenoticeserved']}',
						servedby='{$_POST['servedby']}',
						address='{$_POST['address']}',
						workrequired='{$_POST['workrequired']}',
						anticipatedcost='{$_POST['anticipatedcost']}',
						noticeextended='$noticeExtended',
						datenoticeextended='{$_POST['datenoticeextended']}',
						lengthofextension='{$_POST['lengthofextension']}',
						noticerevoked='$noticeRevoked',
						datenoticerevoked='{$_POST['datenoticerevoked']}',
						noticesuspended='$noticeSuspended',
						datenoticesuspended='{$_POST['datenoticesuspended']}',
						worknoticeexpirydate='{$_POST['worknoticeexpirydate']}',
						noticeenforced='$noticeEnforced',
						enforcementdate='{$_POST['enforcementdate']}',
						completiondate='{$_POST['completiondate']}',
						modified=UNIX_TIMESTAMP() WHERE id='{$_POST['id']}'");
					}
					else {
						$db->query("INSERT INTO worknotices (
						refno,
						datenoticeserved,
						servedby,
						address,
						workrequired,
						anticipatedcost,
						noticeextended,
						datenoticeextended,
						lengthofextension,
						noticerevoked,
						datenoticerevoked,
						noticesuspended,
						datenoticesuspended,
						worknoticeexpirydate,
						noticeenforced,
						enforcementdate,
						completiondate,
						created,
						modified) VALUES (
						'{$_POST['refno']}',
						'{$_POST['datenoticeserved']}',
						'{$_POST['servedby']}',
						'{$_POST['address']}',
						'{$_POST['workrequired']}',
						'{$_POST['anticipatedcost']}',
						'$noticeExtended',
						'{$_POST['datenoticeextended']}',
						'{$_POST['lengthofextension']}',
						'$noticeRevoked',
						'{$_POST['datenoticerevoked']}',
						'$noticeSuspended',
						'{$_POST['datenoticesuspended']}',
						'{$_POST['worknoticeexpirydate']}',
						'$noticeEnforced',
						'{$_POST['enforcementdate']}',
						'{$_POST['completiondate']}',
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

	if ( ($datenoticeserved == "1970-01-01") || ($datenoticeserved == "0000-00-00") ) { $datenoticeserved = "";}
	if ( ($datenoticeextended == "1970-01-01") || ($datenoticeextended == "0000-00-00") ) { $datenoticeextended = "";}
	if ( ($datenoticerevoked == "1970-01-01") || ($datenoticerevoked == "0000-00-00") ) { $datenoticerevoked = "";}
	if ( ($datenoticesuspended == "1970-01-01") || ($datenoticesuspended == "0000-00-00") ) { $datenoticesuspended = "";}
	if ( ($worknoticeexpirydate == "1970-01-01") || ($worknoticeexpirydate == "0000-00-00") ) { $worknoticeexpirydate = "";}
	if ( ($enforcementdate == "1970-01-01") || ($enforcementdate == "0000-00-00") ) { $enforcementdate = "";}
	if ( ($completiondate == "1970-01-01") || ($completiondate == "0000-00-00") ) { $completiondate = "";}


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
<form name="occlic" method="post" action="newworknotices.php" onsubmit="myOnSubmitEventHandler()">
<table class="table table-striped table-bordered" width="100%">
<tr>
	<td class="label">Reference:</td>
	<td><input type="text" name="refno" value="<?php echo $refno; ?>" maxlength="20" style="width: 180px;" /></td>
</tr>

<tr>
	<td class="label">Date Noticed Served:</td>
	<td><input type="text" id="datepicker" name="datenoticeserved" value="<?php echo $datenoticeserved; ?>" maxlength="10" style="width: 100px;" /></td>
</tr>

<tr>
	<td class="label">Served By:</td>
	<td><input type="text" name="servedby" value="<?php echo $servedby; ?>" maxlength="25" style="width: 180px;" /></td>
</tr>
<tr>
	<td class="label">Address:</td>
	<td><input type="text" name="address" value="<?php echo $address; ?>" maxlength="100" style="width: 380px;" /></td>
</tr>
<tr>
	<td class="label">Work Required:</td>
	<td><input type="text" name="workrequired" value="<?php echo $workrequired; ?>" maxlength="100" style="width: 280px;" /></td>
</tr>
<tr>
	<td class="label">Anticipated Cost:</td>
	<td><input type="text" name="anticipatedcost" value="<?php echo $anticipatedcost; ?>" maxlength="20" style="width: 180px;" /></td>
</tr>
<tr>
	<td class="label">Notice Extended:</td>
	<td>
	<?php
	build_yes_no_checkbox("noticeextended", $noticeextended);
	?>
	</td>
</tr>
<tr>
	<td class="label">Date Notice Extended:</td>
	<td><input type="text" id="datepicker1" name="datenoticeextended" value="<?php echo $datenoticeextended; ?>" maxlength="10" style="width: 100px;" /></td>
</tr>

<tr>
	<td class="label">Length of Extension:</td>
	<td><input type="text" name="lengthofextension" value="<?php echo $lengthofextension; ?>" maxlength="20" style="width: 180px;" /></td>
</tr>

<tr>
	<td class="label">Notice Revoked:</td>
	<td>
	<?php
	build_yes_no_checkbox("noticerevoked", $noticerevoked);
	?>
	</td>
</tr>
<tr>
	<td class="label">Date Notice Revoked:</td>
	<td><input type="text" id="datepicker2" name="datenoticerevoked" value="<?php echo $datenoticerevoked; ?>" maxlength="10" style="width: 100px;" /></td>
</tr>
<tr>
	<td class="label">Notice Suspended:</td>
	<td>
	<?php
	build_yes_no_checkbox("noticesuspended", $noticesuspended);
	?>
	</td>
</tr>
<tr>
	<td class="label">Date Notice Suspended:</td>
	<td><input type="text" id="datepicker3" name="datenoticesuspended" value="<?php echo $datenoticesuspended; ?>" maxlength="10" style="width: 100px;" /></td>
</tr>


<tr>
	<td class="label">Work Notice Expiry Date:</td>
	<td><input type="text" id="datepicker4" name="worknoticeexpirydate" value="<?php echo $worknoticeexpirydate; ?>" maxlength="10" style="width: 100px;" /></td>
</tr>
<tr>
	<td class="label">Notice Enforced:</td>
	<td>
	<?php
	build_yes_no_checkbox("noticeenforced", $noticeenforced);
	?>
	</td>
</tr>
<tr>
	<td class="label">Enforcement Date:</td>
	<td><input type="text" id="datepicker5" name="enforcementdate" value="<?php echo $enforcementdate; ?>" maxlength="10" style="width: 100px;" /></td>
</tr>
<tr>
	<td class="label">Completion Date:</td>
	<td><input type="text" id="datepicker6" name="completiondate" value="<?php echo $completiondate; ?>" maxlength="10" style="width: 100px;" /></td>
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