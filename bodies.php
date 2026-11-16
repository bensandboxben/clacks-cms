<?php

$records = []; 

/**
* Manage bodies
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("members", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage bodies";
$table = "bodies";
$dbkey = "bodyid";
$keyfield = "body";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "body");
if (!$_SESSION[$table_st]) set_sort_type($table, "asc");
// setup environment variables - sort order, filter, offset
setup_env($table, "body"); // table and default sort order
include "includes/header.inc.php";
// Print table of records
$query = "SELECT bodies.*, type FROM bodies LEFT JOIN bodytypes USING(typeid) WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT bodyid) AS numrecs FROM $table WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (body LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (body LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if ($_SESSION[$table . "_category"]){
	$query .= "AND bodies.typeid='" . $_SESSION[$table . '_category'] . "' ";
	$count_query .= "AND bodies.typeid='" . $_SESSION[$table . '_category'] . "' ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";

$categories = $db->get_results("SELECT DISTINCT bodytypes.typeid, type FROM bodytypes RIGHT JOIN bodies USING(typeid) ORDER BY type", ARRAY_N);
$records = print_nav($table, $query, $count_query, $categories);
if ($user_message) echo $user_message;
?>

<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Body", "body", "70%");
print_column_heading("Type", "typeid", "15%");
?>
<th width="15%">Actions <a href="#" onClick="window.open('newbody.php', 'body_new', 'width=800,height=500,top=50,left=50,resizable=yes,scrollbars=yes')"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add record</span></a></th>
</tr>
<?php
if (is_array($records) && count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr>';
		echo '<td width="70%">';
		if ($record->type == 'Committee') {
			echo '<a href="/council/committees/' . $record->bodyurl . '/">' . $record->body . '</a>';
		} else {
			echo $record->body;
		}
		echo '</td>';
		echo '<td width="15%">' . $record->type . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newbody.php?action=edit&bodyid=' . $record->$dbkey . '\', \'body_edit\', \'width=500,height=520,top=50,left=50,resizable=yes,scrollbars=yes\')"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">edit</span></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=bodies&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">delete</span></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>