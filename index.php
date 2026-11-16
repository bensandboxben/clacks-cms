<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);


session_start(); // Start the session

$areas = [];

/**
* Login to ClacksWeb CMS
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "CMS";
include "includes/header.inc.php";
reset($areas);
?>


<div class="row rowheader">
        	<div class="col-md-4 contentpadding">


<div class="list-group">
<a class="list-group-item nonactive">
<strong>Manage</strong></a>
 

<?php
foreach ($areas as $area=>$title) {
	 if ($useraccess->$area == 1) {
		if (array_key_exists($area, $popups)) {
			$dim = explode(",", $popups[$area]);
			$width = $dim[0];
			$height = $dim[1];
			echo '<a class="list-group-item"  href="#" onClick="window.open(\'' . $area . '.php\', \'school_sess\', \'width=' . $width . ',height=' . $height . ',top=50,left=50,resizable=yes,scrollbars=yes\')">' . $title . '</a>';
		} else {
			echo '<a class="list-group-item"  href="' . $area . '.php">' . $title . '</a>';
	 }
	}
}
?>
</div>




	</div>
    <div class="col-md-4 contentpadding">


<div class="list-group">
<a class="list-group-item nonactive">
<strong>Content</strong></a>
<?php
if ($_SESSION["userlevel"] > 2) {
	$count_query = "SELECT COUNT( * ) AS numpages, content.statusid, status FROM content LEFT JOIN status USING ( statusid ), cms.services";
	if ($_SESSION["userlevel"] < 4) $count_query .= " WHERE content.serviceid=services.serviceid AND (content.serviceid='$_SESSION[userserviceid]' OR parentserviceid='$_SESSION[userserviceid]')";

	$count_query .= "  GROUP BY statusid";
	$contentrecs = $db->get_results($count_query);
	foreach ($contentrecs as $status) {
		echo '<a class="list-group-item" href="content.php?category=' . $status->statusid . '">' . $status->numpages . ' ' . $status->status . '</a>';
	}
	if ($reviews = $db->get_var("SELECT COUNT(*) AS numrevs FROM content WHERE (( TO_DAYS( reviewdate) - TO_DAYS( CURRENT_DATE ))  < 14 ) AND statusid != 4 AND reviewdate > CURRENT_DATE  AND content.serviceid = '$_SESSION[userserviceid]'")) {
		echo '<a class="list-group-item" href="content.php?so=reviewdate&st=asc&category=">' . $reviews . ' Review pending</a>';
	}
	if ($reviews = $db->get_var("SELECT COUNT(*) AS numrevs FROM content WHERE reviewdate <= CURDATE() AND statusid != 4 AND content.serviceid = '$_SESSION[userserviceid]'")) {
		echo '<a class="list-group-item" href="content.php?so=reviewdate&st=asc&category=">' . $reviews . ' Review overdue</a>';
	}
} else {
	echo '<li>None</li>';
}
?>
</div>

	</div>
    <div class="col-md-4 contentpadding">


<div class="list-group">
<a class="list-group-item nonactive">
        <strong>Tools</strong></a>
	<a class="list-group-item" href="docs/style.pdf">ClacksWeb Style Guide</a>
	<a class="list-group-item" href="docs/content.pdf">Content task guide</a>
	<a class="list-group-item" href="docs/forms.pdf">Forms task guide</a>
	<a class="list-group-item" href="docs/licensing.pdf">Licensing task guide</a>
	<a class="list-group-item" href="docs/meetings.pdf">Meetings & agendas task guide</a>
	<a class="list-group-item" href="docs/writingfortheweb.pdf">Writing for the Web guidance notes</a>
	<a class="list-group-item" target="_blank" href="http://www.xstandard.com/en/documentation/xstandard-end-user-guide/">XStandard user guide</a>
</div>

</div>

<?php include "includes/footer.inc.php"; ?>
