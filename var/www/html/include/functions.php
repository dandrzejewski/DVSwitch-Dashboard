<?php
function get_string_between($string, $start, $end) {
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0) {
	return "";
    }
    $ini += strlen($start);   
    $len = strpos($string,$end,$ini) - $ini;
    return substr($string,$ini,$len);
}

function getMMDVMConfig() {
	// loads MMDVM_Bridge.ini into array for further use
	$conf = array();
	if ($configs = @fopen(MMDVMINIPATH."/".MMDVMINIFILENAME, 'r')) {
		while ($config = fgets($configs)) {
			array_push($conf, trim ( $config, " \t\n\r\0\x0B"));
		}
		fclose($configs);
	}
	return $conf;
}

function getYSFGatewayConfig() {
	// loads YSFGateway.ini into array for further use
	$conf = array();
	if ($configs = @fopen(YSFGATEWAYINIPATH."/".YSFGATEWAYINIFILENAME, 'r')) {
		while ($config = fgets($configs)) {
			array_push($conf, trim ( $config, " \t\n\r\0\x0B"));
		}
		fclose($configs);
	}
	return $conf;
}

function getP25GatewayConfig() {
	// loads P25Gateway.ini into array for further use
	$conf = array();
	if ($configs = @fopen(P25GATEWAYINIPATH."/".P25GATEWAYINIFILENAME, 'r')) {
		while ($config = fgets($configs)) {
			array_push($conf, trim ( $config, " \t\n\r\0\x0B"));
		}
		fclose($configs);
	}
	return $conf;
}

function getNXDNGatewayConfig() {
	// loads NXDNGateway.ini into array for further use
	$conf = array();
	if ($configs = @fopen(NXDNGATEWAYINIPATH."/".NXDNGATEWAYINIFILENAME, 'r')) {
		while ($config = fgets($configs)) {
			array_push($conf, trim ( $config, " \t\n\r\0\x0B"));
		}
		fclose($configs);
	}
	return $conf;
}

function getDAPNETGatewayConfig() {
	// loads /etc/dapnetgateway into array for further use
	$conf = array();
	if ($configs = @fopen('/etc/dapnetgateway', 'r')) {
		while ($config = fgets($configs)) {
			array_push($conf, trim ( $config, " \t\n\r\0\x0B"));
		}
		fclose($configs);
	}
	return $conf;
}

function getConfigItem($section, $key, $configs) {
	// retrieves the corresponding config-entry within a [section]
	$sectionpos = array_search("[" . $section . "]", $configs) + 1;
	$len = count($configs);
	while(startsWith($configs[$sectionpos],$key."=") === false && $sectionpos <= ($len) ) {
		if (startsWith($configs[$sectionpos],"[")) {
			return null;
		}
		$sectionpos++;
	}

	return substr($configs[$sectionpos], strlen($key) + 1);
}

function getEnabled ($mode, $mmdvmconfigs) {
	// returns enabled/disabled-State of mode
	return getConfigItem($mode, "Enable", $mmdvmconfigs);
}

function showMode($mode, $mmdvmconfigs) {
	// shows if mode is enabled or not.
	if (getEnabled($mode, $mmdvmconfigs) == 1) {
		if ($mode == "D-Star Network") {
			if (isProcessRunning("ircddbgatewayd")) {
				echo "<td style=\"background:#12AD2A; color:#030; width:10%;\">";
			} else {
				echo "<td style=\"background:#b00; color:#500; width:10%;\">";
			}
		}
		elseif ($mode == "System Fusion Network") {
			if (isProcessRunning("YSFGateway")) {
				echo "<td style=\"background:#12AD2A; color:#030; width:10%;\">";
			} else {
				echo "<td style=\"background:#b00; color:#500; width:10%;\">";
			}
		}
		elseif ($mode == "P25 Network") {
			if (isProcessRunning("P25Gateway")) {
				echo "<td style=\"background:#12AD2A; color:#030; width:10%;\">";
			} else {
				echo "<td style=\"background:#b00; color:#500; width:10%;\">";
			}
		}
		elseif ($mode == "NXDN Network") {
			if (isProcessRunning("NXDNGateway")) {
				echo "<td style=\"background:#12AD2A; color:#030; width:10%;\">";
			} else {
				echo "<td style=\"background:#b00; color:#500; width:10%;\">";
			}
		}
		elseif ($mode == "DMR Network") {
			if (getConfigItem("DMR Network", "Address", $mmdvmconfigs) == '127.0.0.1') {
				if (isProcessRunning("DMRGateway")) {
					echo "<td style=\"background:#12AD2A; color:#030; width:10%;\">";
				} else {
					echo "<td style=\"background:#b00; color:#500; width:10%;\">";
				}
			}
			else {
				if (isProcessRunning("MMDVM_Bridge")) {
					echo "<td style=\"background:#12AD2A; color:#030; width:10%;\">";
				} else {
					echo "<td style=\"background:#b00; color:#500; width:10%;\">";
				}
			}
		}
		else {
			if ($mode == "D-Star" || $mode == "DMR" || $mode == "System Fusion" || $mode == "P25" || $mode == "NXDN" ) {
				if (isProcessRunning("MMDVM_Bridge")) {
					echo "<td style=\"background:#12AD2A; color:#030; width:10%;\">";
				} else {
					echo "<td style=\"background:#b00; color:#500; width:10%;\">";
				}
			}
		}
	}
	else {
		echo "<td style=\"background:#606060; color:#b0b0b0; width:10%;\">";
    }
    $mode = str_replace("System Fusion", "YSF", $mode);
    $mode = str_replace("Network", "Net", $mode);
    if (strpos($mode, 'YSF2') > -1) { $mode = str_replace(" Net", "", $mode); }
    if (strpos($mode, 'DMR2') > -1) { $mode = str_replace(" Net", "", $mode); }
    echo $mode."</td>\n";
}

