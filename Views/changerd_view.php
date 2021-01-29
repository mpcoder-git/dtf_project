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
					
				<h1>Изменение регистрационных данных</h1>
				<br>
				<form name="changerdform" id="changerdform" class="mainform" action="/changerd/postchangerdform" accept-charset="UTF-8" method="POST">	
				
					<p>
					<label for="usernick">Ник<br />
					<input type="text" name="usernick" id="usernick" class="maininput" value="<?php echo $nick; ?>" size="20" maxlength="20" /></label>
					</p>
					<br>
					<p>
					<label for="username">Имя<br />
					<input type="text" name="username" id="username" class="maininput" value="<?php echo $name; ?>" size="20" maxlength="20" required /></label>
					</p>
					<br>
					<p>
					<label for="userfamily">Фамилия<br />
					<input type="text" name="userfamily" id="userfamily" class="maininput" size="30" maxlength="30" value="<?php echo $family; ?>" required  /></label>
					</p>
					<br>
					<p>
					<label for="userotch">Отчество<br />
					<input type="text" name="userotch" id="userotch" class="maininput" size="30" maxlength="30" value="<?php echo $otch; ?>" required /></label>
					</p>
					<br>
					<p>
					<label for="useremail">Email<br />
					<input type="email" name="useremail" id="useremail" class="maininput" value="<?php echo $email; ?>" size="30" maxlength="30" required /></label>
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
					<input type="submit" name="rdsubmit" class="mainbutton" value="Изменить данные!" />
					</p>

				</form>
			



			</div>



		</div>
		
	</div>
</div>