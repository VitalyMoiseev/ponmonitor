<?php

$table = $tbl_pref.'olt';
$query = "SELECT * FROM $table;";

$result = $mysqli_wb->query($query);

echo '<table class="features-table" width="100%">';
echo '<thead><tr><td class="grey" colspan="6">OLT</td></tr><thead><tbody>';
echo '<tr><td class="grey">№</td><td class="grey">OLT</td>';
echo '<td class="grey">'.$labels['pon01'].'</td>';
echo '<td class="grey">'.$labels['Refresh'].'</td>';
echo '<td class="grey">'.$labels['Last_act'].'</td></tr>';
$num1 = 0;
while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
    $community = $row['community'];
    $host = $row['host'];
    $olt_id = $row['Id'];
    if ($row['status'] != 1){
        $tdclass = "red";
    }else{
        $tdclass = "green";
    }
    echo '<tr><td width="3%" class="'.$tdclass.'">';
    echo ++$num1;
    echo "</td><td class=\"$tdclass\">";
    echo "<strong>".$row['name']." | ".$row['place']."</strong></td><td class=\"$tdclass\">";
    echo show_sfp($olt_id);
    echo '</td><td class="'.$tdclass.'">';
    echo "<a href=\"\" onclick=\"check_onu($olt_id); return false;\"><img id=\"refresh\" src=\"$protocol$sitename/img/refresh1.png\" alt=\"".$labels['Refresh']."\" /></a></td><td class=\"$tdclass\">";
    echo $row['last_act'];
    echo "</td></tr>";
}
echo '</tbody><tfoot><tr><td class="grey" colspan="5"><div id="pon_status"><a href="" onclick="check_onu(0); return false;">'.$labels['pon02'].'</a> | <a href="" onclick="upd_desc(); return false;">Update ONU descriptions</a></div></td></tr></tfoot>';
echo "</table>";
$result->close();
echo '<table class="features-table" width="100%"><thead><tr><td class="grey" colspan="5">'.$labels['Find'].' ONU</td></tr><thead><tbody>';
echo '<tr><td class="grey">&nbsp;</td><td><strong>'.$labels['Com'].':</strong></td><td><input id="s_com" size="30"></td><td><button id = "serach_com">'.$labels['Find'].'</button></td><td class="grey">&nbsp;</td></tr>';
echo '<tr><td class="grey">&nbsp;</td><td><strong>'.$labels['IDKlient'].':</strong></td><td><input id="s_id" size="30"></td><td><button id = "serach_id">'.$labels['Find'].'</button></td><td class="grey">&nbsp;</td></tr>';
echo '<tr><tr><td class="grey">&nbsp;</td><td><strong>MAC:</strong></td><td><input id="s_mac" size="30"></td><td><button id = "serach_mac">'.$labels['Find'].'</button></td><td class="grey">&nbsp;</td></tr>';
echo '</tbody><tfoot><tr><td class="grey" colspan="5"></td></tr></tfoot>';
echo "</table>";
echo '<div id="search_status"></div>';
if ($detect->isMobile()){
?>
<table class="features-table" width="100%">
    <thead>
<tr>
    <td colspan="3" class="grey"><?php echo $labels['Splitters']; ?></td>
</tr>
    <thead>
    <tbody>
<tr>
    <td class="green">1x2 - 3.17dB</td><td class="green">1x4 - 7.4dB</td><td class="green">1x8 - 10.7dB</td></tr><tr>
    <td class="green">1x16 - 13.9dB</td><td class="green">1x32 - 17.2dB</td><td class="green">1x64 - 21.5dB</td>
</tr>
<tr>
    <td colspan="114" class="grey"><strong><?php echo $labels['Couplers']; ?></strong></td>
</tr>
<tr>
<td class="green">5% - 13.7dB</td><td class="green">10% - 10.0dB</td><td class="green">15% - 8.16dB<</td></tr><tr>
<td class="green">20% - 7.11dB</td><td class="green">25% - 6.29dB</td><td class="green">30% - 5.39dB</td></tr><tr>
<td class="green">35% - 4.56dB</td><td class="green">40% - 4.01dB</td><td class="green">45% - 3.73dB</td></tr><tr>
<td class="green">50% - 3.17dB</td><td class="green">55% - 2.71dB</td><td class="green">60% - 2.34dB</td></tr><tr>
<td class="green">65% - 1.93dB</td><td class="green">70% - 1.56dB</td><td class="green">75% - 1.42dB</td></tr><tr>
<td class="green">80% - 1.06dB</td><td class="green">85% - 0.76dB</td><td class="green">90% - 0.49dB</td></tr><tr>
<td class="green" colspan="3">95% - 0.32dB</td></tr>
</tbody><tfoot><tr><td class="grey" colspan="3"></td></tr></tfoot>
</table>
<?php
    
}else{
?>
<table class="features-table" width="100%">
    <thead>
<tr>
    <td colspan="114" class="grey"><?php echo $labels['Splitters']; ?></td>
</tr>
    <thead>
    <tbody>
<tr>
    <td class="green" colspan="19">1x2 - 3.17dB</td><td class="green" colspan="19">1x4 - 7.4dB</td><td class="green" colspan="19">1x8 - 10.7dB</td>
    <td class="green" colspan="19">1x16 - 13.9dB</td><td class="green" colspan="19">1x32 - 17.2dB</td><td class="green" colspan="19">1x64 - 21.5dB</td>
</tr>
<tr>
    <td colspan="114" class="grey"><strong><?php echo $labels['Couplers']; ?></strong></td>
</tr>
<tr>
<td class="green" colspan="6">5%</td><td class="green" colspan="6">10%</td><td class="green" colspan="6">15%</td><td class="green" colspan="6">20%</td><td class="green" colspan="6">25%</td>
<td class="green" colspan="6">30%</td><td class="green" colspan="6">35%</td><td class="green" colspan="6">40%</td><td class="green" colspan="6">45%</td><td class="green" colspan="6">50%</td>
<td class="green" colspan="6">55%</td><td class="green" colspan="6">60%</td><td class="green" colspan="6">65%</td><td class="green" colspan="6">70%</td><td class="green" colspan="6">75%</td>
<td class="green" colspan="6">80%</td><td class="green" colspan="6">85%</td><td class="green" colspan="6">90%</td><td class="green" colspan="6">95%</td></tr>
<tr>
<td class="green" colspan="6">13.7dB</td><td class="green" colspan="6">10.0dB</td><td class="green" colspan="6">8.16dB</td><td class="green" colspan="6">7.11dB</td><td class="green" colspan="6">6.29dB</td>
<td class="green" colspan="6">5.39dB</td><td class="green" colspan="6">4.56dB</td><td class="green" colspan="6">4.01dB</td><td class="green" colspan="6">3.73dB</td><td class="green" colspan="6">3.17dB</td>
<td class="green" colspan="6">2.71dB</td><td class="green" colspan="6">2.34dB</td><td class="green" colspan="6">1.93dB</td><td class="green" colspan="6">1.56dB</td><td class="green" colspan="6">1.42dB</td>
<td class="green" colspan="6">1.06dB</td><td class="green" colspan="6">0.76dB</td><td class="green" colspan="6">0.49dB</td><td class="green" colspan="6">0.32dB</td></tr>
</tbody><tfoot><tr><td class="grey" colspan="114"></td></tr></tfoot>
</table>
<?php
}
?>
<script type='text/javascript'>
$(document).ready(function(){
    $('#serach_com').click(function(){
        document.getElementById('search_status').innerHTML = '<strong>working...</strong>';
        var s_com = document.getElementById('s_com').value;
        s_com = encodeURIComponent(s_com);
        var url1 = "<?php echo $protocol.$sitename; ?>/scripts/search_onu.php?s=com&pat=" + s_com;
        $('#search_status').load(url1);
    });
    $('#serach_id').click(function(){
        document.getElementById('search_status').innerHTML = '<strong>working...</strong>';
        var s_com = document.getElementById('s_id').value;
        s_com = encodeURIComponent(s_com);
        var url1 = "<?php echo $protocol.$sitename; ?>/scripts/search_onu.php?s=userid&pat=" + s_com;
        $('#search_status').load(url1);
    });
    $('#serach_mac').click(function(){
        document.getElementById('search_status').innerHTML = '<strong>working...</strong>';
        var s_com = document.getElementById('s_mac').value;
        s_com = encodeURIComponent(s_com);
        var url1 = "<?php echo $protocol.$sitename; ?>/scripts/search_onu.php?s=mac&pat=" + s_com;
        $('#search_status').load(url1);
    });
    $('#s_com').bind("enterKey",function(e){
     document.getElementById("serach_com").click();
    });
    $('#s_com').keyup(function(e){
     if(e.keyCode == 13)
     {
        $(this).trigger("enterKey");
     }
    });
    $('#s_id').bind("enterKey",function(e){
     document.getElementById("serach_id").click();
    });
    $('#s_id').keyup(function(e){
     if(e.keyCode == 13)
     {
        $(this).trigger("enterKey");
     }
    });
    $('#s_mac').bind("enterKey",function(e){
     document.getElementById("serach_mac").click();
    });
    $('#s_mac').keyup(function(e){
     if(e.keyCode == 13)
     {
        $(this).trigger("enterKey");
     }
    });
});
function check_onu(olt){
    if (olt === 0){
        if (window.confirm('<?php echo $labels['pon03']; ?>')){
            var url1 = "<?php echo $protocol.$sitename; ?>/scripts/check_onu.php?web=1";
        }else{
            return;
        }
    }else{
        var url1 = "<?php echo $protocol.$sitename; ?>/scripts/check_onu.php?web=1&olt_check=" + olt;
    }
    document.getElementById('pon_status').innerHTML = '<strong>working...</strong>';
    $('#pon_status').load(url1);
}
function upd_desc(){
    if (window.confirm('Update ONU descriptions for all OLT?')){
        var url1 = "<?php echo $protocol.$sitename; ?>/scripts/upd_desc.php?web=1";
    }else{
        return;
    }
    document.getElementById('pon_status').innerHTML = '<strong>working...</strong>';
    $('#pon_status').load(url1);
}
</script>