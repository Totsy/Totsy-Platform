<?php use app\models\Address; ?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>

<?php $this->title("Add a Credit Card"); ?>
<?php if (!$isAjax): ?>
<div class="grid_16">
	<h2 class="page-title gray">Add a Credit Card</h2>
	<hr />
</div>
<div class="grid_4">
	<?php echo $this->view()->render(array('element' => 'myAccountNav')); ?>
	<?php echo $this->view()->render(array('element' => 'helpNav')); ?>
</div>
<?php endif ?>


<div class="grid_11 omega roundy grey_inside<?php if (!$isAjax): ?> b_side <?php endif ?>">

	<?php if ($message): ?>
		<div class="standard-message"><?=$message; ?></div>
	<?php endif ?>

	<h2 class="page-title gray">Add a Credit Card<span style="float:right; font-weight:normal; font-size:12px;"><?php if (!$isAjax): ?>
                <?=$this->html->link('Manage Credit Cards','creditcards');?><?php endif ?></span>
	</h2>
	<hr />
	<img src="/img/creditcards.jpg" width="180px">
	<br/>
		<?=$this->form->create($creditcard, array(
		'id' => 'addressForm',
		'class' => "fl",
		'action' => "{$action}/{$creditcard->_id}"
	)); ?>

				<div id="credit_card_form" >
				<span class="cart-select">
				<?=$this->form->hidden('opt_submitted', array('class'=>'inputbox', 'id' => 'opt_submitted')); ?>
				<?=$this->form->label('type', 'Card Type', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->select('type', array('visa' => 'Visa', 'mc' => 'MasterCard','amex' => 'American Express'), array('id' => 'type', 'class'=>'inputbox')); ?>
				</span>
				<div style="clear:both; padding-top:5px !important"></div>
				<?=$this->form->label('number', 'Card Number', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('number', array('class'=>'validate[required] inputbox','id' => 'number')); ?>
				<?=$this->form->hidden('valid', array('class'=>'inputbox', 'id' => 'valid')); ?>
				<?=$this->form->error('number'); ?>
				<div id="error_valid" style="display:none;">
					Wrong Credit Card Number
				</div>
				<div style="clear:both"></div>
				<span style="padding-left:2px">
				<?=$this->form->label('month', 'Expiration Date', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->select('month', array(
										'' => 'Month',
										1 => 'January',
										2 => 'February',
										3 => 'March',
										4 => 'April',
										5 => 'May',
										6 => 'June',
										7 => 'July',
										8 => 'August',
										9 => 'September',
										10 => 'October',
										11 => 'November',
										12 => 'December'
				), array('id'=>"month", 'class'=>'validate[required] inputbox')); ?>
				</span>
				<span style="padding-left:2px">
				<?php
					$now = intval(date('Y'));
					$years = array_combine(range($now, $now + 15), range($now, $now + 15)); ?>					
				<?=$this->form->select('year', array('' => 'Year') + $years, array('id' => "year", 'class'=>'validate[required inputbox')); ?>
				<div style="clear:both; padding-top:5px !important"></div>
				<?=$this->form->label('code', 'Security Code', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('code', array('id' => 'code','class'=>'validate[required] inputbox', 'maxlength' => '4', 'size' => '4')); ?>
				<?php 
				if(empty($checked)) {
					$checked = false; 
				}
				?>
				</span>
				</div>
				<br />
				<br />
				<div id="billing_address_form">
				<h3>Billing Address</h3>
				<hr />
				<?=$this->form->label('firstname', 'First Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('firstname', array('class' => 'validate[required] inputbox', 'id'=>'firstname')); ?>
				<?=$this->form->error('firstname'); ?>
				<div style="clear:both"></div>
				<?=$this->form->label('lastname', 'Last Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('lastname', array('class' => 'validate[required] inputbox', 'id'=>'lastname')); ?>
				<?=$this->form->error('lastname'); ?>
				<div style="clear:both"></div>
				<?=$this->form->label('telephone', 'Telephone <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('telephone', array('class' => 'validate[custom[phone]] inputbox', 'id' => 'telephone')); ?>
				<div style="clear:both"></div>
				<?=$this->form->label('address', 'Street Address <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('address', array('class' => 'validate[required] inputbox', 'id'=>'address')); ?>
				<?=$this->form->error('address'); ?>
				<div style="clear:both"></div>
				<?=$this->form->label('address2', 'Street Address 2', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('address2', array('class' => 'inputbox', 'id'=>'address2')); ?>
				<div style="clear:both"></div>
				<?=$this->form->label('city', 'City <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('city', array('class' => 'validate[required] inputbox', 'id'=>'city')); ?>
				<?=$this->form->error('city'); ?>
				<div style="clear:both"></div>
				<span style="padding-left:2px">
				<label for="state" class='required'>State <span>*</span></label>
				<?=$this->form->select('state', Address::$states, array('empty' => 'Select a state', 'id'=>'state','class' => 'validate[required] inputbox')); ?>
				<?=$this->form->error('state'); ?>
				</span>
				<div style="clear:both; padding-top:5px"></div>
				<?=$this->form->label('zip', 'Zip Code <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('zip', array('class' => 'validate[required] inputbox', 'id' => 'zip')); ?>
				<div style="clear:both"></div>

				<?=$this->form->hidden('opt_description', array('id' => 'opt_description' , 'value' => 'billing')); ?>
				<?=$this->form->hidden('opt_shipping_select', array('id' => 'opt_shipping_select')); ?>
				</div>
				</div>
					
			<div class="grid_16">	
				<?=$this->form->submit('CONTINUE', array('class' => 'button fr', 'style'=>'margin-right:10px;')); ?>
			</div>

		<?php if ($isAjax): ?>
			<?=$this->form->hidden('isAjax', array('value' => 1)); ?>
		<?php endif ?>
	<?=$this->form->end();?> 
	<br />

</div>
</div>
<div class="clear"></div>

<script type="text/javascript">
jQuery(function($){
   $("#date").mask("99/99/9999");
   $("#telephone").mask("(999) 999-9999");
   $("#tin").mask("99-9999999");
   $("#zip").mask("99999");
});
</script>
