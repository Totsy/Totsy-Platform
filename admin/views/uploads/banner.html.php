<table border="1" cellspacing="30" cellpadding="30">
<tr>
	<th align="justify">
		Image Name
	</th>
	<th align="justify">
		Image
	</th>
	<th align="justify">
		URL
	</th>
	<th align="justify">
		Open New Page
	</th>
</tr>
<tr>
	<td align="center" width="200">
		<?php echo $fileName ?>
	</td>
	<td align="center">
		<?php echo $this->html->image("/image/$id.jpg", array('alt' => 'altText')); ?>
	</td>
	<td align="center">
		<input type="text" name="url[<?php echo $id; ?>]" value="">
	</td>
	<td align="center">
		<input type="hidden" name="img[]" value="<?php echo $id; ?>">
	</td>
	<td align="center">
		<input type="checkbox" name="newPage" value="1">
	</td>
</tr>
</table>