function getMMDVMLog() {
	// Open Logfile and copy loglines into LogLines-Array()
	$logLines = array();
	$logLines1 = array();
	$logLines2 = array();
	if (file_exists(LOGPATH."/".MMDVMLOGPREFIX."-".gmdate("Y-m-d").".log")) {
		$logPath = LOGPATH."/".MMDVMLOGPREFIX."-".gmdate("Y-m-d").".log";
		$logLines1 = explode("\n", `egrep -h "Begin|frames|from|end|watchdog|lost" $logPath | sed '/\(CSBK\|overflow\|Downlink\)/d' | sed 's/I:/M:/g' | tail -250`);
	}
	$logLines1 = array_slice($logLines1, -250);
	if (sizeof($logLines1) < 250) {
		if (file_exists(LOGPATH."/".MMDVMLOGPREFIX."-".gmdate("Y-m-d", time() - 86340).".log")) {
			$logPath = LOGPATH."/".MMDVMLOGPREFIX."-".gmdate("Y-m-d", time() - 86340).".log";
			$logLines2 = explode("\n", `egrep -h "Begin|frames|from|end|watchdog|lost" $logPath | sed '/\(CSBK\|overflow\|Downlink\)/d' | sed 's/I:/M:/g' | tail -250`);
		}
	}
	$logLines2 = array_slice($logLines2, -250);
	$logLines = $logLines1 + $logLines2;
	$logLines = array_slice($logLines, -250);
//	print_r($logLines);
	return $logLines;
}

function getYSFGatewayLog() {
	// Open Logfile and copy loglines into LogLines-Array()
	$logLines = array();
	$logLines1 = array();
	$logLines2 = array();
	if (file_exists(LOGPATH."/".YSFGATEWAYLOGPREFIX."-".gmdate("Y-m-d").".log")) {
		$logPath1 = LOGPATH."/".YSFGATEWAYLOGPREFIX."-".gmdate("Y-m-d").".log";
		//$logLines1 = explode("\n", `egrep -h "repeater|Starting|Opening YSF|Disconnect|Connect|Automatic|Disconnecting|Reverting|Linked" $logPath1 | tail -250`);
		$logLines1 = preg_split('/\r\n|\r|\n/', `grep -E "onnection to|onnect to|ink|isconnect|Opening YSF network" $logPath1 | sed '/Linked to MMDVM/d' | sed '/Link successful to MMDVM/d' | sed '/*Link/d' | tail -1`);
	}
	$logLines1 = array_filter($logLines1);
	//$logLines1 = array_slice($logLines1, -250);
	//if (sizeof($logLines1) < 250) {
	if (sizeof($logLines1) == 0) {
		if (file_exists(LOGPATH."/".YSFGATEWAYLOGPREFIX."-".gmdate("Y-m-d", time() - 86340).".log")) {
			$logPath2 = LOGPATH."/".YSFGATEWAYLOGPREFIX."-".gmdate("Y-m-d", time() - 86340).".log";
			//$logLines2 = explode("\n", `egrep -h "repeater|Starting|Opening YSF|Disconnect|Connect|Automatic|Disconnecting|Reverting|Linked" $logPath2 | tail -250`);
			$logLines1 = preg_split('/\r\n|\r|\n/', `grep -E "onnection to|onnect to|ink|isconnect|Opening YSF network" $logPath2 | sed '/Linked to MMDVM/d' | sed '/Link successful to MMDVM/d' | sed '/*Link/d' | tail -1`);
		}
		$logLines2 = array_filter($logLines2);
	}
	//$logLines2 = array_slice($logLines2, -250);
	//$logLines = $logLines1 + $logLines2;
	//$logLines = array_slice($logLines, -250);
	//return $logLines;
	if (sizeof($logLines1) == 0) { $logLines = $logLines2; } else { $logLines = $logLines1; }
        return array_filter($logLines);
}

function getP25GatewayLog() {
        // Open Logfile and copy loglines into LogLines-Array()
        $logLines = array();
	$logLines1 = array();
	$logLines2 = array();
        if (file_exists(LOGPATH."/".P25GATEWAYLOGPREFIX."-".gmdate("Y-m-d").".log")) {
		$logPath1 = LOGPATH."/".P25GATEWAYLOGPREFIX."-".gmdate("Y-m-d").".log";
		$logLines1 = preg_split('/\r\n|\r|\n/', `egrep -h "ink|Starting" $logPath1 | cut -d" " -f2- | tail -1`);
        }
	$logLines1 = array_filter($logLines1);
        if (sizeof($logLines1) == 0) {
                if (file_exists(LOGPATH."/".P25GATEWAYLOGPREFIX."-".gmdate("Y-m-d", time() - 86340).".log")) {
                        $logPath2 = LOGPATH."/".P25GATEWAYLOGPREFIX."-".gmdate("Y-m-d", time() - 86340).".log";
			$logLines2 = preg_split('/\r\n|\r|\n/', `egrep -h "ink|Starting" $logPath2 | cut -d" " -f2- | tail -1`);
                }
		$logLines2 = array_filter($logLines2);
        }
	if (sizeof($logLines1) == 0) { $logLines = $logLines2; } else { $logLines = $logLines1; }
        return array_filter($logLines);
}

function getNXDNGatewayLog() {
        // Open Logfile and copy loglines into LogLines-Array()
        $logLines = array();
	$logLines1 = array();
	$logLines2 = array();
        if (file_exists(LOGPATH."/".NXDNGATEWAYLOGPREFIX."-".gmdate("Y-m-d").".log")) {
		$logPath1 = LOGPATH."/".NXDNGATEWAYLOGPREFIX."-".gmdate("Y-m-d").".log";
		$logLines1 = preg_split('/\r\n|\r|\n/', `egrep -h "ink|Starting" $logPath1 | cut -d" " -f2- | tail -1`);
        }
	$logLines1 = array_filter($logLines1);
        if (sizeof($logLines1) == 0) {
                if (file_exists(LOGPATH."/".NXDNGATEWAYLOGPREFIX."-".gmdate("Y-m-d", time() - 86340).".log")) {
			$logPath2 = LOGPATH."/".NXDNGATEWAYLOGPREFIX."-".gmdate("Y-m-d", time() - 86340).".log";
			$logLines2 = preg_split('/\r\n|\r|\n/', `egrep -h "ink|Starting" $logPath2 | cut -d" " -f2- | tail -1`);
                }
		$logLines2 = array_filter($logLines2);
        }
	if (sizeof($logLines1) == 0) { $logLines = $logLines2; } else { $logLines = $logLines1; }
        return array_filter($logLines);
}

