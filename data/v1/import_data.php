<?php

/**
* Importing customer data from the strike out platform 
* for script kiddies.
* 
* There are other things you must do FIRST in order for
* this script to work:
* 
* 1: psql -A -d totsy -f export_customers.sql -o customers.csv -F ","
* 2: delete all duplicate entries and add to ../redirects.txt
* 3: mongoimport -d totsytest -c users --drop --headerline --type csv --file customers.csv
* 4: php -f import_data.php
* 
**/

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
$mongousers = $mongoconn->$mongodbname->users;

/////////////////////////////////////////////////////////////
// USER COLLECTION
/////////////////////////////////////////////////////////////

// get cursor of user ids for all this nonsense
$users = $mongousers->find();

// reset cursor, just in case
$users->rewind();

// loop through all user documents, adding each missing element
foreach($users AS $user){
	// move dates from strings to MongoDate() objects, save as _orig
	$user['created_orig'] = $user['created'];
	$user['created'] = new MongoDate(strtotime($user['created']));
	$user['updated_orig'] = $user['updated'];
	$user['updated'] = new MongoDate(strtotime($user['updated']));
	// determine if they are an affiliate or not
	if($user['first_name'] == 'Affiliate'){
		$user['affiliate'] = 1;
		$user['affiliate_url'] = $user['last_name'];
	}
	//
	// fetch invitation codes
	//
	$invsql = 'SELECT id, code FROM invitation_tokens WHERE customer_id = ' . $user['_id'];
	$result = pg_query($pg, $invsql);
	if (!$result) {
	  echo "An error occurred - no invitation tokens for this account (#".$user['id'].").\n";
	  exit;
	}
	$invitationcodes = pg_fetch_all($result);
	foreach($invitationcodes AS $invitationcode){
		$codes[] = $invitationcode['code'];
		$ids[] = $invitationcode['id'];
	}
	$user['invitation_codes'] = $codes;
	//
	// fetch invitations
	//
	$invitation_code_ids = implode( ', ', $ids);
	$invitessql = 'select created_at AS "date", invitee_email AS "email" from invitations where invitation_token_id IN ('.$invitation_code_ids.')';
	var_dump( $user );
	unset($codes, $ids);
	pg_free_result($result);
	echo "\n\n////////////////////////////////////////////////////////////////////////////\n\n";
	// update the user with an UPSERT
}



/*
$result = pg_query($pg, $sql_customers);
if (!$result) {
  echo "An error occured.\n";
  exit;
}

$users = pg_fetch_all($result);
var_dump($users);
*/

// convert timestamps to mongo date() objects

/////////////////////////////////////////////////////////////
// ORDER COLLECTION
/////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////
// CREDITS COLLECTION
/////////////////////////////////////////////////////////////

/////////////////////////////////////////////////////////////
// ADDRESS COLLECTION
/////////////////////////////////////////////////////////////
