<?php

/*
* CREDITS - issues credits to a pile of user accounts for a variety
* of reasons.
*/

// purely for debugging bliss
function debug( $thingie ){
	var_dump( $thingie );
	exit;
}

// Configuration
require_once '../reports/reports_conf.php';

// aaaaand here's mongo!
$mongo = new Mongo($mhost);

$mongousers = $mongo->$mdb->users;
$mongocredits = $mongo->$mdb->credits;

foreach($list AS $item){
	// Get the user._id for the credit entry
	$options = array('email' => $item['email']);
	$user = $mongousers->findOne( $options );
	// Check to see if we got a user record, otherwise the credit assignment will fail
	if($user['_id'] == ''){
		echo "----- ERROR ----- There was a problem finding the user account for email " . $item['email'] . "\n";
	}else{
		$record = array(
			'customer_id' => $user['_id'],
			'type' => 'Credit',
			'description' => 'Customer Service Credit',
			'amount' => $item['amount'],
			'date_created' => new MongoDate()
		);
		$mongocredits->save( $record );
		$mongousers->update($options, array('$inc' => array('total_credit' => $item['amount'])));
		array_push( $record, $item['email']);
		echo implode( ",", $record ) . "\n";
	}
}
