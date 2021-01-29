<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">
	<div class="mainblock innerblock">
		<div class="registrationform centeredblock">
			
			<h1 class="pageTitle">Регистрация</h1>
			
			<form name="registrationform" id="registrationform" class="mainform" action="/registration/postregform" accept-charset="UTF-8" method="post">
	
				<p>
				<label for="usernick">Ник: <span class="redstick">*</span><br />
				<input type="text" name="usernick" id="usernick" class="maininput adaptiveInput" value="" size="30" maxlength="20" required /></label>
				</p>
	
				<p>
				<label for="username">Имя: <span class="redstick">*</span><br />
				<input type="text" name="username" id="username" class="maininput adaptiveInput" value="" size="30" maxlength="20" required /></label>
				</p>
	
				<p>
				<label for="userfamily">Фамилия: <span class="redstick">*</span><br />
				<input type="text" name="userfamily" id="userfamily" class="maininput adaptiveInput" size="30" maxlength="20" value="" required  /></label>
				</p>
		
				<p>
				<label for="userotch">Отчество: <span class="redstick">*</span><br />
				<input type="text" name="userotch" id="userotch" class="maininput adaptiveInput" size="30" maxlength="20" value="" required /></label>
				</p>
				
				<p>
				<label for="useremail">Email: <span class="redstick">*</span><br />
				<input type="email" name="useremail" id="useremail" class="maininput adaptiveInput" value="" size="30" maxlength="30" required /></label>
				</p>
		
				<p>
				<label for="password">Пароль <span class="mainhelperText ">(минимум 8 символов)</span>: <span class="redstick">*</span><br />
				<input type="password" name="password" id="password" class="maininput adaptiveInput" value="" size="32" minlength="8" maxlength="32" required /></label>
				</p>	
				<p>
				<label for="repassword">Повтор пароля <span class="mainhelperText ">(минимум 8 символов)</span>: <span class="redstick">*</span><br />
				<input type="password" name="repassword" id="repassword" class="maininput adaptiveInput" value="" size="32" minlength="8" maxlength="32" required /></label>
				</p>
				
				<p class="invisible">
				<input type="text" name="name" id="name"  value="" size="30" maxlength="30" />
				<input type="text" name="email" id="email"  value="" size="30" maxlength="30" />
				<input type="text" name="captcha" size="10" maxlength="10">
				</p>				
				<input type="hidden" name="snumber" value="<?php echo md5(md5(session_id()));  ?>">
				<input type="hidden" name="skey" value="<?php echo $skey; ?>">
				<input type="hidden" name="ukey" value="<?php echo $ukey; ?>">
				<p style="margin: 30px 0; text-align: center;"><input type="checkbox" name="agree" id="agree" style="margin-right: 10px;" />Согласен на обработку персональных данных</p>
				<p>
				<input type="submit" name="sendregistrationform" id="sendregistrationform" class="mainbutton" value="Регистрация" />
				</p>
				<br>
				<p class="mainhelperText">Есть аккаунт? <a href="/login" class="mainlink">Войдите сдесь</a>!</p>
				</form>
			
		</div>
	</div>

</div>