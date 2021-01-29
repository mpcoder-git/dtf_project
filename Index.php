<?php
require_once('./Config/nocodview.php');
require_once('./Core/model.php');
require_once('./Core/view.php');
require_once('./Core/controller.php');
require_once('./Core/route.php');

//подключаем класс рассылки почты
require_once('./Core/SendMailSmtpClass.php');


// Отключить вывод ошибок (защитит от представления хакеру информации)
//если раскоментировано - то ошибки не показываются
//error_reporting(0);

Route::start(); // запускаем маршрутизатор
?>