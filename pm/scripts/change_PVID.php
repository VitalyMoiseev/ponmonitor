<?php

if (!isset($_GET['oltid'])){
    exit();
}
if (!isset($_GET['onukey'])){
    exit();
}
if (!isset($_GET['pvid'])){
    exit();
}
require '../include/vars.php';
require '../include/database.php';
require '../include/select_lang.php';
require '../include/pon_functions.php';
if(isset($_COOKIE["pm_username"])){
    $username = $_COOKIE["pm_username"];
}
if(isset($_COOKIE["pm_password"])){
    $password = $_COOKIE["pm_password"];
}
include '../include/auth_user.php';

$olt_id = $_GET['oltid'];
$onukey = $_GET['onukey'];
$port = isset($_GET['port']) ? $_GET['port'] : 1;
$pvid = $_GET['pvid'];

$table = $tbl_pref.'olt';
$query = "SELECT * FROM $table WHERE Id = $olt_id;";
    
$result = $mysqli_wb->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$result->close();

$communityrw = $row['communityrw'];
$host = $row['host'];
if($row['snmp_port'] != 161){
    $host = $host.':'.$row['snmp_port'];
}

if($session = new SNMP(SNMP::VERSION_2C, $host, $communityrw, 2000000, 20)){
    $oid = ".1.3.6.1.4.1.3320.101.12.1.1.3.$onukey.$port";
    if($session->set($oid, 'i', $pvid)){
        echo "<strong>PVID successfully changed!</strong>\n";
        echo "Do not forget to save OLT configuration!";
    }else{
        echo "<strong><font color=\"red\">SNMP Error!</font></strong>";
    }

    $session->close();
}
