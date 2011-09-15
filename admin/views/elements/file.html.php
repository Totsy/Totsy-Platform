<?

extract(array(
	'item' => null,
	'editable' => false
), EXTR_SKIP);

$renameUrl = $this->url(array(
	'controller' => 'files', 'action' => 'rename',
	'id' => $item->_id
));

?>

<?=$this->html->image($item->url(), array('alt' => 'image')); ?>
<div class="meta">
	<?php if ($editable): ?>
		<div class="name" contenteditable target="<?=$renameUrl; ?>"><?=$item->name ?></div>
	<?php else: ?>
		<div class="name"><?=$item->name ?></div>
	<?php endif; ?>
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