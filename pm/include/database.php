<?php

// _wb - web billing database
$mysqli_wb = new mysqli($dbhost_wb, $dbuser_wb, $dbpassword_wb, $database_wb);
if ($mysqli_wb->connect_error) {
    die('Ошибка подключения ('.$mysqli_wb->connect_errno.') '.$mysqli_wb->connect_error);
}
$mysqli_wb->set_charset("utf8");

?>
