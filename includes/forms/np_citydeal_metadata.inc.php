<?php
np_display_errors();
?>
<script language="JavaScript">
<!--
function doLGCLTerm (term, termid) {
document.forms['metadata'].lgclterm.value = term;
document.forms['metadata'].np_lgcltermid.value = termid;
}
//-->
</script>
<form method="post" action="newintranetcontent.php" name="metadata" id="metadata">
<table>
<tr><td class="label"><span class="required">*</span> Local Government Category List term:</td>
<td>
<input type="text" name="lgclterm" readonly="readonly" style="width: 200px;" value="<?php if ($thistermid=get_np_var("lgcltermid")) echo $db->get_var("SELECT term FROM lgcl.terms WHERE termid='$thistermid'"); ?>" /> <a href="#" onClick="window.open('lgcl.php', 'lgcl', 'width=500,height=500,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/edit.gif" alt="Select term" border="0" /></a>
<input type="hidden" name="np_lgcltermid" value="<?php echo get_np_var("lgcltermid"); ?>" />
</td></tr>
<!--
<tr><td class="label">Government Category List term:</td>
<td>
<?php
$terms = $db->get_results("SELECT t2.term AS parent, t1.termid AS termid, t1.term AS term FROM gcl AS t1, gcl AS t2 WHERE t2.termid=t1.parentid ORDER BY t1.parentid, t1.term", ARRAY_A);
$gclnodes = array();
$count = 1;
$parentterm = '';
foreach ($terms as $term) {
	if ($parentterm != $term["parent"]) {
		$gclnodes[] = array($count, $term["parent"]);
		$count++;
		$parentterm = $term["parent"];
	}
	$gclnodes[] = array($term["termid"], '&nbsp;&nbsp;--&nbsp;' . $term["term"]);
}
echo '<select name="np_gcltermid" size="14">';
echo '<option value="">-</option>';
foreach ($gclnodes as $option) {
	echo '<option value="' . $option[0] . '"';
	if ($option[0] == get_np_var("gcltermid")) {
		echo ' selected';
	}
	if ($option[0] < 13) echo ' style="background-color: #ddd;"';
	echo '>' . $option[1] . '</option>';
}
echo '</select>';
?>
</td></tr>
-->
<tr><td class="label">Settlement:</td>
<td>
<?php
$settlements = $db->get_results("SELECT settlementid, settlement FROM settlements ORDER BY settlement", ARRAY_N);
build_select_box($settlements, "np_settlementid", get_np_var("settlementid"), '--');
?>
</td></tr>
<tr><td class="label"><span class="required">*</span> Keywords:</td>
<td><textarea name="np_keywords" style="width: 300px; height: 100px;"><?php echo get_np_var("keywords"); ?></textarea></td></tr>
<tr><td class="label"><span class="required">*</span> Description:</td>
<td><textarea name="np_description" style="width: 300px; height: 100px;"><?php echo get_np_var("description"); ?></textarea></td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" class="btn btn-md btn-primary"  name="save" value="Save Metadata" />
</td></tr>
</table>
</form>