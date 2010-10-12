<?php

/*
* btrendie Duplicate User report
* 
* This report spits out a csv of users that
* registered on the site and also have an 
* existing Totsy account - making their
* btrendie account a dupe.
* 
* OUTPUT: firstname, lastname, email, count
* 
*/

// purely for debugging bliss
function debug( $thingie ){
	var_dump( $thingie );
	exit;
}

// Configuration
require_once '../reports/reports_conf.php';
$start = new MongoDate( strtotime( '2010-09-11 00:00:00' ) );
$options = array(
	'invited_by' => 'btrendie'
);
$ticker = 0;

// Aaaaaand here's mongo!
$mongo = new Mongo($mhost);

// Get the users collection
$mongousers = $mongo->$mdb->users;

// Get a cursor of btrendie users
$users = $mongousers->find( $options );

foreach( $users AS $user){
	$opts = array(
		'email' => $user['email']
	);
	$count = $mongousers->find($opts)->count();
	if($count > 1 && !isset($user['logincounter'])){
		echo $user['_id'] . ',' . $user['firstname'] . ',' . $user['lastname'] . ',' . $user['email'] . ',' . $count . "\n";
		$mongousers->remove( array( '_id' => new MongoId($user['_id'])));
		$ticker++;
	}
}

echo "There are $ticker btrendie accounts that just got tossed because they are dupes of Totsy users.\n";

