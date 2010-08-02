<?php

/**
* Connect to mongodb server, and remove all cart items
* that are 11 minutes old or older
*/

// just for debugging convenience
function debug( $thingie ){
	var_dump( $thingie );
	exit;
}

/**
* Configuration
*/
$mhost = '172.20.8.33';
$mdb = 'totsy';
$minutes = 10; // set this to the number of MINUTES your expiration needs
$expire = $minutes * 60;

$mongo = new Mongo("mongodb://$mhost");

$mongocarts = $mongo->$mdb->carts;

/**
* Uncomment to get a count of cart items BEFORE cleanup
*/
echo "There are " . $mongocarts->count() . " items in the collection before cleanup.\n";

$deadline = new MongoDate(time() - 660);

$mongocarts->remove(array("created" => array('$lte' => $deadline)));

/**
* Uncomment to get a count of cart items AFTER cleanup
*/
echo "There are " . $mongocarts->count() . " items in the collection after cleanup.\n";
