<?php
	use app\models\Address;
	$this->html->script('application', array('inline' => false));
	$this->form->config(array('text' => array('class' => 'inputbox')));
	$countLayout = "layout: '{mnn}{sep}{snn} minutes'";
?>

<div class="grid_16">
	<h2 class="page-title gray">Shipping Information</h2>
	<hr />
	<?php if (!empty($error)) { ?>
	<div class="checkout-error"><h2>Uh Oh! Please fix the errors below:</h2><hr /><?=$error; ?></div>
<?php } ?>
</div>

<div class="grid_10">
<?=$this->form->create($address, array(
		'id' => 'addressForm',
		'class' => "fl",
		'action' => "{$action}/{$address->_id}"
	)); ?>
			

				<?=$this->form->label('firstname', 'First Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('firstname', array('class' => 'inputbox')); ?>
				<?=$this->form->error('firstname'); ?>

				<?=$this->form->label('lastname', 'Last Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('lastname', array('class' => 'inputbox')); ?>
				<?=$this->form->error('lastname'); ?>

				<?=$this->form->label('telephone', 'Telephone', array('escape' => false,'class' => 'addresses')); ?>
				<?=$this->form->text('telephone', array('class' => 'inputbox', 'id' => 'phone')); ?>

				<?=$this->form->label('address', 'Street Address <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('address', array('class' => 'inputbox')); ?>
				<?=$this->form->error('address'); ?>

				<?=$this->form->label('address_2', 'Street Address 2', array('escape' => false,'class' => 'addresses')); ?>
				<?=$this->form->text('address_2', array('class' => 'inputbox')); ?>

				<?=$this->form->label('city', 'City <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('city', array('class' => 'inputbox')); ?>
				<?=$this->form->error('city'); ?>

				<label for="state" class='required'>State <span>*</span></label>
				<?=$this->form->select('state', Address::$states, array('empty' => 'Select a state')); ?>
				<?=$this->form->error('state'); ?>

				<?=$this->form->label('zip', 'Zip Code <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('zip', array('class' => 'inputbox', 'id' => 'zip')); ?>
			<?=$this->form->submit('Shipping Information', array('class' => 'button fr')); ?>

<?=$this->form->end();?> 
</div>

<div class="clear"></div>
</div>
<script type="text/javascript">
    $('#disney').click(function(){
        $('#modal').load('/events/disney').dialog({
            autoOpen: false,
            modal:true,
            width: 739,
            height: 700,
            position: 'top',
            close: function(ev, ui) {}
        });
        $('#modal').dialog('open');
    });
</script>
	<?php if ($cartEmpty == true):
	?>
		<script>
			window.location.replace('/cart/view');
		</script>
<?php endif ?>
