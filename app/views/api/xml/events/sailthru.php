<?php echo '<?xml version="1.0"?>'; ?>
<root xmlns:tns="http://totsy.com/totsy-xml-rss-name-space">
<?php if (isset($token)){ ?>
	<token><?php echo $token?></token>
<?php } ?>
<?php if (is_array($events)){ ?>
	<events>
	<?php foreach($events as $event){ ?>
		<event id="<?php echo $event['_id']; ?>">
			<name><?php echo htmlspecialchars($event['name']) ?></name>
			<description><?php echo htmlspecialchars( sailthru_xml_cut_string($event['blurb'],90) ) ?></description>
			<short><?php echo (empty($event['short'])) ? htmlspecialchars(sailthru_xml_cut_string($event['blurb'],45)) : htmlspecialchars($event['short']); ?></short>
			<availableItems><?php echo $event['available_items']==true?'YES':'NO';?></availableItems>
			<brandName><?php echo htmlspecialchars($event['vendor']);?></brandName>
			<image><?php echo $event['event_image']; ?></image>
			<image_small><?php echo $event['event_image_small']; ?></image_small>
			<discount><?php echo number_format($event['maxDiscount'],2); ?></discount>
			<url><?php echo $base_url.'sale/'.$event['url']; ?></url>
			<categories><?php
			if (count($event['groups']['categories'])>0){
				foreach ($event['groups']['categories'] as $c){
				?>
				<category><?php echo $c; ?></category><?php		
				}
			} 
			?>
			</categories>
			<ages><?php
			if (count($event['groups']['ages'])>0){
				foreach ($event['groups']['ages'] as $a){
				?>
				<age><?php echo $a; ?></age><?php		
				}
			} 
			?>
			</ages>
			<tns:items>
<?php   if (!empty($event['items'])){
			foreach($event['items'] as $item) { ?>
				<tns:item id="<?php echo $item; ?>" />
<?php		}
		} ?>
			</tns:items>
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
			<endDate><?php date('F j',$event['end_date']['sec']); ?></endDate>
		</closingEvent>
	<?php } ?>
	</closingEvents>
	<maxOff><?php echo floor($maxOff); ?></maxOff>
<?php } ?>
</root>

<?php 
function sailthru_xml_cut_string($str,$length=null){
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