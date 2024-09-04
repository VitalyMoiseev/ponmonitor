<?php

if(!isset($_GET["host"])){
    exit();
}
if(!isset($_GET["port"])){
    exit();
}
if(!isset($_GET["t_name"])){
    exit();
}
if(!isset($_GET["t_pass"])){
    exit();
}

$host = $_GET["host"];
$port = $_GET["port"];
$t_name = $_GET["t_name"];
$t_pass = $_GET["t_pass"];

require '../include/vars.php';
require '../include/database.php';
if(isset($_COOKIE["pm_username"])){
    $username = $_COOKIE["pm_username"];
}
if(isset($_COOKIE["pm_password"])){
    $password = $_COOKIE["pm_password"];
}
include '../include/auth_user.php';

if ($con = pfsockopen($host, $port, $errno, $errstr, 10)){
        $s1 = $t_name."\r\n";
        fwrite($con, $s1);
        $s1 = $t_pass."\r\n";
        fwrite($con, $s1);
        sleep(1);
        $s1 = "ena\r\n";
        fwrite($con, $s1);
        fwrite($con, "show ver \r\n");
        sleep(2);
        $resp = fread($con, 16536);
        $resp = explode('Switch#show ver', $resp);
        $resp = end($resp);
        fclose($con);
        echo '<font color="green">Telnet<strong> OK</strong></font><br>';
        echo "<pre>";
        echo $resp;
        echo "</pre>";
        echo "<script type='text/javascript'>
telnet_done = true;
</script>";
    }else{
        echo '<font color="red">telnet<strong> WRONG!</strong></font><br>';
        echo "<script type='text/javascript'>
telnet_done = false;
</script>";
    }
$mysqli_wb->close();
?>
    