<?php

if(!isset($_GET["olt_id"])){
    exit();
}

$olt_id = $_GET["olt_id"];

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
$query = "DELETE FROM $table WHERE Id = $olt_id";
if($mysqli_wb->query($query)){
    echo "OLT удален!";
    echo "<script type='text/javascript'>\n";
    echo "alert(\"OLT ".$labels['deleted']."!\" );";
    echo "location.reload();\n";
    echo "</script>";
}else{
    echo $mysqli_wb->error;
}

$mysqli_wb->close();
?>
    