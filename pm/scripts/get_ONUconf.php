<?php

if (!isset($_GET['olt_id'])){
    exit();
}
if (!isset($_GET['onu_name'])){
    exit();
}

require '../include/vars.php';
require '../include/database.php';
if(isset($_COOKIE["pm_username"])){
    $username = $_COOKIE["pm_username"];
}
if(isset($_COOKIE["pm_password"])){
    $password = $_COOKIE["pm_password"];
}
include '../include/auth_user.php';

$olt_id = $_GET['olt_id'];
$onu_name = $_GET['onu_name'];

$table = $tbl_pref.'olt';
$query = "SELECT * FROM $table WHERE Id = $olt_id;";
    
$result = $mysqli_wb->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$result->close();

$host = $row['host'];
$port = $row['telnet_port'];
    
$tlog = $row['telnet_name'];
$tpas = $row['telnet_password'];


if ($con = pfsockopen($host, $port, $errno, $errstr, 10)){
    $s1 = $tlog."\r\n";
    fwrite($con, $s1);
    $s1 = $tpas."\r\n";
    fwrite($con, $s1);
    sleep(1);
    $s1 = "ena\r\n";
    fwrite($con, $s1);
    fwrite($con, "show running-config interface $onu_name \r\n");
    sleep(2);
    $out = fread($con, 16536);
    $out = explode("Current configuration:\r\n!\r\n", $out);
    $out = end($out);
    fclose($con);
    echo "<div style=\"text-align:left; padding:0 10px; margin:1% 15% 1% 1%; border: 1px solid black; border-radius: 3px;\">";
    echo "<pre>\n";
    $arr_out = explode("\r\n", $out);
    array_pop($arr_out);
    foreach ($arr_out as $value) {
        echo "$value\n";
    }
    echo "</pre>";
    echo "</div>\n";
}else{
    echo "OLT offline!";
}
$mysqli_wb->close();
?>
