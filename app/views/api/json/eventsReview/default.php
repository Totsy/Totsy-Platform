<?php 
$out = array('events'=>array(),'pending'=>array(),'closing'=>array());
if (isset($token)){
	$out['token'] = $token;
} 

if (is_array($events)){ 
	foreach($events as $event){ 
		$evnt['id'] = $event['_id'];
		$evnt['name'] = $event['name'];
		$evnt['description'] = eventsReview_default_json_cut_string($event['blurb']);
		$evnt['short'] = (empty($event['short'])) ? eventsReview_default_json_cut_string($event['blurb'],45) : $event['short'];
		$evnt['availableItems'] = $event['available_items']==true?'YES':'NO';
		$evnt['brandName'] = $event['vendor'];
		$evnt['image'] = $event['event_image'];
		$evnt['image_small'] = $event['event_image_small'];
		$evnt['discount'] = floor($event['maxDiscount']);
		$evnt['url'] = $base_url.'sale/'.$event['url']."?gotologin=true";
		$evnt['start_date'] = date('m-d-y g:i:s A',$event['start_date']['sec']);
		$evnt['end_date'] = date('m-d-y g:i:s A',$event['end_date']['sec']);
		$evnt['categories'] = $event['groups']['categories'];
		$evnt['ages'] = $event['groups']['ages'];
		$evnt['items'] = $event['items'];
		$evnt['tag'] = implode(',',$event['tags']['ages']);
		$out['events'][] = $evnt;
	}
}

if (is_array($pending) && count($pending)){
	unset($event,$evnt); 
	foreach($pending as $event){ 
		$evnt['name'] = $event['name'];
		$evnt['url'] = $base_url.'sale/'.$event['url']."?gotologin=true";
		$out['pending'][] = $evnt;
	}
}

if (is_array($closing) && count($closing)){ 
	unset($event,$evnt);
	foreach($closing as $event){ 
		$evnt['name'] = $event['name'];
		$evnt['url'] = $base_url.'sale/'.$event['url']."?gotologin=true";
		if (is_object($event['end_date'])) { 
			$event['end_date'] = array(
				'sec' => $event['end_date']->sec,
				'usec' => $event['end_date']->usec
			); 
		}
		$evnt['end_date'] = date('m-d-y g:i:s A',$event['end_date']['sec']);
		$out['closing'][] = $evnt;
	}
}
$out['max_off'] = floor($maxOff);
echo json_encode($out);

function eventsReview_default_json_cut_string($str,$length=null){
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