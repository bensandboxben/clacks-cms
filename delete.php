<?php

include_once "/home/site/wwwroot/cms/includes/prepend.inc.php";





header("Cache-Control: private");
if ((($_GET["table"])&&($_GET["id"]))||(($_POST["table"])&&($_POST["id"]))) {
	if ($_GET["table"]) {
		$table = $_GET["table"];
		$id = $_GET["id"];

	} else {
		$table = $_POST["table"];
		$id = $_POST["id"];
	}
	switch ($table) {
		case "content":
			$keyfield = "pageid";
			$title = "title";
			break;
		case "contacts":
			$keyfield = "contactid";
			$title = "jobtitle";
			break;
		case "forms":
			$keyfield = "formid";
			$title = "name";
			break;
		case "docs":
			$keyfield = "docid";
			$title = "name";
			break;
		case "councillors":
			$keyfield = "emid";
			$title = "name";
			break;
		case "councillordocs":
			$keyfield = "docid";
			$title = "name";
			break;
		case "events.events":
			$keyfield = "eventid";
			$title = "event";
			break;
		case "events.meetings":
			$keyfield = "meetingid";
			$title = "meetingdate";
			break;
		case "tenders":
			$keyfield = "tenderid";
			$title = "tender";
			break;
		case "hr.vacancies":
			$keyfield = "vacid";
			$title = "jobno";
			break;
		case "hygieneinspections":
			$keyfield = "inspectionid";
			$title = "inspectiondate";
			break;
		case "hicategories":
			$keyfield = "catid";
			$title = "category";
			break;
		case "licenses":
			$keyfield = "licid";
			$title = "name";
			break;
		case "occasionallicences":
			$keyfield = "id";
			$title = "location";
			break;
		case "press":
			$keyfield = "prid";
			$title = "subject";
			break;
		case "tros":
			$keyfield = "troid";
			$title = "title";
			break;
		case "busdir.details":
			$keyfield = "busid";
			$title = "business";
			//$db = new db("admin", "HbCLlVH", "busdir", "51.104.239.104");
			break;
		case "elections.elections":
			$keyfield = "electionid";
			$title = "electionid";
			break;
		case "roadreport.info":
			$keyfield = "id";
			$title = "road";
			break;
		case "communications.commsqs":
			$keyfield = "projectid";
			$title = "projecttitle";
			break;
		case "bodies":
			$keyfield = "bodyid";
			$title = "body";
			break;
		case "extlinks":
			$keyfield = "extlinkid";
			$title = "title";
			break;
		case "worknotices":
			$keyfield = "id";
			$title = "refno";
			break;
		case "collections":
			$keyfield = "ccid";
			$title = "ccdate";
			break;
		case "rclinks":
			$keyfield = "rclinkid";
			$title = "title";
			break;
		case "rclinkcats":
			$keyfield = "rclinkcatid";
			$title = "rclinkcat";
			break;
		case "galleries.galleries":
			$keyfield = "galleryid";
			$title = "name";
			break;
		case "hr.files":
			$keyfield = "fileid";
			$title = "title";
			break;
		case "emergency.notices":
			$keyfield = "enid";
			$title = "title";
			//$db = new db("emergency", "32xN!7b", "emergency", "51.104.239.104");
			break;
		case "polls.polls":
			$keyfield = "pollid";
			$title = "title";
			//$db = new db("admin", "HbCLlVH", "polls", "51.104.239.104");
			break;
		default:
			$text = '<p>There was a problem processing the deletion: table not found.</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:window.close();" /></p>';
	}
}
if (($_GET["table"])&&($_GET["id"])) {
// Process deletion request
	if (!isset($text)) {
		if ($table == "councillordocs"){
			$delrec = $db->get_row("SELECT " . $table . ".docid, name FROM " . $table . " WHERE docid = '$id'");

			$text .= "<p>Are you sure you want to delete <strong>$delrec->name</strong> from <strong>$table</strong>?</p>";
			$text .= '<form method="post" action="delete.php"><input type="submit" class="btn btn-lg btn-primary"  name="confirm" value="Yes" /> <input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="No" onclick="javascript:window.close();" /><input type="hidden" name="table" value="' . $table . '" /><input type="hidden" name="id" value="' . $id . '" /></form>';

		}
		else if ($table == "councillors"){
			$delrec = $db->get_row("SELECT " . $table . ".emid, name FROM " . $table . " WHERE emid = '$id'");

			$text .= "<p>Are you sure you want to delete <strong>$delrec->name</strong> from <strong>$table</strong>?</p>";
			$text .= '<form method="post" action="delete.php"><input type="submit" class="btn btn-lg btn-primary"  name="confirm" value="Yes" /> <input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="No" onclick="javascript:window.close();" /><input type="hidden" name="table" value="' . $table . '" /><input type="hidden" name="id" value="' . $id . '" /></form>';

		}
		else if ($table == "occasionallicences"){
			$delrec = $db->get_row("SELECT " . $table . ".id, location FROM " . $table . " WHERE id = '$id'");

			$text .= "<p>Are you sure you want to delete <strong>$delrec->location</strong> from <strong>$table</strong>?</p>";
			$text .= '<form method="post" action="delete.php"><input type="submit" class="btn btn-lg btn-primary"  name="confirm" value="Yes" /> <input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="No" onclick="javascript:window.close();" /><input type="hidden" name="table" value="' . $table . '" /><input type="hidden" name="id" value="' . $id . '" /></form>';

		}
		else if ($table == "worknotices"){
			$delrec = $db->get_row("SELECT " . $table . ".id, refno FROM " . $table . " WHERE id = '$id'");

			$text .= "<p>Are you sure you want to delete <strong>$delrec->refno</strong> from <strong>$table</strong>?</p>";
			$text .= '<form method="post" action="delete.php"><input type="submit" class="btn btn-lg btn-primary"  name="confirm" value="Yes" /> <input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="No" onclick="javascript:window.close();" /><input type="hidden" name="table" value="' . $table . '" /><input type="hidden" name="id" value="' . $id . '" /></form>';

		}

		else if ($delrec = $db->get_row("SELECT " . $table . ".serviceid, parentserviceid, " . $title . " AS title FROM " . $table . " LEFT JOIN cms.services USING(serviceid) WHERE " . $keyfield . " = '$id'")) {
				if (($delrec->serviceid == $_SESSION["userserviceid"])||($delrec->parentserviceid == $_SESSION["userserviceid"])||($_SESSION["userlevel"] > 3)) {
					$text = '<p>Are you really sure you want to delete "<strong>' . $delrec->title . '</strong>" from ' . $table . '?</p>';
					if ($table == "forms") {
						if ($formfiles = $db->get_results("SELECT filename FROM formfiles WHERE formid=$id")) {
							$text .= '<p>The following files associated with this form will also be deleted:</p><ul>';
							foreach ($formfiles as $formfile) {
								$text .=  '<li>' . $formfile->filename . '</li>';
							}
							$text .=  '</ul>';
						}
					}
					$text .= '<form method="post" action="delete.php"><input type="submit" class="btn btn-lg btn-primary"  name="confirm" value="Yes" /> <input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="No" onclick="javascript:window.close();" /><input type="hidden" name="table" value="' . $table . '" /><input type="hidden" name="id" value="' . $id . '" /></form>';
				} else {
					$text = '<p>You are not authorised to delete that record.</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:window.close();" /></p>';
				}
		}
		else {
			$text = '<p>There was a problem processing the deletion: record not found.</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:window.close();" /></p>';
		}
	}
} else if ($_POST["confirm"]) {
// Process confirmed deletion
	if (($_POST["table"])&&($_POST["id"])) {
		if ($table == "councillordocs"){

		$delrec = $db->get_row("SELECT " . $table . ".docid, name FROM " . $table . " WHERE docid = '$id'");

			switch($table){
				case "councillordocs":
					$id = $delrec->docid;
					delete_councillordoc($id);
					$text = '<p>Document deleted: "<strong>' . $delrec->name . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
			}


		}
		else if ($table == "councillors"){

		$delrec = $db->get_row("SELECT " . $table . ".emid, name FROM " . $table . " WHERE emid = '$id'");

			switch($table){
				case "councillors":
					$id = $delrec->emid;
					delete_councillor($id);
					$text = '<p>Councillor deleted: "<strong>' . $delrec->name . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
			}



		}
		else if ($table == "occasionallicences"){

		$delrec = $db->get_row("SELECT " . $table . ".id, location FROM " . $table . " WHERE id = '$id'");

			switch($table){
				case "occasionallicences":
					$id = $delrec->id;
					delete_occlic($id);
					$text = '<p>Licence deleted: "<strong>' . $delrec->location . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
			}


		}

		else if ($table == "worknotices"){

		$delrec = $db->get_row("SELECT " . $table . ".id, refno FROM " . $table . " WHERE id = '$id'");

			switch($table){
				case "worknotices":
					$id = $delrec->id;
					delete_worknotices($id);
					$text = '<p>Work Notice deleted: "<strong>' . $delrec->refno . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
			}


		}

		else if($delrec = $db->get_row("SELECT " . $table . ".serviceid, parentserviceid, " . $title . " AS title FROM " . $table . " LEFT JOIN cms.services USING(serviceid) WHERE " . $keyfield . " = '$id'")) {
			if (($delrec->serviceid == $_SESSION["userserviceid"])||($delrec->parentserviceid == $_SESSION["userserviceid"])||($_SESSION["userlevel"] > 3)) {
				switch ($table) {
				case "content":
					delete_page($id);
					$text = '<p>Record deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "contacts":
					if ($contactpages = $db->get_results("SELECT pagename, title FROM content WHERE contactid='$id' ORDER BY title")) {
						$text = '<p>This contact cannot be deleted since it has the following content records assigned to it:</p><p><ul>';
						foreach ($contactpages as $contactpage) {
							$text .= '<li>' . $contactpage->title . '</li>';
						}
						$text .= '</ul></p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:window.close();" /></p>';
					} else {
						delete_contact($id);
						$text = '<p>Contact deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					}
					break;
				case "forms":
					if ($formpages = $db->get_results("SELECT title FROM pageXform LEFT JOIN content USING(pageid) WHERE formid=$id")) {
						$text = '<p>This form cannot be deleted since it is linked to the following ClacksWeb pages:</p><p><ul>';
						foreach ($formpages as $formpage) {
							$text .= '<li>' . $formpage->title . '</li>';
						}
						$text .= '</ul></p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:window.close();" /></p>';
					} else {
						delete_form($id);
						$text = '<p>Form deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					}
					break;
				case "docs":
					if ($docsubjects = $db->get_results("SELECT subject FROM cms.subject_doc_map LEFT JOIN cms.subjects USING(subjectid) WHERE docid=$id")) {
						$text = '<p>This document cannot be deleted since it has the following subjects associated with it on the FAQ system:</p><p><ul>';
						foreach ($docsubjects as $docsubject) {
							$text .= '<li>' . $docsubject->subject . '</li>';
						}
						$text .= '</ul></p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:window.close();" /></p>';
					} else if ($docfaqs = $db->get_results("SELECT faq FROM cms.faq_doc_map LEFT JOIN cms.faqs USING(faqid) WHERE docid=$id")) {
						$text = '<p>This document cannot be deleted since it has the following FAQs associated with it on the FAQ system:</p><p><ul>';
						foreach ($docfaqs as $docfaq) {
							$text .= '<li>' . $docfaq->faq . '</li>';
						}
						$text .= '</ul></p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:window.close();" /></p>';
					} else if ($docpages = $db->get_results("SELECT title FROM pageXdoc LEFT JOIN content USING(pageid) WHERE docid=$id")) {
						$text = '<p>This document cannot be deleted since it is linked to the following ClacksWeb pages:</p><p><ul>';
						foreach ($docpages as $docpage) {
							$text .= '<li>' . $docpage->title . '</li>';
						}
						$text .= '</ul></p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:window.close();" /></p>';
					} else {
						delete_doc($id);
						$text = '<p>Document deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					}
					break;

				case "events.events":
					delete_event($id);
					$text = '<p>Event deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "collections":
					delete_collection($id);
					$text = '<p>Collection deleted for "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "events.meetings":
					delete_meeting($id);
					$text = '<p>Meeting deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "tenders":
					delete_tender($id);
					$text = '<p>Tender deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "hr.vacancies":
					delete_vacancy($id);
					$text = '<p>Vacancy deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "hygieneinspections":
					delete_hygieneinspection($id);
					$text = '<p>Inspection deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "emergency.notices":
					delete_emergencynotice($id);
					$text = '<p>Notice deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "polls.polls":
					delete_poll($id);
					$text = '<p>Poll deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "hicategories":
					if ($catests = $db->get_results("SELECT hygieneinspections.inspectiondate, establishment FROM hygieneinspections LEFT JOIN hicategories USING(catid), hiestablishments WHERE hygieneinspections.catid=$id AND hygieneinspections.establishmentid=hiestablishments.establishmentid")) {
						$text = '<p>This category cannot be deleted since it has the following inspections associated with it:</p><p><ul>';
						foreach ($catests as $catest) {
							$text .= '<li>' . $catest->establishment . ' (' . $catest->inspectiondate . ')</li>';
						}
						$text .= '</ul></p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:window.close();" /></p>';
					} else {
						delete_hicategory($id);
						$text = '<p>Category deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					}
					break;
				case "licenses":
					delete_license($id);
					$text = '<p>License deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					log_entry($_SESSION['userid'],"Deleted licence file $delrec->title");
					break;
				case "press":
					delete_press($id);
					$text = '<p>Press release deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "tros":
					delete_tro($id);
					$text = '<p>TRO deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "busdir.details":
					delete_business($id);
					$text = '<p>Business deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "elections.elections":
					delete_election($id);
					$text = '<p>Election deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "galleries.galleries":
					if ($images = $db->get_results("SELECT imageid FROM galleries.images WHERE images.galleryid=$id")) {
						$text = '<p>This gallery cannot be deleted since it contains images. Please delete these images first.</p>';
						$text .= '<p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:window.close();" /></p>';
					} else {
						delete_gallery($id);
						$text = '<p>Gallery deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					}
					break;
				case "hr.files":
					delete_jobpackfile($id);
					$text = '<p>Job pack file deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "roadreport.info":
					delete_roadreport($id);
					$text = '<p>Road Report deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "communications.commsqs":
					delete_commsq($id);
					$text = '<p>Questionnaire deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "bodies":
					delete_body($id);
					$text = '<p>Body deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "extlinks":
					delete_extlink($id);
					$text = '<p>External link deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "rclinks":
					delete_rclink($id);
					$text = '<p>Reference Central link deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					break;
				case "rclinkcats":
					if ($links = $db->get_results("SELECT title FROM rclinks LEFT JOIN rclinkXrclinkcat USING(rclinkid) WHERE rclinkcatid=$id")) {
						$text = '<p>This category cannot be deleted since it has the following links associated with it. Please remove these from this category before deletion:</p><p><ul>';
						foreach ($links as $link) {
							$text .= '<li>' . $link->title . '</li>';
						}
						$text .= '</ul></p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:window.close();" /></p>';
					} else {
						delete_rclinkcat($id);
						$text = '<p>Category deleted: "<strong>' . $delrec->title . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';
					}
					break;
				}
			} else {
				$text = '<p>You are not authorised to delete that record.</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:window.close();" /></p>';
			}
		}

		else {
			$text = '<p>There was a problem processing the deletion.</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:window.close();" /></p>';
		}
	} else {
		$text = '<p>There was a problem processing the deletion.</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:window.close();" /></p>';
	}
} else if ($_GET['table'] == "councillors"){

		$delrec = $db->get_row("SELECT emid, name FROM councillors WHERE emid = '$id'");

					$id = $delrec->emid;
					delete_councillor($id);
					$text = '<p>Councillor deleted: "<strong>' . $delrec->name . '</strong>".</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:opener.location.reload(true);window.close();" /></p>';

		}
		else {
// Something wrong - no parameters passed or no confirmation
	$text = '<p>There was a problem processing the deletion.</p><p><input type="submit" class="btn btn-lg btn-primary"  name="cancel" value="Close" onclick="javascript:window.close();" /></p>';
}
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="/css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<h3>Delete record</h3>
<?php echo $text; ?>
</body>
</html>