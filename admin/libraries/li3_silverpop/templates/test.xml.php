<?php

$campaignId = '776348';
$subject = 'Totsy - Welcome to Totsy!!!'
?>

<XTMAILING>
<CAMPAIGN_ID><?=$campaignId?></CAMPAIGN_ID>
	<RECIPIENT>
		<EMAIL><?=$data['email']?></EMAIL>
		<BODY_TYPE>HTML</BODY_TYPE>
		<PERSONALIZATION>
			<TAG_NAME>SUBJECT_LINE</TAG_NAME>
			<VALUE><?=$subject?></VALUE>
		</PERSONALIZATION>
		<PERSONALIZATION>
			<TAG_NAME>ORDER_NUMBER</TAG_NAME>
			<VALUE><?=$data['order_id']?></VALUE>
			</PERSONALIZATION>
		<PERSONALIZATION>
			<TAG_NAME>BRAND_NAME</TAG_NAME>
			<VALUE><?=$data['brand_name']?></VALUE>
		</PERSONALIZATION>
	</RECIPIENT>
</XTMAILING>

