<?php

/**
* Connect to mongodb server, and remove all cart items
* that are older than the time specified
*/

// just for debugging convenience
function debug( $thingie ){
	var_dump( $thingie );
	exit;
}

/**
* Configuration
*/
if($_SERVER['USER'] == 'lhanson'){
    $mhost = '127.0.0.1';
}else{
    $mhost = 'test';
}
$mdb = 'totsy';
$minutes = 17; // set this to the number of MINUTES your expiration needs
$expire = $minutes * 60;

$mongo = new Mongo("mongodb://$mhost");

$mongocarts = $mongo->$mdb->carts;

/**
* Uncomment to get a count of cart items BEFORE cleanup
*/
//echo "There are " . $mongocarts->count() . " items in the collection before cleanup.\n";

$deadline = new MongoDate(time() - $expire);

$mongocarts->remove(array("created" => array('$lte' => $deadline)));

/**
* Uncomment to get a count of cart items AFTER cleanup
*/
//echo "There are " . $mongocarts->count() . " items in the collection after cleanup.\n";
