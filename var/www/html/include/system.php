<?php 
include_once $_SERVER['DOCUMENT_ROOT'].'/include/tools.php';
$progname = basename($_SERVER['SCRIPT_FILENAME'],".php");
$cpuLoad = sys_getloadavg();
$cpuTempCRaw = exec('cat /sys/class/thermal/thermal_zone0/temp');
if ($cpuTempCRaw > 1000) { $cpuTempC = round($cpuTempCRaw / 1000); } else { $cpuTempC = round($cpuTempCRaw); }
$cpuTempF = round(+$cpuTempC * 9 / 5 + 32, 1);
if ($cpuTempC < 50) { $cpuTempHTML = "<td style=\"background: #1d1\">".$cpuTempC."&deg;C / ".$cpuTempF."&deg;F</td>\n"; }
if ($cpuTempC >= 50) { $cpuTempHTML = "<td style=\"background: #fa0\">".$cpuTempC."&deg;C / ".$cpuTempF."&deg;F</td>\n"; }
if ($cpuTempC >= 69) { $cpuTempHTML = "<td style=\"background: #f00\">".$cpuTempC."&deg;C / ".$cpuTempF."&deg;F</td>\n"; }
?>
<span style="font-weight: bold;font-size:13px;">Hardware info</span>
<fieldset style="box-shadow:0 0 10px #999;background-color:#e8e8e8e8; width:855px;margin-top:8px;margin-left:6px;margin-right:0px;font-size:12px;border-top-left-radius: 10px; border-top-right-radius: 10px;border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">
<table>
  <tr>
    <th>Hostname<br/><span style="font-weight: bold;font-size:10px;">IP: <?php echo str_replace(',', ',<br />', exec('hostname -I'));?></span></th>
    <th><b>Kernel<br/>release</b></th>
    <th colspan="2">Platform <br><span style="font-weight: bold;font-size:11px;">Uptime: <?php echo str_replace(',', ',', exec('uptime -p'));?></span></th>
    <th><span><b>CPU Load</b></span></th>
    <th><span><b>CPU Temp</b></span></th>
  </tr>
  <tr>
    <td><?php echo php_uname('n');?></td>
    <td><?php echo php_uname('r');?></td>
    <td colspan="2"><?php echo exec('/usr/local/bin/platformDetect.sh');?></td>
    <td><?php echo round($cpuLoad[0],1);?> / <?php echo round($cpuLoad[1],1);?> / <?php echo round($cpuLoad[2],1);?></td>
    <?php echo $cpuTempHTML; ?>
  </tr>
</table>
</fieldset>
<br>
