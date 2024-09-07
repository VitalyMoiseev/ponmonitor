<?php



$table = $tbl_pref.'olt';
$query = "SELECT * FROM $table WHERE Id = $olt_id;";
    
$result = $mysqli_wb->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$result->close();
$olt_name = $row['name'];
$olt_host = $row['host'];
$olt_place = $row['place'];
if ($row['type'] == 1){
    $olt_gpon = true;
    $sfp_name = 'GPON0/';
    $olt_type = 'GPON';
}else{
    $olt_gpon = false;
    $sfp_name = 'EPON0/';
    $olt_type = 'EPON';
}

$onu_name = "$sfp_name$sfp_s:$onu_n";

echo '<table class="features-table" width="100%"><thead><tr><td class="grey"><div align="left">';
echo "<strong>";
echo $olt_name;
echo " | ";
echo $olt_host;
echo " | ";
echo $olt_place;
echo '</td><td class="grey"><a href="'.$protocol.$sitename.'/'.$base_url1.'/PON">'.$labels['pon04'].'</a></td></tr></thead><tfoot><tr><td class="grey" colspan="2"><div align="left">';
echo show_sfp($olt_id);
echo '</div></td></tr></tfoot></table>';
    
$community = $row['community'];
$communityrw = $row['communityrw'];
$host = $row['host'];
if($row['snmp_port'] != 161){
    $host = $host.':'.$row['snmp_port'];
}

$thost = $row['host'];
$tport = $row['telnet_port'];
$tlog = $row['telnet_name'];
$tpas = $row['telnet_password'];

$table = $tbl_pref.'onu';
$query = "SELECT * FROM $table WHERE olt = $olt_id AND onu_name = '$onu_name' AND present=1 AND mac='$onu_mac'";

$result = $mysqli_wb->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$comment = $row['comment'];
$userid = $row['userid'];
$mac = $row['mac'];
$onuId = $row['Id'];
$desc = $row['description'];

