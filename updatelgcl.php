<?php
/**
* Import LGCL for ClacksWeb CMS
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Import LGCL";

$open_tags = array(
    'ITEM' => '<ITEM>',
    'USEITEM' => '<USEITEM>',
    'NAME' => '<NAME>',
    'RELATEDITEM' => '<RELATEDITEM>',
	'BROADERITEM' => '<BROADERITEM>');

$close_tags = array(
    'ITEM' => '</ITEM>',
    'USEITEM' => '</USEITEM>',
    'NAME' => '</NAME>',
    'RELATEDITEM' => '</RELATEDITEM>',
	'BROADERITEM' => '</BROADERITEM>');

// handles the attributes for opening tags
// $attrs is a multidimensional array keyed by attribute
// name and having the value of that attribute
function startElement($parser, $name, $attrs=''){
    global $open_tags, $temp, $current_tag, $lgcl_array, $termid, $broaderterms, $relatedterms;
    $current_tag = $name;
    if ($format = $open_tags[$name]){
    switch($name){
        case 'ITEM':
		$lgcl_array[$termid]["id"] = $attrs["ID"];
		if ($attrs["CATEGORY"] == "true") {
			$lgcl_array[$termid]["category"] = 1;
		} else {
			$lgcl_array[$termid]["category"] = 0;
		}
		if ($attrs["PREFERRED"] == "true") {
			$lgcl_array[$termid]["preferred"] = 1;
		} else {
			$lgcl_array[$termid]["preferred"] = 0;
		}
        echo '<p>New Item: ' . $attrs["ID"] . ', ' . $attrs["PREFERRED"] . ', ' . $attrs["CATEGORY"] . ': ';
        break;
        case 'BROADERITEM':
		if ($attrs["ID"]) {
			$broaderterms[] = $lgcl_array[$termid]["id"] . "|" . $attrs["ID"];
		}
		break;
        case 'RELATEDITEM':
		if ($attrs["ID"]) {
			$relatedterms[] = $lgcl_array[$termid]["id"] . "|" . $attrs["ID"];
		}
		break;
        case 'USEITEM':
		if ($attrs["ID"]) {
			$lgcl_array[$termid]["useitem"] = $attrs["ID"];
		} else {
			$lgcl_array[$termid]["useitem"] = 0;
		}
        break;
        default:
        break;
    }
    }
}

// $current_tag lets us know what tag we are currently
// dealing with - we use that later in the characterData
// function.
function endElement($parser, $name, $attrs=''){
    global $close_tags, $temp, $current_tag, $termid;
    if ($format = $close_tags[$name]){
    switch($name){
        case 'ITEM':
		echo '</p>';
		$termid++;
        $temp = '';
        break;
        default:
        break;
    }
    }
}
// this function is passed data between elements
// theu $data would equal 'Title Here'
// in the line <TITLE>Title Here</TITLE>
function characterData($parser, $data){
    global $current_tag, $temp, $catID, $termid, $lgcl_array;
    switch($current_tag){
    case 'NAME':
		$lgcl_array[$termid]["name"] = addslashes($data);
        echo $data;
        $current_tag = '';
        break;
    default:
        $current_tag = '';
        break;
    }
}

include "includes/header.inc.php";

// what are we parsing?
$xml_file = 'lgcl.xml';

// declare the character set - UTF-8 is the default
$type = 'UTF-8';

// create our parser
$xml_parser = xml_parser_create($type);

// set some parser options
xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);
xml_parser_set_option($xml_parser, XML_OPTION_TARGET_ENCODING, 'UTF-8');

// this tells PHP what functions to call when it finds an element
// these funcitons also handle the element's attributes
xml_set_element_handler($xml_parser, 'startElement','endElement');

// this tells PHP what function to use on the character data
xml_set_character_data_handler($xml_parser, 'characterData');

if (!($fp = fopen($xml_file, 'r'))) {
    die("Could not open $xml_file for parsing!\n");
}

// loop through the file and parse baby!
$lgcl_array = array();
$broaderterms = array();
$relatedterms = array();
$termid = 0;
while ($data = fread($fp, 4096)) {
    if (!($data = utf8_encode($data))) {
        echo 'ERROR'."\n";
    }
    if (!xml_parse($xml_parser, $data, feof($fp))) {
        die(sprintf( "XML error: %s at line %d\n\n",
        xml_error_string(xml_get_error_code($xml_parser)),
        xml_get_current_line_number($xml_parser)));
    }
}

xml_parser_free($xml_parser);
$db->query("DELETE FROM lgcl.terms");
foreach ($lgcl_array as $lgcl) {
	$db->query("INSERT INTO lgcl.terms (termid, term, preferred, usetermid, category) VALUES ('$lgcl[id]', '$lgcl[name]', '$lgcl[preferred]', '$lgcl[useitem]', '$lgcl[category]')");
}
$db->query("DELETE FROM lgcl.broaderterms");
foreach ($broaderterms as $broaderterm) {
	$values = explode("|", $broaderterm);
	$db->query("INSERT INTO lgcl.broaderterms (termid, broadertermid) VALUES ('$values[0]', '$values[1]')");
}
$db->query("DELETE FROM lgcl.relatedterms");
foreach ($relatedterms as $relatedterm) {
	$values = explode("|", $relatedterm);
	$db->query("INSERT INTO lgcl.relatedterms (termid, relatedtermid) VALUES ('$values[0]', '$values[1]')");
}

include "includes/footer.inc.php"; ?>
