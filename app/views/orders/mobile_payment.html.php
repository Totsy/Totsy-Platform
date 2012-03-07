<script type="text/javascript">
		function fadeOut_CCForm() {
			$('#credit_card_form').hide();
		}
		
		function fadeOut_BillingAddressForm() {
			$('#billing_address_form').hide();
		}
		
		function fadeOut_saved_CCs() {
			$('#saved_credit_cards').hide();
		}
		
		function fadeIn_saved_CCs() {
			$('#saved_credit_cards').show();
		}
		
		function fadeIn_CCForm() {
			$('input[name=savedCreditCard]').attr('checked', false);
			$('#credit_card_form').show();
		}
		
		function fadeIn_BillingAddressForm() {
			$('#billing_address_form').show();
		}

	$( function () {

	    var itemExpires = new Date(<?php echo ($cartExpirationDate  * 1000)?>);
		var now = new Date();
		$('#itemCounter').countdown( {until: itemExpires, onExpiry: refreshCart, expiryText: "<div class='over' style='color:#EB132C; padding:5px;'>no longer reserved</div>", layout: '{mnn}{sep}{snn} minutes'} );
		if (itemExpires < now) {
			$('#itemCounter').html("<span class='over' style='color:#EB132C; padding:5px;'>no longer reserved</span>");
		}
		function refreshCart() {
			window.location.reload(true);
		}
	});
	
</script>

