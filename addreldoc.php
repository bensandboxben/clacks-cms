<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$curr_docs = get_np_var('reldocs');
if (isset($_GET["del"])) {
	unset($curr_docs[$_GET["del"]]);
	set_np_var('reldocs', $curr_docs);
}
if ($_POST["addbtn"]) {
	$curr_docs[] = array("docid"=>$_POST["reldoc"]);
	set_np_var('reldocs', $curr_docs);
}
$instr = "";
if (is_array($curr_docs)) {
	foreach ($curr_docs as $doc) {
		$instr .= $doc["docid"] . ',';
	}
}
$instr .= '0';
$query = 'SELECT docid, name FROM docs WHERE docid NOT IN (' . $instr . ')';
if ($_POST["clearfilter"]) unset($_SESSION["filters"]["reldocs"]);
if ($_POST["filter"]) $_SESSION["filters"]["reldocs"] = $_POST["filter"];
if (isset($_SESSION["filters"]["reldocs"])) $query .= ' AND name LIKE "%' . $_SESSION["filters"]["reldocs"] . '%"';
$query .= ' ORDER BY name';
$reldocs = $db->get_results($query, ARRAY_N);
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<h3>Add related document</h3>
<p>Adding related documents to page: <?php echo get_np_var('title'); ?></p>
<form method="post" action="addreldoc.php">
<p>Filter: <input type="text" name="filter" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="filterbtn" value="Filter" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="clearfilter" value="Clear" /></p>
<table class="table table-striped table-bordered">
<tr><td>Document to link to</td><td>Action</td></tr>
<tr><td style="width:30%">
<?php build_select_box($reldocs, "reldoc", ""); ?>
</td>
<td><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="addbtn" value="+" /></td>
</tr>
<?php
if (is_array($curr_docs) && count($curr_docs) > 0) {
	foreach ($curr_docs as $key=>$curr_doc) {
		$name = $db->get_var("SELECT name FROM docs WHERE docid='{$curr_doc["docid"]}'");
		echo '<tr><td>' . $name . '</td><td><a href="addreldoc.php?del=' . $key . '"><img border="0" src="images/delete.gif" alt="Delete" /></a></td></tr>';
	}
}
?>
</table>
<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="savedocs" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p></form>
</body>
</html>