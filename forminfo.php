<?php


include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$formid = $_REQUEST["formid"];
$form=$db->get_var("SELECT name FROM forms WHERE formid='$formid'");
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include "includes/head.inc.php"; ?>
</head>
<body class="popup">
<h3><?php echo $form; ?></h3>
<div id="formarea">
<h4>Form files</h4>
<ul>
<?php
if ($formfiles = $db->get_results("SELECT * FROM formfiles WHERE formid='$formid'")) {
	foreach($formfiles as $formfile) {
		if ($formfile->mime) {
			echo '<li><strong><a target="_blank" href="download.php?ft=form&file=' . $formfile->formfileid . $formfile->ext . '">' . $formfile->filename . '</a></strong><br />' . $formfile->description . '</li>';
		} else {
			echo '<li><strong><a target="_blank" href="' . $formfile->url . '">' . $formfile->url . '</a></strong><br />' . $formfile->description . '</li>';
		}
	}
} else {
	echo '<li>No form files uploaded for this form.</li>';
}
?>
</ul>
<h4>ClacksWeb links</h4>
<ul>
<?php
if ($pages = $db->get_results("SELECT pagename, title, sectionurl FROM pageXform LEFT JOIN content USING(pageid) LEFT JOIN sections USING(sectionid) WHERE formid='$formid'")) {
	foreach($pages as $page) {
		echo '<li><strong><a href="/' . $page->sectionurl . '/' . $page->pagename . '/" target="_blank">' . $page->title . '</a></strong></li>';
	}
} else {
	echo '<li>No links to this form from ClacksWeb.</li>';
}
?>
</ul>
<h4>FAQ system links</h4>
<ul>
<?php
$faqs = $db->get_results("SELECT faq AS label, 'f' AS type ,faqs.faqid AS id FROM cms.faq_form_map LEFT JOIN cms.faqs USING(faqid) WHERE formid='$formid'", ARRAY_A);
$subjects = $db->get_results("SELECT subject AS label, 's' AS type, subjects.subjectid AS id FROM cms.subject_form_map LEFT JOIN cms.subjects USING(subjectid) WHERE formid='$formid'", ARRAY_A);
if ($faqs === null) {
    $faqs = array();
}
if ($subjects === null) {
    $subjects = array();
}
$links = array_merge($faqs, $subjects);


if ($links = array_merge($faqs, $subjects)) {
	foreach($links as $link) {
		echo '<li><strong>';
		if ($link["type"] == 's') {
			echo '<a href="http://faqs/subject.php?subjectid=' . $link["id"] . '" target="_blank">';
		} else {
			echo '<a href="#" onclick="window.open(\'http://faqs/faq.php?faqid=' . $link["id"] . '\', \'faq\', \'width=700,height=600,top=50,left=50,resizable=yes,scrollbars=yes\')">';
		}
		echo $link["label"];
		echo '</a></strong></li>';
	}
} else {
	echo '<li>No FAQ system links for this form.</li>';
}
?>
</ul>
<p class="formbtns"><input type="submit" class="btn btn-md btn-secondary" value="Close" onclick="self.close();"></p>
</div>
</body>
</html>