<?php

/**
* Importing customer data from the strike out platform 
* for script kiddies.
* 
* There are other things you must do FIRST in order for
* this script to work:
* 
* 1: psql -Aqt -d totsy -f export_customers.sql -o customers.csv -F ","
* 2: delete all duplicate entries and add to ../redirects.txt
* 3: mongoimport -d totsytest -c users --drop --fieldFile customer_fields.txt --type csv --file customers.csv
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
	// move dates from strings to MongoDate() objects
	$user['created'] = new MongoDate(strtotime($user['created_orig']));
	$user['updated'] = new MongoDate(strtotime($user['updated_orig']));
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
	// do they have an invitation code?
	if(count($invitationcodes) < 1)
	{
		//echo $user['email'] . " has no invitation codes.\n";
	} else {
		foreach($invitationcodes AS $invitationcode){
			$codes[] = $invitationcode['code'];
			$ids[] = $invitationcode['id'];
		}
		$user['invitation_codes'] = $codes;
	}
	//
	// fetch invitations
	//
	$invitation_code_ids = implode( ', ', $ids);
	$invitessql = 'select created_at AS "date", invitee_email AS "email", invited_customer_id from invitations where invitation_token_id IN ('.$invitation_code_ids.')';
	$result1 = pg_query($pg, $invitessql);
	if (!$result1) {
	  echo "An error occurred - no invitations for this account (#".$user['id'].").\n";
	  exit;
	}
	$invitations = pg_fetch_all($result1);
	//debug($invitations);
	// are there any invitations for this user?
	if((count($invitations) < 1) OR (!$invitations))
	{
		//echo $user['email'] . " needs to get off their butt and send some invitations.\n\n";
	} else {
		foreach($invitations AS $invitation){
			if($invitation['invited_customer_id'] == $user['_id']){
				$invitation['status'] = 'Ignored';
			}elseif($invitation['invited_customer_id'] == ''){
				$invitation['status'] = 'Sent';
			}else{
				$invitation['status'] = 'Accepted';
				//unset($invitation['invited_customer_id']);
			}
			unset($invitation['invited_customer_id']);
			$array_invitations[] = $invitation;
		}
	}
	$user['invitations'] = $array_invitations;
		
	//
	// DEBUGGING OUTPUT
	//
	//var_dump( $user );
	//echo "\n\n////////////////////////////////////////////////////////////////////////////\n\n";

	//
	// update the user with an UPSERT
	//
	$userid = $user['_id'];
	unset($user['_id']);
	$mongousers->update(
	        array('_id' => $userid),
	        array('$set' => $user)
	        );
	
	//
	// clear everything for the next user document
	unset($codes, $ids, $array_invitations);
	pg_free_result($result);
	pg_free_result($result1);

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
