<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>

<div class="forum_topicsNamecol forum_sectionSubrow">
	<a href="/forum/viewtopic/<?php echo $row['id']; ?>" class="mainlink"><?php echo $row['topicname']; ?></a>
	<br>
	<span class="forum_secondaryText">Сообщений: <?php echo $row['cntmessages']; ?>, Дата создания:<?php echo $this->rus_date("d F Y",strtotime($row['createdata'])); ?></span>  
</div>



