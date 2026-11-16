<?php
np_display_errors();
if (!get_np_var('basic')) {
	echo '<p>Please complete the <a href="newcontent.php?section=basic">basic details</a> for this page before creating links</p>';
} else {
	set_np_var("links", 1);
?>
<fieldset><legend>Internal Links <a href="#" onClick="window.open('addintlink.php', 'link_add', 'width=1000,height=500,resizable=yes,scrollbars=yes,top=50,left=50')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add internal link</span></a> </legend>
<?php
if ((!is_array(get_np_var('intlinks')))||(count(get_np_var('intlinks')) == 0)) {
	echo '<p>No internal links currently defined.</p>';
} else {
	echo '<ul>';
	foreach (get_np_var('intlinks') as $key=>$curr_link) {
		$title = $db->get_var("SELECT title FROM content WHERE pageid='{$curr_link["pageid"]}'");
		echo '<li>' . $title . '</li>';
	}
	echo '</ul>';
}
?>
</fieldset>
<fieldset><legend>External Links <a href="#" onClick="window.open('addextlink.php', 'link_add', 'width=1000,height=500,resizable=yes,scrollbars=yes,top=50,left=50')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add external link</span></a> </legend>
<?php
if ((!is_array(get_np_var('extlinks')))||(count(get_np_var('extlinks')) == 0)) {
	echo '<p>No external links currently defined.</p>';
} else {
	echo '<ul>';
	foreach (get_np_var('extlinks') as $key=>$curr_link) {
		$title = $db->get_var("SELECT title FROM extlinks WHERE extlinkid='{$curr_link["extlinkid"]}'");
		echo '<li>' . $title . '</li>';
	}
	echo '</ul>';
}
?>
</fieldset>
<?php } ?>