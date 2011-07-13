<?php 
$out = array('events'=>array(),'pending'=>array(),'closing'=>array());
if (isset($token)){
	$out['token'] = $token;
} 

if (is_array($events)){ 
	foreach($events as $event){ 
		$evnt['name'] = $event['name'];
		$evnt['description'] = $event['blurb'];
		$evnt['availableItems'] = $event['available_items']==true?'YES':'NO';
		$evnt['brandName'] = $event['vendor'];
		$evnt['image'] = $base_url.$event['event_image'];
		$evnt['discount'] = number_format($event['maxDiscount'],2);
		$evnt['url'] = $base_url.'sale/'.$event['url'];
		$out['events'][] = $evnt;
	}
}

if (is_array($pending) && count($pending)){ 
	foreach($pending as $event){ 
		$evnt['name'] = $event['name'];
		$evnt['url'] = $base_url.'sale/'.$event['url'];
		$out['pending'][] = $evnt;
	}
}

if (is_array($closing) && count($closing)){ 
	foreach($closing as $event){ 
		$evnt['name'] = $event['name'];
		$evnt['url'] = $base_url.'sale/'.$event['url'];
		$out['closing'][] = $evnt;
	}
}

echo json_encode($out);

?>