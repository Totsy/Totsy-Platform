<table border="1" cellspacing="30" cellpadding="30">
<tr>
	<th align="justify">
		Image Name
	</th>
	<th align="justify">
		Image
	</th>
	<th align="center">
		Splash Big Image
	</th>
	<th align="center">
		Splash Small Image
	</th>
	<th align="center">
		Event Image
	</th>
	<th align="center">
		Logo Image
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
		<input type="radio" name="splash_big_image" value="<?php echo $id;?>">
	</td>
	<td align="center">
		<input type="radio" name="splash_small_image" value="<?php echo $id;?>">
	</td>
	<td align="center">
		<input type="radio" name="event_image" value="<?php echo $id;?>">
	</td>
	<td align="center">
		<input type="radio" name="logo_image" value="<?php echo $id;?>">
	</td>
</tr>
</table>