function getDAPNETGatewayLog() {
        // Open Logfile and copy loglines into LogLines-Array()
        $logLines = array();
	$logLines1 = array();
	$logLines2 = array();
        if (file_exists("/var/log/mmdvm/DAPNETGateway-".gmdate("Y-m-d").".log")) {
		$logPath1 = "/var/log/mmdvm/DAPNETGateway-".gmdate("Y-m-d").".log";
		$logLines1 = preg_split('/\r\n|\r|\n/', `egrep -h "Sending message" $logPath1 | cut -d" " -f2- | tail -n 20 | tac`);
        }
	$logLines1 = array_filter($logLines1);
        if (sizeof($logLines1) == 0) {
                if (file_exists("/var/log/mmdvm/DAPNETGateway-".gmdate("Y-m-d", time() - 86340).".log")) {
			$logPath2 = "/var/log/mmdvm/DAPNETGateway-".gmdate("Y-m-d", time() - 86340).".log";
			$logLines2 = preg_split('/\r\n|\r|\n/', `egrep -h "Sending message" $logPath2 | cut -d" " -f2- | tail -n 20 | tac`);
                }
		$logLines2 = array_filter($logLines2);
        }
	$logLines = $logLines1 + $logLines2;
	$logLines = array_slice($logLines, -20);
	return array_filter($logLines);
}

// MMDVMHost
// 00000000001111111111222222222233333333334444444444555555555566666666667777777777888888888899999999990000000000111111111122
// 01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901
// M: 2000-00-00 00:00:00.000 D-Star, received network header from M1ABC   /ABCD to CQCQCQ   via REF000 A
// M: 2000-00-00 00:00:00.000 DMR Slot 2, received network voice header from M1ABC to TG 1
// M: 2000-00-00 00:00:00.000 DMR Slot 2, received RF voice header from M1ABC to 5000
// M: 2000-00-00 00:00:00.000 DMR Slot 2, received RF end of voice transmission, 1.8 seconds, BER: 3.9%
// M: 2000-00-00 00:00:00.000 DMR Slot 2, received network end of voice transmission from M1ABC to TG 2, 0.0 seconds, 0% packet loss, BER: 0.0%
// M: 2000-00-00 00:00:00.000 DMR Slot 2, RF voice transmission lost, 1.1 seconds, BER: 6.5%
// M: 2000-00-00 00:00:00.000 DMR Slot 2, received RF CSBK Preamble CSBK (1 to follow) from M1ABC to TG 1
// M: 2000-00-00 00:00:00.000 DMR Slot 2, received network Data Preamble VSBK (11 to follow) from 123456 to TG 123456
// M: 2000-00-00 00:00:00.000 DMR Talker Alias (Data Format 1, Received 24/24 char): 'Hide the bottle from Ont'
// M: 2000-00-00 00:00:00.000 0000:  07 00 20 4F 6E 74 00 00 00                         *.. Ont...*
// M: 2000-00-00 00:00:00.000 DMR Slot 2, Embedded Talker Alias Block 3
// M: 2000-00-00 00:00:00.000 P25, received RF transmission from M1ABC to TG 10200
// M: 2000-00-00 00:00:00.000 Debug: P25RX: pos/neg/centre/threshold 106 -105 0 106
// M: 2000-00-00 00:00:00.000 Debug: P25RX: sync found in Ldu pos/centre/threshold 3986 9 104
// M: 2000-00-00 00:00:00.000 Debug: P25RX: pos/neg/centre/threshold 267 -222 22 245
// M: 2000-00-00 00:00:00.000 Debug: P25RX: sync found in Ldu pos/centre/threshold 3986 10 112
// M: 2000-00-00 00:00:00.000 P25, received RF end of transmission, 0.4 seconds, BER: 0.0%
// M: 2000-00-00 00:00:00.000 P25, received network transmission from 10999 to TG 10200
// M: 2000-00-00 00:00:00.000 P25, network end of transmission, 1.8 seconds, 0% packet loss
// M: 2000-00-00 00:00:00.000 YSF, received RF data from MW0MWZ     to ALL
// M: 2000-00-00 00:00:00.000 YSF, received RF end of transmission, 5.1 seconds, BER: 3.8%
// M: 2000-00-00 00:00:00.000 YSF, received network data from M1ABC      to ALL        at MB6IBK
// M: 2000-00-00 00:00:00.000 YSF, network watchdog has expired, 5.0 seconds, 0% packet loss, BER: 0.0%
// M: 2000-00-00 00:00:00.000 NXDN, received RF transmission from MW0MWZ to TG 65000
// M: 2000-00-00 00:00:00.000 Debug: NXDNRX: pos/neg/centre/threshold 106 -105 0 106
// M: 2000-00-00 00:00:00.000 Debug: NXDNRX: sync found in Ldu pos/centre/threshold 3986 9 104
// M: 2000-00-00 00:00:00.000 Debug: NXDNRX: pos/neg/centre/threshold 267 -222 22 245
// M: 2000-00-00 00:00:00.000 Debug: NXDNRX: sync found in Ldu pos/centre/threshold 3986 10 112
// M: 2000-00-00 00:00:00.000 NXDN, received RF end of transmission, 0.4 seconds, BER: 0.0%
// M: 2000-00-00 00:00:00.000 NXDN, received network transmission from 10999 to TG 65000
// M: 2000-00-00 00:00:00.000 NXDN, network end of transmission, 1.8 seconds, 0% packet loss
// M: 2000-00-00 00:00:00.000 POCSAG, transmitted 1 frame(s) of data from 1 message(s)
/
// MMDVM_Bridge loglines
//
// M: 2020-09-23 06:08:14.717 DMR, TX state = ON
// I: 2020-09-23 04:56:54.073 DMR, Begin TX: src=2602001 rpt=260201498 dst=9 slot=2 cc=1 metadata=SP2ABC
// M: 2020-09-23 04:57:20.031 DMR, TX state = OFF, DMR frame count was 57 frames
//
// M: 2020-09-23 06:47:51.710 P25, TX state = ON
// I: 2020-09-23 04:47:51.712 P25, Begin TX: src=2602001 rpt=260200198 dst=9 slot=2 cc=1
// M: 2020-09-23 04:47:55.447 P25, TX state = OFF
//
// M: 2020-09-23 06:44:41.587 YSF, TX state = ON
// I: 2020-09-23 06:44:41.587 YSF, Begin TX: src=2602001 rpt=260200198 dst=10200 slot=2 cc=1 metadata=SP2ABC
// I: 2020-09-23 06:44:41.777 YSF, Narrow transmit (72 bit)
// M: 2020-09-23 06:44:47.407 YSF, TX state = OFF
//
// M: 2020-09-23 05:34:44.836 NXDN, TX state = ON
// I: 2020-09-23 05:34:44.836 NXDN, dmrToNxdn by call: SP2ABC
// I: 2020-09-23 05:34:44.836 NXDN, Begin TX: src=2602001 rpt=260200198 dst=9 slot=2 cc=1 metadata=SP2ABC
// I: 2020-09-23 05:34:44.836 NXDN, (2602074, 9) -> (4280, 9)
// M: 2020-09-23 05:34:44.839 NXDN, received RF header from SP2ABC to TG 9


