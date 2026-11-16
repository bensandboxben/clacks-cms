<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);



include_once "includes/prepend.inc.php";
if (check_auth("busdir", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
$db = new db("admin", "HbCLlVH", "busdir", "164.177.128.68");


header("Cache-Control: private");
if ($_POST["export"]) {
//	include_once "constants.inc.php";
//	include_once "functions.inc.php";
	include( "/usr/local/lib/php/excelgen.class.php" );
	$excel = new ExcelGen("ExcelGen");
	$row=0;
	$col=0;
	$excel->WriteText($row,$col,"Business");
	$excel->WriteText($row,1,"Title");
	$excel->WriteText($row,2,"Forename");
	$excel->WriteText($row,3,"Surname");
	$excel->WriteText($row,4,"Position");
	$excel->WriteText($row,5,"Address");
	$excel->WriteText($row,6,"County");
	$excel->WriteText($row,7,"Postcode");
	$excel->WriteText($row,8,"UPRN");
	$excel->WriteText($row,9,"Settlement");
	$excel->WriteText($row,10,"Telephone");
	$excel->WriteText($row,11,"Mobile");
	$excel->WriteText($row,12,"Fax");
	$excel->WriteText($row,13,"Email");
	$excel->WriteText($row,14,"Web");
	$excel->WriteText($row,15,"Type");
	$excel->WriteText($row,16,"Section");
	$excel->WriteText($row,17,"Subsection");
	$excel->WriteText($row,18,"Other services");
	$excel->WriteText($row,19,"Fairtrade");
	$query = "SELECT * FROM busdir.details LEFT JOIN clacksweb2.settlements USING(settlementid), busdir.sections, busdir.subsections WHERE details.sectionid=sections.sectionid AND details.subsectionid=subsections.subsectionid";
	if ($_POST["export"] != "all") $query .= " AND details.sectionid = '$_POST[export]'";
	$query .= " ORDER BY business";
	$records = $db->get_results($query);
	foreach ($records as $record) {
		$row++;
		$excel->WriteText($row,0,$record->business);
		$excel->WriteText($row,1,$record->title);
		$excel->WriteText($row,2,$record->firstname);
		$excel->WriteText($row,3,$record->surname);
		$excel->WriteText($row,4,$record->position);
		$excel->WriteText($row,5,$record->address);
		$excel->WriteText($row,6,$record->county);
		$excel->WriteText($row,7,$record->postcode);
		$excel->WriteText($row,8,$record->uprn);
		$excel->WriteText($row,9,$record->settlement);
		$excel->WriteText($row,10,$record->tel);
		$excel->WriteText($row,11,$record->mobile);
		$excel->WriteText($row,12,$record->fax);
		$excel->WriteText($row,13,$record->email);
		$excel->WriteText($row,14,$record->web);
		$excel->WriteText($row,15,$record->type);
		$excel->WriteText($row,16,$record->section);
		$excel->WriteText($row,17,$record->subsection);
		$excel->WriteText($row,18,$record->otherservice);
		$excel->WriteText($row,19,$record->fairtrade);
	}
	$excel->ExcelEnd();
	header ( "Expires: Mon, 1 Apr 1974 05:00:00 GMT");
	header ( "Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT" );
	header ( "Pragma: no-cache" );
//	header ( "Content-type: application/octet-stream; name=busdir.xls" );
	header ( "Content-Disposition: attachment; filename=busdir.xls");
//	header ( "Content-Description: PHP ExcelGen Class" );
	print $excel->excel_data;
} else {
?>


<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="/css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
<link rel="stylesheet" href="css/intranetscreen.css" type="text/css">

</head>
<body bgcolor="#FFFFFF" class="popup">
<h3>Export businesses to Excel</h3>
<div id="formarea">
<form name="bdexport" method="post" action="busdirexport.php">
<table width="100%" class="table table-striped table-bordered">
<tr><td class="label">Export:</td>
<td>
<select name="export"><option value="all">All businesses</option>
<?php
$sections = $db->get_results("SELECT sectionid, section FROM busdir.sections ORDER BY section");
foreach ($sections as $section) {
	echo '<option value="' . $section->sectionid . '">' . $section->section . '</option>';
}
?>
</select>
</td></tr>
<tr><td colspan="2" class="formbtns">
<input class="btn btn-md btn-primary" type="submit" name="exportbtn" value="Export" /> <input class="btn btn-md btn-primary" type="submit" value="Cancel" onclick="javascript: window.close();" />
</td></tr>
</table>
</form>
</div>
</body>
</html>




<?php

}

?>