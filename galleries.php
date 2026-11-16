<?php
/**
* Manage galleries
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (check_auth("galleries", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: nocache");
/**
* Variables for this CMS area
*/
$CMStitle = "Manage galleries";
$table = "galleries.galleries";
$dbkey = "galleryid";
$keyfield = "name";
$table_so = $table . "_so";
$table_st = $table . "_st";
// Make sure user is logged-in
//check_login();
// set default sort type for content (title - asc)
if (!$_SESSION[$table_so]) set_sort_order($table, "name");
if (!$_SESSION[$table_st]) set_sort_type($table, "asc");
// setup environment variables - sort order, filter, offset
setup_env($table, "name"); // table and default sort order
include "includes/header.inc.php";
// Print table of records
$query = "SELECT galleryid, name, galleryurl, live, created FROM galleries.galleries LEFT JOIN cms.services USING(serviceid) WHERE 1=1 ";
if ($_SESSION["userlevel"] < 4) $query .= "AND (galleries.serviceid='$_SESSION[userserviceid]' OR parentserviceid='$_SESSION[userserviceid]') ";
$count_query="SELECT COUNT(DISTINCT galleryid) AS numrecs FROM $table, cms.services WHERE services.serviceid=galleries.serviceid ";
if ($_SESSION[$table . "_filter"]) {
	$query .= "AND (name LIKE '%" . $_SESSION[$table . '_filter'] . "%'') ";
	$count_query .= "AND (name LIKE '%" . $_SESSION[$table . '_filter'] . "%') ";
}
if ($_SESSION["userlevel"] < 4) $count_query .= "AND (galleries.serviceid='$_SESSION[userserviceid]' OR parentserviceid='$_SESSION[userserviceid]') ";
if (isset($_SESSION[$table_so])) $query .= " ORDER BY $_SESSION[$table_so] $_SESSION[$table_st]";
if ($_GET["debug"]) echo "<p>$query</p><p>$count_query</p>";

$records = print_nav($table, $query, $count_query);
if ($user_message) echo $user_message;
?>

<table class="cmslist" width="100%" cellspacing="0">
<tr class="cmslisthead">
<?php
print_column_heading("Gallery", "name", "45%");
print_column_heading("Images", "", "15%");
print_column_heading("Live?", "live", "10%");
print_column_heading("Created", "created", "15%");
?>
<td width="15%">Actions <a href="#" onClick="window.open('newgallery.php?noreturn=1', 'gallery_new', 'width=500,height=300,top=50,left=50,resizable=yes,scrollbars=yes')"><img src="images/add.gif" alt="Add gallery" border="0"></a></td>
</tr>
<?php
if (count($records) > 0) {
	foreach ($records as $record) {
		$num_images = $db->get_var("SELECT COUNT(imageid) FROM galleries.images WHERE galleryid='$record->galleryid'");
		echo '<tr onMouseOver="this.style.background=\'#EEEEAA\'" onMouseOut="this.style.background=\'#F0F2FF\'">';
		echo '<td width="45%"><a target="_blank" href="http://clacksweb/site/galleries/' . $record->galleryurl . '/">' . $record->name . '</a></td>';
		echo '<td width="15%">' . $num_images . ' images</td>';
		echo '<td width="10%">' . (($record->live) ? "Yes" : "No") . '</td>';
		echo '<td width="15%">' . date("j M Y", $record->created) . '</td>';
		echo '<td width="15%">';
		echo '<a href="#" onClick="window.open(\'newgallery.php?noreturn=1&action=edit&id=' . $record->$dbkey . '\', \'gallery_edit\', \'width=500,height=300,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/edit.gif" alt="edit gallery" border="0"></a> ';
		echo '<a href="#" onClick="window.open(\'galleryimages.php?galleryid=' . $record->$dbkey . '\', \'gallery_images\', \'width=700,height=600,top=50,left=50,resizable=yes,scrollbars=yes\')"><img src="images/images.gif" alt="edit gallery images" border="0"></a> ';
		echo "<a href=\"#\" onClick=\"window.open('delete.php?table=galleries.galleries&id=" . $record->$dbkey . "', 'delete', 'width=400,height=250,resizable=yes,scrollbars=yes,top=50,left=50')\">";
		echo '<img src="images/delete.gif" alt="delete gallery" border="0"></a> ';
		echo '</td></tr>';
	}
}
echo "</table>";
include "includes/footer.inc.php";
?>