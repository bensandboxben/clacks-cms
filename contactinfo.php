<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$contactid = $_REQUEST["contactid"];
$contact=$db->get_row("SELECT name, jobtitle FROM clacksweb2.contacts WHERE contactid='$contactid'");
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<h3><?php echo $contact->jobtitle . ' (' . $contact->name . ')'; ?></h3>
<div id="formarea">
<h4>Content records</h4>
<ul>
<?php
if ($contents = $db->get_results("SELECT pageid, title, sectionurl, pagename FROM content LEFT JOIN sections USING(sectionid) WHERE contactid='$contactid' ORDER BY title")) {
	foreach($contents as $content) {
		echo '<li><strong><a target="_blank" href="' . SITE_HOST . '/' . $content->sectionurl . '/' . $content->pagename . '/">' . $content->title . '</a></strong></li>';
	}
} else {
	echo '<li>No content records for this contact.</li>';
}
?>
</ul>
<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Close" onclick="self.close();"></p>
</div>
</body>
</html>