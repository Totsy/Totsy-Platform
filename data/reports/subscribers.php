<?php

/*
* User Subscription report
* 
* This report spits out a csv of users that
* registered on the site.
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
$start = strtotime( '2010-09-11 00:00:00' );
$options = array(
	'created_date' => array('$gte' => $start),
	'invited_by' => array('$ne' => 'btrendie')
);

// Aaaaaand here's mongo!
$mongo = new Mongo($mhost);

// Get the users collection
$mongousers = $mongo->$mdb->users;

// Get a cursor of the user documents
$users = $mongousers->find(
		$options,
		array( 
			"_id" => 1,
			"firstname" => 1,
			"lastname" => 1,
			"email" => 1
		)
	);

var_dump( $options );

// Output column headers
echo "First,Last,Email\n";

foreach( $users AS $user ){
	echo trim($user['firstname']) . ',' . trim($user['lastname']) . ',' . trim($user['email']) . "\n";
}