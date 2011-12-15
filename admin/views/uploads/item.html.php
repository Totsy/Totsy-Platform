<table border="1" cellspacing="30" cellpadding="30">
<tr>
	<th align="justify">
		Image Name
	</th>
	<th align="justify">
		Image Preview
	</th>
	<th align="center">
		Zoom Image
	</th>
	<th align="center">
		Primary Image
	</th>
	<th align="center">
		Alternate Image
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
		<input type="radio" name="zoom_image" value="<?php echo $id;?>">
	</td>
	<td align="center">
		<input type="radio" name="primary_image" value="<?php echo $id;?>">
	</td>
	<td align="center">
		<input type="checkbox" name="alternate-<?php echo $id;?>" value="<?php echo $id;?>">
	</td>
</tr>
</table>