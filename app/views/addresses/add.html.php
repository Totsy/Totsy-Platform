<?php use app\models\Address; ?>
<?php echo $this->html->script('jquery.maskedinput-1.2.2')?>

<?php $this->title("Add / Edit Address Book "); ?>
<?php if (!$isAjax): ?>
<div class="grid_16">
	<h2 class="page-title gray">Add / Edit Address Book </h2>
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

	<h2 class="page-title gray">Add / Edit Address Book <span style="float:right; font-weight:normal; font-size:12px;"><?php if (!$isAjax): ?>
                <?php echo $this->html->link('Manage Address Book','addresses');?><?php endif ?></span>
	</h2>
	<hr />
	<?php echo $this->form->create($address, array(
		'id' => 'addressForm',
		'class' => "fl",
		'action' => "{$action}/{$address->_id}"
	)); ?>
		<fieldset>
			<legend class="no-show">New Address</legend>
			<?php if (!$isAjax): ?>
				<div class="form-row">
					<label class="addresses">Make Default</label>
					<input type="radio" name="default" value="1" checked> Yes<br>
					<input type="radio" name="default" value="0"> No
				</div>
			<?php endif ?>
			<div class="form-row">

				<?php echo $this->form->label('description', 'Description <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('description', array('class' => 'inputbox')); ?>
				<?php echo $this->form->error('description'); ?>
			<span style="font-size:10px;">(i.e. home, work, school, etc)</span>
            </div>

			<div class="form-row">
				<?php echo $this->form->label('firstname', 'First Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('firstname', array('class' => 'inputbox')); ?>
				<?php echo $this->form->error('firstname'); ?>
			</div>

			<div class="form-row">
				<?php echo $this->form->label('lastname', 'Last Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('lastname', array('class' => 'inputbox')); ?>
				<?php echo $this->form->error('lastname'); ?>
			</div>

			<div class="form-row">
				<?php echo $this->form->label('telephone', 'Telephone <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('telephone', array('class' => 'inputbox', 'id' => 'phone')); ?>
				<?php echo $this->form->error('telephone'); ?>
			</div>

			<div class="form-row">
				<?php echo $this->form->label('address', 'Street Address <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('address', array('class' => 'inputbox')); ?>
				<?php echo $this->form->error('address'); ?>
			</div>

			<div class="form-row">
				<?php echo $this->form->label('address_2', 'Street Address 2', array('escape' => false,'class' => 'addresses')); ?>
				<?php echo $this->form->text('address_2', array('class' => 'inputbox')); ?>
			</div>

			<div class="form-row">
				<?php echo $this->form->label('city', 'City <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('city', array('class' => 'inputbox')); ?>
				<?php echo $this->form->error('city'); ?>
			</div>

			<div class="form-row">
				<label for="state" class='required'>State <span>*</span></label>
				<?php echo $this->form->select('state', Address::$states, array('empty' => 'Select a state')); ?>
				<?php echo $this->form->error('state'); ?>
			</div>

			<div class="form-row">
				<?php echo $this->form->label('zip', 'Zip Code <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('zip', array('class' => 'inputbox', 'id' => 'zip')); ?>
			</div>
			<?php echo $this->form->submit('Update Address Book', array('class' => 'button')); ?>

		</fieldset>
		<?php if ($isAjax): ?>
			<?php echo $this->form->hidden('isAjax', array('value' => 1)); ?>
		<?php endif ?>
	<?php echo $this->form->end();?>

</div>

<script type="text/javascript">
jQuery(function($){
   $("#date").mask("99/99/9999");
   $("#phone").mask("(999) 999-9999");
   $("#tin").mask("99-9999999");
   $("#zip").mask("99999");
});
</script>
