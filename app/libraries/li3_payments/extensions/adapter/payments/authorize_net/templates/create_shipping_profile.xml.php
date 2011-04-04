<?xml version="1.0" encoding="utf-8"?>
<createCustomerShippingAddressRequest xmlns="<?=$gateways['schema']['profile']; ?>">

<?=$this->view()->render('template', compact('config'), array('template' => 'authentication')); ?>

<customerProfileId><?=$customer->id; ?></customerProfileId>
<address>
	<?=$this->view()->render(
		'template', array('address' => $customer->address), array('template' => 'address')
	); ?>
</address>

</createCustomerShippingAddressRequest>