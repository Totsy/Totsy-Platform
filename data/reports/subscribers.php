<?php

/*
* User Subscription report
* 
* This report spits out a csv of users that
* registered on the site.
* 
* This script will be significantly modified
* when we start storing timestamps with user
* documents. For now we have to loop through 
* the entire collection, convert _id to get 
* timestamps and go from there.
* 
* With timestamps, we can probably just script
* mongoexport with query parameters.
* 
* OUTPUT: firstname, lastname, email
* 
*/

// purely for debugging bliss
function debug( $thingie ){
	var_dump( $thingie );
	exit;
}

// Configuration
require_once 'reports_conf.php';
$start = strtotime( '2010-08-17 00:00:00' );

// Aaaaaand here's mongo!
$mongo = new Mongo($mhost);

// Get the users collection
$mongousers = $mongo->$mdb->users;

// Get a cursor of ALL USERS, ugh.
$users = $mongousers->find(
		array(),
		array( 
			"_id" => 1,
			"firstname" => 1,
			"lastname" => 1,
			"email" => 1
		)
	);

// Output column headers
echo "First,Last,Email\n";

foreach( $users AS $user ){
	// Is this a V2 user?
	if(strlen($user['_id']) == 24 ){
		$mongoid = new MongoId( $user['_id'] );
		$timestamp = $mongoid->getTimestamp();
		$signup = date( 'Y-m-d', $timestamp );
		if( $timestamp >= $start ){
			// Output that data
			echo trim($user['firstname']) . ',' . trim($user['lastname']) . ',' . trim($user['email']) . "\n";
		}
	}
}