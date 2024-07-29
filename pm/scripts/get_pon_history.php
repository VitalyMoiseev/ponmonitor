<?php
if (!isset($_GET['mac'])){
    exit();
}
$mac = $_GET['mac'];
require '../include/vars.php';
require '../include/database.php';
require '../include/select_lang.php';
$table = $tbl_pref.'onu_pwr_history';
$query = "SELECT * FROM $table WHERE mac='$mac' ORDER BY starttime DESC";
if ($result = $mysqli_wb->query($query)){
    
?>

<table class="features-table">
    <thead>
        <tr>
            <td class="grey" colspan="3"><?php echo $labels['HistPwr']; ?></td>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="grey"><strong><?php echo $labels['H1']; ?></strong></td><td class="grey"><strong><?php echo $labels['H2']; ?></strong></td><td class="grey"><strong>Db</strong></td>
        </tr>
<?php
while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
    $tdclass = "red";
    if($row['pwr'] < 0){
        $tdclass = "green";
    }
    echo "<tr><td>".$row['starttime']."</td><td>".$row['stoptime']."</td><td class=\"$tdclass\">".$row['pwr']."</td></tr>";
}
?>
    </tbody>
    <tfoot>
        <tr>
            <td class="grey" colspan="3"></td>
        </tr>        
    </tfoot>
</table>
<?php
}else{
    echo "SQL error!";
}
?>