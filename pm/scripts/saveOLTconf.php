<?php

if (!isset($_GET['olt_id'])){
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

$olt_id = $_GET['olt_id'];

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
    $oid = ".1.3.6.1.4.1.3320.20.15.1.1.0";
    if($session->set($oid, 'i', 1)){
        echo "<strong>Command OLT save config was send!</strong>\n";
    }else{
        echo "<strong><font color=\"red\">SNMP Error!</font></strong>";
    }

    $session->close();
}