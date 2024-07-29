<?php
if ($username == "vm"){
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
};

$query = "SELECT
  `switches`.`name`,
  `switches`.`community`,
  `switches`.`us_id`,
  `switches`.`host`,
  `switch_type`.`type` 
FROM
  `switches` INNER JOIN `switch_type` ON `switch_type`.`id` = `switches`.`type_id`
WHERE
  `switches`.`id` = $olt_id";
    
$result = $mysqli_bil->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$result->close();
$olt_name = $row['name'];
$olt_host = $row['host'];
$olt_us_id = $row['us_id'];
if ($row['type'] == 3){
    $olt_gpon = true;
    $sfp_name = 'GPON0/';
    $olt_type = 'GPON';
}else{
    $olt_gpon = false;
    $sfp_name = 'EPON0/';
    $olt_type = 'EPON';
}

$onu_name = "$sfp_name$sfp_s:$onu_n";

$ask = "cat=device&action=get_data&object_type=olt&object_id=$olt_us_id";
$resp = ask_userside($ask);
$olt_data = $resp['data'][$olt_us_id];
//$query_uuu = "SELECT * FROM olt WHERE us_id=$olt_us_id";
//$result_uuu = $mysqli_wb->query($query_uuu);
//$olt_data = $result_uuu->fetch_array(MYSQLI_ASSOC);
//$result_uuu->close();
##########

echo '<table class="features-table" width="100%"><thead><tr><td class="grey"><div align="left">';
echo "<b>";
echo $olt_name;
echo " | ";
echo "<a href=\"http://$olt_host\" target=\"_blank\">";
echo $olt_host;
echo "</a>";
echo " | ";
echo $olt_data['nazv'];
echo " | ";
echo $olt_data['location'];
echo '</td><td class="grey"><a href="/'.$labels['billing'].'/PON">'.$labels['pon04'].'</a></td></tr></thead><tfoot><tr><td class="grey" colspan="2"><div align="left">';
echo show_sfp($olt_id);
echo '</div></td></tr></tfoot></table>';
    
$community = $row['community'];
$communityrw = $olt_data['com_private'];
$host = $row['host'];

$query = "SELECT * FROM onu WHERE olt = $olt_id AND onu_name = '$onu_name' AND present=1 AND mac='$onu_mac'";

