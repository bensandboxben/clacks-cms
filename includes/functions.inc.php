<?php

//error_reporting(E_ALL);               // Report all errors and warnings
//ini_set('display_errors', 1);         // Display errors on the screen

/**
* ClacksWeb v2 CMS core functions
* @package ClacksWeb v2 CMS
*/
/**
* Delete content record
* @param integer pageid
*/
function delete_page($pageid) {
	global $db;
	// delete content record
	$db->query("DELETE FROM content WHERE pageid='$pageid'");
	// clear internal and external links, and records for pages linking to this page
	$db->query("DELETE FROM pageXlink WHERE hostid='$pageid' OR pageid='$pageid'");
	// clear A to Z entries
	$db->query("DELETE FROM atoz WHERE id='$pageid' AND UCASE(element)='C'");
	// clear subject links
	$db->query("DELETE FROM pageXsubject WHERE pageid='$pageid'");
	// clear form links
	$db->query("DELETE FROM pageXform WHERE pageid='$pageid'");
	// clear document links
	$db->query("DELETE FROM pageXdoc WHERE pageid='$pageid'");
}
/**
* Delete contact record
* @param integer contactid
*/
function delete_contact($contactid) {
	global $db;
	// delete contact record
	$db->query("DELETE FROM contacts WHERE contactid='$contactid'");
}
/**
* Delete form record
* @param integer formid
*/
function delete_form($formid) {
	global $db;
	// delete form files
	if ($formfiles = $db->get_results("SELECT * FROM formfiles WHERE formid='$formid'")) {
		foreach ($formfiles as $formfile) {
			unlink("/www/cms/form/" . $formfile->formfileid . $formfile->ext);
			$db->query("DELETE FROM formfiles WHERE formfileid='$formfile->formfileid'");
		}
	}
	// delete A to Z entries
	$db->query("DELETE FROM atoz WHERE element='F' AND id='$formid'");
	// delete content links
	$db->query("DELETE FROM pageXform WHERE formid='$formid'");
	// delete form record
	$db->query("DELETE FROM forms WHERE formid='$formid'");
}
/**
* Delete document record
* @param integer docid
*/
function delete_doc($docid) {
	global $db;
	// delete document files
	if ($docfiles = $db->get_results("SELECT * FROM docfiles WHERE docid='$docid'")) {
		foreach ($docfiles as $docfile) {
			unlink("/www/cms/document/" . $docfile->docfileid . $docfile->ext);
			unlink_wc("/www/cms/document/" . $docfile->docfileid . "/", "*.png");
			unlink_wc("/www/cms/document/" . $docfile->docfileid . "/", "*.html");
			unlink_wc("/www/cms/document/" . $docfile->docfileid . "/", "*.txt");
			rmdir("/www/cms/document/" . $docfile->docfileid . "/");
			$db->query("DELETE FROM docfiles WHERE docfileid='$docfile->docfileid'");
		}
	}
	// delete A to Z entries
	$db->query("DELETE FROM atoz WHERE element='D' AND id='$docid'");
	// delete content links
	$db->query("DELETE FROM pageXdoc WHERE docid='$docid'");
	// delete form record
	$db->query("DELETE FROM docs WHERE docid='$docid'");
}


/**
* Delete councillordocument record
* @param integer docid
*/
function delete_councillordoc($docid) {
	global $db;
	// delete document files
	if ($docfiles = $db->get_results("SELECT * FROM councillordocfiles WHERE docid='$docid'")) {
		foreach ($docfiles as $docfile) {
			unlink("/www/cms/document/" . $docfile->docfileid . $docfile->ext);
			unlink_wc("/www/cms/document/" . $docfile->docfileid . "/", "*.png");
			unlink_wc("/www/cms/document/" . $docfile->docfileid . "/", "*.html");
			unlink_wc("/www/cms/document/" . $docfile->docfileid . "/", "*.txt");
			rmdir("/www/cms/document/" . $docfile->docfileid . "/");
			$db->query("DELETE FROM councillordocfiles WHERE docfileid='$docfile->docfileid'");
		}
	}

	// delete doc record
	$db->query("DELETE FROM councillordocs WHERE docid='$docid'");
	// delete associated info in councillordocinstances table
	$db->query("DELETE FROM councillordocinstances WHERE councillordocid='$docid'");
}

/**
* Delete councillor record
* @param integer id
*/
function delete_councillor($id) {
	global $db;
	// delete councillor entry
	$db->query("DELETE FROM councillors WHERE emid='$id'");
	// and their log in
	$db->query("DELETE FROM councillorsLogin WHERE loginid='$id'");

}

/**
* Delete work notices record
* @param integer id
*/
function delete_worknotices($id) {
	global $db;
	// delete work notices entry
	$db->query("DELETE FROM worknotices WHERE id='$id'");

}

/**
* Delete event record
* @param integer eventid
*/
function delete_event($eventid) {
	global $db;
	// delete category entries
	$db->query("DELETE FROM events.eventXeventcat WHERE eventid='$eventid'");
	// delete event record
	$db->query("DELETE FROM events.events WHERE eventid='$eventid'");
}
/**
* Delete election record
* @param integer electionid
*/
function delete_election($electionid) {
	global $db;
	// delete results entries
	$db->query("DELETE FROM elections.results WHERE electionid='$electionid'");
	// delete election record
	$db->query("DELETE FROM elections.elections WHERE electionid='$electionid'");
}
/**
* Delete gallery record
* @param integer galleryid
*/
function delete_gallery($galleryid) {
	global $db;
	// delete gallery record
	$db->query("DELETE FROM galleries.galleries WHERE galleryid='$galleryid'");
}
/**
* Delete road report record
* @param integer id
*/
function delete_roadreport($id) {
	global $db;
	// delete rentry
	$db->query("DELETE FROM roadreport.info WHERE id='$id'");
}
/**
* Delete road report record
* @param integer id
*/
function delete_occlic($id) {
	global $db;
	// delete rentry
	$db->query("DELETE FROM clacksweb2.occasionallicences WHERE id='$id'");
}

