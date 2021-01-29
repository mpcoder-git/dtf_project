<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">
	
	<div id="controlsigninblock" class=" centeredblock  ">
			<h1>Вход</h1>
			<p>
			<?php
  			echo $_SERVER["REMOTE_ADDR"];
			?>
			</p>
			<form name="admloginform" id="admloginform" action="<?php echo '/controlpanel/postloginform'; ?>" method="POST">
			<p>
			<label for="admlogin" class="formlabel">Логин<br />
			<input type="text" name="admlogin" id="admlogin" class="maininput" value="" size="32" maxlength="30"  required /></label>
			</p>
			<p>
			<label for="password" class="formlabel">Пароль<br />
			<input type="password" name="password" id="password" class="maininput" value="" size="32" minlength="8"  maxlength="32" required  /></label>
			</p>
			<p class="invisible">
			<input type="text" name="name" id="name"  value="" size="30" maxlength="30" />
			<input type="text" name="email" id="email"  value="" size="30" maxlength="30" />
			<input type="text" name="captcha" size="10" maxlength="10">
			</p>
			<input type="hidden" name="snumber" value="<?php echo md5(md5(session_id()));  ?>">
			<input type="hidden" name="skey" value="<?php echo $skey; ?>">	
			<p>
			<input type="submit" name="login" class="mainbutton" value="Вход" />
			</p>
			</form>
	</div>
	
	
	
</div>