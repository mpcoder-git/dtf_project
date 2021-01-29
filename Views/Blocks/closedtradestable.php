<?php

	if(file_exists('./Signalsfiles/ClosedTrades.txt')){
		
			$file_ideas = fopen ("./Signalsfiles/ClosedTrades.txt", "r");
			
			$txt = fread ($file_ideas, filesize("./Signalsfiles/ClosedTrades.txt")) or die ("Ошибка чтения файла");
			fclose($file_ideas);
			
			
			//massive
			$closedtrades_exparr = explode(";", $txt);
			array_pop($closedtrades_exparr);		
			//теперь надо записать массивы второго уровня
			

			foreach ($closedtrades_exparr as $value) {
				$closedtrades_arr[] = explode(",", $value);

			}
			
			

?>			
			<table class="signals_table" id="ct_table">
			<tr bgcolor="#D3D3D3">
				<th>Time</th><th>Idea</th><th>Rank</th><th>Stock</th><th>Side</th><th>Size</th><th>Entry</th><th>Exit</th><th>Pips</th><th>Cash</th>
			</tr>
			<?php
				
				$i=0;
				foreach ($closedtrades_arr as &$value) {
				
				if($i>0){
				?>
				<tr <?php if($i%2) print 'class="signals_table_row"';?>>
				<td><?php print $closedtrades_arr[$i][0]; ?></td><td><?php print $closedtrades_arr[$i][1]; ?></td><td  align="right"><?php print $closedtrades_arr[$i][2]; ?></td>
				
				<td ><?php print $closedtrades_arr[$i][3]; ?></td>
				<td 
				<?php 
					if ($closedtrades_arr[$i][4] == 'LONG') print 'bgcolor="#A6FF47"'; 
					if ($closedtrades_arr[$i][4] == 'SHORT') print 'bgcolor="#FF6B47"'; 
				?>><?php print $closedtrades_arr[$i][4]; ?></td>
				<td align="right"><?php print $closedtrades_arr[$i][5]; ?></td>
				
				<td align="right"><?php print $closedtrades_arr[$i][6]; ?></td><td align="right"><?php print $closedtrades_arr[$i][7]; ?></td><td align="right"><?php print $closedtrades_arr[$i][8]; ?></td><td align="right"><?php print $closedtrades_arr[$i][9]; ?></td>
				</tr>
				
				<?php
				}
				$i=$i+1;
				}		
			?>
			
			
			</table>
			
			
<?php			
			
						
			unset($closedtrades_exparr );
			unset($closedtrades_arr );
	}


?>