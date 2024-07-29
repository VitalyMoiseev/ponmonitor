<?php
header("Content-Type: text/plain");
require '../include/vars.php';
require '../include/database.php';
# Check IP
if ( ! in_array($_SERVER['REMOTE_ADDR'], $vmagentIPs) ) {
    echo $_SERVER['REMOTE_ADDR'];
    exit();
}

echo "#PON metrics start at $starttime\n";

$query = "SELECT mac, pwr FROM onu WHERE present=1";
$result = $mysqli_wb->query($query);
while($row = $result->fetch_assoc()) {
    echo 'PON_ONU_pwr{onu="'.$row['mac'].'"} '.$row['pwr']."\n";
}
$mysqli_bil->close();
$mysqli_rad->close();
$mysqli_wb->close();
?>