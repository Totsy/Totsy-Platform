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
		$evnt['image'] = $event['event_image'];
		$evnt['discount'] = floor($event['maxDiscount']);
		$evnt['url'] = $base_url.'sale/'.$event['url'];
		$evnt['start_date'] = date('m-d-y g:i:s A',$event['start_date']['sec']);
		$evnt['end_date'] = date('m-d-y g:i:s A',$event['end_date']['sec']);
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
		$evnt['end_date'] = date('m-d-y g:i:s A',$event['end_date']['sec']);
		$out['closing'][] = $evnt;
	}
}
echo json_encode($out);

?>