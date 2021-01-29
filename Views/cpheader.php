<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<header class="header">
	<div class="innerblock">
	
		<div>DTF Control panel</div>
		
		<nav role="navigation" class="header_navmenuGrid">
		
			<div class="header_navmenuElement"><a class="header_link" href="/controlpanel">ГЛАВНАЯ</a></div>	
			<div class="header_navmenuElement"><a class="header_link" href="/controlpanel/users">ПОЛЬЗОВАТЕЛИ</a></div>	
			<div class="header_navmenuElement"><a class="header_link" href="/controlpanel/news">НОВОСТИ</a></div>
			<div class="header_navmenuElement"><a class="header_link" href="/controlpanel/wsserver">WEBSOCKET SERVER</a></div>
		
		</nav>
		
		<div><a href="/controlpanel/logout" class="header_link">Выйти</a></div>
	
	</div>
</header>