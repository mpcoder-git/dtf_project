<div class="content">
	<div  class="mainblock innerblock">
		<h1 class="pageTitle">МАТЕРИАЛЫ ОБУЧЕНИЯ</h1>
		<?php		
			
			for ($i = 0; $i <= 11; $i++) {
				if ($lessons>=$i) { ?>
				
					<div>
					<a href="/lessons/lesson/<?php echo $i+1; ?>" class="main_buttonGreen"><b>ЗАНЯТИЕ <?php echo $i+1; ?></b></a>
					</div>

				<?php } 

			}
		?>
	</div>
</div>