<script type="text/javascript">
var paymentForm = new Object();
</script>
<?php
	use app\models\Address;
	$this->html->script('application', array('inline' => false));
	$this->form->config(array('text' => array('class' => 'inputbox')));
	$countLayout = "layout: '{mnn}{sep}{snn} minutes'";
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

			var savedCreditCard = $('input:radio[name=savedCreditCard]:checked').val();

    		if(validCC()==false && savedCreditCard === undefined) {
				return false;
			}
			
    		paymentForm.submitted = true;
    		paymentForm.form = $(this).serializeArray();

    		var invalid_count = 0;
    		var set_bubble= false;

    		$("#paymentForm").validationEngine('attach');
    		$("#paymentForm").validationEngine('init', { promptPosition : "centerRight", scroll: false } );

    		$.each(	paymentForm.form, function(i, field) {
    		    if( savedCreditCard === undefined && field.value=="" &&
    		    	field.name!=="address_2" &&
    		    	field.name!=="opt_submitted" &&
    		    	field.name!=="opt_shipping" &&
    		    	field.name!=="opt_shipping_select" &&
    		    	field.name!=="card_valid" &&
    		    	field.name!=="paymentInfosSave"
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
<?php  if(empty($cartEmpty)): ?>
<h2 class="page-title gray">
		<span class="cart-step-status gray" style="font-weight:bold">Payment</span>
	<div style="float:right;">
		<span class="cart-step-status"><img src="/img/cart_steps_completed.png"></span>
		<span class="cart-step-status"><img src="/img/cart_steps_completed.png"></span>
		<span class="cart-step-status"><img src="/img/cart_steps3.png"></span>
		<span class="cart-step-status"><img src="/img/cart_steps_remaining.png"></span>
	</div>
</h2>
<?php if (!empty($error)) { ?>
	<div class="checkout-error"><h2>Uh Oh! Please fix the errors below:</h2><hr /></div>
<?php } ?>
<hr />
<div class="grid_5 cart-header-right">
		<?php echo $this->view()->render( array('element' => 'mobile_shipdateTimer'), array( 'shipDate' => $shipDate) ); ?>
</div>

<?php echo $this->form->create($payment, array ('id' => 'paymentForm')); ?>

<div class="clear"></div>

<div class="grid_16">
<?php
 
if ($cyberSourceProfiles) { 
	if (sizeof($cyberSourceProfiles->data()) > 0) { 
?>
<h3 style="margin-bottom: 11px;">Pay with a saved Credit Card: </h3>
<hr />
<?php
	$i = 0;
	foreach ($cyberSourceProfiles as $cyberSourceProfile):
		if($cyberSourceProfile[savedByUser]) :
?>
<style>
.ui-radio {margin:0 0 0 -17px;}
</style>
<div id="saved_credit_cards">
<div data-role="fieldcontain_">
         	<input type="radio" name="savedCreditCard" id="savedCreditCard" value="<?php print $cyberSourceProfile[profileID];?>" checked="checked"  onclick="fadeOut_BillingAddressForm(); fadeOut_CCForm();" <?php if ($i == 0) print 'checked'; ?> style="margin:5px 0px 0px 0px; float:left; display:block; width:20px; height:20px; padding:0px 40px 0px 0px;"/>
         	<label for="savedCreditCard_" style="width:200px; font-size:12px; display:block; float:left; margin:0px 0px 0px 40px;">
	 <?php echo ucfirst($cyberSourceProfile[creditCard][type]);?> ending in <?php echo $cyberSourceProfile[creditCard][number]; ?> 
	 <?php echo $cyberSourceProfile[billing][firstname]." ".$cyberSourceProfile[billing][lastname];?>
	 <br />
	 Exp on <?php echo $cyberSourceProfile[creditCard][month];?> / <?php echo $cyberSourceProfile[creditCard][year];?></label>
	 <img src="<?php
switch ($cyberSourceProfile[creditCard][type]) {
	case 'visa': 
		print "/img/cc_visa.gif"; 
	break;
	case 'mastercard': 
		print "/img/cc_mastercard.gif"; 
	break;
	case 'mc': 
		print "/img/cc_mastercard.gif"; 
	break;
	case 'amex': 
		print "/img/cc_amex.gif"; 
	break;
}	?>" style="display:block; margin:0px 4px 0px 0px; float:left;">
	 <div style="clear:both;"></div>
</div>
</div>
<?php
	$i++;
	endif;
endforeach;
?>
<h3 style="margin-top: 11px"><a href="#" onclick="fadeIn_saved_CCs(); fadeIn_CCForm(); fadeIn_BillingAddressForm();" style="text-decoration:underline;">Add New Card</a></h3>
<?php 
}
} else {
?>
<h3>Pay with a Credit Card</h3>
<?php
}
?>

<hr />
				
				<div id="credit_card_form" style="display: <?php if ($cyberSourceProfiles) { if (sizeof($cyberSourceProfiles->data()) > 0) { print 'none'; } } else { print 'block'; } ?>;">				
				<span class="cart-select">
				<?php echo $this->form->error('cc_error'); ?>
				<?php echo $this->form->hidden('opt_submitted', array('class'=>'inputbox', 'id' => 'opt_submitted')); ?>
				<?php echo $this->form->label('card_type', 'Card Type', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->select('card_type', array('visa' => 'Visa', 'mc' => 'MasterCard','amex' => 'American Express'), array('id' => 'card_type', 'class'=>'inputbox', 'data-placeholder' => 'true', 'data-native-menu' => 'false')); ?>
				</span>
				<div style="clear:both; padding-top:5px !important"></div>
				<?php echo $this->form->label('card_number', 'Card Number', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('card_number', array('class'=>'validate[required] inputbox','id' => 'card_number')); ?>
				<?php echo $this->form->hidden('card_valid', array('class'=>'inputbox', 'id' => 'card_valid')); ?>
				<?php echo $this->form->error('card_number'); ?>
				<div id="error_valid" style="display:none;">
					Wrong Credit Card Number
				</div>
				<div style="clear:both"></div>
				<span style="padding-left:2px">
				<?php echo $this->form->label('card_month', 'Expiration Date', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->select('card_month', array(
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
				), array('id'=>"card_month", 'class'=>'validate[required] inputbox', 'data-placeholder' => 'true', 'data-native-menu' => 'false')); ?>
				</span>
				<span style="padding-left:2px">
				<?php
					$now = intval(date('Y'));
					$years = array_combine(range($now, $now + 15), range($now, $now + 15)); ?>
				<?php echo $this->form->select('card_year', array('' => 'Year') + $years, array('id' => "card_year", 'class'=>'validate[required inputbox', 'data-placeholder' => 'true', 'data-native-menu' => 'false')); ?>
				<div style="clear:both; padding-top:5px !important"></div>
				<?php echo $this->form->label('card_code', 'Security Code', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('card_code', array('id' => 'card_code','class'=>'validate[required] inputbox', 'maxlength' => '4', 'size' => '4')); ?>
				<?php
				if(empty($checked)) {
					$checked = false;
				}
				?>
				</span>
				</div>
				<div id="billing_address_form" style="display: <?php if ($cyberSourceProfiles) { if (sizeof($cyberSourceProfiles->data()) > 0) { print 'none'; } } else { print 'block'; } ?>;">
				<?php if(!empty($addresses_ddwn) && (count($addresses_ddwn) > 1)) : ?>
					Choose your address :<?php echo $this->form->select('addresses', $addresses_ddwn, array("id" => 'addresses', 'value' => $selected));?>
					<div style="clear:both"></div>
				<hr />
				<?php endif ?>
				<h3>Billing Address <br /><span style="font-size:12px;"><?php echo $this->form->checkbox("opt_shipping", array('id' => 'opt_shipping', 'onclick' => 'replace_address();', 'class' => 'custom' , 'data-role' => 'none', "checked" => $checked)) ?>
				<label for="opt_shipping"> My Shipping is the same as Billing</label></span></h3>
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
				<?php echo $this->form->label('address_2', 'Street Address 2', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('address_2', array('class' => 'inputbox', 'id'=>'address_2')); ?>
				<div style="clear:both"></div>
				<?php echo $this->form->label('city', 'City <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('city', array('class' => 'validate[required] inputbox', 'id'=>'city')); ?>
				<?php echo $this->form->error('city'); ?>
				<div style="clear:both"></div>
				<span style="padding-left:2px">
				<label for="state" class='required'>State <span>*</span></label>
				<?php echo $this->form->select('state', Address::$states, array('empty' => 'Select a state', 'id'=>'state','class' => 'validate[required] inputbox', 'data-placeholder' => 'true', 'data-native-menu' => 'false')); ?>
				<?php echo $this->form->error('state'); ?>
				</span>
				<div style="clear:both; padding-top:5px"></div>
				<?php echo $this->form->label('zip', 'Zip Code <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('zip', array('class' => 'validate[required] inputbox', 'id' => 'zip')); ?>
				<div style="clear:both"></div>
				<h3><span style="font-size:12px;"><?php echo $this->form->checkbox("paymentInfosSave", array('id' => 'paymentInfosSave', 'checked' => false, 'data-role' => 'none')); ?>
				<label for="opt_shipping"> Save this credit card and billing address</label></span></h3>
				<hr />
				
				<?php echo $this->form->hidden('opt_description', array('id' => 'opt_description' , 'value' => 'billing')); ?>
				<?php echo $this->form->hidden('opt_shipping_select', array('id' => 'opt_shipping_select')); ?>
				</div>

			<div class="grid_16">
				<?php echo $this->form->submit('CONTINUE', array('class' => 'button fr', 'style'=>'margin-right:10px;')); ?>
			</div>

</div>
<?php else: ?>
	<div class="grid_16 cart-empty">
		<h1>
			<span class="page-title gray" style="padding:0px 0px 10px 0px;">Your shopping cart is empty</span>
			<a href="/sales" title="Continue Shopping">Continue Shopping</a/></h1>
	</div>
	
<?php endif ?>
</div>
<?php echo $this->form->end();?>

<div class="clear"></div>



<div id="address_form" style="display:none">
	<?php echo $this->form->create(null ,array('id'=>'selectForm')); ?>
	<?php echo $this->form->hidden('address_id', array('class' => 'inputbox', 'id' => 'address_id')); ?>
	<?php echo $this->form->end();?>
</div>
<script>

var shippingAddress = <?php echo $shipping; ?>

//validate card number when a correct card is entered
$("#card_number").blur( function(){
	validCC();
});

function replace_address() {
    if($("#opt_shipping").is(":checked")) {
    	//run through shippinAddress object and set values for corresponding fields
    	$.each ( shippingAddress, function(k, v) {
    		$("#" + k + "").val(v);
    		if(k == 'state') {
    			$("#" + k + 'option:selected').next('option').attr('selected', 'selected');
  				$("#" + k + "").change();
    		}

    		if(paymentForm.opt_submitted==true && v!=="") {
    			$('#' + k + "").attr("style", "background: #FFF !important");
    		}
    	});
    } else {
    	$.each ( shippingAddress, function(k, v) {
    		$("#" + k + "").val("");

    			if(paymentForm.opt_submitted==true) {
    				$('#' + k + "").attr("style", "background: #FFFFC5 !important");
    			}
    		}
    	);
    }
};

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

	if($('#card_type').val()=="amex") {
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
	var test = isValidCard($("#card_number").val());
	$("#card_valid").val(test);

	if(!test){
		$("#card_number").validationEngine('showPrompt','*This is not a valid credit card number', '', true);
		$("#card_number").attr('style', 'background: #FFFFC5 !important');
		return false;
	} else {
		$("#card_number").attr('style', 'background: #FFFFFF !important');
		$("#card_number").validationEngine('hide');
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