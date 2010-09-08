<?php

/**
* Daily Sales Detail Report
* 
* This report generates the line item detail listing for operations
* and purchasing. This is run every morning for the previous day.
* 
* This report outputs the following:
*   Order Id (order.order_id)
*   Event (items.event_name)
*   Product Name (items.description)
*   Product SKU (item.vendor_style)
*   Shipping Method (orders.shippingMethod)
*   Quantity (orders.items.quantity)
*   Item Price (orders.items.sale_retail)
*   Subtotal (Quantity * Item Price)
*   Created At (derived from orders._id)
*   Customer ID (users._id)
*   First Name (users.firstname)
*   Last Name (users.lastname)
*   Email (users.email)
*   Shipping Name (orders.shipping.firstname . orders.shipping.lastname)
*   Shipping Address (orders.shipping.address)
*   Shipping City (orders.shipping.city)
*   Shipping State (orders.shipping.state)
*   Shipping Zip (orders.shipping.zip)
*   Shipping Phone (orders.shipping.phone)
* 
* NOTE: Presently there's no timestamp stored in orders or order line items.
* This makes for an excruciating experience, as we have to loop through the 
* entire orders collection, derive the timestamp from each ObjectID, and 
* compare to a timestamp.
*/ 

// purely for debugging bliss
function debug( $thingie ){
	var_dump( $thingie );
	exit;
}

// Configuration
require_once 'reports_conf.php';
$debug = false;
// Suppress errors unless you're debugging
if($debug == true){
	error_reporting(E_ERROR);
}else{
	error_reporting(0);
}

$fields = array(
	'Order Id',
	'Event',
	'Product Name',
	'Product Color',
	'Profuct Size',
	'Product SKU',
	'Shipping Method',
	'Quantity',
	'Item Price',
	'Subtotal',
	'Created At',
	'Customer ID',
	'Customer Name',
	'Email',
	'Billing Name',
	'Shipping Name',
	'Shipping Address',
	'Shipping City',
	'Shipping State',
	'Shipping Zip',
	'Shipping Phone'
	);

$output[] = $fields;

$mongo = new Mongo($mhost);

$mongoorders = $mongo->$mdb->orders;
$mongousers = $mongo->$mdb->users;
$mongoitems = $mongo->$mdb->items;
$mongoevents = $mongo->$mdb->events;

// Set the search properties
if(isset($argv[2])){
	$start = new MongoDate(strtotime($argv[1] . "00:00:00"));
	$stop = new MongoDate(strtotime($argv[2] . "00:00:00"));
	$options = array(
		'date_created' => array('$gte' => $start, '$lte' => $stop)
	);
	$orders = $mongoorders->find( $options );
}elseif(isset($argv[1])){
	$start = new MongoDate(strtotime($argv[1] . "00:00:00"));
	$options = array(
		'date_created' => array('$gte' => $start)
	);
	$orders = $mongoorders->find( $options );
}else{
	$options = array();
	$orders = $mongoorders->find();
}

/*
* debugging
*/
if($debug == true){
	echo "We got " . $orders->count() . " order items with the following options:\n";
	debug( $options );
}

foreach($orders AS $order){
	// Get the user name
	if(strlen($order['user_id']) == 24){
		$userid = new MongoID( $order['user_id'] );
		$user = $mongousers->findOne( array( '_id' => $userid ));
	} else {
		$user = $mongousers->findOne( array( '_id' => $order['user_id'] ));
	}
	$username_first = $user['firstname'];
	$username_last = $user['lastname'];
	$email = $user['email'];
	// How many line items do we have?
	if(count($order['items']) > 0){
		// Reach into the items array for line item data
		foreach($order['items'] AS $item){
			// Get the sku (vendor_style) from items since it is missing from order item
			$item_id = new MongoID( $item['item_id'] );
			$product = $mongoitems->findOne( array( '_id' => $item_id ));
			// Check for event_name
			if(!isset($item['event_name'])){
				$event = $mongoevents->findOne( array( 'items' => $item['item_id'] ));
				$item['event_name'] = $event['name'];
			}
			// looping through embedded array
			$output[] = array(
				$order['order_id'],
				$item['event_name'],
				$item['description'],
				$item['color'],
				$item['size'],
				$product['vendor_style'],
				$order['shippingMethod'],
				$item['quantity'],
				$item['sale_retail'],
				$item['quantity'] * $item['sale_retail'],
				date('Y-m-d h:i:s', $order['date_created']->sec),
				$order['user_id'],
				$username_first . ' ' . $username_last,
				$email,
				$order['billing']['firstname'] . ' ' . $order['billing']['lastname'],
				$order['shipping']['firstname'] . ' ' . $order['shipping']['lastname'],
				$order['shipping']['address'],
				$order['shipping']['city'],
				$order['shipping']['state'],
				$order['shipping']['zip'],
				$order['shipping']['phone']
				);
		}
	}
}

// spit out that stuff
foreach($output AS $line){
	echo implode('|', $line);
	echo "\n";
}
