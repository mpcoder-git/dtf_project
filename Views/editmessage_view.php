<?php
//защита от просмотра файла вручную через адресную строку
require_once('./Includes/verify_varreadfile.php'); 	
?>
<script src="./Nicedit/nicEdit.js" type="text/javascript"></script>
<script src="<?php echo PATCH_JAVASCRIPT.'ajax.js'; ?>"></script>	
<div class="content">
	<div  class="mainblock innerblock">
		<h1 class="pageTitle">Изменение сообщения </h1>
		
		<div class="newmessageform centeredblock">
			<form name="editmessageform" id="editmessageform" action="/forum/posteditmessageform" class="mainform"   method="POST">
						
				<p>
					<label for="messageeditor" class="formlabel">Текст сообщения:<br />
					<textarea  id="messageeditor" name="messageeditor" rows="20" cols="110" class="maintextarea"><?php echo $messagetext; ?></textarea>		
					<script type="text/javascript">
						var nicedit = new nicEditor({fullPanel : true,  iconsPath : './Nicedit/nicEditorIcons.gif'}).panelInstance('messageeditor');
					</script>	
				</p>
				
				<p class="invisible">
				<input type="text" name="name" id="name"  value="" size="30" maxlength="30" />
				<input type="text" name="email" id="email"  value="" size="30" maxlength="30" />
				<input type="text" name="captcha" size="10" maxlength="10">
				</p>

				<input type="hidden" name="messageid" value="<?php echo $mid;  ?>">
				<input type="hidden" name="snumber" value="<?php echo md5(md5(session_id()));  ?>">
				<input type="hidden" name="skey" value="<?php echo $skey; ?>">
				<br>
				<p><input type="submit" name="posteditmessage" class="mainbutton" value="Сохранить" /></p>
		
			</form>
			
			<br><br>		
			
			<div id="attachfiles">
			<?php 							
				require_once("./Views/Blocks/showattaches.php"); 
			?>

			</div>
						
			<br>
			
			<form id="uploadattachform" enctype="multipart/form-data"  method="POST">
					
				<fieldset>
				<legend>Форма загрузки файлов</legend>
				<!-- Поле MAX_FILE_SIZE должно быть указано до поля загрузки файла 5 Мб -->
				<input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
				<!-- Название элемента input определяет имя в массиве $_FILES -->
				Отправить этот файл: <input name="attachfile" id="attachfile" type="file" />
				<p class="invisible">
				<input type="text" name="name" id="name"  value="" size="30" maxlength="30" />
				<input type="text" name="email" id="email"  value="" size="30" maxlength="30" />
				<input type="text" name="captcha" size="10" maxlength="10">
				</p>

				<input type="hidden" name="userid" id="userid"  value="<?php echo $userid;  ?>" size="10" />
				<input type="hidden" name="topicid" id="topicid"  value="<?php echo $tid;  ?>" size="10" />
				<input type="hidden" name="messageid" id="messageid"  value="<?php echo $mid;  ?>" size="10" />
				<input type="hidden" name="snumber" value="<?php echo md5(md5(session_id()));  ?>">

				<input type="button" name="postattachfile" id="postattachfile"  class="mainbutton"  value="Загрузить файл" onclick="post_form()" />
				</fieldset>
			</form>
			
		</div>

	</div>
</div>