<?php
$olt_id = $_GET["sw"];
$port_id = $_GET['port'];
$pkey = $_GET['pkey'];

include '../include/vars.php';
include '../include/database.php';
include '../include/functions.php';
require '../include/select_lang.php';
echo "<br>";
$table = $tbl_pref.'olt';
$query = "SELECT * FROM $table WHERE Id = $olt_id;";
    
$result = $mysqli_wb->query($query);
if($result->num_rows < 1){
    echo $labels['Error'];
    echo "<script type='text/javascript'>window.close();</script>";
    exit();
}
$row = $result->fetch_array(MYSQLI_ASSOC);
$host = $row['host'];
if($row['snmp_port'] != 161){
    $host = $host.':'.$row['snmp_port'];
}
$sw_snmp_com = $row['community'];

$session = new SNMP(SNMP::VERSION_2C, $host, $sw_snmp_com, 2000000, 20);
$session->quick_print = 1;
$sw_uptime = $session->get(".1.3.6.1.2.1.1.3.0");
    #$sw_uptime = snmp2_get($sw_ip, $sw_snmp_com, ".1.3.6.1.2.1.1.3.0", 2000000, 10);
if($sw_uptime){
    $oid_active_port = "1.3.6.1.4.1.3320.101.12.1.1.7.$port_id.$pkey";
    $oid_speed_port = "1.3.6.1.2.1.2.2.1.5.".$port_id;
    $oid_bytes_in = "1.3.6.1.2.1.2.2.1.10.".$port_id;
    $oid_bytes_out = "1.3.6.1.2.1.2.2.1.16.".$port_id;
    $oid_ucast_pkts_in = "1.3.6.1.2.1.2.2.1.11.".$port_id;
    $oid_ucast_pkts_out = "1.3.6.1.2.1.2.2.1.17.".$port_id;
    $oid_nucast_pkts_in = "1.3.6.1.2.1.2.2.1.12.".$port_id;
    $oid_nucast_pkts_out = "1.3.6.1.2.1.2.2.1.18.".$port_id;
    $act_port = $session->get($oid_active_port);
    $speed_port = snmp_rep($session->get($oid_speed_port));
    $bytes_in = snmp_rep($session->get($oid_bytes_in));
    $bytes_out = snmp_rep($session->get($oid_bytes_out));
    $ucast_pkts_in = snmp_rep($session->get($oid_ucast_pkts_out));
    $ucast_pkts_out = snmp_rep($session->get($oid_ucast_pkts_out));
    $nucast_pkts_in = snmp_rep($session->get($oid_nucast_pkts_out));
    $nucast_pkts_out = snmp_rep($session->get($oid_nucast_pkts_out));
    
    $pkts_in = $ucast_pkts_in + $nucast_pkts_in;
    $pkts_out = $ucast_pkts_out + $nucast_pkts_out;
    if($act_port == 'up' OR $act_port == 1){
        $act_port = "<strong><font color=\"green\">UP</font></strong>";
        if($row['type'] == 2){
            $oid_level = ".1.3.6.1.4.1.3320.101.10.5.1.5.$port_id";
            $level = snmp_rep($session->get($oid_level))/10;
            $act_port = "$act_port&nbsp;&nbsp;&nbsp;<strong><font color=\"green\">$level dB</font></strong>";
        }
    }else{
        $act_port = "<strong><font color=\"red\">DOWN</font></strong>";
    }
    echo $labels['port01'].": $act_port";
    $speed_port = $speed_port / 1000000;
    echo "<br>".$labels['port02'].": <strong>$speed_port Mb/s</strong><br>";

    echo "<table width=\"100%\">";
    echo "<tr><td colspan=\"4\"><strong>Статистика - ".$labels['bytes']."</strong></td></tr>";
    echo "<tr>";
    echo "<td>Принято:</td>";
    echo "<td><strong><font color=\"blue\">$bytes_in</font></strong></td>";
    echo "<td>Отправлено:</td>";
    echo "<td><strong><font color=\"blue\">$bytes_out</font></strong></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Скорость:</td>";
    if(isset($_COOKIE['count_in'])){
        $speed_in = ($bytes_in - $_COOKIE['count_in']) / 5;
    }
    SetCookie("count_in",$bytes_in,time()+6);
    echo "<td><strong><font color=\"blue\">$speed_in </font></strong>б/с</td>";
    echo "<td>Скорость:</td>";
    if(isset($_COOKIE['count_out'])){
        $speed_out = ($bytes_out - $_COOKIE['count_out']) / 5;
    }
    SetCookie("count_out",$bytes_out,time()+6);
    echo "<td><strong><font color=\"blue\">$speed_out </font></strong>б/с</td>";
    echo "</tr>";
    echo "<tr><td colspan=\"4\"><strong>Статистика - ".$labels['packets']."</strong></td></tr>";
    echo "<tr>";
    echo "<tr><td colspan=\"4\"><strong>Unicast</strong></td></tr>";
    echo "<tr>";
    echo "<td>Принято:</td>";
    echo "<td><strong><font color=\"blue\">$ucast_pkts_in</font></strong></td>";
    echo "<td>Отправлено:</td>";
    echo "<td><strong><font color=\"blue\">$ucast_pkts_out</font></strong></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Скорость:</td>";
    if(isset($_COOKIE['ucast_pkts_in'])){
        $speed_in = ($ucast_pkts_in - $_COOKIE['ucast_pkts_in']) / 5;
    }
    SetCookie("ucast_pkts_in",$ucast_pkts_in,time()+6);
    echo "<td><strong><font color=\"blue\">$speed_in </font></strong>пак/с</td>";
    echo "<td>Скорость:</td>";
    if(isset($_COOKIE['ucast_pkts_out'])){
        $speed_out = ($ucast_pkts_out - $_COOKIE['ucast_pkts_out']) / 5;
    }
    SetCookie("ucast_pkts_out",$ucast_pkts_out,time()+6);
    echo "<td><strong><font color=\"blue\">$speed_out </font></strong>пак/с</td>";
    echo "</tr>";
    echo "<tr><td colspan=\"4\"><strong>Broadcast/Multicast</strong></td></tr>";
    echo "<tr>";
    echo "<td>Принято:</td>";
    echo "<td><strong><font color=\"blue\">$nucast_pkts_in</font></strong></td>";
    echo "<td>Отправлено:</td>";
    echo "<td><strong><font color=\"blue\">$nucast_pkts_out</font></strong></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Скорость:</td>";
    if(isset($_COOKIE['nucast_pkts_in'])){
        $speed_in = ($nucast_pkts_in - $_COOKIE['nucast_pkts_in']) / 5;
    }
    SetCookie("nucast_pkts_in",$nucast_pkts_in,time()+6);
    echo "<td><strong><font color=\"blue\">$speed_in </font></strong>пак/с</td>";
    echo "<td>Скорость:</td>";
    if(isset($_COOKIE['nucast_pkts_out'])){
        $speed_out = ($nucast_pkts_out - $_COOKIE['nucast_pkts_out']) / 5;
    }
    SetCookie("nucast_pkts_out",$nucast_pkts_out,time()+6);
    echo "<td><strong><font color=\"blue\">$speed_out </font></strong>пак/с</td>";
    echo "</tr>";
    echo "<tr><td colspan=\"4\"><strong>All</strong></td></tr>";
    echo "<tr>";
    echo "<td>Принято:</td>";
    echo "<td><strong><font color=\"blue\">$pkts_in</font></strong></td>";
    echo "<td>Отправлено:</td>";
    echo "<td><strong><font color=\"blue\">$pkts_out</font></strong></td>";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Скорость:</td>";
    if(isset($_COOKIE['pkts_in'])){
        $speed_in = ($pkts_in - $_COOKIE['pkts_in']) / 5;
    }
    SetCookie("pkts_in",$pkts_in,time()+6);
    echo "<td><strong><font color=\"blue\">$speed_in </font></strong>пак/с</td>";
    echo "<td>Скорость:</td>";
    if(isset($_COOKIE['pkts_out'])){
        $speed_out = ($pkts_out - $_COOKIE['pkts_out']) / 5;
    }
    SetCookie("pkts_out",$pkts_out,time()+6);
    echo "<td><strong><font color=\"blue\">$speed_out </font></strong>пак/с</td>";
    echo "</tr>";
    echo "</table>";
    $session->close();
}else{
    echo "<strong><font color=\"red\"> ".$labels['SwDontResp']."!</font></strong><br><br><br><br><br><br><br><br><br><br><br>";
}  
$mysqli_wb->close();

?>
