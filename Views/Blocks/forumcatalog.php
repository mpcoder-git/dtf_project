<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<ul class="forum_sectionRow">
				
			<div class="forum_sectionHeader">
			<b><?php echo $row['sectionname']; ?></b>
			</div>
				
<?php
			//выбор подразделов
			while( $rowp = mysqli_fetch_assoc(${'forumsubcatalog_result'.$row['id']}) ){ ?>
			<li class="forum_sectionSubrow">
				
				<ul class="gorientation">
					<li class="forum_topicsNamecol">
						<a class="mainlink" href="/forum/viewtopics/<?php echo $rowp['id']; ?>"><?php echo $rowp['sectionname']; ?></a>
						<br>
						<span class="forum_secondaryText">Тем: <?php echo $rowp['topicscnt']; ?></span>
					</li>
								
				</ul>
			
	
			</li>	
				
			<?php }
?>

</ul>
<br>