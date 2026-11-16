<?php
np_display_errors();
if (!get_np_var('basic')) {
	echo '<p>Please complete the <a href="newintranetcontent.php?section=basic">basic details</a> for this page before setting related FAQs, documents and forms</p>';
} else {
	set_np_var("related", 1);
?>
<fieldset><legend>Related documents <a href="#" onClick="window.open('addreldoc.php', 'reldoc_add', 'width=600,height=500,top=50,left=50,resizable=yes')"><img src="images/add.gif" alt="Add external link" border="0" /></a></legend>
<?php
if (!is_array(get_np_var('reldocs'))||(count(get_np_var('reldocs')) == 0)) {
	echo '<p>No related documets currently defined.</p>';
} else {
	echo '<ul>';
	foreach (get_np_var('reldocs') as $key=>$curr_doc) {
		$name = $db->get_var("SELECT name FROM docs WHERE docid='{$curr_doc["docid"]}'");
		echo '<li>' . $name . '</li>';
	}
	echo '</ul>';
}
?>
</fieldset>
<fieldset><legend>Related forms <a href="#" onClick="window.open('addrelform.php', 'relform_add', 'width=600,height=500,top=50,left=50,resizable=yes')"><img src="images/add.gif" alt="Add external link" border="0" /></a></legend>
<?php
if (!is_array(get_np_var('relforms'))||(count(get_np_var('relforms')) == 0)) {
	echo '<p>No related forms currently defined.</p>';
} else {
	echo '<ul>';
	foreach (get_np_var('relforms') as $key=>$curr_form) {
		$name = $db->get_var("SELECT name FROM forms WHERE formid='{$curr_form["formid"]}'");
		echo '<li>' . $name . '</li>';
	}
	echo '</ul>';
}
?>
</fieldset>
<!--
<fieldset><legend>Related FAQ Subjects <a href="#" onClick="window.open('addrelfaq.php', 'relform_add', 'width=600,height=500,top=50,left=50,resizable=yes')"><img src="images/add.gif" alt="Add external link" border="0" /></a></legend>
<?php
if (!is_array(get_np_var('relfaqs'))||(count(get_np_var('relfaqs')) == 0)) {
	echo '<p>No related subjects currently defined.</p>';
} else {
	echo '<ul>';
	foreach (get_np_var('relfaqs') as $key=>$curr_faq) {
		$subject = $db->get_var("SELECT subject FROM cms.subjects WHERE subjectid='{$curr_faq["subjectid"]}'");
		echo '<li>' . $subject . '</li>';
	}
	echo '</ul>';
}
?>
</fieldset>
-->
<?php } ?>