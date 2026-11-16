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
$CMStitle = "Manage councillor details";
$table = "councillors";
$dbkey = "emid";
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
$query = "SELECT emid, name, party, modified FROM councillors ";
$count_query="SELECT COUNT(DISTINCT emid) AS numrecs FROM $table ";

if ($_SESSION[$table . "_filter"]) {
//	$query .= "AND (name LIKE '%" . $_SESSION[$table . '_filter'] . "%' ) ";
//	$count_query .= "AND (name LIKE '%" . $_SESSION[$table . '_filter'] . "%' ) ";
}


if (($_SESSION[$table . "_party"]) && ($_SESSION[$table."_filter"]) ){ // if the user filters by document type
	$query .= "AND party = ".$_SESSION[$table.'_party'];
  	$count_query .= "AND party = ".$_SESSION[$table.'_party'];
}
else if ($_SESSION[$table . "_party"]){
	$query .= "AND party = ".$_SESSION[$table.'_party'];
  	$count_query .= "WHERE party = ".$_SESSION[$table.'_party'];
}




if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";
$categories = $db->get_results("SELECT party FROM parties RIGHT JOIN councillors USING(partyid) ORDER BY party", ARRAY_N);
$records = print_nav($table, $query, $count_query, $categories);
if ($user_message) echo $user_message;
?>

<table class="cmslist" width="100%" cellspacing="0">
<tr class="cmslisthead">
<?php
print_column_heading("Councillor", "name", "40%");
print_column_heading("Party", "party", "35%");
print_column_heading("Last Updated", "modified", "10%");
?>
<td width="15%">Actions <a href="#" onClick="window.open('newcouncillordetails.php?noreturn=1', 'doc_new', 'width=500,height=550,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add document" border="0"></a></td></tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr onMouseOver="this.style.background=\'#EEEEAA\'" onMouseOut="this.style.background=\'#F0F2FF\'">';
		echo '<td width="40%">' . $record->name . '</td>';
		echo '<td width="35%">' . $record->party . '</td>';
		echo '<td width="10%" class="indate">' . date("d M, Y", $record->modified) .'</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newcouncillordetails.php?noreturn=1&action=edit&emid=' . $record->emid . '\', \'doc_edit\', \'width=500,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit" border="0"></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=councillors&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<img src="images/delete.gif" alt="delete" border="0"></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>