<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">	
	<div class="mainblock innerblock">
		
		<div class="forum_topPanel">
		<a href="/forum/" class="mainlink">Форумы</a> > <?php echo $sectionname; ?>
		</div>
		<?php include("Blocks/topicspagenav.php"); ?>
		
		<div class="forum_sectionRow">
			
			<div class="forum_sectionHeader"></div>
			
						
				<?php					
					while( $row = mysqli_fetch_assoc($limitedtopicslist_result) ){						
						include("Blocks/topicsblock.php");
					}								
				?>
			
		</div>
		
		<?php include("Blocks/topicspagenav.php"); ?>
		<br>
	</div>
</div>	