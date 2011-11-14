<?php echo '<?xml version="1.0"?>'; ?>
<root>
<?php if (isset($data['token'])){ ?>
	<token><?php echo $data['token']?></token>
<?php } ?>
	<items>
<?php if (is_array($data['items'])){ ?>
	<?php foreach($data['items'] as $item){ ?>
		<item id="<?php echo $item['_id']?>">
			<name><?php echo htmlspecialchars($item['description']) ?></name>
			<url><?php echo $item['base_url'].'sale/'.$item['event_url'].'/'.$item['url'];?></url>
			<image><?php echo $item['base_url'].'image/'.$item['primary_image'].'.jpg';?></image>
			<instock><?php echo $item['total_quantity']>0?true:false;?></instock>
			<discount><?php echo floor($item['precent_off']); ?></discount>
			<?php 
			if (array_key_exists('blurb',$item)) {
				?><description><?php echo htmlspecialchars($item['blurb']); ?></description><?php 
			} else {
				?><description/><?php 
			}
			?>
			<startDate><?php echo date('m-d-y g:i:s A',$item['start_date']['sec']); ?></startDate>
			<endDate><?php echo date('m-d-y g:i:s A',$item['end_date']['sec']); ?></endDate>
		</item>
	<?php }?>
<?php } ?>
	</items>
</root>