<?php use app\models\Address; ?>
<?=$this->html->script('jquery.maskedinput-1.2.2')?>

<div class="grid_11 omega roundy grey_inside<?php if (!$isAjax): ?> b_side <?php endif ?>">

	<?php if ($message): ?>
		<div class="standard-message"><?=$message; ?></div>
	<?php endif ?>

	<h2>Add / Edit Address Book <span style="float:right; font-weight:normal; font-size:12px;"><?=$this->html->link('Manage Address Book','addresses');?></span>
	</h2>
	<hr />
	<?=$this->form->create($address, array(
		'id' => 'addressForm',
		'class' => "fl",
		'action' => "{$action}/{$address->_id}"
	)); ?>
			<p>

				<?=$this->form->label('description', 'Label <span>*</span>', array('escape' => false,'class' => 'required')); ?><span style="font-size:10px;">(i.e. home, work, school, etc)</span>
				<?=$this->form->text('description', array('class' => 'inputbox')); ?>
				<?=$this->form->error('description'); ?>
			
            </p>

			<p>
				<?=$this->form->label('firstname', 'First Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('firstname', array('class' => 'inputbox')); ?>
				<?=$this->form->error('firstname'); ?>
			</p>

			<p>
				<?=$this->form->label('lastname', 'Last Name <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('lastname', array('class' => 'inputbox')); ?>
				<?=$this->form->error('lastname'); ?>
			</p>

			<p>
				<?=$this->form->label('telephone', 'Telephone <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('telephone', array('class' => 'inputbox', 'id' => 'phone')); ?>
				<?=$this->form->error('telephone'); ?>
			</p>

			<p>
				<?=$this->form->label('address', 'Street Address <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('address', array('class' => 'inputbox')); ?>
				<?=$this->form->error('address'); ?>
			</p>

			<p>
				<?=$this->form->label('address_2', 'Street Address 2', array('escape' => false,'class' => 'addresses')); ?>
				<?=$this->form->text('address_2', array('class' => 'inputbox')); ?>
			</p>

			<p>
				<?=$this->form->label('city', 'City <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('city', array('class' => 'inputbox')); ?>
				<?=$this->form->error('city'); ?>
			</p>

			<p>
				<label for="state" class='required'>State <span>*</span></label>
				<?=$this->form->select('state', Address::$states, array('empty' => 'Select a state')); ?>
				<?=$this->form->error('state'); ?>
			</p>

			<p>
				<?=$this->form->label('zip', 'Zip Code <span>*</span>', array('escape' => false,'class' => 'required')); ?>
				<?=$this->form->text('zip', array('class' => 'inputbox', 'id' => 'zip')); ?>
			</p>
    <fieldset data-role="controlgroup" data-type="horizontal">
    	<legend>Make Default</legend>
         	<input type="radio" name="default" id="radio-choice-1" value="1" checked="checked" />
         	<label for="radio-choice-1">Yes</label>

         	<input type="radio" name="default" id="radio-choice-2" value="0"  />
         	<label for="radio-choice-2">No</label>

    </fieldset>
			<?=$this->form->submit('Update Address Book'); ?>
	<?=$this->form->end();?>

