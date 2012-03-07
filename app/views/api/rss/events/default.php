<?php echo '<?xml version="1.0"?>'; ?>
<rss version="2.0" xmlns:tns="http://totsy.com/totsy-xml-rss-name-space">
	<channel> 
    	<title>Events</title>     
    	<link>http://totsy.com/</link> 
    	<description></description> 
<?php 
if (is_array($events) && count($events)>0){
	foreach($events as $event){ ?>
		<item id="<?php echo $event['_id']; ?>">
			<title><?php echo htmlspecialchars($event['name']) ?></title>         
			<link><?php echo $base_url.'sale/'.$event['url']; ?></link> 
			<description><?php echo htmlspecialchars( $event['blurb'] ) ?></description>
			<short><?php echo (empty($event['short'])) ? htmlspecialchars(default_events_rss_cut_string($event['blurb'],45)) : htmlspecialchars($event['short']); ?></short>
			<category>OPEN</category> 
			<image><?php echo $event['event_image']; ?></image> 
			<image_small><?php echo $event['event_image_small']; ?></image_small> 
			<brandName><?php echo htmlspecialchars($event['vendor'])?></brandName>
			<availableItems><?php echo $event['available_items']==true?'YES':'NO';?></availableItems>
			<discount><?php echo floor($event['maxDiscount']); ?></discount>
			<startDate><?php echo date('m-d-y g:i:s A',$event['start_date']['sec']); ?></startDate>
			<endDate><?php echo date('m-d-y g:i:s A',$event['end_date']['sec']); ?></endDate>
		<?php 
		if (count($event['groups']['categories'])>0){
			foreach ($event['groups']['categories'] as $c){ ?>
			<category><?php echo $c; ?></category><?php		
			}
		} 
		?>
			<tns:ages><?php
			if (count($event['groups']['ages'])>0){
				foreach ($event['groups']['ages'] as $a){
				?>
				<tns:age><?php echo $a; ?></tns:age><?php		
				}
			} 
			?>
			</tns:ages>
			<tns:items>
<?php   if (!empty($event['items'])){
			foreach($event['items'] as $item) { ?>
				<tns:item id="<?php echo $item; ?>" />
<?php		}
		} ?>
			</tns:items>
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

<?php 

function default_events_rss_cut_string($str,$length=null){
	$return = '';
	$str = strip_tags($str);
	$split = preg_split("/[\s]+/",$str);
	$len = 0;
	if (is_array($split) && count($split)>0){
		foreach($split as $splited){
			$tmp_len = $len + strlen($splited) +1;
			if ($tmp_len < $length){
				$len = $tmp_len;
				$return.= $splited.' ';
			} else {
				break;
			}
		}
	}
	
	if (strlen($return)>0){
		return $return;
	} else {
		return $str;
	}
}

?>