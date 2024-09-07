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

function GetOnuStatus($key, $host, $community, $olt_gpon = false) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    if ($olt_gpon){
        $oid = ".1.3.6.1.4.1.3320.10.3.3.1.4.$key";
    }else{
        $oid = ".1.3.6.1.4.1.3320.101.10.1.1.26.$key";
    }
    if ($resp = $session->get($oid)){
        $session->close();
        return intval($resp);
    }else{
        $session->close();
        if ($resp == "0"){
            return 0;
        }else{
            return false;
        }
    }
}

function GetOnuDist($key, $host, $community, $olt_gpon = false) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    if ($olt_gpon){
        $oid = '.1.3.6.1.4.1.3320.10.3.1.1.33.'.$key;
        $kf = 0.1;
    }else{
        $oid = '1.3.6.1.4.1.3320.101.10.1.1.27.'.$key;
        $kf = 1;
    }
    if ($resp = $session->get($oid)){
        $session->close();
        return intval($resp) * $kf;
    }else{
        $session->close();
        return false;
    }
}

function GetOnuPwr($key, $host, $community, $olt_gpon = false) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    if ($olt_gpon){
        $oid = '.1.3.6.1.4.1.3320.10.3.4.1.2.'.$key;
    }else{
        $oid = ".1.3.6.1.4.1.3320.101.10.5.1.5.$key";
    }
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

function GetPwrFromOnu($key, $host, $community, $olt_gpon = false) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    if ($olt_gpon){
        $oid = ".1.3.6.1.4.1.3320.10.2.3.1.3.$key";
    }else{
        $oid = ".1.3.6.1.4.1.3320.101.108.1.3.$key";
    }
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

function GetOnuKeyByMac($mac, $host, $community, $olt_gpon = false) {
    if (!$olt_gpon){
        $mac = str_replace(":", " ", $mac);
    }
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    if ($olt_gpon){
        $macs_oid = ".1.3.6.1.4.1.3320.10.3.3.1.2";
    }else{
        $macs_oid = ".1.3.6.1.4.1.3320.101.10.1.1.3";
    }
    if($ar1 = $session->walk($macs_oid, true)){
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

function GetOnuEthEna($key, $host, $community, $olt_gpon = false) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    if ($olt_gpon){
        $oid = ".1.3.6.1.4.1.3320.10.4.1.1.2.$key";
    }else{
        $oid = "1.3.6.1.4.1.3320.101.12.1.1.7.$key";
    }
    if($aaa = $session->walk($oid, true)){
        $session->close();
        return $aaa;
    }else{
        $session->close();
        return false;
    }
}

function GetOnuEthState($key, $host, $community, $olt_gpon = false) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    if ($olt_gpon){
        $oid = ".1.3.6.1.4.1.3320.10.4.1.1.4.$key";
    }else{
        $oid = "1.3.6.1.4.1.3320.101.12.1.1.8.$key";
    }
    if($aaa = $session->walk($oid, true)){
        $session->close();
        return $aaa;
    }else{
        $session->close();
        return false;
    }
}

function GetOnuPvid($key, $host, $community, $olt_gpon = false) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    if ($olt_gpon){
        $oid = ".1.3.6.1.4.1.3320.10.4.1.1.6.$key";
    }else{
        $oid = "1.3.6.1.4.1.3320.101.12.1.1.3.$key";
    }
    if($aaa = $session->walk($oid, true)){
        $session->close();
        return $aaa;
    }else{
        return false;
    }
}

