<?xml version="1.0" encoding="utf-8"?>
<createCustomerProfileTransactionRequest xmlns="<?=$gateways['schema']['profile']; ?>">

<?=$this->view()->render('template', compact('config'), array('template' => 'authentication')); ?>

<transaction>
	<profileTransAuthOnly>
		<amount><?=$amount; ?></amount>
		<?=$this->view()->render('template', array(), array('template' => 'order')); ?>

		<customerProfileId><?=$customer->key; ?></customerProfileId>

		<?php if (isset($customer->payment)) { ?>
			<customerPaymentProfileId><?=$customer->payment->key; ?></customerPaymentProfileId>
		<?php } ?>
		<?php if (isset($customer->shipping)) { ?>
			<customerShippingAddressId><?=$customer->shipping->key; ?></customerShippingAddressId>
		<?php } ?>
	</profileTransAuthOnly>
</transaction>
</createCustomerProfileTransactionRequest>