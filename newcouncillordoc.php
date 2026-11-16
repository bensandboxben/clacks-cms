 <?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$edit = 0;
if ($_GET["action"] == "edit") {
	$edit = 1;
	$doc_array = $db->get_row("SELECT * FROM councillordocs WHERE docid='$_GET[id]'", ARRAY_A);
	extract($doc_array);
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include "includes/head.inc.php"; ?>
</head>

  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  <script>
  $(function() {
    $( "#datepicker" ).datepicker();
  });
  </script>



<body class="popup">
<?php if ($edit == 1) { ?>
<h3>Edit document</h3>
<?php } else { ?>
<h3>Add new document</h3>
<?php
}
if ($_POST["save"]) {

	$_POST["reviewdate"] = date("Y-m-d", strtotime($_POST["reviewdate"]));

	if ($_POST["docid"]) {

	$tempdocid = $_POST["docid"];

	}


	$errors = array();


	if (!$_POST["name"]) $errors[] = "Please enter the name of this document";
	$query = "SELECT docid FROM councillordocs WHERE name='" . addslashes($_POST["name"]) . "'";
	if ($_POST["docid"]) $query .= " AND docid != " . $_POST["docid"];
	if ($exists = $db->get_var($query)) $errors[] = "A document already exists with this name";
	if (!$_POST["doctypeid"]) $errors[] = "Please select a document type for this document";
	if (!$_POST["wardid"] && !$_POST["partyid"] && !$_POST["emid"]) $errors[] = "Please select a recipient for this document";
	if (!$_POST["description"]) $errors[] = "Please enter a brief description for this document";




		if (($_POST["doctypeid"] == "15") || ($_POST["doctypeid"] == "18"))
		{
			$_POST["reviewdate"] = date('Y-m-d', mktime (0,0,0,date("m"),date("d"),date("Y")+4));
		}
		else
		{
			$_POST["reviewdate"] = date('Y-m-d', mktime (0,0,0,date("m"),date("d"),date("Y")+1));
		}


		if (($_POST["reviewdate"] == '0000-00-00')||($_POST["reviewdate"] == '1970-01-01')||($_POST["reviewdate"] == '')||(!isset($_POST["reviewdate"]))) $_POST["reviewdate"] = "0000-00-00";


	if (count($errors) == 0) {
		$nameurl = strtolower(preg_replace("/[^[:alnum:]]/", "", $_POST["name"]));



		// Saving existing document entry
		if ($_POST["docid"]) {
				$db->query("UPDATE councillordocs SET doctypeid='$_POST[doctypeid]', wardid='$_POST[wardid]', partyid='$_POST[partyid]', name='$_POST[name]', description='$_POST[description]', reviewdate='$_POST[reviewdate]', modified=UNIX_TIMESTAMP() WHERE docid='$_POST[docid]'");
				$db->query("DELETE FROM councillordocinstances WHERE councillordocid ='$_POST[docid]'");


			if ('$_POST[wardid]'){ // if a ward is selected, put a document instance in for every councillor in that ward
				$sql="SELECT emid FROM councillors WHERE wardid='$_POST[wardid]'";
				if ($result=  mysqli_query($sql)) {
					while($ward=  mysqli_fetch_array($result)) {
						$db->query("INSERT INTO councillordocinstances (councillordocid, councillorid) VALUES ($_POST[docid], $ward[0])"); // update table with instance and councillor id
					}
				}
			}

			if ('$_POST[partyid]'){ // if a party is selected, put a document instance in for every councillor in that party
				$sql="SELECT emid FROM councillors WHERE partyid='$_POST[partyid]'";
				if ($result=  mysqli_query($sql)) {
					while($party=  mysqli_fetch_array($result)) {
						$db->query("INSERT INTO councillordocinstances (councillordocid, councillorid) VALUES ($_POST[docid], $party[0])"); // update table with instance and councillor id
					}
				}
			}

			if ('$_POST[emid]'){ // if a councillor is selected, put a document instance in for that councillor
				$tempemid=$_POST['emid'];
	 			foreach ($tempemid as $t)
					{
					$db->query("INSERT INTO councillordocinstances (councillordocid, councillorid) VALUES ($_POST[docid], $t)"); // update table with instance and councillor id
					}
			}

		}

		//	Saving new document
		else {
			$db->query("INSERT INTO councillordocs (doctypeid, wardid, partyid, name, description, reviewdate, created, modified) VALUES('$_POST[doctypeid]', '$_POST[wardid]', '$_POST[partyid]', '$_POST[name]', '$_POST[description]', '$_POST[reviewdate]', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())");
			$newdocid = $db->insert_id;
			$tempdocid = $db->get_var("SELECT docid FROM councillordocs WHERE name = '$_POST[name]'"); // get the new docid to put into the instances table


			if ('$_POST[wardid]'){ // if a ward is selected, put a document instance in for every councillor in that ward
				$sql="SELECT emid FROM councillors WHERE wardid='$_POST[wardid]'";
				if ($result=  mysqli_query($sql)) {
					while($ward=  mysqli_fetch_array($result)) {
						$db->query("INSERT INTO councillordocinstances (councillordocid, councillorid) VALUES ($tempdocid, $ward[0])"); // update table with instance and councillor id
					}
				}
			}

			if ('$_POST[partyid]'){ // if a party is selected, put a document instance in for every councillor in that party
				$sql="SELECT emid FROM councillors WHERE partyid='$_POST[partyid]'";
				if ($result=  mysqli_query($sql)) {
					while($party=  mysqli_fetch_array($result)) {
						$db->query("INSERT INTO councillordocinstances (councillordocid, councillorid) VALUES ($tempdocid, $party[0])"); // update table with instance and councillor id
					}
				}
			}

			if ('$_POST[emid]'){ // if a councillor is selected, put a document instance in for that councillor
				$tempemid=$_POST['emid'];
	 			foreach ($tempemid as $t)
					{
					$db->query("INSERT INTO councillordocinstances (councillordocid, councillorid) VALUES ($tempdocid, $t)"); // update table with instance and councillor id
					}
			}
		}
?>
<script language="JavaScript">
<!--
opener.location.reload(true);
window.open('councillorconfirm.php?docid=<?php echo $tempdocid ?>&name=<?php echo htmlspecialchars($_POST["name"]) ?>&type=<?php echo htmlspecialchars($_POST["doctypeid"]) ?>', 'mywindow', 'width=500,height=550,top=50,left=50,resizable=yes,scrollbars=yes');
self.close();
//-->
</script>

<?php
	} else {
		echo '<p class="form-error-intro" role="alert">The document was not saved. Please correct these errors and resubmit the form:</p><ul class="error">';
		foreach ($errors as $error) {
			echo '<li>' . $error . '</li>';
		}
		echo '</ul>';
	}
}
extract ($_POST);
?>
<div id="formarea">
<script language="JavaScript">
<!--
function doAddCat (label, value) {
var s = document.doc.elements["catid"];
var l = s.length;
s.options[s.length] = new Option(label, value);
s.length = l + 1;
s.selectedIndex = l;
}
//-->




</script>

<?php
	echo'<form name="doc" method="post" action="newcouncillordoc.php">';
?>

<table class="table table-striped table-bordered">
<tr><td class="label">Document type:</td>
<td>
<?php
$doctypes = $db->get_results("SELECT doctypeid, doctype FROM clacksweb2.doctypes ORDER BY doctype", ARRAY_N);
build_select_box($doctypes, "doctypeid", $doctypeid, '-');
?>
</td></tr>

<tr><td class="label">Ward:</td>
<td>
<?php
$wards = $db->get_results("SELECT wardid, wardarea FROM clacksweb2.ward ", ARRAY_N);
build_select_box($wards, "wardid", $wardid, '-');
?>
</td></tr>

<tr><td class="label">Party:</td>
<td>
<?php
$parties = $db->get_results("SELECT partyid, partyname FROM clacksweb2.parties ", ARRAY_N);
build_select_box($parties, "partyid", $partyid, '-');
?>
</td></tr>

<?php
		if ($edit == 1) {
			echo '<tr><td class="label" style="color:#c00;" colspan="2">Editing document, please re-enter councillor permissions:</td></tr>';
			}
?>

<tr><td class="label">Councillor:</td>
<td>
<?php
$councillor = $db->get_results("SELECT emid, name FROM clacksweb2.councillors ORDER BY name", ARRAY_N);

$emid = []; // Define $emid as an empty array or with your desired values

build_multiple_select_box($councillor, "emid[]", $emid); // Check function call
?>


</td></tr>


<tr><td class="label">Document name:</td>
<td><input type="text" name="name" value="<?php echo $name; ?>" maxlength="200" style="width: 300px;" /></td></tr>
<tr><td class="label">Description:</td>
<td><textarea name="description" style="width: 300px; height: 80px;"><?php echo $description; ?></textarea></td></tr>



<tr>
	<td class="label">Review Date:</td>
		<td>
			<input id="datepicker" type="text" name="reviewdate" value="<?php echo $reviewdate; ?>" maxlength="10" style="width: 100px;" />
		</td>
</tr>




<tr>
	<td colspan="2">&nbsp;</td>
</tr>
<tr><td colspan="2" class="formbtns">


<input type="submit" class="btn btn-md btn-primary"  name="save" value="Save" /> <input type="submit" class="btn btn-md btn-secondary" value="Cancel" onclick="javascript: window.close();" />
<?php if ($_REQUEST["noreturn"] == 1) echo '<input type="hidden" name="noreturn" value="1" />'; ?>
<?php if ($docid) echo '<input type="hidden" name="docid" value="' . $docid . '" />'; ?>
</td></tr>
</table>
</form>
</div>
<script>
document.doc.serviceid.focus();
</script>
</body>
</html>