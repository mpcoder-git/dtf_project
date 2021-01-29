<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php
if ($_SERVER['REQUEST_URI'] == '/cabinet'){ 
?>
<p class="cabinetmenuitem-select">Статистическая информация</p>
<?php
} else { 
?>
<p class="cabinetmenuitem"><a href="/cabinet" class="mainlink">Статистическая информация</a></p>
<?php
}
?>

<?php
if ($_SERVER['REQUEST_URI'] == '/changerd'){ 
?>
<p class="cabinetmenuitem-select">Изменить регистрационные данные</p>	
<?php
} else { 
?>
<p class="cabinetmenuitem"><a href="/changerd" class="mainlink">Изменить регистрационные данные</a></p>	
<?php
}
?>

<?php
if ($_SERVER['REQUEST_URI'] == '/changepass'){ 
?>
<p class="cabinetmenuitem-select">Сменить пароль</p>	
<?php
} else { 
?>
<p class="cabinetmenuitem"><a href="/changepass" class="mainlink">Сменить пароль</a></p>	
<?php
}
?>

<?php
if ($_SERVER['REQUEST_URI'] == '/changeavatar'){ 
?>
<p class="cabinetmenuitem-select">Загрузить/Сменить аватар</p>
<?php
} else { 
?>
<p class="cabinetmenuitem"><a href="/changeavatar" class="mainlink">Загрузить/Сменить аватар</a></p>
<?php
}
?>

