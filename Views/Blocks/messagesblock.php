<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<ul >
				
			<li >
			
				
				<div class="forum_message_rowBlock">
					<div class="forum_message_autorCol">
						
						<div class="forum_message_userInfo">
							<b><?php echo $autornick; ?></b>
							<br>
							<?php 
							if ($avtorgroup==0) { echo "Пользователь"; } 
							if ($avtorgroup==1) { echo "Администратор"; } 
							?>
						</div>
						
						<div class="forum_message_userAvatar">
							<?php
								$avatarpicture = $this->find_avatar($avtorid);
								
								if ($avatarpicture !== "") {   ?>
								
								<img class="avatarImage"  src="<?php echo PATCH_AVATARS.$avtorid."/".$avatarpicture; ?> " alt="">
								
								<?php
								}				
							?>	
						</div>
						

					</div>
					<div class="forum_message_textCol">
					
						<div class="mainhelperText forum_messageInfo">Дата создания сообщения: <?php echo $this->ref_date($createdata); ?>   № сообщения: <?php echo $messageid; ?></div>
						<div class="forum_messageButtons">
							<?php
								if (isset($_SESSION["session_userid"])){
									if ($avtorid == $_SESSION["session_userid"] || (isset($_SESSION["session_userid"]) && $usergroup==1)){
								?>	
								<a href="/forum/editmessage/<?php echo $messageid; ?>" class="mainbutton">Редактировать</a>
								<a href="/forum/deletemessagedialog/<?php echo $messageid; ?>" class="mainbutton">Удалить</a>
							
							<?php	}} ?>						
						
							<?php
								if (isset($_SESSION["session_userid"])){ ?>
								<a href="/forum/replyquotemessage/<?php echo $messageid; ?>" class="mainbutton">Ответ с цитированием</a>
							<?php } ?>													
						</div>
						
					
						<br>
						<?php echo $message; ?>
						<br><br>
					
						<?php 
						//вывести блок вложений если они есть
						$counttrueattach = $this->find_attaches($messageid);
						if ($counttrueattach > 0){
							include("messagesattachesblock.php");
						}

						?>
					
					</div>
					<div style="width: 100%; clear: both;"></div>
					
					
				</div>
		
			
	
			</li>	
				


</ul>
