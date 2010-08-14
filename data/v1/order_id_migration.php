<?php

/**
* This is a quick and dirty script to take all existing order documents
* in mongodb that do not have an order_id property, and give them one
* derived from the first 8 characters of the _id.
* 
* This is actually for new orders, as the legacy orders coming over 
* from the strike out platform already have order_id defined.
*/

// DON'T FORGET TO SET THIS
$mongodbname = 'totsy';
$debug = false;

// just for debugging convenience
function debug( $thingie ){
	var_dump( $thingie );
	exit;
}

// ...aaaaand here's mongo!
$mongoconn = new Mongo();
$mongoorders = $mongoconn->$mongodbname->orders;
$conditions = array(
		"order_id" => array(
				'$exists' => false
			)
	);

if($debug == true){
	$orders = $mongoorders->find($conditions)->limit(20);
} else {
	$orders = $mongoorders->find($conditions);
}

foreach($orders AS $order){
	$item = $order;
	$order_id = $item['_id'];
	unset($item['_id']);
	$item['order_id'] = substr($order_id, 0, 8);
	echo "The _id is $order_id and the order_id is " . $item['order_id'] . ".\n";
	$mongoorders->update(
			array( '_id' => $order_id),
			array('$set' => $item)
		);
}