<table border="1" cellspacing="30" cellpadding="30">
<tr>
	<th align="justify">
		Active
	</th>
	<th align="justify">
		Image Name
	</th>
	<th align="center">
		Event List Image
	</th>
	<th align="center">
		Event Image
	</th>
	<th align="center">
		Vendor Image
	</th>
</tr>
<tr>
	<td align="center">
		<input type="checkbox" name="file-<?=$id?>" value="1" checked>
	</td>
	<td align="center" width="200">
		<?php echo $fileName ?>
	</td>
	<td align="center">
		<input type="radio" name="list_image" value="<?=$id;?>">
	</td>
	<td align="center">
		<input type="radio" name="event_image" value="<?=$id;?>">
	</td>
	<td align="center">
		<input type="radio" name="vendor_image" value="<?=$id;?>">
	</td>
</tr>
</table>

