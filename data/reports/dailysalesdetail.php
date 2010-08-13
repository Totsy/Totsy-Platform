<?php

/**
* Daily Sales Detail Report
* 
* This report generates the line item detail listing for operations
* and purchasing. This is run every morning for the previous day.
* 
* This report outputs the following:
*   Order Id (orders._id)
*   Event (events.event_name)
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
$start_date = '2010-08-12';
$end_date = '2010-08-13';
$start = new MongoDate(strtotime("$start_date 00:00:00"));
$end = new MongoDate(strtotime("$end_date 00:00:00"));

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
$mongoevents = $mongo->$mdb->events;
$mongoitems = $mongo->$mdb->items;

// find dates between 1/15/2010 and 1/30/2010
$orders = $mongoorders->find(array("date_created" => array('$gt' => $start, '$lte' => $end)));

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
			// Get the event name since it is missing from order
			$event = $mongoevents->findOne( array( 'items' => $item['item_id'] ));
			// Get the sku (vendor_style) from items since it is missing from order item
			$item_id = new MongoID( $item['item_id'] );
			$product = $mongoitems->findOne( array( '_id' => $item_id ));
			// Check for shipping phone number
			if(!isset($order['shipping']['phone'])){
				$order['shipping']['phone'] = '';
			}
			// Get the 8-character shorter order id
			$orderid = substr($order['_id'], 0, 8);
			// looping through embedded array
			$output[] = array(
				$orderid,
				$event['name'],
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

/*
* debugging
*/
//$count = count($output) - 1;
//echo "We got $count order items for $start_date.\n";

// spit out that stuff
foreach($output AS $line){
	echo implode('|', $line);
	echo "\n";
}
