<?php
#error_reporting(E_ALL);
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
$log_file = $log_file.date("Y-m-d H:i:s").'.log';
$file_log = "Start ".date("Y-m-d H:i:s")."\n";
file_put_contents($log_file, $file_log, FILE_APPEND);


#read settings
$table = $tbl_pref.'settings';
$query = "SELECT * FROM $table";
if ($result = $mysqli_wb->query($query)){
    while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
        $param[$row['parametr']] = $row['value'];
    }
}else{
    $file_log = $mysqli_wb->error."\n";
    file_put_contents($log_file, $file_log, FILE_APPEND);
    exit();
}
$result->close();

#check wrong param
if (($param['check_onu_state_enable'] == 1) AND ($param['onu_check_begin'] == 1)){
    if ((time() - strtotime($param['onu_check_last_start'])) > 1800){
        $query = "UPDATE ".$tbl_pref."settings SET value = 0 WHERE parametr = 'onu_check_begin'";
        $mysqli_wb->query($query);
    }
}

echo '<pre>';
if (($param['check_onu_state_enable'] == 1) AND ($param['onu_check_begin'] == 0) AND ((time() - strtotime($param['onu_check_last_start'])) > $param['check_onu_state_interval'])){
    $query = "UPDATE ".$tbl_pref."settings SET value = 1 WHERE parametr = 'onu_check_begin'";
    $mysqli_wb->query($query);
    $query = "UPDATE ".$tbl_pref."settings SET value = NOW() WHERE parametr = 'onu_check_last_start'";
    $mysqli_wb->query($query);

    #get list OLT

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
        unset($sfp_count, $onu_macs, $onu_names, $onu_pwrs);
        $er1 = false;
        $er2 = false;
        $er3 = false;
        $community = $row['community'];
        $host = $row['host'];
        if($row['snmp_port'] != 161){
            $host = $host.':'.$row['snmp_port'];
        }
        $file_log = $row['Id']." $host ".strftime("%X");
        echo $file_log;
        file_put_contents($log_file, $file_log, FILE_APPEND);
        $olt_id = $row['Id'];
        unset($sfp_count, $onu_macs, $onu_names, $sfp_online_count);
        $session = new SNMP(SNMP::VERSION_2C, $host, $community, 5000000, 20);
        $session->quick_print = 1;
            
        for ($i = 1; $i <= $param['max_snmp_try']; $i++) {
            if ($olt_ver = $session->get(".1.3.6.1.2.1.1.1.0")){
                $snmperr = false;
                if(strpos($olt_ver, "P3608")){
                    $olt_type = 2;
                }else{
                    $olt_type = 1;
                }
                break;
            }else{
                $snmperr = true;
            }
        }
        if ($snmperr){
            $file_log = $session->getError()."\n";
            echo $file_log;
            file_put_contents($log_file, $file_log, FILE_APPEND);
            $table = $table = $tbl_pref.'olt';
            $query .= "UPDATE $table SET status=0 WHERE Id = $olt_id;\n";
            $session->close();
            continue;
        }
        $dtnow = date("Y-m-d H:i:s");
        $table = $tbl_pref.'olt';
        $query .= "UPDATE $table SET status=1, last_act='$dtnow' WHERE Id=$olt_id;\n";
        for ($i = 1; $i <= $param['max_snmp_try']; $i++) {
            if ($onu_macs = $session->walk(".1.3.6.1.4.1.3320.101.10.1.1.3", true)){
                $snmperr = false;
                break;
            }else{
                $snmperr = true;
            }
        }
        if ($snmperr){
            $file_log = $session->getError()."\n";
            echo $file_log;
            file_put_contents($log_file, $file_log, FILE_APPEND);
            $session->close();
            continue;
        }
        $file_log = " mac:".count($onu_macs)." ".strftime("%X").", ";
        echo $file_log;
        file_put_contents($log_file, $file_log, FILE_APPEND);

        for ($i = 1; $i <= $param['max_snmp_try']; $i++) {
            if ($onu_names = $session->walk(".1.3.6.1.2.1.2.2.1.2", true)){
                $snmperr = false;
                break;
            }else{
                $snmperr = true;
            }
        }
        if ($snmperr){
            $file_log = $session->getError()."\n";
            echo $file_log;
            file_put_contents($log_file, $file_log, FILE_APPEND);
            $session->close();
            continue;
        }
        $file_log = " names:".count($onu_names)." ".strftime("%X").", ";
        echo $file_log;
        file_put_contents($log_file, $file_log, FILE_APPEND);

        if ($olt_type == 2){
            #P3608
            $onu_ids = array_keys($onu_macs);
            foreach ($onu_ids as $onu_id) {
                $oid = ".1.3.6.1.4.1.3320.101.10.5.1.5.$onu_id";
                if ( $pwr = $session->get($oid, true)){
                    $onu_pwrs[$onu_id] = $pwr;
                }
            }
            $snmperr = false;
        }else{
            #P3310
            $oid = ".1.3.6.1.4.1.3320.101.10.5.1.5";
            for ($i = 1; $i <= $param['max_snmp_try']; $i++) {
                if ($onu_pwrs = $session->walk($oid, true)){
                    $snmperr = false;
                    break;
                }else{
                    $snmperr = true;
                }
            }
        }
        if ($snmperr){
            $file_log = $session->getError()."\n";
            echo $file_log;
            file_put_contents($log_file, $file_log, FILE_APPEND);
            $session->close();
            continue;
        }
        $session->close();
        $file_log = " powers:".count($onu_pwrs)." ".strftime("%X")."\n";
        echo $file_log;
        file_put_contents($log_file, $file_log, FILE_APPEND);
            
              
        $table = $tbl_pref.'onu';
        $query .= "UPDATE $table SET present = 0 WHERE olt = $olt_id;\n";
        foreach ($onu_macs as $key => $onu_mac) {
            $onu_names[$key] = trim($onu_names[$key],'"');
            $sfp_er = substr($onu_names[$key], 0, 6);
            $nam_ar = explode(':', $onu_names[$key]);
            if ((count($nam_ar) == 2) AND ($sfp_er == 'EPON0/')){
                $onu_name = $onu_names[$key];
                $sfp = $nam_ar[0];
                if(!isset($sfp_count[$sfp])){
                    $sfp_count[$sfp] = 0;
                }
                ++$sfp_count[$sfp];
                $sfp_ar = explode('/', $sfp);
                $onu_order_id = $sfp_ar[1] * 1000 + $nam_ar[1];
                $onu_mac = trim($onu_mac,'"');
                $onu_mac = trim($onu_mac);
                $onu_mac = str_replace (" ", ":", $onu_mac);
                if (!array_key_exists($key, $onu_pwrs)){
                    $table = $tbl_pref.'onu';
                    $query .= "INSERT INTO $table (mac, olt, onu_name, present, status, order_id) VALUES ('$onu_mac', $olt_id, '$onu_name', 1, 0, $onu_order_id) ON DUPLICATE KEY UPDATE olt=$olt_id, onu_name='$onu_name', present=1, status=0, order_id=$onu_order_id;\n";
                    $pwr = 0;
                }elseif ($onu_pwrs[$key] == "-65535") {
                    $table = $tbl_pref.'onu';
                    $query .= "INSERT INTO $table (mac, olt, onu_name, present, status, order_id) VALUES ('$onu_mac', $olt_id, '$onu_name', 1, 0, $onu_order_id) ON DUPLICATE KEY UPDATE olt=$olt_id, onu_name='$onu_name', present=1, status=0, order_id=$onu_order_id;\n";
                    $pwr = 0;
                }else {
                    if(!isset($sfp_online_count[$sfp])){
                        $sfp_online_count[$sfp] = 0;
                    }
                    ++$sfp_online_count[$sfp];
                    $pwr = $onu_pwrs[$key];
                    $pwr = $pwr / 10;
                    $dtnow = date("Y-m-d H:i:s");
                    $table = $tbl_pref.'onu';
                    $query .= "INSERT INTO $table (mac, olt, onu_name, present, status, pwr, last_act, order_id) VALUES ('$onu_mac', $olt_id, '$onu_name', 1, 1, '$pwr', '$dtnow', $onu_order_id) ON DUPLICATE KEY UPDATE olt=$olt_id, onu_name='$onu_name', present=1, status=1, pwr='$pwr', last_act='$dtnow', order_id=$onu_order_id;\n";
                }
                $pwrs_macs[$onu_mac] = floatval($pwr);
                
            }
        }
        $table = $tbl_pref.'olt_sfp';
        $query .= "UPDATE $table SET count_onu = 0, online_count=0 WHERE olt=$olt_id;\n";
        foreach ($sfp_count as $sfp => $value){
            if(!isset($sfp_online_count[$sfp])){
                $sfp_online_count[$sfp] = 0;
            }
            $onlc = $sfp_online_count[$sfp];
            $table = $tbl_pref.'olt_sfp';
            $query .= "INSERT INTO $table (olt, sfp, count_onu, online_count) VALUES ($olt_id, '$sfp', $value, $onlc) ON DUPLICATE KEY UPDATE count_onu=$value, online_count=$onlc;\n";
        }
    }
    
    $table = $tbl_pref.'settings';
    $query2 = "UPDATE $table SET value = 0 WHERE parametr = 'onu_check_begin';";
    if (!$mysqli_wb->query($query2)){
        $file_log = $mysqli_wb->error."\n";
        file_put_contents($log_file, $file_log, FILE_APPEND);
    }
    #$file_log = $query;;
    #echo $file_log;
    #file_put_contents($log_file, $file_log, FILE_APPEND);
    echo $query;
    
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
    
