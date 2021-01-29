<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">		
	<div class="mainblock innerblock">
			
		<div class="forum_topPanel">
		Форумы
		</div>
		<?php		
			while( $row = mysqli_fetch_assoc($forumcatalog_result) ){
				
				include("Blocks/forumcatalog.php");
			}
					
		?>		
	</div>
</div>