<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">
	<div class="mainblock innerblock">
		
		<div class="cabinetGrid">

			<div  class="cabinet_menu ">
				<?php require_once("Blocks/cabinetmenu.php"); ?>
			</div>

			
			<div class="cabinet_content">
					
				<h1>Смена пароля</h1>
				<br>
				<form name="changepassform" id="changepassform" class="mainform" action="/changepass/postchangepassform" method="POST">	
				
					<p>
					<label for="newpass">Введите новый пароль<br />
					<input type="text" name="newpass" id="newpass" class="maininput" value="" size="32" minlength="8"  maxlength="32" required  /></label>
					</p>
					<br>
					<p>
					<label for="renewpass">Введите повторно новый пароль<br />
					<input type="text" name="renewpass" id="renewpass" class="maininput" value="" size="32" minlength="8"  maxlength="32" required /></label>
					</p>
					<br>
					<p>
					<label for="renewpass">Введите текущий пароль<br />
					<input type="text" name="actualpass" id="actualpass" class="maininput" value="" size="32" minlength="8"  maxlength="32" required /></label>
					</p>
					<br>
					<p class="invisible">
					<input type="text" name="name" id="name"  value="" size="30" maxlength="30" />
					<input type="text" name="email" id="email"  value="" size="30" maxlength="30" />
					<input type="text" name="captcha" size="10" maxlength="10">
					</p>
					<input type="hidden" name="snumber" value="<?php echo md5(md5(session_id()));  ?>">
					<input type="hidden" name="skey" value="<?php echo $skey; ?>">
					<p>
					<input type="submit" name="repasssubmit" class="mainbutton" value="Сменить пароль!" />
					</p>

				</form>
			
			</div>

		</div>
		
	</div>
</div>