$key = GetOnuKeyByMac($mac, $host, $community, $olt_gpon);

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
    $query = "UPDATE $table SET pwr = '".$onu_s."', status=1, last_act=NOW() WHERE Id=".$row['Id'];
    ##
    $pwr = $onu_s;
    $pwr_from_onu = GetPwrFromOnu($key, $host, $community, $olt_gpon);
    $tdclass = "green";
    if ($olt_gpon){
        $onuRegData = GetGponOnuAct($host, 23, $tlog, $tpas, $onu_name);
    }else{
        $onuRegData = GetRegData($host, 23, $tlog, $tpas, $onu_name);
    }
}elseif($onu_status < 2 AND $olt_gpon){
    $pwr = 0;
    $pwr_from_onu = 0;
    $onu_s = 'OFFLINE';
    $onu_dist = "";
    $query = "UPDATE onu SET status=0 WHERE Id=".$row['Id'];
    $tdclass = "red";
    $onuDeregData = GetGponOnuInact($host, 23, $tlog, $tpas, $onu_name);
}elseif($onu_status == 2){
    $pwr = 0;
    $pwr_from_onu = 0;
    $onu_s = 'OFFLINE';
    $onu_dist = "";
    $query = "UPDATE onu SET status=0 WHERE Id=".$row['Id'];
    $tdclass = "red";
    $onuDeregData = GetDeregData($host, 23, $tlog, $tpas, $onu_name);
}elseif($onu_status !=6){
    $pwr = 0;
    $onu_s = 'OFFLINE';
    $onu_dist = "";
    $query = "UPDATE onu SET status=0 WHERE Id=".$row['Id'];
    $tdclass = "red";
    $onuRegData = GetRegData($host, 23, $tlog, $tpas, $onu_name);
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
echo '<td class="grey" colspan="7">';
echo $onu_name;
echo '</td></tr></thead><tbody><tr>';
echo '<td class="grey" colspan="7"><strong>';
echo $mac;
echo '</strong></td></tr><tr>';
echo '<td class="grey"><strong>'.$labels['Stat'].'</strong></td>';
echo '<td class="grey"><b>'.$labels['pon05'].' ONU</b></td>';
echo '<td class="grey"><b>'.$labels['pon05'].' OLT</b></td>';
echo '<td class="grey"><strong>'.$labels['Dist'].'</strong></td>';
echo '<td class="grey"><strong>'.$labels['Com'].'</strong></td>';
echo '<td class="grey"><strong>'.$labels['IDKlient'].'</strong></td>';
echo '<td class="grey"><strong>ONU description</strong></td>';
echo "</tr></thead><tbody><tr><td class=\"$tdclass\">";
if ($detect->isMobile()){
    echo "<small>";
}else{
    echo "<strong>";
}
echo $stat_n_ar[$onu_status];
if ($detect->isMobile()){
    echo "</small>";
}else{
    echo "</strong>";
}
echo "</td><td class=\"$tdclass\"><strong>$onu_s</strong>";
if($pwr != 0){
    echo " Db";
}
echo "</a></td><td class=\"$tdclass\">";
if ($pwr_from_onu){
    echo "<b>$pwr_from_onu</b> Db";
}
echo "</td><td class=\"$tdclass\"><strong>$onu_dist</strong>";
if($pwr != 0){
    echo " m";
}
echo "</td>";
echo "<td class=\"$tdclass\"><strong id=\"comment_t\">$comment</strong> <input id=\"com_new\" type=\"text\" value=\"$comment\" hidden></td>";
echo "<td class=\"$tdclass\"><a href=\"\" onclick=\"openuser($userid); return false;\"><strong id=\"userid_t\">$userid</strong></a> <input id=\"userid_new\" type=\"text\" value=\"$userid\" hidden></td>";
echo "<td class=\"$tdclass\"><strong>$desc</strong></td>";
echo '</tr>';
echo '<tr><td></td><td><small><a href="javascript:void();" onclick="document.location.reload(true);">['.$labels['Refresh'].']</a></small></td>';
echo '<td></td><td></td><td>';
if ($spLevel < 2){
    echo '<small><div id="editcmd"><a href="javascript:void();" onclick="editcomment();">['.$labels['Edit'].']</a></div></small>';
}
echo '</td>';
echo '<td>';
if ($spLevel < 2){
    echo '<small><div id="edituserid"><a href="javascript:void();" onclick="edituserid();">['.$labels['Edit'].']</a></div></small><div id="editcmdu"></div>';
}
echo '</td><td>';
echo '<small><a href="javascript:void();" onclick="upd_desc('.$olt_id.');">['.$labels['Refresh'].']</a></small>';
echo '</td>';

echo '</tr>';
if ($onu_status < 3){
    echo "<tr><td colspan=7 class=\"$tdclass\">".$labels['Stat']." ONU: <b>".$onuDeregData['Status']."</b>&nbsp;&nbsp;|&nbsp;&nbsp;";
    echo $labels['pon06'].': <b>'.$onuDeregData['LastRegTime'].'</b>&nbsp;&nbsp;|&nbsp;&nbsp;';
    echo $labels['pon07'].': <b>'.$onuDeregData['LastDeregTime'].'</b>&nbsp;&nbsp;|&nbsp;&nbsp;';
    echo $labels['pon08'].': <b>'.$onuDeregData['LastDeregReason'].'</b> <a href="javascript:void();" onclick="show_statuses('.$olt_gpon.');">[info]</a></td></tr>';
}elseif ($onu_status != 6){
    echo "<tr><td colspan=7 class=\"$tdclass\">Alive time: <b>".$onuRegData['AliveTime']."</b>&nbsp;&nbsp;|&nbsp;&nbsp;";
    echo $labels['pon06'].': <b>'.$onuRegData['LastRegTime'].'</b>&nbsp;&nbsp;|&nbsp;&nbsp;';
    echo $labels['pon07'].': <b>'.$onuRegData['LastDeregTime'].'</b>&nbsp;&nbsp;|&nbsp;&nbsp;';
    echo $labels['pon08'].': <b>'.$onuRegData['LastDeregReason'].'</b> <a href="javascript:void();" onclick="show_statuses('.$olt_gpon.');">[info]</a></td></tr>';
}

echo '</tbody><tfoot><tr>';
echo '<td class="grey" colspan="7">';
echo '|&nbsp;&nbsp;&nbsp;<strong><a href="javascript:void();" onclick="show_signal_history(\''.$mac.'\')">'.$labels['HistPwr'].'</a></strong>&nbsp;&nbsp;&nbsp;|';
if ($onu_s !='OFFLINE'){
    echo '&nbsp;&nbsp;&nbsp;<strong><a href="javascript:void();" onclick="show_FDB(\''.$olt_id.'\',\''.$onu_name.'\', \''.$mac.'\', '.$key.', '.$onuId.')">FDB '.$labels['table'].'</a></strong>&nbsp;&nbsp;&nbsp;|';
}else{
    echo '';
}
if ($spLevel < 2 AND $communityrw !=""){
    echo '&nbsp;&nbsp;&nbsp;<strong><a href="javascript:void();" onclick="manage_ONU_OLT(\''.$olt_id.'\', '.$key.');">Manage ONU and OLT</a></strong>&nbsp;&nbsp;&nbsp;|';
}
echo "&nbsp;&nbsp;&nbsp;<b><a href=\"javascript:void();\" onclick=\"show_int();\">Show interface</a></b>&nbsp;&nbsp;&nbsp;|\n";
echo "&nbsp;&nbsp;&nbsp;<b><a href=\"javascript:void();\" onclick=\"showONUbasic($olt_id, '$onu_name', '$olt_type');\">Show ONU basic-info</a></b>&nbsp;&nbsp;&nbsp;|\n";
echo "</td></tr></tfoot></table>\n";
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
            echo '<td><font color="red"><strong>disabled</strong></font></td>';
        }else{
            if ($onu_eth_state[$pkey] == '1' OR $onu_eth_state[$pkey] == 'up'){
                echo '<td><font color="green"><strong>UP</strong></font></td>';
            } else {
                echo '<td><font color="grey"><strong>DOWN</strong></font></td>';
            }
        }
        if ($olt_gpon){
            
            $onu_eth_pvid[$pkey] = $onu_eth_pvid[$pkey] == 0 ? 1 : GetGponVLANProfilePvid($onu_eth_pvid[$pkey], $host, $community);
        }
        echo '<td>'.$onu_eth_pvid[$pkey].'</td>';
        echo '<td><small><a href="javascript:void();" onclick="sh_port_st('.$olt_id.', \''.$onu_mac.'\', '.$pkey.');">['.$labels['Stat'].']</a></small>';
        if ($spLevel < 2 AND $communityrw !="" AND !$olt_gpon){
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
function sh_port_st(sw, port, pkey){
    var url1 = '<?php echo "$protocol$sitename/$base_url1"; ?>/switchport/' + sw + '/' + port + '/' + pkey;
    NewWin6 = window.open(url1,'w3','width=600,height=520,location=no,toolbar=no,menubar=no,status=no,scrollbars=yes,resizable=no');
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
    var url1 = "<?php echo $protocol.$sitename; ?>/scripts/change_onu_comment.php?id=<?php echo $onuId ?>&comment=" + new_comm;
    $('#editcmd').load(url1);
}
function reset_com(){
    $('#com_new').hide();
    document.getElementById('editcmd').innerHTML = '<a href="javascript:void();" onclick="editcomment();">[<?php echo $labels['Edit']; ?>]</a>';
}
function save_userid(){
    $('#userid_new').hide();
    var new_userid = document.getElementById('userid_new').value;
    new_userid = encodeURIComponent(new_userid);
    var url1 = "<?php echo $protocol.$sitename; ?>/scripts/change_onu_userid.php?id=<?php echo $onuId ?>&userid=" + new_userid;
    $('#edituserid').load(url1);
}
function reset_userid(){
    $('#userid_new').hide();
    document.getElementById('edituserid').innerHTML = '<a href="javascript:void();" onclick="edituserid();">[<?php echo $labels['Edit']; ?>]</a>';
}
function show_signal_history(mac){
    document.getElementById('onu_data_field').innerHTML = '<strong>working...</strong>';
    var url1 = "<?php echo $protocol.$sitename; ?>/scripts/get_pon_history.php?mac=" + mac;
    $('#onu_data_field').load(url1);
}
function changePVID(port, curpvid){
    document.getElementById('onu_data_field').innerHTML = '<strong>working...</strong>';
    var url1 = "<?php echo $protocol.$sitename; ?>/scripts/select_PVID.php?onuid=<?php echo $onuId ?>&port=" + port + "&curpvid=" + curpvid + "&onukey=<?php echo $key; ?>";
    $('#onu_data_field').load(url1);
}
function show_FDB(param1, param2, param3, param4, param5){
    document.getElementById('onu_data_field').innerHTML = '<strong>working...</strong>';
    var url1 = "<?php echo $protocol.$sitename; ?>/scripts/get_pon_FDB.php?olt_id=" + param1 + "&onu=" + param2 + "&onu_mac=" + param3 + "&key=" + param4 + "&onuid=" + param5;
    $('#onu_data_field').load(url1);
}
function manage_ONU_OLT(olt_id, onu_key){
    document.getElementById('onu_data_field').innerHTML = '<strong>working...</strong>';
    var onu_name = encodeURIComponent('<?php echo $onu_name; ?>')
    var url1 = "<?php echo $protocol.$sitename; ?>/scripts/manage_ONU_OLT.php?olt_id=" + olt_id + "&onu_key=" + onu_key + "&onu_name=" + onu_name;
    $('#onu_data_field').load(url1);
}
function show_int(){
    document.getElementById('onu_data_field').innerHTML = '<b>working...</b>';
    var onu_name = encodeURIComponent('<?php echo $onu_name; ?>')
    var thost = encodeURIComponent('<?php echo $thost; ?>');
    var tlog = encodeURIComponent('<?php echo $tlog; ?>');
    var tport = encodeURIComponent('<?php echo $tport; ?>');
    var tpas = encodeURIComponent('<?php echo $tpas; ?>');
    var url1 = "<?php echo $protocol.$sitename; ?>/scripts/get_pon_int.php?thost=" + thost + "&tport=" + tport + "&tlog=" + tlog + "&tpas=" + tpas + "&onu_name=" + onu_name;
    $('#onu_data_field').load(url1);
}
function showONUbasic(olt_id, onu_name, olt_type){
    document.getElementById('onu_data_field').innerHTML = '<b>working...</b>';
    var thost = encodeURIComponent('<?php echo $thost; ?>');
    var tlog = encodeURIComponent('<?php echo $tlog; ?>');
    var tport = encodeURIComponent('<?php echo $tport; ?>');
    var tpas = encodeURIComponent('<?php echo $tpas; ?>');
    var url1 = "<?php echo $protocol.$sitename; ?>/scripts/get_ONU_bainf.php?olt_id=" + olt_id + "&onu_name=" + encodeURIComponent(onu_name) + "&tlog=" + tlog + "&tpas=" + tpas + "&thost=" + thost + "&tport=" + tport + "&olt_type=" + encodeURIComponent(olt_type);
    $('#onu_data_field').load(url1);
}
function show_statuses(gpon){
        document.getElementById('onu_data_field').innerHTML = '<b>working...</b>';
        var url1 = "<?php echo $protocol.$sitename; ?>/scripts/get_pon_statuses.php?gpon=" + gpon;
        $('#onu_data_field').load(url1);
}
function upd_desc(olt){
    if (window.confirm('Update ONU descriptions for all ONU on this OLT?')){
        var url1 = "<?php echo $protocol.$sitename; ?>/scripts/upd_desc.php?web=1&olt_check=" + olt;
    }else{
        return;
    }
    document.getElementById('onu_data_field').innerHTML = '<b>working...</b>';
    $('#onu_data_field').load(url1);
}
</script>