function GetGponVLANProfilePvid($profile, $host, $community) {
    $session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
    $session->quick_print = 1;
    $oid = ".1.3.6.1.4.1.3320.10.6.1.1.1.4.$profile";
    if($aaa = $session->get($oid)){
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
    $dstoffset = false;
    $out = '';
    if (date("I") == 1){
        $dstoffset = true;
    }
    if ($con = pfsockopen($host, $port, $errno, $errstr, 10)){
        $s1 = $tlog."\r\n";
        fwrite($con, $s1);
        $s1 = $tpas."\r\n";
        fwrite($con, $s1);
        sleep(1);
        $s1 = "enable\r\nterminal length 0\r\nterminal width 0\r\n";
        fwrite($con, $s1);
        fwrite($con, "show epon inact int $sfp_int $onu_num \r\n");
        sleep(2);
        $s1 = "exit\r\n";
        fwrite($con, $s1);
        fwrite($con, $s1);
        while (!feof($con)) {
            $out .= fread($con, 8192);
        }
        fclose($con);
        $out = explode("\r\n", $out);
        #echo "<pre>";
        foreach ($out as $value) {
            if(strncmp($value, 'EPON0/', 6) == 0){
                #var_dump($value);
                $arr_out = preg_split("/[\s,]+/",$value);
                if (count($arr_out) > 7){
                    $value = str_replace('N/A', 'not applicable', $value);
                }
                #$value = str_replace('N/A', 'not applicable', $value);
                $arr_out = preg_split("/[\s,]+/",$value);
                if (count($arr_out) > 7){
                    $res['Status'] = $arr_out[2];
                    $res['LastRegTime'] = $arr_out[3]." ".$arr_out[4];
                    $res['LastDeregTime'] = $arr_out[5]." ".$arr_out[6];
                    $res['LastDeregReason'] = $arr_out[7];
                    if ($dstoffset AND ($res['LastRegTime'] != 'not applicable') AND ($res['LastDeregTime'] != 'not applicable')){
                        if ($res['LastRegTime'] != 'not applicable' AND $res['LastRegTime'] != 'N/A'){
                            $date = new DateTime($res['LastRegTime']);
                            $date->modify('+1 hour');
                            $res['LastRegTime'] = $date->format('Y-m-d H:i:s');
                        }
                        if ($res['LastDeregTime'] != 'not applicable' AND $res['LastDeregTime'] != 'N/A'){
                            $date = new DateTime($res['LastDeregTime']);
                            $date->modify('+1 hour');
                            $res['LastDeregTime'] = $date->format('Y-m-d H:i:s');
                        }
                    }
                }else{
                    $res['Status'] = $arr_out[2];
                    $res['LastRegTime'] = $arr_out[3];
                    $res['LastDeregTime'] = $arr_out[4];
                    $res['LastDeregReason'] = $arr_out[5];
                    if ($dstoffset){
                        if ($res['LastRegTime'] != 'not applicable' AND $res['LastRegTime'] != 'N/A'){
                            $date = DateTime::createFromFormat('Y.m.d.H:i:s', $res['LastRegTime']);
                            $date->modify('+1 hour');
                            $res['LastRegTime'] = $date->format('Y-m-d H:i:s');
                        }
                        if ($res['LastDeregTime'] != 'not applicable' AND $res['LastDeregTime'] != 'N/A'){
                            $date = DateTime::createFromFormat('Y.m.d.H:i:s', $res['LastDeregTime']);
                            $date->modify('+1 hour');
                            $res['LastDeregTime'] = $date->format('Y-m-d H:i:s');
                        }
                    }
                }
            }
        }
        return $res;
    }else{
        return false;
    }
    
}

function GetRegData($host, $port, $tlog, $tpas, $onu_name) {
    $ar1 = explode(':', $onu_name);
    $sfp_int = $ar1[0];
    $onu_num = $ar1[1];
    $out = '';
    $dstoffset = date("I") == 1;
    if ($con = pfsockopen($host, $port, $errno, $errstr, 10)){
        $s1 = $tlog."\r\n";
        fwrite($con, $s1);
        $s1 = $tpas."\r\n";
        fwrite($con, $s1);
        sleep(1);
        $s1 = "enable\r\nterminal length 0\r\nterminal width 0\r\n";
        fwrite($con, $s1);
        fwrite($con, "show epon act int $sfp_int $onu_num \r\n");
        sleep(2);
        $s1 = "exit\r\n";
        fwrite($con, $s1);
        fwrite($con, $s1);
        while (!feof($con)) {
            $out .= fread($con, 8192);
        }
        fclose($con);
        $out = explode("\r\n", $out);
        foreach ($out as $value) {
            if(strncmp($value, 'EPON0/', 6) == 0){
                $arr_out = preg_split("/[\s,]+/",$value);
                if (count($arr_out) > 10){
                    $res['Status'] = $arr_out[2];
                    $res['LastRegTime'] = $arr_out[6]." ".$arr_out[7];
                    $res['LastDeregTime'] = $arr_out[8]." ".$arr_out[9];
                    $res['LastDeregReason'] = $arr_out[10];
                 $res['AliveTime'] = $arr_out[11];
                  if (isset($arr_out[12])){
                       $res['AliveTime'] = $res['AliveTime'].$arr_out[12];
                   }
                    if ($dstoffset){
                       $date = new DateTime($res['LastRegTime']);
                        $date->modify('+1 hour');
                        $res['LastRegTime'] = $date->format('Y-m-d H:i:s');
                        $date = new DateTime($res['LastDeregTime']);
                        $date->modify('+1 hour');
                        $res['LastDeregTime'] = $date->format('Y-m-d H:i:s');            
                    }
                }else{
                    $res['Status'] = $arr_out[2];
                    $res['LastRegTime'] = $arr_out[6];
                    $res['LastDeregTime'] = $arr_out[7];
                    $res['LastDeregReason'] = $arr_out[8];
                    $res['AliveTime'] = $arr_out[9];
                    if ($dstoffset){
                        $date = DateTime::createFromFormat('Y.m.d.H:i:s', $res['LastRegTime']);
                        $date->modify('+1 hour');
                        $res['LastRegTime'] = $date->format('Y-m-d H:i:s');
                        $date = DateTime::createFromFormat('Y.m.d.H:i:s', $res['LastDeregTime']);
                        $date->modify('+1 hour');
                        $res['LastDeregTime'] = $date->format('Y-m-d H:i:s');            
                    }
                }
            }
        }
        return $res;
    }else{
        return false;
    }
    
}

function GetGponOnuAct($host, $port, $tlog, $tpas, $onu_name) {
    $out = '';
    $dstoffset = date("I") == 1;
    if ($con = pfsockopen($host, $port, $errno, $errstr, 10)){
        $s1 = $tlog."\r\n";
        fwrite($con, $s1);
        $s1 = $tpas."\r\n";
        fwrite($con, $s1);
        sleep(1);
        $s1 = "enable\r\nterminal length 0\r\nterminal width 0\r\n";
        fwrite($con, $s1);
        fwrite($con, "show gpon interface $onu_name onu basic-info \r\n");
        sleep(2);
        $s1 = "exit\r\n";
        fwrite($con, $s1);
        fwrite($con, $s1);
        while (!feof($con)) {
            $out .= fread($con, 8192);
        }
        fclose($con);
        $out = explode("--------------------------------------------------------------", $out);
        $out[0] = explode("\r\n", $out[0]);
        foreach ($out[0] as $value) {
            if(strncmp($value, 'Online Duration', 15) == 0){
                $value = explode("Online Duration                ", $value);
                $res['AliveTime'] = $value[1];
            }
        }
        $out[1] = explode("\r\n", $out[1]);
        $out[1] = array_reverse($out[1]);
        $arr_out = preg_split("/[\s,]+/",$out[1][4]);
        $res['LastRegTime'] = $arr_out[2]." ".$arr_out[3];
        if ($arr_out[1] != "01"){
            $arr_out = preg_split("/[\s,]+/",$out[1][5]);
            $res['LastDeregTime'] = $arr_out[4]." ".$arr_out[5];
            if (array_key_exists(7, $arr_out)){
                $res['LastDeregReason'] = $arr_out[6]." ".$arr_out[7];
            }else{
                $res['LastDeregReason'] = $arr_out[6];
            }
            if ($dstoffset){
                $date = new DateTime($res['LastRegTime']);
                $date->modify('+1 hour');
                $res['LastRegTime'] = $date->format('Y-m-d H:i:s');
                $date = new DateTime($res['LastDeregTime']);
                $date->modify('+1 hour');
                $res['LastDeregTime'] = $date->format('Y-m-d H:i:s');            
            }
        }else{
            $res['LastDeregTime'] = "n/a";
            $res['LastDeregReason'] = "n/a";
        }
        return $res;
    }else{
        return false;
    }
    
}
function GetGponOnuInact($host, $port, $tlog, $tpas, $onu_name) {
    $out = '';
    if (date("I") == 1){
        $dstoffset = true;
    }
    if ($con = pfsockopen($host, $port, $errno, $errstr, 10)){
        $s1 = $tlog."\r\n";
        fwrite($con, $s1);
        $s1 = $tpas."\r\n";
        fwrite($con, $s1);
        sleep(1);
        $s1 = "enable\r\nterminal length 0\r\nterminal width 0\r\n";
        fwrite($con, $s1);
        fwrite($con, "show gpon interface $onu_name onu basic-info \r\n");
        sleep(2);
        $s1 = "exit\r\n";
        fwrite($con, $s1);
        fwrite($con, $s1);
        while (!feof($con)) {
            $out .= fread($con, 8192);
        }
        fclose($con);
        $out = explode("--------------------------------------------------------------", $out);
        $out[1] = explode("\r\n", $out[1]);
        $out[1] = array_reverse($out[1]);
        $arr_out = preg_split("/[\s,]+/",$out[1][4]);
        if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $arr_out[2])){
            $res['LastRegTime'] = $arr_out[2]." ".$arr_out[3];
            $res['LastDeregTime'] = $arr_out[4]." ".$arr_out[5];
            if (array_key_exists(7, $arr_out)){
                $res['LastDeregReason'] = $arr_out[6]." ".$arr_out[7];
            }else{
                $res['LastDeregReason'] = $arr_out[6];
            }
            if ($dstoffset){
                $date = new DateTime($res['LastRegTime']);
                $date->modify('+1 hour');
                $res['LastRegTime'] = $date->format('Y-m-d H:i:s');
                $date = new DateTime($res['LastDeregTime']);
                $date->modify('+1 hour');
                $res['LastDeregTime'] = $date->format('Y-m-d H:i:s');            
            }
        }else{
            $res['LastRegTime'] = "n/a";
            $res['LastDeregTime'] = "n/a";
            $res['LastDeregReason'] = "n/a";
        }
        $res['Status'] = "Innactive";
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
        $s1 = "enable\r\n";
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

function GponSNtoMAC($mac) {
    global $GPON_macs;
    foreach ($GPON_macs as $search => $replace) {
        if (strpos($mac, $search) === 0){
            $m1 = explode(':',$mac);
            $m2 = substr($m1[1],-6);
            $m2 = dechex(hexdec($m2) -1);
            $mac = $replace.$m2;
            break;
        }
    }
    return $mac;
}