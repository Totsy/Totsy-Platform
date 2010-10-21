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
		"3233714999",
		"3233632809",
		"3231275976",
		"3229335346",
		"3195413499",
		"3233562573",
		"3233467420",
		"3233071882",
		"3233066332",
		"3233036430",
		"3233035498",
		"3237554128",
		"3236767932",
		"3234390133",
		"3233746433",
		"3233712422",
		"3232993191",
		"3239836524",
		"3238081924",
		"3237200333",
		"3206646546",
		"3241089192",
		"3240777599"
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