####### ONU pwr history

# read last powers
    $table = $tbl_pref.'onu_pwr_history';
    $query = "SELECT * FROM $table WHERE stoptime IS NULL";
    if (!$result = $mysqli_wb->query($query)){
        $file_log = $mysqli_wb->error."\n";
        echo $file_log;
        file_put_contents($log_file, $file_log, FILE_APPEND);
        exit();
    }
    $query = "DELETE FROM $table WHERE stoptime < (NOW() - INTERVAL $PwrHistTerm);";
    if($result->num_rows == 0 ){
        foreach ($pwrs_macs as $mac => $pwr) {
            $pwr = str_replace(",", ".", $pwr);
            $query .= "INSERT INTO $table (mac, pwr, starttime) VALUES ('$mac', $pwr, NOW());\n";
        }
    }else{
        while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
            $pwr_old[$row['mac']] = floatval($row['pwr']);
            $hist_ids[$row['mac']] = $row['Id'];
        }
        foreach ($pwrs_macs as $mac => $pwr) {
            if (array_key_exists($mac, $pwr_old)){
                $pwr = $pwr + 0;
                if($pwr_old[$mac] != $pwr){
                    $h_id = $hist_ids[$mac];
                    $pwr = str_replace(",", ".", $pwr);
                    $query .= "UPDATE $table SET stoptime=NOW() WHERE Id=$h_id;\n";
                    $query .= "INSERT INTO $table (mac, pwr, starttime) VALUES ('$mac', $pwr, NOW());\n";
                }
            }else{
                $pwr = str_replace(",", ".", $pwr);
                $query .= "INSERT INTO $table (mac, pwr, starttime) VALUES ('$mac', $pwr, NOW());\n";
            }
        }
    }
#echo $query;

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
    }else{
        echo "MySQL error".$mysqli_wb->error;
    }
    
#
#######
}else{
    $ttt = time() - strtotime($param['onu_check_last_start']);
    $file_log = "$ttt sec from last start, exit\n";
    echo $file_log;
    file_put_contents($log_file, $file_log, FILE_APPEND);
}

if ($web){
    echo "<script type='text/javascript'>\n";
    echo "location.reload();\n";
    echo "</script>";
}


$mysqli_wb->close();