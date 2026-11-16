
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once "includes/prepend.inc.php";

header("Cache-Control: private");

$errors = array();

$venue = $_POST['venue'] ?? '';
$settlementid = $_POST['settlementid'] ?? '';
$address = $_POST['address'] ?? '';
$postcode = $_POST['postcode'] ?? '';
$uprn = $_POST['uprn'] ?? '';

if (isset($_POST['save'])) {

    if (trim($venue) === '') {
        $errors[] = "Please enter a venue name";
    }

    if (count($errors) === 0) {

        // Escape submitted values using the existing database wrapper.
        $venue_sql = $db->escape($venue);
        $settlementid_sql = $db->escape($settlementid);
        $address_sql = $db->escape($address);
        $postcode_sql = $db->escape($postcode);

	if (trim((string)$uprn) !== '' && ctype_digit((string)$uprn)) {
	    $uprn_sql = (string)(int)$uprn;
	} else {
	    $uprn_sql = "0";
	}

        // Insert new venue.
        $sql = "
            INSERT INTO events.venues
            (venue, settlementid, address, postcode, uprn)
            VALUES (
                '$venue_sql',
                '$settlementid_sql',
                '$address_sql',
                '$postcode_sql',
                $uprn_sql
            )
        ";

        if ($db->query($sql) === true) {

            $newid = $db->insert_id;
            ?>

            <script>
            self.opener.doAddVenue(
                <?php echo json_encode($venue); ?>,
                <?php echo json_encode((string)$newid); ?>
            );
            self.close();
            </script>

            <?php
            exit;

        } else {
            $errors[] = "The venue could not be saved. Please try again.";
        }
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

<h3>Add new venue</h3>

<?php
if (count($errors) > 0) {

    echo '<p>The venue was not saved. Please correct these errors and resubmit the form:</p><ul>';

    foreach ($errors as $error) {
        echo '<li>' . htmlspecialchars($error, ENT_QUOTES, 'ISO-8859-1') . '</li>';
    }

    echo '</ul>';
}
?>

<div id="formarea">

<script>
function doAddLocation(address, uprn, postcode) {
    document.forms['venuefrm'].address.value = address;
    document.forms['venuefrm'].uprn.value = uprn;
    document.forms['venuefrm'].postcode.value = postcode;
}
</script>

<form name="venuefrm" method="post" action="newvenue.php">

<table class="table table-striped table-bordered">

<tr>
    <td class="label">Venue:</td>
    <td>
        <input
            type="text"
            name="venue"
            value="<?php echo htmlspecialchars($venue, ENT_QUOTES, 'ISO-8859-1'); ?>"
            maxlength="200"
            style="width: 250px;"
        />
    </td>
</tr>

<tr>
    <td class="label">Settlement:</td>
    <td>
        <?php
        $settlements = $db->get_results(
            "SELECT settlementid, settlement FROM settlements ORDER BY settlement",
            ARRAY_N
        );

        build_select_box($settlements, "settlementid", $settlementid);
        ?>
    </td>
</tr>

<tr>
    <td class="label">
        Address:
        <a href="#" onClick="window.open('newlocation.php', 'loc_new', 'width=500,height=300,resizable=yes,scrollbars=yes,top=50,left=50')">
            <img src="images/add.gif" alt="Add new location" border="0" />
        </a>
    </td>
    <td>
        <textarea name="address" style="width: 250px; height: 80px;"><?php echo htmlspecialchars($address, ENT_QUOTES, 'ISO-8859-1'); ?></textarea>
    </td>
</tr>

<tr>
    <td class="label">Postcode:</td>
    <td>
        <input
            type="text"
            name="postcode"
            value="<?php echo htmlspecialchars($postcode, ENT_QUOTES, 'ISO-8859-1'); ?>"
            maxlength="10"
            style="width: 80px;"
        />
    </td>
</tr>

<tr>
    <td class="label">UPRN (read only):</td>
    <td>
        <input
            type="text"
            name="uprn"
            value="<?php echo htmlspecialchars((string)$uprn, ENT_QUOTES, 'ISO-8859-1'); ?>"
            readonly="readonly"
            style="width: 80px;"
        />
    </td>
</tr>

<tr>
    <td colspan="2" class="formbtns">
        <input type="submit" class="btn btn-md btn-primary" name="save" value="Save" />
        <input type="submit" class="btn btn-md btn-primary" name="cancel" value="Cancel" onclick="self.close();" />
    </td>
</tr>

</table>

</form>
</div>

<script>
document.venuefrm.venue.focus();
</script>

</body>
</html>
