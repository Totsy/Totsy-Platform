<?php echo '<?xml version="1.0"?>'; ?>
<root>
	<entries total="<?php echo $cursor->count(); ?>">
	<?php foreach( $cursor AS $row ){ ?>
		<entry>
			<clickId><?php echo $row['keyade_referral_user_id']; ?></clickId>
			<eventMerchantId><?php echo $row['_id']; ?></eventMerchantId> 
			<time><?php echo $row['created_date']->sec; ?></time>
		</entry>
	<?php } ?>
	</entries>
</root>