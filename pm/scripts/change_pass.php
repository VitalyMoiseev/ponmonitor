<?php

if(!isset($_GET["userid"])){
    exit();
}
if(!isset($_GET["pass"])){
    exit();
}

$userid = $_GET["userid"];
$pass = $_GET["pass"];

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

$password = password_hash($pass, PASSWORD_DEFAULT);

$table = $tbl_pref.'users';
$query = "UPDATE $table SET `password`='$password' WHERE Id=$userid";
if($mysqli_wb->query($query)){
    echo "<script type='text/javascript'>\n";
    echo "alert(\"".$labels['set10']."!\" );";
    echo "location.reload();\n";
    echo "</script>";
}else{
    echo $mysqli_wb->error;
}
$mysqli_wb->close();