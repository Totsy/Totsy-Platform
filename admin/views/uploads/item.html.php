<table border="1" cellspacing="30" cellpadding="30">
<tr>
	<th align="justify">
		Image Name
	</th>
	<th align="justify">
		Image Preview
	</th>
	<th align="center">
		Primary Product Image
	</th>
	<th align="center">
		Secondary Product Image
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
		<input type="checkbox" name="primary-<?=$id;?>" value="<?=$id;?>">
	</td>
	<td align="center">
		<input type="checkbox" name="secondary-<?=$id;?>" value="<?=$id;?>">
	</td>
</tr>
</table>
