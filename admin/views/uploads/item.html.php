<table border="1" cellspacing="30" cellpadding="30">
<tr>
	<th align="justify">
		Active
	</th>
	<th align="justify">
		Image Name
	</th>
	<th align="center">
		Main Product Image
	</th>
	<th align="center">
		Detail Product Image
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
		<input type="checkbox" name="main_image" value="<?=$id;?>">
	</td>
	<td align="center">
		<input type="checkbox" name="detail_image_<?=$id;?>" value="<?=$id;?>">
	</td>
</tr>
</table>
