<?php
/**
* Manage vacancies
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("vacancies", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage vacancies";
$table = "hr.vacancies";
$dbkey = "vacid";
$keyfield = "jobtitle";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "closedate");
if (!$_SESSION[$table_st]) set_sort_type($table, "desc");
// setup environment variables - sort order, filter, offset
setup_env($table, "closedate"); // table and default sort order
include "includes/header.inc.php";
// Print table of records
$query = "SELECT vacid, jobno, jobtitle, closedate, live FROM hr.vacancies LEFT OUTER JOIN hr.jobtitles USING(titleid) WHERE 1=1 ";
$count_query="SELECT COUNT(DISTINCT vacid) AS numrecs FROM hr.vacancies LEFT OUTER JOIN hr.jobtitles USING(titleid) WHERE 1=1 ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (jobtitle LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
	$count_query .= "AND (jobtitle LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";
$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>

<table class="cmslist" width="100%" cellspacing="0">
<tr class="cmslisthead">
<?php
print_column_heading("Job number", "jobno", "15%");
print_column_heading("Job title", "jobtitle", "40%");
print_column_heading("Closing date", "closedate", "15%");
print_column_heading("Live?", "live", "15%");
?>
<td width="15%">Actions <a href="#" onClick="window.open('newvacancy.php', 'vac_new', 'width=800,height=550,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add record" border="0"></a></td>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		echo '<tr onMouseOver="this.style.background=\'#EEEEAA\'" onMouseOut="this.style.background=\'#F0F2FF\'">';
		echo '<td width="15%">' . $record->jobno . '</td>';
		echo '<td width="40%"><a href="/council/vacancies/' . $record->vacid . '/?hr=1">' . $record->jobtitle . '</a></td>';
		echo '<td width="15%">' . $record->closedate . '</td>';
		echo '<td width="15%">';
		 if ($record->live == 1) {
		 	echo 'Yes';
		} else {
			echo 'No';
		}
		echo '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newvacancy.php?action=edit&vacid=' . $record->$dbkey . '\', \'vac_edit\', \'width=800,height=550,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit" border="0"></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=hr.vacancies&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<img src="images/delete.gif" alt="delete" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'vacancyfiles.php?vacid=' . $record->$dbkey . '\', \'vac_files\', \'width=700,height=600,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/files.gif" alt="files" border="0"></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>