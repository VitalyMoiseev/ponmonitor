<table class="features-table">
    <thead>
        <tr><td class="grey" colspan="4"><?php echo $labels['Uacc']; ?></td></tr>
    </thead>
    <tbody>
        <tr><td class="grey"><strong>№</strong></td><td class="grey"><strong><?php echo $labels['name']; ?></strong></td><td class="grey"><strong><?php echo $labels['Laccess']; ?></strong></td><td class="grey"><strong><?php echo $labels['Manage']; ?></strong></td></tr>
<?php

if($spLevel == 0){
$table = $tbl_pref.'users';
$query = "SELECT Id, username, splevel FROM $table";
$result = $mysqli_wb->query($query);
$num1 = 0;
while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
    echo '<tr><td>';
    echo ++$num1;
    echo '</td><td>';
    echo $row['username'];
    echo '<div id="new_pass_'.$row['Id'].'"></div>';
    echo '</td><td>';
    echo $levelnames[$row['splevel']];    
    echo '<div id="new_level_'.$row['Id'].'"></div></td><td class="client_h">';
    echo '<button onclick="change_pass('.$row['Id'].'); return false;">'.$labels['Change_pas'].'</button> ';
    if($row['Id'] > 1){
        echo '<button onclick="change_level('.$row['Id'].'); return false;">'.$labels['Change_lev'].'</button> ';
    }
    if($row['Id'] > 1){
        echo '<button onclick="del_user('.$row['Id'].',\''.$row['username'].'\'); return false;">удалить</button> ';
    }
    echo '</td></tr>';
}
echo '<tr><td>';
echo 'Новый';
echo '</td><td>';
echo 'Имя: <input id="nname" size="10"> | ';
echo 'Пароль: <input id="npas" size="10" type="password">';
echo '</td><td>';
echo '<select id="splevel_add" size="1">';
foreach ($levelnames as $key => $value) {
    echo "<option value=$key>$value</option>";
}
echo '</select>';
echo '</td><td class="client_h">';
echo '<button onclick="add_user(); return false;">добавить</button> ';
echo '</td></tr>';

$result->close();
?>
    </tbody>
    <tfoot>
        <tr><td class="grey" colspan="4"></td></tr>
    </tfoot>
</table>
<table class="features-table">
    <thead>
        <tr><td class="grey" colspan="5">OLT</td></tr>
    </thead>
    <tbody>
        <tr><td class="grey"><strong>№</strong></td><td class="grey"><strong><?php echo $labels['Nazv']; ?></strong></td><td class="grey"><strong><?php echo $labels['PlaceSet']; ?></strong></td><td class="grey"><strong><?php echo $labels['AccessP']; ?></strong></td><td class="grey"><strong><?php echo $labels['Operations']; ?></strong></td></tr>
<?php
$table = $tbl_pref.'olt';
$query = "SELECT * FROM $table;";
$result = $mysqli_wb->query($query);
while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
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
        
    echo "<tr><td><strong>$olt_id</strong></td>";
    echo "<td><strong>$olt_name</strong></td>";
    echo "<td><strong>$place</strong></td>";
    echo "<td class=\"client_h\">";
    echo "<br><li>host: $host";
    echo "<li>SNMP port: $snmp_port";
    echo "<li>SNMP community RO: $community";
    echo "<li>SNMP community RW: ************";
    echo "<li>telnet port: $t_port";
    echo "<li>telnet ".$labels['name'].": $t_name";
    echo "<li>telnet ".$labels['password'].": ********";
    echo "<br>&nbsp;</td><td><a href=\"$protocol$sitename/$base_url1/settings/OLT/$olt_id\">".$labels['Edit']."</a>";
    echo "<p><button onclick=\"delete_olt($olt_id);\">".$labels['Delete']."</button>";
    echo "</td></tr>";

}
?>
    </tbody>
    <tfoot>
        <tr><td  class="grey" colspan="5">
