<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ Route::ErrorPage404(); }	
?>