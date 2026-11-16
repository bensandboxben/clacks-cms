<?php
np_display_errors();
?>
<script language="JavaScript">
<!--
function doAddContact (label, value) {
var s = document.forms['contacts'].np_contactid;
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>
<form name="contacts" method="post" action="newcontent.php">
<table class="table table-striped table-bordered">
<tr><td class="label"><span class="required">*</span> Page owner:</td>
<td>
<?php
if ($_SESSION["userlevel"] == 3) {
	$users = $db->get_results("SELECT DISTINCT users.serviceid, name FROM cms.users LEFT JOIN cms.services USING(serviceid) WHERE userlevel=3 AND (users.serviceid='$_SESSION[userserviceid]' OR parentserviceid='$_SESSION[userserviceid]')", ARRAY_N);
} else if ($_SESSION["userlevel"] > 3) {
	$users = $db->get_results("SELECT DISTINCT serviceid, name FROM cms.users WHERE userlevel=3 ORDER BY name", ARRAY_N);
} else {
	$users = $db->get_results("SELECT DISTINCT serviceid, name FROM cms.users WHERE userlevel=3 AND serviceid='$_SESSION[userserviceid]'", ARRAY_N);
}
build_select_box($users, "np_serviceid", get_np_var("serviceid"), "-");
?>
</td></tr>
<tr><td class="label"><span class="required">*</span> Page contact:</td>
<td>
<?php
if ($_SESSION["userlevel"] < 4) {
	$contacts = $db->get_results("SELECT contactid, CONCAT(jobtitle, ' - ', name, ', ', service) AS value FROM contacts LEFT JOIN cms.services USING(serviceid) WHERE (contacts.serviceid='$_SESSION[userserviceid]' OR parentserviceid='$_SESSION[userserviceid]') ORDER BY value", ARRAY_N);
} else {
	$contacts = $db->get_results("SELECT contactid, CONCAT(jobtitle, ' - ', name, ', ', service) AS value FROM contacts LEFT JOIN cms.services USING(serviceid) ORDER BY value", ARRAY_N);
}
build_select_box($contacts, "np_contactid", get_np_var("contactid"), "-");
?>
 <a href="#" onClick="window.open('newcontact.php', 'contact_new', 'width=600,height=400,top=50,left=50,resizable=yes,scrollbars=yes')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new contact</span></a>
</td></tr>
<tr><td class="label">Send review emails to:</td>
<td><input type="text" name="np_reviewemail" value="<?php echo get_np_var("reviewemail"); ?>" style="width: 300px;" /></td></tr>
<tr><td class="label">Print contact details:</td>
<td>
<?php
build_yes_no_checkbox("np_printcontact", get_np_var("printcontact"));
?>
</td></tr>
<tr><td class="label">Name contact:</td>
<td>
<?php
build_yes_no_checkbox("np_namecontact", get_np_var("namecontact"));
?>
</td></tr>
<tr><td class="label">Provide contact form:</td>
<td>
<?php
build_yes_no_checkbox("np_contactform", get_np_var("contactform"));
?>
</td></tr>
<!--tr><td class="label">Allow page to be emailed:</td>
<td>
<?php
build_yes_no_checkbox("np_pageemail", get_np_var("pageemail"));
?>
</td></tr-->
<tr><td colspan="2" class="formbtns">
<input type="submit" name="save" class="btn btn-md btn-primary" value="Save Contact Details" />
</td></tr>
</table>
</form>
