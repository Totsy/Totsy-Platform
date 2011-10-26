<?php 
$out = array();
$out['report']['entry'] = array();
foreach( $cursor AS $row ){ 
	$out['report']['entry'][] = array( 
		'clickId' => $row['keyade_user_id'], 
		'eventMerchantId' => $row['_id'], 
		'time' => $row['created_date']->sec
	);
} 

echo json_encode($out);
?>
