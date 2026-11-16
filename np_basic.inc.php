<?php
np_display_errors();
?>




  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script>
  $(function() {
    $( "#datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
    $( "#datepicker1" ).datepicker({ dateFormat: 'yy-mm-dd' });
  });
  </script>


<form name="basic" method="post" action="newcontent.php">
<table class="table table-striped table-bordered">


<tr><td class="label"><span class="required">*</span> Page title:</td>
<td><input type="text" name="np_title" value="<?php echo get_np_var("title"); ?>" style="width: 300px;" />&nbsp;&nbsp;&nbsp;Print title: <?php
build_yes_no_checkbox("np_printtitle", get_np_var("printtitle"));
?></td></tr>


<tr><td class="label"><span class="required">*</span> Page name:</td>
<td><input type="text" name="np_pagename" value="<?php echo get_np_var("pagename"); ?>" maxlength="32" style="width: 200px;" /></td></tr>
<!--tr><td class="label">Old page name:</td>
<td><input type="text" name="np_dynaurl" value="<?php echo get_np_var("dynaurl"); ?>" maxlength="32" style="width: 200px;" /></td></tr-->
<tr><td class="label"><span class="required">*</span> Section:</td>
<td>
<?php
$sections = $db->get_results("SELECT sectionid, sectiontitle FROM sections WHERE `sectiontitle` NOT LIKE '%intranet%' ORDER BY sectiontitle", ARRAY_N);
build_select_box($sections, "np_sectionid", get_np_var("sectionid"), "-");
?>
</td></tr>
<tr><td class="label">Page status:</td>
<td>
<?php
$status = $db->get_results("SELECT statusid, status FROM status ORDER BY statusid", ARRAY_N);
if ($_SESSION["username"] == "training") {
	$status = $db->get_results("SELECT statusid, status FROM status WHERE status='Draft' ORDER BY statusid", ARRAY_N);
}

build_select_box($status, "np_statusid", get_np_var("statusid"));
?>
</td></tr>



<tr>
	<td class="label">Expiry date:</td>
		<td>
			<input id="datepicker" value="<?php echo get_np_var("title"); ?>"  type="text" name="np_expirydate" value="<?php echo get_np_var("expirydate"); ?>" maxlength="10" style="width: 100px;" />
		</td>
</tr>
<tr>
	<td class="label">Review date:</td>
		<td>
			<input id="datepicker1" type="text" name="np_reviewdate" value="<?php echo $oneYearFromNow = date('Y-m-d', strtotime('+1 year')); ?>" maxlength="10" style="width: 100px;" />
		</td>
</tr>



<!--tr><td class="label">Index page:</td>
<td>
<?php
build_yes_no_checkbox("np_indexed", get_np_var("indexed"));
?>
</td></tr-->
<tr><td colspan="2" class="formbtns">
	<input type="submit" name="save" class="btn btn-md btn-primary" value="Save Details" />
</td></tr>
</table>
</form>
<script>
document.basic.np_title.focus();
</script>
