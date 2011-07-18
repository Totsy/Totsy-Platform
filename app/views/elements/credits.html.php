<?php if ($credit): ?>
    	<?php
    	 if(!empty($orderCredit)) {
    	 	$orderCredit->credit_amount = abs($orderCredit->credit_amount); 
    	 }
    	?>
        <?=$this->form->create($orderCredit); ?>
        <?=$this->form->error('amount'); ?>
        <hr />
        You have $<?=number_format((float) $userDoc->total_credit, 2);?> in credits
        <br />
        <?=$this->form->text('credit_amount', array('size' => 6, 'maxlength' => '6')); ?>
                <?=$this->form->submit('Apply Credit'); ?>
                <hr />
                    <strong>Credit:</strong>
            -$
            <?php if(!empty($orderCredit)): ?>
            	<?=number_format((float) $orderCredit->credit_amount, 2);?>
        	<?php else : ?>
        		0
        	<?php endif ?>
        	<?=$this->form->end(); ?>
<?php else : ?>
<?php endif ?>