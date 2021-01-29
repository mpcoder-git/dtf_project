<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="navigationPanel">
	<div class="forum_pageNavigation">
	
	<?php

	// Проверяем нужны ли стрелки назад
	if ($page != 1) $pervpage = "<a href=\"/forum/viewtopic/".$tid."/1\" class=\"mainlink\">Первая</a> | <a href=\"/forum/viewtopic/".$tid."/".($page - 1)."\" class=\"mainlink\">Предыдущая</a> | ";
	// Проверяем нужны ли стрелки вперед
	if ($page != $total) $nextpage = " | <a href=\"/forum/viewtopic/".$tid."/".($page + 1)."\" class=\"mainlink\">Следующая</a> | <a href=\"/forum/viewtopic/".$tid."/" .$total."\" class=\"mainlink\">Последняя</a>";

	// Находим две ближайшие станицы с обоих краев, если они есть
	if($page - 5 > 0) $page5left = " <a href=\"/forum/viewtopic/".$tid."/". ($page - 5) ."\" class=\"mainlink\">". ($page - 5) ."</a> | ";
	if($page - 4 > 0) $page4left = " <a href=\"/forum/viewtopic/".$tid."/". ($page - 4) ."\" class=\"mainlink\">". ($page - 4) ."</a> | ";
	if($page - 3 > 0) $page3left = " <a href=\"/forum/viewtopic/".$tid."/". ($page - 3) ."\" class=\"mainlink\">". ($page - 3) ."</a> | ";
	if($page - 2 > 0) $page2left = " <a href=\"/forum/viewtopic/".$tid."/". ($page - 2) ."\" class=\"mainlink\">". ($page - 2) ."</a> | ";
	if($page - 1 > 0) $page1left = " <a href=\"/forum/viewtopic/".$tid."/". ($page - 1) ."\" class=\"mainlink\">". ($page - 1) ."</a> | ";

	if($page + 5 <= $total) $page5right = " | <a href=\"/forum/viewtopic/".$tid."/". ($page + 5) ."\" class=\"mainlink\">". ($page + 5) ."</a>";
	if($page + 4 <= $total) $page4right = " | <a href=\"/forum/viewtopic/".$tid."/". ($page + 4) ."\" class=\"mainlink\">". ($page + 4) ."</a>";
	if($page + 3 <= $total) $page3right = " | <a href=\"/forum/viewtopic/".$tid."/". ($page + 3) ."\" class=\"mainlink\">". ($page + 3) ."</a>";
	if($page + 2 <= $total) $page2right = " | <a href=\"/forum/viewtopic/".$tid."/". ($page + 2) ."\" class=\"mainlink\">". ($page + 2) ."</a>";
	if($page + 1 <= $total) $page1right = " | <a href=\"/forum/viewtopic/".$tid."/". ($page + 1) ."\" class=\"mainlink\">". ($page + 1) ."</a>";

	// Вывод меню если страниц больше одной

	if ($total > 1)
	{
	Error_Reporting(E_ALL & ~E_NOTICE);
	echo "<div class=\"pstrnav\">";
	echo $pervpage.$page5left.$page4left.$page3left.$page2left.$page1left.'<b>'.$page.'</b>'.$page1right.$page2right.$page3right.$page4right.$page5right.$nextpage;
	echo "</div>";
	}

	?>
	</div>
	
	<div class="forum_buttonPanel">
		<?php if (!isset($_SESSION['session_userid'])) {
		echo "Чтобы ответить в теме, вам нужно залогиниться";
		} else { ?>
		
		<?php
		if ($avtortopic == $_SESSION["session_userid"] || (isset($_SESSION['session_userid']) && $usergroup==1)){
		?>	
		<a href="/forum/edittopicname/<?php echo $tid; ?>" class="mainbutton">Редактировать название темы</a>								
		<?php	} ?>


		<a href="/forum/newmessage/<?php echo $tid; ?>" class="mainbutton">Ответить</a>	
		<?php }
		?>	
	</div>
</div>