<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$extlinkid = $_REQUEST["extlinkid"];
$extlink=$db->get_var("SELECT title FROM extlinks WHERE extlinkid='$extlinkid'");
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include "includes/head.inc.php"; ?>
</head>
<body class="popup">
<h3><?php echo $extlink; ?></h3>
<div id="formarea">
<h4>ClacksWeb links</h4>
<ul>
<?php
if ($pages = $db->get_results("SELECT pagename, title, sectionurl FROM pageXlink LEFT JOIN content ON (hostid=content.pageid) LEFT JOIN sections USING(sectionid) WHERE extlinkid='$extlinkid'")) {
	foreach($pages as $page) {
		echo '<li><strong><a href="/' . $page->sectionurl . '/' . $page->pagename . '/" target="_blank">' . $page->title . '</a></strong></li>';
	}
} else {
	echo '<li>No pages on ClacksWeb use this external link.</li>';
}
?>
</ul>
<h4>Links database</h4>
<ul>
<?php
if ($entries = $db->get_results("SELECT extlinkcat, extlinkcaturl FROM extlinkXextlinkcat LEFT JOIN extlinkcats USING(extlinkcatid) WHERE extlinkid='$extlinkid'")) {
	foreach($entries as $entry) {
		echo '<li><strong><a href="/site/links/' . $entry->extlinkcaturl . '/" target="_blank">' . $entry->extlinkcat . '</a></strong></li>';
	}
} else {
	echo '<li>No entries in the links database for this external link.</li>';
}
?>
</ul>
<p class="formbtns"><input type="submit" class="btn btn-md btn-secondary" value="Close" onclick="self.close();"></p>
</div>
</body>
</html>