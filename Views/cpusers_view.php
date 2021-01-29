<?php
//защита от просмотра файла вручную через адресную строку
require_once('./Includes/verify_varreadfile.php'); 	
?>
<div class="content">

	<div  class="mainblock innerblock">
		
		<div id="findusers" >
			<br><br>
			<p>Поиск пользователей</p>
			<form name="findusersform" id="findusersform" action="/controlpanel/users" method="POST">
			<p><input type="text" name="searchtext" id="searchtext" class="maininput" value="<?php if (isset($searchtext) && !empty($searchtext)){ echo $searchtext; } ?>" size="80" /></p>
			<p>
			<input type="radio" name="searchselect" class="maininput" value="1" <?php if (!isset($searchselect) || $searchselect == 1){  echo 'checked'; } ?> /> По фамилии
			<input type="radio" name="searchselect" class="maininput" value="2" <?php if (isset($searchselect) && $searchselect == 2){  echo 'checked'; } ?>  /> По емайлу
			</p>
			
			<p><input type="submit" name="sendfindusers" class="mainbutton" value="Искать!" /></p>
			</form>	
		</div>
	
		<div id="userslist">
			<br><br>
			<p>Список пользователей</p>
			<table width="100%">
			<tr>
				<th width="12%">Фамилия</th>
				<th width="12%">Имя</th>
				<th width="12%">Отчество</th>
				<th width="12%">Ник</th>
				<th width="50px">Емайл</th>
				<th width="50px">Активирован</th>
				<th></th>
			</tr>
			<?php		
				if (isset($searchusers_result)){
				while( $row = mysqli_fetch_assoc($searchusers_result) ){			
					include("./Views/Blocks/cpuserblock.php");
				}
				}				
			?>
			</table>
		
		
		</div>
	</div>
</div>