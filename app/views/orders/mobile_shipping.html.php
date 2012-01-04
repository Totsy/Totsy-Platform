<script type="text/javascript">	
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
var addressForm = new Object();
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

    $(document).ready(function() {
        $("#addressForm").validationEngine('attach');        
		$("#addressForm").validationEngine({ promptPosition : "centerRight", scroll: false });
    	$("#addressForm").validationEngine('init', { promptPosition : "centerRight", scroll: false });   
    	
    	$("#addressForm").submit( function() {
    	
    		addressForm.submitted = true;
    		addressForm.form = $(this).serializeArray(); 
    		
    		var invalid_count = 0;
    		var set_bubble = false;
    		
    		$("#addressForm").validationEngine('attach');        
    		$("#addressForm").validationEngine('init', { promptPosition : "centerRight", scroll: false } );      		
    		    		    		    		
    		$.each(	addressForm.form, function(i, field) {	
    		    if(	field.value=="" &&  
    		    	field.name!=="address_2" && 
    		    	field.name!=="submitted" &&
    		    	field.name!=="opt_save"
					) {
    		    	
    		    	//the bubble will only be set for the first one in the set
    		    	if(set_bubble==false){    		 		
    		 		$('#' + field.name + "").validationEngine('showPrompt','*This field is required', '', true);
    		 		$('#' + field.name + "").validationEngine({ promptPosition :"centerRight", scroll: false });
    		 		set_bubble = true;
    		 		}
    		 		    		 		
    		 		$('#' + field.name + "").attr('style', 'background: #FFFFC5 !important');
    		 		
    		 		invalid_count++;
    		 	} 
			});
						
			if(invalid_count > 0 ) {
    		    return false;
    		}		
    	});
    	
    	$(".inputbox").blur( function() { 
    	    
			$('#' + this.id + "").validationEngine('hide');	
			//if they validate the field by filling it in, reset the background of the control to white again
			if($('#' + this.id + "").val()!="" || this.id=="address_2") { 
			     $('#' + this.id + "").attr('style', 'background: #FFF !important');
			} else {
			    $('#' + this.id + "").attr('style', 'background: #FFFFC5 !important');
			}
    	});
    	    
    });
   
</script>
<?php  if(empty($cartEmpty)): ?>

	
<h2 class="page-title gray">
		<span class="cart-step-status gray" style="font-weight:bold">Shipping Information</span>
	<div style="float:right;">
		<span class="cart-step-status"><img src="/img/cart_steps_completed.png"></span>
		<span class="cart-step-status"><img src="/img/cart_steps2.png"></span>
		<span class="cart-step-status"><img src="/img/cart_steps_remaining.png"></span>
		<span class="cart-step-status"><img src="/img/cart_steps_remaining.png"></span>
	</div>
</h2>
<?php if (!empty($error)) { ?>
	<div class="checkout-error"><h2>Uh Oh! Please fix the errors below:</h2></div>
<?php } ?>
<hr />

<?php echo $this->view()->render( array('element' => 'mobile_shipdateTimer'), array( 'shipDate' => $shipDate) ); ?>
	
<?php echo $this->form->create($address, array('id' => 'addressForm')); ?>	
			
		<?php if(!empty($addresses_ddwn) && (count($addresses_ddwn) > 1)) : ?>
		<div data-role="fieldcontain">
		<label for="select-choice-15" class="select">Choose your address</label>
		<?php echo $this->form->select('addresses', $addresses_ddwn, array('data-overlay-them' => 'd','data-native-menu' => 'false', 'id' => 'addresses', 'value' => $selected));?>
		</div>	

			
		<?php endif ?>
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
		<?php echo $this->form->text('telephone', array('class' => 'validate[required] inputbox', 'id' => 'telephone')); ?>
		<div style="clear:both"></div>
		<?php echo $this->form->label('address', 'Street Address <span>*</span>', array('escape' => false,'class' => 'required')); ?>
		<?php echo $this->form->text('address', array('class' => 'validate[required] inputbox', 'id'=>'address' )); ?>
		<?php echo $this->form->error('address'); ?>
		<div style="clear:both"></div>
		<?php echo $this->form->label('address_2', 'Street Address 2', array('escape' => false,'class' => 'required')); ?>
		<?php echo $this->form->text('address_2', array('class' => 'inputbox', 'id'=>'address_2')); ?>
		<div style="clear:both"></div>
		<?php echo $this->form->label('city', 'City <span>*</span>', array('escape' => false,'class' => 'required')); ?>
		<?php echo $this->form->text('city', array('class' => 'validate[required] inputbox', 'id'=>'city')); ?>
		<?php echo $this->form->error('city'); ?>
		<div style="clear:both"></div>
		<?php echo $this->form->label('state', 'State <span>*</span>', array('escape' => false,'class' => 'required')); ?>
		
		
		<?php echo $this->form->select('state', Address::$states, array('empty' => 'Select a state', 'class' => 'validate[required]', 'data-placeholder' => 'true', 'data-native-menu' => 'false', 'id'=>'state')); ?>
		<?php echo $this->form->error('state'); ?>
		<span class="cart-select">
		<?php echo $this->form->label('zip', 'Zip Code<span>*</span>', array('escape' => false,'class' => 'required')); ?>
		<?php echo $this->form->text('zip', array('class' => 'validate[required] inputbox', 'id' => 'zip')); ?>
		<?php echo $this->form->error('zip'); ?>
		</span>
		<strong style="font-size:12px;">
		<?php echo $this->form->checkbox("opt_save", array('id' => 'opt_save', 'data-role' => 'none')) ?>
	Save this address
		</strong>
		<a href="javascript:document.getElementById('addressForm').submit();" data-role="button">Continue</a>
<?php echo $this->form->end();?> 



<div id="address_form" style="display:none">
	<?php echo $this->form->create(null ,array('id'=>'selectForm')); ?>
	<?php echo $this->form->hidden('address_id', array('class' => 'inputbox', 'id' => 'address_id')); ?>
	<?php echo $this->form->end();?>
</div>
<div class="clear"></div>
</div>
<?php else: ?>
	<div class="grid_16 cart-empty">
		<h1>
			<span class="page-title gray" style="padding:0px 0px 10px 0px;">Your shopping cart is empty</span> 	
			<a href="/sales" title="Continue Shopping">Continue Shopping</a/></h1>
	</div>
<?php endif ?>
<script>
$(document).ready(function(){ 
	$("#addresses").change(function () {
		$("#address_id").val($("#addresses option:selected").val());
		$("#selectForm").submit();
	});
});
$("[type='submit']").button();
</script>
<?php if ($cartEmpty == true): ?>
<script>
	window.location.replace('/cart/view');
</script>
<?php endif ?>