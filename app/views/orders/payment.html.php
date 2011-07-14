<?php
	use app\models\Address;
	$this->html->script('application', array('inline' => false));
	$this->form->config(array('text' => array('class' => 'inputbox')));
	$countLayout = "layout: '{mnn}{sep}{snn} minutes'";
?>
<div class="grid_16">
	<h2 class="page-title gray">Payment Information</h2>
	<hr />
	<?php if (!empty($error)) { ?>
		<div class="checkout-error"><h2>Uh Oh! Please fix the errors below:</h2><hr /><?=$error; ?></div>
	<?php } ?>
</div>

<div class="grid_10">
<?=$this->form->create($address, array(
		'id' => 'paymentForm',
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
				<?=$this->form->text('card[number]', array('class' => 'inputbox','id' => 'card[number]', 'onblur' => 'validCC()')); ?>
				<?=$this->form->hidden('card[valid]', array('class' => 'inputbox', 'id' => 'card[valid]')); ?>
				<?=$this->form->error('card[number]'); ?>
				<div id='error_valid' style="display:none;">
					Wrong Credit Card Number
				</div>
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
				<?php 
				if(empty($checked)) {
					$checked = false;
				}
				?>
				<h3>Billing Address</h3>
				<hr />
				Use my shipping address as my billing address: <?=$this->form->checkbox("shipping", array('onclick' => 'replace_address()' , "checked" => $checked, "value" => "1")) ?>
				<br />
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
			<?=$this->form->submit('CONTINUE', array('class' => 'button fr')); ?>

<?=$this->form->end();?> 
</div>
<script>
function isValidCard(cardNumber){
	var ccard = new Array(cardNumber.length);
	var i     = 0;
        var sum   = 0;

	// 6 digit is issuer identifier
	// 1 last digit is check digit
	// most card number > 11 digit
	if(cardNumber.length < 11){
		return false;
	}
	// Init Array with Credit Card Number
	for(i = 0; i < cardNumber.length; i++){
		ccard[i] = parseInt(cardNumber.charAt(i));
	}
	// Run step 1-5 above above
	for(i = 0; i < cardNumber.length; i = i+2){
		ccard[i] = ccard[i] * 2;
		if(ccard[i] > 9){
			ccard[i] = ccard[i] - 9;
		}
	}
	for(i = 0; i < cardNumber.length; i++){
		sum = sum + ccard[i];
	}
	return ((sum%10) == 0);
  }

function validCC() {
	var test = isValidCard($("input[name='card[number]']").val());
	$("input[name='card[valid]']").val(test);
	if(!test) {
		$('#error_valid').show();
	} else {
		$('#error_valid').hide()();
	}
}

function replace_address() {
  $("#paymentForm").submit();
};
</script>