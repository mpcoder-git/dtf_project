<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<div class="news_row">
<h2><b><?php echo $row['newstitle']; ?></b></h2>
<br>
<p><?php 
//date_default_timezone_set("UTC");
print $this->rus_date("d F Y",strtotime($row['newsdata']));

?></p><br>
<p><?php echo $row['newstext']; ?></p>
</div>