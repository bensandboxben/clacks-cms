<?php
include_once "includes/prepend.inc.php";
if (check_auth("slgcl", $_SESSION["userid"]) == 0) {
	header("Location: index.php");
}
header("Cache-Control: private");
function get_children($termid) {
	global $db;
	if ($childterms = $db->get_results("SELECT terms.termid, term FROM slgcl.terms LEFT JOIN slgcl.broaderterms USING(termid) WHERE broadertermid=$termid")) {
		return($childterms);
	} else {
		return false;
	}
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
<h3>Scottish slgcl</h3>
<ul>
<?php
$topterms = $db->get_results("SELECT COUNT( broaderterms.broadertermid )  AS parents, term, terms.termid
FROM slgcl.terms
LEFT JOIN slgcl.broaderterms
ON terms.termid=broaderterms.termid
WHERE preferred=1
GROUP BY term
HAVING parents=0
ORDER BY term");
foreach ($topterms as $topterm) {
	echo '<p>' . $topterm->term . '</p>';
	if ($childterms = get_children($topterm->termid)) {
		foreach($childterms as $childterm) {
			echo '<p>' . $topterm->term . '|' . $childterm->term . '</p>';
			if ($child2terms = get_children($childterm->termid)) {
				foreach($child2terms as $child2term) {
					echo '<p>' . $topterm->term . '|' . $childterm->term . '|' . $child2term->term . '</p>';
					if ($child3terms = get_children($child2term->termid)) {
						foreach($child3terms as $child3term) {
							echo '<p>' . $topterm->term . '|' . $childterm->term . '|' . $child2term->term . '|' . $child3term->term . '</p>';
							if ($child4terms = get_children($child3term->termid)) {
								foreach($child4terms as $child4term) {
									echo '<p>' . $topterm->term . '|' . $childterm->term . '|' . $child2term->term . '|' . $child3term->term . '|' . $child4term->term . '</p>';
									if ($child5terms = get_children($child4term->termid)) {
										foreach($child5terms as $child5term) {
											echo '<p>' . $topterm->term . '|' . $childterm->term . '|' . $child2term->term . '|' . $child3term->term . '|' . $child4term->term . '|' . $child5term->term . '</p>';
											if ($child6terms = get_children($child5term->termid)) {
												foreach($child6terms as $child6term) {
													echo '<p>' . $topterm->term . '|' . $childterm->term . '|' . $child2term->term . '|' . $child3term->term . '|' . $child4term->term . '|' . $child5term->term . '|' . $child6term->term . '</p>';
													if ($child7terms = get_children($child6term->termid)) {
														foreach($child7terms as $child7term) {
															echo '<p>' . $topterm->term . '|' . $childterm->term . '|' . $child2term->term . '|' . $child3term->term . '|' . $child4term->term . '|' . $child5term->term . '|' . $child6term->term . '|' . $child7term->term . '</p>';
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}
?>
</div>
</body>
</html>