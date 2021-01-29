<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="forum_quickReplyintopicform centeredblock">
	
		<form name="replyform" id="replyform" action="/forum/postquickreply" method="POST">
			
				<label for="messagereply">Быстрый ответ<br />
				<textarea  id="messagereply" name="messagereply" rows="10" cols="100" class="maintextarea" ></textarea>
				<script type="text/javascript">
					nicedit = new nicEditor({fullPanel : true,  iconsPath : './Nicedit/nicEditorIcons.gif'}).panelInstance('messagereply');
				</script>
			
			<p class="invisible">
			<input type="text" name="name" id="name"  value="" size="30" maxlength="30" />
			<input type="text" name="email" id="email"  value="" size="30" maxlength="30" />
			<input type="text" name="captcha" size="10" maxlength="10">
			</p>
			
			<input type="hidden" name="topicid" value="<?php echo $tid;  ?>">
			<input type="hidden" name="snumber" value="<?php echo md5(md5(session_id()));  ?>">
			<input type="hidden" name="skey" value="<?php echo $skey; ?>">
			<br>
			<input type="submit" name="postquickreply" class="mainbutton" value="Ответить" />
	
		</form>

</div>