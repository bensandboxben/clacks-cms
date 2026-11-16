<?php
/**
* Error handling functions for ClacksWeb
* @package ClacksWeb v2 CMS
*/
/**
* Core error handler
* @param string $errortype
* @param string $errormsg
* @param string $filename
* @param string $errorline
* @return bool
*/
function eh($errortype, $errormsg, $filename, $errorline) {
	if (($errortype == 8)||($errortype == 2)) return;
	ob_end_clean();
	output_doctype();
	echo '<head><link rel="stylesheet" type="text/css" media="screen, print" href="/css/screen.css" /><title>Error</title></head>';
	echo '<body><h1>Error</h1><p>We are sorry but this page could not be displayed due to an internal error. The error has been recorded and will be recitified as soon as possible Our apologies for any inconvenience caused. </p><p><a href="/index.php">Please return to the front page.</a></p>';
	echo '<ul>';
	echo '<li>Type: ' . $errortype . '</li>';
	echo '<li>Message: ' . $errormsg . '</li>';
	echo '<li>File: ' . $filename . '</li>';
	echo '<li>Line: ' . $errorline . '</li>';
	echo '</ul>';
	echo '</body></html>';
	print_r($_SESSION);
	exit();
}
?>