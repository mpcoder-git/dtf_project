<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">
	<div class="mainblock innerblock">
		
		<div class=" centeredblock">
		
		<h1 class="pageTitle">Восстановление пароля</h1>

		<form name="recoverypasswordform" id="recoverypasswordform" class="mainform"  action="/recoverypassword/sendrecoveryemail" method="POST">
			<p>
			<label for="useremail">Введите email<br />
			<input type="text" name="useremail" id="useremail" class="maininput" value="" size="32" maxlength="30"  required />
			</p>
			<p class="invisible">
			<input type="text" name="name" id="name"  value="" size="30" maxlength="30" />
			<input type="text" name="email" id="email"  value="" size="30" maxlength="30" />
			<input type="text" name="captcha" size="10" maxlength="10">
			</p>
			<input type="hidden" name="snumber" value="<?php echo md5(md5(session_id()));  ?>">
			<input type="hidden" name="skey" value="<?php echo $skey; ?>">
			<p>
			<input type="submit" name="sendrecoverybutton" class="mainbutton" value="Выслать письмо с данными для восстановления!" />
			</label>
			</p>
			
		</form>
		
		</div>
	
	</div>
</div>