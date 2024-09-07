<?php
function ip_in_range( $ip, $range ) {
	if ( strpos( $range, '/' ) == false ) {
		$range .= '/32';
	}
	// $range is in IP/CIDR format eg 127.0.0.1/24
	list( $range, $netmask ) = explode( '/', $range, 2 );
	$range_decimal = ip2long( $range );
	$ip_decimal = ip2long( $ip );
	$wildcard_decimal = pow( 2, ( 32 - $netmask ) ) - 1;
	$netmask_decimal = ~ $wildcard_decimal;
	return ( ( $ip_decimal & $netmask_decimal ) == ( $range_decimal & $netmask_decimal ) );
}
# Параметри доступа БД
$dbhost_wb = 'localhost';
$dbuser_wb = 'ponmon';
$dbpassword_wb = '';
$database_wb = 'ponmon';

# Изменить при необходимости и переименовать таблицы в файле ponmon.sql
$tbl_pref = 'pm_';

# Email в колонтитуле
$admin_email = "admin@site";

#$protocol - 'http://' or 'https://'
$protocol = 'http://';

$ip = $_SERVER['REMOTE_ADDR'];
$localnet = '10.0.0.0/16';
if (php_sapi_name() !== 'cli'){
	if (ip_in_range( $ip, $localnet)){
    	#$sitename = доменное имя (с полным путем);
    	$sitename = '/pm';
	}else{
    	#$sitename = доменное имя (с полным путем);
    	$sitename = '1/pm';
	}
}

# Метод съема таблицы FDB. При проблемах - заменить на 'telnet'
$FDB_method = 'SNMP';

# Путь к логу скрипта опроса ОНУ
$log_file = "/var/log/pm/pm_check_onu.log";

# Названия уровней доступа к системе:
# администратор - полный доступ
# монтажник - нет доступа к настройкам
# оператор - только просмотр ОНУ, без редактирования
$levelnames = array ("администратор", "монтажник", "оператор");

# Сколько хранить историю сигналов ОНУ. В формате MYSQL interval
$PwrHistTerm = "3 MONTH";

# Power difference for power history
$pwr_diff_to_rec = 0.2;

#############################################################################
$base_url1 = 'PM';
$date_format1 = 'd.m.Y';
$years = "2019-2024";
$servername = "PonMonitor";
$version = "2.b";
$support_mail = "<a href=\"mailto:$admin_email\">$admin_email</a>";
$pon_types_names = array ("EPON", "GPON");
setlocale(LC_ALL, "uk_UA.utf8");
