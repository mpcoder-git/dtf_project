<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">
	<div  class="mainblock innerblock">
		<h1 class="pageTitle">Анкета новичка</h1>
		
		<p>E-mail: <?php echo $useremail; ?></p><br>
			
			
			<form name="beginner_blankForm" id="beginner_blankForm" action="/job/postbeginnerblankform" class="mainform beginner_blankForm" method="post">
			
			<table class="beginner_blankTable">
				<tr>
				<td width="40%">Фамилия</td>
				<td><input type="text" name="family" id="family" class="maininput" value="" size="50" maxlength="20" required /></td>
				</tr>
				<tr>
				<td>Имя</td>
				<td><input type="text" name="username" id="username" class="maininput" value="" size="50" maxlength="20" required /></td>
				</tr>
				<tr>
				<td>Отчество</td>
				<td><input type="text" name="otch" id="otch" class="maininput" value="" size="50" maxlength="20" required /></td>
				</tr>
				
				<tr>
				<td width="450px">1. Год вашего рождения?</td>
				<td><input type="text" name="field1" id="field1" class="maininput" value="" size="4" maxlength="4" required /></td>
				</tr>
				<tr>
				<td>2. Какое у Вас образование?</td>
				<td><input type="text" name="field2" id="field2" class="maininput" value="" size="50" maxlength="50" required /></td>
				</tr>
				<tr>
				<td>3. Какой у Вас трудовой стаж?</td>
				<td><input type="text" name="field3" id="field3" class="maininput" value="" size="50" maxlength="50" required /></td>
				</tr>
				<tr>
				<td>4. Вы готовы работать с 16-00 до 00-00 мск 5 дней в неделю?</td>
				<td><input type="text" name="field4" id="field4" class="maininput" value="" size="50" maxlength="50" required /></td>
				</tr>
				<tr>
				<td>5. На какую зарплату Вы претендуете за эту работу?</td>
				<td><input type="text" name="field5" id="field5" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>6. Где Вам комфортно работать - в офисе, в большом зале за своим рабочим местом на расстоянии вытянутой руки от коллеги или дома?</td>
				<td><input type="text" name="field6" id="field6" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>7. Сколько часов в день Вы готовы сейчас потратить на то, чтобы получить нужные знания и навыки для получения работы?</td>
				<td><input type="text" name="field7" id="field7" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>8. В какое время суток Вам удобно быть он-лайн?</td>
				<td><input type="text" name="field8" id="field8" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>9. Сколько дней в неделю Вы готовы заниматься обучением?</td>
				<td><input type="text" name="field9" id="field9" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>10. Сколько месяцев Вы готовы учиться каждый день?</td>
				<td><input type="text" name="field10" id="field10" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>11. Есть ли у Вас рабочее место дома (или в офисе), где Вам не будут мешать обучаться все это время?</td>
				<td><input type="text" name="field11" id="field11" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>12. Вы сторонник самостоятельного изучения материалов или занятий с преподавателем?</td>
				<td><input type="text" name="field12" id="field12" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>13. Какой у Вас компьютер (процессор, ОЗУ) и монитор (диагональ, в дюймах)?</td>
				<td><input type="text" name="field13" id="field13" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>14. Какой у Вас Интернет - тип и скорость?</td>
				<td><input type="text" name="field14" id="field14" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>15. Ваш уровень знаний программы Microsoft Word?   1-10</td>
				<td><input type="text" name="field15" id="field15" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>16. Ваш уровень знаний программы Microsoft Excel?   1-10</td>
				<td><input type="text" name="field16" id="field16" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>17. Каким браузером в Интернете Вы пользуетесь?</td>
				<td><input type="text" name="field17" id="field17" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>18. Какие мессенджеры используете?</td>
				<td><input type="text" name="field18" id="field18" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>19. Уровень знания английского? 1-10</td>
				<td><input type="text" name="field19" id="field19" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>20. Вы знакомы с финансовыми рынками?</td>
				<td><input type="text" name="field20" id="field20" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>21. Вы знаете о трейдинге?</td>
				<td><input type="text" name="field21" id="field21" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>22. Знаете что такое Спред?</td>
				<td><input type="text" name="field22" id="field22" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>23. Знаете что такое Ликвидность?</td>
				<td><input type="text" name="field23" id="field23" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td>24. Знаете что такое Bid и Ask?</td>
				<td><input type="text" name="field24" id="field24" class="maininput" value="" size="50" maxlength="50" required /></td>
				
				</tr>
				<tr>
				<td valign="middle">25. От себя</td>
				<td><textarea  id="field25" name="field25" rows="3" cols="50" class="maintextarea" ></textarea></td>
				
				</tr>
				</table>
				
				<p class="invisible">
				<input type="text" name="name" id="name"  value="" size="30" maxlength="30" />
				<input type="text" name="email" id="email"  value="" size="30" maxlength="30" />
				<input type="text" name="captcha" size="10" maxlength="10">
				</p>
				
				<input type="hidden" name="snumber" value="<?php echo md5(md5(session_id()));  ?>">
				<input type="hidden" name="skey" value="<?php echo $skey; ?>">
				
				<p style="margin: 30px 0; text-align: center;"><input type="checkbox" name="agree" id="agree" style="margin-right: 10px;" />Согласен на обработку персональных данных</p>			
			
				<input type="submit" name="savebeginnerblank" id="savebeginnerblank" class="mainbutton" value="Сохранить" />
				<br><br>
			
			
			</form>

		
		
		
	</div>
</div>