<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$school = $db->get_row("SELECT * FROM schools WHERE schoolid='$_REQUEST[schoolid]'");
if (isset($_GET["del"])) {
	// delete doc relationship
	$db->query("DELETE FROM pageXdoc WHERE pageid='" . SCHOOL_SCRIPT_ID . "' AND docid='$_GET[del]' AND qualifier='$school->schoolurl'");
}
if ($_POST["addbtn"]) {
	// save doc relationship
	$db->query("INSERT INTO pageXdoc (pageid, docid, qualifier) VALUES('" . SCHOOL_SCRIPT_ID . "', '$_POST[reldoc]', '$school->schoolurl')");
}
$curr_docs = $db->get_results("SELECT pageXdoc.*, docs.name FROM pageXdoc LEFT JOIN docs USING(docid) WHERE pageid='" . SCHOOL_SCRIPT_ID . "' AND qualifier='$school->schoolurl' ORDER BY name");
$instr = "";
if (is_array($curr_docs)) {
	foreach ($curr_docs as $doc) {
		$instr .= $doc->docid . ',';
	}
}
$instr .= '0';
$query = 'SELECT docid, name FROM docs WHERE docid NOT IN (' . $instr . ')';
if ($_POST["clearfilter"]) unset($_SESSION["filters"]["schooldocs"]);
if ($_POST["filter"]) $_SESSION["filters"]["schooldocs"] = $_POST["filter"];
if (isset($_SESSION["filters"]["schooldocs"])) $query .= ' AND name LIKE "%' . $_SESSION["filters"]["schooldocs"] . '%"';
$query .= ' ORDER BY name';
$documents = $db->get_results($query, ARRAY_N);
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<h3>Add related document</h3>
<p>Adding related documents to school: <?php echo $school->name; ?></p>
<form method="post" action="schooldocs.php">
<p>Filter: <input type="text" name="filter" /> <input type="submit" class="btn btn-md btn-primary" name="filterbtn" value="Filter" /> <input type="submit" class="btn btn-md btn-primary" name="clearfilter" value="Clear" /></p>
<table class="table table-striped table-bordered">
<tr><td>Document to link to</td><td>Action</td></tr>
<tr><td>
<?php build_select_box($documents, "reldoc", "", " "); ?>
</td>
<td><input width="300px" type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="addbtn" value="+" /></td>
</tr>
<?php
if (count($curr_docs) > 0) {
	echo '<tr><td><h4>Documents currently linked:</h4></td></tr>';
	foreach ($curr_docs as $curr_doc) {
		echo '<tr><td>' . $curr_doc->name . '</td><td><a href="schooldocs.php?del=' . $curr_doc->docid . '&schoolid=' . $_REQUEST["schoolid"] . '">Unlink</a></td></tr>';
	}
}
?>
</table>
<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="close" value="Close" onclick="window.close();" /></p>
<input type="hidden" name="schoolid" value="<?php echo $_REQUEST["schoolid"]; ?>" />
</form>
</body>
</html>