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
if ($_GET['olt_type'] == 'GPON'){
    $command = "show gpon interface $onu_name onu basic-info";
}else{
    $command = "show epon interface $onu_name onu ctc basic-info";
}

if ($con = pfsockopen($host, $port, $errno, $errstr, 10)){
    $s1 = $tlog."\r\n";
    fwrite($con, $s1);
    $s1 = $tpas."\r\n";
    fwrite($con, $s1);
    sleep(1);
    $s1 = "enable\r\nterminal length 0\r\nterminal width 0\r\n";
    fwrite($con, $s1);
    fwrite($con, "$command \r\n");
    sleep(2);
    $s1 = "exit\r\n";
    fwrite($con, $s1);
    fwrite($con, $s1);
    while (!feof($con)) {
        $out .= fread($con, 8192);
    }
    fclose($con);
    $out = explode("$command \r\n", $out);
    $out = end($out);
    echo '<table class="features-table" width="100%"><thead>';
    echo '<tr><td class="grey">Show '.$onu_name.' ONU basic-info</td></tr></thead><tbody>';
    echo '<tr><td class="grey">';
    echo "<div style=\"text-align:left; padding:0 10px; margin:1% 15% 1% 1%; border: 1px solid black; border-radius: 3px;\">";
    echo "<pre>\n";
    $arr_out = explode("\r\n", $out);
    array_pop($arr_out);
    array_pop($arr_out);
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
