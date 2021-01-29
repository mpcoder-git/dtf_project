<tr height="30px">
<td><?php echo $row['family']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['otchestvo']; ?></td>
<td><?php echo $row['nick']; ?></td>
<td><?php echo $row['email']; ?></td>
<td align="center"><?php if ($row['emailactivate'] == 0){ echo 'не активна'; } ; ?></td>
<td align="right">
	<?php if ($row['emailactivate'] == 0){ ?>
	<a href="/controlpanel/useractivate/<?php echo $row['id']; ?>" class="mainbutton">Активировать</a> 
	<?php } ?>
</td>
</tr>