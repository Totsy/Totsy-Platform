<?php

/**
* Generate the daily sales report, for a specified date. with the following output:
* 	Date
* 	Day
* 	Items Sold
* 	Orders
* 	Total Sales
* 	Average Basket
* 	Average Items / Order
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
$start_date = '2010-08-04';
$start_timestamp = strtotime($start_date);
$end_date = '2010-08-05';
$end_timestamp = strtotime($end_date);

$mongo = new Mongo($mhost);

$mongoorders = $mongo->$mdb->orders;

$orders = $mongoorders->find();

$idx = 0;

foreach($orders AS $order){
	$id = $order["_id"];
	$ordertimestamp = $id->getTimestamp();
	// Only look at the documents in our date range
	if(($ordertimestamp > $start_timestamp) AND ($ordertimestamp < $end_timestamp)){
		//var_dump( $order);
		// Add up the order total amounts for grand total
		$tot_amount += $order['total'];
		// How many line items do we have?
		if(count($order['items']) > 1){
			// Reach into the items array for line item data
			$item_qty = 0;
			foreach($order['items'] AS $item){
				$item_qty += $item['quantity'];
			}
			$tot_qty += $item_qty;
		}else{
			// We only got one line item for this order
			$tot_qty += $item_qty;
		}
		// Increment the counter
		$idx++;
	}
}

// Calculate average baskets
$avg_basket = $tot_amount / $idx;
$avg_items = $tot_qty / $idx;

echo "DATE: $start_date\nITEMS SOLD: $tot_qty\nORDERS: $idx\nTOTAL: $tot_amount\nAVERAGE BASKET: $avg_basket\nAVERAGE ITEMS / ORDER: $avg_items\n";