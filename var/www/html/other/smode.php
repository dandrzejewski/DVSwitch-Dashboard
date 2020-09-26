<?php

switch($_GET['m']) {

case 'DMR':
	shell_exec('/opt/MMDVM_Bridge/dvswitch.sh mode DMR');
	header('Location: ' . $_SERVER['HTTP_REFERER']);
	break;
case 'DStar': 
	shell_exec('/opt/MMDVM_Bridge/dvswitch.sh mode DSTAR');
	header('Location: ' . $_SERVER['HTTP_REFERER']);
	break;
case 'NXDN':   
	shell_exec('/opt/MMDVM_Bridge/dvswitch.sh mode NXDN');
	header('Location: ' . $_SERVER['HTTP_REFERER']);
	break;
case 'P25':    
	shell_exec('/opt/MMDVM_Bridge/dvswitch.sh mode P25');
	header('Location: ' . $_SERVER['HTTP_REFERER']);
	break;
case 'YSF':
	shell_exec('/opt/MMDVM_Bridge/dvswitch.sh mode YSF');
	header('Location: ' . $_SERVER['HTTP_REFERER']);
	break;
}
?>
