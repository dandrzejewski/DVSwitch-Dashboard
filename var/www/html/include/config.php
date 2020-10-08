<?php
// Report all errors except E_NOTICE
error_reporting(E_ALL & ~E_NOTICE);

// ABINFO = rxPort from [USRP] Analog_Bridge.ini
define("ABINFO", "34001");
define("LOGPATH", "/var/log/mmdvm");
define("MMDVMLOGPREFIX", "MMDVM_Bridge");
define("MMDVMINIPATH", "/opt/MMDVM_Bridge/");
define("MMDVMINIFILENAME", "MMDVM_Bridge.ini");
define("DMRIDDATPATH", "/var/lib/mmdvm");
define("YSFGATEWAYLOGPREFIX", "YSFGateway");
define("YSFGATEWAYINIPATH", "/opt/YSFGateway");
define("YSFGATEWAYINIFILENAME", "YSFGateway.ini");
define("P25GATEWAYLOGPREFIX", "P25Gateway");
define("P25GATEWAYINIPATH", "/opt/P25Gateway");
define("P25GATEWAYINIFILENAME", "P25Gateway.ini");
define("NXDNGATEWAYLOGPREFIX", "NXDNGateway");
define("NXDNGATEWAYINIPATH", "/opt/NXDNGateway");
define("NXDNGATEWAYINIFILENAME", "NXDNGateway.ini");
define("LINKLOGPATH", "/var/log/ircddbgateway");
define("IRCDDBGATEWAY", "ircddbgatewayd");


?>
