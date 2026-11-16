<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$curr_faqs = get_np_var('relfaqs');
if (isset($_GET["del"])) {
	unset($curr_faqs[$_GET["del"]]);
	set_np_var('relfaqs', $curr_faqs);
}
if ($_POST["addbtn"]) {
	$curr_faqs[] = array("subjectid"=>$_POST["relfaq"]);
	set_np_var('relfaqs', $curr_faqs);
}
$instr = "";
if (is_array($curr_faqs)) {
	foreach ($curr_faqs as $faq) {
		$instr .= $faq["subjectid"] . ',';
	}
}
$instr .= '0';
$query = 'SELECT subjectid, subject FROM cms.subjects WHERE subjectid NOT IN (' . $instr . ')';
if ($_POST["clearfilter"]) unset($_SESSION["filters"]["relfaqs"]);
if ($_POST["filter"]) $_SESSION["filters"]["relfaqs"] = $_POST["filter"];
if (isset($_SESSION["filters"]["relfaqs"])) $query .= ' AND subject LIKE "%' . $_SESSION["filters"]["relfaqs"] . '%"';
$query .= ' ORDER BY subject';
$relfaqs = $db->get_results($query, ARRAY_N);
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<h3>Add related FAQ subject</h3>
<p>Adding related FAQ subjects to page: <?php echo get_np_var('title'); ?></p>
<form method="post" action="addrelfaq.php">
<p>Filter: <input type="text" name="filter" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="filterbtn" value="Filter" /> <input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="clearfilter" value="Clear" /></p>
<table class="table table-striped table-bordered">
<tr><td>Subject to link to</td><td>Action</td></tr>
<tr><td>
<?php build_select_box($relfaqs, "relfaq", "", " "); ?>
</td>
<td><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="addbtn" value="+" /></td>
</tr>
<?php
if (is_array($curr_faqs) && count($curr_faqs) > 0) {
	foreach ($curr_faqs as $key=>$curr_faq) {
		$subject = $db->get_var("SELECT subject FROM cms.subjects WHERE subjectid='{$curr_faq["subjectid"]}'");
		echo '<tr><td>' . $subject . '</td><td><a href="addrelfaq.php?del=' . $key . '"><img border="0" src="images/delete.gif" alt="Delete" /></a></td></tr>';
	}
}
?>
</table>
<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="savefaqs" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p></form>
</body>
</html>