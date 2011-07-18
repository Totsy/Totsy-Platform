<?php if ($credit): ?>
    	<?php
	if(!empty($orderCredit)) {
		$orderCredit->credit_amount = abs($orderCredit->credit_amount);
	}
?>
        <?php echo $this->form->create($orderCredit); ?>
        <?php echo $this->form->error('amount'); ?>
        <hr />
        You have $<?php echo number_format((float) $userDoc->total_credit, 2);?> in credits
        <br />
        <?php echo $this->form->text('credit_amount', array('size' => 6, 'maxlength' => '6')); ?>
                <?php echo $this->form->submit('Apply Credit'); ?>
                <hr />
                    <strong>Credit:</strong>
            -$
            <?php if(!empty($orderCredit)): ?>
            	<?php echo number_format((float) $orderCredit->credit_amount, 2);?>
        	<?php else : ?>
        		0
        	<?php endif ?>
        	<?php echo $this->form->end(); ?>
<?php else : ?>
<?php endif ?>