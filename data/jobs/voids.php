<?php

/*
* Output a simple csv format of voided orders
* 
* This currently searches for orders based on authKey, then
* matches them up by event name.
*/


// purely for debugging bliss
function debug( $thingie ){
	var_dump( $thingie );
	exit;
}

// Configuration
require_once '../reports/reports_conf.php';
$status = 'Order Canceled';

// aaaaand here's mongo!
$mongo = new Mongo($mhost);

$mongoorders = $mongo->$mdb->orders;
$mongoevents = $mongo->$mdb->events;

$authKeys = array(
		"3226594712",
		"3026490052",
		"3195192618",
		"3231167834",
		"3231163666",
		"3226128066",
		"3225255720",
		"3229380677",
		"3199414866",
		"3199111418",
		"3196733930",
		"3193276815",
		"3224852775",
		"3213842520"
	);

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