<?php
//защита от просмотра файла вручную через адресную строку
require_once('./Includes/verify_varreadfile.php'); 		
?>
<script src="/Nicedit/nicEdit.js" type="text/javascript"></script>
<script src="<?php echo PATCH_JAVASCRIPT.'ajax_cp.js'; ?>"></script>
<script>
		var nicedit;
		
		function addNicedit() {
		nicedit = new nicEditor({fullPanel : true,  iconsPath : '../nicedit/nicEditorIcons.gif'}).panelInstance('newseditor');
		}
		function removeNicedit() {
		nicedit.removeInstance('newseditor');
		}
	</script>
<div class="content">
	<div  class="mainblock innerblock">
		<h1 class="pageTitle"><?php  echo $title_page;  ?></h1>
		
		<div class=" centeredblock">
			<form name="newseditorform" id="newseditorform" action="/controlpanel/postnewseditorform" class="mainform"   method="POST">
						
				<p>
					<label for="newstitle" >Заголовок новости:<br />
					<input type="text" name="newstitle" class="maininput" value="<?php if (isset($newstitle)){  echo $newstitle; } ?>" size="100">
				</p>
				<p>
					<label for="newseditor" >Текст новости:<br />
					<textarea  id="newseditor" name="newseditor" rows="20" cols="110" class="maintextarea"><?php if (isset($newstext)){ echo $newstext;} ?></textarea>		
					<script type="text/javascript">
						var nicedit = new nicEditor({fullPanel : true,  iconsPath : '/Nicedit/nicEditorIcons.gif'}).panelInstance('newseditor');
					</script>	
				</p>
				
				<input type="hidden" name="newsid" value="<?php echo $newsid;  ?>">
				<input type="hidden" name="snumber" value="<?php echo md5(md5(session_id()));  ?>">
				<input type="hidden" name="skey" value="<?php echo $skey; ?>">
				
				<p id="panelniceditcontrol"><a onClick="addNicedit();">Визуальный редактор</a><a onClick="removeNicedit();">Исходник</a></p>
				<br>
				<p>
				<input type="submit" name="posteditnews" class="mainbutton" value="Сохранить" />
				<a href="/controlpanel/news" class="mainbutton">Отмена</a>
				</p>
		
			</form>
			
			<br><br>		
			
			<div id="attachfiles">
			<?php 							
				require_once("./Views/Blocks/cpnewsshowattaches.php"); 
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
				<input type="hidden" name="newsid" id="newsid"  value="<?php echo $newsid;  ?>" size="10" />
				<input type="hidden" name="snumber" value="<?php echo md5(md5(session_id()));  ?>">

				<input type="button" name="postattachfile" id="postattachfile"  class="mainbutton"  value="Загрузить файл" onclick="post_form()" />
				</fieldset>
			</form>
			
		</div>
		
		
		
	</div>
</div>	