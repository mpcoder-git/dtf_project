<?php

define("DOMAIN_NAME", "localhost");

define("WEBSOKET_IP", "127.0.0.1");
define("WEBSOKET_PORT", 3333);

// Database Constants
define("DB_SERVER", "localhost");
define("DB_USER", "root");
define("DB_PASS", "");
define("DB_NAME", "dtf");


//пути к каталогам
define("PATCH_AVATARS", "/Avatars/");
define("PATCH_ATTACHES", "/Attaches/");
define("PATCH_LESSONS", "/Lessonsdata/");
define("PATCH_JAVASCRIPT", "/Js/"); 
define("PATCH_IMAGES", "/Images/");
define("PATCH_NEWS", "./Newsattaches/");
define("PATCH_SIGNALS", "./Signalsfiles/");

define("ACCESS_COOKIE", "abcd"); 

define('MAX_ID', 9999); 					//максимальный номер темы/сообщения
define('MESSAGES_IN_PAGE', 10); 			//количество сообщений на странице
define('TOPICS_IN_PAGE', 10); 			//количество тем на странице
define('MAX_ATTACHFILE_SIZE', 3000000); 	//максимальный размер файла вложения (3 мб)
define('MAX_AVATARFILE_SIZE', 100000); 	//максимальный размер файла аватара (100 кб)

define('MAX_RECOVERY_MINUTS', 15); 		//количество минут, за которые надо восстановить пароль

date_default_timezone_set("UTC");

//настройка внешнего почтового ящика для отправки почты
define("SMTP_EMAIL_ADRESS", "mail@example.com"); 
define("SMTP_EMAIL_PASSWORD", "pass");
define("SMTP_EMAIL_SERVER", "ssl://smtp.yandex.ru");
define("SMTP_EMAIL_SERVERPORT", 465);
define("SMTP_EMAIL_ENCODING", "UTF-8");

//параметры отправляемого письма
define("SMTP_EMAIL_FROMNAME", "Day Traders Factory robot"); //что будет написано от кого
define("SMTP_EMAIL_FROMEMAIL", "mail@example.com"); //что будет написано с какого емайла отправлено
?>