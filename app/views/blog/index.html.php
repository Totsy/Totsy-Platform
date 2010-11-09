<?php foreach ($rss->item as $item): ?>
	<div>
		<h2><?php echo $item->title?></h2>
		<div><?=$item->pubDate?></div>
		<?php echo $item->description?>
	</div>
<?php endforeach ?>
