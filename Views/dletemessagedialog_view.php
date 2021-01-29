<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">
	<div  class="mainblock innerblock">
		<h1 class="pageTitle">Добавление нового сообщения </h1>

		<div class="centeredblock">
			<p>Вы уверены, что хотите удалить сообщение?</p>
			<p><a href="/forum/deletemessage/<?php echo $mid; ?>">ДА</a> | <a href="/forum/viewtopic/<?php echo $tid; ?>">НЕТ</a></p>	
		</div>	

	</div>
</div>		