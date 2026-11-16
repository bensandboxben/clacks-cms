<?php

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include_once "includes/prepend.inc.php";

header("Cache-Control: private");

$edit = 0;

if (isset($_GET["action"]) && $_GET["action"] == "edit") {

    $edit = 1;

    $id = (int)$_GET["id"];

    $election_array = $db->get_row(
        "SELECT * FROM elections.elections WHERE electionid='$id'",
        ARRAY_A
    );

    extract($election_array);
}

?>
<!DOCTYPE html>
<html>
<head>
<title>ClacksWeb CMS</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<script>
$(function() {
    $("#datepicker").datepicker({
        dateFormat: "yy-mm-dd"
    });
});
</script>

</head>

<body bgcolor="#FFFFFF" class="popup">

<?php if ($edit == 1) { ?>
<h3>Edit election</h3>
<?php } else { ?>
<h3>Add new election</h3>
<?php } ?>

<?php

if (isset($_POST["save"])) {

    $_POST["electiondate"] = date("Y-m-d", strtotime($_POST["electiondate"]));

    if (
        ($_POST["electiondate"] == '0000-00-00') ||
        ($_POST["electiondate"] == '1970-01-01') ||
        ($_POST["electiondate"] == '') ||
        (!isset($_POST["electiondate"]))
    ) {
        $_POST["electiondate"] = "0000-00-00";
    }

    $errors = array();

    if (!$_POST["electiondate"] || $_POST["electiondate"] == "0000-00-00") {
        $errors[] = "Please enter the date of this election";
    }

    if (count($errors) == 0) {

        $bodyid = (int)$_POST["bodyid"];

        $electiondate = mysqli_real_escape_string($db->dbh, $_POST["electiondate"]);
        $notes = mysqli_real_escape_string($db->dbh, $_POST["notes"]);
        $electorate = mysqli_real_escape_string($db->dbh, $_POST["electorate"]);
        $turnout = mysqli_real_escape_string($db->dbh, $_POST["turnout"]);
        $rejected = mysqli_real_escape_string($db->dbh, $_POST["rejected"]);

        $showgraphs = isset($_POST['showgraphs']) ? 1 : 0;
        $showelected = isset($_POST['showelected']) ? 1 : 0;

        if (!empty($_POST["electionid"])) {

            $electionid = (int)$_POST["electionid"];

            $db->query("
                UPDATE elections.elections
                SET
                    bodyid='$bodyid',
                    electiondate='$electiondate',
                    notes='$notes',
                    electorate='$electorate',
                    turnout='$turnout',
                    rejected='$rejected',
                    showgraphs='$showgraphs',
                    showelected='$showelected',
                    modified=UNIX_TIMESTAMP()
                WHERE electionid='$electionid'
            ");

        } else {

            $randomElectionId = rand(100, 9999);

            $db->query("
                INSERT INTO elections.elections
                (
                    electionid,
                    bodyid,
                    electiondate,
                    notes,
                    electorate,
                    turnout,
                    rejected,
                    showgraphs,
                    showelected,
                    created,
                    modified
                )
                VALUES
                (
                    '$randomElectionId',
                    '$bodyid',
                    '$electiondate',
                    '$notes',
                    '$electorate',
                    '$turnout',
                    '$rejected',
                    '$showgraphs',
                    '$showelected',
                    UNIX_TIMESTAMP(),
                    UNIX_TIMESTAMP()
                )
            ");
        }

?>
<script>
opener.location.reload(true);
self.close();
</script>
<?php

    } else {

        echo '<p>The election was not saved. Please correct these errors and resubmit the form:</p><ul>';

        foreach ($errors as $error) {
            echo '<li>' . $error . '</li>';
        }

        echo '</ul>';
    }
}

extract($_POST);

?>

<link rel="stylesheet" type="text/css" media="all" href="calendar-win2k-cold-1.css" title="win2k-cold-1" />

<script type="text/javascript" src="js/calendar.js"></script>
<script type="text/javascript" src="js/calendar-en.js"></script>
<script type="text/javascript" src="js/calendar-setup.js"></script>

<div id="formarea">

<script>
function doAddBody(label, value) {

    var s = document.election.elements["bodyid"];
    var l = s.length;

    s.options[s.length] = new Option(label, value);

    s.length = l + 1;
    s.selectedIndex = l;
}
</script>

<form name="election" method="post" action="newelection.php">

<table class="table table-striped table-bordered">

<tr>
<td class="label">Type:</td>

<td>

<?php

$bodies = $db->get_results(
    "SELECT bodyid, body FROM elections.bodies ORDER BY body",
    ARRAY_N
);

build_select_box($bodies, "bodyid", $bodyid);

?>

<a href="#"
onClick="window.open('newelectionbody.php', 'electionbody_new', 'width=500,height=300,resizable=yes,scrollbars=yes,top=50,left=50')">
<img src="images/add.gif" alt="Add new type" border="0" />
</a>

</td>
</tr>

<tr>
<td class="label">Date:</td>

<td>
<input
    id="datepicker"
    type="text"
    name="electiondate"
    value="<?php echo isset($electiondate) ? $electiondate : ''; ?>"
    maxlength="10"
    style="width: 100px;"
/>
</td>
</tr>

<tr>
<td class="label">Notes:</td>

<td>
<textarea name="notes" style="width: 300px; height: 80px;"><?php echo isset($notes) ? $notes : ''; ?></textarea>
</td>
</tr>

<tr>
<td class="label">Electorate:</td>

<td>
<input
    type="text"
    name="electorate"
    value="<?php echo isset($electorate) ? $electorate : ''; ?>"
    style="width: 50px;"
/>
</td>
</tr>

<tr>
<td class="label">Turnout:</td>

<td>
<input
    type="text"
    name="turnout"
    value="<?php echo isset($turnout) ? $turnout : ''; ?>"
    style="width: 50px;"
/>
</td>
</tr>

<tr>
<td class="label">Rejected ballots:</td>

<td>
<input
    type="text"
    name="rejected"
    value="<?php echo isset($rejected) ? $rejected : ''; ?>"
    style="width: 50px;"
/>
</td>
</tr>

<!--
<tr>
<td class="label">Show graphs:</td>
<td><?php build_yes_no_checkbox("showgraphs", $showgraphs); ?></td>
</tr>
-->

<tr>
<td class="label">Show elected:</td>

<td>
<?php build_yes_no_checkbox("showelected", $showelected ?? 0); ?>
</td>
</tr>

<tr>
<td colspan="2" class="formbtns">

<input
    type="submit"
    class="btn btn-md btn-primary"
    name="save"
    value="Save"
/>

<input
    type="button"
    class="btn btn-md btn-primary"
    value="Cancel"
    onclick="window.close();"
/>

<?php if (!empty($electionid)) { ?>
<input type="hidden" name="electionid" value="<?php echo $electionid; ?>" />
<?php } ?>

</td>
</tr>

</table>

</form>

</div>

<script>
document.election.bodyid.focus();
</script>

</body>
</html>
