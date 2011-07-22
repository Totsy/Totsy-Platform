<?php echo '<?xml version="1.0"?>'; ?>
<root>
<?php if (isset($data['token'])){ ?>
	<token><?php echo $data['token']?></token>
<?php } ?>
<?php if (is_array($data['items'])){ ?>
	<products>
	<?php foreach($data['items'] as $item){ ?>
		<product id="<?php echo $item['_id']?>">
			<name><?php echo htmlspecialchars($item['description']) ?></name>
			<producturl><?php echo $item['base_url'].'sale/'.$item['event_url'].'/'.$item['url'];?></producturl>
			<bigimage><?php echo $item['base_url'].'image/'.$item['zoom_image'].'.jpg';?></bigimage>
			<smallimage><?php echo $item['base_url'].'image/'.$item['primary_image'].'.jpg';?></smallimage>
			<instock><?php echo $item['total_quantity']>0?true:false;?></instock>
		</product>
	<?php }?>
	</products>
<?php } ?>
</root>