<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
* Login to ClacksWeb CMS
* @author Ben Short <bshort@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Login";
if ($_GET["logout"]) {
	//log_entry($_SESSION["userid"], "Logout - $_SESSION[username]");
	$_SESSION=array();
	session_destroy();
} else if ($_POST["login"]) {
	$pw = md5($_POST["password"]);

    $qstring = "SELECT * FROM cms.users WHERE username='$_POST[loginuser]' AND password='$pw'";
    $result = $db->get_results($qstring);
   

	if (!empty($result)) {
		//setcookie ("username", $result[0]->username,time()+31536000);
		//header("Cache-Control: private");
		// set user profile vars
		$_SESSION['username'] = $result[0]->username;
		$_SESSION['userid'] = $result[0]->userid;
		$_SESSION['userlevel'] = $result[0]->userlevel;
		$_SESSION['userserviceid'] = $result[0]->serviceid;	
		header("Location: index.php");
	} else if ($_POST["loginuser"]) {
		$loginerror = "Login failed";
		//log_entry("0", "Failed login - $_POST[loginuser]");
	}
}
include "includes/header.inc.php";

?>
<div style="width:500px;margin:auto;margin-bottom:70px;margin-top:20px;">
	<div style="width:270px;margin:auto;background-color:#eee;border:1px solid #dbdbdb;padding:0px;">

	<!--p style="padding:10px;">Please note that if you are experiencing problems with the CMS text editor in FireFox then you can use Internet Explorer in the interim.</p-->
		<form name="loginfrm" id="loginfrm" action="login.php" method="post">
		<fieldset style="border:0px;">
		<h4 style="font-size:20px;"><strong>Login Details</strong></h4><br />
		<label for="loginuser"><input type="text" name="loginuser" /></label>
		<label for="password"><input style="margin-bottom:20px;" type="password" name="password" /></label>
		<br />

		<div style="text-align:center;">
			<input  class="btn btn-lg btn-primary" style="margin:0px; padding:0px; width:13em;" type="submit" name="login" value="Login" />
		</div>

		</fieldset>
		</form>
	</div>
</div>
<script>
document.loginfrm.loginuser.focus();
</script>
<?php include "includes/footer.inc.php"; ?>
