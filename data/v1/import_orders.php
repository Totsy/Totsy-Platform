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
*/

// DON'T FORGET TO SET THIS
$pgdbname = 'totsy';
$mongodbname = 'totsytest';

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
$orders = $mongoorders->find()->limit(20);

// reset cursor, just in case
$orders->rewind();

// loop through all user documents, adding each missing element
foreach($orders AS $order){
	// Get the order items
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
	WHERE order_id =' . $order['_id'];
	$item_res = pg_query($pg, $item_sql);
	$items = pg_fetch_all( $item_res );
	$idx = 0;
	if(is_array($items)){
		foreach($items AS $item){
			$item['_id'] = new MongoId();
			$item['line_number'] = $idx;
			$order['items'][] = $item;
			$idx++;
		}
	}else{
		$item['_id'] = new MongoId();
		$item['line_number'] = $idx;
		$order['items'][] = $item;		
	}
	$order_id = $order['_id'];
	unset( $order['_id'] );
	$mongoorders->update(
			array( '_id' => $order_id),
			array('$set' => $order)
		);
	//var_dump( $order );
}