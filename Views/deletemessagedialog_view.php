<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">
	<div  class="mainblock innerblock">
		<h1 class="pageTitle">Удаление сообщения </h1>

		<div class="centeredblock" style="text-align: center;">
			<p>Вы уверены, что хотите удалить сообщение?</p>
			<p><a href="/forum/deletemessage/<?php echo $mid; ?>" class="mainbutton">ДА</a> | <a href="/forum/viewtopic/<?php echo $tid; ?>/<?php echo $page; ?>" class="mainbutton">НЕТ</a></p>	
		</div>	

	</div>
</div>		