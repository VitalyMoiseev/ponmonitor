<?php

$table = $tbl_pref.'users';
$usernameForReq = $mysqli_wb->real_escape_string($username);
$query = "SELECT * FROM $table WHERE username = '$usernameForReq' LIMIT 1;";
$result = $mysqli_wb->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$result->close();
if(password_verify($password, $row['password'])){
    $spLevel = $row['splevel'];
    SetCookie("pm_username",$username,0,"/");
    SetCookie("pm_password",$password,0,"/");
    SetCookie("pm_spLevel",$username,0,"/");
    
}else{
    $mysqli_wb->close();
    $url = "Location: $protocol$sitename/enter/error";
    header($url);;
    exit;
}

?>