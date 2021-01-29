<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<header class="header">
	<div class="innerblock">
		
		<div class="header_userarea ">			
			<?php if (!isset($_SESSION['session_userid'])) { ?>
			<a class="header_link" href="/login">Вход</a> / <a class="header_link" href="/registration">Регистрация</a>
			<?php } else { ?>
			<?php 					
				//$query =mysqli_query($con,"SELECT nick FROM users WHERE Id=".$_SESSION['session_userid']);
				
					while($row=mysqli_fetch_row($header_result))
					{
						$usernick=$row[4];
					}
						mysqli_free_result($header_result);
									
				?> 
				<a class="header_link" href="/cabinet"><?php echo $usernick; ?></a> / <a class="header_link" href="/logout">Выход</a>
			<?php }	?>			
			</div>
		
				
		<div class="header_logoblock">
			<div class="header_logoimage">
			<a href="/"><img src="<?php echo PATCH_IMAGES.'logo.png'; ?>" alt="" class="header_logoimage"> </a>
			</div>
			<div class="header_dtftext">
			<b>Day Traders Factory</b>
			</div>
		</div>

		<nav role="navigation" class="header_navmenuGrid">
		
			<div class="header_navmenuElement"><a class="header_link" href="/">О НАС</a></div>	
			<div class="header_navmenuElement"><a class="header_link" href="/training">ОБУЧЕНИЕ</a></div>
			<div class="header_navmenuElement"><a class="header_link" href="/signals">СИГНАЛЫ</a></div>
			<div class="header_navmenuElement"><a class="header_link" href="/partners">ПАРТНЁРСТВО</a></div>
			<div class="header_navmenuElement"><a class="header_link" href="/propoffice">ПРОП ОФИС</a></div>
			<div class="header_navmenuElement"><a class="header_link" href="/forum">ФОРУМ</a></div>
			<div class="header_navmenuElement"><a class="header_link" href="/contacts">КОНТАКТЫ</a></div>				

		</nav>
	</div>	
</header>