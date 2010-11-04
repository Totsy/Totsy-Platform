<?php

/**
* Generate the daily sales summary report, for a specified 
* date range, with daily totals providing the following output:
* 
* NY Total Sales
* NY Total Tax
* NY Total Shipping
* PA Total Sales
* PA Total Tax
* PA Total Shipping
* Other Total Sales
* Other Total Tax
* Other Total Shipping
* Grand Total Sales
* Grand Total Tax
* Grand Total Shipping
* 
*/

// just for debugging convenience
function debug( $thingie ){
	var_dump( $thingie );
	exit;
}

/**
* Configuration
*/
require_once 'reports_conf.php';
$states = array( 'NY', 'PA', 'Other' );
$start_date = $argv[1];
$start = new MongoDate(strtotime($start_date));
$end_date = $argv[2];
$end = new MongoDate(strtotime($end_date));
$options = array(
		'date_created' => array( '$gte' => $start, '$lte' => $end ),
		'items.0.order_status' => array( '$ne' => 'Order Canceled')
	);

/**
* The report
*/
$mongo = new Mongo($mhost);
$mongoorders = $mongo->$mdb->orders;
$orders = $mongoorders->find( $options );

/*
* Debugging shizzle
*/
//$message = 'There are ' . $orders->count() . ' orders that are in this date range that are getting processed.';
//debug( $message );

if($orders->count() == 0){
	echo "There are no orders in this timeframe.\n";
	exit;
}else{
	echo "DATE,CATEGORY,ORDER_ID,STATUS,STATE,QUANTITY,SALE,TAX,FREIGHT\n";
}

foreach($orders AS $order){
	switch( $order['billing']['state'] ){
		case 'NY':
			$state = 'NY';
			break;
		case 'PA':
			$state = 'PA';
			break;
		default:
			$state = 'Other';
	}
	
	// Get the total count of order items per order
	$itemcount = 0;
	foreach($order['items'] AS $orderitem){
		$itemcount = $itemcount + $orderitem['quantity'];
		$order_status = $orderitem['status'];
	}
	
	// Get the date string from the MongoDate for the order
	$date = date('Y-m-d', $order['date_created']->sec);

	echo "$date,$state," . $order['order_id'] . ',' . $order_status . ',' . $order['billing']['state'] . ',' . $itemcount . ',' . $order['subTotal'] . ',' . $order['tax'] . ',' . $order['handling'] . "\n";
	
}