function getHeardList($logLines) {
	//array_multisort($logLines,SORT_DESC);
	$heardList = array();
	$ts1duration	= "";
	$ts1loss	= "";
	$ts1ber		= "";
	$ts1rssi	= "";
	$ts2duration	= "";
	$ts2loss	= "";
	$ts2ber		= "";
	$ts2rssi	= "";
	$dstarduration	= "";
	$dstarloss	= "";
	$dstarber	= "";
	$dstarrssi	= "";
	$ysfduration	= "";
        $ysfloss	= "";
        $ysfber		= "";
	$ysfrssi	= "";
	$p25duration	= "";
        $p25loss	= "";
        $p25ber		= "";
	$p25rssi	= "";
	$nxdnduration	= "";
        $nxdnloss	= "";
        $nxdnber	= "";
	$nxdnrssi	= "";
	$pocsagduration	= "";
	foreach ($logLines as $logLine) {
		$duration	= "";
		$loss		= "";
		$ber		= "";
		$rssi		= "";
		//removing invalid lines
		if(strpos($logLine,"BS_Dwn_Act")) {
			continue;
		} else if(strpos($logLine,"invalid access")) {
			continue;
		} else if(strpos($logLine,"GPS Position")) {
			continue;
		} else if(strpos($logLine,"received RF header for wrong repeater")) {
			continue;
		} else if(strpos($logLine,"unable to decode the network CSBK")) {
			continue;
		} else if(strpos($logLine,"overflow in the DMR slot RF queue")) {
			continue;
		} else if(strpos($logLine,"non repeater RF header received")) {
			continue;
		} else if(strpos($logLine,"Embedded Talker Alias")) {
                        continue;
		} else if(strpos($logLine,"DMR Talker Alias")) {
			continue;
		} else if(strpos($logLine,"CSBK Preamble")) {
                        continue;
		} else if(strpos($logLine,"Preamble CSBK")) {
                        continue;
		}
//		print($logLine);
		if(strpos($logLine,"DMR frame count was") || strpos($logLine, "end of") || strpos($logLine, "watchdog has expired") || strpos($logLine, "ended RF data") || strpos($logLine, "ended network") || strpos($logLine, "RF user has timed out") || strpos($logLine, "transmission lost") || strpos($logLine, "POCSAG")) {

		if (strpos($logLine,"DMR frame count was")){
			$duration = substr($logLine, strpos($logLine,"was")+4, strpos($logLine,"frames") - strpos($logLine,"was")-5)*0.059;
			$ts2duration=number_format($duration, 1, '.', '.');
			} 
			$lineTokens = explode(", ",$logLine);
			if (array_key_exists(2,$lineTokens)) {
				$duration = strtok($lineTokens[2], " ");
			}
			if (array_key_exists(3,$lineTokens)) {
				$loss = $lineTokens[3];
			}
			if (strpos($logLine,"RF user has timed out")) {
				$duration = "TOut";
				$ber = "??%";
			}

			// if RF-Packet with no BER reported (e.g. YSF Wires-X commands) then RSSI is in LOSS position
			if (startsWith($loss,"RSSI")) {
				$lineTokens[4] = $loss; //move RSSI to the position expected on code below
				$loss = 'BER: ??%';
			}

			// if RF-Packet, no LOSS would be reported, so BER is in LOSS position
			if (startsWith($loss,"BER")) {
				$ber = substr($loss, 5);
				$loss = "0%";
				if (array_key_exists(4,$lineTokens) && startsWith($lineTokens[4],"RSSI")) {
					$rssi = substr($lineTokens[4], 6);
					$rssi = substr($rssi, strrpos($rssi,'/')+1); //average only
					$relint = intval($rssi) + 93;
					$signal = round(($relint/6)+9, 0);
					if ($signal < 0) $signal = 0;
					if ($signal > 9) $signal = 9;
					if ($relint > 0) {
						$rssi = "S{$signal}+{$relint}dB";
					} else {
						$rssi = "S{$signal}";
					}
				}
			} else {
				$loss = strtok($loss, " ");
				if (array_key_exists(4,$lineTokens)) {
					$ber = substr($lineTokens[4], 5);
				}
			}

			if (strpos($logLine,"ended RF data") || strpos($logLine,"ended network")) {
				switch (substr($logLine, 27, strpos($logLine,",") - 27)) {
					case "DMR Slot 1":
						$ts1duration = "SMS";
						break;
					case "DMR Slot 2":
						$ts2duration = "SMS";
						break;
				}
			} else {
				switch (substr($logLine, 27, strpos($logLine,",") - 27)) {
					case "D-Star":
						$dstarduration	= $duration;
						$dstarloss	= $loss;
						$dstarber	= $ber;
						$dstarrssi	= $rssi;
						break;
					case "DMR Slot 1":
						$ts1duration	= $duration;
						$ts1loss	= $loss;
						$ts1ber		= $ber;
						$ts1rssi	= $rssi;
						break;
					case "DMR Slot 2":
						$ts2duration	= $duration;
						$ts2loss	= $loss;
						$ts2ber		= $ber;
						$ts2rssi	= $rssi;
						break;
					case "YSF":
						$ysfduration	= $duration;
						$ysfloss	= $loss;
						$ysfber		= $ber;
						$ysfrssi	= $rssi;
						break;
					case "P25":
						$p25duration	= $duration;
						$p25loss	= $loss;
						$p25ber		= $ber;
						$p25rssi	= $rssi;
						break;
					case "NXDN":
						$nxdnduration	= $duration;
						$nxdnloss	= $loss;
						$nxdnber	= $ber;
						$nxdnrssi	= $rssi;
						break;
					case "POCSAG":
						$pocsagduration	= "";
						break;						
				}
			}
		}

		if (strpos($logLine,"Begin TX")) {
		$mode = "DMR Slot 2";		    
		$timestamp = substr($logLine, 3, 19);
		$callsign2 = substr($logLine, strpos($logLine,"metadata=")+9);				
		$callsign = $callsign2;
		$callsign = trim($callsign);
		$id ="";
		$target = "TG ".substr($logLine,strpos($logLine,"dst=")+4,strpos($logLine,"slot=") - strpos($logLine,"dst=")-4);
		$source = "DVSM/UC";
		$ts2loss = "0%";
                $ts2ber	 = "0.0%";
		}
		if (strpos($logLine,"from")) {
		$timestamp = substr($logLine, 3, 19);
		$mode = substr($logLine, 27, strpos($logLine,",") - 27);
		$callsign2 = substr($logLine, strpos($logLine,"from") + 5, strpos($logLine,"to") - strpos($logLine,"from") - 6);
		$callsign = $callsign2;
		if (strpos($callsign2,"/") > 0) {
			$callsign = substr($callsign2, 0, strpos($callsign2,"/"));
		}
		$callsign = trim($callsign);

		$id ="";
		if ($mode == "D-Star") {
			$id = substr($callsign2, strpos($callsign2,"/") + 1);
		}

		$target = trim(substr($logLine, strpos($logLine, "to") + 3));
		// Handle more verbose logging from MMDVMHost
                if (strpos($target,",") !== 'false') { $target = explode(",", $target)[0]; }
			
		$source = "Net";
			
		}
		switch ($mode) {
			case "D-Star":
				$duration	= $dstarduration;
				$loss		= $dstarloss;
				$ber		= $dstarber;
				$rssi		= $dstarrssi;
				break;
			case "DMR Slot 1":
				$duration	= $ts1duration;
				$loss		= $ts1loss;
				$ber		= $ts1ber;
				$rssi		= $ts1rssi;
				break;
			case "DMR Slot 2":
				$duration	= $ts2duration;
				$loss		= $ts2loss;
				$ber		= $ts2ber;
				$rssi		= $ts2rssi;
				break;
			case "YSF":
                		$duration	= $ysfduration;
                		$loss		= $ysfloss;
                		$ber		= $ysfber;
				$rssi		= $ysfrssi;
				$target		= preg_replace('!\s+!', ' ', $target);
                		break;
			case "P25":
				if ($source == "Net" && $target == "TG 10") {$callsign = "PARROT";}
				if ($source == "Net" && $callsign == "10999") {$callsign = "MMDVM";}
                		$duration	= $p25duration;
                		$loss		= strlen($p25loss) ? $p25loss : "---";
                		$ber		= strlen($p25ber) ? $p25ber : "---";
				$rssi		= $p25rssi;
                		break;
			case "NXDN":
				if ($source == "Net" && $target == "TG 10") {$callsign = "PARROT";}
                		$duration	= $nxdnduration;
                		$loss		= strlen($nxdnloss) ? $nxdnloss : "---";
                		$ber		= strlen($nxdnber) ? $nxdnber : "---";
				$rssi		= $nxdnrssi;
                		break;
			case "POCSAG":
				$callsign	= "DAPNET";
				$target		= "DAPNET User";
				$duration	= "0.0";
				$loss		= "0%";
                		$ber		= "0.0%";
				break;
		}

		// Callsign or ID should be less than 11 chars long, otherwise it could be errorneous
		if ( strlen($callsign) < 11 ) {
			array_push($heardList, array($timestamp, $mode, $callsign, $id, $target, $source, $duration, $loss, $ber, $rssi));
			$duration = "";
			$loss ="";
			$ber = "";
			$rssi = "";
		}
	}
	return $heardList;
}

