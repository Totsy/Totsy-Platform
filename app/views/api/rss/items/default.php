<?php use app\models\File; ?>
<?php echo '<?xml version="1.0"?>'."\n"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:tns="http://totsy.com/totsy-xml-rss-name-space" xmlns:ev='http://purl.org/rss/1.0/modules/event/' xmlns:dc="http://purl.org/dc/elements/1.1/">
	<channel> 
		<atom:link rel="self" href="https://slavik.totsy.com/api/items.rss?auth_token=a23a3ca72553e553be95d2982300fe84cd3058f4" type="application/rss+xml"/>
    	<title>Items</title>     
    	<link>http://totsy.com/</link> 
    	<description></description> 
<?php if (is_array($data['items'])){ 
		foreach($data['items'] as $item){ 
			if (isset($item['zoom_image'])){
				$image = $item['base_url'].'image/'.$item['zoom_image'].'.jpg';
				$image_size = File::first($item['zoom_image'])->file->getSize();
			} else if (isset($item['primary_image'])){
				$image = $item['base_url'].'image/'.$item['primary_image'].'.jpg';
				$image_size = File::first($item['primary_image'])->file->getSize();
			} else {
				$image = $item['base_url'].'/img/no-image-large.jpeg';
				$image_size = '35445';
			} 
			$link = $item['base_url'].'sale/'.$item['event_url'].'/'.$item['url'];
			?>
		<item>
			<title><![CDATA[<?php echo $item['description']; ?>]]></title>
			<link><?=$link?></link>
			<enclosure url="<?=$image?>" length="<?=$image_size?>" type="image/jpeg" />
<?php if (array_key_exists('blurb',$item)) { ?>
			<description><![CDATA[<?php 
				//echo htmlspecialchars('<img src="'.$image.'" width="150" height="150">'.$item['blurb']);
				?><a href="<?=$link ?>">
					<img src="<?=$image?>" style="border-top-width: 0px; border-right-width: 0px; border-bottom-width: 0px; border-left-width: 0px; border-top-style: solid; border-right-style: solid; border-bottom-style: solid; border-left-style: solid; margin-left: 4px; margin-right: 4px; margin-top: 4px; margin-bottom: 4px;>
				   </a><?php 
				echo $item['blurb'] ?> 
				?>]]></description>
<?php } else { ?>
			<description/>
<?php } ?>
			<guid isPermaLink="false"><?php echo $item['_id']?></guid>
<?php if (count($item['categories'])>0){
			foreach ($item['categories'] as $c){ ?>
			<category><?php echo $c; ?></category><?php		
			}
} ?>
			<tns:brand><?php echo htmlspecialchars($item['vendor']); ?></tns:brand>
			<tns:event id="<?php echo $item['event']['0']; ?>" />
			<tns:instock><?php echo $item['total_quantity']>0?true:false;?></tns:instock>
			<tns:discount><?php echo floor($item['percent_off']); ?></tns:discount>
			<tns:ages><?php
			if (count($item['ages'])>0){
				foreach ($item['ages'] as $age){
				?>
				<tns:age><?php echo $age; ?></tns:age>
				<?php 
				}
			} ?>
			</tns:ages>
			<ev:startdate><?php echo date('c',$item['start_date']['sec']); ?></ev:startdate>
			<ev:enddate><?php echo date('c',$item['end_date']['sec']); ?></ev:enddate>
			<ev:type>sale</ev:type>
			<dc:creator><![CDATA[ Totsy ]]></dc:creator>
		</item>
<?php 	
		}
	} 
	
?>
	</channel>
</rss>


