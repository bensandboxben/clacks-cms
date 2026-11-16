<?php

include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$emid = $_REQUEST["emid"];
$member=$db->get_var("SELECT name FROM councillors WHERE emid='$emid'");
// Should we output a table of interests?
$print_table = 0;
function show_contract_form($values=array()) {
	global $db;
	echo '<form action="memberinterests.php" method="POST" name="contractfrm">';
	echo '<table class="table table-striped table-bordered">';
	echo '<tr><td class="label">Description: </td><td><input style="width: 250px;" type="text" name="description" value="' . $values["description"] . '" />';
	echo '</td></tr>';
	echo '<tr><td class="label">Duration: </td><td><input style="width: 250px;" type="text" name="duration" value="' . $values["duration"] . '" />';
	echo '</td></tr>';
	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="submit" value="Save Contract">&nbsp;<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onClick="history.go(-1);"></p>';
	echo '<input type="hidden" name="emid" value="' . $values["emid"] . '" />';
	if ($_GET["action"] == "editcontract") echo '<input type="hidden" name="id" value="' . $values["id"] . '" />';
	echo '</form>';
}
function show_directorship_form($values=array()) {
	global $db;
	echo '<form action="memberinterests.php" method="POST" name="directorshipfrm">';
	echo '<table class="table table-striped table-bordered">';
	echo '<tr><td class="label">Company: </td><td><input style="width: 250px;" type="text" name="company" value="' . $values["company"] . '" /></td></tr>';
	echo '<tr><td class="label">Nature of business: </td><td><input style="width: 250px;" type="text" name="business" value="' . $values["business"] . '" /></td></tr>';
	echo '<tr><td class="label">Relationship to employment: </td><td><input style="width: 250px;" type="text" name="relationship" value="' . $values["relationship"] . '" /></td></tr>';
	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="submit" value="Save Directorship">&nbsp;<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onClick="history.go(-1);"></p>';
	echo '<input type="hidden" name="emid" value="' . $values["emid"] . '" />';
	if ($_GET["action"] == "editdirectorship") echo '<input type="hidden" name="id" value="' . $values["id"] . '" />';
	echo '</form>';
}
function show_employment_form($values=array()) {
	global $db;
	echo '<form action="memberinterests.php" method="POST" name="employmentfrm">';
	echo '<table class="table table-striped table-bordered">';
	echo '<tr><td class="label">Name of employer: </td><td><input style="width: 250px;" type="text" name="name" value="' . $values["name"] . '" /></td></tr>';
	echo '<tr><td class="label">Nature of business: </td><td><input style="width: 250px;" type="text" name="business" value="' . $values["business"] . '" /></td></tr>';
	echo '<tr><td class="label">Post/activity: </td><td><input style="width: 250px;" type="text" name="activity" value="' . $values["activity"] . '" /></td></tr>';
	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="submit" value="Save Employment">&nbsp;<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onClick="history.go(-1);"></p>';
	echo '<input type="hidden" name="emid" value="' . $values["emid"] . '" />';
	if ($_GET["action"] == "editemployment") echo '<input type="hidden" name="id" value="' . $values["id"] . '" />';
	echo '</form>';
}
function show_land_form($values=array()) {
	global $db;
	echo '<form action="memberinterests.php" method="POST" name="landfrm">';
	echo '<table class="table table-striped table-bordered">';
	echo '<tr><td class="label">Address: </td><td><input style="width: 250px;" type="text" name="address" value="' . $values["address"] . '" /></td></tr>';
	echo '<tr><td class="label">Description: </td><td><input style="width: 250px;" type="text" name="description" value="' . $values["description"] . '" /></td></tr>';
	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="submit" value="Save Land">&nbsp;<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onClick="history.go(-1);"></p>';
	echo '<input type="hidden" name="emid" value="' . $values["emid"] . '" />';
	if ($_GET["action"] == "editland") echo '<input type="hidden" name="id" value="' . $values["id"] . '" />';
	echo '</form>';
}
function show_interests_form($values=array()) {
	global $db;
	echo '<form action="memberinterests.php" method="POST" name="interestsfrm">';
	echo '<table class="table table-striped table-bordered">';
	echo '<tr><td class="label">Election expenses: </td><td><input style="width: 250px;" type="text" name="electionexpenses" value="' . $values["electionexpenses"] . '" /></td></tr>';
	echo '<tr><td class="label">Corporate Tenancies: </td><td><input style="width: 250px;" type="text" name="corporatetenancies" value="' . $values["corporatetenancies"] . '" /></td></tr>';
	echo '<tr><td class="label">Shares: </td><td><input style="width: 250px;" type="text" name="shares" value="' . $values["shares"] . '" /></td></tr>';
	echo '<tr><td class="label">Non-financial interests: </td><td><textarea style="width: 350px; height: 200px;" name="nonfinancial">' . $values["nonfinancial"] . '</textarea></td></tr>';
	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="submit" value="Save Other Interests">&nbsp;<input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" name="cancel" value="Cancel" onClick="history.go(-1);"></p>';
	echo '<input type="hidden" name="emid" value="' . $values["emid"] . '" />';
	echo '</form>';
}
?>
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="css/bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="css/screen.css" type="text/css">
</head>
<body bgcolor="#FFFFFF" class="popup">
<div id="formarea">
<h3>Register of interests</h3>
<?php
switch ($_REQUEST["action"]) {
	case "addcontract":
		echo "<p>Add Contract for Member: <strong>$member</strong></p>";
		break;
	case "adddirectorship":
		echo "<p>Add Directorship for Member: <strong>$member</strong></p>";
		break;
	case "addemployment":
		echo "<p>Add Employment for Member: <strong>$member</strong></p>";
		break;
	case "addland":
		echo "<p>Add Land for Member: <strong>$member</strong></p>";
		break;
	case "edit":
		echo "<p>Edit Interests for Member: <strong>$member</strong></p>";
		break;
	case "editcontract":
		echo "<p>Edit Contract for Member: <strong>$member</strong></p>";
		break;
	case "editdirectorship":
		echo "<p>Edit Directorship for Member: <strong>$member</strong></p>";
		break;
	case "editemployment":
		echo "<p>Edit Employment for Member: <strong>$member</strong></p>";
		break;
	case "editland":
		echo "<p>Edit Land for Member: <strong>$member</strong></p>";
		break;
	default:
		echo '<p>Register of Interests for Member: <strong>' . $member . '</strong></p>';
		break;
}
if ($_POST["submit"]) {
	$db->query("UPDATE miinterests SET updated=UNIX_TIMESTAMP() WHERE emid='$_POST[emid]'");
	switch ($_POST["submit"]) {
		case "Save Contract":
			if ($_POST["id"]) {
				$db->query("UPDATE micontracts SET description='$_POST[description]', duration='$_POST[duration]' WHERE id='$_POST[id]'");
			} else {
				$db->query("INSERT INTO micontracts (emid, description, duration) VALUES('$_POST[emid]', '$_POST[description]', '$_POST[duration]')");
			}
			break;
		case "Save Directorship":
			if ($_POST["id"]) {
				$db->query("UPDATE midirectorships SET company='$_POST[company]', business='$_POST[business]', relationship='$_POST[relationship]' WHERE id='$_POST[id]'");
			} else {
				$db->query("INSERT INTO midirectorships (emid, company, business, relationship) VALUES('$_POST[emid]', '$_POST[company]', '$_POST[business]', '$_POST[relationship]')");
			}
			break;
		case "Save Employment":
			if ($_POST["id"]) {
				$db->query("UPDATE miemployment SET name='$_POST[name]', business='$_POST[business]', activity='$_POST[activity]' WHERE id='$_POST[id]'");
			} else {
				$db->query("INSERT INTO miemployment (emid, name, business, activity) VALUES('$_POST[emid]', '$_POST[name]', '$_POST[business]', '$_POST[activity]')");
			}
			break;
		case "Save Land":
			if ($_POST["id"]) {
				$db->query("UPDATE miland SET address='$_POST[address]', description='$_POST[description]' WHERE id='$_POST[id]'");
			} else {
				$db->query("INSERT INTO miland (emid, address, description) VALUES('$_POST[emid]', '$_POST[address]', '$_POST[description]')");
			}
			break;
		case "Save Other Interests":

			$electionexpenses = addslashes($_POST["electionexpenses"]);
			$corporatetenancies = addslashes($_POST["corporatetenancies"]);
			$shares = addslashes($_POST["shares"]);
			$nonfinancial = addslashes($_POST["nonfinancial"]);

			$db->query("UPDATE miinterests SET electionexpenses='$electionexpenses', corporatetenancies='$corporatetenancies', shares='$shares', nonfinancial='$nonfinancial' WHERE emid='$_POST[emid]'");
			break;
	}
	$print_table = 1;
} else if ($_GET["action"]) {
	switch ($_GET["action"]) {
		case "addcontract":
			// set values for addition
			$values = array();
			$values["emid"] = $emid;
			show_contract_form($values);
			break;
		case "editcontract":
			// set values for edit
			$contract=$db->get_row("SELECT * FROM micontracts WHERE id='$_GET[id]'");
			$values = array();
			$values["emid"] = $emid;
			$values["id"] = $_GET["id"];
			$values["description"] = $contract->description;
			$values["duration"] = $contract->duration;
			show_contract_form($values);
			break;
		case "deletecontract":
			$db->query("DELETE FROM micontracts WHERE id='$_GET[id]'");
			$print_table = 1;
			break;
		case "adddirectorship":
			// set values for addition
			$values = array();
			$values["emid"] = $emid;
			show_directorship_form($values);
			break;
		case "editdirectorship":
			// set values for edit
			$directorship=$db->get_row("SELECT * FROM midirectorships WHERE id='$_GET[id]'");
			$values = array();
			$values["emid"] = $emid;
			$values["id"] = $_GET["id"];
			$values["business"] = $directorship->business;
			$values["company"] = $directorship->company;
			$values["relationship"] = $directorship->relationship;
			show_directorship_form($values);
			break;
		case "deletedirectorship":
			$db->query("DELETE FROM midirectorships WHERE id='$_GET[id]'");
			$print_table = 1;
			break;
		case "addemployment":
			// set values for addition
			$values = array();
			$values["emid"] = $emid;
			show_employment_form($values);
			break;
		case "editemployment":
			// set values for edit
			$directorship=$db->get_row("SELECT * FROM miemployment WHERE id='$_GET[id]'");
			$values = array();
			$values["emid"] = $emid;
			$values["id"] = $_GET["id"];
			$values["name"] = $directorship->name;
			$values["business"] = $directorship->business;
			$values["activity"] = $directorship->activity;
			show_employment_form($values);
			break;
		case "deleteemployment":
			$db->query("DELETE FROM miemployment WHERE id='$_GET[id]'");
			$print_table = 1;
			break;
		case "addland":
			// set values for addition
			$values = array();
			$values["emid"] = $emid;
			show_land_form($values);
			break;
		case "editland":
			// set values for edit
			$land=$db->get_row("SELECT * FROM miland WHERE id='$_GET[id]'");
			$values = array();
			$values["emid"] = $emid;
			$values["id"] = $_GET["id"];
			$values["address"] = $land->address;
			$values["description"] = $land->description;
			show_land_form($values);
			break;
		case "deleteland":
			$db->query("DELETE FROM miland WHERE id='$_GET[id]'");
			$print_table = 1;
			break;
		case "edit":
			// set values for edit
			$interests=$db->get_row("SELECT * FROM miinterests WHERE emid='$_GET[emid]'");
			$values = array();
			$values["emid"] = $emid;
			$values["electionexpenses"] = $interests->electionexpenses;
			$values["corporatetenancies"] = $interests->corporatetenancies;
			$values["shares"] = $interests->shares;
			$values["nonfinancial"] = $interests->nonfinancial;
			show_interests_form($values);
			break;
		default:
			// unknown action
			echo "Unknown action";
			break;
	}
} else {
	$print_table = 1;
}
if ($print_table == 1) {
	echo '<h3>Contracts  <a href="memberinterests.php?action=addcontract&emid=' . $emid . '"><img src="images/add.gif" alt="Add new contract" border="0" /></a></h3>';
	if ($contracts = $db->get_results("SELECT * FROM micontracts WHERE emid='$emid' ORDER BY description")) {
		echo '<table width="100%">';
		foreach ($contracts as $contract) {
			echo '<tr><td class="detail">' . $contract->description . '</td>';
			echo '<td class="detail">' . $contract->duration . '</td>';
			echo '<td width="100" align="right">';
			echo '<a href="memberinterests.php?action=editcontract&id=' . $contract->id . '&emid=' . $emid . '"><img src="images/edit.gif" alt="Edit Contract" border="0"></a> ';
			echo '<a href="memberinterests.php?action=deletecontract&id=' . $contract->id . '&emid=' . $emid . '"><img src="images/delete.gif" alt="Delete Contract" border="0"></a>';
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	} else {
		echo '<p>No contracts for ' . $member . '</p>';
	}
	echo '<h3>Directorships  <a href="memberinterests.php?action=adddirectorship&emid=' . $emid . '"><img src="images/add.gif" alt="Add new directorship" border="0" /></a></h3>';
	if ($directorships = $db->get_results("SELECT * FROM midirectorships WHERE emid='$emid' ORDER BY company")) {
		echo '<table width="100%">';
		foreach ($directorships as $directorship) {
			echo '<tr><td class="detail">' . $directorship->company . '</td>';
			echo '<td class="detail">' . $directorship->relationship . '</td>';
			echo '<td width="100" align="right">';
			echo '<a href="memberinterests.php?action=editdirectorship&id=' . $directorship->id . '&emid=' . $emid . '"><img src="images/edit.gif" alt="Edit directorship" border="0"></a> ';
			echo '<a href="memberinterests.php?action=deletedirectorship&id=' . $directorship->id . '&emid=' . $emid . '"><img src="images/delete.gif" alt="Delete directorship" border="0"></a>';
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	} else {
		echo '<p>No directorships for ' . $member . '</p>';
	}
	echo '<h3>Employment  <a href="memberinterests.php?action=addemployment&emid=' . $emid . '"><img src="images/add.gif" alt="Add new employment" border="0" /></a></h3>';
	if ($employments = $db->get_results("SELECT * FROM miemployment WHERE emid='$emid' ORDER BY name")) {
		echo '<table width="100%">';
		foreach ($employments as $employment) {
			echo '<tr><td class="detail">' . $employment->name . '</td>';
			echo '<td class="detail">' . $employment->activity . '</td>';
			echo '<td width="100" align="right">';
			echo '<a href="memberinterests.php?action=editemployment&id=' . $employment->id . '&emid=' . $emid . '"><img src="images/edit.gif" alt="Edit employment" border="0"></a> ';
			echo '<a href="memberinterests.php?action=deleteemployment&id=' . $employment->id . '&emid=' . $emid . '"><img src="images/delete.gif" alt="Delete employment" border="0"></a>';
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	} else {
		echo '<p>No employment for ' . $member . '</p>';
	}
	echo '<h3>Land  <a href="memberinterests.php?action=addland&emid=' . $emid . '"><img src="images/add.gif" alt="Add new land" border="0" /></a></h3>';
	if ($lands = $db->get_results("SELECT * FROM miland WHERE emid='$emid' ORDER BY address")) {
		echo '<table width="100%">';
		foreach ($lands as $land) {
			echo '<tr><td class="detail">' . $land->address . '</td>';
			echo '<td class="detail">' . $land->description . '</td>';
			echo '<td width="100" align="right">';
			echo '<a href="memberinterests.php?action=editland&id=' . $land->id . '&emid=' . $emid . '"><img src="images/edit.gif" alt="Edit land" border="0"></a> ';
			echo '<a href="memberinterests.php?action=deleteland&id=' . $land->id . '&emid=' . $emid . '"><img src="images/delete.gif" alt="Delete land" border="0"></a>';
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	} else {
		echo '<p>No land records for ' . $member . '</p>';
	}
	echo '<h3>Other Interests  <a href="memberinterests.php?action=edit&emid=' . $emid . '"><img src="images/edit.gif" alt="Edit other interests" border="0" /></a></h3>';
	$interests = $db->get_row("SELECT * FROM miinterests WHERE emid='$emid'");
	echo '<table width="100%">';
	echo '<tr><td class="label">Election expenses:</td><td class="detail">' . $interests->electionexpenses . '</td>';
	echo '<tr><td class="label">Corporate tenancies:</td><td class="detail">' . $interests->corporatetenancies . '</td>';
	echo '<tr><td class="label">Shares &amp; securities:</td><td class="detail">' . $interests->shares . '</td>';
	echo '<tr><td class="label">Non-financial interests:</td><td class="detail">' . nl2br($interests->nonfinancial) . '</td>';
	echo '</tr>';
	echo '</table>';
}
if ($print_table == 1) 	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" class="btn btn-md btn-primary" value="Close" onclick="self.close();"></p>';
?>
</div>
</body>
</html>