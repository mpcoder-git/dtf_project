
<fieldset>
<legend>Вложения</legend>
<ul>
<?php
	$attch_path = PATCH_ATTACHES.$messageid."/";
	$dir_pointer = opendir ($attch_path);
	
	while (($res = readdir($dir_pointer))!==FALSE){
		if ($res != "." && $res != ".." && $res != "Index.php"){
			
			//строка получает тип файла
			//echo mime_content_type($attch_path.$res);
			if ($this->image_valid(mime_content_type($attch_path.$res) == FALSE)){
			$fsize = $this->filesize_get(filesize($attch_path.$res));
		?>		
			<li class="forum_message_attaches">
				<a href="<?php echo $attch_path.$res; ?>"><?php echo $res; ?></a> <?php  echo $fsize; ?>
				<a href="#" class="mainbutton">Удалить вложение</a>
			</li>	
		<?php		
			}
		}								
	}	
?>
</ul>
</fieldset>

