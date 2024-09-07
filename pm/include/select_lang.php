<?php
if(isset($_COOKIE['bil_s_lang'])){
    $lang = $_COOKIE['bil_s_lang'];
}else{
    if(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) == 'ru'){
        $lang = 'ru';
    }else{
        $lang = 'uk';
    }
}
$table = $tbl_pref.'texts';
switch ($lang) {
    case 'ru':
        setlocale(LC_ALL, "ru_RU.UTF-8");
        $query = "SELECT label, label_ru AS text FROM $table;";
        $date_loc = "ru_RU";

	break;
    default:
        // укр
        setlocale(LC_ALL, "uk_UA.UTF-8");
        $query = "SELECT label, label_uk AS text FROM $table;";
        $date_loc = "uk_UA";
}
if($result = $mysqli_wb->query($query)){
    while( $row = $result->fetch_array(MYSQLI_ASSOC) ){
        $labels[$row['label']] = $row['text'];
    }
} else {
    echo $mysqli_wb->error;
    exit();
}
$result->close();
        
mb_internal_encoding("UTF-8");
function mb_ucfirst($text) {
    return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
}