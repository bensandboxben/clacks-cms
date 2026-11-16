<?php
np_display_errors();
if (!get_np_var('basic')) {
	echo '<p>Please complete the <a href="newcontent.php?section=basic">basic details</a> for this page before setting breadcrumbs</p>';
} else {
	set_np_var("breadcrumbs", 1);
	$bc_array = array("Home"=>"/index.php");
	$bc_array[get_np_var('sectiontitle')] = '/' . get_np_var('sectionurl') . '/';
	if (get_np_var('parents')) {
		$parents = explode("|", get_np_var('parents'));
		foreach($parents as $parent) {
			$parentbc = $db->get_row("SELECT pagename, title, sectionurl FROM content LEFT JOIN sections USING(sectionid) WHERE pageid='$parent'");
			$bc_array["{$parentbc->title}"] = '/' . $parentbc->sectionurl . '/' . $parentbc->pagename . '/';
		}
	}
	$bc_array[get_np_var('title')] = '/';

?>
<p>Specify up to 2 parents for this page. By default the page will have breadcrumbs to the section homepage and site homepage.</p>
<p>Current breadcrumbs:</p>
<?php output_breadcrumbs($bc_array); ?>
<form method="post" action="newcontent.php">
<table class="table table-striped table-bordered">
<tr><td class="label">1st parent:</td>
<td>
<?php
$parents = $db->get_results("SELECT pageid, title FROM content WHERE sectionid=" . get_np_var('sectionid') . " ORDER BY title", ARRAY_N);
build_select_box($parents, "np_parent1", get_np_var("parent1"), "None");
?>
</td></tr>
<tr><td class="label">2nd parent:</td>
<td>
<?php
$parents = $db->get_results("SELECT pageid, title FROM content WHERE sectionid=" . get_np_var('sectionid') . " ORDER BY title", ARRAY_N);
build_select_box($parents, "np_parent2", get_np_var("parent2"), "None");
?>
</td></tr>
<tr><td colspan="2" class="formbtns">
<input type="submit" name="save" class="btn btn-md btn-primary" value="Save Breadcrumbs" />
</td></tr>
</table>
</form>
<?php } ?>