<?php echo '<?xml version="1.0"?>'; ?>
<rss version="2.0">
	<channel> 
    	<title>Events</title>     
    	<link>http://totsy.com/</link> 
    	<description></description> 
<?php 
if (is_array($events) && count($events)>0){
	foreach($events as $event){ ?>
		<item>
			<title><?php echo htmlspecialchars($event['name']) ?></title>         
			<link><?php echo $base_url.'sale/'.$event['url']; ?></link> 
			<description><?php echo htmlspecialchars( $event['blurb'] ) ?></description>
			<category>OPEN</category> 
			<image><?php echo $event['event_image']; ?></image> 
			<brandName><?php echo htmlspecialchars($event['vendor'])?></brandName>
			<availableItems><?php echo $event['available_items']==true?'YES':'NO';?></availableItems>
			<discount><?php echo floor($event['maxDiscount']); ?></discount>
			<startDate><?php echo date('m-d-y g:i:s A',$event['start_date']['sec']); ?></startDate>
			<endDate><?php echo date('m-d-y g:i:s A',$event['end_date']['sec']); ?></endDate>
		</item><?php 
	}
}
if (is_array($pending) && count($pending)>0){ 
	foreach($pending as $event){ ?>
		<item>
			<title><?php echo htmlspecialchars($event['name']) ?></title>         
			<link><?php echo $base_url.'sale/'.$event['url']; ?></link> 
			<description/>
			<category>PENDING</category>  
		</item><?php 
	}
}
if (is_array($closing) && count($closing)>0){
	foreach($closing as $event){ ?>
		<item>
			<title><?php echo htmlspecialchars($event['name']) ?></title>         
			<link><?php echo $base_url.'sale/'.$event['url']; ?></link> 
			<description><?php echo htmlspecialchars( $event['blurb'] ) ?></description>
			<category>CLOSING</category> 
			<endDate><?php echo date('m-d-y g:i:s A',$event['end_date']['sec']); ?></endDate>
		</item><?php 
	}
}?>
	</channel>
</rss>