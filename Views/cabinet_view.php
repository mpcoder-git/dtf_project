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
					
			<p>Добро пожаловать, <span><?php echo $name." ".$otch." ".$family; ?>! </span></p>
			<br><br>
			<?php if ($lessons > 0){ ?>
			<p>Завершенных занятий: <?php echo $lessons; ?> </p>
			<p>Дата прохождения тестирования: <?php echo $this->rus_date("d F Y",strtotime($lesdata)); ?></p>
			<?php } ?>
			<?php if ($beginprofile > 0){ ?>
			<br><br>
			<p>Дата заполнения анкеты новичка: <?php echo $this->rus_date("d F Y",strtotime($bpenterdata)); ?></p>
			<?php } ?>
			<?php if ($traderprofile > 0){ ?>
			<br><br>
			<p>Дата заполнения анкеты трейдера: <?php echo $this->rus_date("d F Y",strtotime($tpenterdata)); ?></p>
			<?php } ?>
			<br><br>
			
			<p><a href="/logout" class="mainlink">Выйти</a> </p>



			</div>



		</div>
		
	</div>
</div>