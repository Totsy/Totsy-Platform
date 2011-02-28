<?php

/**
 * Grabs a cursor of user documents that have uppercase letters 
 * in their email addresses, and lowercases them.
 */

// CONFIGURATION
$dbhost = '127.0.0.1';
$dbname = 'totsy';

// CONNECTION
$idx = 0;
$m = new Mongo($dbhost);
$db = $m->$dbname;
$users = $db->users;
$uppers = new MongoRegex("/[A-Z]/");
$cursor = $users->find(array('email' => $uppers))->limit(1000);

// EXECUTION
echo "There are " . $cursor->count() . " users in the database.\n";
foreach($cursor AS $user){
	$loweremail = strtolower($user['email']);
	$users->update(array('email' => $user['email']), array('$set' => array('email' => $loweremail)));
	echo "$idx: Just lowered " . $user['email'] . " to $loweremail.\n";
	$idx++;
}




