<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/config.php';         
include_once $_SERVER['DOCUMENT_ROOT'].'/include/tools.php';        
include_once $_SERVER['DOCUMENT_ROOT'].'/include/functions.php';

?>
<span style="font-weight: bold;font-size:14px;">Status</span>
<fieldset style="background-color:#e8e8e8e8;width:160px;margin-top:8px;;margin-bottom:0px;margin-left:0px;margin-right:3px;font-size:12px;border-top-left-radius: 10px; border-top-right-radius: 10px;border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
  <table>
    <tr><th colspan="2">Modes Enabled:</th></tr>
    <tr><?php showMode("DMR", $mmdvmconfigs);?><?php showMode("System Fusion", $mmdvmconfigs);?></tr>
    <tr><?php showMode("NXDN", $mmdvmconfigs);?><?php showMode("P25", $mmdvmconfigs);?></tr>
    <tr><?php showMode("D-Star", $mmdvmconfigs);?><td style="background:#606060; color:#b0b0b0; width:15%;"></tr>
  </table>
<br>
<table>
  <tr><th colspan="2">Networks:</th></tr>
  <tr><?php showMode("DMR Network", $mmdvmconfigs);?><?php showMode("System Fusion Network", $mmdvmconfigs);?></tr>
  <tr><?php showMode("NXDN Network", $mmdvmconfigs);?><?php showMode("P25 Network", $mmdvmconfigs);?></tr>
  <tr><?php showMode("D-Star Network", $mmdvmconfigs);?><td style="background:#606060; color:#b0b0b0; width:15%;"></tr>