$result = $mysqli_wb->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$comment = $row['comment'];
$userid = $row['userid'];
$mac = $row['mac'];
$onuId = $row['Id'];
#$ask = "cat=device&action=get_ont_data&id=".str_replace(':', '', $mac);
#$resp = ask_userside($ask);
#$onu_us = $resp['data'];
#if (isset($onu_us['iface_number'])){
#    $key = intval($onu_us['iface_number']);
#}else{
#    $key = 0;
#}
$key = 0;
if ($key == 0){
    $key = GetOnuKeyByMac($mac, $host, $community, $olt_gpon);
}
if(!$onu_status = GetOnuStatus($key, $host, $community, $olt_gpon)){
    if ($onu_status != 0){
        $onu_status = 6;
    }else{
        $onu_status = 0;
    }
}
$stat_n_ar = array('authenticated', 'registered', 'deregistered', 'auto-configured', 'lost', 'standby', 'OLT offline');
if ($onu_status == 3){
    $onu_s = GetOnuPwr($key, $host, $community, $olt_gpon);
    $onu_dist = GetOnuDist($key, $host, $community, $olt_gpon);
    # write to DB
    $query = "UPDATE onu SET pwr = '".$onu_s."', status=1, last_act=NOW() WHERE Id=".$row['Id'];
    ##
    #$onu_eth_ena = GetOnuEthEna($key, $host, $community);
    #$onu_eth_state = GetOnuEthState($key, $host, $community);
    #$onu_eth_pvid = GetOnuPvid($key, $host, $community);
    $pwr = $onu_s;
    if ($olt_data['nazv'] != 'BDCOM OLT P3310'){
        $pwr_from_onu = GetPwrFromOnu($key, $host, $community, $olt_gpon);
    }else{
        $pwr_from_onu = false;
    }
    #$onu_s = $onu_s.' Db';
    $tdclass = "green";
    if ($olt_gpon){
        $onuRegData = GetGponOnuAct($olt_data['host'], 23, $olt_data['telnet_login'], $olt_data['telnet_pass'], $onu_name);
    }else{
        $onuRegData = GetRegData($olt_data['host'], 23, $olt_data['telnet_login'], $olt_data['telnet_pass'], $onu_name);
    }
}elseif($onu_status < 2 AND $olt_gpon){
    $pwr = 0;
    $pwr_from_onu = 0;
    $onu_s = 'OFFLINE';
    $onu_dist = "";
    $query = "UPDATE onu SET status=0 WHERE Id=".$row['Id'];
    $tdclass = "red";
    $onuDeregData = GetGponOnuInact($olt_data['host'], 23, $olt_data['telnet_login'], $olt_data['telnet_pass'], $onu_name);
}elseif($onu_status == 2){
    $pwr = 0;
    $pwr_from_onu = 0;
    $onu_s = 'OFFLINE';
    $onu_dist = "";
    $query = "UPDATE onu SET status=0 WHERE Id=".$row['Id'];
    $tdclass = "red";
    $onuDeregData = GetDeregData($olt_data['host'], 23, $olt_data['telnet_login'], $olt_data['telnet_pass'], $onu_name);
}elseif($onu_status !=6){
    $pwr = 0;
    $onu_s = 'OFFLINE';
    $onu_dist = "";
    $query = "UPDATE onu SET status=0 WHERE Id=".$row['Id'];
    $tdclass = "red";
    $onuRegData = GetRegData($olt_data['host'], 23, $olt_data['telnet_login'], $olt_data['telnet_pass'], $onu_name);
}else{
    $pwr = 0;
    $pwr_from_onu = 0;
    $onu_s = 'OFFLINE';
    $onu_dist = "";
    $query = "SELECT 1;";
    $tdclass = "red";
}
$mysqli_wb->query($query);
echo '<table class="features-table" width="100%"><thead><tr>';
echo '<td class="grey" colspan="6">';
echo $onu_name;
echo '</td></tr></thead><tbody><tr>';
echo '<td class="grey" colspan="6"><span style="font-family: monospace; font-weight: bold; font-size: 125%;">';
$mac_show = $mac;
$mac_inv = $mac;
if ($olt_gpon){
    foreach ($GPON_Vendors as $search => $replace) {
        $mac_show = str_replace($search, $replace, $mac_show);
    }
    $mac_inv = GponSNtoMAC($mac_inv);
}
echo $mac_show;
echo '</span>';
echo " <a href=\"$usInvFindStr$mac_inv\" target=\"_blank\"><sup>[склад]</sup></a>";
echo '</td></tr><tr>';
echo '<td class="grey"><b>'.$labels['Stat'].'</b></td>';
echo '<td class="grey"><b>'.$labels['pon05'].' ONU</b></td>';
echo '<td class="grey"><b>'.$labels['pon05'].' OLT</b></td>';
echo '<td class="grey"><b>'.$labels['Dist'].'</b></td>';
echo '<td class="grey"><b>'.$labels['Com'].'</b></td>';
echo '<td class="grey"><b>'.$labels['IDKlient'].'</b></td>';
echo "</tr></thead><tbody><tr><td class=\"$tdclass\">";
if ($detect->isMobile()){
    echo "<small>";
}else{
    echo "<b>";
}
echo $stat_n_ar[$onu_status];
if ($detect->isMobile()){
    echo "</small>";
}else{
    echo "</b>";
}
echo "</td><td class=\"$tdclass\"><a href=\"#\" onclick=\"openONUpwrGraf('$mac'); return false;\" title=\"Show power graf\"><b>$onu_s</b>";
if($pwr != 0){
    echo " Db";
}
echo "</a></td><td class=\"$tdclass\">";
if ($pwr_from_onu){
    echo "<b>$pwr_from_onu</b> Db";
}
echo "</td><td class=\"$tdclass\"><b>$onu_dist</b>";
if($pwr != 0){
    echo " m";
}
echo "</td><td class=\"$tdclass\"><strong id=\"comment_t\">$comment</strong> <input id=\"com_new\" type=\"text\" value=\"$comment\" hidden>";
echo "<td class=\"$tdclass\"><span id='userid_t'>";
if (!is_null($userid)){
    $query = "SELECT name FROM user WHERE Id=$userid";
    $result1 = $mysqli_bil->query($query);
    if (mysqli_num_rows($result1) > 0) {
        $row1 = mysqli_fetch_array($result1);
        $username = $row1['name'];
        echo "$username <a href=\"#\" onclick=\"openuser($userid); return false;\">$userid</a>";
        $query = "SELECT acctstoptime FROM radacct WHERE username = '$username' ORDER BY acctstarttime DESC LIMIT 1;";
        $result1 -> close();
        $result1 = $mysqli_rad->query($query);
        if (mysqli_num_rows($result1) > 0) {
            echo " | <a href=\"#\" onclick=\"openuserwork($userid); return false;\">";
            $row1 = mysqli_fetch_array($result1);
            if (is_null($row1['acctstoptime'])){
                echo '<b style="color: green;">online</b>';
            }else{
                echo '<b style="color: red;">offline</b> ';
            }
            echo "</a>";
        }
    }
}
echo " </span><input id=\"userid_new\" type=\"text\" value=\"$userid\" hidden>";
echo '</tr>';
echo '<tr><td></td><td colspan="2"><small><a href="javascript:void();" onclick="document.location.reload(true);">['.$labels['Refresh'].']</a></small></td>';
echo '<td></td><td>';
echo '<small><div id="editcmd"><a href="javascript:void();" onclick="editcomment();">['.$labels['Edit'].']</a>';
$query = "SELECT * FROM onu_comment_history WHERE mac = '$mac';";
$result = $mysqli_wb->query($query);
if($result->num_rows > 0 ){
    echo ' | <a href="javascript:void();" onclick="showcomhist();">['.$labels['History'].']</a>';
}

