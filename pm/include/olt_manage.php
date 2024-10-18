<table class="features-table">
    <thead>
        <tr><td class="grey" colspan="5">OLT</td></tr>
    </thead>
    <tbody>
        <tr><td class="grey"><strong>№</strong></td><td class="grey"><strong><?php echo $labels['Nazv']; ?></strong></td><td class="grey"><strong><?php echo $labels['PlaceSet']; ?></strong></td><td class="grey"><strong>PON Type</strong></td><td class="grey"><strong><?php echo $labels['AccessP']; ?></strong></td></tr>
<?php

if($olt_mode == 'edit'){
    $table = $tbl_pref.'olt';
    $query = "SELECT * FROM $table WHERE Id=$olt_id;";
    $result = $mysqli_wb->query($query);
    if($row = $result->fetch_array(MYSQLI_ASSOC)){
        $olt_id = $row['Id'];
        $olt_name = $row['name'];
        $host = $row['host'];
        $place = $row['place'];
        $snmp_port = $row['snmp_port'];
        $community = $row['community'];
        $communityrw = $row['communityrw'];
        $t_port = $row['telnet_port'];
        $t_name = $row['telnet_name'];
        $t_pass = $row['telnet_password'];
        $status = $row['status'];
        $last_act = $row['last_act'];
        $olt_gpon = false;
        $pon_type = 0;
        if (array_key_exists('type', $row)){
            if ($row['type'] == 1){
                $olt_gpon = true;
                $pon_type = 1;
            }
        }
        
    echo "<tr><td>$olt_id</td>";
    echo "<td><input id=\"olt_name\" value=\"$olt_name\"></td>";
    echo "<td><input id=\"place\" value=\"$place\"></td>";
    echo "<td><select id=\"pon_type\">";
    foreach ($pon_types_names as $key=>$value){
        echo "<option value=$key";
        if ($pon_type == $key){
            echo " selected";
        }
        echo ">$value</option>";
    }
    echo "</select></td>";
    echo "<td class=\"client_h\">";
    echo "<li>host: <input id=\"host\" value=\"$host\">";
    echo "<li>SNMP порт: <input id=\"snmp_port\" value=\"$snmp_port\">";
    echo "<li>SNMP community RO: <input id=\"community\" value=\"$community\">";
    echo "<li>SNMP community RW: <input id=\"communityrw\" value=\"$communityrw\" type=\"password\"><small>".$labels['comrwnotuse']."</small>";
    echo "<li>telnet порт: <input id=\"t_port\" value=\"$t_port\">";
    echo "<li>telnet ".$labels['name'].": <input id=\"t_name\" value=\"$t_name\">";
    echo "<li>telnet ".$labels['password'].": <input id=\"t_pass\" value=\"$t_pass\" type=\"password\">";
    
    echo "</td></tr>";

    }else{
        echo "error";
    }
}else{
    $olt_id = "new";
    $olt_name = $labels['Nazv'].' OLT';
    $host = 'IP OLT';
    $place = $labels['PlaceSet'];
    $snmp_port = 161;
    $community = 'public';
    $communityrw = 'private';
    $t_port = 23;
    $t_name = 'admin';
    $t_pass = '1234';
    $status = 0;
    $last_act = null;
    $olt_gpon = false;
    $pon_type = 0;
    echo "<tr><td>&nbsp;</td>";
    echo "<td><input id=\"olt_name\" value=\"$olt_name\"></td>";
    echo "<td><input id=\"place\" value=\"$place\"></td>";
    echo "<td><select id=\"pon_type\">";
    foreach ($pon_types_names as $key=>$value){
        echo "<option value=$key";
        if ($pon_type == $key){
            echo " selected";
        }
        echo ">$value</option>";
    }
    echo "</select></td>";
    echo "<td class=\"client_h\">";
    echo "<li>host: <input id=\"host\" value=\"$host\">";
    echo "<li>SNMP порт: <input id=\"snmp_port\" value=\"$snmp_port\">";
    echo "<li>SNMP community RO: <input id=\"community\" value=\"$community\">";
    echo "<li>SNMP community RW: <input id=\"communityrw\" value=\"$communityrw\" type=\"password\">";
    echo "<li>telnet порт: <input id=\"t_port\" value=\"$t_port\">";
    echo "<li>telnet ".$labels['name'].": <input id=\"t_name\" value=\"$t_name\">";
    echo "<li>telnet ".$labels['password'].": <input id=\"t_pass\" value=\"$t_pass\" type=\"password\">";
    
    echo "</td></tr>";

}
?>
    </tbody>
    <tfoot>
        <tr><td  class="grey" colspan="5"><button onclick="check_snmp(0); return false;"><?php echo $labels['Check']; ?> SNMP</button> | <button onclick="check_telnet(0); return false;"><?php echo $labels['Check']; ?> telnet</button> | <button onclick="save_olt(1); return false;"><?php echo $labels['Save']; ?></button> | <button onclick="window.location ='<?php echo "$protocol$sitename/$base_url1/настройки"; ?>'; return false;"><?php echo $labels['exit']; ?></button></a></td></tr>
    </tfoot>
