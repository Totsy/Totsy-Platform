<?php
$out = array();
$out['report']['entry'] = array();
foreach( $cursor AS $row ){
	$out['report']['entry'][] = array(
		'clickId' => $row['keyade_user_id'],
		'eventMerchantId' => $row['_id'],
		'time' => $this->DataFormat->timeValue($row['created_date'])
	);
}

echo json_encode($out);
?>
