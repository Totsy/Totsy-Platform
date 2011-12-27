<?php 
$out = array();
if (isset($token)){
	$out['token'] = $token;
} 
if (is_array($items)){ 
	$out['products'] = array();
	
	foreach($items as $item){ 
		$itm['id'] = $item['_id'];
		$itm['name'] = $item['description'];
		$itm['producturl'] = $item['base_url'].'/sale/'.$item['event_url'].'/'.$item['url'];
		$itm['bigimage'] = $item['base_url'].'/image/'.$item['zoom_image'].'.jpg';
		$itm['smallimage'] = $item['base_url'].'/image/'.$item['primary_image'].'.jpg';
		$itm['instock'] = $item['total_quantity']>0?true:false;
		$out['products'][] = $itm;
	}
} 
echo json_encode($out);
?>