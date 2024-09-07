
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
if (!isset($request[4])){
    $period = 10;
}else{
    $period = $request[4];
}
$url1 = "$protocol$sitename/$request[0]/$request[1]/$request[2]/$request[3]";
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
    $port_show = "Порт: $port";
}
echo "<table class=\"features-table\"><thead>";
echo "<tr><td colspan=\"2\" class=\"grey\"><b>SW: $name_sw/$host $port_show</b> $port_key</td></tr></thead>";
echo "<tbody><tr><td class=\"grey\">";
echo "<b>$type_sw</b></td><td class=\"grey\">";
echo "<input type=\"radio\" id=\"5s\" name=\"period\" value=\"5\"";
if ($period == 5){
    echo " checked";
}
echo "><label for=\"5s\">5s</label>&nbsp";
echo "<input type=\"radio\" id=\"10s\" name=\"period\" value=\"10\"";
if ($period == 10){
    echo " checked";
}
echo "><label for=\"10s\">10s</label>&nbsp";
echo "<input type=\"radio\" id=\"30s\" name=\"period\" value=\"30\"";
if ($period == 30){
    echo " checked";
}
echo "><label for=\"30s\">30s</label>&nbsp";
echo "<input type=\"radio\" id=\"1m\" name=\"period\" value=\"60\"";
if ($period == 60){
    echo " checked";
}
echo "><label for=\"1m\">1m</label>&nbsp";
echo "<input type=\"radio\" id=\"5m\" name=\"period\" value=\"300\"";
if ($period == 300){
    echo " checked";
}
echo "><label for=\"5m\">5m</label>&nbsp";
echo "<button onclick=\"upd_cl();\" type=\"button\">Обновить</button>";
echo "</td></tr><tr><td colspan=\"2\" >";
echo "<span id=\"port_state\">One sec, please...</span>";
echo "</td></tr></tbody><tfoot>";
echo "<tr><td class=\"grey\"colspan=\"2\" ><span class=\"seconds\">$period</span></td><tr>";
echo "</tfoot></table>";


?>
<script type='text/javascript'>
var time1 = setTimeout(function(){ window.close(); },600000);
function get_state(){
    var url1 = "<?php echo $protocol.$sitename; ?>/scripts/get_port_state.php?sw=<?php echo $id_sw; ?>&port=<?php echo $port; ?>&period=<?php echo $period; ?>";
    $('#port_state').load(url1);
    var seconds = <?php echo $period; ?>;
    var seconds_timer_id = setInterval(function() {
        if (seconds > 0) {
            seconds --;
            $(".seconds").text(seconds);
        }
    }, 1000);
}
function upd_cl(){
    var radios = document.getElementsByName('period');
    var nosel = true;
    for (var i = 0, length = radios.length; i < length; i++) {
        if (radios[i].checked) {
            nosel = false;
            period = (radios[i].value);
            url1 = "<?php echo "$url1/"; ?>" + period;
            window.location.href = url1;
        break;
        }
    }
    //get_state();
    //clearInterval(time2);
    //time2 = setInterval(function(){ get_state(); },5000);
}
get_state();
var time2 = setInterval(() => get_state(), <?php echo $period * 1000; ?>);
</script>