echo '</div></small></td>';
echo '<td><small><div id="edituserid"><a href="javascript:void();" onclick="edituserid();">['.$labels['Edit'].']</a>';
if(!is_null($userid)){
    echo "&nbsp;|&nbsp;<a href=\"javascript:void();\" onclick=\"saveonutouser($userid);\">[ONU &rarr; Юзер]</a>";
}
echo '</div></small><div id="editcmdu"></div></td>';

echo '</tr>';
if ($onu_status < 3){
    echo "<tr><td colspan=6 class=\"$tdclass\">".$labels['Stat']." ONU: <b>".$onuDeregData['Status']."</b>&nbsp;&nbsp;|&nbsp;&nbsp;";
    echo $labels['pon06'].': <b>'.$onuDeregData['LastRegTime'].'</b>&nbsp;&nbsp;|&nbsp;&nbsp;';
    echo $labels['pon07'].': <b>'.$onuDeregData['LastDeregTime'].'</b>&nbsp;&nbsp;|&nbsp;&nbsp;';
    echo $labels['pon08'].': <b>'.$onuDeregData['LastDeregReason'].'</b> <a href="javascript:void();" onclick="show_statuses('.$olt_gpon.');">[info]</a></td></tr>';
}elseif ($onu_status != 6){
    echo "<tr><td colspan=6 class=\"$tdclass\">Alive time: <b>".$onuRegData['AliveTime']."</b>&nbsp;&nbsp;|&nbsp;&nbsp;";
    echo $labels['pon06'].': <b>'.$onuRegData['LastRegTime'].'</b>&nbsp;&nbsp;|&nbsp;&nbsp;';
    echo $labels['pon07'].': <b>'.$onuRegData['LastDeregTime'].'</b>&nbsp;&nbsp;|&nbsp;&nbsp;';
    echo $labels['pon08'].': <b>'.$onuRegData['LastDeregReason'].'</b> <a href="javascript:void();" onclick="show_statuses('.$olt_gpon.');">[info]</a></td></tr>';
}

