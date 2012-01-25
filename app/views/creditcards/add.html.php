<script type="text/javascript">
var paymentForm = new Object();
</script>

<?php
	use app\models\Address;
	$this->html->script('application', array('inline' => false));
	$this->form->config(array('text' => array('class' => 'inputbox')));
?>

<link rel="stylesheet" type="text/css" href="/css/validation-engine.jquery.css" media="screen" />
<link rel="stylesheet" type="text/css" href="/css/validation-template.css" media="screen" />
<script type="text/javascript" src="/js/form_validator/jquery.validation-engine.js" charset="utf-8"></script>
<script type="text/javascript" src="/js/form_validator/languages/jquery.validation-engine-en.js" charset="utf-8"></script>

<script type="text/javascript">

    $(document).ready( function() {

        //if its not true, set it to false.
        //used to avoid overwriting the submitted
        //value on refresh, persiting whether a
        //form submit was made or not
    	if(!paymentForm.submitted) {
    		paymentForm.submitted=false;
    	} else {
    		$("#opt_submitted").val(paymentForm.submitted);
    	}

    	//detach the plugin from the form if it hasn't been submitted yet
    	if(paymentForm.submitted==false){
    		$("#paymentForm").validationEngine('detach');
    	}

    	//highlight the invalid fields and show a prompt for the first of those highlighted
    	$("#paymentForm").submit(function() {

    		if(validCC()==false) {
				return false;
			}

    		paymentForm.submitted = true;
    		paymentForm.form = $(this).serializeArray();

    		var invalid_count = 0;
    		var set_bubble= false;

    		$("#paymentForm").validationEngine('attach');
    		$("#paymentForm").validationEngine('init', { promptPosition : "centerRight", scroll: false } );

    		$.each(	paymentForm.form, function(i, field) {
    		    if(	field.value=="" &&
    		    	field.name!=="address2" &&
    		    	field.name!=="opt_submitted" &&
    		    	field.name!=="opt_shipping" &&
    		    	field.name!=="opt_shipping_select" &&
    		    	field.name!=="card_valid" &&
    		    	field.name!=="opt_save"
    		    	) {

    		 		if(set_bubble==false) {
    		 			$('#' + field.name + "").validationEngine('showPrompt','*This field is required', '', true);
    		 			$('#' + field.name + "").validationEngine({ promptPosition : "centerRight", scroll: false });
    		 			set_bubble=true;
     		 		}

    		 		$('#' + field.name + "").attr('style', 'background: #FFFFC5 !important');

    		 		invalid_count++;
    		 	}
			});

			if(invalid_count > 0 ) {
    		    return false;
    		}
    	});

    	//if the form has been, hide propmts on a given element's blur event
    	//controls only show a prompt when they have focus and aren't valid
    	$(".inputbox").blur(function() {
    		if(paymentForm.submitted==true) {
				$('#' + this.id + "").validationEngine('hide');
				//if they validate the field by filling it in, reset the background of the control to white
				if($('#' + this.id + "").val()!==""){
					$('#' + this.id + "").attr('style', 'background: #FFF !important');
				} else {
					$('#' + this.id + "").attr('style', 'background: #FFFFC5 !important');
				}
			}
    	});
    });

