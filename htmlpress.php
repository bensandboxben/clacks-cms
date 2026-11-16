<?php
include_once "includes/prepend.inc.php";
$press = $db->get_results("SELECT prid, body FROM press");
foreach ($press as $htmlpress) {
	$body = nl2br($htmlpress->body);
	$body = '<p>' . $body;
	$body = ereg_replace("\r", "", $body);
	$body = ereg_replace("\n", "", $body);
	$body = ereg_replace("<br /><br />", "</p><p>", $body);
	$body = ereg_replace("<br />", "", $body);
	$config = array('clean' => TRUE, 'drop-font-tags' => TRUE, 'wrap' => 0, 'show-body-only' => 1, 'output-xhtml' => TRUE);
	$body = tidy_repair_string($body, $config);
	$body = addslashes($body);
	$db->query("UPDATE press SET htmlbody='$body' WHERE prid='$htmlpress->prid'");
	echo $htmlpress->prid . '<br />';
}
?>
