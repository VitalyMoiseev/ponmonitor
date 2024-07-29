<?php

require '../include/vars.php';
require '../include/database.php';
require '../include/select_lang.php';
if(isset($_GET['s'])){
    $s_com = $_GET['pat'];
    if (strlen($s_com) > 1){
        $table = $tbl_pref.'olt';
        $query = "SELECT * FROM $table;";
        $result = $mysqli_wb->query($query);
        while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
            $olt[$row['Id']] = $row;
        }
        $result->close;
        $s_com = $mysqli_wb->real_escape_string($s_com);
        $table = $tbl_pref.'onu';
        if($_GET['s'] == 'com'){
            $query = "SELECT * FROM $table WHERE present=1 AND comment LIKE '%$s_com%' LIMIT 50";
        }elseif($_GET['s'] == 'userid'){
            $query = "SELECT * FROM $table WHERE present=1 AND userid = $s_com LIMIT 50";
        }else{
            $query = "SELECT * FROM $table WHERE present=1 AND mac LIKE '%$s_com%' LIMIT 50";
        }
        
        
        $result = $mysqli_wb->query($query);
        echo '<table class="features-table" width="100%"><thead>';
        echo "<td class=\"grey\">&nbsp;</td><td class=\"grey\"><strong>OLT</strong></td><td class=\"grey\"><strong>ONU</strong></td><td class=\"grey\"><strong>mac</strong></td><td class=\"grey\"><strong>".$labels['Com']."</strong></td><td class=\"grey\"><strong>".$labels['IDKlient']."</strong></td><td class=\"grey\"><strong>".$labels['pon05']."</strong></td><td class=\"grey\"><strong>".$labels['L_act']."</strong></td></tr></thead><tbody>";
    
        while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
            $onu_t = $row;
            $sfp_n = substr($onu_t['onu_name'],6,1);
            echo '<tr>';
            if ($onu_t['status'] == '0'){
                $tdclass = "red";
            }else{
                $tdclass = "green";
            }
            $spl1 = explode("/", $onu_t['onu_name']);
            $spl2 = explode(":", $spl1[1]);
            $onu_n = $spl2[1];
            echo '<td class="'.$tdclass.'"><a href="'.$protocol.$sitename.'/'.$base_url1.'/PON/'.$onu_t['olt'].'/'.$sfp_n.'/'.$onu_n.'/'.$onu_t['mac'].'">ONU card</td>';
            echo '<td class="'.$tdclass.'"><strong>'.$olt[$onu_t['olt']]['name'].'</strong></td>';
            echo '<td class="'.$tdclass.'"><strong>'.$onu_t['onu_name'].'</strong></td><td class="'.$tdclass.'">'.$onu_t['mac'].'</td><td class="'.$tdclass.'">'.$onu_t['comment'].'</td><td class="'.$tdclass.'">'.$onu_t['userid'].'</td><td class="'.$tdclass.'">'.$onu_t['pwr'].'</td><td class="'.$tdclass.'">'.$onu_t['last_act'].'</td></tr>';
        }
        echo '</tbody><tfoot><tr><td class="grey" colspan="8"><button id = "reset_res">Очистить результаты</button></td></tr></tfoot>';
        echo "</table>";
        ?>
<script type='text/javascript'>
$(document).ready(function(){
    $('#reset_res').click(function(){
        document.getElementById('search_status').innerHTML = '';
    })
});
</script>
<?php
    }else{
        echo "<script type='text/javascript'>alert('".$labels['s01']."!');</script>";
    }
    
}