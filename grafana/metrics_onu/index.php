<?php

require '../include/vars.php';
require '../include/database.php';
# Check IP
if ( ! in_array($_SERVER['REMOTE_ADDR'], $vmagentIPs) ) {
    echo $_SERVER['REMOTE_ADDR'];
    exit();
}
header("Content-Type: text/plain");
$starttime1 = microtime(true);
$oid_bytes_in = "1.3.6.1.2.1.2.2.1.10";
$oid_bytes_out = "1.3.6.1.2.1.2.2.1.16";

$query = "SELECT `switches`.`host`, `switches`.`community`, `switch_type`.`type`
  FROM `switches`
  INNER JOIN `switch_type` ON `switch_type`.`id` = `switches`.`type_id`
  WHERE  `switch_type`.`type` > 1 AND `switches`.`check_onu` = 1
  ORDER BY `switches`.`name`;";
if (!$result = $mysqli_bil->query($query)){
    exit();
}
while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
    $onu_macs = array();
    $bytes_in = array();
    $bytes_out = array();
    $community = $row['community'];
    $host = $row['host'];
    if ($row['type'] == 3){
        $macs_oid = ".1.3.6.1.4.1.3320.10.3.3.1.2";
    }else{
        $macs_oid = ".1.3.6.1.4.1.3320.101.10.1.1.3";
    }
    $starttime = microtime(true);
    echo "#Getting metrics OLT $host start at $starttime\n";
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    if ($onu_macs = $session->walk($macs_oid, true)){
        $macs = count($onu_macs);
        $time1 = microtime(true) - $starttime;
        echo "#MACs: $macs, getting time: $time1\n";
        if ($bytes_in = $session->walk($oid_bytes_in, true)){
            $in = count($bytes_in);
            $time2 = microtime(true) - $starttime - $time1;
            echo "#IN: $in, getting time: $time2\n";
            if ($bytes_out = $session->walk($oid_bytes_out, true)){
                $out = count($bytes_out);
                $time3 = microtime(true) - $starttime - $time1 - $time2;
                echo "#OUT: $out, getting time: $time3\n";
                foreach ($onu_macs as $ifindex => $onu_mac) {
                    $onu_mac = trim($onu_mac,'"');
                    $onu_mac = trim($onu_mac);
                    $onu_mac = str_replace (" ", ":", $onu_mac);
                    echo "PON_ONU_bytes_in{onu=\"$onu_mac\"} $bytes_in[$ifindex]\n";
                    echo "PON_ONU_bytes_out{onu=\"$onu_mac\"} $bytes_out[$ifindex]\n";
                }
            }
        }
    }
    $session->close();
}
$mysqli_bil->close();
$mysqli_rad->close();
$mysqli_wb->close();
$time_all = microtime(true) - $starttime1;
echo "#All done, time: $time_all";
?>