echo '</tbody><tfoot><tr>';
echo '<td class="grey" colspan="6">';
#echo '|&nbsp;&nbsp;&nbsp;<b><a href="javascript:void();" onclick="show_signal_history(\''.$mac.'\','.$key.')">'.$labels['HistPwr'].' (Userside)</a></b>&nbsp;&nbsp;&nbsp;';
echo '|&nbsp;&nbsp;&nbsp;<b><a href="javascript:void();" onclick="show_pwr_history(\''.$mac.'\')">'.$labels['HistPwr'].' (billing)</a></b>&nbsp;&nbsp;&nbsp;|';
if ($onu_s !='OFFLINE'){
    echo '&nbsp;&nbsp;&nbsp;<b><a href="javascript:void();" onclick="show_FDB(\''.$olt_us_id.'\',\''.$onu_name.'\', \''.$mac.'\', '.$key.', '.$onuId.')">FDB '.$labels['table'].'</a></b>&nbsp;&nbsp;&nbsp;|';
}else{
    echo '';
}
if ($spLevel == 0 AND $communityrw !=""){
    echo '&nbsp;&nbsp;&nbsp;<b><a href="javascript:void();" onclick="manage_ONU_OLT(\''.$olt_id.'\', '.$key.');">'.$labels['Manage'].' ONU '.$labels['and'].' OLT</a></b>&nbsp;&nbsp;&nbsp;|';
}
echo "&nbsp;&nbsp;&nbsp;<b><a href=\"javascript:void();\" onclick=\"show_int();\">Show interface</a></b>&nbsp;&nbsp;&nbsp;|\n";
echo "&nbsp;&nbsp;&nbsp;<b><a href=\"javascript:void();\" onclick=\"showONUbasic($olt_id, '$onu_name', '$olt_type');\">Show ONU basic-info</a></b>&nbsp;&nbsp;&nbsp;|\n";
echo "&nbsp;&nbsp;&nbsp;<b><a href=\"javascript:void();\" onclick=\"showONUtraf('$onu_mac');\">Show ONU traffic</a></b>&nbsp;&nbsp;&nbsp;|\n";
echo '</td></tr></tfoot></table>';
# Eth ports
if ($onu_s != 'OFFLINE'){
    $onu_eth_ena = GetOnuEthEna($key, $host, $community, $olt_gpon);
    $onu_eth_state = GetOnuEthState($key, $host, $community, $olt_gpon);
    $onu_eth_pvid = GetOnuPvid($key, $host, $community, $olt_gpon);
    echo '<table class="features-table" width="100%"><thead><tr>';
    echo '<td class="grey">Ethernet порт ID</td>';
    echo '<td class="grey">State</td>';
    echo '<td class="grey">PVID</td>';
    echo '<td class="grey">Manage</td>';
    echo '</tr></thead>';
    echo '<tbody>';
    foreach ($onu_eth_ena as $pkey => $pstate) {
        echo "<tr><td>$pkey</td>";
        if ($pstate != '1' AND $pstate != 'up'){
            echo '<td><font color="red"><b>disabled</b></font></td>';
        }else{
            if ($onu_eth_state[$pkey] == '1' OR $onu_eth_state[$pkey] == 'up'){
                echo '<td><font color="green"><b>UP</b></font></td>';
            } else {
                echo '<td><font color="grey"><b>DOWN</b></font></td>';
            }
        }
        if ($olt_gpon){
            $onu_eth_pvid[$pkey] = GetGponVLANProfilePvid($onu_eth_pvid[$pkey], $host, $community);
        }
        echo '<td>'.$onu_eth_pvid[$pkey].'</td>';
        echo '<td><small><a href="javascript:void();" onclick="sh_port_st('.$olt_id.', \''.$onu_mac.'\', '.$pkey.');">['.$labels['Stat'].']</a></small>';
        if ($spLevel == 0 AND $communityrw !="" AND !$olt_gpon){
            echo '&nbsp;<small><span id="editvlan"><a href="javascript:void();" onclick="changePVID('.$pkey.', '.$onu_eth_pvid[$pkey].');">['.$labels['Edit'].' PVID]</a></span></small>';
        }
        echo '</td>';
    }
    echo '</tbody><tfoot><tr><td class="grey" colspan="4">&nbsp;</td></tr></tfoot></table>';
}
#
echo '<div id="onu_data_field"></div>';

$mactouser = str_replace(":", "", $mac);
if (!$olt_gpon){
    $mactouser = hexdec($mactouser);
}

