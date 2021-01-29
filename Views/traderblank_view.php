<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="content">
	<div  class="mainblock innerblock">
		<h1 class="pageTitle">Анкета трейдера</h1>
		
		<p>ФИО: <?php echo $userfamily." ".$username." ".$userotch; ?></p>
		<p>E-mail: <?php echo $useremail; ?></p><br>


		<form name="trader_blankForm" id="trader_blankForm" action="/job/posttraderblankform" class="mainform trader_blankForm" method="post">
			
			<table class="trader_blankTable">
				<tr>
				<td>Фамилия</td>
				<td><input type="text" name="family" id="family" class="maininput" value="" size="50" maxlength="20" required  /></td>
				</tr>
				<tr>
				<td>Имя</td>
				<td><input type="text" name="username" id="username" class="maininput" value="" size="50" maxlength="20" required  /></td>
				</tr>
				<tr>
				<td>Отчество</td>
				<td><input type="text" name="otch" id="otch" class="maininput" value="" size="50" maxlength="20" required  /></td>
				</tr>
				
				<tr>
				<td width="450px">1. Год вашего рождения:</td>
				<td><input type="text" name="field1" id="field1" class="maininput" value="" size="4" maxlength="4" required  /></td>
				</tr>
				<tr>
				<td>2. Телефон для связи:</td>
				<td><input type="text" name="field2" id="field2" class="maininput" value="" size="50" maxlength="50" required  /></td>
				</tr>
				<tr>
				<td>3. Skype:</td>
				<td><input type="text" name="field3" id="field3" class="maininput" value="" size="50" maxlength="50" required  /></td>
				</tr>
				<tr>
				<td>4. Образование: </td>
				<td><input type="text" name="field4" id="field4" class="maininput" value="" size="50" maxlength="50" required  /></td>
				</tr>
				<tr>
				<td>5. Город: </td>
				<td><input type="text" name="field5" id="field5" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>6. Семейное положение: </td>
				<td><input type="text" name="field6" id="field6" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>7. Место работы / должность: </td>
				<td><input type="text" name="field7" id="field7" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>8. Опыт работы на финансовых рынках: </td>
				<td><input type="text" name="field8" id="field8" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>9. Какие площадки и инструменты?</td>
				<td><input type="text" name="field9" id="field9" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>10. В какой компании? </td>
				<td><input type="text" name="field10" id="field10" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>11. Максимальная сумма депозита: </td>
				<td><input type="text" name="field11" id="field11" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>12. Применяемые тактики торговли: </td>
				<td><input type="text" name="field12" id="field12" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>13. Полученные результаты за все время работы: </td>
				<td><input type="text" name="field13" id="field13" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>14. Сколько часов Вы смогли бы уделять в день? </td>
				<td><input type="text" name="field14" id="field14" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>15. Когда бы Вы смогли начать обучение? </td>
				<td><input type="text" name="field15" id="field15" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>16. В какой период времени (Мск.)? </td>
				<td><input type="text" name="field16" id="field16" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>17. Какое обучение хотели бы пройти платное/бесплатное? </td>
				<td><input type="text" name="field17" id="field17" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>18. Уровень пользования ПК: </td>
				<td><input type="text" name="field18" id="field18" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>19. Какими программами владеете? </td>
				<td><input type="text" name="field19" id="field19" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>20. Ваши положительные качества: </td>
				<td><input type="text" name="field20" id="field20" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>21. Ваши отрицательные качества: </td>
				<td><input type="text" name="field21" id="field21" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>22. Ваше хобби: </td>
				<td><input type="text" name="field22" id="field22" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>
				<tr>
				<td>23. Откуда Вы о нас узнали? </td>
				<td><input type="text" name="field23" id="field23" class="maininput" value="" size="50" maxlength="50" required  /></td>
				
				</tr>

				<tr>
				<td valign="middle">24. От себя </td>
				<td><textarea  id="field24" name="field24" rows="3" cols="50" class="maintextarea"  ></textarea></td>				
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


			<input type="submit" name="savetraderblank" id="savetraderblank" class="mainbutton" value="Сохранить" />
			<br><br>
			
			
			</form>






	</div>
</div>