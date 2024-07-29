<?php
error_reporting(E_ALL);
if (!isset($_GET['onuid'])){
    exit();
}
if (!isset($_GET['onukey'])){
    exit();
}
$port = isset($_GET['port']) ? $_GET['port'] : 1;
$curpvid = isset($_GET['curpvid']) ? $_GET['curpvid'] : 1;

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

$onuId = $_GET['onuid'];
$onukey = $_GET['onukey'];
$table1 = $tbl_pref.'onu';
$table2 = $tbl_pref.'olt';
$query = "SELECT
  `$table2`.`Id` AS olt_id,
  `$table2`.`host`,
  `$table2`.`snmp_port`,
  `$table2`.`community`,
  `$table2`.`telnet_port`,
  `$table2`.`telnet_name`,
  `$table2`.`telnet_password`,
  `$table1`.`onu_name`
FROM
  `$table1`
  INNER JOIN `$table2` ON `$table1`.`olt` = `$table2`.`Id`
WHERE
  `$table1`.`Id` = $onuId";
$result = $mysqli_wb->query($query);
$row = $result->fetch_array(MYSQLI_ASSOC);
$result->close();

$onu_name = $row['onu_name'];

$thost = $row['host'];
$tport = $row['telnet_port'];
    
$tlog = $row['telnet_name'];
$tpas = $row['telnet_password'];

$olt_id = $row['olt_id'];

$community = $row['community'];
$snmp_host = $row['host'];
if($row['snmp_port'] != 161){
    $snmp_host = $snmp_host.':'.$row['snmp_port'];
}

echo '<table class="features-table" width="100%"><thead>';
echo "<td class=\"grey\">Change PVID on port $port</td></tr></thead><tbody>";
echo "<tr><td>";
if($vlan_allowed = GetAllowedVlans($thost, $tport, $tlog, $tpas, $onu_name)){
    echo "PVID: <select id=\"pvid\">\n";
    foreach ($vlan_allowed as $key => $value) {
        echo "<option ";
        if($value == $curpvid){
            echo "selected ";
        }
        echo "value=$value>$value</option>\n";
    }
    echo "</select>\n";
}
echo '&nbsp;<button onclick="savePVID();">'.$labels['Save'].'</button>';
echo "</td></tr>";
echo '</tbody><tfoot><tr><td class="grey"><div id="editcmdf">&nbsp;</div></td></tr></tfoot></table>';
$mysqli_wb->close();

?>
<script type='text/javascript'>
function savePVID(){
    var curpvid = <?php echo $curpvid ?>;
    var pvid = document.getElementById('pvid').value;
    if(pvid == curpvid){
        var msg = "PVID " + pvid + "is alredy set :)";
        alert(msg);
        return false;
    }
    var msg = "Set " + pvid + " as PVID? Are you shure?";
    if(confirm(msg)){
        document.getElementById('editcmdf').innerHTML = '<strong>working...</strong>';
        var url1 = "<?php echo $protocol.$sitename; ?>/scripts/change_PVID.php?oltid=<?php echo $olt_id; ?>&onukey=<?php echo $onukey; ?>&port=<?php echo $port; ?>&pvid=" + pvid;
        $('#editcmdf').load(url1);
    }
}
</script>