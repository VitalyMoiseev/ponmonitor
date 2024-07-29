<?php

if(!isset($_GET["username"])){
    exit();
}
if(!isset($_GET["pass"])){
    exit();
}
if(!isset($_GET["splevel"])){
    exit();
}
$uname = $_GET["username"];
$pass = $_GET["pass"];
$splevel = intval($_GET["splevel"]);

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
$query = "INSERT INTO $table (username, password, splevel) VALUES ('$uname', '$password', $splevel);";
if($mysqli_wb->query($query)){
    echo "<script type='text/javascript'>\n";
    echo "alert(\"".$labels['User']." $uname ".$labels['set08']."!\" );";
    echo "location.reload();\n";
    echo "</script>";
}else{
    echo $mysqli_wb->error;
}
$mysqli_wb->close();