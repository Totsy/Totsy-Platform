<?php

/**
* This script loops through the credits in MongoDB and:
* 1) loops through legacy credit documents
* 2) increments the user->total_credit for each document returned
*/

// DON'T FORGET TO SET THIS
$mongodbname = 'totsy';
$params = array( 'description' => "Legacy Credits");

// just for debugging convenience
function debug( $thingie ){
	var_dump( $thingie );
	exit;
}

// ...aaaaand here's mongo!
$mongoconn = new Mongo();
$mongocredits = $mongoconn->$mongodbname->credits;
$mongousers = $mongoconn->$mongodbname->users;

// get cursor of credits to apply to user documents
$credits = $mongocredits->find( $params );

echo "There are " . count( $credits ) . " credit records that need to know about their users.\n";

foreach( $credits AS $credit ){
	$mongousers->update(
			array( '_id' => $credit['customer_id']),
			array( '$inc' => array( 'total_credit' => $credit['amount'] ) )
		);
	echo "We just worked with the following credit record:\n\n";
	var_dump( $credit );
}
