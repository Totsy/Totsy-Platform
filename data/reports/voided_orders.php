<?php

/*
* This report generates a csv of voided orders, and takes up
* to two optional arguments:
* 
* 1) Start Date: "YYYY-MM-DD"
* 2) End Date: "YYYY-MM-DD"
* 
* TODO: The date strings are not working in the query at all,
* this needs to be figured out if we want to run this report 
* by date range.
*/

// purely for debugging bliss
function debug( $thingie ){
	var_dump( $thingie );
	exit;
}

// Configuration
require_once 'reports_conf.php';
ini_set("display_errors", false);

$fields = array(
		"order_id",
		"authKey",
		"auth_confirmation",
		"firstname",
		"lastname",
		"address",
		"city",
		"state",
		"zip",
		"card_number",
		"card_type",
		"date_created",
		"description",
		"quantity",
		"color",
		"sale_retail",
		"tracking_number"
	);

echo implode(",", $fields);

// aaaaand here's mongo!
$mongo = new Mongo($mhost);

// Get the orders collection
$mongoorders = $mongo->$mdb->orders;

// Set the search properties
if(isset($argv[2])){
	$start = new MongoDate(strtotime($argv[1] . "00:00:00"));
	$stop = new MongoDate(strtotime($argv[2] . "00:00:00"));
	$options = array(
		'items.status' => 'Order Canceled',
		'date_created' => array('$gte' => $start, '$lte' => $stop)
	);
}elseif(isset($argv[1])){
	$start = new MongoDate(strtotime($argv[1] . "00:00:00"));
	$options = array(
		'items.status' => 'Order Canceled',
		'date_created' => array('$gte' => $start)
	);
}else{
	$options = array( 'items.status' => 'Order Canceled' );
}

$voids = $mongoorders->find( $options );

//echo "There are " . $voids->count() . " voided orders that need our attention.\n";

foreach($voids AS $void){
	foreach($void['items'] AS $item){
		$result = array(
			$void['order_id'],
			$void["authKey"],
			$void["auth_confirmation"],
			$void["firstname"],
			$void["lastname"],
			$void["address"],
			$void["city"],
			$void["state"],
			$void["zip"],
			$void["card_number"],
			$void["card_type"],
			$void["date_created"],
			$item["description"],
			$item["quantity"],
			$item["color"],
			$item["sale_retail"],
			$item["tracking_number"]
		);
	}
	echo implode(",", $result);
}