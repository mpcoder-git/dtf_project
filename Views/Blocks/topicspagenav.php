<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="navigationPanel">
	<div class="pageNavigation">
	
		<?php

		// Проверяем нужны ли стрелки назад
		if ($page != 1) $pervpage = '<a href=viewtopics/'.$pid.'/1>Первая</a> | <a href=viewtopics/'.$pid.'/'. ($page - 1) .'>Предыдущая</a> | ';
		// Проверяем нужны ли стрелки вперед
		if ($page != $total) $nextpage = ' | <a href=viewtopics/'.$pid.'/'. ($page + 1) .'>Следующая</a> | <a href=viewtopics/'.$tid.'/' .$total. '>Последняя</a>';

		// Находим две ближайшие станицы с обоих краев, если они есть
		if($page - 5 > 0) $page5left = ' <a href=viewtopics/'.$pid.'/'. ($page - 5) .'>'. ($page - 5) .'</a> | ';
		if($page - 4 > 0) $page4left = ' <a href=viewtopics/'.$pid.'/'. ($page - 4) .'>'. ($page - 4) .'</a> | ';
		if($page - 3 > 0) $page3left = ' <a href=viewtopics/'.$pid.'/'. ($page - 3) .'>'. ($page - 3) .'</a> | ';
		if($page - 2 > 0) $page2left = ' <a href=viewtopics/'.$pid.'/'. ($page - 2) .'>'. ($page - 2) .'</a> | ';
		if($page - 1 > 0) $page1left = '<a href=viewtopics/'.$pid.'/'. ($page - 1) .'>'. ($page - 1) .'</a> | ';

		if($page + 5 <= $total) $page5right = ' | <a href=viewtopics/'.$pid.'/'. ($page + 5) .'>'. ($page + 5) .'</a>';
		if($page + 4 <= $total) $page4right = ' | <a href=viewtopics/'.$pid.'/'. ($page + 4) .'>'. ($page + 4) .'</a>';
		if($page + 3 <= $total) $page3right = ' | <a href=viewtopics/'.$pid.'/'. ($page + 3) .'>'. ($page + 3) .'</a>';
		if($page + 2 <= $total) $page2right = ' | <a href=viewtopics/'.$pid.'/'. ($page + 2) .'>'. ($page + 2) .'</a>';
		if($page + 1 <= $total) $page1right = ' | <a href=viewtopics/'.$pid.'/'. ($page + 1) .'>'. ($page + 1) .'</a>';

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
	
	<div class="replyintopic">
		<?php if (!isset($_SESSION['session_userid'])) {
		echo "Чтобы создать тему, вам нужно залогиниться";
		} else { ?>
		<a href="Newtopic.php?pid=<?php echo $pid; ?>" class="mainbutton">Создать новую тему</a>	
		<?php }
		?>	
	</div>
</div>