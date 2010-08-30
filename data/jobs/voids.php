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
		"3125639083",
		"3128731392",
		"3128081197",
		"3128016008",
		"3127918424",
		"3127845119",
		"3127797661",
		"3126111146",
		"3126104854",
		"3126099760",
		"3125994077",
		"3125639083",
		"3125621770",
		"3125189236",
		"3124116293",
		"3122080418",
		"3119754280",
		"3118648653",
		"3116456242",
		"3116372742",
		"3116284032",
		"3116282561",
		"3114921420",
		"3114618266",
		"3114595628",
		"3114588592",
		"3114552428", 
		"3127604971",
		"3127581707",
		"3129860000",
		"3129851419",
		"3127436710",
		"3129187507",
		"3131858704",
		"3131777506",
		"3130928856",
		"3129187507",
		"3125549213",
		"3132747168",
		"3131964841",
		"3131062924",
		"3131061679",
		"3136886927",
		"3132399748",
		"3132118277",
		"3127866375",
		"3127514045",
		"3114836189",
		"3139362556",
		"3139330869",
		"3137047606",
		"3136886927",
		"3132399748",
		"3132118277",
		"3127866375",
		"3127514045",
		"3114836189",
		"3138711115", 
		"3131093357",
		"3142403989",
		"3136841382",
		"3118530631",
		"3115254389",
		"3138716391",
		"3135144041",
		"3148599903",
		"3148377788",
		"3148376974",
		"3148191547",
		"3148065680",
		"3148060127",
		"3147980626",
		"3147636052",
		"3146129939",
		"3142048538",
		"3140942038",
		"3118622112",
		"3117064209",
		"3116315698"
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