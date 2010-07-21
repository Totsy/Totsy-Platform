<?php

/**
* Importing customer data from the strike out platform 
* for script kiddies.
* 
* Note that the initial SELECT has two additional lines 
* for making debugging reasonable.
* 
**/

$conn = pg_pconnect("dbname=totsy");
if (!$conn) {
  echo "Yeowch! There's no PostgreSQL around here that goes by that name.\n";
  exit;
}

/**
* USER COLLECTION
**/

/**
* This is the SQL for getting the initial list of customers. 
* Note that this SELECT has two additional lines 
* for making debugging reasonable.
* 
**/
$sql_customers = '
	SELECT c.id AS "_id",
	c.first_name AS "firstname",
	c.last_name AS "lastname",
	0 AS "affiliate",
	c.login AS "username",
	c.email AS "email",
	1 AS "legacy",
	c.crypted_password AS "password",
	c.password_salt AS "salt",
	c.created_at AS "created",
	c.updated_at AS "updated",
	c.last_login_at AS "lastlogin",
	c.last_login_ip AS "lastip",
	c.login_count AS "logincounter",
	1 AS "active",
	it.code AS "invitation_code"

	FROM customers c,
	invitation_tokens it

	WHERE first_name != \'\'
	AND it.customer_id = c.id
	-- skip crap data
	AND c.id > 3000

	ORDER BY c.id
	-- limit output
	LIMIT 20
	;';

$result = pg_query($conn, $sql_customers);
if (!$result) {
  echo "An error occured.\n";
  exit;
}

$arr = pg_fetch_all($result);
var_dump($arr);

// find all invitations
// find all credits
// find all orders
// convert timestamps to mongo date() objects

/**
* ORDER COLLECTION
**/

/**
* ADDRESS COLLECTION
**/