</table>
<?php
$testMMDVModeDMR = getConfigItem("DMR", "Enable", $mmdvmconfigs);
if ( $testMMDVModeDMR == 1 ) { //Hide the DMR information when DMR mode not enabled.
if (file_exists('/opt/DMRGateway/DMRGateway.ini')) {
$dmrGatewayConfigFile = '/opt/DMRGateway/DMRGateway.ini';
if (fopen($dmrGatewayConfigFile,'r')) { $configdmrgateway = parse_ini_file($dmrGatewayConfigFile, true); } }
$dmrMasterFile = fopen("/var/lib/mmdvm/DMR_Hosts.txt", "r");
$dmrMasterHost = getConfigItem("DMR Network", "Address", $mmdvmconfigs);
$dmrMasterPort = getConfigItem("DMR Network", "Port", $mmdvmconfigs);
if ($dmrMasterHost == '127.0.0.1') {
    if (isset($configdmrgateway['XLX Network 1']['Address'])) { $xlxMasterHost1 = $configdmrgateway['XLX Network 1']['Address']; }
    else { $xlxMasterHost1 = ""; }
    $dmrMasterHost1 = $configdmrgateway['DMR Network 1']['Address'];
    $dmrMasterHost2 = $configdmrgateway['DMR Network 2']['Address'];
    $dmrMasterHost3 = str_replace('_', ' ', $configdmrgateway['DMR Network 3']['Name']);
    if (isset($configdmrgateway['DMR Network 4']['Name'])) {$dmrMasterHost4 = str_replace('_', ' ', $configdmrgateway['DMR Network 4']['Name']);}
    if (isset($configdmrgateway['DMR Network 5']['Name'])) {$dmrMasterHost5 = str_replace('_', ' ', $configdmrgateway['DMR Network 5']['Name']);}
    while (!feof($dmrMasterFile)) {
	$dmrMasterLine = fgets($dmrMasterFile);
                $dmrMasterHostF = preg_split('/\s+/', $dmrMasterLine);
	if ((count($dmrMasterHostF) >= 2) && (strpos($dmrMasterHostF[0], '#') === FALSE) && ($dmrMasterHostF[0] != '')) {
	    if ((strpos($dmrMasterHostF[0], 'XLX_') === 0) && ($xlxMasterHost1 == $dmrMasterHostF[2])) { $xlxMasterHost1 = str_replace('_', ' ', $dmrMasterHostF[0]); }
	    if ((strpos($dmrMasterHostF[0], 'BM_') === 0) && ($dmrMasterHost1 == $dmrMasterHostF[2])) { $dmrMasterHost1 = str_replace('_', ' ', $dmrMasterHostF[0]); }
	    if ((strpos($dmrMasterHostF[0], 'DMR+_') === 0) && ($dmrMasterHost2 == $dmrMasterHostF[2])) { $dmrMasterHost2 = str_replace('_', ' ', $dmrMasterHostF[0]); }
	}
    }
    if (strlen($xlxMasterHost1) > 19) { $xlxMasterHost1 = substr($xlxMasterHost1, 0, 17) . '..'; }
    if (strlen($dmrMasterHost1) > 19) { $dmrMasterHost1 = substr($dmrMasterHost1, 0, 17) . '..'; }
    if (strlen($dmrMasterHost2) > 19) { $dmrMasterHost2 = substr($dmrMasterHost2, 0, 17) . '..'; }
    if (strlen($dmrMasterHost3) > 19) { $dmrMasterHost3 = substr($dmrMasterHost3, 0, 17) . '..'; }
    if (isset($dmrMasterHost4)) { if (strlen($dmrMasterHost4) > 19) { $dmrMasterHost4 = substr($dmrMasterHost4, 0, 17) . '..'; } }
    if (isset($dmrMasterHost5)) { if (strlen($dmrMasterHost5) > 19) { $dmrMasterHost5 = substr($dmrMasterHost5, 0, 17) . '..'; } }
}
else {
    while (!feof($dmrMasterFile)) {
	$dmrMasterLine = fgets($dmrMasterFile);
                $dmrMasterHostF = preg_split('/\s+/', $dmrMasterLine);
	if ((count($dmrMasterHostF) >= 4) && (strpos($dmrMasterHostF[0], '#') === FALSE) && ($dmrMasterHostF[0] != '')) {
	    if (($dmrMasterHost == $dmrMasterHostF[2]) && ($dmrMasterPort == $dmrMasterHostF[4])) { $dmrMasterHost = str_replace('_', ' ', $dmrMasterHostF[0]); }
	}
    }
    if (strlen($dmrMasterHost) > 19) { $dmrMasterHost = substr($dmrMasterHost, 0, 17) . '..'; }
}
fclose($dmrMasterFile);
echo "<br />\n";
echo "<table>\n";
echo "<tr><th colspan=\"2\">DMR Master</th></tr>\n";
if (getEnabled("DMR Network", $mmdvmconfigs) == 1) {
	if ($dmrMasterHost == '127.0.0.1') {
	    if ((isset($configdmrgateway['XLX Network 1']['Enabled'])) && ($configdmrgateway['XLX Network 1']['Enabled'] == 1)) {
		echo "<tr><td  style=\"background: #ffffff;\" colspan=\"2\">".$xlxMasterHost1."</td></tr>\n";
	    }
                        if ( !isset($configdmrgateway['XLX Network 1']['Enabled']) && isset($configdmrgateway['XLX Network']['Enabled']) && $configdmrgateway['XLX Network']['Enabled'] == 1) {
		if (file_exists("/var/log/mmdvm/DMRGateway-".gmdate("Y-m-d").".log")) { $xlxMasterHost1 = exec('grep \'XLX, Linking\|Unlinking\' /var/log/pi-star/DMRGateway-'.gmdate("Y-m-d").'.log | tail -1 | awk \'{print $5 " " $8 " " $9}\''); }
                                else { $xlxMasterHost1 = exec('grep \'XLX, Linking\|Unlinking\' /var/log/pi-star/DMRGateway-'.gmdate("Y-m-d", time() - 86340).'.log | tail -1 | awk \'{print $5 " " $8 " " $9}\''); }
		if ( strpos($xlxMasterHost1, 'Linking') !== false ) { $xlxMasterHost1 = str_replace('Linking ', '', $xlxMasterHost1); }
		else if ( strpos($xlxMasterHost1, 'Unlinking') !== false ) { $xlxMasterHost1 = "XLX Not Linked"; }
		echo "<tr><td  style=\"background: #ffffff;\" colspan=\"2\">".$xlxMasterHost1."</td></tr>\n";
                        }
	    if ($configdmrgateway['DMR Network 1']['Enabled'] == 1) {
		echo "<tr><td  style=\"background: #ffffff;\" colspan=\"2\">".$dmrMasterHost1."</td></tr>\n";
	    }
	    if ($configdmrgateway['DMR Network 2']['Enabled'] == 1) {
		echo "<tr><td  style=\"background: #ffffff;\" colspan=\"2\">".$dmrMasterHost2."</td></tr>\n";
	    }
	    if ($configdmrgateway['DMR Network 3']['Enabled'] == 1) {
		echo "<tr><td  style=\"background: #ffffff;\" colspan=\"2\">".$dmrMasterHost3."</td></tr>\n";
	    }
	    if (isset($configdmrgateway['DMR Network 4']['Enabled'])) {
		if ($configdmrgateway['DMR Network 4']['Enabled'] == 1) {
		    echo "<tr><td  style=\"background: #ffffff;\" colspan=\"2\">".$dmrMasterHost4."</td></tr>\n";
		}
	    }
	    if (isset($configdmrgateway['DMR Network 5']['Enabled'])) {
		if ($configdmrgateway['DMR Network 5']['Enabled'] == 1) {
		    echo "<tr><td  style=\"background: #ffffff;\" colspan=\"2\">".$dmrMasterHost5."</td></tr>\n";
		}
	    }
	}
	else {
	    echo "<tr><td  style=\"background: #ffffff;\" colspan=\"2\">".$dmrMasterHost."</td></tr>\n";
	}
    }
    else {
	echo "<tr><td colspan=\"2\" style=\"background:#606060; color:#b0b0b0;\">No DMR Network</td></tr>\n";
    }
echo "</table>\n";
}

