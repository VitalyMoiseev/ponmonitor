CREATE TABLE `pm_olt` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `snmp_port` int(11) DEFAULT '161',
  `community` varchar(255) DEFAULT 'public',
  `communityrw` varchar(255) DEFAULT 'private',
  `telnet_port` int(11) DEFAULT '23',
  `telnet_name` varchar(255) DEFAULT 'admin',
  `telnet_password` varchar(255) DEFAULT '1234',
  `status` tinyint(3) DEFAULT NULL,
  `last_act` datetime DEFAULT NULL,
  `olt_type` varchar(255) NOT NULL DEFAULT '1',
  `type` int(11) NOT NULL DEFAULT '0',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `pm_olt_sfp` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `olt` int(11) DEFAULT NULL,
  `sfp` varchar(255) DEFAULT NULL,
  `count_onu` int(11) DEFAULT NULL,
  `online_count` int(11) DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `olt` (`olt`,`sfp`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `pm_onu` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `mac` varchar(255) DEFAULT NULL,
  `olt` smallint(6) DEFAULT NULL,
  `onu_name` varchar(255) DEFAULT NULL,
  `pwr` varchar(255) DEFAULT '0',
  `last_act` datetime DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `userid` varchar(255) DEFAULT NULL,
  `status` tinyint(3) DEFAULT '0',
  `present` tinyint(3) DEFAULT '0',
  `order_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `key` (`mac`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `pm_onu_pwr_history` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `mac` varchar(20) DEFAULT NULL,
  `pwr` decimal(6,1) DEFAULT NULL,
  `starttime` datetime DEFAULT NULL,
  `stoptime` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `pm_settings` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `parametr` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

INSERT INTO `pm_settings` VALUES (1,'check_onu_state_enable','1'),(2,'onu_check_begin','0'),(3,'onu_check_last_start','2019-09-06 23:28:19'),(4,'check_onu_state_interval','10'),(5,'max_snmp_try','3');

CREATE TABLE `pm_texts` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  `label_uk` text,
  `label_ru` text,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `label` (`label`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8;

INSERT INTO `pm_texts` VALUES (1,'Add','Додати','Добавить'),(2,'All','Всі','Все'),(3,'auth','аутентифікація','аутентификация'),(4,'bilenter','Вхід у Pon Monitor','Вход в Pon Monitor'),(5,'Change','Змінити','Изменить'),(6,'Com','Коментар','Комментарий'),(7,'Couplers','Каплери','Каплеры'),(8,'do_enter','Увійти','Войти'),(9,'Edit','Редагувати','Редактировать'),(10,'enter','вхід','вход'),(11,'error','помилка','ошибка'),(12,'Esc','Скасувати','Отмена'),(13,'exit','вихід','выход'),(14,'Find','Знайти','Найти'),(15,'HistPwr','Історія сигналів','История сигналов'),(16,'IDKlient','ID клієнта','ID клиента'),(17,'L_act','Ост. активність','Посл. активность'),(18,'Last_act','Остання активніть','Последняя активность'),(19,'Login','Логін','Логин'),(20,'name','Ім\'я','Имя'),(21,'Note','Опис','Описание'),(22,'password','Пароль','Пароль'),(23,'pon01','Список ONU','Список ONU'),(24,'pon02','Оновити список ONU по всім OLT','Обновить список ONU по всем OLT'),(25,'pon03','Опитати всі OLT? Це займе кілька хвилин!','Опросить все OLT? Это займет несколько минут!'),(26,'pon04','Список OLT','Список OLT'),(27,'pon05','Сигнал','Сигнал'),(28,'pon06','Остання реєстрація','Последняя регистрация'),(29,'pon07','Остання дереєстрація','Последняя дерегистрация'),(30,'pon08','Причина','Причина'),(31,'pon09','Записати ONU як порт клієнта','Записать ONU как порт клиента'),(32,'Refresh','Оновити','Обновить'),(33,'Save','Зберегти','Сохранить'),(34,'settings','настройки','настройки'),(35,'Show','Показати','Показать'),(36,'Splitters','Сплітери','Сплиттеры'),(37,'Stat','Стан','Состояние'),(38,'status','статус','статус'),(39,'table','таблиця','таблица'),(40,'User','Користувач','Пользователь'),(41,'Write','Записати','Записать'),(42,'Nazv','Назва','Название'),(43,'PlaceSet','Місце встановлення','Место установки'),(44,'AccessP','Параметри доступу','Параметры доступа'),(45,'Check','Перевірити','Проверить'),(46,'pon10','Мінімальна довжина','Минимальная длина'),(47,'pon11','символів','символов'),(48,'pon12','Мінімальна довжина назви - 5 символів','Минимальная длина названия - 5 символов'),(49,'pon13','Мінімальна довжина місця встановлення - 5 символів','Минимальная длина места установки - 5 символов'),(50,'pon14','не відповідає, перевірте','не отвечает, проверьте'),(51,'Uacc','Користувачи','Пользователи'),(52,'Laccess','Рівень доступу','Уровень доступа'),(53,'Manage','Керування','Управление'),(54,'Change_pas','Змінити пароль','Изменить пароль'),(55,'Change_lev','Змінити рівень','Изменить уровень'),(56,'Delete','Видалити','Удалить'),(57,'Operations','Операції','Операции'),(58,'set01','Уведені паролі не співпадають','Введенные пароли не совпадают'),(59,'set02','Мінімальна довжина пароля','Минимальная длинна пароля'),(60,'set03','Замінити пароль','Заменить пароль'),(61,'set04','Мінімальна довжина імені','Минимальная длинна имени'),(62,'set05','Ви не маєте доступу до настройок','У вас нет доступа к настройкам'),(63,'set06','Видалити користувача','Удалить пользователя'),(64,'set07','Змінити рівень','Изменить уровень'),(65,'set08','доданий','добавлен'),(66,'set09','Рівень доступу змінено','Уровень доступа изменен'),(67,'Saved','Збережено','Сохранено'),(68,'set10','Пороль користуваяа змінено','Пароль пользователя изменен'),(69,'deleted','видалений','Удален'),(70,'H1','З','С'),(71,'H2','До','По'),(72,'port01','Стан порту','Состояние порта'),(73,'port02','Швидкість порту','Скорость порта'),(74,'bytes','байти','байти'),(75,'packets','пакети','пакеты'),(76,'SwDontResp','Немає відповіді від OLT','Нет ответа от OLT'),(77,'s01','Мінімум 2 символи для пошуку','Минимум 2 символа для поиска'),(78,'comrwnotuse','Якщо не використовується - залишити пустим!','Если не используется - оставьте пустым!'),(79,'Dist','Відстань','Расстояние');

CREATE TABLE `pm_users` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `splevel` tinyint(3) DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

INSERT INTO `pm_users` VALUES (1,'admin','$2y$10$P2ZFsmt0FPNzVor22hcj3OUwql.IdyqWa8H4j0gD1bIeK6vM4WCJi',0);