function getLastHeard($logLines) {
	//returns last heard list from log
	$lastHeard = array();
	$heardCalls = array();
	$heardList = getHeardList($logLines);
	$counter = 0;
	foreach ($heardList as $listElem) {
		if ( ($listElem[1] == "D-Star") || ($listElem[1] == "YSF") || ($listElem[1] == "P25") || ($listElem[1] == "NXDN") || ($listElem[1] == "POCSAG") || (startsWith($listElem[1], "DMR")) ) {
			$callUuid = $listElem[2]."#".$listElem[1].$listElem[3].$listElem[5];
			if(!(array_search($callUuid, $heardCalls) > -1)) {
				array_push($heardCalls, $callUuid);
				array_push($lastHeard, $listElem);
				$counter++;
			}/*
			if ($counter == LHLINES) {
				return $lastHeard;
			}*/
		}
	}
	return $lastHeard;
}

function getActualMode($metaLastHeard, $mmdvmconfigs) {
	// returns mode of repeater actual working in
        $utc_tz =  new DateTimeZone('UTC');
        $local_tz = new DateTimeZone(date_default_timezone_get ());
        $listElem = $metaLastHeard[0];
        $timestamp = new DateTime($listElem[0], $utc_tz);
        $timestamp->setTimeZone($local_tz); 
        $mode = $listElem[1];
	if (startsWith($mode, "DMR")) {
		$mode = "DMR";
	}

	$now =  new DateTime();
	$hangtime = getConfigItem("General", "ModeHang", $mmdvmconfigs);

	if ($hangtime != "") {
		$timestamp->add(new DateInterval('PT' . $hangtime . 'S'));
	} else {
		$source = $listElem[5];
		if ($source == "RF" && $mode === "D-Star") {
			$hangtime = getConfigItem("D-Star", "ModeHang", $mmdvmconfigs);
		}
		else if ($source == "Net" && $mode === "D-Star") {
			$hangtime = getConfigItem("D-Star Network", "ModeHang", $mmdvmconfigs);
		}
		else if ($source == "RF" && $mode === "DMR") {
			$hangtime = getConfigItem("DMR", "ModeHang", $mmdvmconfigs);
		}
		else if ($source == "Net" && $mode === "DMR") {
			$hangtime = getConfigItem("DMR Network", "ModeHang", $mmdvmconfigs);
		}
		else if ($source == "RF" && $mode === "YSF") {
			$hangtime = getConfigItem("System Fusion", "ModeHang", $mmdvmconfigs);
		}
		else if ($source == "Net" && $mode === "YSF") {
			$hangtime = getConfigItem("System Fusion Network", "ModeHang", $mmdvmconfigs);
		}
		else if ($source == "RF" && $mode === "P25") {
			$hangtime = getConfigItem("P25", "ModeHang", $mmdvmconfigs);
		}
		else if ($source == "Net" && $mode === "P25") {
			$hangtime = getConfigItem("P25 Network", "ModeHang", $mmdvmconfigs);
		}
		else if ($source == "RF" && $mode === "NXDN") {
			$hangtime = getConfigItem("NXDN", "ModeHang", $mmdvmconfigs);
		}
		else if ($source == "Net" && $mode === "NXDN") {
			$hangtime = getConfigItem("NXDN Network", "ModeHang", $mmdvmconfigs);
		}
		else if ($source == "Net" && $mode === "POCSAG") {
			$hangtime = getConfigItem("POCSAG Network", "ModeHang", $mmdvmconfigs);
		}
		else {
			$hangtime = getConfigItem("General", "RFModeHang", $mmdvmconfigs);
		}
		$timestamp->add(new DateInterval('PT' . $hangtime . 'S'));
	}
	if ($listElem[6] != null) { //if terminated, hangtime counts after end of transmission
		$timestamp->add(new DateInterval('PT' . ceil($listElem[6]) . 'S'));
	} else { //if not terminated, always return mode
		return $mode;
	}
	if ($now->format('U') > $timestamp->format('U')) {
		return "idle";
	} else {
		return $mode;
	}
}

