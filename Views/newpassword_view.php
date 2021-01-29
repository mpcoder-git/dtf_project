<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">
	<div class="mainblock innerblock">
		
		<div class="centeredblock">
		
		<h1 class="pageTitle">Восстановление пароля</h1>

		<form name="newpasswordform" id="newpasswordform" class="mainform" action="/recoverypassword/changenewpassword" method="POST">
			<p>
			<label for="usernewpass">Введите новый пароль<br />
			<input type="text" name="usernewpass" id="usernewpass" class="maininput" value="" size="32" minlength="8"  maxlength="32" required />
			</label>
			</p>
			<br>
			<p>
			<label for="userrenewpass">Введите повторно новый пароль<br />
			<input type="text" name="userrenewpass" id="userrenewpass" class="maininput" value="" size="32" minlength="8"  maxlength="32" required />
			</label>
			</p>
			<br>
			<p class="invisible">
			<input type="text" name="name" id="name"  value="" size="30" maxlength="30" />
			<input type="text" name="email" id="email"  value="" size="30" maxlength="30" />
			<input type="text" name="captcha" size="10" maxlength="10">
			</p>
			<input type="hidden" name="snumber" value="<?php echo md5(md5(session_id()));  ?>">
			<input type="hidden" name="skey" value="<?php echo $skey; ?>">
			<input type="hidden" name="recoveryid" value="<?php echo $userid;  ?>">
			<input type="submit" name="newpasswordsubmit" class="mainbutton" value="Сменить пароль!" />
			</form>
		
		</div>
	
	</div>
</div>