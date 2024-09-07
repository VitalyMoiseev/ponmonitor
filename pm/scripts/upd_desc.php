<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../include/vars.php';
require '../include/database.php';

if (isset($_GET['olt_check'])){
    $olt_check = "WHERE Id = ".$_GET['olt_check'];
}else{
    $olt_check = "";
}

if (isset($_GET['web'])){
    $web = true;
}else{
    $web = false;
}
echo '<pre>';
$table = $tbl_pref.'olt';
$query = "SELECT * FROM $table $olt_check";

if (!$result = $mysqli_wb->query($query)){
    $file_log = $mysqli_wb->error."\n";
    echo $file_log;
    file_put_contents($log_file, $file_log, FILE_APPEND);
    exit();
}
$query = '';
while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
    unset($onu_macs);
    $snmperr = false;
    $onu_macs = array();
    $community = $row['community'];
    $host = $row['host'];
    if ($row['type'] == 1){
        $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
        $session->quick_print = 1;
        $macs_oid = ".1.3.6.1.4.1.3320.10.3.3.1.2";
        $macs_replace_str = 'STRING:';
        if ($onu_macs = $session->walk($macs_oid, true)){
            $snmperr = false;
            $onu_macs = str_replace($macs_replace_str, '', $onu_macs);
        }elseif($session->getErrno() == 8){
                $snmperr = false;
                $onu_macs = array();
            }else{
                $snmperr = true;
        }
        if ($snmperr){
            $file_log = $session->getError()."\n";
            echo $file_log;
            file_put_contents($log_file, $file_log, FILE_APPEND);
            $session->close();
            continue;
        }
        $descs_oid = ".1.3.6.1.4.1.3320.10.3.2.1.21";
        $descs_replace_str = 'STRING:';
        if ($onu_descs = $session->walk($descs_oid, true)){
            $snmperr = false;
            $onu_descs = str_replace($descs_replace_str, '', $onu_descs);
        }elseif($session->getErrno() == 8){
                $snmperr = false;
                $onu_descs = array();
            }else{
                $snmperr = true;
        }
        if ($snmperr){
            $file_log = $session->getError()."\n";
            echo $file_log;
            file_put_contents($log_file, $file_log, FILE_APPEND);
            $session->close();
            continue;
        }
        $table = $tbl_pref.'onu';
        foreach ($onu_macs as $key => $onu_mac) {
            $onu_mac = trim($onu_mac,'"');
            $onu_mac = trim($onu_mac);
            $onu_mac = str_replace (" ", ":", $onu_mac);
            if (array_key_exists($key, $onu_descs)){
                $onu_desc = trim($onu_descs[$key],'"');
                $onu_desc = trim($onu_desc);
                $query .= "INSERT INTO $table (mac, description) VALUES ('$onu_mac', '$onu_desc') ON DUPLICATE KEY UPDATE description='$onu_desc';\n";
            }
        }


        $olt_gpon = true;
    }else{
        $olt_gpon = false;
    }
    $session->close();
}
if ($mysqli_wb->multi_query($query)){
    $i = 0;
    do {
        $i++;
    } while ($mysqli_wb->next_result());   
    if ($mysqli_wb->errno) {
        echo "Batch execution prematurely ended on row $i.\n";
        $qar = explode("\n", $query);
        $file_log = $qar[$i]." - ".$mysqli_wb->error."\n";
        echo $file_log;
        file_put_contents($log_file, $file_log, FILE_APPEND);
    }
}

if ($web){
    echo "<script type='text/javascript'>\n";
    echo "location.reload();\n";
    echo "</script>";
}


$mysqli_wb->close();