<?php
echo "<a href=\"$protocol$sitename/$base_url1/settings/OLT/\">".$labels['Add'];
?>
 OLT</a></td></tr>
    </tfoot>
</table>
<div id="area1"></div>
<script type="text/javascript">
function change_pass(uid){
    ar1 = "new_pass_" + uid;
    text1 = '<div id="' + ar1 + '" style="border: 2px solid red; padding: 1 2px;">Пароль: <input id="np1_' + uid + '" size="6" type="password"> повтор: <input id="np2_' + uid + '" size="6" type="password"> <button onclick="npsave(' + uid +'); return false;"><?php echo $labels['Save']; ?></button><button onclick="location.reload();"><?php echo $labels['Esc']; ?></button></div>';
    ar2 = "#" + ar1;
    $(ar2).replaceWith(text1);
}
function delete_olt(olt_id){
    if(confirm('<?php echo $labels['Delete']; ?> OLT?')){
        url1 = '<?php echo $protocol.$sitename; ?>/scripts/del_olt.php?olt_id=' + olt_id;
        $('#area1').load(url1);
    }
}
function npsave(uid){
    np1 = document.getElementById('np1_' + uid).value;
    np2 = document.getElementById('np2_' + uid).value;
    if(np1 != np2){
        alert('<?php echo $labels['set01']; ?>!');
        return false;
    }else{
        if(np1.length < 5){
            alert('<?php echo $labels['set02']; ?> - 5 <?php echo $labels['pon11']; ?>!');
            return false;
        }else{
            if(confirm("<?php echo $labels['set03']; ?>?")){
                np1 = encodeURIComponent(np1);
                url1 = '<?php echo $protocol.$sitename; ?>/scripts/change_pass.php?userid=' + uid + '&pass=' + np1;
                $('#area1').load(url1);
            }
            return false;
        }
    }
}
function levelsave(uid){
    splevel = document.getElementById('splevel_new_' + uid).value;
    if(confirm("<?php echo $labels['set07']; ?>?")){
        url1 = '<?php echo $protocol.$sitename; ?>/scripts/change_level.php?userid=' + uid + '&splevel=' + splevel;
        $('#area1').load(url1);
    }
    return false;
}
function change_level(uid){
    ar1 = "new_level_" + uid;
    text1 = '<div id="' + ar1 + '" style="border: 2px solid red; padding: 1 2px;"><select id="splevel_new_' + uid + '" size="1">';
    text1 = text1 + '<?php foreach ($levelnames as $key => $value){ echo "<option value=$key>$value</option>";}?>';
    text1 = text1 + '</select><button onclick="levelsave(' + uid +'); return false;"><?php echo $labels['Save']; ?></button><button onclick="location.reload();"><?php echo $labels['Esc']; ?></button></div>';
    ar2 = "#" + ar1;
    $(ar2).replaceWith(text1);
}

function add_user(){
    nname = document.getElementById('nname').value;
    npas = document.getElementById('npas').value;
    splevel = document.getElementById('splevel_add').value;
    if(nname.length < 5){
        alert('<?php echo $labels['set03']; ?> - 5 <?php echo $labels['pon11']; ?>!');
        return false;
    }
    if(npas.length < 5){
        alert('<?php echo $labels['set02']; ?> - 5 <?php echo $labels['pon11']; ?>!');
        return false;
    }
    nname = encodeURIComponent(nname);
    npas = encodeURIComponent(npas);
    url1 = '<?php echo $protocol.$sitename; ?>/scripts/add_user.php?username=' + nname + '&pass=' + npas + '&splevel=' + splevel;
    alert(url1);
    $('#area1').load(url1);
}
function del_user(uid, uname){
    if(confirm("<?php echo $labels['set06']; ?> " + uname + "?")){
        url1 = '<?php echo $protocol.$sitename; ?>/scripts/del_user.php?userid=' + uid;
        $('#area1').load(url1);
    }
    return false;
}
</script>
<?php
}else{
    echo $labels['set05'];
}
?>