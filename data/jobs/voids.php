<?php

/*
* Output a simple csv format of voided orders
* 
* This currently searches for orders based on authKey, then
* matches them up by event name.
* 
* Call this script thusly, with great panache:
* 
*   php -f voids.php voids-2010102601.php
* 
* Failing to pass a datafile will result in a warning 
* followed by an abrupt exit.
*/


// purely for debugging bliss
function debug( $thingie ){
	var_dump( $thingie );
	exit;
}

if(isset( $argv[1] )){
	include $argv[1];
}else{
	echo "You need to pass a datafile to this script in order for it to work. Exiting now...\n";
	exit;
}

// Configuration
require_once '../reports/reports_conf.php';
$status = 'Order Canceled';

// aaaaand here's mongo!
$mongo = new Mongo($mhost);

$mongoorders = $mongo->$mdb->orders;
$mongoevents = $mongo->$mdb->events;

$voids = $mongoorders->find( 
		array(
			'authKey' => array('$in' => array_unique( $authKeys ) )
		) 
	);

echo "There are " . count( array_unique( $authKeys ) ) . " void requests, resulting in " . $voids->count() . " voids needing attention.\n";

foreach( $voids AS $void ){
	$order_id = $void['_id'];
	unset( $void['_id'] );
	$items = count( $void['items'] );
	echo "Order $order_id (" . $void['authKey'] . ") has $items items that need cancellation.\n";
	if($items > 0){
		// if there's items, we got status to update
		foreach( $void['items'] AS &$item ){
			$item['status'] = $status;
		}
	}
	$mongoorders->update(
		array( 'authKey' => $void['authKey'] ),
		$void
	);
}