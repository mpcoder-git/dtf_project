<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">

	<div  class="mainblock innerblock">
		
			<h1 class="pageTitle">ОБУЧЕНИЕ</h1>
		
			<p class="training_centeredText">ОБУЧАЕМ ТРЕЙДИНГУ НА АМЕРИКАНСКОЙ ФОНДОВОЙ БИРЖЕ С "НУЛЯ"</p>
			<br>
			<p class="training_centeredText"><b>ПЛАН ОБУЧЕНИЯ:</b></p>
			<br>
			<p class="training_centeredText">КУРС № 1</p>
			<br>
			
			
			<ul class="training_list centeredblock">
				<li>ЗАНЯТИЕ 1. Биржа - история возникновения и ее роль в экономике.</li>
				<li>ЗАНЯТИЕ 2. История создания и функционирование NYSE</li>
				<li>ЗАНЯТИЕ 3. Конкурирующие биржи.</li>
				<li>ЗАНЯТИЕ 4. Участники торгов.</li>
				<li>ЗАНЯТИЕ 5. ИНДЕКСЫ.</li>
				<li>ЗАНЯТИЕ 6. ТИПЫ ОРДЕРОВ.</li>
				<li>ЗАНЯТИЕ 7. ГРАФИКИ (CHARTS).</li>
				<li>ЗАНЯТИЕ 8. ТЕХНИЧЕСКИЙ АНАЛИЗ.</li>
				<li>ЗАНЯТИЕ 9. ФУНДАМЕНТАЛЬНЫЙ АНАЛИЗ.</li>
				<li>ЗАНЯТИЕ 10. Торговый терминал АВРОРА.</li>
			</ul>

						
			<br><br><br>
						
			<p><a href="<?php if (!isset($_SESSION['session_userid'])) { echo "/registration"; } else { echo "/lessons"; }  ?>" class="main_buttonGreen">ПРИСТУПИТЬ К ОБУЧЕНИЮ</a></p>			
		
	</div>


</div>