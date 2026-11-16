<?php
/**
* Manage ClacksWeb content
* @author Ben Short
* @CMS for internal system
* @version 0.1
*/

//error_reporting(E_ALL);
//ini_set('display_errors', 1);


include_once "includes/prepend.inc.php";
if (check_auth("citydeal", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage City Deal content";
$table = "citydeal";
$dbkey = "pageid";
$keyfield = "title";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "title");
if (!$_SESSION[$table_st]) set_sort_type($table, "asc");
// setup environment variables - sort order, filter, offset
setup_env($table, "title"); // table and default sort order
// Process new content cancel reset
if ($_POST["resetnp"]) {
	unset($_SESSION["newpagedata"]);
}
// Process new content save
if ($_POST["savenp"]) {
	save_secure_page($_SESSION["newpagedata"]);
	unset($_SESSION["newpagedata"]);
	// redirect to prevent page re-save issues
	header("Location: citydeal.php");
}
include "includes/header.inc.php";
// Print table of records
$query = "SELECT pageid, title, sectionurl, pagename, reviewdate, jobtitle, name, status, parentserviceid FROM securecontent LEFT JOIN sections USING(sectionid), contacts, status, cms.services WHERE securecontent.contactid=contacts.contactid AND securecontent.serviceid=services.serviceid AND status.statusid=securecontent.statusid ";
if ($_SESSION["userlevel"] < 4) $query .= "AND (securecontent.serviceid='$_SESSION[userserviceid]' OR services.parentserviceid='$_SESSION[userserviceid]') ";
$count_query="SELECT COUNT(DISTINCT pageid) AS numrecs FROM securecontent, cms.services WHERE services.serviceid=securecontent.serviceid ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (title LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR description LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR keywords LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (title LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR description LIKE '%" . $_SESSION[$table . '_filter'] . "%' OR keywords LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if ($_SESSION[$table . "_category"]){
	$query .= "AND securecontent.statusid='" . $_SESSION[$table . '_category'] . "' ";
	$count_query .= "AND securecontent.statusid='" . $_SESSION[$table . '_category'] . "' ";
}
if ($_SESSION["userlevel"] < 4) $count_query .= "AND (securecontent.serviceid='$_SESSION[userserviceid]' OR services.parentserviceid='$_SESSION[userserviceid]') ";
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";

$statuses = $db->get_results("SELECT statusid, status FROM status ORDER BY status", ARRAY_N);
$records = print_nav($table, $query, $count_query, $statuses);
if ($user_message) echo $user_message;
?>

<table class="table table-striped table-bordered" width="100%" cellspacing="0">
<tr>
<?php
print_column_heading("Page title", "title", "30%");
print_column_heading("Contact", "jobtitle", "35%");
print_column_heading("Status", "status", "10%");
print_column_heading("Review date", "reviewdate", "10%");
?>
<td width="15%">Actions <a href="newcitydealcontent.php"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add record</span></a></td>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr onMouseOver="this.style.background=\'#EEEEAA\'" onMouseOut="this.style.background=\'#F0F2FF\'">';
		echo '<td width="30%"><a href="' . SITE_HOST . '/' . $record->sectionurl . '/' . $record->pagename . '/">' . $record->title . '</a></td>';
		echo '<td width="35%">' . $record->jobtitle . ' (' . $record->name . ')</td>';
		echo '<td width="10%"';
		if ($record->status == "Expired") echo ' class="overdue"';
		echo '>' . $record->status . '</td>';
		echo '<td width="10%"';
		if ((mktime(0,0,0,substr($record->reviewdate, 5, 2), substr($record->reviewdate, 8, 2),substr($record->reviewdate, 0, 4))) < time()) {
			echo ' class="overdue"';
		} else 		if ((mktime(0,0,0,substr($record->reviewdate, 5, 2), substr($record->reviewdate, 8, 2),substr($record->reviewdate, 0, 4))-1209600) < time()) {
			echo ' class="imminent"';
		} else {
			echo ' class="indate"';
		}
		echo '>' . $record->reviewdate;
		echo '</td>';
		echo '<td width="15%">';
		echo '<a href="newcitydealcontent.php?action=edit&id=' . $record->$dbkey . '"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">edit</span></a> ';
		echo '<a href="newcitydealcontent.php?action=copy&id=' . $record->$dbkey . '"><svg class="icon icon-copy" aria-hidden="true" focusable="false"><use href="images/icons.svg#copy"></use></svg><span class="sr-only">copy</span></a> ';
		echo "<a href=\"#\" onClick=\"window.open('securedelete.php?table=securecontent&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">delete</span></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>