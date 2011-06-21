<?php if ($credit): ?>
    <div style="padding:10px; background:#eee;"><?php $orderCredit->credit_amount = abs($orderCredit->credit_amount); ?>
        <?=$this->form->create($orderCredit); ?>
        <?=$this->form->error('amount'); ?>
        You have $<?=number_format((float) $userDoc->total_credit, 2);?> in credits
        <hr />
        <?=$this->form->text('credit_amount', array('size' => 6, 'maxlength' => '6')); ?>
                <?=$this->form->submit('Apply Credit', array('class' => 'button')); ?>
                <hr />
                    <strong>Credit:</strong>
            -$<?=number_format((float) $orderCredit->credit_amount, 2);?>
        <?=$this->form->end(); ?>
        <div style="clear:both"></div>
    </div>
<?php else : ?>
    <?php if ($credit = '0') { ?>
    <div style="padding:10px; background:#eee;"><h1 style="color:#707070; font-size:14px;">Credits: <span style="color:#009900; float:right;">$0.00</span></h1></div>
    <?php } ?>
<?php endif ?>