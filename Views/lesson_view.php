<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">
	<div  class="mainblock innerblock">

		<?php		
			require_once($lessonfile);
		
			if ($number!=10){
			?>
			<div>
			<a href="/lessons/test" class="main_buttonGreen"><b>ТЕСТИРОВАНИЕ</b></a>
			</div>
			<?php
			}
		?>	

	</div>
</div>