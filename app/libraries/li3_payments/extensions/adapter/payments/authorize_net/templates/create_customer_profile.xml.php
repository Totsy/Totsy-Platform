<?xml version="1.0" encoding="utf-8"?>
<createCustomerProfileRequest xmlns="<?=$gateways['schema']['profile']; ?>">

<?=$this->view()->render('template', compact('config'), array('template' => 'authentication')); ?>

<?php //if (isset($object->reference)) { ?>
	<?php // <refId>< ?=$object->reference; </refId> ?>
<?php //} ?>

<profile>
	<merchantCustomerId><?=$customer->id; ?></merchantCustomerId>
	<?php if ($email = $customer->email) { ?>
	<email><?=$email; ?></email>
	<?php } ?>
	<paymentProfiles>
		<customerType><?=$customer->type; ?></customerType>
		<?php if ($customer->billing) { ?>
			<billTo>
			<?=$this->view()->render(
				'template', array('address' => $customer->billing), array('template' => 'address')
			); ?>
			</billTo>
		<?php } ?>
		<?php
			if (!is_array($customer->payment)) {
				$customer->payment = array($customer->payment);
			}
			foreach ($customer->payment as $payment) {
				echo $this->view()->render(
					'template', compact('payment'), array('template' => 'payment')
				);
			}
		?>
	</paymentProfiles>
</profile>
<validationMode><?=$config['debug'] ? 'testMode' : 'liveMode'; ?></validationMode>
</createCustomerProfileRequest>
