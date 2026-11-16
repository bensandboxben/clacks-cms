<?php
include_once "includes/prepend.inc.php";
if (check_auth("slgcl", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include "includes/head.inc.php"; ?>
</head>
<body class="popup">
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
<ul><li><a href="lgcl.php">Top level</a> <button type="button" class="icon-action" title="Add" onclick="addTerm('top')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add</span></button>
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
		echo '<ul><li><a href="lgcl.php?parentid=' . $parentterm->termid . '">' . $parentterm->term . '</a> <button type="button" class="icon-action" title="Add" onclick="addTerm(\'' .  $parentterm->termid . '\')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add</span></button> <button type="button" class="icon-action" title="Edit" onclick="editTerm(\'' .  $parentterm->termid . '\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#file-pen"></use></svg><span class="sr-only">Edit</span></button>';
	}
	echo '<ul>';
	$children = $db->get_results("SELECT terms.* FROM lgcl.terms LEFT JOIN lgcl.broaderterms USING(termid) WHERE broaderterms.broadertermid='$_GET[parentid]' ORDER BY term");
	foreach ($children as $child) {
		$bottom = $db->get_var("SELECT COUNT(termid) FROM lgcl.broaderterms WHERE broadertermid='$child->termid'");
		if ($bottom) {
			echo '<li><a href="lgcl.php?parentid=' . $child->termid . '">' . $child->term . '</a> <button type="button" class="icon-action" title="Add" onclick="addTerm(\'' .  $child->termid . '\')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add</span></button> <button type="button" class="icon-action" title="Edit" onclick="editTerm(\'' .  $child->termid . '\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#file-pen"></use></svg><span class="sr-only">Edit</span></button></li>';
		} else {
			echo '<li>' . $child->term . ' <button type="button" class="icon-action" title="Add" onclick="addTerm(\'' .  $child->termid . '\')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add</span></button> <button type="button" class="icon-action" title="Edit" onclick="editTerm(\'' .  $child->termid . '\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#file-pen"></use></svg><span class="sr-only">Edit</span></button>  <a href="lgcl.php?del=' . $child->termid . '&parentid=' . $_GET["parentid"] . '"><svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">Delete</span></a></li>';
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
			echo '<li>' . $term->term . ' <button type="button" class="icon-action" title="Add" onclick="addTerm(\'' .  $term->termid . '\')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add</span></button> <button type="button" class="icon-action" title="Edit" onclick="editTerm(\'' .  $term->termid . '\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#file-pen"></use></svg><span class="sr-only">Edit</span></button> <a href="lgcl.php?del=' . $term->termid . '"><svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">Delete</span></a>';
		} else {
			echo '<li><a href="lgcl.php?parentid=' . $term->termid . '">' . $term->term . '</a> <button type="button" class="icon-action" title="Add" onclick="addTerm(\'' .  $term->termid . '\')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add</span></button> <button type="button" class="icon-action" title="Edit" onclick="editTerm(\'' .  $term->termid . '\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#file-pen"></use></svg><span class="sr-only">Edit</span></button>';
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