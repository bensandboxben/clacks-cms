<?php
include_once "includes/prepend.inc.php";
header("Cache-Control: private");

$sql = "SELECT DISTINCT councillors.name FROM councillors JOIN councillordocinstances ON emid = councillordocinstances.councillorid AND councillordocinstances.councillordocid = '$_GET[docid]' ";
$documents = $db->get_results($sql, ARRAY_N);

?>

<table width="100%" cellspacing="0">
<tr><td>The following councillors now have access to this document:</td></tr>
<tr><td>&nbsp;</td></tr>

<?php

foreach ($documents as $document){
		echo '<tr>';
		echo '<td width="100%">' . $document[0] . '</td>';
		echo '</td>';
		echo '</tr>';
}

echo '<tr><td>&nbsp;</td></tr>';
echo '<tr><td colspan="2" class="formbtns">';
//	<input type="submit" class="btn btn-md btn-secondary" value="Back" onclick="window.open(\'newcouncillordoc.php?noreturn=1&action=edit&id=' . $_GET[docid]  . '\', \'mywindow\', \'width=500,height=550,top=50,left=50,resizable=yes,scrollbars=yes \');" />&nbsp;
echo '	<input type="submit" class="btn btn-md btn-secondary" value="Close" onclick="javascript: window.close();" />';
echo '</td></tr>';
echo '</table>';


$type = $_GET[type];
switch($type)
{
case'19':
	$type='Blank Forms';
	break;
case'16':
	$type='Briefing Papers';
	break;
case'6':
	$type='Charts &amp; diagrams';
	break;
case'5':
	$type='Consultation documents';
	break;
case'15':
	$type='Correspondence';
	break;
case'12':
	$type='Council Committee Papers';
	break;
case'13':
	$type='Exempt & Confidential Papers';
	break;
case'7':
	$type='Guides &amp; manuals';
	break;
case'9':
	$type='Information Bulletins';
	break;
case'14':
	$type='Information Reports';
	break;
case'11':
	$type='Leaflets';
	break;
case'10':
	$type='Legislation';
	break;
case'3':
	$type='Plans';
	break;
case'1':
	$type='Policy documents';
	break;
case'8':
	$type='Programmes';
	break;
case'4':
	$type='Reports';
	break;
case'2':
	$type='Strategy documents';
	break;
case'18':
	$type='Training Papers';
	break;
case'17':
	$type='Weekly Briefings';
	break;
}



$sqlemail = "SELECT DISTINCT councillors.email FROM councillors JOIN councillordocinstances ON emid = councillordocinstances.councillorid AND councillordocinstances.councillordocid =  '$_GET[docid]' ";
$emails = $db->get_results($sqlemail, ARRAY_N);
$mail = new PHPMailer();
$mail->From     = "clacksweb@clacks.gov.uk";
$mail->FromName = "Clackmannanshire Council";
$mail->AddAddress("bshort@clacks.gov.uk");
foreach ($emails as $email){

$mail->AddBCC("$email[0]");
}
$mail->Subject = "A new document of type $type has been added to the Councillors Portal called $_GET[name]";
$mail->Body    = "Please login using the following link to view the Portal:\n\nhttp://clacksweb/site/councillorsportal/";
$mail->Host    = "itssmail02";
$mail->Mailer  = "smtp";
$mail->Send();
?>