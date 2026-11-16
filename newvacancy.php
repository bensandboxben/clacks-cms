<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$vac_array = $db->get_row("SELECT * FROM hr.vacancies WHERE vacid='$_GET[vacid]'", ARRAY_A);
	extract($vac_array);
} else if ($_GET["action"] == "copy") {
	$vac_array = $db->get_row("SELECT * FROM hr.vacancies WHERE vacid='$_GET[vacid]'", ARRAY_A);
	extract($vac_array);
	unset($vacid);
}
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<script language="JavaScript">
<!--
function doAddGrade (label, value) {
var s = document.vacancy.elements["gradeid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
function updateGrade (index, label) {
document.vacancy.gradeid[index].text = label;
}
function editGrade() {
var gradeid = document.vacancy.gradeid.value;
var gradeIndex = document.vacancy.gradeid.selectedIndex;
window.open('newgrade.php?action=edit&gradeid=' + gradeid + '&index=' + gradeIndex, 'editgrade', 'width=300,height=300,top=50,left=50,resizable=yes,scrollbars=yes');
}
function doAddJobTitle (label, value) {
var s = document.vacancy.elements["titleid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
function updateJobTitle (index, label) {
document.vacancy.titleid[index].text = label;
}
function editJobTitle() {
var titleid = document.vacancy.titleid.value;
var titleIndex = document.vacancy.titleid.selectedIndex;
window.open('newjobtitle.php?action=edit&titleid=' + titleid + '&index=' + titleIndex, 'editjobtitle', 'width=500,height=150,top=50,left=50,resizable=yes,scrollbars=yes');
}
function doAddDivision (label, value) {
var s = document.vacancy.elements["divid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
function updateDivision (index, label) {
document.vacancy.divid[index].text = label;
}
function editDivision() {
var divid = document.vacancy.divid.value;
var divIndex = document.vacancy.divid.selectedIndex;
window.open('newhrdivision.php?action=edit&divid=' + divid + '&index=' + divIndex, 'editdivision', 'width=500,height=350,top=50,left=50,resizable=yes,scrollbars=yes');
}
//-->
</script>
<?php if ($edit == 1) { ?>
<h3>Edit vacancy</h3>
<?php } else { ?>
<h3>Add new vacancy</h3>
<?php
}
if ($_POST["save"]) {
	$errors = array();
	if (!$_POST["jobno"]) $errors[] = "Please enter a job number";
	if (!$_POST["opendate"]) $errors[] = "Please enter an opening date";
	if (!$_POST["closedate"]) $errors[] = "Please enter a closing date";
	if (!$_POST["details"]) $errors[] = "Please enter a brief summary for this vacancy";
	if (count($errors) == 0) {
		if ($_POST["vacid"]) {
			$db->query("UPDATE hr.vacancies SET jobno='$_POST[jobno]', serviceid='21', titleid='$_POST[titleid]', location='$_POST[location]', tempuntil='$_POST[tempuntil]', contractcode='$_POST[contractcode]', gradeid='$_POST[gradeid]', showgrade='$_POST[showgrade]', allowances='$_POST[allowances]', prorata='$_POST[prorata]', divid='$_POST[divid]', hours='$_POST[hours]', opendate='$_POST[opendate]', closedate='$_POST[closedate]', closehour='$_POST[closehour]', closeminute='$_POST[closeminute]', contactid='$_POST[contactid]', teaching='$_POST[teaching]', social='$_POST[social]', promoted='$_POST[promoted]', internal='$_POST[internal]', details='$_POST[details]', extcontact='$_POST[extcontact]', onlineapp='$_POST[onlineapp]', infoonly='$_POST[infoonly]', live='$_POST[live]', modified=UNIX_TIMESTAMP() WHERE vacid='$_POST[vacid]'");
		} else {
			$db->query("INSERT INTO hr.vacancies (jobno, serviceid, titleid, location, tempuntil, contractcode, gradeid, showgrade, allowances, prorata, divid, hours, opendate, closedate, closehour, closeminute, contactid, teaching, social, promoted, internal, details, extcontact, onlineapp, infoonly, live, modified, created) VALUES('$_POST[jobno]', '21', '$_POST[titleid]', '$_POST[location]', '$_POST[tempuntil]', '$_POST[contractcode]', '$_POST[gradeid]', '$_POST[showgrade]', '$_POST[allowances]', '$_POST[prorata]', '$_POST[divid]', '$_POST[hours]', '$_POST[opendate]', '$_POST[closedate]', '$_POST[closehour]', '$_POST[closeminute]', '$_POST[contactid]', '$_POST[teaching]', '$_POST[social]', '$_POST[promoted]', '$_POST[internal]', '$_POST[details]', '$_POST[extcontact]', '$_POST[onlineapp]', '$_POST[infoonly]', '$_POST[live]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
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
		echo '<p>The vacancy was not saved. Please correct these errors and resubmit the form:</p><ul>';
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
<form name="vacancy" method="post" action="newvacancy.php">
<table class="table table-striped table-bordered">
<tr><td class="label">Job Number:</td>
<td><input type="text" name="jobno" value="<?php echo $jobno; ?>" maxlength="10" style="width: 120px;" /></td></tr>
<tr><td class="label">Job title:</td><td>
<?php
$jobtitles = $db->get_results("SELECT titleid, jobtitle FROM hr.jobtitles ORDER BY jobtitle", ARRAY_N);
build_select_box($jobtitles, "titleid", $titleid);
?>
<a href="#" onClick="window.open('newjobtitle.php', 'jobtitle_new', 'width=500,height=150,resizable=yes,scrollbars=yes,top=50,left=50')"><img src="images/add.gif" alt="Add new job title" border="0" /></a>
<a href="#" onClick="editJobTitle()"><img src="images/edit.gif" alt="Edit jobtitle" border="0" /></a>
</td></tr>
<tr><td class="label">Contract:</td><td>
<?php
$contracts = $db->get_results("SELECT contractcode, contract FROM hr.contracts ORDER BY contract", ARRAY_N);
build_select_box($contracts, "contractcode", $contractcode);
?>
</td></tr>
<tr><td class="label">Temporary:</td>
<td><input type="text" name="tempuntil" value="<?php echo $tempuntil; ?>" maxlength="100" style="width: 300px;" /></td></tr>
<tr><td class="label">Grade:</td><td>
<?php
$grades = $db->get_results("SELECT gradeid, CONCAT(grade, ' - £', minsal, ' - £', maxsal, ' - ', hourly) AS gradedet FROM hr.grades ORDER BY grade", ARRAY_N);
build_select_box($grades, "gradeid", $gradeid);
?>
<a href="#" onClick="window.open('newgrade.php', 'grade_new', 'width=300,height=300,resizable=yes,scrollbars=yes,top=50,left=50')"><img src="images/add.gif" alt="Add new grade" border="0" /></a>
<a href="#" onClick="editGrade()"><img src="images/edit.gif" alt="Edit grade" border="0" /></a>
</td></tr>
<tr><td class="label">Hours:</td>
<td><input type="text" name="hours" value="<?php if ($hours) {echo $hours;}else{echo " hours per week";}?>" maxlength="100" style="width: 300px;" /></td></tr>
<tr><td class="label">Pro rata:</td><td>
<?php
build_yes_no_box("prorata", $prorata);
?>
</td></tr>
<tr><td class="label">Other payments:</td>
<td><textarea name="allowances" style="width: 300px; height: 60px;"><?php echo $allowances; ?></textarea></td></tr>
<tr><td class="label">Options:</td>
<td><table style="border-collapse: collapse;">
<tr>
<td style="border: 1px solid #ccc;"><?php build_yes_no_checkbox("showgrade", $showgrade); ?> <label for="showgrade">Show grade</label></td>
<td style="border: 1px solid #ccc;"><?php build_yes_no_checkbox("teaching", $teaching); ?> <label for="teaching">Teaching post</label></td>
<td style="border: 1px solid #ccc;"><?php build_yes_no_checkbox("promoted", $promoted); ?> <label for="promoted">Promoted post</label></td>
<td style="border: 1px solid #ccc;"><?php build_yes_no_checkbox("social", $social); ?> <label for="social">Social Services post</label></td>
</tr>
<tr>
<td style="border: 1px solid #ccc;"><?php build_yes_no_checkbox("internal", $internal); ?> <label for="internal">Internal only</label></td>
<td style="border: 1px solid #ccc;"><?php build_yes_no_checkbox("infoonly", $infoonly); ?> <label for="infoonly">For info only</label></td>
<td style="border: 1px solid #ccc;"><?php build_yes_no_checkbox("onlineapp", $onlineapp); ?> <label for="onlineapp">Online applications</label></td>
<td style="border: 1px solid #ccc;">&nbsp;</td>
</tr>
</table>
</td></tr>
<tr><td class="label">Live:</td><td>
<?php
build_yes_no_box("live", $live);
?>
</td></tr>
<tr><td class="label">Contact:</td><td>
<?php
$contacts = $db->get_results("SELECT contactid, CONCAT(name, ' -> ', tel) AS condet FROM hr.contacts ORDER BY name", ARRAY_N);
build_select_box($contacts, "contactid", $contactid);
?>
</td></tr>
<tr><td class="label">External contact:</td>
<td><textarea name="extcontact" style="width: 300px; height: 60px;"><?php echo $extcontact; ?></textarea></td></tr>
<tr><td class="label">Service:</td><td>
<?php
$services = $db->get_results("SELECT hr.divisions.divid, CONCAT(hr.services.service, ' -> ', hr.divisions.division) AS divtext FROM hr.divisions LEFT OUTER JOIN hr.services USING(servicecode) ORDER BY divtext", ARRAY_N);
build_select_box($services, "divid", $divid);
?>
<a href="#" onClick="window.open('newhrdivision.php', 'hrdivision_new', 'width=500,height=350,resizable=yes,scrollbars=yes,top=50,left=50')"><img src="images/add.gif" alt="Add new service" border="0" /></a>
<a href="#" onClick="editDivision()"><img src="images/edit.gif" alt="Edit service" border="0" /></a>
</td></tr>
<tr><td class="label">Location:</td>
<td><input type="text" name="location" value="<?php echo $location; ?>" maxlength="100" style="width: 300px;" /></td></tr>
<tr><td class="label">Opening date:</td>
<td><input type="text" name="opendate" value="<?php echo $opendate; ?>" maxlength="10" style="width: 100px;" />
<button type="reset" id="f_trigger_a">...</button>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "opendate",      // id of the input field
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        showsTime      :    false,            // will display a time selector
        button         :    "f_trigger_a",   // trigger for the calendar (button ID)
        singleClick    :    true,           // double-click mode
        step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    });
</script>
</td></tr>
<tr><td class="label">Closing date:</td>
<td><input type="text" name="closedate" value="<?php echo $closedate; ?>" maxlength="10" style="width: 100px;" />
<button type="reset" id="f_trigger_b">...</button>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "closedate",      // id of the input field
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        showsTime      :    false,            // will display a time selector
        button         :    "f_trigger_b",   // trigger for the calendar (button ID)
        singleClick    :    true,           // double-click mode
        step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    });
</script>
</td></tr>
<tr><td class="label">Closing time:</td><td>
<select name="closehour">
<?php
for ($x=1; $x<25; $x++) {
	echo '<option value="' . str_pad($x, 2, "0", STR_PAD_LEFT) . '"';
	if (isset($closehour)) {
		if ($x==$closehour) echo ' SELECTED';
	} else {
		if ($x==23) echo ' SELECTED';
	}
	echo '>' . str_pad($x, 2, "0", STR_PAD_LEFT) . '</option>';
}
?>
</select> : <select name="closeminute">
<?php
for ($x=0; $x<60; $x++) {
	echo '<option value="' . str_pad($x, 2, "0", STR_PAD_LEFT) . '"';
	if (isset($closeminute)) {
		if ($x==$closeminute) echo ' SELECTED';
	} else {
		if ($x==59) echo ' SELECTED';
	}
	echo '>' . str_pad($x, 2, "0", STR_PAD_LEFT) . '</option>';
}
?>
</select>
</td></tr>
<tr><td class="label">Details:</td>
<td><textarea name="details" style="width: 450px; height: 180px;"><?php echo $details; ?></textarea></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="save" value="Save" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($vacid) echo '<input type="hidden" name="vacid" value="' . $vacid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.vacancy.jobno.focus();
</script>
</body>
</html>