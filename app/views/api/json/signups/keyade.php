<?php 
$out = array();
$out['report']['entry'] = array();
foreach( $cursor AS $row ){ 
	$out['report']['entry'][] = array( 
		'clickId' => $row['keyade_user_id'], 
		'eventMerchantId' => $row['_id'], 
		'count1' => "1",
		'time' => $row['created_date']->sec, 
		'eventStatus' => "confirmed"
	);
} 

echo json_encode($out);
?>
