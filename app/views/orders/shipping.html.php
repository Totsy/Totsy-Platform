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

<script>

    $(document).ready(function() {
        $("#addressForm").validationEngine('attach');        
		$("#addressForm").validationEngine({ promptPosition : "centerRight", scroll: false });
    	$("#addressForm").validationEngine('init', { promptPosition : "centerRight", scroll: false });       
    });

</script>

<div class="grid_16">
	<h2 class="page-title gray">Shipping Information</h2>
	<hr />
	<?php if (!empty($error)) { ?>
		<div class="checkout-error"><h2>Uh Oh! Please fix the errors below:</h2></div>
	<?php } ?>
</div>

<div class="container_16">
<?=$this->form->create($address, array(
		'id' => 'addressForm',
		'class' => "fl"
)); ?>
	<?php if(!empty($addresses_ddwn)) : ?>
		Choose your address :
		<?=$this->form->select('addresses', $addresses_ddwn, array("id" => 'addresses', 'value' => $selected));?>
		<hr />
		<?php endif ?>
		
	<div class="grid_8">
		<?=$this->form->label('firstname', 'First Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
		<?=$this->form->text('firstname', array('class' => 'validate[required] inputbox', 'id'=>'firstname')); ?>
		<?=$this->form->error('firstname'); ?>
		<br />
		<?=$this->form->label('lastname', 'Last Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
		<?=$this->form->text('lastname', array('class' => 'validate[required] inputbox', 'id'=>'lastname')); ?>
		<?=$this->form->error('lastname'); ?>
		<br />
		<?=$this->form->label('telephone', 'Telephone', array('escape' => false,'class' => 'required')); ?>
		<?=$this->form->text('telephone', array('class' => 'validate[custom[phone]] inputbox', 'id' => 'phone')); ?>
		<br />
		<?=$this->form->label('address', 'Street Address <span>*</span>', array('escape' => false,'class' => 'required')); ?>
		<?=$this->form->text('address', array('class' => 'validate[required] inputbox', 'id'=>'address' )); ?>
		<?=$this->form->error('address'); ?>
		<br />
	</div>
	
	<div class="grid_8">
		<?=$this->form->label('address_2', 'Street Address 2', array('escape' => false,'class' => 'required')); ?>
		<?=$this->form->text('address_2', array('class' => 'inputbox', 'id'=>'address_2')); ?>
		<br />
		<?=$this->form->label('city', 'City <span>*</span>', array('escape' => false,'class' => 'required')); ?>
		<?=$this->form->text('city', array('class' => 'validate[required] inputbox', 'id'=>'city')); ?>
		<?=$this->form->error('city'); ?>
		<br />
		<label for="state" class='required'>State <span>*</span></label>
		<?=$this->form->select('state', Address::$states, array('empty' => 'Select a state', 'class' => 'validate[required] required', 'id'=>'state')); ?>
		<?=$this->form->error('state'); ?>
		<br />
		<?=$this->form->label('zip', 'Zip Code <span>*</span>', array('escape' => false,'class' => 'required')); ?>
		<?=$this->form->text('zip', array('class' => 'validate[required] inputbox', 'id' => 'zip')); ?>
	</div>
	
	<div class="grid_16">
			<?=$this->form->submit('Continue', array('class' => 'button fr')); ?>
	</div>

<?=$this->form->end();?> 

</div>


<div id="address_form" style="display:none">
	<?=$this->form->create(null ,array('id'=>'selectForm')); ?>
	<?=$this->form->hidden('address_id', array('class' => 'inputbox', 'id' => 'address_id')); ?>
	<?=$this->form->end();?>
</div>
<div class="clear"></div>
</div>
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