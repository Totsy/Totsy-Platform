<?php
$out = array();
$out['report']['entry'] = array();
foreach( $cursor AS $row ){
	if (is_object($row)) {
		$row = $row->data();
	}
	$out['report']['entry'][] = array(
		'clickId' => $row['keyade_referral_user_id'],
		'eventMerchantId' => $row['_id'],
		'time' => $this->DataFormat->timeValue($row['created_date'])
	);
}

echo json_encode($out);
?>
