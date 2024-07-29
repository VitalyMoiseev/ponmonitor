<?php

if(!isset($_GET["host"])){
    exit();
}
if(!isset($_GET["port"])){
    exit();
}
if(!isset($_GET["community"])){
    exit();
}
if(!isset($_GET["communityrw"])){
    exit();
}

$host = $_GET["host"];
$port = $_GET["port"];
$community = $_GET["community"];
$communityrw = $_GET["communityrw"];

require '../include/vars.php';
require '../include/database.php';
if(isset($_COOKIE["pm_username"])){
    $username = $_COOKIE["pm_username"];
}
if(isset($_COOKIE["pm_password"])){
    $password = $_COOKIE["pm_password"];
}
include '../include/auth_user.php';

if($port != 161){
    $host = $host.':'.$port;
}

$session = new SNMP(SNMP::VERSION_2C, $host, $community, 2000000, 20);
$session->quick_print = 1;
$oid = ".1.3.6.1.2.1.1.1.0";
$oidrw = ".1.3.6.1.2.1.1.5.0";
if ($resp = $session->get($oid)){
    echo '<font color="green">SNMP RO<strong> OK</strong></font><br>';
    echo "<pre>";
    echo $resp;
    echo "</pre>";
    $resp = $session->get($oidrw);
    $session->close();
    if($communityrw != ""){
        $session = new SNMP(SNMP::VERSION_2C, $host, $communityrw, 2000000, 20);
        if($session->set($oidrw, 's', $resp)){
            echo '<font color="green">SNMP RW<strong> OK</strong></font><br>';
            echo "<script type='text/javascript'>snmp_done = true;</script>";
            $session->close();
        }else{
            echo $session->getError();
            $session->close();
            echo "<script type='text/javascript'>snmp_done = false;</script>";
        }
    }else{
        echo "<script type='text/javascript'>snmp_done = true;</script>";
    }
}else{
    echo $session->getError();
    $session->close();
    echo "<script type='text/javascript'>snmp_done = false;</script>";
}
$mysqli_wb->close();
?>

    