<?php if ($credit): ?>
    	<?php
	if(!empty($orderCredit)) {
		$orderCredit->credit_amount = abs($orderCredit->credit_amount);
	}
?>
<?php echo $this->form->create($orderCredit); ?>
	<hr />
	You have $<?php echo number_format((float) $userDoc->total_credit, 2);?> in credits
	<br /><input type="text" name="credit_amount" style='width:70px;' />
	<?php echo $this->form->submit('Apply Credit'); ?>
	<?php echo $this->form->error('amount'); ?>
<?php echo $this->form->end(); ?>
<?php endif ?>