<?php
//защита от просмотра файла вручную через адресную строку
if (!defined( 'READFILE' )){ exit ( "Error" ); }	
?>
<?php
	$attch_path = '.'.PATCH_ATTACHES.$mid."/";
	if (file_exists($attch_path)) {
?>
<fieldset>
<legend>Вложения</legend>
<table>
<?php
				
		$dir_pointer = opendir ($attch_path);
		
		while (($file = readdir($dir_pointer))!==FALSE){
			if ($file != "." && $file != ".." && $file != "Index.php"){
				
				//строка получает тип файла
				//echo mime_content_type($attch_path.$res);
				if ($this->image_valid(mime_content_type($attch_path.$file) == FALSE)){
				$fsize = $this->filesize_get(filesize($attch_path.$file));
			?>		
				<tr>
					
					<td class="forum_message_attaches"><a href="<?php echo $attch_path.$file; ?>"><?php echo $file; ?></a> <?php  echo $fsize; ?></td>
					<td class="forum_message_attaches">
						<input type="button" name="postattachfile" id="postattachfile"  class="mainbutton"  value="Удалить вложение" onclick="reload_attaches('/forum/deleteattachfile<?php echo '?mid='.$mid.'&filename='.$file;  ?>')" />		
					</td>				
					
				</tr>	
			<?php		
				}
			}								
		}
		
?>
</table>
</fieldset>
<?php
	}
?>