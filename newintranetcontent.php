<?php
/**
* Add new ClacksWeb content
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
// Check editing rights
if (($_GET["id"])&&($_SESSION["userlevel"] < 4)) {
	if (($_SESSION["userserviceid"] != $db->get_var("SELECT serviceid FROM content WHERE pageid='$_GET[id]'"))&&($_SESSION["userserviceid"] != $db->get_var("SELECT parentserviceid FROM content LEFT JOIN cms.services USING(serviceid) WHERE pageid='$_GET[id]'"))) {
		header ("Location: /cms/content.php");
	}
}
header("Cache-Control: private");
/**
* Preprocessing for new content
*/
// Sections for creating new content
$sections=array("basic" => "Basic details",
				"metadata" => "Metadata",
				"contacts" => "Ownership &amp; contacts",
				"contents" => "Content",
				"breadcrumbs" => "Breadcrumbs",
				"links" => "Links",
				"related" => "Forms &amp; documents",
				"atoz" => "A to Z");
$required_sections = array("basic", "metadata", "contacts", "contents");
/**
* If editing collect existing page data and set edit flag
*/
if (($_GET["action"] == "edit")&&($_GET["id"])) {
	$_SESSION["newpagedata"] = load_page($_GET["id"]);
	set_np_var("currentsection", "basic");
	if (get_np_var("expirydate") == "0000-00-00") set_np_var("expirydate", '');
	foreach($sections as $sectioncode => $section) {
		set_np_var($sectioncode, 1);
	}
	$ep_section = $db->get_row("SELECT sectiontitle, sectionurl FROM sections WHERE sectionid='{$_SESSION[newpagedata][sectionid]}'");
	set_np_var('sectionurl', $ep_section->sectionurl);
	set_np_var('sectiontitle', $ep_section->sectiontitle);
} else if (($_GET["action"] == "copy")&&($_GET["id"])) {
	$_SESSION["newpagedata"] = load_page($_GET["id"]);
	set_np_var('pageid', '');
	if (get_np_var("expirydate") == "0000-00-00") set_np_var("expirydate", '');
	set_np_var("currentsection", "basic");
	foreach($sections as $sectioncode => $section) {
		set_np_var($sectioncode, 1);
	}
	set_np_var("basic", 0);
	$ep_section = $db->get_row("SELECT sectiontitle, sectionurl FROM sections WHERE sectionid='{$_SESSION[newpagedata][sectionid]}'");
	set_np_var('sectionurl', $ep_section->sectionurl);
	set_np_var('sectiontitle', $ep_section->sectiontitle);
}
if (!isset($_SESSION["newpagedata"])) {
	$_SESSION["newpagedata"] = array();
	set_np_var("currentsection", "basic");
	set_np_var("content", "<p>Enter content...</p>");
	foreach($required_sections as $sectioncode) {
		set_np_var($sectioncode, 0);
	}
} else if (($_GET["section"])&&(array_key_exists($_GET["section"], $sections))) {
	set_np_var("currentsection", $_GET["section"]);
	clear_np_errors();
}
if (!get_np_var('created')) {
	set_np_var('created', time());
	set_np_var('modified', time());
}
// Process save
if ($_POST["save"]) {
	np_save(get_np_var("currentsection"), $_POST);
}
$CMStitle = "New content";
include "includes/header.inc.php";
// Check for access rights to this area
?>
<div id="sections">
<ul>
<?php
foreach ($sections as $sectioncode=>$section) {
	echo '<li><a href="newcontent.php?section=' . $sectioncode . '"';
	if (get_np_var(currentsection) == $sectioncode) echo ' id="current"';
	if (in_array($sectioncode, $required_sections)) {
		if (get_np_var($sectioncode)) {
			echo ' class="complete"';
		} else {
			echo ' class="incomplete"';
		}
	} else {
		if (get_np_var($sectioncode)) {
			echo ' class="complete"';
		} else {
			echo ' class="optional"';
		}
	}
	echo '>' . $section . '</a>';
	echo '</li>';
}
?>
</ul>
<form name="np_global" action="content.php" method="post">
<?php
$cansave = 1;
reset ($required_sections);
foreach ($required_sections as $sectioncode) {
	$stillcansave = get_np_var($sectioncode);
	if ($stillcansave == 0) $cansave = 0;
}
if ($cansave) echo ' <input type="submit" class="btn btn-md btn-success" name="savenp" value="Commit page" />';
?>
<input type="submit" class="btn btn-md btn-secondary" name="resetnp" value="Cancel &amp; reset" />
</form>
<p><a href="content.php">Back to content list</a></p>
</div>
<div class="main" id="formarea">
<?php
echo '<h3>' . $sections[$_SESSION["newpagedata"]["currentsection"]];
if ($editedpage = get_np_var("title")) echo ' - ' . $editedpage;
echo '</h3>';
include "includes/forms/np_" . get_np_var("currentsection") . ".inc.php";
//echo '<p>Created: ' . timestamp_to_medium_datetime(get_np_var('created')) . '; modified: ' . timestamp_to_medium_datetime(get_np_var('modified')) . '</p>';
?>
</div>
<div class="clear"></div>
<?php
include "includes/footer.inc.php";
?>
