<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">
	<div  class="mainblock innerblock">
		<h1 class="pageTitle">НОВОСТИ</h1>
		<?php		
		while( $row = mysqli_fetch_assoc($news_result) ){			
			include("Blocks/newsblock.php");
		}					
		?>
	</div>
</div>