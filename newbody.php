<?php

include_once "includes/prepend.inc.php";
include_once("devedit/class.devedit.php");

// Create a new DevEdit class object
$myDE = new devedit;
$myDE->SetName("myDevEditControl");

header("Cache-Control: private");

$edit = 0;
$errors = [];

// Safe GET values
$action = $_GET['action'] ?? '';
$bodyid = $_GET['bodyid'] ?? '';

$body = '';
$typeid = '';
$active = '';
$url = '';
$notes = '';

if ($action === "edit" && $bodyid) {

    $bodyid = intval($bodyid);

    $body_array = $db->get_row(
        "SELECT * FROM bodies WHERE bodyid='$bodyid'",
        ARRAY_A
    );

    if ($body_array) {
        extract($body_array);
        $edit = 1;
    } else {
        echo "<p style='color:red;'>Error: Body record not found.</p>";
    }
}

?>

<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>

<body bgcolor="#FFFFFF" class="popup">

<?php if ($edit == 1) { ?>
<h3>Edit body</h3>
<?php } else { ?>
<h3>Add new body</h3>
<?php } ?>

<?php

if (isset($_POST["save"])) {

    $body   = $_POST['body'] ?? '';
    $typeid = $_POST['typeid'] ?? '';
    $active = !empty($_POST['active']) ? 1 : 0;
    $url    = $_POST['url'] ?? '';
    $notes  = $_POST['notes'] ?? '';
    $bodyid = $_POST['bodyid'] ?? '';

    // Basic validation
    if (!$body) {
        $errors[] = "Please enter a name for this body";
    }

    if (count($errors) == 0) {

        $bodyurl = strtolower(preg_replace("/[^[:alnum:]]/", "", $body));

        // Basic legacy escaping
        $body   = addslashes($body);
	$typeid = intval($typeid);
	$active = intval($active);
        $url    = addslashes($url);
        $notes  = addslashes($notes);

        if ($bodyid) {

            $bodyid = intval($bodyid);

	$result = $db->query("UPDATE bodies SET
	bodyurl='$bodyurl',
	body='$body',
	typeid=$typeid,
	active=$active,
	url='$url',
	notes='$notes',
	modified=UNIX_TIMESTAMP()
	WHERE bodyid=$bodyid");

        } else {
	
	$result = $db->query("INSERT INTO bodies (bodyurl, body, typeid, active, url, notes, serviceid, created, modified)
	VALUES ('$bodyurl','$body','$typeid',$active,'$url','$notes',3,UNIX_TIMESTAMP(),UNIX_TIMESTAMP())");

        }
        

if (!$result) {
?>
	echo "SQL FAILED";
<?
} else {
?>

	<script language="JavaScript">
	opener.location.reload(true);
	self.close();
	</script>

<?php
        }

    } else {

        echo '<p>The body information was not saved. Please correct these errors and resubmit the form:</p><ul>';

        foreach ($errors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }

        echo '</ul>';
    }
}

?>

<form name="body" method="post" action="newbody.php">

<table class="table table-striped table-bordered">

<tr>
<td class="label">Body:</td>
<td>
<input type="text"
name="body"
value="<?php echo htmlspecialchars($body); ?>"
maxlength="100"
style="width:250px;" />
</td>
</tr>

<tr>
<td class="label">Type:</td>
<td>

<?php

$bodytypes = $db->get_results(
    "SELECT typeid, type FROM bodytypes ORDER BY type",
    ARRAY_N
);

if ($bodytypes) {
    build_select_box($bodytypes, "typeid", $typeid);
} else {
    echo "<p style='color:red;'>Error loading body types</p>";
}

?>

</td>
</tr>

<tr>
<td class="label">Active?</td>
<td class="label">
<?php build_yes_no_checkbox("active", $active); ?>
</td>
</tr>

<tr>
<td class="label">URL:</td>
<td>
<input type="text"
name="url"
value="<?php echo htmlspecialchars($url); ?>"
maxlength="100"
style="width:200px;" />
</td>
</tr>

<tr>
<td class="label">Notes:</td>
<td>
<input type="text"
name="notes"
value="<?php echo htmlspecialchars($notes); ?>"
maxlength="2000"
style="width:300px;" />
</td>
</tr>

<tr>
<td colspan="2" class="formbtns">

<input class="btn btn-lg btn-primary"
type="submit"
name="save"
value="Save" />

<input class="btn btn-lg btn-primary"
type="button"
value="Cancel"
onclick="window.close();" />

<?php
if (!empty($bodyid)) {
    echo '<input type="hidden" name="bodyid" value="' . intval($bodyid) . '" />';
}
?>

</td>
</tr>

</table>

</form>

<script>
document.body.body.focus();
</script>

</body>
</html>