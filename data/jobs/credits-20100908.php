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

// This is the list of credits to process
$list = array(
	array('email' => 'breeucsb@gmail.com', 'amount' => 40, 'reason' => 'Customer Service Credit'),
	array('email' => 'anitankapoor@yahoo.com', 'amount' => 15, 'reason' => 'Customer Service Credit'),
	array('email' => 'billetdouxs@aol.com', 'amount' => 10, 'reason' => 'Customer Service Credit'),
	array('email' => 'lietze_y@hotmail.com', 'amount' => 10, 'reason' => 'Customer Service Credit'),
	array('email' => 'heletj8@hotmail.com', 'amount' => 10, 'reason' => 'Customer Service Credit'),
	array('email' => 'Jennifermathys@gmail.com', 'amount' => 10, 'reason' => 'Customer Service Credit'),
	array('email' => 'cah@alum.mit.edu', 'amount' => 15, 'reason' => 'Customer Service Credit'),
	array('email' => 'rniehus@me.com', 'amount' => 15, 'reason' => 'Customer Service Credit'),
	array('email' => 'tiffanyandjaime@hotmail.com', 'amount' => 15, 'reason' => 'Customer Service Credit'),
	array('email' => 'rherstein@briggs.com', 'amount' => 15, 'reason' => 'Customer Service Credit'),
	array('email' => 'thumpr2523@earthlink.net', 'amount' => 15, 'reason' => 'Customer Service Credit'),
	array('email' => 'lschanely@gmail.com', 'amount' => 15, 'reason' => 'Customer Service Credit'),
	array('email' => 'Julielee1@gmail.com', 'amount' => 15, 'reason' => 'Customer Service Credit'),
	array('email' => 'k_carnovale@hotmail.com', 'amount' => 15, 'reason' => 'Customer Service Credit'),
	array('email' => 'jillomonster@yahoo.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'ajalikhan@gmail.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'cathyschneiderman@yahoo.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'mcb1723@yahoo.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'millersurfteam@gmail.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'wlhlm@aol.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'stevensweaver@gmail.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'jnv4@verizon.net', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'anniemelendrez@me.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'sesmolchuck@aol.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'robincgillis@yahoo.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'kmontags@Hotmail.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'thewdfam@gmail.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'kristin5419@yahoo.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'va.greenwoods@gmail.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'grosey1@cox.net', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'naylev@yahoo.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'raventres@aol.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'charchar30@gmail.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'angel@nnproduce.net', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'cschaefer@csfmed.net', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'clmorris17@hotmail.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'randi282@aol.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'rmlabarbera@aol.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'katilanehughes@gmail.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'rooreider@aol.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'tonikackert@hotmail.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'ktbend@live.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'gwbrandes@yahoo.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'kerishawn@hotmail.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'yandmchisko@yahoo.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'franef@hotmail.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'raclick1977@yahoo.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'Milkywoman98@yahoo.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'kfbrana@yahoo.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'Irodriguez1157@gmail.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'tracyjkrebs@msn.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'stacygolden100@msn.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'kjcleland@hotmail.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'anneyimgeiger@yahoo.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'Triciafinnegan@comcast.net', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'tapthemind@aol.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'kmmcphail@yahoo.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'nburley15@yahoo.com', 'amount' => 5, 'reason' => 'Customer Service Credit'),
	array('email' => 'danielletoet@sbcglobal.net', 'amount' => 5, 'reason' => 'Customer Service Credit')
);

include_once 'credits.inc.php';