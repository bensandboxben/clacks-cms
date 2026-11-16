<?php
/**
* Constants for ClacksWeb
* @package ClacksWeb v2 CMS
*/
// MySQL parameters for public access
define("EZSQL_DB_USER", "cwadmin");			// <-- mysql db user
/**
* @access private
*/
define("EZSQL_DB_PASSWORD", "cwadmin");		// <-- mysql db password
define("EZSQL_DB_NAME", "clacksweb2");		// <-- mysql db pname
define("EZSQL_DB_HOST", "localhost");	// <-- mysql server host
// A to Z elements array
$atozelements = array("a"=>"All", "c"=>"Content", "d"=>"Documents", "s"=>"Subjects", "f"=>"Forms" ,"t"=>"Themes");
// Site host
define("SITE_HOST", "https://clacks-web2023-localserver.azurewebsites.net");
define("LIVE_HOST", 0);
define("GIS_IP", "128.127.95.5");
// Mail host
define("MAIL_HOST", "itssmail02");
// Location for downloads & uploads
define("UPLOAD_DIR", "/var/www/html/cwv2");	// <-- root for file uploads
// CMS areas
$areas = array("content" => "ClacksWeb Content", "councillordetails" => "Add/Change Councillor Details", "councillordocs" => "Add Councillors' Documents", "calendar" => "Browsable Calendar for Council Meetings", "intranetcontent"=>"ClacksWeb Intranet Content", "contacts"=>"ClacksWeb Contacts", "forms"=>"Forms", "docs"=>"Documents &amp; Publications", "events"=>"Events Diary", "members"=>"Elected Members", "tenders"=>"Tenders", "press"=>"Press Releases", "schools"=>"Schools", "extlinks"=>"External Links", "pows"=>"Places of Worship", "slgcl"=>"Scottish LGCL", "meetings"=>"Meetings, Agendas &amp; Minutes", "menus"=>"Contextual menus", "licenses"=>"Licenses", "bodies"=>"Bodies", "elections"=>"Elections", "rclinks"=>"Reference Central", "rclinkcats"=>"Reference Central Categories", "consultations"=>"Consultations and Surveys", "occasionallicences"=>"Occasional Licences", "roadreport"=>"Road Report", "hygieneinspections" => "Food Hygiene Inspections", "hicategories" => "Hygiene Inspection Categories", "hiestablishments" => "Hygiene Inspection Estalishments", "emergencynotices" => "Emergency Notices", "busdir" => "Business Directory", "tros" => "Traffic Regulation Orders", "schoolsession" => "School Session", "mainfeature" => "Main Feature", "fpalert" => "Frontpage Alert", "collections" => "Charitable Collections", "vacancies" => "Vacancies", "jobpacks" => "Jobpack files", "polls" => "Polls", "galleries" => "Galleries", "commsqs" => "Communications Unit Questionnaires", "fp" => "ClacksWeb Front Page", "worknotices" => "Work Notices");
asort($areas);
$popups = array("schoolsession" => "300,150", "fpalert" => "800,600", "fp" => "800,600");
// School constants
$sectors = array("PRI"=>"Primary", "SEC"=>"Secondary", "SUP"=>"Support Services", "SEN"=>"Special", "NUR"=>"Nursery");
// Constants for dynamic scripts which have qualified links to docs and forms - these are pageids from clacksweb2.content
define("SCHOOL_SCRIPT_ID", 91);
define("LICENSE_SCRIPT_ID", 1585);
?>