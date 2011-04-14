<table border="1" cellspacing="30" cellpadding="30">
<tr>
	<th align="justify">
		Img Name
	</th>
	<th align="justify">
		Img
	</th>
	<th align="center">
		Bkground
	</th>
	<th align="center">
		Logo
	</th>
	<th align="center">
		Feat. #1
	</th>
	<th align="center">
		Feat. #2
	</th>
	<th align="center">
		Feat. #3
	</th>
	<th align="center">
		Feat. #4
	</th>
</tr>
<tr>
	<td align="center" width="100">
		<?php echo $fileName ?>
	</td>
	<td align="center">
		<a href="/image/<?php echo $id; ?>.jpg" target="_blank" ><?=$this->html->image("/image/$id.jpg", array('alt' => 'altText', 'width' => 100)); ?></a>
	</td>
	<td align="center">
		<input type="checkbox" name="img_type[background]" value="<?=$id;?>">
	</td>
	<td align="center">
		<input type="checkbox" name="img_type[logo]" value="<?=$id;?>">
	</td>
	<td align="center">
		<input type="checkbox" name="img_type[feature_one]" value="<?=$id;?>">
	</td>
	<td align="center">
		<input type="checkbox" name="img_type[feature_two]" value="<?=$id;?>">
	</td>
	<td align="center">
		<input type="checkbox" name="img_type[feature_three]" value="<?=$id;?>">
	</td>
	<td align="center">
		<input type="checkbox" name="img_type[feature_four]" value="<?=$id;?>">
	</td>
</tr>
</table>