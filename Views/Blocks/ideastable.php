<?php

	if(file_exists('./Signalsfiles/Ideas.txt')){
		
			$file_ideas = fopen ("./Signalsfiles/Ideas.txt", "r");
			
			$txt = fread ($file_ideas, filesize("./Signalsfiles/Ideas.txt")) or die ("Ошибка чтения файла");
			fclose($file_ideas);
			
			
			//massive
			$ideas_exparr = explode(";", $txt);
			array_pop($ideas_exparr);		
			//теперь надо записать массивы второго уровня
			

			foreach ($ideas_exparr as $value) {
				$ideas_arr[] = explode(",", $value);

			}
			
			

?>			
			<table class="signals_table" id="s_table">
			<tr bgcolor="#D3D3D3">
				<th>Time</th><th>Idea</th><th>Rank</th><th>Stock</th><th>Side</th><th>Size</th><th>Price</th><th>TP</th><th>SL</th>
			</tr>
			<?php
				
				$i=0;
				foreach ($ideas_arr as &$value) {
				
				if($i>0){
				?>
				<tr onclick="document.title = 'Сигналы ['+document.getElementById('s_table').rows[this.rowIndex].cells[3].innerHTML+']';"   <?php if($i%2) print 'class="signals_table_row"';?>>
				<td><?php print $ideas_arr[$i][0]; ?></td><td><?php print $ideas_arr[$i][1]; ?></td><td  align="right"><?php print $ideas_arr[$i][2]; ?></td>
				
				<td onMouseover="ShowGraph(this)" onMouseout="document.getElementById('signals_graphics').style.display = 'none';"><?php print $ideas_arr[$i][3]; ?></td>
				<td 
				<?php 
					if ($ideas_arr[$i][4] == 'LONG') print 'bgcolor="#A6FF47"'; 
					if ($ideas_arr[$i][4] == 'SHORT') print 'bgcolor="#FF6B47"'; 
				?>><?php print $ideas_arr[$i][4]; ?></td>
				<td align="right"><?php print $ideas_arr[$i][5]; ?></td>
				
				<td align="right"><?php print $ideas_arr[$i][6]; ?></td><td align="right"><?php print $ideas_arr[$i][7]; ?></td><td align="right"><?php print $ideas_arr[$i][8]; ?></td>
				</tr>
				
				<?php
				}
				$i=$i+1;
				}		
			?>
			
			
			</table>
			
			
<?php			
			
						
			unset($ideas_exparr );
			unset($ideas_arr );
	}


?>