<style type="text/css">
    div.scroll-table {
        width: 100%;
        overflow: auto;
        height: 85%;
}
</style>
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
    $tms1 = "SN";
}else{
    $olt_gpon = false;
    $tms1 = "mac";
}
    
echo '<table class="features-table" width="100%"><thead><tr><td class="grey"><div align="left">';
echo "<strong>";
echo $olt_name;
echo " | ";
echo $olt_host;
echo " | ";
echo $olt_place;
echo '</td><td class="grey"><a href="'.$protocol.$sitename.'/'.$base_url1.'/PON">'.$labels['pon04'].'</a></td></tr></thead><tfoot><tr><td class="grey" colspan="2"><div align="left">';
echo show_sfp($olt_id);
echo "</div></td></tr></tfoot></table>\n";

$table = $tbl_pref.'onu';
$query = "SELECT * FROM $table WHERE present = 1 AND olt = $olt_id ORDER BY $OrderOnu";
$result = $mysqli_wb->query($query);

echo '<div class="scroll-table" id="onu_table">';
echo '<table class="features-table" width="100%"><thead>';
echo "<td class=\"grey\"><strong><a href=\"\" onclick=\"sortby('order_id'); return false;\">ONU<span id=\"sort_order_id\">";
if ($OrderOnu == 'order_id ASC'){
    echo "&nbsp;&dArr;";
}elseif ($OrderOnu == 'order_id DESC'){
    echo "&nbsp;&uArr;";
}
echo "</span></a></strong></td>\n";
echo "<td class=\"grey\"><strong><a href=\"\" onclick=\"sortby('mac'); return false;\">$tms1<span id=\"sort_mac\">";
if ($OrderOnu == 'mac ASC'){
    echo "&nbsp;&dArr;";
}elseif ($OrderOnu == 'mac DESC'){
    echo "&nbsp;&uArr;";
}
echo "</span></a></strong></td>\n";
echo "<td class=\"grey\"><strong><a href=\"\" onclick=\"sortby('comment'); return false;\">".$labels['Com']."<span id=\"sort_comment\">";
if ($OrderOnu == 'comment ASC'){
    echo "&nbsp;&dArr;";
}elseif ($OrderOnu == 'comment DESC'){
    echo "&nbsp;&uArr;";
}
echo "</span></a></strong></td>\n";
echo "<td class=\"grey\"><strong><a href=\"\" onclick=\"sortby('userid'); return false;\">".$labels['IDKlient']."<span id=\"sort_userid\">";
if ($OrderOnu == 'userid ASC'){
    echo "&nbsp;&dArr;";
}elseif ($OrderOnu == 'userid DESC'){
    echo "&nbsp;&uArr;";
}
echo "</span></a></strong></td>\n";
echo "<td class=\"grey\"><strong><a href=\"\" onclick=\"sortby('pwr'); return false;\">".$labels['pon05']."<span id=\"sort_pwr\">";
if ($OrderOnu == 'pwr ASC'){
    echo "&nbsp;&dArr;";
}elseif ($OrderOnu == 'pwr DESC'){
    echo "&nbsp;&uArr;";
}
echo "</span></a></strong></td>\n";
echo "<td class=\"grey\"><strong><a href=\"\" onclick=\"sortby('last_act'); return false;\">".$labels['L_act']."<span id=\"sort_last_act\">";
if ($OrderOnu == 'last_act ASC'){
    echo "&nbsp;&dArr;";
}elseif ($OrderOnu == 'last_act DESC'){
    echo "&nbsp;&uArr;";
}
echo "</span></a></strong></td>\n";
echo "</tr></thead><tbody>";
$cc1 = false;
while( $onu_t = $result->fetch_array(MYSQLI_ASSOC) ){
    $sfp_n = explode('/',$onu_t['onu_name']);
    $sfp_n = $sfp_n[1];
    $sfp_n = explode(':',$sfp_n);
    $sfp_n = $sfp_n[0];
    #$sfp_n = substr($onu_t['onu_name'],6,1);
    if ($sfp_s == '0' OR $sfp_n == $sfp_s){
        #var_dump($onu_us);
        echo '<tr>';
        if ($onu_t['status'] == '0'){
            $tdclass = "red";
        }else{
            $tdclass = "green";
        }
        $spl1 = explode("/", $onu_t['onu_name']);
            $spl2 = explode(":", $spl1[1]);
            $onu_n = $spl2[1];
            echo '<td class="'.$tdclass.'"><strong><a href="'.$protocol.$sitename.'/'.$base_url1.'/PON/'.$onu_t['olt'].'/'.$sfp_n.'/'.$onu_n.'/'.$onu_t['mac'].'">';
            echo $onu_t['onu_name'];
            echo '</a></strong></td><td class="'.$tdclass.'">';
            if ($detect->isMobile()){
                echo "<small>";
            }
            echo $onu_t['mac'];
            if ($detect->isMobile()){
                echo "</small>";
            }
            echo '</td><td class="'.$tdclass.'">'.$onu_t['comment'].'</td><td class="'.$tdclass.'">'.$onu_t['userid'].'</td><td class="'.$tdclass.'"><strong>'.$onu_t['pwr'].'</strong></td><td class="'.$tdclass.'">'.$onu_t['last_act'].'</td></tr>';
        }
    unset ($onu_us);
    }
    echo "</tbody><tfoot><tr><td class=\"grey\" colspan=\"7\">&nbsp;</td></tr></tfoot></table></div>\n";
?>
<script type='text/javascript'>
<?php
$sort_par = explode(' ', $OrderOnu);

echo "var sort_par1 = '".$sort_par[0]."';";
echo "var sort_par2 = '".$sort_par[1]."';";

?>    
function sortby(par1){
    if(par1 == sort_par1){
        if(sort_par2 == 'ASC'){
            sort_par2 = 'DESC';
        }else{
            sort_par2 = 'ASC';
        }
    }else{
        sort_par1 = par1;
        sort_par2 = 'ASC';
    }
    document.getElementById('sort_order_id').innerHTML = '';
    document.getElementById('sort_comment').innerHTML = '';
    document.getElementById('sort_userid').innerHTML = '';
    document.getElementById('sort_mac').innerHTML = '';
    document.getElementById('sort_pwr').innerHTML = '';
    document.getElementById('sort_last_act').innerHTML = '';
    if(sort_par2 == 'ASC'){
        document.getElementById('sort_' + par1).innerHTML = '&nbsp;&dArr;';
    }else{
        document.getElementById('sort_' + par1).innerHTML = '&nbsp;&uArr;';
    }
    order_par = "pm_ordreonu=" + sort_par1 + "%20" + sort_par2;
    document.cookie = order_par;
    document.location.reload(true);
    return false;
};
</script>