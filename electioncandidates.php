<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include_once "includes/prepend.inc.php";

header("Cache-Control: private");

$electionid = $_REQUEST["electionid"];

$election = $db->get_row("
    SELECT electiondate, body
    FROM elections.elections
    LEFT JOIN elections.bodies USING(bodyid)
    WHERE electionid='$electionid'
");

// Should we output a table of candidates?
$print_table = 0;

function show_candidate_form($values = array()) {

    global $db;

    echo <<<HTML

<script language="JavaScript">
function doAddParty(label, value) {

    var s = document.candidatefrm.elements["partyid"];
    var l = s.length;

    s.options[s.length] = new Option(label, value);

    s.length = l + 1;
    s.selectedIndex = l;
}
</script>

<script language="JavaScript">
function doAddConstituency(label, value) {

    var s = document.candidatefrm.elements["conid"];
    var l = s.length;

    s.options[s.length] = new Option(label, value);

    s.length = l + 1;
    s.selectedIndex = l;
}
</script>

HTML;

    echo '<form action="electioncandidates.php" method="POST" name="candidatefrm">';

    echo '<table class="table table-striped table-bordered">';

    echo '<tr>';
    echo '<td class="label">Constituency:</td>';
    echo '<td>';

    $constituencies = $db->get_results(
        "SELECT conid, constituency FROM elections.constituencies ORDER BY constituency",
        ARRAY_N
    );

    build_select_box($constituencies, "conid", $values["conid"]);

    echo '<a href="#" onClick="window.open(\'newconstituency.php\', \'con_new\', \'width=500,height=250,resizable=yes,scrollbars=yes,top=50,left=50\')"><img src="images/add.gif" alt="Add new constituency" border="0" /></a>';

    echo '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td class="label">Party:</td>';
    echo '<td>';

    $parties = $db->get_results(
        "SELECT partyid, party FROM elections.parties ORDER BY party",
        ARRAY_N
    );

    build_select_box($parties, "partyid", $values["partyid"]);

    echo '<a href="#" onClick="window.open(\'newparty.php\', \'party_new\', \'width=500,height=250,resizable=yes,scrollbars=yes,top=50,left=50\')"><img src="images/add.gif" alt="Add new party" border="0" /></a>';

    echo '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td class="label">Forename:</td>';
    echo '<td><input type="text" id="forename" name="forename" value="' . $values["forename"] . '" /></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td class="label">Surname:</td>';
    echo '<td><input type="text" id="surname" name="surname" value="' . $values["surname"] . '" /></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td class="label">Votes:</td>';
    echo '<td><input type="text" id="votes" name="votes" value="' . $values["votes"] . '" /></td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td class="label">Elected:</td>';
    echo '<td>';

    build_yes_no_checkbox("elected", $values["elected"]);

    echo '</td>';
    echo '</tr>';

    echo '</table>';

    echo '<p class="formbtns">';
    echo '<input type="submit" class="btn btn-md btn-primary" name="submit" value="Save"> ';
    echo '<input type="button" class="btn btn-md btn-primary" name="cancel" value="Cancel" onClick="history.go(-1);">';
    echo '</p>';

    echo '<input type="hidden" name="electionid" value="' . $values["electionid"] . '" />';

    if ($_REQUEST["action"] == 'edit') {
        echo '<input type="hidden" name="resultid" value="' . $values["resultid"] . '" />';
        echo '<input type="hidden" name="oldconid" value="' . $values["oldconid"] . '" />';
        echo '<input type="hidden" name="oldpartyid" value="' . $values["oldpartyid"] . '" />';
    }

    echo '</form>';
}

?>

<!DOCTYPE html>

<html>

<head>

<title>ClacksWeb CMS</title>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">

</head>

<body bgcolor="#FFFFFF" class="popup">

<div id="formarea">

<h3>Election candidates</h3>

<?php

switch ($_REQUEST["action"]) {

    case "add":

        echo "<p>Add Candidate to Election: <strong>$election->body</strong></p>";

        break;

    case "edit":

        echo "<p>Edit Candidate for Election: <strong>$election->body</strong></p>";

        break;

    default:

        echo '<p>Manage Candidates for Election: <strong>' . $election->body . '</strong> <a href="electioncandidates.php?action=add&electionid=' . $electionid . '"><img src="images/add.gif" alt="Add new candidate" border="0" /></a></p>';

        break;
}

if (isset($_POST["submit"])) {

    $print_table = 1;

    $elected = isset($_POST["elected"]) ? 1 : 0;

    $votes = isset($_POST["votes"]) ? trim($_POST["votes"]) : '';

    if ($votes === '') {
        $votes = 0;
    } else {
        $votes = (int)$votes;
    }

    $conid = (int)$_POST["conid"];
    $partyid = (int)$_POST["partyid"];
    $resultid = (int)$_POST["resultid"];
    $electionid = (int)$_POST["electionid"];

    $forename = addslashes($_POST["forename"]);
    $surname = addslashes($_POST["surname"]);

    if (
        !empty($_POST["electionid"]) &&
        !empty($_POST["oldconid"]) &&
        !empty($_POST["oldpartyid"])
    ) {

        $db->query("
            UPDATE elections.results
            SET
                conid='$conid',
                partyid='$partyid',
                forename='$forename',
                surname='$surname',
                votes='$votes',
                elected='$elected'
            WHERE resultid='$resultid'
        ");

    } else {

        $db->query("
            INSERT INTO elections.results
            (
                electionid,
                conid,
                partyid,
                forename,
                surname,
                votes,
                elected
            )
            VALUES
            (
                '$electionid',
                '$conid',
                '$partyid',
                '$forename',
                '$surname',
                '$votes',
                '$elected'
            )
        ");
    }

} else if ($_GET["action"]) {

    switch ($_GET["action"]) {

        case "add":

            $values = array();

            $values["electionid"] = $electionid;
            $values["conid"] = '';
            $values["partyid"] = '';
            $values["forename"] = '';
            $values["surname"] = '';
            $values["votes"] = '';
            $values["elected"] = 0;

            show_candidate_form($values);

            break;

        case "edit":

            $candidate = $db->get_row("
                SELECT *
                FROM elections.results
                WHERE resultid='$_GET[resultid]'
            ");

            $values = array();

            $values["electionid"] = $electionid;
            $values["resultid"] = $_GET["resultid"];
            $values["conid"] = $_GET["conid"];
            $values["partyid"] = $_GET["partyid"];
            $values["oldconid"] = $_GET["conid"];
            $values["oldpartyid"] = $_GET["partyid"];
            $values["forename"] = $candidate->forename;
            $values["surname"] = $candidate->surname;
            $values["votes"] = $candidate->votes;
            $values["elected"] = $candidate->elected;

            show_candidate_form($values);

            break;

        case "delete":

            $db->query("
                DELETE FROM elections.results
                WHERE resultid='$_GET[resultid]'
            ");

            $print_table = 1;

            break;

        default:

            echo "Unknown action";

            break;
    }

} else {

    $print_table = 1;
}

if (
    ($candidates = $db->get_results("
        SELECT *
        FROM elections.constituencies
        LEFT JOIN elections.results USING(conid)
        LEFT JOIN elections.parties USING(partyid)
        WHERE electionid='$electionid'
        ORDER BY constituency, surname, party
    "))
    &&
    ($print_table == 1)
) {

    echo '<table width="100%">';

    echo '<thead>';
    echo '<tr>';
    echo '<th>Name</th>';
    echo '<th>Constituency</th>';
    echo '<th>Party</th>';
    echo '</tr>';
    echo '</thead>';

    echo '<tbody>';

    foreach ($candidates as $candidate) {

        echo '<tr>';

        echo '<td class="detail">' . $candidate->forename . ' ' . $candidate->surname . '</td>';

        echo '<td class="detail">' . $candidate->constituency . '</td>';

        echo '<td class="detail">' . $candidate->party . '</td>';

        echo '<td width="100" align="right">';

        echo '<a href="electioncandidates.php?action=edit&electionid=' . $candidate->electionid . '&conid=' . $candidate->conid . '&resultid=' . $candidate->resultid . '&partyid=' . $candidate->partyid . '"><img src="images/edit.gif" alt="Edit Candidate" border="0"></a> ';

        echo '<a href="electioncandidates.php?action=delete&electionid=' . $candidate->electionid . '&conid=' . $candidate->conid . '&resultid=' . $candidate->resultid . '&partyid=' . $candidate->partyid . '"><img src="images/delete.gif" alt="Delete Candidate" border="0"></a>';

        echo '</td>';

        echo '</tr>';
    }

    echo '</tbody>';

    echo '</table>';
}

if ($print_table == 1) {

    echo '<p class="formbtns">';
    echo '<input type="button" class="btn btn-md btn-primary" value="Close" onclick="self.close();">';
    echo '</p>';
}

?>

</div>

</body>

</html>
