<?php

	if(file_exists('./Signalsfiles/Trades.txt')){
		
			$file_ideas = fopen ("./Signalsfiles/Trades.txt", "r");
			
			$txt = fread ($file_ideas, filesize("./Signalsfiles/Trades.txt")) or die ("Ошибка чтения файла");
			fclose($file_ideas);
			
			
			//massive
			$trades_exparr = explode(";", $txt);
			array_pop($trades_exparr);		
			//теперь надо записать массивы второго уровня
			

			foreach ($trades_exparr as $value) {
				$trades_arr[] = explode(",", $value);

			}
			
			

?>			
			<table class="signals_table" id="t_table">
			<tr bgcolor="#D3D3D3">
				<th>Time</th><th>Idea</th><th>Rank</th><th>Stock</th><th>Side</th><th>Size</th><th>Price</th><th>TP</th><th>SL</th><th>PnL</th>
			</tr>
			<?php
				
				$i=0;
				foreach ($trades_arr as &$value) {
				
				if($i>0){
				?>
				<tr <?php if($i%2) print 'class="signals_table_row"';?>>
				<td><?php print $trades_arr[$i][0]; ?></td><td><?php print $trades_arr[$i][1]; ?></td><td  align="right"><?php print $trades_arr[$i][2]; ?></td>
				
				<td ><?php print $trades_arr[$i][3]; ?></td>
				<td 
				<?php 
					if ($trades_arr[$i][4] == 'LONG') print 'bgcolor="#A6FF47"'; 
					if ($trades_arr[$i][4] == 'SHORT') print 'bgcolor="#FF6B47"'; 
				?>><?php print $trades_arr[$i][4]; ?></td>
				<td align="right"><?php print $trades_arr[$i][5]; ?></td>
				
				<td align="right"><?php print $trades_arr[$i][6]; ?></td><td align="right"><?php print $trades_arr[$i][7]; ?></td><td align="right"><?php print $trades_arr[$i][8]; ?></td><td align="right"><?php print $trades_arr[$i][9]; ?></td>
				</tr>
				
				<?php
				}
				$i=$i+1;
				}		
			?>
			
			
			</table>
			
			
<?php			
			
						
			unset($trades_exparr );
			unset($trades_arr );
	}


?>