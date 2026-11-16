<?php
/**
* Manage emergency notices
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("emergencynotices", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage emergency noticess";
$table = "emergency.notices";
$dbkey = "enid";
$keyfield = "title";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "created");
if (!$_SESSION[$table_st]) set_sort_type($table, "desc");
// setup environment variables - sort order, filter, offset
setup_env($table, "created"); // table and default sort order
include "includes/header.inc.php";
// Print table of records
$db = new db("emergency", "32xN!7b", "emergency", "164.177.128.68");
$query = "SELECT * FROM $table WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT enid) AS numrecs FROM $table WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (title LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (title LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";
$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>

<table class="cmslist" width="100%" cellspacing="0">
<tr class="cmslisthead">
<?php
print_column_heading("Notice", "title", "45%");
print_column_heading("Live", "live", "15%");
print_column_heading("Created", "created", "25%");
?>
<td width="15%">Actions <a href="#" onClick="window.open('newemergencynotice.php', 'en_new', 'width=500,height=450,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add record" border="0"></a></td>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr onMouseOver="this.style.background=\'#EEEEAA\'" onMouseOut="this.style.background=\'#F0F2FF\'">';
		echo '<td width="45%">' . $record->title . '</a></td>';
		echo '<td width="15%">' . $record->live . '</td>';
		echo '<td width="25%">' . $record->created . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newemergencynotice.php?action=edit&enid=' . $record->$dbkey . '\', \'en_edit\', \'width=500,height=450,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'ebulletins.php?enid=' . $record->$dbkey . '\', \'en_bulletinss\', \'width=600,height=400,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/bulletins.gif" alt="bulletins" border="0"></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=emergency.notices&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<img src="images/delete.gif" alt="delete" border="0"></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>