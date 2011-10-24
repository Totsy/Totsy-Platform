<?php 
$out = array();
$out['report']['entry'] = array();
foreach( $cursor AS $row ){ 
	if (is_object($row)) $row = $row->data();
	
	$out['report']['entry'][] = array( 
		'clickId' => $row['keyade_referral_user_id'], 
		'eventMerchantId' => $row['_id'], 
		'count1' => "1",
		'time' => $row['created_date']->sec, 
		'eventStatus' => "confirmed"
	);
} 

echo json_encode($out);
?>
