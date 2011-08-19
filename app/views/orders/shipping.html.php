<script type="text/javascript">	
	$( function () {
	    var itemExpires = new Date(<?=($cartExpirationDate  * 1000)?>);	    
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
    	
    	 
    	$(".inputbox").blur( function() { 
    	    
			$('#' + this.id + "").validationEngine('hide');	
			//if they validate the field by filling it in, reset the background of the control to white again
			if($('#' + this.id + "").val()!="" || this.id=="phone" || this.id=="address_2") { 
			     $('#' + this.id + "").attr('style', 'background: #FFF !important');
			} else {
			    $('#' + this.id + "").attr('style', 'background: #FFFFC5 !important');
			}
    	});
    	    
    });
   
</script>
<?php  if(empty($cartEmpty)): ?>
<div class="container_16" style="height:1000px">

	<div style="margin:10px">
	<div class="grid_8" style="padding-bottom:10px; margin:20px auto auto auto;">
		<div style="float:left">
			<h2 class="page-title gray">
				<span class="cart-step-status gray">Shipping Information</span>
				<span class="cart-step-status"><img src="/img/cart_steps_completed.png"></span>
				<span class="cart-step-status"><img src="/img/cart_steps2.png"></span>
				<span class="cart-step-status"><img src="/img/cart_steps_remaining.png"></span>
				<span class="cart-step-status"><img src="/img/cart_steps_remaining.png"></span>
			</h2>
			<?php if (!empty($error)) { ?>
			<div class="checkout-error"><h2>Uh Oh! Please fix the errors below:</h2></div>
			<?php } ?>
		</div>
	</div>
	
	<div class="grid_8" style="padding-bottom:10px; margin:20px auto auto auto;">
		<div style="float:right;">
		Item Reserved For: <br />
		<span id="itemCounter" style="color:#009900; float:right !important">Dummy cart expiration date</span>
	    </div>
	</div>
	
	<?=$this->form->create($address, array(
		'id' => 'addressForm',
		'class' => "fl"
)); ?>

	<?php if(!empty($addresses_ddwn) && (count($addresses_ddwn) > 1)) : ?>
		<hr />
		Choose your address :
		<?=$this->form->select('addresses', $addresses_ddwn, array("id" => 'addresses', 'value' => $selected));?>
		<hr />
		<?php endif ?>
			
	<div class="grid_16">
		<div>
		<?=$this->form->label('firstname', 'First Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
		<?=$this->form->text('firstname', array('class' => 'validate[required] inputbox', 'id'=>'firstname')); ?>
		<?=$this->form->error('firstname'); ?>
		</div>
		<div>
		<?=$this->form->label('lastname', 'Last Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
		<?=$this->form->text('lastname', array('class' => 'validate[required] inputbox', 'id'=>'lastname')); ?>
		<?=$this->form->error('lastname'); ?>
		</div>
		<div>
		<?=$this->form->label('telephone', 'Telephone', array('escape' => false,'class' => 'required')); ?>
		<?=$this->form->text('telephone', array('class' => 'validate[custom[phone]] inputbox', 'id' => 'phone')); ?>
		</div>
		<div>
		<?=$this->form->label('address', 'Street Address <span>*</span>', array('escape' => false,'class' => 'required')); ?>
		<?=$this->form->text('address', array('class' => 'validate[required] inputbox', 'id'=>'address' )); ?>
		<?=$this->form->error('address'); ?>
		</div>
		<div>
		<?=$this->form->label('address_2', 'Street Address 2', array('escape' => false,'class' => 'required')); ?>
		<?=$this->form->text('address_2', array('class' => 'inputbox', 'id'=>'address_2')); ?>
		</div>
		<div>
		<?=$this->form->label('city', 'City <span>*</span>', array('escape' => false,'class' => 'required')); ?>
		<?=$this->form->text('city', array('class' => 'validate[required] inputbox', 'id'=>'city')); ?>
		<?=$this->form->error('city'); ?>
		</div>
		<div>
		<?=$this->form->label('state', 'State <span>*</span>', array('escape' => false,'class' => 'required')); ?>
		<?=$this->form->select('state', Address::$states, array('empty' => 'Select a state', 'class' => 'validate[required] inputbox','id'=>'state')); ?>
		<?=$this->form->error('state'); ?></div>
		<div>
		<?=$this->form->label('zip', 'Zip Code<span>*</span>', array('escape' => false,'class' => 'required')); ?>
		<?=$this->form->text('zip', array('class' => 'validate[required] inputbox', 'id' => 'zip')); ?>
		</div>
	</div>
	
	<div class="grid_16">
			<?=$this->form->submit('Payment', array('class' => 'button fr', 'style'=>'float:right')); ?>
	</div>

<?=$this->form->end();?> 

</div>
</div>

<div id="address_form" style="display:none">
	<?=$this->form->create(null ,array('id'=>'selectForm')); ?>
	<?=$this->form->hidden('address_id', array('class' => 'inputbox', 'id' => 'address_id')); ?>
	<?=$this->form->end();?>
</div>
<div class="clear"></div>
</div>
<?php else: ?>
	<div class="grid_16" style="padding:20px 0; margin:20px 0;"><h1><center><span class="page-title gray" style="padding:0px 0px 10px 0px;">Your shopping cart is empty</span> <a href="/sales" title="Continue Shopping">Continue Shopping</a/></center></h1></div>
<?php endif ?>
<script>
$(document).ready(function(){ 
	$("#addresses").change(function () {
		$("#address_id").val($("#addresses option:selected").val());
		$("#selectForm").submit();
	});
});
</script>
<?php if ($cartEmpty == true): ?>
<script>
	window.location.replace('/cart/view');
</script>
<?php endif ?>