function getDSTARLinks() {
	// returns link-states of all D-Star-modules
	if (filesize(LINKLOGPATH."/Links.log") == 0) {
		return "<span style=\"color:#b0b0b0;\"><b>Not Linked</b></span>";
	}
	if ($linkLog = fopen(LINKLOGPATH."/Links.log",'r')) {
		while ($linkLine = fgets($linkLog)) {
			$linkDate	= "&nbsp;";
			$protocol	= "&nbsp;";
			$linkType	= "&nbsp;";
			$linkSource	= "&nbsp;";
			$linkDest	= "&nbsp;";
			$linkDir	= "&nbsp;";
// Reflector-Link, sample:
// 2011-09-22 02:15:06: DExtra link - Type: Repeater Rptr: DB0LJ	B Refl: XRF023 A Dir: Outgoing
// 2012-04-03 08:40:07: DPlus link - Type: Dongle Rptr: DB0ERK B Refl: REF006 D Dir: Outgoing
// 2012-04-03 08:40:07: DCS link - Type: Repeater Rptr: DB0ERK C Refl: DCS001 C Dir: Outgoing
			if(preg_match_all('/^(.{19}).*(D[A-Za-z]*).*Type: ([A-Za-z]*).*Rptr: (.{8}).*Refl: (.{8}).*Dir: (.{8})/',$linkLine,$linx) > 0){
				$linkDate	= $linx[1][0];
				$protocol	= $linx[2][0];
				$linkType	= $linx[3][0];
				$linkSource	= $linx[4][0];
				$linkDest	= $linx[5][0];
				$linkDir	= $linx[6][0];
			}
// CCS-Link, sample:
// 2013-03-30 23:21:53: CCS link - Rptr: PE1AGO C Remote: PE1KZU	Dir: Incoming
			if(preg_match_all('/^(.{19}).*(CC[A-Za-z]*).*Rptr: (.{8}).*Remote: (.{8}).*Dir: (.{8})/',$linkLine,$linx) > 0){
				$linkDate	= $linx[1][0];
				$protocol	= $linx[2][0];
				$linkType	= $linx[2][0];
				$linkSource	= $linx[3][0];
				$linkDest	= $linx[4][0];
				$linkDir	= $linx[5][0];
			}
// Dongle-Link, sample: 
// 2011-09-24 07:26:59: DPlus link - Type: Dongle User: DC1PIA	Dir: Incoming
// 2012-03-14 21:32:18: DPlus link - Type: Dongle User: DC1PIA Dir: Incoming
			if(preg_match_all('/^(.{19}).*(D[A-Za-z]*).*Type: ([A-Za-z]*).*User: (.{6,8}).*Dir: (.*)$/',$linkLine,$linx) > 0){
				$linkDate	= $linx[1][0];
				$protocol	= $linx[2][0];
				$linkType	= $linx[3][0];
				$linkSource	= "&nbsp;";
				$linkDest	= $linx[4][0];
				$linkDir	= $linx[5][0];
			}
			$out = "Linked to <span style=\"color:#b5651d;font-weight:bold;\">" . $linkDest . "</span><br />\n(<span style=\"color:green;\"><b>" . $protocol . " " . $linkDir . "</b></span>)";
		}
	}
	fclose($linkLog);
	return $out;
}

