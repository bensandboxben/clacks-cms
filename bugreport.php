<?php
/**
* Report a bug
* @author Daniel Champion <dchampion@clacks.gov.uk>
* @package ClacksWeb v2 CMS
* @version 0.1
*/
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
/**
* Variables for this CMS area
*/
$CMStitle = "Report a bug";
include "includes/header.inc.php";
?>
<div>
<h3>Report a bug</h3>
<form action="bugreport.php" method="post">
<fieldset style="width: 60%"><legend><label for="problem">What's the problem?</legend>
<textarea name="problem" id="problem" style="margin-left: 20px; width: 50%; height:150px;">
<?php echo $_POST["problem"]; ?>
</textarea>
</fieldset>
</form>
</div>
<?php
include "includes/footer.inc.php";
?>