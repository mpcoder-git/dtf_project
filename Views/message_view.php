<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">	

	
		<div class="innerblock">
			<h1 class="pageTitle">СООБЩЕНИЕ</h1>
			<p style="text-align: center;"><?php echo $message; ?></p>		

		</div>
	
</div>