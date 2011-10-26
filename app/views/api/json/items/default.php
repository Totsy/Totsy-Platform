<?php 
$out = array();
if (isset($token)){
	$out['token'] = $token;
} 
$out['items'] = array();
if (is_array($items)){ 
	foreach($items as $item){ 
		$itm['id'] = $item['_id'];
		$itm['name'] = $item['description'];
		$itm['url'] = $item['base_url'].'/sale/'.$item['event_url'].'/'.$item['url'];
		$itm['image'] = $item['base_url'].'/image/'.$item['zoom_image'].'.jpg';
		$itm['instock'] = $item['total_quantity']>0?true:false;
		if (array_key_exists('blurb',$item)) {
			$itm['description'] = $item['blurb'];
		} else {
			$itm['description'] = '';
		}
		$itm['discount'] = floor($item['precent_off']);
		$itm['start_date'] = date('m-d-y g:i:s A',$item['start_date']['sec']);
		$itm['end_date'] = date('m-d-y g:i:s A',$item['end_date']['sec']);
		$out['items'][] = $itm;
	}
} 
echo json_encode($out);
?>