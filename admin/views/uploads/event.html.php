<table border="1" cellspacing="30" cellpadding="30">
<tr>
	<th align="justify">
		Image Name
	</th>
	<th align="justify">
		Image
	</th>
	<th align="center">
		Event Preview Image
	</th>
	<th align="center">
		Event Banner Image
	</th>
	<th align="center">
		Event Logo Image
	</th>
</tr>
<tr>
	<td align="center" width="200">
		<?php echo $fileName ?>
	</td>
	<td align="center">
		<?=$this->html->image("/image/$id.jpg", array('alt' => 'altText')); ?>
	</td>
	<td align="center">
		<input type="radio" name="preview_image" value="<?=$id;?>">
	</td>
	<td align="center">
		<input type="radio" name="banner_image" value="<?=$id;?>">
	</td>
	<td align="center">
		<input type="radio" name="logo_image" value="<?=$id;?>">
	</td>
</tr>
</table>
