<div class="fl">
<?php echo $this->form->create($orderPromo,array('id' => 'promo-form')); ?>
<?php if(empty($promocode_disable)): ?>
	
	    <div style='float:right !important; width:200px !important; margin-left: 10px; text-align:center'>
	    <?php if (is_array($this->form->error('promo'))): ?>
	        <?php foreach($this->form->error('promo') as $msg) :?>
	            <span style="margin-left:-105px"><?php echo $msg ?></span>
	        <?php endforeach; ?>
	    <?php else: ?>
	        <?php echo $this->form->error('promo');?>
	    <?php endif; ?>
	    </div>
	    <?php if($this->form->error('promo')) { ?>
	    	<script type="text/javascript">discountErrors.promo = true;</script>
	    <?php } ?>
	    <input type="text" name="code" id="promo_code" style='width:70px;' />
	    <span id='promobtn'>
	    	<?php echo $this->form->submit('Apply Promo Code'); ?>
	    </span>
<?php else: ?>
	<div style='float:right !important; margin: 10px !important; width:280px !important; text-align:center' class="error">
		You are already qualified for a discount. Only one promotional discount can be used per order.
	</div>
<?php endif; ?>
<?php echo $this->form->end(); ?>
</div>