?>
<script type='text/javascript'>
function sh_port_st(sw, port){
    var url1 = '/<?php echo $labels['billing']; ?>/switchport/' + sw + '/' + port;
    NewWin6 = window.open(url1,'w3','width=600,height=520,location=no,toolbar=no,menubar=no,status=no,scrollbars=yes,resizable=no');
}
function openuser(userid){
    var url1 = "/<?php echo $labels['billing']; ?>/user/" + userid;
    window.open(url1,'w1','width=600,height=500,location=no,toolbar=no,menubar=no,status=no,scrollbars=yes,resizable=yes');
}
function editcomment(){
    $('#com_new').show();
    document.getElementById('editcmd').innerHTML = '<a href="javascript:void();" onclick="save_com();">[<?php echo $labels['Save']; ?>]</a> | <a href="javascript:void();" onclick="reset_com();">[<?php echo $labels['Esc']; ?>]</a>';
}
function edituserid(){
    $('#userid_new').show();
    document.getElementById('edituserid').innerHTML = '<a href="javascript:void();" onclick="save_userid();">[<?php echo $labels['Save']; ?>]</a> | <a href="javascript:void();" onclick="reset_userid();">[<?php echo $labels['Esc']; ?>]</a>';
}
function save_com(){
    $('#com_new').hide();
    var new_comm = document.getElementById('com_new').value;
    new_comm = encodeURIComponent(new_comm);
    var url1 = "/scripts/change_onu_comment.php?id=<?php echo $onuId ?>&comment=" + new_comm;
    $('#editcmd').load(url1);
    //sleep(1000);
    document.location.reload(true);
}
function reset_com(){
    $('#com_new').hide();
    document.getElementById('editcmd').innerHTML = '<a href="javascript:void();" onclick="editcomment();">[<?php echo $labels['Edit']; ?>]</a>';
}
function save_userid(){
    $('#userid_new').hide();
    var new_userid = document.getElementById('userid_new').value;
    new_userid = encodeURIComponent(new_userid);
    var url1 = "/scripts/change_onu_userid.php?id=<?php echo $onuId ?>&userid=" + new_userid;
    $('#edituserid').load(url1);
    //sleep(1000);
    document.location.reload(true);
}
function reset_userid(){
    $('#userid_new').hide();
    document.getElementById('edituserid').innerHTML = '<a href="javascript:void();" onclick="edituserid();">[<?php echo $labels['Edit']; ?>]</a>';
}
function show_signal_history(mac, key){
        document.getElementById('onu_data_field').innerHTML = '<b>working...</b>';
        var url1 = "/scripts/get_us_pon_history.php?mac=" + mac + "&key=" + key;
        $('#onu_data_field').load(url1);
}
function show_statuses(gpon){
        document.getElementById('onu_data_field').innerHTML = '<b>working...</b>';
        var url1 = "/scripts/get_pon_statuses.php?gpon=" + gpon;
        $('#onu_data_field').load(url1);
}
function show_pwr_history(mac){
        document.getElementById('onu_data_field').innerHTML = '<b>working...</b>';
        var url1 = "/scripts/get_pon_history.php?mac=" + mac;
        $('#onu_data_field').load(url1);
}
function changePVID(port, curpvid){
    document.getElementById('onu_data_field').innerHTML = '<b>working...</b>';
    var onu_name = encodeURIComponent('<?php echo $onu_name; ?>');
    var thost = encodeURIComponent('<?php echo $olt_data['host']; ?>');
    var tlog = encodeURIComponent('<?php echo $olt_data['telnet_login']; ?>');
    var tpas = encodeURIComponent('<?php echo $olt_data['telnet_pass']; ?>');
    var comrw = encodeURIComponent('<?php echo $communityrw; ?>');
    var host = encodeURIComponent('<?php echo $host; ?>');
    var url1 = "/scripts/select_PVID.php?onuid=<?php echo $onuId ?>&port=" + port + "&curpvid=" + curpvid + "&onukey=<?php echo $key; ?>&onu_name=" + onu_name + "&thost=" + thost + "&tlog=" + tlog + "&tpas=" + tpas + "&host=" + host + "&comrw=" + comrw;
    $('#onu_data_field').load(url1);
}
function show_FDB(param1, param2, param3, param4, param5){
    document.getElementById('onu_data_field').innerHTML = '<b>working...</b>';
    var url1 = "/scripts/get_pon_FDB.php?us_id=" + param1 + "&onu=" + param2 + "&onu_mac=" + param3 + "&key=" + param4 + "&onuid=" + param5;
    $('#onu_data_field').load(url1);
}
function show_int(){
    document.getElementById('onu_data_field').innerHTML = '<b>working...</b>';
    var onu_name = encodeURIComponent('<?php echo $onu_name; ?>')
    var thost = encodeURIComponent('<?php echo $olt_data['host']; ?>');
    var tlog = encodeURIComponent('<?php echo $olt_data['telnet_login']; ?>');
    var tpas = encodeURIComponent('<?php echo $olt_data['telnet_pass']; ?>');
    var url1 = "/scripts/get_pon_int.php?thost=" + thost + "&tlog=" + tlog + "&tpas=" + tpas + "&onu_name=" + onu_name;
    $('#onu_data_field').load(url1);
}
function showONUbasic(olt_id, onu_name, olt_type){
    document.getElementById('onu_data_field').innerHTML = '<b>working...</b>';
    var thost = encodeURIComponent('<?php echo $olt_data['host']; ?>');
    var tlog = encodeURIComponent('<?php echo $olt_data['telnet_login']; ?>');
    var tpas = encodeURIComponent('<?php echo $olt_data['telnet_pass']; ?>');
    var url1 = "/scripts/get_ONU_bainf.php?olt_id=" + olt_id + "&onu_name=" + encodeURIComponent(onu_name) + "&tlog=" + tlog + "&tpas=" + tpas + "&thost=" + thost + "&olt_type=" + encodeURIComponent(olt_type);
    $('#onu_data_field').load(url1);
}
function showONUtraf(onu_mac){
    document.getElementById('onu_data_field').innerHTML = '<iframe src="https://grafana.********.****.***/d/b3e7bb76-e2be-4c73-aa98-d164d92e4573/onu-trafic-view?orgId=2&var-mac=' + encodeURIComponent(onu_mac) +'" width="100%" height="420" frameborder="0"></iframe>';
    }
