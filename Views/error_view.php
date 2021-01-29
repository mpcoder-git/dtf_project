<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">	
	
		<div class="mainblock innerblock">
			<h1 class="pageTitle">ОШИБКА</h1>
			<p style="text-align: center;"><?php echo $message; ?></p>		

		</div>
	
</div>