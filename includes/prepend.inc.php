<?php
session_start();

if ((!isset($_SESSION["username"]))&&($_SERVER["PHP_SELF"] != "/cms/login.php")&&($_SERVER["PHP_SELF"] != "/cms/comms.php")) {
	header("Location: login.php");
}
unset($_SESSION["pagedata"]);
/**
* Include libraries and classes
*/
//include_once "includes/errors.inc.php";
date_default_timezone_set("Europe/London");
error_reporting(E_ALL);
//set_error_handler("eh");
include_once "/home/site/wwwroot/cms/includes/constants.inc.php";
include_once "functions.inc.php";
include_once "secure_functions.inc.php";
include_once "../ezdb.inc.php";
include_once "includes/class.phpmailer.php";
include_once "includes/validateEmailFormat.php";
include_once "includes/sanitize.inc.php";
/**
* Provide default content-type, charset and language information
* Manual pages will override this, and maybe others too
*/
ob_start();
header("Content-language: en");
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?>