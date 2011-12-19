<?php use app\models\Address; ?>
<?php echo $this->html->script('jquery.maskedinput-1.2.2')?>

<div class="grid_11 omega roundy grey_inside<?php if (!$isAjax): ?> b_side <?php endif ?>">

	<?php if ($message): ?>
		<div class="standard-message"><?php echo $message; ?></div>
	<?php endif ?>

	<h2>Add / Edit Address Book <span style="float:right; font-weight:normal; font-size:12px;"><?php echo $this->html->link('Manage Address Book','addresses');?></span>
	</h2>
	<hr />
	<?php echo $this->form->create($address, array(
		'id' => 'addressForm',
		'class' => "fl",
		'action' => "{$action}/{$address->_id}"
	)); ?>
			<p>

				<?php echo $this->form->label('description', 'Label <span>*</span>', array('escape' => false,'class' => 'required')); ?><span style="font-size:10px;">(i.e. home, work, school, etc)</span>
				<?php echo $this->form->text('description', array('class' => 'inputbox')); ?>
				<?php echo $this->form->error('description'); ?>
			
            </p>

			<p>
				<?php echo $this->form->label('firstname', 'First Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('firstname', array('class' => 'inputbox')); ?>
				<?php echo $this->form->error('firstname'); ?>
			</p>

			<p>
				<?php echo $this->form->label('lastname', 'Last Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('lastname', array('class' => 'inputbox')); ?>
				<?php echo $this->form->error('lastname'); ?>
			</p>

			<p>
				<?php echo $this->form->label('telephone', 'Telephone <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('telephone', array('class' => 'inputbox', 'id' => 'phone')); ?>
				<?php echo $this->form->error('telephone'); ?>
			</p>

			<p>
				<?php echo $this->form->label('address', 'Street Address <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('address', array('class' => 'inputbox')); ?>
				<?php echo $this->form->error('address'); ?>
			</p>

			<p>
				<?php echo $this->form->label('address_2', 'Street Address 2', array('escape' => false,'class' => 'addresses')); ?>
				<?php echo $this->form->text('address_2', array('class' => 'inputbox')); ?>
			</p>

			<p>
				<?php echo $this->form->label('city', 'City <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('city', array('class' => 'inputbox')); ?>
				<?php echo $this->form->error('city'); ?>
			</p>

			<p>
				<label for="state" class='required'>State <span>*</span></label>
				<?php echo $this->form->select('state', Address::$states, array('empty' => 'Select a state')); ?>
				<?php echo $this->form->error('state'); ?>
			</p>

			<p>
				<?php echo $this->form->label('zip', 'Zip Code <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?php echo $this->form->text('zip', array('class' => 'inputbox', 'id' => 'zip')); ?>
			</p>
    <fieldset data-role="controlgroup" data-type="horizontal">
    	<legend>Make Default</legend>
         	<input type="radio" name="default" id="radio-choice-1" value="1" checked="checked" />
         	<label for="radio-choice-1">Yes</label>

         	<input type="radio" name="default" id="radio-choice-2" value="0"  />
         	<label for="radio-choice-2">No</label>

    </fieldset>
			<?php echo $this->form->submit('Update Address Book'); ?>
	<?php echo $this->form->end();?>

