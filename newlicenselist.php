<?php
/**
* ClacksWeb Civic License list
* Produces lists of provisional and awarded civic licenses from XML files
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2
* @version 0.1
*/
global $table;
// XML callback functions
function startElement($parser, $name, $attrs) {
	global $currentTag;
	global $table;
	$currentTag = $name;
	// output opening HTML tags
	switch ($name) {
	case "DATAROW":
		$table .= "<tr>";
        break;
	case "LICENCE_NO.":
		$table .= "<td>";
		break;
	case "ADDRESS":
		$table .= "<td>";
		break;
	case "TYPE":
		$table .= "<td>";
		break;
	case "CURRENT_GRANT":
		$table .= "<td>";
		break;
	case "CTYPE":
		$table .= "<td>";
		break;
	case "NAME":
		$table .= "<td>";
		break;
	default:
		break;
	}
}
function endElement($parser, $name) {
	global $currentTag;
	global $table;
	// output closing HTML tags
	switch ($name) {
	case "DATAROW":
		$table .= "</tr>";
		break;
	case "LICENCE_NO.":
		$table .= "</td>";
		break;
	case "ADDRESS":
		$table .= "</td>";
		break;
	case "TYPE":
		$table .= "</td>";
		break;
	case "CURRENT_GRANT":
		$table .= "</td>";
		break;
	case "CTYPE":
		$table .= "</td>";
		break;
	case "NAME":
		$table .= "</td>";
		break;
	default:
		break;
	}
	// clear current tag variable
	$currentTag = "";
}
// process data between tags
function characterData($parser, $data) {
	global $currentTag;
	global $table;
	// format the data
	$data = htmlentities(html_entity_decode($data));
	switch ($currentTag) {
	case "LICENCE_NO.":
		$table .= trim($data);
		break;
	case "ADDRESS":
		$table .= str_replace(chr(10),"<br />",$data);
		break;
	case "TYPE":
		$table .= str_replace(chr(10),"<br />",$data);
		break;
	case "CURRENT_GRANT":
		$table .= trim($data);
		break;
	case "CTYPE":
		$table .= str_replace(chr(10),"<br />",$data);
		break;
	case "NAME":
		$table .= trim($data);
		break;
	default:
		break;
	}
}
$bc_array["Licensing"] = '/regulation/licensing/';
$pagedata["suppresssectionmenu"] = 1;
$pagedata["content"] = '';
/* Licensing lists content */
// data file
$file = ROOT_DIR . "xml/cm.xml";

// initialize parser
$xml_parser = xml_parser_create();
// set callback functions
xml_set_element_handler($xml_parser, "startElement", "endElement");
xml_set_character_data_handler($xml_parser, "characterData");
// open XML file
if (!($fp = fopen($file, "r"))) {
    die("Cannot locate XML data file: $file");
} else {
	$currentTag = "";
	// read and parse data
	while ($data = fread($fp, 4096)) {
	    // error handler
		if (!xml_parse($xml_parser, $data, feof($fp))) {
		    die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));
	    }
	}
	// clean up
	xml_parser_free($xml_parser);
}
$pagedata["content"] .= '<p>List updated on ' . date ("jS F Y \a\\t g:ia.", filemtime($file)) . '</p>';
$pagedata["modified"] = filemtime($file);
$pagedata["content"] .= '<table class="outlined"><thead><tr><th>License Number</th><th>Address</th><th>Licence Type</th><th>Date Granted</th><th>Action</th><th>Area</th></tr></thead><tbody>' . $table . '</tbody></table>';
?>