/**
* Delete comms questionnaire record
* @param integer id
*/
function delete_commsq($id) {
	global $db;
	// delete rentry
	$db->query("DELETE FROM communications.commsqs WHERE projectid='$id'");
}
/**
* Delete charitable collection record
* @param integer id
*/
function delete_collection($id) {
	global $db;
	// delete entry
	$db->query("DELETE FROM collections WHERE ccid='$id'");
}
/**
* Delete meeting record
* @param integer meetingid
*/
function delete_meeting($meetingid) {
	global $db;
	// delete meeting files
	$bodyid=$db->get_var("SELECT bodyid FROM events.meetings WHERE meetingid='$meetingid'");
	if ($meetingfiles = $db->get_results("SELECT * FROM events.meetingfiles WHERE meetingid='$meetingid'")) {
		foreach ($meetingfiles as $meetingfile) {
			unlink(UPLOAD_DIR . "document/meeting/" . $bodyid . "/" . $meetingid . "/" . $meetingfile->meetingfileid . $meetingfile->ext);
			$db->query("DELETE FROM events.meetingfiles WHERE meetingfileid='$meetingfile->meetingfileid'");
		}
	}
	// delete meeting record
	$db->query("DELETE FROM events.meetings WHERE meetingid='$meetingid'");
}
/**
* Delete tender record
* @param integer tenderid
*/
function delete_tender($tenderid) {
	global $db;
	// delete tender files
	if ($tenderfiles = $db->get_results("SELECT * FROM tenderfiles WHERE tenderid='$tenderid'")) {
		foreach ($tenderfiles as $tenderfile) {
			unlink(UPLOAD_DIR . "document/tenders/" . $tenderfile->tenderfileid . $tenderfile->ext);
			$db->query("DELETE FROM tenderfiles WHERE tenderfileid='$tenderfile->tenderfileid'");
		}
	}
	// delete tender record
	$db->query("DELETE FROM tenders WHERE tenderid='$tenderid'");
}
/**
* Delete vacancy record
* @param integer vacid
*/
function delete_vacancy($vacid) {
	global $db;
	// delete vacancy files
//	if ($tenderfiles = $db->get_results("SELECT * FROM tenderfiles WHERE tenderid='$tenderid'")) {
//		foreach ($tenderfiles as $tenderfile) {
//			unlink(UPLOAD_DIR . "document/tenders/" . $tenderfile->tenderfileid . $tenderfile->ext);
//			$db->query("DELETE FROM tenderfiles WHERE tenderfileid='$tenderfile->tenderfileid'");
//		}
//	}
	// delete tender record
	$db->query("DELETE FROM hr.vacancies WHERE vacid='$vacid'");
}
/**
* Delete Jobpack file
* @param integer fileid
*/
function delete_jobpackfile($fileid) {
	global $db;
	if ($file = $db->get_row("SELECT * FROM hr.files WHERE fileid='$fileid'")) {
		unlink(UPLOAD_DIR . "jobpacks/" . $file->filename);
		$db->query("DELETE FROM hr.vacXfile WHERE fileid='$fileid'");
		$db->query("DELETE FROM hr.files WHERE fileid='$fileid'");
	}
}
/**
* Delete TRO record
* @param integer troid
*/
function delete_tro($troid) {
	global $db;
	// delete TRO files
	if ($trofiles = $db->get_results("SELECT * FROM trofiles WHERE troid='$troid'")) {
		foreach ($trofiles as $trofile) {
			unlink(UPLOAD_DIR . "document/tros/" . $trofile->trofileid . $trofile->ext);
			$db->query("DELETE FROM trofiles WHERE trofileid='$trofile->trofileid'");
		}
	}
	// delete TRO record
	$db->query("DELETE FROM tros WHERE troid='$troid'");
}
/**
* Delete hygiene inspection record
* @param integer inspectionid
*/
function delete_hygieneinspection($inspectionid) {
	global $db;
	// delete inspection file
	if (file_exists(UPLOAD_DIR . "document/hygieneinspections/" . $inspectionid . ".rtf")) {
		unlink(UPLOAD_DIR . "document/hygieneinspections/" . $inspectionid . ".rtf");
	}
	// delete inspection record
	$db->query("DELETE FROM hygieneinspections WHERE inspectionid='$inspectionid'");
}
/**
* Delete hygiene inspection category
* @param integer catid
*/
function delete_hicategory($catid) {
	global $db;
	// delete inspection category
	$db->query("DELETE FROM hicategories WHERE catid='$catid'");
}
/**
* Delete emergency notice record
* @param integer enid
*/
function delete_emergencynotice($enid) {
	global $db;
	// delete notice record
	$db->query("DELETE FROM emergency.notices WHERE enid='$enid'");
	// delete related bulletins
	$db->query("DELETE FROM emergency.bulletins WHERE enid='$enid'");
}
/**
* Delete poll record
* @param integer pollid
*/
function delete_poll($pollid) {
	global $db;
	// delete poll record
	$db->query("DELETE FROM polls.polls WHERE pollid='$pollid'");
	// delete related answers
	$db->query("DELETE FROM polls.answers WHERE pollid='$pollid'");
}
/**
* Delete license record
* @param integer licid
*/
function delete_license($licid) {
	global $db;
	// delete license record
	$db->query("DELETE FROM licenses WHERE licid='$licid'");
}
/**
* Delete press release record
* @param integer prid
*/
function delete_press($prid) {
	global $db;
	// delete press record
	$db->query("DELETE FROM press WHERE prid='$prid'");
}
/**
* Delete business directory record
* @param integer busid
*/
function delete_business($busid) {
	global $db;
	// delete business record
	$db->query("DELETE FROM busdir.details WHERE busid='$busid'");
}
/**
* Delete body record
* @param integer prid
*/
function delete_body($bodyid) {
	global $db;
	// delete councillor<>body relationships
	$db->query("DELETE FROM councillorXbody WHERE bodyid='$bodyid'");
	// delete body record
	$db->query("DELETE FROM bodies WHERE bodyid='$bodyid'");
}
/**
* Delete external link record
* @param integer extlinkid
*/
function delete_extlink($extlinkid) {
	global $db;
	// delete external link
	$db->query("DELETE FROM extlinkXextlinkcat WHERE extlinkid='$extlinkid'");
	$db->query("DELETE FROM extlinks WHERE extlinkid='$extlinkid'");
}
/**
* Delete reference central link record
* @param integer rclinkid
*/
function delete_rclink($rclinkid) {
	global $db;
	// delete rc link
	$db->query("DELETE FROM rclinkXrclinkcat WHERE rclinkid='$rclinkid'");
	$db->query("DELETE FROM rclinks WHERE rclinkid='$rclinkid'");
}
/**
* Delete reference central category record
* @param integer rclinkcatid
*/
function delete_rclinkcat($rclinkcatid) {
	global $db;
	// delete rc link cat
	$db->query("DELETE FROM rclinkXrclinkcat WHERE rclinkcatid='$rclinkcatid'");
	$db->query("DELETE FROM rclinkcats WHERE rclinkcatid='$rclinkcatid'");
}
/**
* Save submitted content data
* @param array $pagedata
*/
function save_page($pagedata) {
	global $db;
	extract($pagedata);
	//if ((!isset($expirydate))||($expirydate == '0000-00-00')||($expirydate == '')) $expirydate = NULL;
	if ((!isset($reviewdate))||($reviewdate == '0000-00-00')||($reviewdate == '')) $reviewdate = date('Y-m-d', mktime (0,0,0,date("m")+3,date("d"),  date("Y")));

	$parsed_content = stripslashes($content);
	$parsed_content = str_replace('<p>&nbsp;</p>', '', $parsed_content);
	$parsed_content = str_replace('http://clacksweb', '', $parsed_content);
	//$parsed_content = str_replace('?', '&quest;', $parsed_content);	
	// Run HTML Tidy on content - http://tidy.sourceforge.net
	$config = array('clean' => FALSE, 'drop-empty-paras'=>TRUE, 'join-styles'=>FALSE, 'drop-font-tags' => TRUE, 'wrap' => 0, 'show-body-only' => 1, 'output-xhtml' => TRUE, 'word-2000'=>TRUE);
//	$parsed_content = tidy_repair_string($parsed_content, $config);
	$parsed_content = str_replace("<p>&nbsp;</p>", "", $parsed_content);
	$parsed_content = addslashes($parsed_content);
	// if pageid is set we are updating an existing page

	//escape values
	$title  = $db->escape($title);
	$keywords = $db->escape($keywords);
	$description = $db->escape($description);

	if ($pageid) {
		$newpageid = $pageid;


if (empty($settlementid)) {
    $settlementid = 0;
}
if (empty($scriptid)) {
    $scriptid = 0;
}
if (empty($domcollapse)) {
    $domcollapse = 0;
}
if (empty($mobile)) {
    $mobile = 0;
}

		
		
// clear internal and external links
		$db->query("DELETE FROM pageXlink WHERE hostid='$pageid'");
		// clear A to Z entries
		$db->query("DELETE FROM atoz WHERE id='$pageid' AND UCASE(element)='C'");
		// clear subject links
		$db->query("DELETE FROM pageXsubject WHERE pageid='$pageid'");
		// clear form links
		$db->query("DELETE FROM pageXform WHERE pageid='$pageid'");
		// clear document links
		$db->query("DELETE FROM pageXdoc WHERE pageid='$pageid' AND qualifier=''");
	// otherwise saving a new page
	} else {


//Check expiry date in PHP8
if ($expirydate === null) {
    $expirydate = '0000-00-00';
}
if (empty($settlementid)) {
    $settlementid = 0;
}
if (empty($scriptid)) {
    $scriptid = 0;
}
if (empty($domcollapse)) {
    $domcollapse = 0;
}
if (empty($mobile)) {
    $mobile = 0;
}

		
	}
	// Process internal links
	if (is_array($intlinks)) {
		foreach ($intlinks as $intlink) {
			$db->query ("INSERT INTO pageXlink (hostid, pageid, extlinkid) VALUES('$newpageid', '$intlink[pageid]', NULL)");
			if ($intlink["reciprocate"] == 1) $db->query ("INSERT INTO pageXlink (hostid, pageid, extlinkid) VALUES('$intlink[pageid]', '$newpageid', NULL)");
		}
	}
	// Process external links
	if (is_array($extlinks)) {
		foreach ($extlinks as $extlink) {
			$db->query ("INSERT INTO pageXlink (hostid, pageid, extlinkid) VALUES('$newpageid', NULL, '$extlink[extlinkid]')");
		}
	}
	// Process A to Z entries
	if (is_array($atoz_entries)) {
		foreach ($atoz_entries as $alpha=>$entry) {
			if ($entry["entry"] == $title) $entry["entry"] = "";
			$db->query("INSERT INTO atoz (id, alpha, element, entry) VALUES ('$newpageid', '$alpha', 'c', '$entry[entry]')");
		}
	}
	// Process subject links
	if (is_array($relfaqs)) {
		foreach ($relfaqs as $subject) {
			$db->query("INSERT INTO pageXsubject (pageid, subjectid) VALUES ('$newpageid', '$subject[subjectid]')");
		}
	}
	// Process form links
	if (is_array($relforms)) {
		foreach ($relforms as $form) {
			$db->query("INSERT INTO pageXform (pageid, formid) VALUES ('$newpageid', '$form[formid]')");
		}
	}
	// Process document links
	if (is_array($reldocs)) {
		foreach ($reldocs as $doc) {
			$db->query("INSERT INTO pageXdoc (pageid, docid) VALUES ('$newpageid', '$doc[docid]')");
		}
	}
}
/**
* Load content data for given pageid
* @param integer $pageid
* @return array
*/
function load_page($pageid) {
	global $db;
	$editpagedata = array();
	// load content
	$ep_content = $db->get_row("SELECT * FROM content WHERE pageid='$pageid'", ARRAY_A);
	foreach ($ep_content as $field=>$value) {
		$editpagedata[$field] = $value;
	}
	// load internal links
	if ($ep_intlinks = $db->get_results("SELECT pageid FROM pageXlink WHERE hostid='$pageid' AND pageid IS NOT NULL")) {
		$intlinks = array();
		foreach ($ep_intlinks as $ep_intlink) {
			$intlinks[]["pageid"] = $ep_intlink->pageid;
		}
		$editpagedata["intlinks"] = $intlinks;
	}
	// load external links
	if ($ep_extlinks = $db->get_results("SELECT extlinkid FROM pageXlink WHERE hostid='$pageid' AND extlinkid IS NOT NULL")) {
		$extlinks = array();
		foreach ($ep_extlinks as $ep_extlink) {
			$extlinks[]["extlinkid"] = $ep_extlink->extlinkid;
		}
		$editpagedata["extlinks"] = $extlinks;
	}
	// load A to Z entries
	if ($ep_atoz_entries = $db->get_results("SELECT alpha, element, entry FROM atoz WHERE id='$pageid' AND UCASE(element)='C'")) {
		$atoz_entries = array();
		foreach ($ep_atoz_entries as $atoz_entry) {
			if ($atoz_entry->entry == "") {
				$entry = $editpagedata["title"];
			} else {
				$entry = $atoz_entry->entry;
			}
			$atoz_entries[$atoz_entry->alpha] = array("entry"=>$entry, "element"=>"C");
		}
		$editpagedata["atoz_entries"] = $atoz_entries;
	}
	// load subject links
	if ($ep_subjects = $db->get_results("SELECT subjectid FROM pageXsubject WHERE pageid='$pageid'")) {
		$relfaqs = array();
		foreach ($ep_subjects as $ep_subject) {
			$relfaqs[] = array("subjectid" => $ep_subject->subjectid);
		}
		$editpagedata["relfaqs"] = $relfaqs;
	}
	// load form links
	if ($ep_forms = $db->get_results("SELECT formid FROM pageXform WHERE pageid='$pageid'")) {
		$relforms = array();
		foreach ($ep_forms as $ep_form) {
			$relforms[] = array("formid" => $ep_form->formid);
		}
		$editpagedata["relforms"] = $relforms;
	}
	// load document links
	if ($ep_docs = $db->get_results("SELECT docid FROM pageXdoc WHERE pageid='$pageid'")) {
		$reldocs = array();
		foreach ($ep_docs as $ep_doc) {
			$reldocs[] = array("docid" => $ep_doc->docid);
		}
		$editpagedata["reldocs"] = $reldocs;
	}
	return $editpagedata;
}
function get_sort_order ($table) { // returns current sort order for table
	return $_SESSION[$table . "_so"];
}
function set_sort_order ($table, $field) { // sets current sort order for table
	$_SESSION[$table . "_so"] = $field;
}
function get_sort_type ($table) { // returns current sort type for table
	return $_SESSION[$table . "_st"];
}
function set_sort_type ($table, $type) { // sets current sort type for table
	$_SESSION[$table . "_st"] = $type;
}
function get_filter ($table) { // returns current filter for table
	return $_SESSION[$table . "_filter"];
}
function set_filter ($table, $filter) { // sets current filter for table
	$_SESSION[$table . "_filter"] = $filter;
}
function get_offset ($table) { // returns current offset for table
	return $_SESSION[$table . "_offset"];
}
function set_offset ($table, $offset) { // sets current offset for table
	$_SESSION[$table . "_offset"] = $offset;
}
function get_category ($table) { // returns current category filter for table
	return $_SESSION[$table . "_category"];
}
function set_category ($table, $category) { // sets current offset for table
	$_SESSION[$table . "_category"] = $category;
}


