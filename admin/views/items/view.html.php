<?=$this->html->style('files.css');?>
<?=$this->html->style('admin_common.css');?>

<div class="grid_16">
	<h2 id="page-heading">View Item</h2>

	<h3></h3>
	<dl>
		<dt>ID</dt>
		<dd><?=$item->_id; ?></dd>
		<dt>Image</dt>
		<dd>
		<?php
			$images = $item->images();
			$url = $images['primary'] ? $images['primary']->url() : "/img/no-image-small.jpeg";
			echo $this->html->image($url);
		?>
		</dd>
		<dt>Description</dt>
		<dd><?=$item->description; ?></dd>
		<dt>Vendor</dt>
		<dd><?=$item->vendor; ?></dd>
		<dt>Vendor Style</dt>
		<dd><?=$item->vendor_style; ?></dd>
		<dt>Color</dt>
		<dd><?=$item->color; ?></dd>
	</dl>

	<h3>Filenames for Form Uploads</h3>
	<?php $names = $item->uploadNames(); ?>
	<dl>
		<?php foreach ($names['form'] as $type => $name): ?>
			<dt><?=$type; ?></dt>
			<dd><?=$name; ?></dd>
		<?php endforeach; ?>
	</dl>
</div>
