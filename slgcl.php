<?php
include_once "includes/prepend.inc.php";
if (check_auth("slgcl", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
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
function editTerm(termid) {
window.open('addslgclterm.php?termid=' + termid, 'editterm', 'width=500,height=300,top=50,left=50,resizable=yes,scrollbars=yes');
}
function addTerm(parentid) {
window.open('addslgclterm.php?parentid=' + parentid, 'addterm', 'width=500,height=300,top=50,left=50,resizable=yes,scrollbars=yes');
}
//-->
</script>
<div id="formarea">
<h3>Scottish slgcl</h3>
<?php
if ($_GET["del"]) {
	if ($children = $db->get_results("SELECT * FROM lgcl.broaderterms WHERE broadertermid='$_GET[del]'")) {
		echo '<p>Cannot delete item since it has children - delete children first.</p>';
	} else {
		$db->query("DELETE FROM lgcl.broaderterms WHERE termid='$_GET[del]'");
		$db->query("DELETE FROM lgcl.relatedterms WHERE termid='$_GET[del]' OR relatedtermid='$_GET[del]'");
		$db->query("DELETE FROM lgcl.terms WHERE termid='$_GET[del]'");
	}
}
?>
<ul><li><a href="lgcl.php">Top level</a> <img src="images/add.gif" border="0" style="vertical-align: middle;" onclick="addTerm('top')" />
<?php
if ($_GET["parentid"]) {
	if (in_array($_GET["parentid"], $_SESSION["slgcl"])) {
		$key = array_search($_GET["parentid"], $_SESSION["slgcl"]);
		$_SESSION["slgcl"] = array_slice($_SESSION["slgcl"], 0, $key+1);
	} else {
		array_push($_SESSION["slgcl"], $_GET["parentid"]);
	}
	foreach ($_SESSION["slgcl"] as $parent) {
		$parentterm = $db->get_row("SELECT * FROM lgcl.terms WHERE termid='$parent'");
		echo '<ul><li><a href="lgcl.php?parentid=' . $parentterm->termid . '">' . $parentterm->term . '</a> <img src="images/add.gif" border="0" style="vertical-align: middle;" onclick="addTerm(\'' .  $parentterm->termid . '\')" /> <img src="images/editpage.gif" border="0" style="vertical-align: middle;" onclick="editTerm(\'' .  $parentterm->termid . '\')" />';
	}
	echo '<ul>';
	$children = $db->get_results("SELECT terms.* FROM lgcl.terms LEFT JOIN lgcl.broaderterms USING(termid) WHERE broaderterms.broadertermid='$_GET[parentid]' ORDER BY term");
	foreach ($children as $child) {
		$bottom = $db->get_var("SELECT COUNT(termid) FROM lgcl.broaderterms WHERE broadertermid='$child->termid'");
		if ($bottom) {
			echo '<li><a href="lgcl.php?parentid=' . $child->termid . '">' . $child->term . '</a> <img src="images/add.gif" border="0" style="vertical-align: middle;" onclick="addTerm(\'' .  $child->termid . '\')" /> <img src="images/editpage.gif" border="0" style="vertical-align: middle;" onclick="editTerm(\'' .  $child->termid . '\')" /></li>';
		} else {
			echo '<li>' . $child->term . ' <img src="images/add.gif" border="0" style="vertical-align: middle;" onclick="addTerm(\'' .  $child->termid . '\')" /> <img src="images/editpage.gif" border="0" style="vertical-align: middle;" onclick="editTerm(\'' .  $child->termid . '\')" />  <a href="lgcl.php?del=' . $child->termid . '&parentid=' . $_GET["parentid"] . '"><img src="images/trash.gif" border="0" style="vertical-align: middle;" /></a></li>';
		}
	}
	echo '</ul>';
	foreach ($_SESSION["slgcl"] as $parent) {
		echo '</li></ul>';
	}
} else {
	$_SESSION["slgcl"] = array();
	echo '<ul>';
	$topterms = $db->get_results("SELECT COUNT( broaderterms.broadertermid )  AS parents, term, terms.termid
	FROM lgcl.terms
	LEFT JOIN lgcl.broaderterms
	ON terms.termid=broaderterms.termid
	WHERE preferred=1
	GROUP BY term
	HAVING parents=0
	ORDER BY term");
	foreach ($topterms as $term) {
		$children = $db->get_var("SELECT COUNT(termid) FROM lgcl.broaderterms WHERE broadertermid='$term->termid'");
		if ($children == 0) {
			echo '<li>' . $term->term . ' <img src="images/add.gif" border="0" style="vertical-align: middle;" onclick="addTerm(\'' .  $term->termid . '\')" /> <img src="images/editpage.gif" border="0" style="vertical-align: middle;" onclick="editTerm(\'' .  $term->termid . '\')" /> <a href="lgcl.php?del=' . $term->termid . '"><img src="images/trash.gif" border="0" style="vertical-align: middle;" /></a>';
		} else {
			echo '<li><a href="lgcl.php?parentid=' . $term->termid . '">' . $term->term . '</a> <img src="images/add.gif" border="0" style="vertical-align: middle;" onclick="addTerm(\'' .  $term->termid . '\')" /> <img src="images/editpage.gif" border="0" style="vertical-align: middle;" onclick="editTerm(\'' .  $term->termid . '\')" />';
		}
		echo '</li>';
	}
	echo '</ul>';
}
?>
</li></ul>
</div>
</body>
</html>