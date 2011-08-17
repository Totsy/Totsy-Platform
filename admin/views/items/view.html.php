<?=$this->html->style('files.css');?>
<?=$this->html->style('agile_uploader.css');?>
<?=$this->html->style('admin_common.css');?>

<div class="grid_16">
	<h2 id="page-heading">View Item</h2>

	<h3>Upload URLs</h3>
	<?php $names = $item->uploadNames(); ?>
	<dl>
		<?php foreach ($names['form'] as $type => $name): ?>
			<dt><?=$type; ?></dt>
			<dd><?=$name; ?></dd>
		<?php endforeach; ?>
	</dl>
</div>
