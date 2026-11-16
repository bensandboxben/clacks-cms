<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$curr_links = get_np_var('intlinks');
if (isset($_GET["del"])) {
	unset($curr_links[$_GET["del"]]);
	set_np_var('intlinks', $curr_links);
}
if ($_POST["addbtn"]) {
	if ($_POST["intlink"]) {
		$curr_links[] = array("pageid"=>$_POST["intlink"], "reciprocate"=>$_POST["reciprocate"]);
		set_np_var('intlinks', $curr_links);
	}
}
$instr = "";
if (is_array($curr_links)) {
	foreach ($curr_links as $link) {
		$instr .= $link["pageid"] . ',';
	}
}
$instr .= '0';
$query = 'SELECT pageid, title FROM securecontent WHERE pageid NOT IN (' . $instr . ')';
if ($_POST["clearfilter"]) unset($_SESSION["filters"]["intlinks"]);
if ($_POST["filter"]) $_SESSION["filters"]["intlinks"] = $_POST["filter"];
if (isset($_SESSION["filters"]["intlinks"])) $query .= ' AND title LIKE "%' . $_SESSION["filters"]["intlinks"] . '%"';
$query .= ' ORDER BY title';
$intpages = $db->get_results($query, ARRAY_N);
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include "includes/head.inc.php"; ?>
</head>
<body class="popup">
<h3>Add internal link</h3>
<p>Adding internal links to page: <?php echo get_np_var('title'); ?></p>
<div id="formarea">
<form method="post" action="addsecureintlink.php">
<p>Filter: <input type="text" name="filter" /> <input type="submit" class="btn btn-md btn-primary" name="filterbtn" value="Filter" /> <input type="submit" class="btn btn-md btn-secondary" name="clearfilter" value="Clear" /></p>
<table class="table table-striped table-bordered">
<tr><td>Page to link to</td><td>Reciprocate link?</td><td>Action</td></tr>
<tr><td>
<?php build_select_box($intpages, "intlink", "", " "); ?>
</td>
<td>
<?php build_yes_no_checkbox("reciprocate", ""); ?>
</td>
<td><input type="submit" class="btn btn-md btn-primary" name="addbtn" value="+" /></td>
</tr>
<?php
if (is_array($curr_links) && count($curr_links) > 0) {
	foreach ($curr_links as $key=>$curr_link) {
		$title = $db->get_var("SELECT title FROM securecontent WHERE pageid='{$curr_link["pageid"]}'");
		echo '<tr class="subrow"><td>' . $title . '</td><td>';
		if ($curr_link["reciprocate"]) echo 'X';
		echo '</td><td><a href="addsecureintlink.php?del=' . $key . '"><svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">delete</span></a></td></tr>';
	}
}
?>
</table>
<p class="formbtns"><input type="submit" class="btn btn-md btn-secondary" name="savelinks" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>
</form>
</div>
</body>
</html>