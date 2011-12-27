<?php echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>'; ?>
<!DOCTYPE report PUBLIC "report" "https://dtool.keyade.com/dtd/conversions_v5.dtd">
<report>
<?php foreach( $cursor AS $row ){ 
	if (is_object($row)) $row = $row->data();	?>
	<entry <?php 
		?>clickId="<?php echo $row['keyade_referral_user_id'] ?>" <?php 
		?>eventMerchantId="<?php echo $row['_id']; ?>" <?php 
		?>count1="1" <?php 
		?>time="<?php echo $row['created_date']->sec ?>" <?php 
		?>eventStatus="confirmed" />
<?php } ?>
</report>