function showcomhist(){
        document.getElementById('onu_data_field').innerHTML = '<b>working...</b>';
        var mac = encodeURIComponent('<?php echo $mac; ?>');
        var url1 = "/scripts/get_onu_comment_history.php?mac=" + mac;
        $('#onu_data_field').load(url1);
}
function saveonutouser(new_userid){
    ask1 = "<?php echo $labels['pon09']; ?> " + new_userid + "?";
    if (window.confirm(ask1)){
        new_userid = encodeURIComponent(new_userid);
        var url1 = "/scripts/change_user_onu.php?onu=<?php echo $mactouser ?>&userid=" + new_userid;
        $('#editcmdu').load(url1);
    }
}
function manage_ONU_OLT(olt_id, onu_key){
    document.getElementById('onu_data_field').innerHTML = '<b>working...</b>';
    var onu_name = encodeURIComponent('<?php echo $onu_name; ?>')
    var thost = encodeURIComponent('<?php echo $olt_data['host']; ?>');
    var tlog = encodeURIComponent('<?php echo $olt_data['telnet_login']; ?>');
    var tpas = encodeURIComponent('<?php echo $olt_data['telnet_pass']; ?>');
    var comrw = encodeURIComponent('<?php echo $communityrw; ?>');
    var host = encodeURIComponent('<?php echo $host; ?>');
    var type = encodeURIComponent('<?php echo $olt_type; ?>');
    var onu_mac = encodeURIComponent('<?php echo $mac; ?>');
    var url1 = "/scripts/manage_ONU_OLT.php?olt_id=" + olt_id + "&onu_key=" + onu_key + "&onu_name=" + onu_name + "&thost=" + thost + "&tlog=" + tlog + "&tpas=" + tpas + "&host=" + host + "&comrw=" + comrw + "&type=" + type + "&onu_mac=" + onu_mac;
    $('#onu_data_field').load(url1);
}
function hide_me(){
    document.getElementById('onu_data_field').innerHTML = '';
}
</script>