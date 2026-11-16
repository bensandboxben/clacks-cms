<?php
/**
* Manage polls
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("polls", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
$db = new db("admin", "HbCLlVH", "polls", "164.177.128.68");
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage polls";
$table = "polls.polls";
$dbkey = "pollid";
$keyfield = "title";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "title");
if (!$_SESSION[$table_st]) set_sort_type($table, "asc");
// setup environment variables - sort order, filter, offset
setup_env($table, "startdate"); // table and default sort order
include "includes/header.inc.php";
// Print table of records
$query = "SELECT * FROM polls.polls LEFT JOIN clacksweb2.categories USING(catid) WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT pollid) AS numrecs FROM $table LEFT JOIN clacksweb2.categories USING(catid) WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (subject LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR category LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (subject LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR category LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";
$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>

<table class="cmslist" width="100%" cellspacing="0">
<tr class="cmslisthead">
<?php
print_column_heading("Title", "title", "40%");
print_column_heading("Start Date", "startdate", "10%");
print_column_heading("End Date", "enddate", "10%");
print_column_heading("Category", "category", "25%");
?>
<td width="15%">Actions <a href="#" onClick="window.open('newpoll.php', 'poll_new', 'width=800,height=550,top=50,left=50,resizable=yes,scrollbars=yes')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add record</span></a></td>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr onMouseOver="this.style.background=\'#EEEEAA\'" onMouseOut="this.style.background=\'#F0F2FF\'">';
		echo '<td width="40%">' . $record->title . '</td>';
		echo '<td width="10%">' . $record->startdate . '</td>';
		echo '<td width="10%">' . $record->enddate . '</td>';
		echo '<td width="25%">' . $record->category . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newpoll.php?action=edit&pollid=' . $record->$dbkey . '\', \'poll_edit\', \'width=800,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">edit</span></a> ';
		echo '<a href="#" onClick="window.open(\'pollanswers.php?pollid=' . $record->$dbkey . '\', \'agenda\', \'width=700,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-items" aria-hidden="true" focusable="false"><use href="images/icons.svg#list"></use></svg><span class="sr-only">Answers</span></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=polls.polls&id=" . $record->$dbkey . "', 'delete', 'width=300,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">delete</span></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>