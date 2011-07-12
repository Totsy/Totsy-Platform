<?php
	use app\models\Address;
	$this->html->script('application', array('inline' => false));
	$this->form->config(array('text' => array('class' => 'inputbox')));
	$countLayout = "layout: '{mnn}{sep}{snn} minutes'";
?>
<?=$this->html->script('creditcard')?>
<div class="grid_16">
	<h2 class="page-title gray">Payment Information</h2>
	<hr />
	<?php if (!empty($error)) { ?>
		<div class="checkout-error"><h2>Uh Oh! Please fix the errors below:</h2><hr /><?=$error; ?></div>
	<?php } ?>
</div>

<div class="grid_10">
<?=$this->form->create(null, array(
		'id' => 'paymentBillingform',
		'class' => "fl"
	)); ?>
			
				<h3>Pay with Credit Card :</h3>
				<hr />
				<?=$this->form->label('card[type]', 'Card Type', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->select('card[type]', array('visa' => 'Visa', 'mc' => 'MasterCard','amex' => 'American Express'), array('id' => 'card_type')); ?>
				<br />
				<?=$this->form->label('card[name]', 'Name On Card', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('card[name]', array('class' => 'inputbox')); ?>
				<?=$this->form->error('card[name]'); ?>
				<br />
				<?=$this->form->label('card[number]', 'Card Number', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('card[number]', array('class' => 'inputbox','id' => 'card[number]')); ?>
				<?=$this->form->error('card[number]'); ?>
				<br />
				<?=$this->form->label('card[number]', 'Expiration Date', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->select('card[month]', array(
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
				), array('id'=>"card_month")); ?>
				<?php
					$now = intval(date('Y'));
					$years = array_combine(range($now, $now + 15), range($now, $now + 15)); ?>					
				<?=$this->form->select('card[year]', array('' => 'Year') + $years, array('id' => "card_exp[year]")); ?>
				<br />
				<?=$this->form->label('card[code]', 'Security Code', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('card[code]', array('id' => 'CVV2','class' => 'inputbox', 'maxlength' => '4', 'size' => '4')); ?>
	
				<h3>Billing Address</h3>
				<hr />
				<?=$this->form->label('firstname', 'First Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('firstname', array('class' => 'inputbox')); ?>
				<?=$this->form->error('firstname'); ?>
				<br />
				<?=$this->form->label('lastname', 'Last Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('lastname', array('class' => 'inputbox')); ?>
				<?=$this->form->error('lastname'); ?>
				<br />
				<?=$this->form->label('telephone', 'Telephone', array('escape' => false,'class' => 'addresses')); ?>
				<?=$this->form->text('telephone', array('class' => 'inputbox', 'id' => 'phone')); ?>
				<br />
				<?=$this->form->label('address', 'Street Address <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('address', array('class' => 'inputbox')); ?>
				<?=$this->form->error('address'); ?>
				<br />
				<?=$this->form->label('address_2', 'Street Address 2', array('escape' => false,'class' => 'addresses')); ?>
				<?=$this->form->text('address_2', array('class' => 'inputbox')); ?>
				<br />
				<?=$this->form->label('city', 'City <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('city', array('class' => 'inputbox')); ?>
				<?=$this->form->error('city'); ?>
				<br />
				<label for="state" class='required'>State <span>*</span></label>
				<?=$this->form->select('state', Address::$states, array('empty' => 'Select a state')); ?>
				<?=$this->form->error('state'); ?>
				<br />
				<?=$this->form->label('zip', 'Zip Code <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('zip', array('class' => 'inputbox', 'id' => 'zip')); ?>
				<br />
			<?=$this->form->submit('Shipping Information', array('class' => 'button fr')); ?>

<?=$this->form->end();?> 
</div>
$(function() {$("#myform").validate({rules: {cardnum: {creditcard2: function(){ return $('#cardType').val(); }}}		});