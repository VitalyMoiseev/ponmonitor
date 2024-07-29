<?php

if (!isset($_GET['olt_id'])){
    exit();
}
if (!isset($_GET['onu_key'])){
    exit();
}
if (!isset($_GET['onu_name'])){
    exit();
}

require '../include/vars.php';
require '../include/database.php';
require '../include/select_lang.php';
require '../include/pon_functions.php';
if(isset($_COOKIE["pm_username"])){
    $username = $_COOKIE["pm_username"];
}
if(isset($_COOKIE["pm_password"])){
    $password = $_COOKIE["pm_password"];
}
include '../include/auth_user.php';

$olt_id = $_GET['olt_id'];
$onu_key = $_GET['onu_key'];
$onu_name = $_GET['onu_name'];

echo '<table class="features-table" width="100%"><thead>';
echo '<td class="grey" colspan="6">Manage ONU '.$onu_name.'</td><td class="grey" colspan="3">Manage OLT</td></tr></thead><tbody>';
echo '<tr><td class="grey"></td>';
echo "<td class=\"green\"><button onclick=\"rebootONU($olt_id, $onu_key);\">Reboot ONU</button></td>";
echo '<td class="grey"></td>';
echo '<td class="grey"></td>';
echo "<td class=\"green\"><button onclick=\"showONUconf($olt_id, '$onu_name');\">Show ONU config</button></td>";
echo '<td class="grey"></td>';
echo '<td class="grey"></td>';
echo "<td class=\"green\"><button onclick=\"saveOLTconf($olt_id);\">Save OLT config</button></td>";
echo '<td class="grey"></td></tr>';
echo '</tbody><tfoot><tr><td class="grey" colspan="9"><div id="cmdrep">&nbsp;</div></td></tr></tfoot></table>';

?>
<script type='text/javascript'>
function rebootONU(olt_id, onu_key){
    var msg = "Reboot ONU? Are you shure?";
    if(confirm(msg)){
        document.getElementById('cmdrep').innerHTML = '<strong>working...</strong>';
        var url1 = "<?php echo $protocol.$sitename; ?>/scripts/rebootONU.php?olt_id=" + olt_id + "&onu_key=" + onu_key;
        $('#cmdrep').load(url1);
    }
}
function saveOLTconf(olt_id){
    var msg = "Save OLT config? Are you shure?";
    if(confirm(msg)){
        document.getElementById('cmdrep').innerHTML = '<strong>working...</strong>';
        var url1 = "<?php echo $protocol.$sitename; ?>/scripts/saveOLTconf.php?olt_id=" + olt_id;
        $('#cmdrep').load(url1);
    }
}
function showONUconf(olt_id, onu_name){
    document.getElementById('cmdrep').innerHTML = '<strong>working...</strong>';
    var url1 = "<?php echo $protocol.$sitename; ?>/scripts/get_ONUconf.php?olt_id=" + olt_id + "&onu_name=" + encodeURIComponent(onu_name);
    $('#cmdrep').load(url1);
}
</script>