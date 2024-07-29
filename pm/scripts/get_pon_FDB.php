<?php

if (!isset($_GET['olt_id'])){
    exit();
}
if (!isset($_GET['onu'])){
    exit();
}
if (!isset($_GET['onu_mac'])){
    exit();
}
if (!isset($_GET['key'])){
    exit();
}
if (!isset($_GET['onuid'])){
    exit();
}
$olt_id = $_GET['olt_id'];
$onu = $_GET['onu'];
$onu_mac = $_GET['onu_mac'];
$key = $_GET['key'];
$onuId = $_GET['onuid'];

require '../include/vars.php';
require '../include/pon_functions.php';
require '../include/database.php';
require '../include/select_lang.php';

$table = $tbl_pref.'olt';
$query = "SELECT * FROM $table WHERE Id = $olt_id;";
    
$result = $mysqli_wb->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$result->close();
    
$host = $row['host'];
$port = $row['telnet_port'];
    
$tlog = $row['telnet_name'];
$tpas = $row['telnet_password'];

$community = $row['community'];
$snmp_host = $row['host'];
if($row['snmp_port'] != 161){
    $snmp_host = $snmp_host.':'.$row['snmp_port'];
}

#det FDB from olt by snmp
switch ($FDB_method) {
    case 'SNMP':
        if($fdb = GetOnuFDBByKey($key, $host, $community, $onu_mac)){
            echo '<table class="features-table" width="100%"><thead>';
            echo '<tr><td class="grey" colspan="2">FDB '.$labels['table'].'</td></tr></thrad>';
            echo "<tbody><tr><td class=\"grey\"><strong>vlan id</strong></td><td class=\"grey\"><strong>MAC</strong></td></tr>";
            foreach ($fdb as $vmac => $mac) {
                $vlan = explode('.', $vmac);
                $vlan = $vlan[0];
                echo "<tr><td class=\"green\">$vlan</td><td class=\"green\">$mac</td></tr>";
            }
            echo '</tbody><tfoot><tr><td class="grey" colspan="2"><div id="editcmdf">&nbsp;</div></td></tr></tfoot></table>';
        }else{
            echo "olt offline";
        }

        break;

    default:
#get mac from olt by telnet
    if ($con = pfsockopen($host, $port, $errno, $errstr, 10)){
        $s1 = $tlog."\r\n";
        fwrite($con, $s1);
        $s1 = $tpas."\r\n";
        fwrite($con, $s1);
        sleep(1);
        $s1 = "su\r\n";
        fwrite($con, $s1);
        fwrite($con, "show mac a int $onu \r\n");
        sleep(2);
        $out = fread($con, 16536);
        $out = explode(' -----', $out);
        $out = end($out);
        $arr_out = explode("\n", $out);
        while (trim(array_pop($arr_out)) == "--More--") {
            fwrite($con, chr(32));
            sleep(2);
            $arr_tmp = explode("\r\n", fread($con, 16536));
            $arr_out = array_merge($arr_out,$arr_tmp);
        }
        fclose($con);
        echo '<table class="features-table" width="100%"><thead>';
        echo '<tr><td class="grey" colspan="2">FDB '.$labels['table'].'</td></tr></thrad>';
        echo "<tbody><tr><td class=\"grey\"><strong>vlan id</strong></td><td class=\"grey\"><strong>MAC</strong></td></tr>";
        foreach ($arr_out as $out_mac){
            $out_mac = str_word_count($out_mac, 1, '0123456789.');
            if (is_null($out_mac[1])){
                continue;
            }
            $vlan = $out_mac[0];
            $mac = $out_mac[1];
            $mac = strtoupper($mac);
            $mac = str_split($mac);
            $mac = $mac[0].$mac[1].':'.$mac[2].$mac[3].':'.$mac[5].$mac[6].':'.$mac[7].$mac[8].':'.$mac[10].$mac[11].':'.$mac[12].$mac[13];
            if ($mac == $onu_mac){
                continue;
            }
            echo "<tr><td class=\"green\">$vlan</td><td class=\"green\">$mac</td></tr>";
        }
        echo '</tbody><tfoot><tr><td class="grey" colspan="2"><div id="editcmdf">&nbsp;</div></td></tr></tfoot></table>';
    }else{
        echo "olt offline";
    }
    break;
}

?>
