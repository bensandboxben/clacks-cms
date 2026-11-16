<?php
/**
* Manage communications questionnaires
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("commsqs", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage Comms Unit questionnaires";
$table = "communications.commsqs";
$dbkey = "projectid";
$keyfield = "projecttitle";
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
$query = "SELECT * FROM communications.commsqs WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT projectid) AS numrecs FROM $table WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (projecttitle LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR client LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (projecttitle LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR client LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";
$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>

<table class="cmslist" width="100%" cellspacing="0">
<tr class="cmslisthead">
<?php
print_column_heading("Project", "projecttitle", "35%");
print_column_heading("Client", "client", "25%");
print_column_heading("Sent", "created", "10%");
print_column_heading("Completed", "submitted", "10%");
?>
<td width="15%">Actions <a href="#" onClick="window.open('newcommsq.php', 'commsq_new', 'width=400,height=220,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add record" border="0"></a></td>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr onMouseOver="this.style.background=\'#EEEEAA\'" onMouseOut="this.style.background=\'#F0F2FF\'">';
		echo '<td width="35%"><a target="_blank" href="http://clacksweb/cms/comms.php?projectid=' . $record->$dbkey . '&amp;confstring=' . $record->confstring . '&view=1">' . $record->projecttitle . '</a></td>';
		echo '<td width="25%">' . $record->client . '</td>';
		echo '<td width="10%">' . date("jS F Y", $record->created) . '</td>';
		echo '<td width="10%">';
		if ($record->submitted) {
			echo date("jS F Y", $record->submitted);
		} else {
			echo 'Pending';
		}
		echo  '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newcommsq.php?action=edit&projectid=' . $record->$dbkey . '\', \'commsq_edit\', \'width=400,height=220,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit" border="0"></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=communications.commsqs&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<img src="images/delete.gif" alt="delete" border="0"></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>