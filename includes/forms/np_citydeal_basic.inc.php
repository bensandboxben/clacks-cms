<?php
np_display_errors();
?>
<!-- calendar stylesheet -->
<link rel="stylesheet" type="text/css" media="all" href="calendar-win2k-cold-1.css" title="win2k-cold-1" />
<!-- main calendar program -->
<script type="text/javascript" src="js/calendar.js"></script>
<!-- language for the calendar -->
<script type="text/javascript" src="js/calendar-en.js"></script>
<!-- the following script defines the Calendar.setup helper function, which makes adding a calendar a matter of 1 or 2 lines of code. -->
<script type="text/javascript" src="js/calendar-setup.js"></script>
<form name="basic" method="post" action="newintranetcontent.php">
<table>
<tr><td class="label"><span class="required">*</span> Page title:</td>
<td><input type="text" name="np_title" value="<?php echo htmlentities(get_np_var("title")); ?>" style="width: 300px;" /></td></tr>
<tr><td class="label">Print title:</td>
<td>
<?php
build_yes_no_checkbox("np_printtitle", get_np_var("printtitle"));
?>
</td></tr>
<tr><td class="label"><span class="required">*</span> Page name:</td>
<td><input type="text" name="np_pagename" value="<?php echo get_np_var("pagename"); ?>" maxlength="32" style="width: 200px;" /></td></tr>
<tr><td class="label">Old page name:</td>
<td><input type="text" name="np_dynaurl" value="<?php echo get_np_var("dynaurl"); ?>" maxlength="32" style="width: 200px;" /></td></tr>
<tr><td class="label"><span class="required">*</span> Section:</td>
<td>
<?php
$sections = $db->get_results("SELECT sectionid, sectiontitle FROM sections WHERE `sectionurl` LIKE '%intranet%' OR `sectionurl` LIKE '%council%' ORDER BY sectiontitle", ARRAY_N);
build_select_box($sections, "np_sectionid", get_np_var("sectionid"), "-");
?>
</td></tr>

<tr>
<td>
<?php
//$status = $db->get_results("SELECT statusid, status FROM status WHERE status='Draft' ORDER BY statusid", ARRAY_N);
//if ($_SESSION["username"] == "training") {
//	$status = $db->get_results("SELECT statusid, status FROM status WHERE status='Draft' ORDER BY statusid", ARRAY_N);
//}


//build_select_box($status, "np_statusid", get_np_var("statusid"));


echo "<input type='hidden' name='np_statusid' value='1'>";
?>

</td></tr>
<tr><td class="label">Expiry date:</td>
<td>
<input type="text" name="np_expirydate" value="<?php echo get_np_var("expirydate"); ?>" maxlength="10" style="width: 100px;" />
<button type="reset" id="f_trigger_b">...</button>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "np_expirydate",      // id of the input field
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        showsTime      :    false,            // will display a time selector
        button         :    "f_trigger_b",   // trigger for the calendar (button ID)
        singleClick    :    true,           // double-click mode
        step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    });
</script>
</td></tr>
<tr><td class="label">Review date:</td>
<td>
<input type="text" name="np_reviewdate" value="<?php echo get_np_var("reviewdate"); ?>" maxlength="10" style="width: 100px;" />
<button type="reset" id="f_trigger_c">...</button>
<script type="text/javascript">
    Calendar.setup({
        inputField     :    "np_reviewdate",      // id of the input field
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        showsTime      :    false,            // will display a time selector
        button         :    "f_trigger_c",   // trigger for the calendar (button ID)
        singleClick    :    true,           // double-click mode
        step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    });
</script>
</td></tr>
<tr><td class="label">Index page:</td>
<td>
<?php
build_yes_no_checkbox("np_indexed", get_np_var("indexed"));
?>
</td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" name="save" class="btn btn-md btn-primary" value="Save Details" />
</td></tr>
</table>
</form>
<script>
document.basic.np_title.focus();
</script>
