<?php
//защита от просмотра файла вручную через адресную строку
require_once('./Includes/verify_varreadfile.php'); 		
?>
<div class="content">
	<div  class="mainblock innerblock">
		<h1 class="pageTitle">Список новостей</h1>
		<a href="/controlpanel/newseditor" class="mainbutton">Добавить новость</a>
		<table width="100%">
			<tr>
				<th width="50%">Заголовок новости</th>
				<th width="150px">Дата создания</th>
				<th width="50px">Активность</th>
				<th></th>
			</tr>
		<?php		
		while( $row = mysqli_fetch_assoc($news_result) ){			
			include("./Views/Blocks/cpnewsblock.php");
		}					
		?>
		</table>
	</div>
</div>