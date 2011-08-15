<?=$this->html->image($item->url(), array('alt' => 'image')); ?>
<div class="meta">
	<div class="name"><?=$item->name ?></div>
	<div class="id"><?=$item->_id ?></div>
	<?php
		$meta = array();

		if ($item->created_date) {
			$meta[] = date('m/d/y', $item->created_date->sec);
		}
		$meta[] = sprintf('%.2f MB', $item->file->getSize() / MEGABYTE);

		if ($item->mime_type) {
			$meta[] = $item->mime_type;
		}
	?>
	<?=implode(', ', $meta) ?>
</div>