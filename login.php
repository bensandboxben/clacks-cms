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
<div class="w-full max-w-sm">
	<div class="mb-6 text-center">
		<h1 class="text-2xl font-semibold tracking-tight">Sign in</h1>
		<p class="mt-1.5 text-sm text-fg-muted">Use your ClacksWeb CMS account to continue.</p>
	</div>
	<div class="card p-6 sm:p-7">
	<!--p style="padding:10px;">Please note that if you are experiencing problems with the CMS text editor in FireFox then you can use Internet Explorer in the interim.</p-->
<?php if (!empty($loginerror)) echo cms_alert('danger', '<p><strong>' . $loginerror . '.</strong> Check your username and password and try again.</p>'); ?>
		<form name="loginfrm" id="loginfrm" action="login.php" method="post">
		<fieldset aria-label="Login Details">
		<div class="field">
			<label class="field-label" for="loginuser">Username</label>
			<input type="text" name="loginuser" id="loginuser" class="w-full" autocomplete="username" autocapitalize="none" spellcheck="false" />
		</div>
		<div class="field">
			<label class="field-label" for="password">Password</label>
			<input type="password" name="password" id="password" class="w-full" autocomplete="current-password" />
		</div>
		<input class="btn btn-lg btn-primary mt-2 w-full" type="submit" name="login" value="Login" />
		</fieldset>
		</form>
	</div>
	<p class="mt-6 text-center text-xs text-fg-subtle">Clackmannanshire Council &middot; Authorised users only</p>
</div>
<script>
document.loginfrm.loginuser.focus();
</script>
<?php include "includes/footer.inc.php"; ?>
