<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">
	<div  class="mainblock innerblock">
		<h1 class="pageTitle">Редактирование темы</h1>
		<form name="edittopicnameform" id="edittopicnameform" action="/forum/postedittopicnameform" class="mainform"   method="POST">
			<p>
				<label for="topiceditor" class="formlabel">Введите новое название темы<br />
				
				<input type="text" name="topicname" id="topicname" class="maininput" value="<? echo $topicname; ?>" size="140" maxlength="140"  />				
			</p>
			
			<p class="invisible">
			<input type="text" name="name" id="name"  value="" size="30" maxlength="30" />
			<input type="text" name="email" id="email"  value="" size="30" maxlength="30" />
			<input type="text" name="captcha" size="10" maxlength="10">
			</p>

			<input type="hidden" name="topicid" value="<?php echo $tid;  ?>">
			<input type="hidden" name="snumber" value="<?php echo md5(md5(session_id()));  ?>">
			<input type="hidden" name="skey" value="<?php echo $skey; ?>">
			
			<p><input type="submit" name="postedittopicname" class="mainbutton" value="Сохранить" /></p>
	
		</form>

	</div>
</div>