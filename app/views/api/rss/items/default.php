<?php echo '<?xml version="1.0"?>'; ?>
<rss version="2.0">
	<channel> 
    	<title>Items</title>     
    	<link>http://totsy.com/</link> 
    	<description></description> 
<?php if (is_array($data['items'])){ ?>>
	<?php foreach($data['items'] as $item){ ?>
		<item>
			<id><?php echo $item['_id']?></id>
			<title><?php echo htmlspecialchars($item['description']) ?></title>
			<link><?php echo $item['base_url'].'sale/'.$item['event_url'].'/'.$item['url'];?></link>
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
	</channel>
</rss>



