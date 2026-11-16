<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");
$db = new db("admin", "HbCLlVH", "polls", "164.177.128.68");
$pollid = $_REQUEST["pollid"];
$poll=$db->get_row("SELECT title FROM polls.polls WHERE pollid='$pollid'");
// Should we output a table of answers?
$print_table = 0;
function show_answer_form($values=array()) {
	global $db;
	echo '<form action="pollanswers.php" method="POST" name="answerfrm">';
	echo '<table class="table table-striped table-bordered">';
	echo '<tr><td class="label">Answer: </td><td><input type="text" maxlength="100" size="50" name="answer" value="' . $values["answer"] . '" />';
	echo '</td></tr>';
	echo '</table>';
	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-primary" name="submit" value="Save">&nbsp;<input type="submit" class="btn btn-md btn-secondary" name="cancel" value="Cancel" onClick="history.go(-1);"></p>';
	echo '<input type="hidden" name="pollid" value="' . $values["pollid"] . '" />';
	if ($_REQUEST["action"] == 'edit') echo '<input type="hidden" name="answerid" value="' . $values["answerid"] . '" />';
	echo '</form>';
}
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
<title>ClacksWeb CMS</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<?php include "includes/head.inc.php"; ?>
</head>
<body class="popup">
<div id="formarea">
<h3>Poll answers</h3>
<?php
switch ($_REQUEST["action"]) {
	case "add":
		echo "<p>Add answer to poll: <strong>$poll->title</strong></p>";
		break;
	case "edit":
		echo "<p>Edit answer for poll: <strong>$poll->title</strong></p>";
		break;
	default:
		echo '<p>Manage answers: <strong>' . $poll->title . '</strong> <a href="pollanswers.php?action=add&pollid=' . $pollid . '"><svg class="icon icon-add" aria-hidden="true" focusable="false"><use href="images/icons.svg#plus"></use></svg><span class="sr-only">Add new answer</span></a></p>';
		break;
}
if ($_POST["submit"]) {
	$print_table = 1;
	$_POST["answer"] = $db->escape($_POST["answer"]);
	if (($_POST["pollid"])&&($_POST["answerid"])) {
		$db->query("UPDATE polls.answers SET answer='$_POST[answer]' WHERE pollid='$_POST[pollid]' AND answerid='$_POST[answerid]'");
	} else {
		$db->query("INSERT INTO polls.answers (pollid, answer) VALUES('$_POST[pollid]', '$_POST[answer]')");
	}
} else if ($_GET["action"]) {
	switch ($_GET["action"]) {
		case "add":
			// set values for addition
			$values = array();
			$values["pollid"] = $pollid;
			show_answer_form($values);
			break;
		case "edit":
			// set values for edit
			$answer=$db->get_row("SELECT * FROM polls.answers WHERE pollid='$_GET[pollid]' AND answerid='$_GET[answerid]'");
			$values = array();
			$values["pollid"] = $pollid;
			$values["answerid"] = $_GET["answerid"];
			$values["answer"] = $answer->answer;
			show_answer_form($values);
			break;
		case "delete":
			$db->query("DELETE FROM polls.answers WHERE pollid='$_GET[pollid]' AND answerid='$_GET[answerid]'");
			$print_table = 1;
			break;
		default:
			// unknown action
			echo "Unknown action";
			break;
	}
} else {
	$print_table = 1;
}
if (($answers = $db->get_results("SELECT * FROM polls.answers WHERE pollid='$pollid' ORDER BY answerid"))&&($print_table == 1)) {
	echo '<table width="100%"><thead><tr><th>Answer</th></tr></thead><tbody>';
	foreach ($answers as $answer) {
		echo '<tr><td class="detail">' . $answer->answer . '</td>';
		echo '<td width="100" align="right">';
		echo '<a href="pollanswers.php?action=edit&pollid=' . $answer->pollid . '&answerid=' . $answer->answerid . '"><svg class="icon icon-edit" aria-hidden="true" focusable="false"><use href="images/icons.svg#pencil"></use></svg><span class="sr-only">Edit answer</span></a> ';
		echo '<a href="pollanswers.php?action=delete&pollid=' . $answer->pollid . '&answerid=' . $answer->answerid . '"><svg class="icon icon-delete" aria-hidden="true" focusable="false"><use href="images/icons.svg#trash-2"></use></svg><span class="sr-only">Delete answer</span></a>';
		echo '</td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
}
if ($print_table == 1) 	echo '<p class="formbtns"><input type="submit" class="btn btn-md btn-secondary" value="Close" onclick="self.close();"></p>';
?>
</div>
</body>
</html>