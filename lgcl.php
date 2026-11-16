<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<script language="JavaScript">
<!--
function passLGCL (term, termid) {
self.opener.doLGCLTerm(term, termid);
self.close();
}
//-->
</script>
<div id="formarea">
<h3>LGCL Category term</h3>
<ul><li><a href="lgcl.php">Top level</a>
<?php
if ($_GET["parentid"]) {
	if (in_array($_GET["parentid"], $_SESSION["lgcl"])) {
		$key = array_search($_GET["parentid"], $_SESSION["lgcl"]);
		$_SESSION["lgcl"] = array_slice($_SESSION["lgcl"], 0, $key+1);
	} else {
		array_push($_SESSION["lgcl"], $_GET["parentid"]);
	}
	foreach ($_SESSION["lgcl"] as $parent) {
		$parentterm = $db->get_row("SELECT * FROM lgcl.terms WHERE termid='$parent'");
		echo '<ul><li><a href="lgcl.php?parentid=' . $parentterm->termid . '">' . $parentterm->term . '</a> <img src="images/select.gif" border="0" style="vertical-align: middle;" onclick="passLGCL(\'' .  $parentterm->term . '\',\'' .  $parentterm->termid . '\')" />';
	}
	echo '<ul>';
	$children = $db->get_results("SELECT terms.* FROM lgcl.terms LEFT JOIN lgcl.broaderterms USING(termid) WHERE broaderterms.broadertermid='$_GET[parentid]' ORDER BY term");
	foreach ($children as $child) {
		$bottom = $db->get_var("SELECT COUNT(termid) FROM lgcl.broaderterms WHERE broadertermid='$child->termid'");
		if ($bottom) {
			echo '<li><a href="lgcl.php?parentid=' . $child->termid . '">' . $child->term . '</a> <img src="images/select.gif" border="0" style="vertical-align: middle;" onclick="passLGCL(\'' .  $child->term . '\',\'' .  $child->termid . '\')" /></li>';
		} else {
			echo '<li>' . $child->term . ' <img src="images/select.gif" border="0" style="vertical-align: middle;" onclick="passLGCL(\'' .  $child->term . '\',\'' .  $child->termid . '\')" /></li>';
		}
	}
	echo '</ul>';
	foreach ($_SESSION["lgcl"] as $parent) {
		echo '</li></ul>';
	}
} else {
	$_SESSION["lgcl"] = array();
	echo '<ul>';
	$topterms = $db->get_results("SELECT COUNT(broaderterms.broadertermid) AS parents, term, terms.termid
FROM lgcl.terms
LEFT JOIN lgcl.broaderterms ON terms.termid = broaderterms.termid
WHERE preferred = 1
GROUP BY term, terms.termid
HAVING parents = 0
ORDER BY term;
");
	foreach ($topterms as $term) {
		echo '<li><a href="lgcl.php?parentid=' . $term->termid . '">' . $term->term . '</a> <img src="images/select.gif" border="0" style="vertical-align: middle;" onclick="passLGCL(\'' .  $term->term . '\',\'' .  $term->termid . '\')" /></li>';
	}
	echo '</ul>';
}
?>
</li></ul>
</div>
</body>
</html>