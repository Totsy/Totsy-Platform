<?php echo '<?xml version="1.0"?>'; ?>
<root>
<?php if (isset($token)){ ?>
	<token><?php echo $token?></token>
<?php } ?>
<?php if (is_array($events)){ ?>
	<events>
	<?php foreach($events as $event){ ?>
		<event>
			<name><?php echo $event['name'] ?></name>
			<description><?php echo $event['blurb'] ?></description>
			<availableItems><?php echo $event['available_items']==true?'YES':'NO';?></availableItems>
			<brandName><?php echo $event['brand_name']?></brandName>
			<image><?php echo $event['base_url'].$event['event_image']; ?></image>
			<discount><?php echo number_format($event['maxDiscount'],2); ?></discount>
			<url><?php echo $event['base_url'].'sale/'.$event['url']; ?></url>
		</event>
	<?php }?>
	</events>
<?php } ?>
</root>