<?php
function GetOnuMac($key, $host, $community) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    $oid = ".1.3.6.1.4.1.3320.101.10.1.1.3.$key";
    if ($onu_mac = $session->get($oid)){
        $onu_mac = trim($onu_mac,'"');
        $onu_mac = trim($onu_mac);
        $onu_mac = str_replace (" ", ":", $onu_mac);
        $session->close();
        return $onu_mac;
    }else{
        $session->close();
        return false;
    }
}

function GetOnuName($key, $host, $community) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    $oid = '.1.3.6.1.2.1.2.2.1.2.'.$key;
    if ($onu_name = $session->get($oid)){
        $session->close();
        return $onu_name;
    }else{
        $session->close();
        return false;
    }
}

function GetOnuStatus($key, $host, $community) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    $oid = '1.3.6.1.4.1.3320.101.10.1.1.26.'.$key;
    if ($resp = $session->get($oid)){
        $session->close();
        return intval($resp);
    }else{
        $session->close();
        return false;
    }
}

function GetOnuDist($key, $host, $community) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    $oid = '1.3.6.1.4.1.3320.101.10.1.1.27.'.$key;
    if ($resp = $session->get($oid)){
        $session->close();
        return intval($resp);
    }else{
        $session->close();
        return false;
    }
}

function GetOnuPwr($key, $host, $community) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    $oid = ".1.3.6.1.4.1.3320.101.10.5.1.5.$key";
    if ($onu_s = $session->get($oid)){
        if ($onu_s == '-65535' OR $onu_s == 0){
            $onu_s = "OFFLINE";
        }else{
            $onu_s = $onu_s / 10;
        }
    }else{
        $onu_s = "OFFLINE";
    }
    $session->close();
    return $onu_s;
}

function GetOnuKeyByMac($mac, $host, $community) {
    $mac = str_replace(":", " ", $mac);
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    if($ar1 = $session->walk(".1.3.6.1.4.1.3320.101.10.1.1.3", true)){
        foreach ($ar1 as $key => $value) {
            if (strripos($value, $mac)){
                $session->close();
                return $key;
            }
        }
    }else{
        $session->close();
        return false;
    }
}

function GetOnuEthEna($key, $host, $community) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    #$oid = '1.3.6.1.2.1.2.2.1.7.'.$key;
    $oid = "1.3.6.1.4.1.3320.101.12.1.1.7.$key";
    if($aaa = $session->walk($oid, true)){
        $session->close();
        return $aaa;
    }else{
        $session->close();
        return false;
    }
}

function GetOnuEthState($key, $host, $community) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    #$oid = '.1.3.6.1.2.1.2.2.1.8.'.$key;
    $oid = "1.3.6.1.4.1.3320.101.12.1.1.8.$key";
    if($aaa = $session->walk($oid, true)){
        $session->close();
        return $aaa;
    }else{
        $session->close();
        return false;
    }
}

function GetOnuPvid($key, $host, $community) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    $oid = "1.3.6.1.4.1.3320.101.12.1.1.3.$key";
    if($aaa = $session->walk($oid, true)){
        $session->close();
        return $aaa;
    }else{
        return false;
    }
}

function GetDeregData($host, $port, $tlog, $tpas, $onu_name) {
    $ar1 = explode(':', $onu_name);
    $sfp_int = $ar1[0];
    $onu_num = $ar1[1];
    if ($con = pfsockopen($host, $port, $errno, $errstr, 10)){
        $s1 = $tlog."\r\n";
        fwrite($con, $s1);
        $s1 = $tpas."\r\n";
        fwrite($con, $s1);
        sleep(1);
        $s1 = "su\r\n";
        fwrite($con, $s1);
        fwrite($con, "show epon inact int $sfp_int $onu_num \r\n");
        sleep(2);
        $out = fread($con, 16536);
        $out = explode(' ------------', $out);
        $out = end($out);
        #$arr_out = explode("\n", $out);
        $arr_out = str_replace(array("\n", "\r"),"",$out);
        fclose($con);
        $arr_out = preg_split("/[\s,]+/",$arr_out);
        $res['Status'] = $arr_out[2];
        $res['LastRegTime'] = $arr_out[3];
        $res['LastDeregTime'] = $arr_out[4];
        $res['LastDeregReason'] = $arr_out[5];
        return $res;
    }else{
        return false;
    }
    
}

function GetOnuFDBByKey($key, $host, $community, $mac) {
    function FormatMacBDCOM(&$macf, $keyf){
        $macf = trim($macf,'"');
        $macf = trim($macf);
        $macf = str_replace (" ", ":", $macf);
    }
    $mac = str_replace(":", " ", $mac);
    $mac = "\"$mac \"";
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    $oid = ".1.3.6.1.4.1.3320.152.1.1.3.$key";
    if ($fdb = $session->walk($oid, true)){
        if($k1 = array_search($mac, $fdb)){
            unset($fdb[$k1]);
        }
        array_walk($fdb,'FormatMacBDCOM');
        $session->close();
        if(count($fdb) < 1){
            $fdb[] = "empty FDB";
        }
        return $fdb;
    }else{
        $session->close();
        return false;
    }
    
}

function GetAllowedVlans($host, $port, $tlog, $tpas, $onu_name) {
    $ar1 = explode(':', $onu_name);
    $sfp_int = $ar1[0];
    if ($con = pfsockopen($host, $port, $errno, $errstr, 10)){
        $s1 = $tlog."\r\n";
        fwrite($con, $s1);
        $s1 = $tpas."\r\n";
        fwrite($con, $s1);
        sleep(1);
        $s1 = "su\r\n";
        fwrite($con, $s1);
        fwrite($con, "show running-config interface $sfp_int | include vlan-allowed\r\n");
        sleep(2);
        $out = fread($con, 16536);
        fclose($con);
        $out = explode("Current configuration:\r\n!\r\n", $out);
        $out = end($out);
        $out = explode("\r\n", $out);
        foreach ($out as $value) {
            $ar1 = explode('vlan-allowed ', $value);
            if(isset($ar1[1])){
                $ar2 = explode(',', $ar1[1]);
                foreach ($ar2 as $value) {
                    if(strpos($value, '-')){
                        $ar3 = explode('-', $value);
                        $ar4 = range($ar3[0], $ar3[1]);
                        foreach ($ar4 as $value) {
                            $vlan_allowed[] = $value;
                        }
                    }else{
                        $vlan_allowed[] = $value;
                    }
                }
            }
        }
        return $vlan_allowed;
    }else{
        return false;
    }
    
}