<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="contactIndex mainblock innerblock">
			
	<h1 class="pageTitle">КОНТАКТЫ</h1>
	
	<p style="text-align: center;">ВЫ МОЖЕТЕ ОТПРАВИТЬ ВАШ ВОПРОС ЧЕРЕЗ ФОРМУ ОБРАТНОЙ СВЯЗИ:</p>
	<br><br>
	<div class="feedbackform centeredblock">
		<form action="/contacts/feedback" class="mainform" method="post">
			<p>
			<label for="postusername">Введите ваше имя: <span class="redstick">*</span><br />
			<input type="text" name="postusername" id="postusername" class="maininput adaptiveInput" value="" size="30" maxlength="20" required /></label>
			</p>
			
			<p>
			<label for="postuseremail">Введите email: <span class="redstick">*</span><br />
			<input type="text" name="postuseremail" id="postuseremail" class="maininput adaptiveInput" value="" size="30" maxlength="30" required /></label>
			</p>
			
			<p>
			<label for="textmessage">Текст сообщения: <span class="redstick">*</span><br />
			</label>
			<textarea name="textmessage" rows="10" cols="60" class="maintextarea" required></textarea>
			</p>
			
			<p class="invisible">
			<input type="text" name="name" id="name"  value="" size="30" maxlength="30" />
			<input type="text" name="email" id="email"  value="" size="30" maxlength="30" />
			<input type="text" name="captcha" size="10" maxlength="10">
			</p>
			<input type="hidden" name="snumber" id="snumber"  value="<?php echo md5(md5(session_id()));  ?>" size="25" />
			<input type="hidden" name="skey" value="<?php echo $skey; ?>">
			<p style="margin: 30px 0; text-align: center;"><input type="checkbox" name="agree" id="agree" style="margin-right: 10px;" checked />Согласен на обработку персональных данных</p>
			<p>
			<input type="submit" name="sendfeedbackform" class="mainbutton" value="Отправить" />
			</p>
		</form>
	</div>
	<br>
	<p style="text-align: center;">ВОПРОСЫ И ПРЕДЛОЖЕНИЯ ОТПРАВЛЯЙТЕ НА АДРЕС: <span>info</span><span class="invisible">mix</span>@<span >daytradersfactory</span><span class="invisible">dog</span><span>.com</span></p>
	<br>
		
</div>