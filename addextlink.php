<?php

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$curr_links = get_np_var('extlinks');
if (isset($_GET["del"])) {
	unset($curr_links[$_GET["del"]]);
	set_np_var('extlinks', $curr_links);
}
if ($_POST["addbtn"]) {
	$curr_links[] = array("extlinkid"=>$_POST["extlink"]);
	set_np_var('extlinks', $curr_links);
}
$instr = "";
if (is_array($curr_links)) {
	foreach ($curr_links as $link) {
		$instr .= $link["extlinkid"] . ',';
	}
}
$instr .= '0';
$query = 'SELECT extlinkid, title FROM extlinks WHERE extlinkid NOT IN (' . $instr . ')';
if ($_POST["clearfilter"]) unset($_SESSION["filters"]["extlinks"]);
if ($_POST["filter"]) $_SESSION["filters"]["extlinks"] = $_POST["filter"];
if (isset($_SESSION["filters"]["extlinks"])) $query .= ' AND title LIKE "%' . $_SESSION["filters"]["extlinks"] . '%"';
$query .= ' ORDER BY title';
$extpages = $db->get_results($query, ARRAY_N);
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<h3>Add external link</h3>
<p>Adding external links to page: <?php echo get_np_var('title'); ?></p>
<script language="JavaScript">
<!--
function doAddExtLink (label, value) {
var s = document.forms['extlinks'].extlink;
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->
</script>
<div id="formarea">
<form name="extlinks" method="post" action="addextlink.php">
<p>Filter: <input type="text" name="filter" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="filterbtn" value="Filter" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="clearfilter" value="Clear" /></p>
<table class="table table-striped table-bordered">
<tr><td>Site to link to <a href="#" onClick="window.open('newextlink.php?noreturn=1', 'extlink_new', 'width=450,height=550,resizable=yes,scrollbars=yes,top=50,left=50')"><img src="images/add.gif" alt="Add new external link" border="0" /></a> </td><td>Action</td></tr>
<tr><td style="colour:#fff;">
<?php build_select_box($extpages, "extlink", "", " "); ?>
</td>
<td><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="addbtn" value="+" /></td>
</tr>
<?php
if (is_array($curr_links) && count($curr_links) > 0) {
	foreach ($curr_links as $key=>$curr_link) {
		$title = $db->get_var("SELECT title FROM extlinks WHERE extlinkid='{$curr_link["extlinkid"]}'");
		echo '<tr><td>' . $title . '</td><td><a href="addextlink.php?del=' . $key . '"><img src="images/delete.gif" height="18" width="18" alt="delete" border="0"></a></td></tr>';
	}
}
?>
</table>
<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="savelinks" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>
</form>
</div>
</body>
</html>