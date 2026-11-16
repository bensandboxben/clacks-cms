<?php
/**
* Manage ClacksWeb content
* @author Megan Hughes
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("docs", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage councillor documents";
$table = "councillordocs";
$dbkey = "docid";
$keyfield = "name";
$table_so = $table . "_so";
$table_st = $table . "_st";


// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "name");
if (!$_SESSION[$table_st]) set_sort_type($table, "asc");
// setup environment variables - sort order, filter, offset
setup_env($table, "name"); // table and default sort order
// Process new content save
include "includes/header.inc.php";
// Print table of records
$query = "SELECT docid, name, reviewdate, doctype FROM councillordocs, doctypes WHERE doctypes.doctypeid = councillordocs.doctypeid ";
$count_query="SELECT COUNT(DISTINCT docid) AS numrecs FROM $table ";

if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (name LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR description LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "WHERE (name LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR description LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}


if (($_SESSION[$table . "_doctype"]) && ($_SESSION[$table."_filter"]) ){ // if the user filters by document type
	$query .= "AND councillordocs.doctypeid = ".$_SESSION[$table.'_doctype'];
  	$count_query .= "AND councillordocs.doctypeid = ".$_SESSION[$table.'_doctype'];
}
else if ($_SESSION[$table . "_doctype"]){
	$query .= "AND councillordocs.doctypeid = ".$_SESSION[$table.'_doctype'];
  	$count_query .= "WHERE councillordocs.doctypeid = ".$_SESSION[$table.'_doctype'];
}




if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";
$categories = $db->get_results("SELECT DISTINCT doctypes.doctypeid, doctype FROM doctypes RIGHT JOIN councillordocs USING(doctypeid) ORDER BY doctype", ARRAY_N);
$records = print_nav($table, $query, $count_query, $categories);
if ($user_message) echo $user_message;
?>

<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Document", "name", "40%");
print_column_heading("Document Type", "doctype", "35%");
print_column_heading("Review date", "reviewdate", "10%");
?>
<th width="15%">Actions <a href="#" onClick="window.open('newcouncillordoc.php?noreturn=1', 'doc_new', 'width=500,height=550,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add document" border="0"></a></th>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr>';
		echo '<td width="35%">' . $record->name . '</td>';
		echo '<td width="35%">' . $record->doctype . '</td>';
		echo '<td width="15%"';
		if ((mktime(0,0,0,substr($record->reviewdate, 5, 2), substr($record->reviewdate, 8, 2),substr($record->reviewdate, 0, 4))) < time()) {
			echo ' class="overdue"';
		} else 		if ((mktime(0,0,0,substr($record->reviewdate, 5, 2), substr($record->reviewdate, 8, 2),substr($record->reviewdate, 0, 4))-604800) < time()) {
			echo ' class="imminent"';
		} else {
			echo ' class="indate"';
		}
		echo '>' . $record->reviewdate;
		echo '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newcouncillordoc.php?noreturn=1&action=edit&id=' . $record->$dbkey . '\', \'doc_edit\', \'width=600,height=600,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit" border="0"></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=councillordocs&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<img src="images/delete.gif" alt="delete" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'councillordocfiles.php?docid=' . $record->$dbkey . '\', \'doc_files\', \'width=700,height=400,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/files.gif" alt="files" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'councillordocinfo.php?docid=' . $record->$dbkey . '\', \'doc_info\', \'width=500,height=500,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/info.gif" alt="information" border="0"></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>