<?php

if(!isset($_GET["host"])){
    exit();
}
if(!isset($_GET["snmp_port"])){
    exit();
}
if(!isset($_GET["t_name"])){
    exit();
}
if(!isset($_GET["t_pass"])){
    exit();
}
if(!isset($_GET["t_port"])){
    exit();
}
if(!isset($_GET["community"])){
    exit();
}
if(!isset($_GET["communityrw"])){
    exit();
}
if(!isset($_GET["olt_name"])){
    exit();
}
if(!isset($_GET["place"])){
    exit();
}
if(!isset($_GET["type"])){
    exit();
}
if(!isset($_GET["olt_id"])){
    exit();
}

$olt_id = $_GET["olt_id"];
$host = $_GET["host"];
$snmp_port = $_GET["snmp_port"];
$t_name = $_GET["t_name"];
$t_port = $_GET["t_port"];
$t_pass = $_GET["t_pass"];
$port = $_GET["port"];
$community = $_GET["community"];
$communityrw = $_GET["communityrw"];
$olt_name = $_GET["olt_name"];
$place = $_GET["place"];
$pon_type = $_GET["type"];

require '../include/vars.php';
require '../include/database.php';
require '../include/select_lang.php';
if(isset($_COOKIE["pm_username"])){
    $username = $_COOKIE["pm_username"];
}
if(isset($_COOKIE["pm_password"])){
    $password = $_COOKIE["pm_password"];
}
include '../include/auth_user.php';

$table = $tbl_pref.'olt';
if ($olt_id == 'new'){
    $query = "INSERT INTO $table (name, host, place, snmp_port, community, communityrw, telnet_port, telnet_name, telnet_password, status, last_act, type) VALUES ('$olt_name', '$host', '$place', $snmp_port, '$community', '$communityrw', $t_port, '$t_name', '$t_pass', 1, NOW(), $pon_type);";
}else{
    $query = "UPDATE $table SET name='$olt_name', host='$host', place='$place', type=$pon_type, snmp_port=$snmp_port, community='$community', communityrw='$communityrw', telnet_port=$t_port, telnet_name='$t_name', telnet_password='$t_pass', status=1, last_act=NOW() WHERE Id=$olt_id;";
}
if($mysqli_wb->query($query)){
    echo "<script type='text/javascript'>\n";
    echo "alert(\"OLT ".$labels['Saved']."!\" );";
    echo "location.reload();\n";
    echo "</script>";
}else{
    echo $mysqli_wb->error;
}

$mysqli_wb->close();
?>
    