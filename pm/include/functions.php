<?php

function snmp_uptime($param) {
    if(is_null($param)){
        return 'no link';
    }
    #$ar1 = explode(') ', $param);
    return $param;
}
function snmp_rep($param) {
    if(is_null($param)){
        return 'no link';
    }
    #$ar1 = explode(': ', $param);
    return $param;
}

function show_sfp($olt_id){
    global $mysqli_wb;
    global $tbl_pref;
    global $base_url1;
    global $protocol;
    global $sitename;
    global $labels;
    $table = $tbl_pref.'olt_sfp';
    $query = "SELECT * FROM $table WHERE olt = $olt_id ORDER BY SUBSTRING(sfp,7)+0";
    $resp = '';
    if($result = $mysqli_wb->query($query)){
        while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
            $sfp = str_replace('EPON0/', '', $row['sfp']);
            if ($row['count_onu'] == 0){
                continue;
            }
            $resp .= " <a href=\"$protocol$sitename/$base_url1/PON/$olt_id/$sfp\">SFP$sfp</a>:<strong> ";
            if ($row['count_onu'] > 60){
                $resp .= '<font color="red">';
            }else{
                $resp .= '<font color="black">';
            }
            $offline_c = $row['count_onu'] - $row['online_count'];
            $resp .= $row['count_onu']. "</font></strong>(<font color=\"green\">".$row['online_count']."</font>/<font color=\"red\">$offline_c</font>)";
        }
        $result->close();
    }
    $resp .= " <a href=\"$protocol$sitename/$base_url1/PON/$olt_id\">".$labels['All']."</a>";
    
    return $resp;
}


?>