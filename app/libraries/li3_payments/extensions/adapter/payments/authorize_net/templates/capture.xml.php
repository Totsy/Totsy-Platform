<?xml version="1.0" encoding="utf-8"?>
<createCustomerProfileTransactionRequest xmlns="<?=$gateways['schema']['profile']; ?>">

<?=$this->view()->render('template', compact('config'), array('template' => 'authentication')); ?>

<transaction>
	<profileTransCaptureOnly>
		<amount><?=$amount; ?></amount>
		
	</profileTransCaptureOnly>
</transaction>
</createCustomerProfileTransactionRequest>