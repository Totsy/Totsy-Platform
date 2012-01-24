<?php echo '<?xml version="1.0"?>'; ?>
<root xmlns:tns="http://totsy.com/totsy-xml-rss-name-space">
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
			<event><?php echo $item['event']['0']; ?></event>
			<vendor><?php echo htmlspecialchars($item['vendor']); ?></vendor>
			<categories><?php
			if (count($item['categories'])>0){
				foreach ($item['categories'] as $c){
				?>
				<category><?php echo $c; ?></category><?php		
				}
			} 
			?>
			</categories>
			<ages><?php
			if (count($item['ages'])>0){
				foreach ($item['ages'] as $a){
				?>
				<age><?php echo $a; ?></age><?php		
				}
			} 
			?>
			</ages>
		</product>
	<?php }?>
	</products>
<?php } ?>
</root>