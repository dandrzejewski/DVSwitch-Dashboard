<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/include/config.php';          
include_once $_SERVER['DOCUMENT_ROOT'].'/include/tools.php';       
include_once $_SERVER['DOCUMENT_ROOT'].'/include/functions.php';    
$localTXList = $lastHeard;
?>
<div>
<span style="font-weight: bold;font-size:13px;">DVS Mobile / pyUC Activity</span>
<fieldset style="box-shadow:0 0 10px #999;background-color:#e8e8e8e8; width:660px;margin-top:8px;margin-left:0px;margin-right:0px;font-size:12px;border-top-left-radius: 10px; border-top-right-radius: 10px;border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
  <table>
    <tr>
      <th>Time (<?php echo date('T')?>)</th>
      <th>Mode</th>
      <th>Callsign</th>
      <th>Target</th>
      <th>Src</th>
      <th>Dur(s)</th>
      <th>BER</th>
    </tr>
<?php
//setlocale(LC_TIME, 'pl_PL.UTF-8');
$counter = 0;
$i = 0;
for ($i = 0; $i < count($localTXList); $i++) {
		$listElem = $localTXList[$i];
		if ($listElem[5] == "DVSM/UC" && ($listElem[1] == "D-Star" || startsWith($listElem[1], "DMR") || $listElem[1] == "YSF" || $listElem[1]== "P25" || $listElem[1]== "NXDN")) {
			if ($counter <= 19) { //last 20 calls
				$utc_time = $listElem[0];
                        	$utc_tz =  new DateTimeZone('UTC');
                        	$local_tz = new DateTimeZone(date_default_timezone_get ());
                        	$dt = new DateTime($utc_time, $utc_tz);
                        	$dt->setTimeZone($local_tz);
                                $local_time = strftime('%H:%M:%S %b %d', $dt->getTimestamp());
//                        	$local_time = $dt->format('H:i:s M jS');

			echo"<tr>";
			echo"<td align=\"left\">&nbsp;$local_time</td>";
			echo"<td align=\"left\">&nbsp;$listElem[1]</td>";
			if (is_numeric($listElem[2])) {
				echo "<td align=\"left\">$listElem[2]</td>";
			} else {
				if ($listElem[3] && $listElem[3] != '    ' ) {
					//echo "<td align=\"left\"><a href=\"http://www.qrz.com/db/$listElem[2]\" data-featherlight=\"iframe\" data-featherlight-iframe-min-width=\"90%\" data-featherlight-iframe-max-width=\"90%\" data-featherlight-iframe-width=\"2000\" data-featherlight-iframe-height=\"2000\">$listElem[2]</a>/$listElem[3]</td>";
					echo "<td align=\"left\">&nbsp;<a href=\"http://www.qrz.com/db/$listElem[2]\" target=\"_blank\"><b>$listElem[2]</a>/$listElem[3]</b></td>";
				} else {
					//echo "<td align=\"left\"><a href=\"http://www.qrz.com/db/$listElem[2]\" data-featherlight=\"iframe\" data-featherlight-iframe-min-width=\"90%\" data-featherlight-iframe-max-width=\"90%\" data-featherlight-iframe-width=\"2000\" data-featherlight-iframe-height=\"2000\">$listElem[2]</a></td>";
					echo "<td align=\"left\">&nbsp;<a href=\"http://www.qrz.com/db/$listElem[2]\" target=\"_blank\"><b>$listElem[2]</b></a></td>";
				}
			}
			echo"<td align=\"left\">&nbsp;<span style=\"color:#b5651d;font-weight:bold;\">".str_replace(" ","&nbsp;", $listElem[4])."</span></td>";
			if ($listElem[5] == "DVSM/UC"){
				echo "<td style=\"background:#1d1;\">DVSM/UC</td>";
			} else {
				echo "<td>$listElem[5]</td>";
			}
			if ($listElem[6] == null) {
				echo "<td colspan=\"2\" style=\"background:#f33;\">TX</td>";
			} else if ($listElem[6] == "SMS") {
				echo "<td colspan=\"2\" style=\"background:#1d1;\">SMS</td>";
			} else {
				echo"<td>$listElem[6]</td>"; //duration
				
				// Colour the BER Field
				if (floatval($listElem[8]) == 0) { echo "<td>$listElem[8]</td>"; }
				elseif (floatval($listElem[8]) >= 0.0 && floatval($listElem[8]) <= 1.9) { echo "<td style=\"background:#1d1;\">$listElem[8]</td>"; }
				elseif (floatval($listElem[8]) >= 2.0 && floatval($listElem[8]) <= 4.9) { echo "<td style=\"background:#fa0;\">$listElem[8]</td>"; }
				else { echo "<td style=\"background:#f33;\">$listElem[8]</td>"; }
			}
			echo"</tr>\n";
			$counter++; }
		}
	}

?>
  </table>
</fieldset>
</div>
<br>
