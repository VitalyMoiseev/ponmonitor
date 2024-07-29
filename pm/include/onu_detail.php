<?php


$onu_name = "EPON0/$sfp_s:$onu_n";

$table = $tbl_pref.'olt';
$query = "SELECT * FROM $table WHERE Id = $olt_id;";
    
$result = $mysqli_wb->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$result->close();
$olt_name = $row['name'];
$olt_host = $row['host'];
$olt_place = $row['place'];

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

if($key = GetOnuKeyByMac($mac, $host, $community)){
    $onu_status = GetOnuStatus($key, $host, $community);
}else{
    $onu_status = 6;
}

$stat_n_ar = array('authenticated', 'registered', 'deregistered', 'auto-configured', 'lost', 'standby', 'OLT offline');
if ($onu_status == 3){
    $onu_s = GetOnuPwr($key, $host, $community);
    $onu_dist = GetOnuDist($key, $host, $community);
    # write to DB
    $query = "UPDATE $table SET pwr = '".$onu_s."', status=1, last_act=NOW() WHERE Id=".$row['Id'];
    ##
    $pwr = $onu_s;
    $tdclass = "green";
}elseif($onu_status !=6){
    $pwr = 0;
    $onu_s = 'OFFLINE';
    $onu_dist = "";
    $query = "UPDATE $table SET status=0 WHERE Id=".$row['Id'];
    $tdclass = "red";
    $onuDeregData = GetDeregData($thost, $tport, $tlog, $tpas, $onu_name);
}else{
    $pwr = 0;
    $onu_s = 'OFFLINE';
    $onu_dist = "";
    $query = "UPDATE $table SET status=0 WHERE Id=".$row['Id'];
    $tdclass = "red";
}
$mysqli_wb->query($query);
echo '<table class="features-table" width="100%"><thead><tr>';
echo '<td class="grey" colspan="5">';
echo $onu_name;
echo '</td></tr></thead><tbody><tr>';
echo '<td class="grey" colspan="5"><strong>';
echo $mac;
echo '</strong></td></tr><tr>';
echo '<td class="grey"><strong>'.$labels['Stat'].'</strong></td>';
echo '<td class="grey"><strong>'.$labels['pon05'].'</strong></td>';
echo '<td class="grey"><strong>'.$labels['Dist'].'</strong></td>';
echo '<td class="grey"><strong>'.$labels['Com'].'</strong></td>';
echo '<td class="grey"><strong>'.$labels['IDKlient'].'</strong></td>';
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
echo "</td><td class=\"$tdclass\"><strong>$onu_dist</strong>";
if($pwr != 0){
    echo " m";
}
echo "</td><td class=\"$tdclass\"><strong id=\"comment_t\">$comment</strong> <input id=\"com_new\" type=\"text\" value=\"$comment\" hidden>";
echo "<td class=\"$tdclass\"><a href=\"\" onclick=\"openuser($userid); return false;\"><strong id=\"userid_t\">$userid</strong></a> <input id=\"userid_new\" type=\"text\" value=\"$userid\" hidden>";
echo '</tr>';
echo '<tr><td></td><td><small><a href="javascript:void();" onclick="document.location.reload(true);">['.$labels['Refresh'].']</a></small></td>';
echo '<td></td><td>';
if ($spLevel < 2){
    echo '<small><div id="editcmd"><a href="javascript:void();" onclick="editcomment();">['.$labels['Edit'].']</a></div></small>';
}
echo '</td>';
echo '<td>';
if ($spLevel < 2){
    echo '<small><div id="edituserid"><a href="javascript:void();" onclick="edituserid();">['.$labels['Edit'].']</a></div></small><div id="editcmdu"></div>';
}
echo '</td>';

echo '</tr>';
if ($onu_status == 2){
    echo "<tr><td colspan=6 class=\"$tdclass\">".$labels['Stat']." ONU: <strong>".$onuDeregData['Status']."</strong>&nbsp;&nbsp;|&nbsp;&nbsp;";
    echo $labels['pon06'].': <strong>'.$onuDeregData['LastRegTime'].'</strong>&nbsp;&nbsp;|&nbsp;&nbsp;';
    echo $labels['pon07'].': <strong>'.$onuDeregData['LastDeregTime'].'</strong>&nbsp;&nbsp;|&nbsp;&nbsp;';
    echo $labels['pon08'].': <strong>'.$onuDeregData['LastDeregReason'].'</strong></td></tr>';
}

echo '</tbody><tfoot><tr>';
echo '<td class="grey" colspan="6">';
echo '|&nbsp;&nbsp;&nbsp;<strong><a href="javascript:void();" onclick="show_signal_history(\''.$mac.'\')">'.$labels['HistPwr'].'</a></strong>&nbsp;&nbsp;&nbsp;|';
if ($onu_s !='OFFLINE'){
    echo '&nbsp;&nbsp;&nbsp;<strong><a href="javascript:void();" onclick="show_FDB(\''.$olt_id.'\',\''.$onu_name.'\', \''.$mac.'\', '.$key.', '.$onuId.')">FDB '.$labels['table'].'</a></strong>&nbsp;&nbsp;&nbsp;|';
}else{
    echo '';
}
if ($spLevel < 2 AND $communityrw !=""){
    echo '&nbsp;&nbsp;&nbsp;<strong><a href="javascript:void();" onclick="manage_ONU_OLT(\''.$olt_id.'\', '.$key.');">Manage ONU and OLT</a></strong>&nbsp;&nbsp;&nbsp;|';
}
echo "</td></tr></tfoot></table>\n";
# Eth ports
if ($onu_s != 'OFFLINE'){
    $onu_eth_ena = GetOnuEthEna($key, $host, $community);
    $onu_eth_state = GetOnuEthState($key, $host, $community);
    $onu_eth_pvid = GetOnuPvid($key, $host, $community);
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
        echo '<td>'.$onu_eth_pvid[$pkey].'</td>';
        echo '<td><small><a href="javascript:void();" onclick="sh_port_st('.$olt_id.', \''.$onu_mac.'\', '.$pkey.');">['.$labels['Stat'].']</a></small>';
        if ($spLevel < 2 AND $communityrw !=""){
            echo '&nbsp;<small><span id="editvlan"><a href="javascript:void();" onclick="changePVID('.$pkey.', '.$onu_eth_pvid[$pkey].');">['.$labels['Edit'].' PVID]</a></span></small>';
        }
        echo '</td>';
    }
    echo '</tbody><tfoot><tr><td class="grey" colspan="4">&nbsp;</td></tr></tfoot></table>';
}
#
echo '<div id="onu_data_field"></div>';


$mactouser = str_replace(":", "", $mac);
$mactouser = hexdec($mactouser);
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
</script>