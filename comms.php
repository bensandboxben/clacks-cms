<?php
/**
* Communications Unit Project Questionnaire
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
if (($_REQUEST["projectid"])&&(is_numeric($_REQUEST["projectid"]))&&($_REQUEST["confstring"])&&(ctype_alnum($_REQUEST["confstring"]))&&($q = $db->get_row("SELECT * FROM communications.commsqs WHERE projectid='$_REQUEST[projectid]' AND confstring='$_REQUEST[confstring]'"))) {
	if ($_GET["view"]) {
		exit("<p>This is where you'll see details of a submitted questionnaire.</p>");
	} else if ($q->submitted) {
		exit("<p>Sorry, that questionnaire has already been submitted.</p>");
	} else if ($_POST["submit"]) {
		$db->query("UPDATE communications.commsqs SET brochure='$_POST[brochure]', report='$_POST[report]', poster='$_POST[poster]', web='$_POST[web]', dwother='$_POST[dwother]', dwquality='$_POST[dwquality]', dwtimetaken='$_POST[dwtimetaken]', dwcompletedontime='$_POST[dwcompletedontime]', dwachievedobjectives='$_POST[dwachievedobjectives]', dwvfm='$_POST[dwvfm]', dwhelpfulness='$_POST[dwhelpfulness]', dwcomments='$_POST[dwcomments]', dwsuggestions='$_POST[dwsuggestions]', media='$_POST[media]', enquiry='$_POST[enquiry]', release='$_POST[release]', launch='$_POST[launch]', mother='$_POST[mother]', mquality='$_POST[mquality]', mtimetaken='$_POST[mtimetaken]', mobjectives='$_POST[mobjectives]', mhelpfulness='$_POST[mhelpfulness]', mcomments='$_POST[mcomments]', msuggestions='$_POST[msuggestions]', submitted=UNIX_TIMESTAMP() WHERE projectid='$_REQUEST[projectid]' AND confstring='$_REQUEST[confstring]'");
		header("Location: thankyou.html");
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-GB" lang="en-GB" dir="ltr">
<head>
<title>Comms Unit Questionnaire: <?php echo $q->projecttitle;?></title>
<?php include "includes/head.inc.php"; ?>
</head>
<body class="comms-page">
<h1>Communications Unit Project Questionnaire</h1>
<div class="waffle"><p>We recently completed the following project for you: <strong><?php echo $q->projecttitle;?></strong></p>
<p>Please complete this form to let us know how satisfied you were with the work we carried out. We will use the information you give us to continue to improve the services we provide.</p>
<p>If you have time to give us your comments and suggestion as well, they will be greatly welcomed.</p>
<p>Thank you for your help.</p></div>
<h2>Questionnaire</h2>
<form action="comms.php" method="post" name="comms">
<fieldset><legend>Design &amp; Web Support</legend>
<p>1. What type of design support did you receive? (please tick all that apply)</p>
<div class="cb"><label for="brochure">Brochure/leaflet</label> <input type="checkbox" name="brochure" id="brochure" value="1"<?php if($_POST["brochure"]) echo ' checked="checked"';?> /></div>
<div class="cb"><label for="report">Report</label> <input type="checkbox" name="report" id="report" value="1"<?php if($_POST["report"]) echo ' checked="checked"';?> /></div>
<div class="cb"><label for="poster">Poster</label> <input type="checkbox" name="poster" id="poster" value="1"<?php if($_POST["poster"]) echo ' checked="checked"';?> /></div>
<div class="cb"><label for="web">Web</label> <input type="checkbox" name="web" id="web" value="1"<?php if($_POST["web"]) echo ' checked="checked"';?> /></div>
<div class="cb"><label for="dwother">Other (please specify)</label> <input type="text" name="dwother" id="dwother" value="<?php echo (stripslashes($_POST["dwother"]));?>" /></div>
<p>2. On a scale of 1 to 4, with 1 being very dissatisfied and 4 being very satisfied, how satisfied were you with the following aspects of the design support you received?</p>
<div class="rating"><label for="dwquality">Overall quality of support</label> <select name="dwquality" id="dwquality"><option value="">--</option><?php for ($x=1; $x<5; $x++) { echo '<option value="' . $x . '"'; if ($_POST["dwquality"] == $x) echo ' selected="selected"'; echo '>' . $x . '</option>';}?></select></div>
<div class="rating"><label for="dwtimetaken">Amount of time taken to produce material/web</label> <select name="dwtimetaken" id="dwtimetaken"><option value="">--</option><?php for ($x=1; $x<5; $x++) { echo '<option value="' . $x . '"'; if ($_POST["dwtimetaken"] == $x) echo ' selected="selected"'; echo '>' . $x . '</option>';}?></select></div>
<div class="rating"><label for="dwcompletedontime">Material/web completed on time</label> <select name="dwcompletedontime" id="dwcompletedontime"><option value="">--</option><?php for ($x=1; $x<5; $x++) { echo '<option value="' . $x . '"'; if ($_POST["dwcompletedontime"] == $x) echo ' selected="selected"'; echo '>' . $x . '</option>';}?></select></div>
<div class="rating"><label for="dwachievedobjectives">Material/web achieved campaign/project objectives</label> <select name="dwachievedobjectives" id="dwachievedobjectives"><option value="">--</option><?php for ($x=1; $x<5; $x++) { echo '<option value="' . $x . '"'; if ($_POST["dwachievedobjectives"] == $x) echo ' selected="selected"'; echo '>' . $x . '</option>';}?></select></div>
<div class="rating"><label for="dwvfm">Material/web represented good value for money</label> <select name="dwvfm" id="dwvfm"><option value="">--</option><?php for ($x=1; $x<5; $x++) { echo '<option value="' . $x . '"'; if ($_POST["dwvfm"] == $x) echo ' selected="selected"'; echo '>' . $x . '</option>';}?></select></div>
<div class="rating"><label for="dwhelpfulness">Helpfulness of staff</label> <select name="dwhelpfulness" id="dwhelpfulness"><option value="">--</option><?php for ($x=1; $x<5; $x++) { echo '<option value="' . $x . '"'; if ($_POST["dwhelpfulness"] == $x) echo ' selected="selected"'; echo '>' . $x . '</option>';}?></select></div>
<div class="ta"><label for="dwcomments">Comments:</label> <textarea name="dwcomments"><?php echo htmlentities(stripslashes($_POST["dwcomments"]));?></textarea></div>
<div class="ta"><label for="dwsuggestions">3. Do you have any other suggestions which will improve the work of the design and web sections?</label> <textarea name="dwsuggestions"><?php echo htmlentities(stripslashes($_POST["dwsuggestions"]));?></textarea></div>
</fieldset>
<fieldset><legend>Media/Press</legend>
<div><label for="media">4. Did you receive any media support?</label> <select name="media" id="media"><option value="0">No (please submit your questionnaire)</option><option value="1"<?phpif ($_POST["media"] == 1) echo ' selected="selected"';?>>Yes (please continue to question 6)</option></select></div>
<p>5. What type of media/press support did you receive? (please tick all that apply)</p>
<div class="cb"><label for="enquiry">Enquiry</label> <input type="checkbox" name="enquiry" id="enquiry" value="1"<?php if($_POST["enquiry"]) echo ' checked="checked"';?> /></div>
<div class="cb"><label for="release">Release</label> <input type="checkbox" name="release" id="release" value="1"<?php if($_POST["release"]) echo ' checked="checked"';?> /></div>
<div class="cb"><label for="launch">Launch</label> <input type="checkbox" name="launch" id="launch" value="1"<?php if($_POST["launch"]) echo ' checked="checked"';?> /></div>
<div class="cb"><label for="mother">Other (please specify)</label> <input type="text" name="mother" id="mother" value="<?php echo ($_POST["mother"]);?>" /></div>
<p>6. On a scale of 1 to 4, with 1 being very dissatisfied, how satisfied were you with the following aspects of the media support you received?</p>
<div class="rating"><label for="mquality">Overall quality of the support you received</label> <select name="mquality" id="mquality"><option value="">--</option><?php for ($x=1; $x<5; $x++) { echo '<option value="' . $x . '"'; if ($_POST["mquality"] == $x) echo ' selected="selected"'; echo '>' . $x . '</option>';}?></select></div>
<div class="rating"><label for="mtimetaken">Amount of time taken to provide support</label> <select name="mtimetaken" id="mtimetaken"><option value="">--</option><?php for ($x=1; $x<5; $x++) { echo '<option value="' . $x . '"'; if ($_POST["mtimetaken"] == $x) echo ' selected="selected"'; echo '>' . $x . '</option>';}?></select></div>
<div class="rating"><label for="mobjectives">Support provided achieved you objectives</label> <select name="mobjectives" id="mobjectives"><option value="">--</option><?php for ($x=1; $x<5; $x++) { echo '<option value="' . $x . '"'; if ($_POST["mobjectives"] == $x) echo ' selected="selected"'; echo '>' . $x . '</option>';}?></select></div>
<div class="rating"><label for="mhelpfulness">Helpfulness of staff</label> <select name="mhelpfulness" id="mhelpfulness"><option value="">--</option><?php for ($x=1; $x<5; $x++) { echo '<option value="' . $x . '"'; if ($_POST["mhelpfulness"] == $x) echo ' selected="selected"'; echo '>' . $x . '</option>';}?></select></div>
<div class="ta"><label for="mcomments">Comments:</label> <textarea name="mcomments"><?php echo htmlentities(stripslashes($_POST["mcomments"]));?></textarea></div>
<div class="ta"><label for="msuggestions">7. Do you have any other suggestions which will help improve the Unit's media work?</label> <textarea name="msuggestions"><?php echo htmlentities(stripslashes($_POST["msuggestions"]));?></textarea></div>
</fieldset>
<p>Thank you for your help with the questionnaire.</p>
<input type="hidden" name="projectid" value="<?php echo $_REQUEST["projectid"];?>" />
<input type="hidden" name="confstring" value="<?php echo $_REQUEST["confstring"];?>" />
<div id="btn"><input type="submit" class="btn btn-md btn-primary" name="submit" value="Submit questionnaire" /></div>
</form>
</body>
</html>
<?php
} else {
	echo 'Error';
}
?>