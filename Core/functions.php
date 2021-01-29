<?php
//функции для работы с сигналами

			function GenerateIdeasHtml(){
				
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
				
				
				
				$output = 
				'<table class="signals_table" id="s_table">
				<tr bgcolor="#D3D3D3">
				<th>Time</th><th>Idea</th><th>Rank</th><th>Stock</th><th>Side</th><th>Size</th><th>Price</th><th>TP</th><th>SL</th>
				</tr>';
				
				$i=0;
				foreach ($ideas_arr as &$value) {
					
					if($i>0){
						/*
						$output = $output.
						'<tr onclick="document.title = "Сигналы ["+document.getElementById("s_table").rows[this.rowIndex].cells[3].innerHTML+"]";"   <?php if($i%2) print "class="signals_table_row"";?>>';
					*/




						
					}
				
				$i=$i+1;	
				}
				$output = $output.'</table>';
				return $output;
			}
?>