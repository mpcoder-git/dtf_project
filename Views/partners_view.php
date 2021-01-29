<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content" id="partners_content">
	<div  class="mainblock innerblock uppertext">
		<h1 class="pageTitle">Партнёрам</h1>
		
		<p class="partners_centeredText">
		Стань партнёром компании <b>day traders factory</b> - открой торговый офис в своём городе
		</p>
		<br>
		<p>Компания DAY TRADERS FACTORY предлагает уникальные условия и бизнес возможности для партнеров</p>
		<br>						
		<p><b>Для компаний и частных лиц DAY TRADERS FACTORY предоставляет:</b></p>
		
		<ul class="mainlist">
			<li>профессиональную торговую платформу</li>
			<li>наше профессиональное обучение - мы полностью обучаем и готовим ваших трейдеров к зарабатыванию на рынке</li>
			<li>торговый капитал - мы финансируем ваших трейдеров</li>
			<li>наша техническая и трейдинг поддержка</li>			
		</ul>
		
		<br>
		
		<div id="partners_form">
				
				<form action="/partners/postpartnersform" class="mainform" method="post" >
				
						<h2 class="centeredtext">Открой свой проп-офис</h2>					
					
						<p><label for="postusername">Ваше имя:</label><input type="text" name="postusername" class="maininput" size="30" maxlength="20"  required placeholder="Ваше имя"></p>	
						<p><label for="postuserphone">Номер телефона:</label><input type="text" name="postuserphone" class="maininput" size="30" maxlength="20"  required placeholder="Номер телефона"></p>
						<p><label for="postuseremail">Ваш E-mail:</label><input type="text" name="postuseremail" class="maininput" size="30" maxlength="30"  required placeholder="Ваш E-mail"></p>
						<p><label for="postusercity">Город проживания:</label><input type="text" name="postusercity" class="maininput" size="30" maxlength="30"  required placeholder="Город проживания"></p>
						
						<p class="invisible">
						<input type="text" name="name" id="name"  value="" size="30" maxlength="30" />
						<input type="text" name="email" id="email"  value="" size="30" maxlength="30" />
						<input type="text" name="captcha" size="10" maxlength="10">
						</p>
						<input type="hidden" name="snumber" id="snumber"  value="<?php echo md5(md5(session_id()));  ?>" size="25" />
						<input type="hidden" name="skey" value="<?php echo $skey; ?>">
						
						<p style="margin: 30px 0;"><input type="checkbox" name="agree" id="agree" style="margin-right: 10px;" checked />Согласен на обработку персональных данных</p>
						<p><button type="submit" name="sendpartnersform" class="mainbutton">Отправить</button></p>
						
				</form>
				
		</div>
		
		<br>
		
		<p><b>Наши партнеры получают уникальные преимущества</b></p>
		
		<ul class="mainlist">
			<li>оборотный капитал для работы трейдеров - на реальный счет трейдера мы даём $50,000.00</li>
			<li>высокие выплаты, без скрытых комиссий</li>
			<li>комплексное обучение - научим управлять проп-офисом, нанимать и обучать трейдеров</li>
			<li>бесплатное демо - неограниченное количество бесплатных демо счетов для тренировок и обучения</li>
			<li>своя платформа и прямой выход на рынки - торговля на самых интересных рынках NYSE, NASDAQ, OTC, CME и другие</li>
		</ul>
		<br>
		
		<p><b>Управляйте своей командой трейдеров</b></p>
		<br>
		
		<p><b>Что требуется от партнера:</b></p>
		
		<ul class="mainlist">
			<li>Организаторские и управленческие возможности;</li>
			<li>Стартовый капитал от 5000$;</li>
			<li>Организовать офис для трейдеров минимум на 10 мест;</li>
			<li>Желание и время развивать свой проп-трейдинговый офис</li>			
		</ul>
		<br>
		
		<p><b>С чего начать?</b></p>
		
		<p>Три простых шага для открытия своего офиса</p>
		<p><b>1. Пройдите регистрацию</b></p>
		<p>Для начала вы должны заполнить регистрационную форму, и предоставить информацию о себе, а также о том, где будет расположен ваш собственный трейдинговый офис</p>
		<p><b>2. Заключите партнерский договор</b></p>
		<p>Мы свяжемся с Вами, обсудим детали и предоставим договор</p>
		<p><b>3. Переходите к оплате</b></p>
		<p>После подписания договора - переходите к оплате согласно договора</p>
		<p><b>4. Приступайте к работе</b></p>
		<p>Работа начинается с открытия Вами офиса и базового обучения для менеджеров</p>
		
		<br>
		
		<p><b>Остались вопросы? Напишите <a href="/contacts" class="mainlink">сюда</a> </b></p>
		<br>
		
	</div>
</div>