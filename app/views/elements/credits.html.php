<div class="fl">
<?php if ($credit): ?>
<?php if(!empty($orderCredit)) {
		$orderCredit->credit_amount = abs($orderCredit->credit_amount);
	}
?>
<?php echo $this->form->create($orderCredit); ?>
	You have $<?php echo number_format((float) $user['total_credit'], 2);?> in credits
	<br /><input type="text" name="credit_amount" style='width:70px;' />
	<?php echo $this->form->submit('Apply Credit'); ?>
	 <div style='float:right !important; margin-left: 5px ; text-align:center; width:200px !important; height:auto !important'>
	<?php echo $this->form->error('amount'); ?>
	</div>
	<?php if($this->form->error('amount')) { ?>
    	<script type="text/javascript">discountErrors.credits = true;</script>
    <?php } ?>
<?php echo $this->form->end(); ?>
<?php endif ?>
</div>