function get_doctype ($table) { // returns current category filter for table
	return $_SESSION[$table . "_doctype"];
}
function set_doctype ($table, $doctype) { // sets current offset for table
	$_SESSION[$table . "_doctype"] = $doctype;
}

function setup_env ($table, $default_so) { // sets values for sort, filter, offset
	if ($_GET["so"]) {
		set_sort_order($table, $_GET["so"]);
	} else {
		if (get_sort_order($table) == "") set_sort_order($table, $default_so);
	}
	if ($_GET["st"]) {
		$_SESSION[$table . "_st"] = $_GET["st"];
	} else {
		if (get_sort_type($table) == "") set_sort_type($table, "asc");
	}
	if (isset($_GET["filtertxt"])) set_filter($table, $_GET["filtertxt"]);
	if (isset($_GET["category"])) set_category($table, $_GET["category"]);
	if (isset($_GET["offset"])) set_offset($table, $_GET["offset"]);
	if (isset($_GET["doctype"])) set_doctype($table, $_GET["doctype"]);
}

function print_calendar_nav($table, $query, $countquery, $categories=array()) {
	global $db;
	$filtertxt = get_filter($table);
	$offset = get_offset($table);
	$so = get_sort_order($table);
	$count = $db->get_row($countquery);
	$resultsnumber = $count->numrecs;
	if ($resultsnumber > 0) {
		// set limit for next / prev functionality
		if ($_SESSION['displaynum']) {
			$limit = $_SESSION['displaynum'];
		} else {
			$limit=9;
		}
		// calculate number of pages needing links
		$pages=intval($resultsnumber/$limit);
		if ($offset > ($pages*$limit)) $offset=0;
		// $pages now contains int of pages needed unless there is a remainder from division
		if ($resultsnumber%$limit) {
		   	// has remainder so add one page
		    $pages++;
		}
//		global $offset;
		if ($_GET["gopage"]) {
			$gopage = $_GET["gopage"];
			if (preg_match("/[^0-9]/", $gopage)) $gopage = 1;
			if ($gopage < 1) $gopage = 1;
			if ($gopage > $pages) $gopage = $pages;
			$offset = ($gopage-1) * $limit;
		}
		// next determine if offset has been passed to script, if not use 0
		if (empty($offset) || $offset < 0) {
		  	    $offset=0;
		}
		// get results with limit clause
		$query = $query . " limit $offset,$limit";
		$records = $db->get_results($query);
	} else {
		$limit=9;
		$page=1;
	}

	echo '<form method="get" action="' . $PHP_SELF  . '" name="filter">';
	echo '<table id="dbnav"><tr><td>';
	// If page 1, then don't show PREV
	if ($offset >= 1) {
		$prevoffset = $offset - $limit;
		$nextoffset = $offset + $limit;
		$currentpage = (floor($offset / $limit)) + 1;
		echo '<a href="' . $_SERVER["PHP_SELF"] . '?offset=' . $prevoffset;
		echo '"><img src="images/arrow_left.gif" alt="Previous page" border="0"></a>&nbsp;&nbsp;';
	} else {
		$currentpage = 1;
		$nextoffset = $limit;
		echo '<img src="images/arrow_leftoff.gif" alt="-" border="0">&nbsp;&nbsp;';
	}
	echo "Page $currentpage of $pages";
	if ($currentpage < $pages) {
		echo '&nbsp;&nbsp;<a href="' . $_SERVER["PHP_SELF"] . '?offset=' . $nextoffset . '"><img src="images/arrow_right.gif" alt="Next page" border="0"></a>';
	} else {
		echo '&nbsp;&nbsp;<img src="images/arrow_rightoff.gif" alt="-" border="0">';
	}

	echo " ($resultsnumber records)</td><td align=\"center\">";
	if (count($categories) > 0) {

		echo '<select name="category">';
		echo '<option value="" >All</option>';
		foreach ($categories as $category) {
			echo '<option value="' . $category[0] . '"';
			if (get_category($table) == $category[0]) echo ' selected="selected"';
			echo '>' . $category[1] . '</option>';
		}
		echo '</select> ';
	}
	echo '<input type="text" width="15" name="filtertxt"';
	if ($filtertxt) echo ' value="' . $filtertxt . '"';
	echo '> <input class="btn btn-md btn-primary" type="submit" value="Filter/Reset">';
	echo "</td><td align=\"right\">";
	echo 'Go to page:&nbsp;';
	echo '<input type="text" size="2" maxlength="4" name="gopage"> <input class="btn btn-md btn-primary" type="submit" value="Go" name="submitpage" />';
	if ($filtertxt) echo '<input type="hidden" name="srchtxt" value="' . $filtertxt . '">';
	echo "</td></tr></table>";
	echo "</form>";
//	echo "<p>$query<p>$count_query<p>";
	return ($records);
}

