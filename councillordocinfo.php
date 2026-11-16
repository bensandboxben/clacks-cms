<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$docid = $_REQUEST["docid"];
$doc=$db->get_var("SELECT name FROM councillordocs WHERE docid='$docid'");
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<h3><?php echo $doc; ?></h3>
<div id="formarea">
<h4>Document files</h4>
<ul>
<?php
if ($docfiles = $db->get_results("SELECT * FROM councillordocfiles WHERE docid='$docid'")) {
	foreach($docfiles as $docfile) {
		if ($docfile->mime) {
			echo '<li><strong><a target="_blank" href="/portal_document/' . $docfile->docfileid . '_'. $docfile->filename . $docfile->ext . '">' . $docfile->filename . '</a></strong><br />' . $docfile->description . '</li>';
		} else {
			echo '<li><strong><a target="_blank" href="' . $docfile->url . '">' . $docfile->url . '</a></strong><br />' . $docfile->description . '</li>';
		}
	}
} else {
	echo '<li>No document files uploaded for this document.</li>';
}
?>
</ul>

<p class="formbtns"><input class="btn btn-lg btn-primary" type="submit" value="Close" onclick="self.close();"></p>
</div>
</body>
</html>