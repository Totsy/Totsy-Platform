<?php echo '<?xml version="1.0" encoding="utf-8" standalone="yes"?>'; ?>
<!DOCTYPE report PUBLIC "report" "https://dtool.keyade.com/dtd/conversions_v5.dtd">
<report>
<?php foreach( $cursor AS $row ) { ?>
	<entry <?php
		?>clickId="<?php echo $row['keyade_user_id'] ?>" <?php
		?>eventMerchantId="<?php echo $row['_id']; ?>" <?php
		?>count1="1" <?php
		?>time="<?php echo $this->DataFormat->timeValue($row['created_date']); ?>" <?php
		?>eventStatus="confirmed" />
<?php } ?>
</report>
