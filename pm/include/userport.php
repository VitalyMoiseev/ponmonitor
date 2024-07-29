
<?php

$table = $tbl_pref.'olt';
$query = "SELECT * FROM $table WHERE Id = $olt_id;";
    
$result = $mysqli_wb->query($query);
    if($result->num_rows < 1){
        echo "Ошибка";
        echo "<script type='text/javascript'>window.close();</script>";
        exit();
    }
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->close();

    $name_sw = $row['name'];
    $community = $row['community'];
    $host = $row['host'];
if($row['snmp_port'] != 161){
    $host = $host.':'.$row['snmp_port'];
}
if (preg_match('/^([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2}$/', $port)){
    $mac = explode(':', $port);
    $port_show = "ONU: $port";
    $port_key = GetOnuKeyByMac($port, $host, $community);
    $port = $port_key;
}else{
    $mac = str_split(strtoupper(dechex($port)),2);
    $port_show = "ONU: ".$mac[0].":".$mac[1].":".$mac[2].":".$mac[3].":".$mac[4].":".$mac[5];
    $mac_id = "Hex-STRING: ".$mac[0]." ".$mac[1]." ".$mac[2]." ".$mac[3]." ".$mac[4]." ".$mac[5]." ";
    $mac_s = $mac[0].":".$mac[1].":".$mac[2].":".$mac[3].":".$mac[4].":".$mac[5];
    $port_key = GetOnuKeyByMac($mac_s, $host, $community);
    $port = $port_key;
}
    
echo "<table class=\"features-table\"><thead>";
echo "<tr><td class=\"grey\"><strong>SW: $name_sw/$host $port_show</strong> Port:$pkey</td></tr></thead>";
echo "<tbody><tr><td class=\"grey\">";
echo "Тип свитча: <strong>OLT</strong></td></tr><tr><td>";
echo "<span id=\"port_state\">One sec, please...</span>";
echo "</td></tr></tbody><tfoot>";
echo "<tr><td class=\"grey\"><button onclick=\"upd_cl();\" type=\"button\">Обновить</button></td><tr>";
echo "</tfoot></table>";


?>
<script src="<?php echo $protocol.$sitename; ?>/include/jquery-3.4.1.min.js" type="text/javascript"></script>
<script type='text/javascript'>
var time1 = setTimeout(function(){ window.close(); },600000);
function get_state(){
    var url1 = "<?php echo $protocol.$sitename; ?>/scripts/get_port_state.php?sw=<?php echo $olt_id; ?>&port=<?php echo $port; ?>&pkey=<?php echo $pkey; ?>";
    $('#port_state').load(url1);
}
function upd_cl(){
    get_state();
    clearInterval(time2);
    time2 = setInterval(function(){ get_state(); },5000);
}
get_state();
var time2 = setInterval(function(){ get_state(); },5000);
</script>