function print_nav($table, $query, $countquery, $categories=array()) {
	global $db;
	$filtertxt = get_filter($table);
	$offset = get_offset($table);
	$so = get_sort_order($table);
	$count = $db->get_row($countquery);
	$resultsnumber = $count->numrecs;
	if ($resultsnumber > 0) {
		// set limit for next / prev functionality
		if ($_SESSION['displaynum']) {
			$limit = $_SESSION['displaynum'];
		} else {
			$limit=10;
		}
		// calculate number of pages needing links
		$pages=intval($resultsnumber/$limit);
		if ($offset > ($pages*$limit)) $offset=0;
		// $pages now contains int of pages needed unless there is a remainder from division
		if ($resultsnumber%$limit) {
		   	// has remainder so add one page
		    $pages++;
		}
//		global $offset;
		if ($_GET["gopage"]) {
			$gopage = $_GET["gopage"];
			if (preg_match("/[^0-9]/", $gopage)) $gopage = 1;
			if ($gopage < 1) $gopage = 1;
			if ($gopage > $pages) $gopage = $pages;
			$offset = ($gopage-1) * $limit;
		}
		// next determine if offset has been passed to script, if not use 0
		if (empty($offset) || $offset < 0) {
		  	    $offset=0;
		}
		// get results with limit clause
		$query = $query . " limit $offset,$limit";
		$records = $db->get_results($query);
	} else {
		$limit=10;
		$page=1;
	}

	echo '<form method="get" action="' . $PHP_SELF  . '" name="filter">';
	echo '<table id="dbnav"><tr><td>';
	// If page 1, then don't show PREV
	if ($offset >= 1) {
		$prevoffset = $offset - $limit;
		$nextoffset = $offset + $limit;
		$currentpage = (floor($offset / $limit)) + 1;
		echo '<a href="' . $_SERVER["PHP_SELF"] . '?offset=' . $prevoffset;
		echo '"><img src="images/arrow_left.gif" alt="Previous page" border="0"></a>&nbsp;&nbsp;';
	} else {
		$currentpage = 1;
		$nextoffset = $limit;
		echo '<img src="images/arrow_leftoff.gif" alt="-" border="0">&nbsp;&nbsp;';
	}
	echo "Page $currentpage of $pages";
	if ($currentpage < $pages) {
		echo '&nbsp;&nbsp;<a href="' . $_SERVER["PHP_SELF"] . '?offset=' . $nextoffset . '"><img src="images/arrow_right.gif" alt="Next page" border="0"></a>';
	} else {
		echo '&nbsp;&nbsp;<img src="images/arrow_rightoff.gif" alt="-" border="0">';
	}

	echo " ($resultsnumber records)</td><td align=\"center\">";
	if (count($categories) > 0) {

				if ( $_SERVER["PHP_SELF"] == ("/cms/councillordocs.php") )
					{
						echo '<select name="doctype">';
					}
				else
					{
						echo '<select name="category">';
					}


		echo '<option value="" >All</option>';
		foreach ($categories as $category) {
			echo '<option value="' . $category[0] . '"';
			if (get_category($table) == $category[0]) echo ' selected="selected"';
			echo '>' . $category[1] . '</option>';
		}
		echo '</select> ';
	}
	echo '<input type="text" width="15" name="filtertxt"';
	if ($filtertxt) echo ' value="' . $filtertxt . '"';
	echo '> <input class="btn btn-md btn-primary" type="submit" value="Filter">';
	echo "</td><td align=\"right\">";
	echo 'Go to page:&nbsp;';
	echo '<input type="text" size="2" maxlength="4" name="gopage"> <input class="btn btn-md btn-primary" type="submit" value="Go" name="submitpage" />';
	if ($filtertxt) echo '<input type="hidden" name="srchtxt" value="' . $filtertxt . '">';
	echo "</td></tr></table>";
	echo "</form>";
//	echo "<p>$query<p>$count_query<p>";
	return ($records);
}
function print_column_heading($title, $field, $width) {
	global $table;
	echo '<td width="' . $width . '">' . $title;
	if (($_SESSION[$table . "_so"] == $field)&&($_SESSION[$table . "_st"] == "asc")) {
		echo '&nbsp;<img src="images/sa_on.gif" width="12" height="10" border="0" alt="Ascending order">';
	} else {
		echo '&nbsp;<a href="' . $PHP_SELF . '?so=' . $field . '&st=asc"><img src="images/sa_off.gif" width="12" height="10" border="0" alt="Ascending order"></a>';
	}
	if (($_SESSION[$table . "_so"] == $field)&&($_SESSION[$table . "_st"] == "desc")) {
		echo '&nbsp;<img src="images/sd_on.gif" width="12" height="10" border="0" alt="Descending order"></a>';
	} else {
		echo '&nbsp;<a href="' . $PHP_SELF . '?so=' . $field . '&st=desc"><img src="images/sd_off.gif" width="12" height="10" border="0" alt="Descending order"></a>';
	}
	echo '</td>';
}
/**
* Output doctype and html header
*/
function output_doctype() {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<?php
}
/**
* Format UNIX_TIMESTAMP to medium datetime - Feb 26 2004 15:31
*/
function timestamp_to_medium_datetime($timestamp) {
	return date("M j Y G:i", $timestamp);
}
/**
* Format UNIX_TIMESTAMP to medium date - Feb 26 2004
*/
function timestamp_to_medium_date($timestamp) {
	return date("M j Y", $timestamp);
}
/**
* Format UNIX_TIMESTAMP to short date - 26-02-2004
*/
function timestamp_to_short_date($timestamp) {
	return date("d-m-Y", $timestamp);
}
/**
* Clear new content session vars
*/
function clear_np() {
	unset($_SESSION["newpagedata"]);
}
/**
* Set variable while building new content
*/
function set_np_var($var, $value) {
	$_SESSION["newpagedata"]["$var"] = $value;
}
/**
* Get variable while building new content
*/
function get_np_var($var) {
	return $_SESSION["newpagedata"]["$var"];
}
/**
* Set error while building new content
*/
function set_np_error($var, $error) {
	$_SESSION["newpagedata"]["errors"]["$var"] = $error;
}
/**
* Get errors while building new content
*/
function get_np_errors() {
	return $_SESSION["newpagedata"]["errors"];
}
/**
* Reset new content errors
*/
function clear_np_errors() {
	$_SESSION["newpagedata"]["errors"] = array();
}
/**
* Process form submission for content section
*/
function np_save($section, $values) {
	global $db;
	foreach($values as $key=>$value) {
		if (substr($key, 0, 3) == 'np_') {
			set_np_var(substr($key, 3), stripslashes($value));
		}
	}
	$_SESSION["newpagedata"]["errors"] = array();
	switch ($section) {
		case 'basic':
			// do basic details validation
			set_np_var('basic', 0);
			if (!get_np_var('title')) {
				set_np_error("title", "Please enter a page title");
			} else {
				if (get_np_var('pageid')) {
					if ($exists = $db->get_var("SELECT pageid FROM content WHERE title='" . addslashes(get_np_var('title')) . "' AND pageid != " . get_np_var('pageid'))) {
						set_np_error("title", "Title already in use - please try another");
					}
					else if ($exists = $db->get_var("SELECT pageid FROM securecontent WHERE title='" . addslashes(get_np_var('title')) . "' AND pageid != " . get_np_var('pageid'))) {
											set_np_error("title", "Title already in use - please try another");
					}
				} else {
					if ($exists = $db->get_var("SELECT pageid FROM content WHERE title='" . addslashes(get_np_var('title')) . "'")) {
						set_np_error("title", "Title already in use - please try another");
					}
					else if ($exists = $db->get_var("SELECT pageid FROM securecontent WHERE title='" . addslashes(get_np_var('title')) . "'")) {
						set_np_error("title", "Title already in use - please try another");
					}
				}
			}
			if (!get_np_var('pagename')) {
				set_np_error("pagename", "Please enter a pagename");
			} else {
				if (preg_match("/[^a-z0-9-]/", get_np_var('pagename'))) {
					set_np_error("pagename", "Invalid characters in pagename - only lowercase numbers and letters allowed");
				}
				if (!get_np_var('pageid')) {
					if ($exists = $db->get_var("SELECT pageid FROM content WHERE pagename='" . get_np_var('pagename') . "' AND sectionid='" . get_np_var("sectionid") . "'")) {
						set_np_error("pagename", "Pagename already in use - please try another");
					}
					else if ($exists = $db->get_var("SELECT pageid FROM securecontent WHERE pagename='" . get_np_var('pagename') . "' AND sectionid='" . get_np_var("sectionid") . "'")) {
						set_np_error("pagename", "Pagename already in use - please try another");
					}
				}
			}
			if (!get_np_var('sectionid')) set_np_error("sectionid", "Please select a section");


			if (get_np_var('expirydate')) {
				if (!preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})/", get_np_var('expirydate'))) {
					set_np_error("expirydate", "Invalid expiry date. Please use the format YYYY-MM-DD");
				} else {
					if (!checkdate(substr(get_np_var('expirydate'),5,2), substr(get_np_var('expirydate'),8,2), substr(get_np_var('expirydate'),0,4))) set_np_error("expirydate", "Invalid expiry date.");
				}
			}
			if (get_np_var('reviewdate')) {
				if (get_np_var('reviewdate') == '0000-00-00') set_np_var('reviewdate', $reviewdate = date('Y-m-d', mktime (0,0,0,date("m")+3,date("d"),  date("Y"))));
				if (!preg_match("/^([0-9]{4})-([0-9]{2})-([0-9]{2})/", get_np_var('reviewdate'))) {
					set_np_error("reviewdate", "Invalid review date. Please use the format YYYY-MM-DD");
				} else if (!checkdate(substr(get_np_var('reviewdate'),5,2), substr(get_np_var('reviewdate'),8,2), substr(get_np_var('reviewdate'),0,4))) {
					set_np_error("reviewdate", "Invalid review date.");
				} else {
					if (((mktime(0,0,0,substr(get_np_var('reviewdate'),5,2), substr(get_np_var('reviewdate'),8,2), substr(get_np_var('reviewdate'),0,4)))-(mktime (0,0,0,date("m"),date("d"),  date("Y"))))>31536000) {
						set_np_var('reviewdate', $reviewdate = date('Y-m-d', mktime (0,0,0,date("m"),date("d"),  date("Y")+1)));
					}
				}
			}



			if (!isset($_POST["np_sectionid"])) set_np_error("sectionid", "Please select a section");
			// checkboxes don't send empty values so we catch them here
			if (empty($_POST["np_indexed"])) set_np_var('indexed', 0);
			if (empty($_POST["np_printtitle"])) set_np_var('printtitle', 0);
			// if no errors set this section to completed
			if (!get_np_errors()) {
				$np_section = $db->get_row("SELECT sectiontitle, sectionurl FROM sections WHERE sectionid='{$_SESSION["newpagedata"]["sectionid"]}'");
				set_np_var('sectionurl', $np_section->sectionurl);
				set_np_var('sectiontitle', $np_section->sectiontitle);
				set_np_var('metaphone', metaphone(get_np_var('pagename')));
				set_np_var('basic' , 1);
			}
			break;
		case 'metadata':
			//do metadata validation
			set_np_var('metadata', 0);
			if (!isset($_POST["np_lgcltermid"]) || ($_POST["np_lgcltermid"] == 0)) set_np_error("lgcltermid", "Please select an LGCL category");
			if (!get_np_var('keywords')) set_np_error("keywords", "Please enter some keywords");
			if (!get_np_var('description')) set_np_error("description", "Please enter a brief description of this page");
			// if no errors set this section to completed
			if (!get_np_errors()) {
				set_np_var('metadata' , 1);
			}
			break;
		case 'breadcrumbs':
			//do breadcrumbs validation
			set_np_var('breadcrumbs', 0);
			$parents = '';
			if (get_np_var('parent1')) {
				$parents = get_np_var('parent1');
				if (get_np_var('parent2')) {
					$parents .= '|' . get_np_var('parent2');
				}
			}
			set_np_var('parents', $parents);
			// if no errors set this section to completed
			if (!get_np_errors()) {
				set_np_var('breadcrumbs' , 1);
			}
			break;
		case 'contacts':
			//do contacts validation
			// checkboxes don't send empty values so we catch them here
			if (empty($_POST["np_printcontact"])) set_np_var('printcontact', 0);
			if (empty($_POST["np_namecontact"])) set_np_var('namecontact', 0);
			if (empty($_POST["np_contactform"])) set_np_var('contactform', 0);
			if (empty($_POST["np_pageemail"])) set_np_var('pageemail', 0);
			if (empty($_POST["np_pagefeedback"])) set_np_var('pagefeedback', 0);
			set_np_var('contacts', 0);
			if (!get_np_var('serviceid')) set_np_error("serviceid", "Please set the owner for this page");
			if (!get_np_var('contactid')) set_np_error("contactid", "Please set the contact for this page");
			// if no errors set this section to completed
			if (!get_np_errors()) {
				set_np_var('contacts' , 1);
			}
			break;
		case 'contents':
			$parsed_content = stripslashes($_POST["np_content"]);
			set_np_var("content", $parsed_content);
			//do content validation
			set_np_var('contents', 0);
			if ((!get_np_var('content'))&&(!get_np_var('scriptid'))) set_np_error("content", "Please select a script or enter some content");
			// if no errors set this section to completed
			if (!get_np_errors()) {
				set_np_var('contents' , 1);
			}
			break;
	}
}
/**
* Build HTML select box from array
* @param array $options
* @param string $name
* @param string $checked_val
* @param string $nullval
* @return bool
*/
function build_select_box ($options, $name, $checked_val="xzxz", $nullval="") {
   	echo '<select name="'.$name.'" id="'.$name.'">';
	if ($nullval) echo '<option value="">' . $nullval . '</option>';
   	foreach ($options as $option) {
   	    echo '<option value="' . $option[0] . '"';
   	    if ($option[0] == $checked_val) {
   	        echo ' selected';
   	    }
   	    echo '>' . $option[1] . '</option>';
  	 }
  	 echo '
   	    </select>';
}
/**
* Build HTML multi-select box from array
* @param array $options
* @param string $name
* @param string $checked_val
* @param string $nullval
* @return bool
*/
function build_multiple_select_box ($options, $name, $checked_array, $size = '8') {
    echo '<select name="' . $name . '" multiple size="' . $size . '">';
    foreach ($options as $option) {
        echo '
        <option value="' . $option[0] . '"';
        if (is_array($checked_array) && in_array($option[0], $checked_array)) {
            echo ' selected="selected"';
        }
        echo '>' . $option[1] . '</option>';
    }
    echo '</select>';
}
/**
* Build HTML select box with Yes / No options
* @param string $name
* @param string $checked_val
* @return bool
*/
function build_yes_no_box ($name, $checked_val="xzxz") {
	$options = array("0"=>"No", "1"=>"Yes");
   	echo '<select name="'.$name.'" id="'.$name.'">';
   	foreach ($options as $key=>$value) {
   	    echo '<option value="' . $key . '"';
   	    if ($key == $checked_val) {
   	        echo ' selected';
   	    }
   	    echo '>' . $value . '</option>';
  	 }
  	 echo '
   	    </select>';
}
/**
* Build HTML checkbox for Yes / No options - 0=No, 1=Yes
* @param string $name
* @param string $checked_val
* @return bool
*/
function build_yes_no_checkbox ($name, $checked_val="xzxz") {
   	echo '<input type="checkbox" class="formcb" name="'.$name.'" id="'.$name.'" value="1"';
    if ($checked_val) {
        echo ' checked="checked"';
    }
    echo ' />';
}
/**
* Show section errors for new content
*/
function np_display_errors() {
	if ($errors = get_np_errors()) {
		echo '<ul class="nperrors">';
		foreach ($errors as $field=>$error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
/**
* Output breadcrumbs
* @param array $bc_array
*/
function output_breadcrumbs($bc_array) {
?>
<div id="breadcrumbs">
<?php
	$count = 1;
	$numbc = count($bc_array);
	foreach ($bc_array as $title => $url) {
		if ($count < $numbc) echo '<a title="' . $title . '" href="' . SITE_HOST . $url . '">';
		echo $title;
		if ($count < $numbc) echo '</a> > ';
		$count ++;
	}
?>
</div>
<?php
}
/**
* Make a log entry for a significant event
* @param integer $userid
* @param integer $action
*/
function log_entry($userid, $action) {
	global $db;
	$db->query("INSERT INTO log (userid, logged, action)  VALUES('$userid', UNIX_TIMESTAMP(), '$action')");
}
/**
 * Build a multiple select box for A to Z entries
 * @param array|null $entries
 */
function build_atoz_box($entries) {
    echo '<select name="atoz[]" multiple size="6" class="button">';
    $selected = array();

    if (!empty($entries) && is_array($entries)) {
        foreach ($entries as $letter) {
            if (isset($letter->alpha)) {
                echo '<option value="' . $letter->alpha . '" selected>' . $letter->alpha . '</option>';
                array_push($selected, $letter->alpha);
            }
        }
    }

    for ($x = 0; $x < 26; $x++) {
        $alpha = chr($x + 65);
        if (!in_array($alpha, $selected)) {
            echo '<option value="' . $alpha . '">' . $alpha . '</option>';
        }
    }

    echo '</select>';
}

/**
* Update A to Z db entries for given item
* @param array $values
* @param string $element
* @param integer $elementid
* @param integer $live
*/
function update_atoz($values, $element, $elementid, $live = 1) {
    global $db;
    $db->query("DELETE FROM atoz WHERE id='$elementid' AND element='$element'");
    
    if (is_array($values) && count($values) > 0) {
        foreach ($values as $value) {
            $db->query("INSERT INTO atoz (id, alpha, element, live) VALUES('$elementid', '$value', '$element', '$live')");
        }
    }
}

/**
* Check user authorisation for a given area
* @param string $area
* @param integer $userid
*/
function check_auth($area, $userid) {
	global $db;
	$auth = $db->get_var("SELECT $area FROM cms.useraccess WHERE userid='$userid'");
	return $auth;
}
/**
* Convert a PDF to HTML
* @param string $filenumber
*/
function pdftohtml($filenumber) {
	if (file_exists("/www/cwv2/document/" . $filenumber . ".pdf")) {
		if (!file_exists("/www/cwv2/document/" . $filenumber . "/")) {
			mkdir("/www/cwv2/document/" . $filenumber);
		}
		shell_exec ("/usr/local/bin/pdftohtml -c -q -noframes /www/cwv2/document/" . $filenumber . ".pdf /www/cwv2/document/" . $filenumber . "/index.html");
	}
}
/**
* Convert a PDF to Text
* @param string $filenumber
*/
function pdftotext($filenumber) {
	if (file_exists("/www/cwv2/document/" . $filenumber . ".pdf")) {
		if (!file_exists("/www/cwv2/document/" . $filenumber . "/")) {
			mkdir("/www/cwv2/document/" . $filenumber);
		}
		shell_exec ("/usr/local/bin/pdftotext -nopgbrk /www/cwv2/document/" . $filenumber . ".pdf /www/cwv2/document/" . $filenumber . "/" . $filenumber . ".txt");
	}
}
/*
  * @Name: unlink_wc($dir,$pattern)
  * @Version: 1.0
  * @Description: A unlink function that support wildcards ( * and ? )
  * (based on code taked from php.net manual comments)
  * @Author: Pablo Rosciani [ pablo@nkstudios.net ] [ http://pi.nks.com.ar ]
  * @Date: 15/10/03
  * @param string $dir      Directory to search files
  * @param string $pattern  Pattern to find.
  *
  */
function unlink_wc($dir, $pattern){
    if ($dh = opendir($dir)) {

        //List and put into an array all files
        while (false !== ($file = readdir($dh))){
            if ($file != "." && $file != "..") {
                $files[] = $file;
            }
        }
        closedir($dh);

        if (is_array($files)){

            //Split file name and extenssion
            if(strpos($pattern,".")) {
                $baseexp=substr($pattern,0,strpos($pattern,"."));
                $typeexp=substr($pattern,strpos($pattern,".")+1,strlen($pattern));

            }else{
                $baseexp=$pattern;
                $typeexp="";
            }

            //Escape all regexp Characters
            $baseexp=preg_quote($baseexp);
            $typeexp=preg_quote($typeexp);

            // Allow ? and *
            $baseexp=str_replace(array("\*","\?"), array(".*","."), $baseexp);
            $typeexp=str_replace(array("\*","\?"), array(".*","."), $typeexp);

            //Search for pattern match
            $i=0;
            foreach($files as $file) {
                $filename=basename($file);
                if(strpos($filename,".")) {
                    $base=substr($filename,0,strrpos($filename,"."));    // !!! use strrpos instead of strpos
                    $type=substr($filename,strrpos($filename,".")+1,strlen($filename)); // !!! use strrpos instead of strpos
                }else{
                    $base=$filename;
                    $type="";
                }

                if(preg_match("/^".$baseexp."$/i",$base) && preg_match("/^".$typeexp."$/i",$type))  {
                    $matches[$i]=$file;
                    $i++;
                }
            }

            while(list($idx,$val) = each($matches)){
                if (substr($dir,-1) == "/"){
                    unlink($dir.$val);
                }else{
                    unlink($dir."/".$val);
                }
            }
        }
    }
}
/**
* Create zip file for agenda items
* @param $itemid
*/
function create_zip($itemid) {
	global $db;
	$meetinginfo = $db->get_row("SELECT meetings.meetingid, meetings.bodyid FROM events.meetings LEFT JOIN events.agendas USING(meetingid) WHERE itemid='$itemid'");
	if (file_exists(UPLOAD_DIR . "/document/meeting/" . $meetinginfo->bodyid . "/" . $meetinginfo->meetingid . "/" . $meetinginfo->meetingid . ".zip")) {
		unlink(UPLOAD_DIR . "/document/meeting/" . $meetinginfo->bodyid . "/" . $meetinginfo->meetingid . "/" . $meetinginfo->meetingid . ".zip");
	}
	if ($files = $db->get_results("SELECT agendafileid, ext FROM events.agendafiles LEFT JOIN events.agendas USING(itemid) WHERE agendas.meetingid='$meetinginfo->meetingid' AND exempt=0 AND confidential=0")) {
		$zipfiles = "";
		foreach ($files as $file) {
			$zipfiles .= UPLOAD_DIR . "document/meeting/" . $meetinginfo->bodyid . "/" . $meetinginfo->meetingid . "/" . $file->agendafileid . $file->ext . " ";
		}
		$zipfile = UPLOAD_DIR . "document/meeting/" . $meetinginfo->bodyid . "/" . $meetinginfo->meetingid . "/" . $meetinginfo->meetingid . ".zip ";
		$cmd = "/usr/bin/zip -j " . $zipfile . $zipfiles;
		exec("$cmd");
	}
}

?>
