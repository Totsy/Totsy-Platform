<?php echo '<?xml version="1.0"?>'; ?>
<root>
<?php if (isset($token)){ ?>
	<token><?php echo $token?></token>
<?php } ?>
<?php if (is_array($events)){ ?>
	<events>
	<?php foreach($events as $event){ ?>
		<event>
			<name><?php echo htmlspecialchars($event['name']) ?></name>
			<description><?php echo htmlspecialchars( $event['blurb'] ) ?></description>
			<availableItems><?php echo $event['available_items']==true?'YES':'NO';?></availableItems>
			<brandName><?php echo htmlspecialchars($event['vendor'])?></brandName>
			<image><?php echo $event['event_image']; ?></image>
			<discount><?php echo floor($event['maxDiscount']); ?></discount>
			<url><?php echo $base_url.'sale/'.$event['url']; ?></url>
			<startDate><?php echo date('m-d-y g:i:s A',$event['start_date']['sec']); ?></startDate>
			<endDate><?php echo date('m-d-y g:i:s A',$event['end_date']['sec']); ?></endDate>
		</event>
	<?php }?>
	</events>
	<pendingEvents>
	<?php foreach($pending as $event){ ?>
		<pendingEvent>
			<name><?php echo htmlspecialchars($event['name']) ?></name>
			<url><?php echo $base_url.'sale/'.$event['url']; ?></url>
		</pendingEvent>
	<?php } ?>
	</pendingEvents>
	<closingEvents>
	<?php foreach($closing as $event){ ?>
		<closingEvent>
			<name><?php echo htmlspecialchars($event['name']) ?></name>
			<url><?php echo $base_url.'sale/'.$event['url']; ?></url>
			<endDate><?php echo date('m-d-y g:i:s A',$event['end_date']['sec']); ?></endDate>
		</closingEvent>
	<?php } ?>
	</closingEvents>
<?php } ?>
</root>