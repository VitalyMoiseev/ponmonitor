<?php
# Параметри доступа БД
$dbhost_wb = 'localhost';
$dbuser_wb = 'ponmon';
$dbpassword_wb = 'ponmon';
$database_wb = 'ponmon';

# Изменить при необходимости и переименовать таблицы в файле ponmon.sql
$tbl_pref = 'pm_';

# Email в колонтитуле
$admin_email = "admin@site";

#$protocol - 'http://' or 'https://'
$protocol = 'http://';

#$sitename = доменное имя (с полным путем);
$sitename = 'site/pm';

# Метод съема таблицы FDB. При проблемах - заменить на 'telnet'
$FDB_method = 'SNMP';

# Путь к логу скрипта опроса ОНУ
$log_file = "/var/pm/logs/wb.log";

# Названия уровней доступа к системе:
# администратор - полный доступ
# монтажник - нет доступа к настройкам
# оператор - только просмотр ОНУ, без редактирования
$levelnames = array ("администратор", "монтажник", "оператор");

# Сколько хранить историю сигналов ОНУ. В формате MYSQL interval
$PwrHistTerm = "3 MONTH";

#############################################################################
$base_url1 = 'PM';
$date_format1 = 'd.m.Y';
$years = "2019";
$servername = "PonMonitor";
$version = "1.5";
$support_mail = "<a href=\"mailto:$admin_email\">$admin_email</a>";
setlocale(LC_ALL, "uk_UA.utf8");
