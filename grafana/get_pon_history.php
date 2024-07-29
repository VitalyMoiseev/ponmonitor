<?php

#### debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
###

if (!isset($_GET['mac'])){
    exit();
}
$mac = $_GET['mac'];
$ofset = isset($_GET['ofset']) ? $_GET['ofset'] : 0;
require '../include/vars.php';
require '../include/database.php';
$scriptmode = true;
require '../include/auth_user.php';
require '../include/select_lang.php';

$query = "SELECT * FROM onu_pwr_history WHERE mac='$mac' ORDER BY starttime DESC LIMIT $ofset, 100";
if ($result = $mysqli_wb->query($query)){
    $countrows = $result->num_rows;
?>
<table class="features-table">
    <thead>
        <tr>
            <td class="grey" colspan="5"><?php echo $labels['HistPwr']; ?></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="grey"></td><td class="grey"><b><?php echo $labels['PwrHist1']; ?>&nbsp;-&nbsp;<?php echo $labels['PwrHist2']; ?></b></td><td class="grey"><b>Db</b></td><td class="grey"></td><td width="60%" style="vertical-align: top;" class="grey"><b>Graf</b></td>
        </tr>

<?php
$show_grafana = true;
while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
    $tdclass = "red";
    if($row['pwr'] < 0){
        $tdclass = "green";
    }
    if (is_null($row['stoptime'])){
        $row['stoptime'] = $labels['PwrHist4'];
    }
    echo "<tr><td></td><td>".$row['starttime']."&nbsp;-&nbsp;".$row['stoptime']."</td><td class=\"$tdclass\">".$row['pwr']."</td><td></td>";
    if ($ofset == 0 AND $show_grafana){
        $mac_html = htmlspecialchars($mac);
        echo "<td style=\"vertical-align: top;\" rowspan=\"$countrows\">";
        echo "<iframe src=\"https://grafana.********.****.**/d/e678df23-cdac-4911-9b1b-45c5899d56b4/pon-onu-pwr?orgId=2&var-onu=$mac_html\" width=\"100%\" height=\"410\" frameborder=\"0\"></iframe></td>";
        $show_grafana = false;
    }
    echo "</tr>";
}
?>
    </tbody>
    <tfoot>
        <tr>
            <td class="grey" colspan="5"><div id="scr<?php echo $ofset; ?>">
<?php
echo '|&nbsp;&nbsp;&nbsp;<b><a href="javascript:void();" onclick="show_pwr_history_'.$ofset.'(\''.$mac.'\')">'.$labels['PwrHist3'].'</a></b>&nbsp;&nbsp;&nbsp;|';
?>
</div></td>
        </tr>        
    </tfoot>
</table>
<div id="onu_data_field_<?php echo $ofset; ?>"></div>
<script type='text/javascript'>prototype
function show_pwr_history_<?php echo $ofset; ?>(mac){
        document.getElementById('onu_data_field_<?php echo $ofset; ?>').innerHTML = '<b>working...</b>';
        document.getElementById('scr<?php echo $ofset; ?>').innerHTML = '';
        var url1 = "/scripts/get_pon_history.php?mac=" + mac + "&ofset=<?php echo $ofset + 100; ?>";
        $('#onu_data_field_<?php echo $ofset; ?>').load(url1);
}
</script>
<?php
}else{
    echo "SQL error!";
}
?>