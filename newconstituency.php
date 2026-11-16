<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include_once "includes/prepend.inc.php";

header("Cache-Control: private");

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

<h3>Add new constituency</h3>

<?php

if (isset($_POST["save"])) {

    $errors = array();

    $constituency = isset($_POST["constituency"])
        ? trim($_POST["constituency"])
        : '';

    $url = isset($_POST["url"])
        ? trim($_POST["url"])
        : '';

    if ($constituency == '') {
        $errors[] = "Please enter a constituency";
    }

    if (count($errors) == 0) {

        $constituency = addslashes($constituency);
        $url = addslashes($url);

        // Generate small random conid
        $conid = rand(1, 32000);

        $db->query("
            INSERT INTO elections.constituencies
            (
                conid,
                constituency,
                url
            )
            VALUES
            (
                '$conid',
                '$constituency',
                '$url'
            )
        ");

        $newid = $conid;

?>

<script language="JavaScript">

self.opener.doAddConstituency(
    '<?php echo $constituency; ?>',
    '<?php echo $newid; ?>'
);

self.close();

</script>

<?php

    } else {

        echo '<p>The constituency was not saved. Please correct these errors and resubmit the form:</p>';

        echo '<ul>';

        foreach ($errors as $error) {
            echo '<li>' . $error . '</li>';
        }

        echo '</ul>';
    }
}

?>

<div id="formarea">

<form name="constituency" method="post" action="newconstituency.php">

<table class="table table-striped table-bordered">

<tr>

<td class="label">Constituency:</td>

<td>
<input
    type="text"
    name="constituency"
    value="<?php echo isset($_POST["constituency"]) ? htmlspecialchars($_POST["constituency"]) : ''; ?>"
    maxlength="200"
    style="width: 250px;"
/>
</td>

</tr>

<tr>

<td class="label">URL:</td>

<td>
<input
    type="text"
    name="url"
    value="<?php echo isset($_POST["url"]) ? htmlspecialchars($_POST["url"]) : ''; ?>"
    maxlength="200"
    style="width: 250px;"
/>
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
    name="cancel"
    value="Cancel"
    onclick="self.close();"
/>

</td>

</tr>

</table>

</form>

</div>

<script>
document.constituency.constituency.focus();
</script>

</body>

</html>
