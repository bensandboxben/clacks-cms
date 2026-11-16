<?
$fp = fopen("/www/cwv2/css/screen.css", "rb");
$css = fread($fp, filesize("/www/cwv2/css/screen.css"));
fclose($fp);
$fp = fopen("/www/cwv2/css/standard.css", "rb");
$css .= fread($fp, filesize("/www/cwv2/css/standard.css"));
fclose($fp);
$css = str_replace("body {background-image:url(../images/standard/bodybg.gif)}", "", $css);
$css = str_replace("../", "", $css);
echo $css;
?>
