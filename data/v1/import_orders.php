<?php

/**
* This script loops through the orders in MongoDB and:
* 1) fetches summary info
* 2) fetches shipping info
* 3) fetches line item info
*/

/**
* CRITICAL:
* Use of this script requires several views to be setup in
* your postgresql database, which can be done thusly:
* 
* 1) create view product_colors as select product_id, attribute_id, 
* attribute_value AS "color" from product_attributes where attribute_id = 137 
* and attribute_value != '';
* 
* 2) create view product_sizes as select product_id, attribute_id, 
* attribute_value AS "size" from product_attributes where attribute_id = 136 
* and attribute_value != '';
* 
* 3) create view product_weights as select product_id, attribute_id, 
* attribute_value AS "weight" from product_attributes where attribute_id = 179 
* and attribute_value != '';
* 
* 4) create index idx_order_products_orderid ON order_products (order_id);
* 
*/

// DON'T FORGET TO SET THIS
$pgdbname = 'totsy';
$mongodbname = 'totsytest';
$testorderid = '7080';
$debug = false;

// just for debugging convenience
function debug( $thingie ){
	var_dump( $thingie );
	exit;
}

// going native PG, no time for fancy classes
$pg = pg_pconnect("dbname=$pgdbname");
if (!$pg) {
  echo "Yeowch! There's no PostgreSQL around here that goes by that name.\n";
  exit;
}

// ...aaaaand here's mongo!
$mongoconn = new Mongo();
$mongoorders = $mongoconn->$mongodbname->orders;

// get cursor of user ids for all this nonsense
if($debug == true){
	// DEBUG, FIND SPECIFIC ORDER FOR TESTING
	$orders = $mongoorders->find(array('_id' => $testorderid));
} else {
	// PRODUCTION
	$orders = $mongoorders->find();
}

// reset cursor, just in case
$orders->rewind();

// loop through all user documents, adding each missing element
foreach($orders AS $order){
	$record = $order;
	// Get the order items
	if( $debug == true ){
		$order_id = $testorderid;
	}else{
		$order_id = $record['_id'];
	}
	$item_sql = 'SELECT 
	-- \'MISSING\' AS "_id",
	 COALESCE(pc.color, \'no color\') AS "color",
	 p.name AS "description",
	 op.product_id AS "item_id",
	 COALESCE(pw.weight, \'no weight\') AS "product_weight",
	 op.quantity,
	 op.price AS "sale_retail",
	 COALESCE(ps.size, \'no size\') AS "size",
	-- \'MISSING\' AS "url",
	 -- \'MISSING\' AS "line_number",
	 \'-----\' AS "status" 
	FROM order_products op
	LEFT JOIN products p ON op.product_id = p.id
	LEFT JOIN product_colors pc ON op.product_id = pc.product_id
	LEFT JOIN product_sizes ps ON op.product_id = ps.product_id
	LEFT JOIN product_weights pw ON op.product_id = pw.product_id
	WHERE order_id = ' . $order_id;
	$item_res = pg_query($pg, $item_sql);
	if(!$item_res){
		echo "Looks like there were no order items, or an error in the database happened while executing this query.\n";
		exit;
	}
	$items = pg_fetch_all( $item_res );
	if(count($items)>0){
		foreach($items AS $item){
			$item['_id'] = new MongoId();
			$record['items'][] = $item;
		}
	}
	
	// DEBUG
	if($debug == true){
		var_dump( $record );
	}else{
		unset( $record['_id']);
		$mongoorders->update(
				array( '_id' => $order_id),
				array('$set' => $record)
		);
	}

	pg_free_result( $item_res );
	unset( $record );
}