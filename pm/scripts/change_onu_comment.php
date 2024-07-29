<?php

if (!isset($_GET['id'])){
    exit();
}
if (!isset($_GET['comment'])){
    exit();
}

require '../include/vars.php';
require '../include/database.php';
require '../include/select_lang.php';

$table = $tbl_pref.'onu';
$query = "UPDATE $table SET comment = '".$_GET['comment']."' WHERE Id = ".$_GET['id'].";";
if ($mysqli_wb->query($query)){
    echo $labels['Saved'];
    echo "<script type='text/javascript'> document.location.reload(true); </script>";
}else{
    echo $labels['Error'];
}
$mysqli_wb->close();