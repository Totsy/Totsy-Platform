<?php
	use app\models\Address;
	$this->html->script('application', array('inline' => false));
	$this->form->config(array('text' => array('class' => 'inputbox')));
	$countLayout = "layout: '{mnn}{sep}{snn} minutes'";
?>

<?=$this->html->style('validationEngine.jquery.css');?>
<?=$this->html->style('validationTemplate.css');?>
<?=$this->html->script('form_validator/languages/jquery.validationEngine-en.js');?>
<?=$this->html->script('form_validator/jquery.validationEngine.js');?>

<script type="text/javascript">

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

<div class="grid_16">
<?=$this->form->create($address, array(
		'id' => 'addressForm',
		'class' => "fl"
)); ?>
	<?php if(!empty($addresses_ddwn)) : ?>
		Choose your address :
		<?=$this->form->select('addresses', $addresses_ddwn, array("id" => 'addresses', 'value' => $selected));?>
		<hr />
		<?php endif ?>
</div>
<div class="grid_8">
	<?=$this->form->label('firstname', 'First Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
	<?=$this->form->text('firstname', array('class' => 'inputbox validate[required]')); ?>
	<?=$this->form->error('firstname'); ?>
	<br />
	<?=$this->form->label('lastname', 'Last Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
	<?=$this->form->text('lastname', array('class' => 'inputbox')); ?>
	<?=$this->form->error('lastname'); ?>
	<br />
	<?=$this->form->label('telephone', 'Telephone', array('escape' => false,'class' => 'required')); ?>
	<?=$this->form->text('telephone', array('class' => 'inputbox', 'id' => 'phone')); ?>
	<br />
	<?=$this->form->label('address', 'Street Address <span>*</span>', array('escape' => false,'class' => 'required')); ?>
	<?=$this->form->text('address', array('class' => 'inputbox')); ?>
	<?=$this->form->error('address'); ?>
</div>

<div class="grid_8">
	<?=$this->form->label('address_2', 'Street Address 2', array('escape' => false,'class' => 'required')); ?>
	<?=$this->form->text('address_2', array('class' => 'inputbox')); ?>
	<br />
	<?=$this->form->label('city', 'City <span>*</span>', array('escape' => false,'class' => 'required')); ?>
	<?=$this->form->text('city', array('class' => 'inputbox')); ?>
	<?=$this->form->error('city'); ?>
	<br />
	<label for="state" class='required'>State <span>*</span></label>
	<?=$this->form->select('state', Address::$states, array('empty' => 'Select a state', 'class' => 'required')); ?>
	<?=$this->form->error('state'); ?>
	<br />
	<?=$this->form->label('zip', 'Zip Code <span>*</span>', array('escape' => false,'class' => 'required')); ?>
	<?=$this->form->text('zip', array('class' => 'inputbox', 'id' => 'zip')); ?>
</div>

<?=$this->form->submit('Continue', array('class' => 'button fr')); ?>
<?=$this->form->end();?> 

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