</script>

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
		<div class="standard-message"><?php echo $message; ?></div>
	<?php endif ?>

	<h2 class="page-title gray">Add a Credit Card<span style="float:right; font-weight:normal; font-size:12px;"><?php if (!$isAjax): ?>
                <?php echo $this->html->link('Manage Credit Cards','creditcards');?><?php endif ?></span>
	</h2>
	<hr />
	<img src="/img/creditcards.jpg" width="180px">
	<br/>
		<?php echo $this->form->create($creditcard, array(
		'id' => 'paymentForm',
		'class' => ""
	)); ?>

				<div id="credit_card_form" >
				<span class="cart-select">
				<?php echo $this->form->hidden('opt_submitted', array('class'=>'inputbox', 'id' => 'opt_submitted')); ?>				
				<?php echo $this->form->label('type', 'Card Type', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->select('type', array('visa' => 'Visa', 'mastercard' => 'MasterCard','amex' => 'American Express'), array('id' => 'type', 'class'=>'inputbox')); ?>
				</span>
				<div style="clear:both; padding-top:5px !important"></div>
				<?php echo $this->form->label('number', 'Card Number', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('number', array('class'=>'validate[required] inputbox','id' => 'number')); ?>
				<?php echo $this->form->hidden('valid', array('class'=>'inputbox', 'id' => 'valid')); ?>
				<?php echo $this->form->error('number'); ?>
				<div id="error_valid" style="display:none;">
					Wrong Credit Card Number
				</div>
				<div style="clear:both"></div>
				<span style="padding-left:2px">
				<?php echo $this->form->label('month', 'Expiration Date', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->select('month', array(
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
				<?php echo $this->form->select('year', array('' => 'Year') + $years, array('id' => "year", 'class'=>'validate[required inputbox')); ?>
				<div style="clear:both; padding-top:5px !important"></div>
				<?php echo $this->form->label('code', 'Security Code', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('code', array('id' => 'code','class'=>'validate[required] inputbox', 'maxlength' => '4', 'size' => '4')); ?>
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
				<?php echo $this->form->label('firstname', 'First Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('firstname', array('class' => 'validate[required] inputbox', 'id'=>'firstname')); ?>
				<?php echo $this->form->error('firstname'); ?>
				<div style="clear:both"></div>
				<?php echo $this->form->label('lastname', 'Last Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('lastname', array('class' => 'validate[required] inputbox', 'id'=>'lastname')); ?>
				<?php echo $this->form->error('lastname'); ?>
				<div style="clear:both"></div>
				<?php echo $this->form->label('telephone', 'Telephone <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('telephone', array('class' => 'validate[custom[phone]] inputbox', 'id' => 'telephone')); ?>
				<div style="clear:both"></div>
				<?php echo $this->form->label('address', 'Street Address <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('address', array('class' => 'validate[required] inputbox', 'id'=>'address')); ?>
				<?php echo $this->form->error('address'); ?>
				<div style="clear:both"></div>
				<?php echo $this->form->label('address2', 'Street Address 2', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('address2', array('class' => 'inputbox', 'id'=>'address2')); ?>
				<div style="clear:both"></div>
				<?php echo $this->form->label('city', 'City <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('city', array('class' => 'validate[required] inputbox', 'id'=>'city')); ?>
				<?php echo $this->form->error('city'); ?>
				<div style="clear:both"></div>
				<span style="padding-left:2px">
				<label for="state" class='required'>State <span>*</span></label>
				<?php echo $this->form->select('state', Address::$states, array('empty' => 'Select a state', 'id'=>'state','class' => 'validate[required] inputbox')); ?>
				<?php echo $this->form->error('state'); ?>
				</span>
				<div style="clear:both; padding-top:5px"></div>
				<?php echo $this->form->label('zip', 'Zip Code <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('zip', array('class' => 'validate[required] inputbox', 'id' => 'zip')); ?>
				<div style="clear:both"></div>

				</div>
				</div>
				
				<?php echo $this->form->hidden('opt_description', array('id' => 'opt_description' , 'value' => 'billing')); ?>
				<?php echo $this->form->hidden('opt_shipping_select', array('id' => 'opt_shipping_select')); ?>
				
					
			<div class="grid_16">	
				<?php echo $this->form->submit('CONTINUE', array('class' => 'button fr', 'style'=>'margin-right:10px;')); ?>
			</div>

		<?php if ($isAjax): ?>
			<?php echo $this->form->hidden('isAjax', array('value' => 1)); ?>
		<?php endif ?>
	<?php echo $this->form->end();?> 
	<br />

</div>
</div>
<div class="clear"></div>

<script>  
	
//validate card number when a correct card is entered
$("#number").blur( function(){
	validCC();
});	
	
function isValidCard(cardNumber) {

	var ccard = new Array(cardNumber.length);
	var i = 0;
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
		
	if($('#type').val()=="amex") {
		if(cardNumber.length >= 15){
			return true;
		} else {
			return false;
		}
	} else {
		return ((sum%10) == 0);
	}
  }


function validCC() {
	var test = isValidCard($("#number").val());
	$("#valid").val(test);
	
	if(!test){
		$("#number").validationEngine('showPrompt','*This is not a valid credit card number', '', true);
		$("#number").attr('style', 'background: #FFFFC5 !important');
		return false;	
	} else {
		$("#number").attr('style', 'background: #FFFFFF !important');
		$("#number").validationEngine('hide');	
		return true;
	}
}

</script>
<script>
$(document).ready(function(){ 
	$("#addresses").change(function () {
		$("#address_id").val($("#addresses option:selected").val());
		$("#selectForm").submit();
	});
});
</script>