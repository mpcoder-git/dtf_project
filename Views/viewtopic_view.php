<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">	
	<div class="mainblock innerblock">
		
		<div class="forum_topPanel">
		<a href="/forum" class="mainlink">Форумы</a> > <a href="/forum/viewtopics/<?php echo $pid; ?>" class="mainlink"><?php echo $sectionname; ?></a> > <?php echo $topicname; ?>
		</div>
		<?php include("Blocks/messagespagenav.php"); ?>
		
		<?php	
			while( $row = mysqli_fetch_assoc($limitedmessageslist_result) ){
				
				$messageid = $row['id'];
				$avtorid = $row['userid'];
				$autornick = $row['nick'];
				$avtorgroup = $row['groupusers'];				
				$message = $row['message'];
				$createdata = $row['createdata'];
				
				include("Blocks/messagesblock.php");
			}						
		?>
		
		<?php include("Blocks/messagespagenav.php"); ?>
				
		<?php
			if(isset($_SESSION["session_userid"])) {
				require_once("Blocks/quickreplyintopic.php");	
			}
		?>		
		
	</div>
</div>		