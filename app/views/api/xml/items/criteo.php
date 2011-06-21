<?php echo '<?xml version="1.0"?>'; ?>
<root>
<?php if (isset($token)){ ?>
	<token><?php echo $token?></token>
<?php } ?>
<?php if (is_array($items)){ ?>`
	<products>
	<?php foreach($items as $item){ ?>
		<product id="<?php echo $item['_id']?>">
			<name><?php echo $item['description'] ?></name>
			<producturl><?php echo $item['base_url'].'/sale/'.$item['event_url'].'/'.$item['url'];?></producturl>
			<bigimmage><?php echo $item['base_url'].'/image/'.$item['zoom_image'].'.jpg';?></bigimage>
			<smallimage><?php echo $item['base_url'].'/image/'.$item['primary_image'].'.jpg';?></smallimage>
			<instock><?php echo $item['total_quantity']>0?true:false;?></instock>
		</product>
	<?php }?>
	</products>
<?php } ?>
</root>