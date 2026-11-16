<?php


/**
* Manage business directory records
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("busdir", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage business directory";
$table = "busdir.details";
$dbkey = "busid";
$keyfield = "business";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "business");
if (!$_SESSION[$table_st]) set_sort_type($table, "asc");
// setup environment variables - sort order, filter, offset
setup_env($table, "business"); // table and default sort order
include "includes/header.inc.php";
// Print table of records
//$db = new db("admin", "HbCLlVH", "busdir", "51.104.239.104");

$query = "SELECT * FROM busdir.details LEFT JOIN busdir.sections USING(sectionid) WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT busid) AS numrecs FROM busdir.details LEFT JOIN busdir.sections USING(sectionid) WHERE 1=1 ";


if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (business LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (business LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if ($_SESSION[$table . "_category"]){
	$query .= "AND details.sectionid='" . $_SESSION[$table . '_category'] . "' ";
	$count_query .= "AND details.sectionid='" . $_SESSION[$table . '_category'] . "' ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
$categories = $db->get_results("SELECT DISTINCT busdir.sections.sectionid, section FROM busdir.sections RIGHT JOIN busdir.	details USING(sectionid) ORDER BY section", ARRAY_N);
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";
$records = print_nav($table, $query, $count_query, $categories);
if ($user_message) echo $user_message;
?>

<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Business", "business", "50%");
print_column_heading("Section", "section", "25%");
print_column_heading("Created", "created", "10%");
?>
<th width="15%">Actions <a href="#" onClick="window.open('newbusiness.php', 'en_new', 'width=650,height=500,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add record" border="0"></a> <a href="#" onClick="window.open('busdirexport.php', 'bdexport', 'width=400,height=200,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/export.gif" alt="Export records" border="0"></a></th>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr>';
		echo '<td width="45%">' . $record->business . '</a></td>';
		echo '<td width="25%">' . $record->section . '</a></td>';
		echo '<td width="15%">' . date('F j, Y', $record->created) . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newbusiness.php?action=edit&busid=' . $record->$dbkey . '\', \'bus_edit\', \'width=650,height=500,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit" border="0"></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=busdir.details&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<img src="images/delete.gif" alt="delete" border="0"></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>