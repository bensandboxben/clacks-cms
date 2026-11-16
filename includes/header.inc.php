<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
<title>ClacksWeb CMS</title>


<?php header('Content-Type: text/html; charset=iso-8859-1');?>

<!-- Bootstrap core CSS -->
<link href="/css/bootstrap.css" rel="stylesheet">
 <!-- Core  styles for this template -->
<link rel="stylesheet" href="css/intranetscreen.css" type="text/css">
<link rel="stylesheet" href="css/calendar.css" type="text/css">

<script language="javascript" type="text/javascript">
<!--
function go()
{
	box = document.forms[0].navi;
	destination = box.options[box.selectedIndex].value;
	if (destination) location.href = destination;
}
// -->
</script>

<meta charset="iso-8859-1">
<meta http-equiv="Content-type" content="text/html; charset=iso-8859-1">

</head>
	<body>
	 <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
	  <div class="container" style="padding:0 5px 20px 5px;height: 103px;">
	    <div class="navbar-header">
	      <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
	        <span class="sr-only">Toggle navigation</span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	        <span class="icon-bar"></span>
	      </button>
	      	<a class="navbar-brand" href="index.php"><img src="images/h1.gif" alt="Logo" title="Logo"></a>
	    </div>


<nav class="collapse navbar-collapse bs-navbar-collapse navbar-right" role="navigation" style="padding-top:10px;">



<?php
session_start();

if (isset($_SESSION["username"])) {
	$useraccess = $db->get_row("SELECT * FROM cms.useraccess WHERE userid='$_SESSION[userid]'");

	//print_r($useraccess);
?>

				<a style="color:#fff;text-decoration:none;" href="index.php">Home</a> <span style="color:#fff;">|</span>
				<a style="color:#fff;text-decoration:none;" href="login.php?logout=1">Logout</a>

<span style="margin-left: 115px;">
		<span style="background: rgb(66, 183, 42); border-radius: 50%; display: inline-block; height: 6px; margin-left: 4px; width: 6px;"></span>
		<a style="color:#ffda00;text-decoration:none;" href="profile.php">
			<strong style="color:#ffda00;text-decoration:none;">
				<?php echo $_SESSION["username"]; ?>
			</strong>
		</a>
</span>

<br /><br /><form name="jumpto">
<select style="padding:7px;" name="navi" onChange="go()">
<option value="">Open</option>


<?php

foreach ($areas as $area => $title) {
    // Check if the property exists in $useraccess before accessing it
    if (property_exists($useraccess, $area) && $useraccess->$area == 1) {
        echo '<option value="' . $area . '.php">' . $title . '</option>';
    }
}

?>
</select></form>
<?php } ?>


	  </div>
	</div>
	  <div class="container" style="padding:130px 5px 0 5px;">


	<?php

if (($_SERVER['REQUEST_URI'] == "/cms/") || ($_SERVER['REQUEST_URI'] == "/cms/index.php") || ($_SERVER['REQUEST_URI'] == "/cms/login.php") || ($_SERVER['REQUEST_URI'] == "/cms/profile.php") || ($_SERVER['REQUEST_URI'] == "/cms/login.php?logout=1"))
			{
	?>
				<div id="title">&nbsp;</div>

	<?php
			}
		else
			{
	?>
				<h4 style="margin-bottom:0px;padding-bottom:5px;color:#369;"><!--a href="/cms/">Home</a>&nbsp;-->&nbsp;<?php echo $CMStitle; ?></h4>
	<?php
			}
	?>

 </nav>




