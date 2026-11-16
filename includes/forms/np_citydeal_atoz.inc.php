<?php
np_display_errors();
if (!get_np_var('basic')) {
	echo '<p>Please complete the <a href="newintranetcontent.php?section=basic">basic details</a> for this page before setting A to Z entries</p>';
} else {
	set_np_var("atoz", 1);
?>
<fieldset><legend>A to Z entries <a href="#" onClick="window.open('addatoz.php', 'atoz_add', 'width=400,height=500,top=50,left=50,resizable=yes')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add internal link</span></a></legend>
<?php
	if (!is_array(get_np_var('atoz_entries'))||(count(get_np_var('atoz_entries')) == 0)) {
		echo '<p>No A to Z entries currently defined.</p>';
	} else {
		echo '<ul>';
		foreach (get_np_var('atoz_entries') as $key=>$curr_atoz) {
			echo '<li>' . $key . ' -> ' . $curr_atoz["entry"] . '</li>';
		}
		echo '</ul>';
	}
?>
</fieldset>
<?php } ?>