</table>
<div id="area1"></div>
<div id="area2"></div>
<div id="area3"></div>
<script type='text/javascript'>
var snmp_done = false;
var telnet_done = false;
function check_snmp(par1){
    host = document.getElementById('host').value;
    snmp_port = document.getElementById('snmp_port').value;
    community = document.getElementById('community').value;
    communityrw = document.getElementById('communityrw').value;
    if(host.length < 5){
        alert('<?php echo $labels['pon10']; ?> host - 5 <?php echo $labels['pon11']; ?>!');
        return false;
    }
    if(snmp_port.length < 3){
        alert('<?php echo $labels['pon10']; ?> SNMP port - 3 <?php echo $labels['pon11']; ?>!');
        return false;
    }
    if(community.length < 5){
        alert('<?php echo $labels['pon10']; ?> community RO - 5 <?php echo $labels['pon11']; ?>!');
        return false;
    }
    if(communityrw.length < 5){
        if(communityrw.length > 0){
            alert('<?php echo $labels['pon10']; ?> community RW - 5 <?php echo $labels['pon11']; ?>!');
            return false;
        }
    }
    document.getElementById('area1').innerHTML = '<strong>working...</strong>';
    host = encodeURIComponent(host);
    snmp_port = encodeURIComponent(snmp_port);
    community = encodeURIComponent(community);
    communityrw = encodeURIComponent(communityrw);
    url1 = '<?php echo $protocol.$sitename; ?>/scripts/check_snmp.php?host=' + host + "&port=" + snmp_port + "&community=" + community + "&communityrw=" + communityrw;
    if(par1 == 0){
        $('#area1').load(url1);
    }else{
        $('#area1').load(url1, function() {
            save_olt(2);
        });
    }
    return true;
}
function check_telnet(par1){
    host = document.getElementById('host').value;
    t_port = document.getElementById('t_port').value;
    t_name = document.getElementById('t_name').value;
    t_pass = document.getElementById('t_pass').value;
    if(host.length < 5){
        alert('<?php echo $labels['pon10']; ?> host - 5 <?php echo $labels['pon11']; ?>!');
        return false;
    }
    if(t_port.length < 2){
        alert('<?php echo $labels['pon10']; ?> telnet port - 2 <?php echo $labels['pon11']; ?>!');
        return false;
    }
    if(t_name.length < 1){
        alert('<?php echo $labels['pon10']; ?> telnet name - 1 <?php echo $labels['pon11']; ?>!');
        return false;
    }
    if(t_pass.length < 1){
        alert('<?php echo $labels['pon10']; ?> telnet password - 1 <?php echo $labels['pon11']; ?>!');
        return false;
    }
    document.getElementById('area2').innerHTML = '<strong>working...</strong>';
    host = encodeURIComponent(host);
    t_port = encodeURIComponent(t_port);
    t_name = encodeURIComponent(t_name);
    t_pass = encodeURIComponent(t_pass);
    url1 = '<?php echo $protocol.$sitename; ?>/scripts/check_telnet.php?host=' + host + "&port=" + t_port + "&t_name=" + t_name + "&t_pass=" + t_pass;
    if(par1 == 0){
        $('#area2').load(url1);
    }else{
        $('#area2').load(url1, function() {save_olt(3)});
    }
    return true;
}
function save_olt(par1){
    if(par1 == 1){
        if(!snmp_done){
            check_snmp(1);
            return false;
        }
    }
    if(par1 == 2){
        if(!telnet_done){
            check_telnet(1);
            return false;
       }
    }
    if(!snmp_done){
        alert('SNMP <?php echo $labels['pon14']; ?>!');
        return false;
    }
    if(!telnet_done){
        alert('Telnet <?php echo $labels['pon14']; ?>!');
        return false;
    }
    
    olt_name = document.getElementById('olt_name').value;
    place = document.getElementById('place').value;
    pon_type = document.getElementById('pon_type').value;
    
    if(olt_name.length < 5){
        alert('<?php echo $labels['pon12']; ?>!');
        return false;
    }
    if(place.length < 5){
        alert('<?php echo $labels['pon13']; ?>!');
        return false;
    }
    host = encodeURIComponent(document.getElementById('host').value);
    t_port = encodeURIComponent(document.getElementById('t_port').value);
    t_name = encodeURIComponent(document.getElementById('t_name').value);
    t_pass = encodeURIComponent(document.getElementById('t_pass').value);
    snmp_port = encodeURIComponent(document.getElementById('snmp_port').value);
    community = encodeURIComponent(document.getElementById('community').value);
    communityrw = encodeURIComponent(document.getElementById('communityrw').value);
    olt_name = encodeURIComponent(olt_name);
    place = encodeURIComponent(place);
    olt_id = '<?php echo $olt_id; ?>';
    document.getElementById('area3').innerHTML = '<strong>working...</strong>';
    url1 = '<?php echo $protocol.$sitename; ?>/scripts/save_olt.php?host=' + host + '&olt_name=' + olt_name + '&place=' + place + '&type=' + pon_type + '&t_port=' + t_port + '&t_name=' + t_name + '&t_pass=' + t_pass + '&snmp_port=' + snmp_port + '&community=' + community + '&olt_id=' + olt_id + "&communityrw=" + communityrw;
    $('#area3').load(url1);
    return true;
}

</script>
<?php

