<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">
	<div class="mainblock innerblock">
		
		<div class="cabinetGrid">

			<div  class="cabinet_menu ">
				<?php require_once("Blocks/cabinetmenu.php"); ?>
			</div>

			<div class="cabinet_content">
				<h1>Загрузка/смена аватара</h1>
				
				<div class="cabinet_contentGrid">
				
					<div class="avatarImage">					
						<?php						
						if ($countavatar == 1) {  ?>
						<img src="<?php echo PATCH_AVATARS.$userid."/".$avatarpicture; ?>" alt="">
						<?php
						
						}
						?>	
					</div>	
		
					<div class="form_avatar" >

						<form enctype="multipart/form-data" action="/changeavatar/uploadavatar" method="POST">
							
							
							<!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла 100 кб -->
							<input type="hidden" name="MAX_FILE_SIZE" value="<? echo MAX_AVATARFILE_SIZE; ?>" />
							<!-- Название элемента input определяет имя в массиве $_FILES -->
							<br>
							<p><?php if ($countavatar == 0) { echo "Аватар не  загружен"; } else { echo "Название файла вашего аватара: ".$avatarpicture; } ?></p>
							<br>
							
							<p>Выберите аватар: <input name="userfile" type="file" class="maininput" required /></p>
							<input type="hidden" name="userid" id="userid"  value="<?php echo $userid;  ?>" size="10" />
							<br>
							<p class="mainhelperText">ВНИМАНИЕ: Вы можете загрузить файлы с расширениями gif,jpg,png размером не более 100 килобайт!</p>
							<p class="invisible">
							<input type="text" name="name" id="name"  value="" size="30" maxlength="30" />
							<input type="text" name="email" id="email"  value="" size="30" maxlength="30" />
							<input type="text" name="captcha" size="10" maxlength="10">
							</p>
							<input type="hidden" name="snumber" value="<?php echo md5(md5(session_id()));  ?>">
							<input type="hidden" name="skey" value="<?php echo $skey; ?>">
							<p>
							<input type="submit" name="postfile" class="mainbutton"  value="Загрузить файл" /> 
							<?php
							if ($countavatar == 1) { ?>
							<a href="/changeavatar/deleteavatar" class="mainbutton">Удалить аватар</a>
							<?php
							}
							?>	
							</p>
						</form>

					</div>
				</div>
			</div>

		</div>
		
	</div>
</div>