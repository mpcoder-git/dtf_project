<tr height="30px">
<td><?php echo $row['newstitle']; ?></td>
<td align="center"><?php print $this->rus_date("d F Y",strtotime($row['newsdata'])); ?></td>
<td align="center"><?php if ($row['active'] == 0){ echo 'не активна'; } ; ?></td>
<td align="right">
	<a href="/controlpanel/newseditor/<?php echo $row['id']; ?>" class="mainbutton">Редактировать</a> 
	<a href="/controlpanel/deletenews/<?php echo $row['id']; ?>" class="mainbutton">Удалить</a>
</td>
</tr>