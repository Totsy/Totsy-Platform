<?xml version="1.0" encoding="utf-8"?>
<createCustomerPaymentProfileRequest xmlns="<?=$gateways['schema']['profile']; ?>">

<?=$this->view()->render('template', compact('config'), array('template' => 'authentication')); ?>

<customerProfileId><?=$customer->id; ?></customerProfileId>
<paymentProfile>
	<customerType><?=$customer->type; ?></customerType>
	<?php if ($customer->billing) { ?>
		<billTo>
		<?=$this->view()->render(
			'template', array('address' => $customer->billing), array('template' => 'address')
		); ?>
		</billTo>
	<?php } ?>
</paymentProfile>
</createCustomerPaymentProfileRequest>