<?php

/*
* Generate a list of people who purchased products from us
* between $start_date and $end_date, and issue them a
* $5 credit by incrementing the users document and 
* creating a credits document.
* 
* OUTPUT: firstname, lastname, email
* 
*/

// purely for debugging bliss
function debug( $thingie ){
	var_dump( $thingie );
	exit;
}

// Check to see if start and end dates are provided
if(isset($argv[1]) && isset( $argv[2]) ){
	$start = new MongoDate( strtotime( $argv[1] . ' 00:00:00' ) );
	$end = new MongoDate( strtotime( $argv[2] . ' 00:00:00' ) );
}else{
	echo "\n\n\nYou forgot to include the start date and end date, please call this script thusly:\n\n     php -f omfgwtfbrblolbbq.php 2010-11-15 2010-12-01\n\n";
	exit;
}

// Configuration
require_once 'reports_conf.php';

// Collect a list of orders between the given dates
$options = array(
	'date_created' => array('$gte' => $start),
	'date_created' => array('$lt' => $end)
);

/**
* The report
*/
$mongo = new Mongo($mhost);
$mongoorders = $mongo->$mdb->orders;
$mongousers = $mongo->$mdb->users;
$mongocredits = $mongo->$mdb->credits;

$orders = $mongoorders->find( $options );

// Debugging output
//echo $orders->count() . ' orders found between ' . $argv[1] . ' and ' . $argv[2] . "\n\n";

foreach( $orders AS $order ){
	$user_ids[] = $order['user_id'];
}

// Make them unique
$user_id = array_unique( $user_ids );

// Debugging output
//echo 'There are ' . count( $user_id ) . " unique users that placed these orders.\n\n";

// Loop through the users, and give them a $5 credit and output first,last,email
foreach( $user_id AS $user ){
	if(strlen( $user ) == 24){
		$useroption = array( '_id' => new MongoId( $user ) );
	}else{
		$useroption = array( '_id' => $user );
	}
	
	// Increment total_credit by $5 for each user
	$mongousers->update($useroption, array('$inc' => array('total_credit' => 5)));
	
	// Create a credits document
	$record = array(
		'customer_id' => $user,
		'type' => 'Credit',
		'description' => 'Customer Service Credit',
		'amount' => 5,
		'date_created' => new MongoDate()
	);
	//debug( $record );
	$mongocredits->save( $record );
	
	// Get a cursor for iterating output and inserting credit documents
	$users = $mongousers->find( $useroption );
	
	foreach($users AS $userdoc){		
		//echo $userdoc['firstname'] . ',' . $userdoc['lastname'] . ',' . strtolower($userdoc['email']) . "\n";
	}

}