$testMMDVModeYSF = getConfigItem("System Fusion Network", "Enable", $mmdvmconfigs);
if ( $testMMDVModeYSF == 1 ) { //Hide the YSF information when System Fusion Network mode not enabled.
        $ysfLinkedTo = getActualLink($reverseLogLinesYSFGateway, "YSF");
        if ($ysfLinkedTo == 'Not Linked' || $ysfLinkedTo == 'Service Not Started') {
                $ysfLinkedToTxt = $ysfLinkedTo;
        } else {
                $ysfHostFile = fopen("/var/lib/mmdvm/YSFHosts.txt", "r");
                $ysfLinkedToTxt = "null";
                while (!feof($ysfHostFile)) {
                        $ysfHostFileLine = fgets($ysfHostFile);
                        $ysfRoomTxtLine = preg_split('/;/', $ysfHostFileLine);
                        if (empty($ysfRoomTxtLine[0]) || empty($ysfRoomTxtLine[1])) continue;
                        if (($ysfRoomTxtLine[0] == $ysfLinkedTo) || ($ysfRoomTxtLine[1] == $ysfLinkedTo)) {
                                $ysfLinkedToTxt = $ysfRoomTxtLine[1];
                                break;
                        }
                }
                if ($ysfLinkedToTxt != "null") { $ysfLinkedToTxt = "Room: ".$ysfLinkedToTxt; } else { $ysfLinkedToTxt = "Linked to ".$ysfLinkedTo; }
                $ysfLinkedToTxt = str_replace('_', ' ', $ysfLinkedToTxt);
        }
        if (strlen($ysfLinkedToTxt) > 19) { $ysfLinkedToTxt = substr($ysfLinkedToTxt, 0, 17) . '..'; }
        echo "<br />\n";
        echo "<table>\n";
        echo "<tr><th colspan=\"2\">YSF Net</th></tr>\n";
        echo "<tr><td colspan=\"2\"style=\"background: #ffffff;\">".$ysfLinkedToTxt."</td></tr>\n";
        echo "</table>\n";
}
$testMMDVModeP25 = getConfigItem("P25 Network", "Enable", $mmdvmconfigs);
if ( $testMMDVModeP25 == 1 ) { //Hide the P25 information when P25 Network mode not enabled.
    echo "<br />\n";
    echo "<table>\n";
    echo "<tr><th colspan=\"2\">P25 Net</th></tr>\n";
    echo "<tr><td colspan=\"2\"style=\"background: #ffffff;\">".getActualLink($logLinesP25Gateway, "P25")."</td></tr>\n";
    echo "</table>\n";
}

$testMMDVModeNXDN = getConfigItem("NXDN Network", "Enable", $mmdvmconfigs);
if ( $testMMDVModeNXDN == 1 ) { //Hide the NXDN information when NXDN Network mode not enabled.
    echo "<br />\n";
    echo "<table>\n";
    echo "<tr><th colspan=\"2\">NXDN Net</th></tr>\n";
    if (file_exists('/opt/NXDNGateway/NXDNGateway.ini')) {
	echo "<tr><td colspan=\"2\"style=\"background: #ffffff;\">".getActualLink($logLinesNXDNGateway, "NXDN")."</td></tr>\n";
    } else {
	echo "<tr><td colspan=\"2\"style=\"background: #ffffff;\">Linked to TG65000</td></tr>\n";
    }
    echo "</table>\n";
}
$testMMDVModeDSTAR = getConfigItem("D-Star", "Enable", $mmdvmconfigs);
if ( $testMMDVModeDSTAR == 1 ) { //Hide the D-Star Reflector information when D-Star Network not enabled.
//Load the ircDDBGateway config file
$configs = array();
if ($configfile = fopen('/etc/ircddbgateway','r')) {
        while ($line = fgets($configfile)) {
                list($key,$value) = preg_split('/=/',$line);
                $value = trim(str_replace('"','',$value));
                if ($key != 'ircddbPassword' && strlen($value) > 0)
                $configs[$key] = $value;
        }
}
    echo "<br />\n";
    echo "<table>\n";
    echo "<tr><th colspan=\"2\">DStar Net</th></tr>\n";
    echo "<tr><th>IRC</th><td style=\"background: #ffffff;\">".substr($configs['ircddbHostname'], 0 ,16)."</td></tr>\n";
    echo "<tr><td colspan=\"2\" style=\"background: #ffffff;\">".getActualLink($reverseLogLinesMMDVM, "D-Star")."</td></tr>\n";
    echo "</table>\n";
}


?>
</fieldset>
