<?php

if(!isset($_GET["userid"])){
    exit();
}

$userid = $_GET["userid"];
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

$table = $tbl_pref.'users';
$query = "DELETE FROM $table WHERE Id=$userid";
if($mysqli_wb->query($query)){
    echo "<script type='text/javascript'>\n";
    echo "alert(\"".$labels['User']." ".$labels['deleted']."!\" );";
    echo "location.reload();\n";
    echo "</script>";
}else{
    echo $mysqli_wb->error;
}
$mysqli_wb->close();