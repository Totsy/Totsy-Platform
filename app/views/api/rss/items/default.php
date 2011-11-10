<?php use app\models\File; ?>
<?php echo '<?xml version="1.0"?>'."\n"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:tns='http://totsy.com/totsy-xml-rss-name-space' xmlns:ev='http://purl.org/rss/1.0/modules/event/'>
	<channel> 
    	<title>Items</title>     
    	<link>http://totsy.com/</link> 
    	<description></description> 
<?php if (is_array($data['items'])){ 
		foreach($data['items'] as $item){ 

			if (isset($item['primary_image'])){
				$image = $item['base_url'].'image/'.$item['primary_image'].'.jpg';
				$image_size = File::first($item['primary_image'])->file->getSize();
			} else {
				$image = $item['base_url'].'/img/no-image-large.jpeg';
				$image_size = '35445';
			} ?>
		<item>
			<title><?php echo htmlspecialchars($item['description']) ?></title>
			<link><?php echo $item['base_url'].'sale/'.$item['event_url'].'/'.$item['url'];?></link>
			<enclosure url="<?=$image?>" length="<?=$image_size?>" type="image/jpeg" />
<?php if (array_key_exists('blurb',$item)) { ?>
			<description><?php echo htmlspecialchars($item['blurb']); ?></description>
<?php } else { ?>
			<description/>
<?php } ?>
			<guid isPermaLink="false"><?php echo $item['_id']?></guid>
			<tns:instock><?php echo $item['total_quantity']>0?true:false;?></tns:instock>
			<tns:discount><?php echo floor($item['percent_off']); ?></tns:discount>
			<ev:startdate><?php echo date('c',$item['start_date']['sec']); ?></ev:startdate>
			<ev:enddate><?php echo date('c',$item['end_date']['sec']); ?></ev:enddate>
			<ev:type>sale</ev:type>
		</item>
<?php 	}
	} 
?>
	</channel>
</rss>



