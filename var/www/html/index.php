<?php
// DVSwitch Server Dashboard based on Pi-Star Dashboard, © Andy Taylor (MW0MWZ). Version SP2ONG 2020
$progname = basename($_SERVER['SCRIPT_FILENAME'],".php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" lang="en">
<head>
    <meta name="robots" content="index" />
    <meta name="robots" content="follow" />
    <meta name="language" content="English" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="generator" content="DVSM" />
    <meta name="Description" content="Dashboard" />
    <meta http-equiv="cache-control" content="max-age=0" />
    <meta http-equiv="cache-control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="expires" content="0" />
    <meta http-equiv="pragma" content="no-cache" />
<link rel="shortcut icon" href="images/favicon.ico" sizes="16x16 32x32" type="image/png">
    <title>DVSwitch Server Dashboard</title>
<?php include_once "include/browserdetect.php"; ?>
    <script type="text/javascript" src="/scripts/jquery.min.js"></script>
    <script type="text/javascript" src="/scripts/functions.js"></script>
    <script type="text/javascript">
      $.ajaxSetup({ cache: false });
    </script>
    <link href="/css/featherlight.css" type="text/css" rel="stylesheet" />
    <script src="/scripts/featherlight.js" type="text/javascript" charset="utf-8"></script>
</head>
<body style="background-color: #f8f8f8f8;font: 11pt arial, sans-serif;">
<center>
<fieldset style="box-shadow:0 0 10px #999; background-color:#fafafa; width:770px;margin-top:15px;margin-left:0px;margin-right:5px;font-size:13px;border-top-left-radius: 10px; border-top-right-radius: 10px;border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
<div class="container"> 
<div class="header">
<center>
<h2>PI-DVSwitch Server Dashboard</h2>
</center>
</div>
<div class="content"><center>
<div style="margin-top:5px;">
<a target='_blank' href="http://<?=$_SERVER['SERVER_NAME']?>:2812"><button class="button link"><b>MONIT Service Manager</b></button></a>
</div></center>
</div>
<?php
include 'include/config.php';
include_once 'include/tools.php';

function getMMDVMConfigFileContent() {
		// loads ini fule into array for further use
		$conf = array();
		if ($configs = @fopen('/opt/MMDVM_Bridge/MMDVM_Bridge.ini', 'r')) {
			while ($config = fgets($configs)) {
				array_push($conf, trim ( $config, " \t\n\r\0\x0B"));
			}
			fclose($configs);
		}
		return $conf;
	}

$mmdvmconfigfile = getMMDVMConfigFileContent();
    echo '<table style="border:none; border-collapse:collapse; cellspacing:0; cellpadding:0; background-color:#fafafa;"><tr style="border:none;background-color:#fafafa;">';
    echo '<td width="205px" valign="top" style="border:none;background-color:#fafafa;">';
    echo '<div class="nav">'."\n";
    echo '<script type="text/javascript">'."\n";
    echo 'function reloadModeInfo(){'."\n";
    echo '  $("#modeInfo").load("/include/mode.php",function(){ setTimeout(reloadModeInfo,1000) });'."\n";
    echo '}'."\n";
    echo 'setTimeout(reloadModeInfo,1000);'."\n";
    echo '$(window).trigger(\'resize\');'."\n";
    echo '</script>'."\n";
    echo '<div id="modeInfo">'."\n";
    include 'include/mode.php';			// Mode and Networks Info
    echo '</div>'."\n";
    echo '</div>'."\n";
    echo '</td>'."\n";

    echo '<td valign="top" style="border:none; background-color:#fafafa;">';
    echo '<div class="content">'."\n";
    echo '<script type="text/javascript">'."\n";
    echo 'function reloadLocalTx(){'."\n";
    echo '  $("#localTxs").load("/include/localtx.php",function(){ setTimeout(reloadLocalTx,1500) });'."\n";
    echo '}'."\n";
    echo 'setTimeout(reloadLocalTx,1500);'."\n";
    echo 'function reloadLastHerd(){'."\n";
    echo '  $("#lastHerd").load("/include/lh.php",function(){ setTimeout(reloadLastHerd,1500) });'."\n";
    echo '}'."\n";
    echo 'setTimeout(reloadLastHerd,1500);'."\n";
    echo '$(window).trigger(\'resize\');'."\n";
    echo '</script>'."\n";
    echo '<div id="lastHerd">'."\n";
    include 'include/lh.php';
    echo '</div>'."\n";
    echo "<br />\n";
    echo '<div id="localTxs">'."\n";
    include 'include/localtx.php';
    echo '</div>'."\n";
    echo '</td>';
?>
</tr></table>
<?php
    echo '<div class="content">'."\n";
    echo '<script type="text/javascript">'."\n";
    echo 'function reloadSysInfo(){'."\n";
    echo '  $("#sysInfo").load("/include/system.php",function(){ setTimeout(reloadSysInfo,15000) });'."\n";
    echo '}'."\n";
    echo 'setTimeout(reloadSysInfo,15000);'."\n";
    echo '$(window).trigger(\'resize\');'."\n";
    echo '</script>'."\n";
    echo '<div id="sysInfo">'."\n";
    include 'include/system.php';		// Basic System Info
    echo '</div>'."\n";
    echo '</div>'."\n";
?>
<div class="content">
<center><span style="font: 7pt arial, sans-serif;">DVSwitch Server Dashboard (2020) based on Pi-Star MW0MWZ</span></cnter>
</div>
</div>
</fieldset>
</body>
</html>
