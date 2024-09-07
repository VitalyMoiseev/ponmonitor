<?php

if (!isset($_GET['thost'])){
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

$onu_name = $_GET['onu_name'];

$host = $_GET['thost'];
$port = $_GET['tport'];
$tlog = $_GET['tlog'];
$tpas = $_GET['tpas'];

if ($con = pfsockopen($host, $port, $errno, $errstr, 10)){
    $s1 = $tlog."\r\n";
    fwrite($con, $s1);
    $s1 = $tpas."\r\n";
    fwrite($con, $s1);
    sleep(1);
    $s1 = "enable\r\n";
    fwrite($con, $s1);
    fwrite($con, "show interface $onu_name \r\n");
    sleep(2);
    $out = fread($con, 16536);
    $out = explode("show interface $onu_name \r\n", $out);
    $out = end($out);
    fclose($con);
    echo '<table class="features-table" width="100%"><thead>';
    echo '<tr><td class="grey">Show interface '.$onu_name.' result</td></tr></thead><tbody>';
    echo '<tr><td class="grey">';
    echo "<div style=\"text-align:left; padding:0 10px; margin:1% 15% 1% 1%; border: 1px solid black; border-radius: 3px;\">";
    echo "<pre>\n";
    $arr_out = explode("\r\n", $out);
    array_pop($arr_out);
    foreach ($arr_out as $value) {
        echo "$value\n";
    }
    echo "</pre>";
    echo "</div>\n";
    echo '</td></tr>';
    echo '</tbody><tfoot><tr><td class="grey"><a href="javascript:void();" onclick="hide_me();">[Hide]</a></td></tr></tfoot></table>';
}else{
    echo "OLT offline!";
}
$mysqli_wb->close();
?>
