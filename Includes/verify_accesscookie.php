<?php 
	if (!isset($_COOKIE["AccessCookie"])){
		Route::ErrorPage404();
	} 		
	if ($_COOKIE["AccessCookie"] != ACCESS_COOKIE){
		Route::ErrorPage404();				
	} 	
?>