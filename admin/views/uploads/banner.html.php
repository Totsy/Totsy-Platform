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
</tr>
<tr>
	<td align="center" width="200">
		<?php echo $fileName ?>
	</td>
	<td align="center">
		<?=$this->html->image("/image/$id.jpg", array('alt' => 'altText')); ?>
	</td>
	<td align="center">
	    <?=$this->html->text(); ?>
		<input type="text" name="url" value="">
	</td>
</tr>
</table>