function getActualLink($logLines, $mode) {
	// returns actual link state of specific mode
	//M: 2016-05-02 07:04:10.504 D-Star link status set to "Verlinkt zu DCS002 S"
	//M: 2016-04-03 16:16:18.638 DMR Slot 2, received network voice header from 4000 to 2625094
	//M: 2016-04-03 19:30:03.099 DMR Slot 2, received network voice header from 4020 to 2625094
	//M: 2017-09-03 08:10:42.862 DMR Slot 2, received network data header from M6JQD to TG 9, 5 blocks
	switch ($mode) {
    case "D-Star":
    	if (isProcessRunning(IRCDDBGATEWAY)) {
			return getDSTARLinks();
    	} else {
    		return "<span style=\"color:#b0b0b0;\"><b>No D-Star Network</b></span>";
    	}
        break;
			
	case "DMR Slot 1":
	case "DMR Slot 2":
	    //M: 2016-04-03 16:16:18.638 DMR Slot 2, received network voice header from 4000 to 2625094
	    //M: 2020-01-22 01:54:50.780 DMR Slot 2, received network voice header from 4000 to TG 9
	    //M: 2016-04-03 19:30:03.099 DMR Slot 2, received network voice header from 4020 to 2625094
	    //M: 2017-09-03 08:10:42.862 DMR Slot 2, received network data header from M6JQD to TG 9, 5 blocks
            foreach ($logLines as $logLine) {
        	if(strpos($logLine,"unable to decode the network CSBK")) {
		    continue;
		}
		else if(substr($logLine, 27, strpos($logLine,",") - 27) == $mode) {
		    $to = "";
		    $from = "";
		    if (strpos($logLine, "from") != FALSE) {
			$from = trim(get_string_between($logLine, "from", "to"));
		    }
		    if (strpos($logLine,"to")) {
			$to = trim(substr($logLine, strpos($logLine,"to") + 3));
		    }
		    if ($from !== "") {
			if ($from === "4000") {
			    return "No TG";
			}
		    }
		    if ($to !== "") {
			if (substr($to, 0, 3) !== 'TG ') {
			    continue;
			}
			if ($to === "TG 4000") {
			    return "No TG";
			}
			if (strpos($to, ',') !== false) {
			    $to = substr($to, 0, strpos($to, ','));
			}
			return $to;
		    }
		}
	    }
	    return "No TG";
            break;

    case "YSF":
	// 00000000001111111111222222222233333333334444444444555555555566666666667777777777888888888899999999990000000000111111111122
	// 01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901
	// M: 0000-00-00 00:00:00.000 Connect to 62829 has been requested by M1ABC
	// M: 0000-00-00 00:00:00.000 Automatic connection to 62829
	// New YSFGateway Format
	// M: 0000-00-00 00:00:00.000 Opening YSF network connection
	// M: 0000-00-00 00:00:00.000 Automatic (re-)connection to 16710 - "GB SOUTH WEST   "
	// M: 0000-00-00 00:00:00.000 Automatic (re-)connection to FCS00290
	// M: 0000-00-00 00:00:00.000 Linked to GB SOUTH WEST   
	// M: 0000-00-00 00:00:00.000 Linked to FCS002-90
	// M: 0000-00-00 00:00:00.000 Disconnect via DTMF has been requested by M1ABC
	// M: 0000-00-00 00:00:00.000 Connect to 00003 - "YSF2NXDN        " has been requested by M1ABC
	// M: 0000-00-00 00:00:00.000 Link has failed, polls lost

         if (isProcessRunning("YSFGateway")) {
            $to = "";
            foreach($logLines as $logLine) {
               if ( (strpos($logLine,"Linked to")) && (!strpos($logLine,"Linked to MMDVM")) ) {
                  $to = trim(substr($logLine, 37, 16));
		  if (substr($to, 0, 3) === "FCS") { $to = str_replace(' ', '', str_replace('-', '', $to)); }
               }
               if (strpos($logLine,"Automatic (re-)connection to")) {
		  if (strpos($logLine,"Automatic (re-)connection to FCS")) {
			$to = substr($logLine, 56, 8);
		  }
		  else {
                  	$to = substr($logLine, 56, 5);
		  }
               }
               if (strpos($logLine,"Connect to")) {
                  $to = substr($logLine, 38, 5);
               }
               if (strpos($logLine,"Automatic connection to")) {
                  $to = substr($logLine, 51, 5);
               }
               if (strpos($logLine,"Disconnect via DTMF")) {
                  $to = "Not Linked";
               }
               if (strpos($logLine,"Opening YSF network connection")) {
                  $to = "Not Linked";
               }
	       if (strpos($logLine,"Link has failed")) {
                  $to = "Not Linked";
               }
               if (strpos($logLine,"DISCONNECT Reply")) {
                  $to = "Not Linked";
               }
               if ($to !== "") {
                  return $to;
               }
            }
            return "Not Linked";
         } else {
            return "No YSF Network";
         }
         break;

     case "NXDN":
        // 00000000001111111111222222222233333333334444444444555555555566666666667777777777888888888899999999990000000000111111111122
        // 01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901
        // 2000-01-01 00:00:00.000 Linked at startup to reflector 65000
        // 2000-01-01 00:00:00.000 Unlinked from reflector 10100 by M1ABC
        // 2000-01-01 00:00:00.000 Linked to reflector 10200 by M1ABC
        // 2000-01-01 00:00:00.000 No response from 10200, unlinking
        if (isProcessRunning("NXDNGateway")) {
            foreach($logLines as $logLine) {
               $to = "";
               if (strpos($logLine,"Linked to")) {
                  $to = preg_replace('/[^0-9]/', '', substr($logLine, 44, 5));
                  $to = preg_replace('/[^0-9]/', '', $to);
                  return "Linked to <span style=\"color:#b5651d;font-weight:bold;\">TG".$to."</span>";
               }
               if (strpos($logLine,"Linked at start")) {
                  $to = preg_replace('/[^0-9]/', '', substr($logLine, 55, 5));
                  $to = preg_replace('/[^0-9]/', '', $to);
                  return "Linked to <span style=\"color:#b5651d;font-weight:bold;\">TG".$to."</span>";
               }
	       if (strpos($logLine,"Starting NXDNGateway")) {
                  return "<span style=\"color:#b0b0b0;\"><b>Not Linked</b></span>";
               }
               if (strpos($logLine,"unlinking")) {
                  return "<span style=\"color:#b0b0b0;\"><b>Not Linked</b></span>";
               }
               if (strpos($logLine,"Unlinked from")) {
                  return "<span style=\"color:#b0b0b0;\"><b>Not Linked</b></span>";
               }
            }
        } else {
            return "<span style=\"color:#b0b0b0;\"><b>No NXDN Network</b></span>";
        }
        break;

    case "P25":
	// 00000000001111111111222222222233333333334444444444555555555566666666667777777777888888888899999999990000000000111111111122
	// 01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901
	// 2000-01-01 00:00:00.000 Linked at startup to reflector 10100
	// 2000-01-01 00:00:00.000 Unlinked from reflector 10100 by M1ABC
	// 2000-01-01 00:00:00.000 Linked to reflector 10200 by M1ABC
	// 2000-01-01 00:00:00.000 No response from 10200, unlinking
	if (isProcessRunning("P25Gateway")) {
	    foreach($logLines as $logLine) {
               $to = "";
               if (strpos($logLine,"Linked to")) {
		  $to = preg_replace('/[^0-9]/', '', substr($logLine, 44, 5));
		  $to = preg_replace('/[^0-9]/', '', $to);
		  return "Linked to <span style=\"color:#b5651d;font-weight:bold;\">TG".$to."</span>";
               }
               if (strpos($logLine,"Linked at startup to")) {
		  $to = preg_replace('/[^0-9]/', '', substr($logLine, 55, 5));
		  $to = preg_replace('/[^0-9]/', '', $to);
		  return "Linked to <span style=\"color:#b5651d;font-weight:bold;\">TG".$to."</span>";
               }
	       if (strpos($logLine,"Starting P25Gateway")) {
                  return "<span style=\"color:#b0b0b0;\"><b>Not Linked</b></span>";
               }
	       if (strpos($logLine,"unlinking")) {
                  return "<span style=\"color:#b0b0b0;\"><b>Not Linked</b></span>";
               }
               if (strpos($logLine,"Unlinked")) {
                  return "<span style=\"color:#b0b0b0;\"><b>Not Linked</b></span>";
               }
	    }
	} else {
            return "<span style=\"color:#b0b0b0;\"><b>No P25 Network</b></span>";
        }
	break;
	}
	return "<span style=\"color:#b0b0b0;\"><b>Service Not Started</b></span>";
}

