<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'include/vars.php';
include 'include/database.php';
include 'include/select_lang.php';

$request = explode("/", ltrim(urldecode($_SERVER['REQUEST_URI']), "/pm"));
if (empty($request[0])){
    #$url = "Location: $protocol$sitename/".$labels['enter'];
    $url = "Location: $protocol$sitename/enter";
    header($url);
    exit;
}

switch ($request[0]) {
    case 'аутентификация':
    case 'аутентифікація':
    case 'auth':
        $username = isset($_POST['username']) ? $_POST['username'] : "";
        $password = isset($_POST['password']) ? $_POST['password'] : "";
        include 'include/auth_user.php';
        $url1 = "Location: $protocol$sitename/$base_url1/PON/";
        header($url1);
        break;
    case $base_url1:
        $username = isset($_POST['username']) ? $_POST['username'] : $_COOKIE["pm_username"];
        $password = isset($_POST['password']) ? $_POST['password'] : $_COOKIE["pm_password"];
        $spLevel = isset($_POST['spLevel']) ? $_POST['spLevel'] : $_COOKIE["pm_spLevel"];
        include 'include/auth_user.php';
        switch ($request[1]) {
            case 'PON':
                $title = "PON monitor - PON";
                require 'include/header.php';
                require 'include/navigator.php';
                require 'include/functions.php';
                require 'include/pon_functions.php';
                if(!isset($request[2])){
                    $request[2] = "";
                }
                if (is_numeric($request[2])){
                    $olt_id = $request[2];
                    $sfp_s = 0;
                    if(!isset($request[3])){
                        $request[3] = "";
                    }
                    if (is_numeric($request[3])){
                        $sfp_s = $request[3];
                    }
                    if(!isset($request[4])){
                        $request[4] = "";
                    }
                    if (is_numeric($request[4]) AND isset($request[5])){
                        $onu_n = $request[4];
                        $onu_mac = $request[5];
                        include 'include/onu_detail.php';
                    }else{
                        $OrderOnu = isset($_COOKIE["pm_ordreonu"]) ? $_COOKIE["pm_ordreonu"] : 'order_id ASC';
                        include 'include/onu_list.php';
                    }
                    
                }else{
                    include 'include/olt_list.php';
                }
                break;
            case 'настройки':
            case 'settings':
                $title = "PON monitor - Настройки";
                include 'include/header.php';
                include 'include/navigator.php';
                if(!isset($request[2])){
                    $request[2] = "";
                }
                switch ($request[2]) {
                    case 'OLT':
                        if (is_numeric($request[3])){
                            $olt_mode = 'edit';
                            $olt_id = $request[3];
                        }else{
                            $olt_mode = 'add';
                        }
                        include 'include/olt_manage.php';
                        break;
                    default :
                            include 'include/settings.php';
                }
                break;
            case 'switchport':
                $olt_id = $request[2];
                $port = $request[3];
                $pkey = $request[4];
                $title = "PON monitor - Порт $port";
                include 'include/header.php';
                include 'include/pon_functions.php';
                include 'include/userport.php';

                break;
            default:
                //exit
                break;
        }

        include 'include/footer.php';
        $mysqli_wb->close();

        break;
            case 'выход':
            case 'вихід':
            case 'exit':
                SetCookie("password","",0,"/");
                SetCookie("spLevel","",0,"/");
                header("Location: $protocol$sitename/enter");
                break;

    break;
        default:
        $title = $labels['bilenter'];
        include 'include/header.php';
        $username = isset($_COOKIE['username']) ? $_COOKIE['username'] : "";
        $message = "";
        if(isset($request[1])){
            switch ($request[1]) {
                case 'ошибка':
                    $message = "<span class=\"c1\"><strong>Ошибка авторизации. Проверьте вводимые данные.</strong></span>";
                    break;
                case 'помилка':
                    $message = "<span class=\"c1\"><strong>Помилка авторизації. Перевірте точність даних.</strong></span>";
                    break;
            }
        }
        include 'include/enterform.php';
        include 'include/footer.php';
        break;
}
?>