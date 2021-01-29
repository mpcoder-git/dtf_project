<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">
	<div class="mainblock innerblock">
		
		<div class="loginform centeredblock">
		
		<h1 class="pageTitle">Вход</h1>

		<form id="loginform" name="loginform"  class="mainform" action="/login/postloginform" method="POST">
		<p>
		<label for="useremail" >Введите email<br />
		<input type="text" name="useremail" id="useremail" class="maininput adaptiveInput" value="" size="32" maxlength="30"  required /></label>
		</p>
		<p>
		<label for="password" >Пароль<br />
		<input type="password" name="password" id="password" class="maininput adaptiveInput" value="" size="32" minlength="8"  maxlength="32" required /></label>
		</p>
		<p class="invisible">
		<input type="text" name="name" id="name"  value="" size="30" maxlength="30" />
		<input type="text" name="email" id="email"  value="" size="30" maxlength="30" />
		<input type="text" name="captcha" size="10" maxlength="10">
		</p>
		<input type="hidden" name="snumber" value="<?php echo md5(md5(session_id()));  ?>">
		<input type="hidden" name="skey" value="<?php echo $skey; ?>">
		<input type="hidden" name="ukey" value="<?php echo $ukey; ?>">
		<p>
		<input type="submit" name="login" class="mainbutton" value="Войти!" />
		</p>
		<br>
		<p class="mainhelperText">Нет аккаунта? <a href="/registration" class="mainlink">Зарегистрируйтесь здесь</a>!</p>
		<p class="mainhelperText">Забыли пароль? <a href="/recoverypassword" class="mainlink">Начните восстановление</a>!</p>
		</form>
		
		</div>
	
	</div>
</div>