function getActualReflector($logLines, $mode) {
	// 00000000001111111111222222222233333333334444444444555555555566666666667777777777888888888899999999990000000000111111111122
	// 01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901
	// M: 2016-05-02 07:04:10.504 D-Star link status set to "Verlinkt zu DCS002 S"
	// M: 2016-04-03 16:16:18.638 DMR Slot 2, received network voice header from 4000 to 2625094
	// M: 2016-04-03 19:30:03.099 DMR Slot 2, received network voice header from 4020 to 2625094
	foreach ($logLines as $logLine) {
		if (substr($logLine, 27, strpos($logLine,",") - 27) == $mode) {
			$from = substr($logLine, strpos($logLine,"from") + 5, strpos($logLine,"to") - strpos($logLine,"from") - 6);
			if (strlen($from) == 4 && startsWith($from,"4")) {
				if ($from == "4000") {
					return "No Ref";
				} else {
					return "Ref ".$from;
				}
			}
		}
	}
	return "No Ref";
}

// Not used - to be removed
//function getActiveYSFReflectors($logLines) {
// 00000000001111111111222222222233333333334444444444555555555566666666667777777777888888888899999999990000000000111111111122
// 01234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901
// D: 2016-06-11 19:09:31.371 Have reflector status reply from 89164/FUSIONBE2       /FusionBelgium /002
//	$reflectors = Array();
//	$reflectorlist = Array();
//	foreach ($logLines as $logLine) {
//		if (strpos($logLine, "Have reflector status reply from")) {
//			$timestamp = substr($logLine, 3, 19);
//			$timestamp2 = new DateTime($timestamp);
//			$now =  new DateTime();
//			$timestamp2->add(new DateInterval('PT2H'));
//			if ($now->format('U') <= $timestamp2->format('U')) {
//				$str = substr($logLine, 60);
//				$id = strtok($str, "/");
//				$name = strtok("/");
//				$description = strtok("/");
//				$concount = strtok("/");
//				if(!(array_search($name, $reflectors) > -1)) {
//					array_push($reflectors,$name);
//					array_push($reflectorlist, array($name, $description, $id, $concount, $timestamp));
//				}
//			}
//		}
//	}
//	array_multisort($reflectorlist);
//	return $reflectorlist;
//}

// Not used - to be removed
//function getName($callsign) {
//	$callsign = trim($callsign);
//	if (strpos($callsign,"-")) {
//		$callsign = substr($callsign,0,strpos($callsign,"-"));
//	}
//	exec("grep ".$callsign." ".DMRIDDATPATH, $output);
//	$delimiter =" ";
//	if (strpos($output[0],"\t")) {
//	$delimiter = "\t";
//	}
//	$name = substr($output[0], strpos($output[0],$delimiter)+1);
//	$name = substr($name, strpos($name,$delimiter)+1);
//	return $name;
//}

//Some basic inits
$mmdvmconfigs = getMMDVMConfig();
if (!in_array($_SERVER["PHP_SELF"],array('/include/bm_links.php','/include/bm_manager.php'),true)) {
	$logLinesMMDVM = getMMDVMLog();
//	print_r($logLinesMMDVM);
	$reverseLogLinesMMDVM = $logLinesMMDVM;
	array_multisort($reverseLogLinesMMDVM,SORT_DESC);
//	print_r($reverseLogLinesMMDVM);
	$lastHeard = getLastHeard($reverseLogLinesMMDVM);

	// Only need these in repeaterinfo.php
	if (strpos($_SERVER["PHP_SELF"], 'mode.php') !== false || strpos($_SERVER["PHP_SELF"], 'index.php') !== false) {
		//$YSFGatewayconfigs = getYSFGatewayConfig();
		$logLinesYSFGateway = getYSFGatewayLog();
		$reverseLogLinesYSFGateway = $logLinesYSFGateway;
		array_multisort($reverseLogLinesYSFGateway,SORT_DESC);
		//$P25Gatewayconfigs = getP25GatewayConfig();
		$logLinesP25Gateway = getP25GatewayLog();
		//$reverseLogLinesP25Gateway = array_reverse(getP25GatewayLog());
		//$NXDNGatewayconfigs = getNXDNGatewayConfig();
		$logLinesNXDNGateway = getNXDNGatewayLog();
		//$reverseLogLinesNXDNGateway = array_reverse(getNXDNGatewayLog());
	}
	// Only need these in index.php
	if (strpos($_SERVER["PHP_SELF"], 'index.php') !== false || strpos($_SERVER["PHP_SELF"], 'pages.php') !== false) {
		$logLinesDAPNETGateway = getDAPNETGatewayLog();
	}
}
function getABInfo() {
	$json = file_get_contents('/var/www/html/ABInfo.json');
	$json_data = json_decode($json,true);
	return $json_data;
}

?>
