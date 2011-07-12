<?php 
$out = array();
if (isset($token)){
	$out['token'] = $token;
} 

if (is_array($events)){ 
	$out['events'] = array();
	
	foreach($events as $event){ 
		$evnt['name'] = $event['name'];
		$evnt['description'] = $event['blurb'];
		$evnt['availableItems'] = $event['available_items']==true?'YES':'NO';
		$evnt['brandName'] = $event['vendor'];
		$evnt['image'] = $event['base_url'].$event['event_image'];
		$evnt['discount'] = number_format($event['maxDiscount'],2);
		$evnt['url'] = $event['base_url'].'sale/'.$event['url'];
		$out['events'][] = $evnt;
	}
} 
echo json_encode($out);

?>