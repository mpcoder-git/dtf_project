<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">
	<div  class="mainblock innerblock">

		<h1 class="pageTitle">ВОПРОС № <?php echo $_SESSION['vopros_number']; ?></h1>
		
		<form name="testform" id="testform"  action="/lessons/test" method="post">
				
			<p><b><?php echo $question; ?></b></p>
			
			<?php
				//получение ответов			
				while ($row = mysqli_fetch_assoc($answers_result)) {
			 	?>
				<p><input type="radio" name="answer" value="<?php echo $row["numberanswer"]; ?>"> <?php echo $row["answer"]; ?></p>
				<?php

				}				
			?>			

			<br>
			<p><input type="submit"  class="mainbutton" value="Ответить" /></p><br><br><br>
		</form>
		

	</div>
</div>