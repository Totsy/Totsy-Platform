<?php $this->title("Change Your Password"); ?>

<div class="grid_16">
	<h2 class="page-title gray">Change Your Password</h2>
	<hr />
</div>

<div class="grid_4">
	<?php echo $this->view()->render(array('element' => 'myAccountNav')); ?>
	<?php echo $this->view()->render(array('element' => 'helpNav')); ?>
</div>

<div class="grid_11 omega roundy grey_inside b_side">

	<h2 class="page-title gray">Change Your Password</h2>
	<hr />
	<fieldset>
			<div>
				<?php
					switch ($status) {
						case 'true' :
							echo "<div class=\"standard-message\">Your information has been updated.</div>";
							break;
						case 'false' :
							echo "<div class=\"standard-error-message\">Your current password is incorrect. Please try again.</div>";
							break;
						case 'errornewpass' :
							echo "<div class=\"standard-error-message\">Please check that your passwords match and try again.</div>";
							break;
						case 'shortpass' :
							echo "<div class=\"standard-error-message\">Password must be at least 6 characters.</div>";
							break;
					}
				?>
			</div>
			<br>
			<?php echo $this->form->create(null, array('class' => "fl") );?>
				<div class="form-row">
				<div class="form-row">
					<?php echo $this->form->label('password', 'Current Password',array('class' => 'account' )); ?>
					<?php echo $this->form->password('password', array(
							'class' => 'inputbox',
							'type' => 'password',
							'id' => 'password'
						))
					;?>
				</div>
				<div class="form-row">
					<?php echo $this->form->label('new_password', 'New Password',array('class' => 'account' )); ?>
					<?php echo $this->form->password('new_password', array(
							'class' => 'inputbox'
						))
					;?>
				</div>
				<div class="form-row">
					<?php echo $this->form->label('password_confirm', 'Confirm Password',array('class' => 'account' )); ?>
					<?php echo $this->form->password('password_confirm', array(
							'class' => 'inputbox'
						))
					;?>
				</div>
			<?php echo $this->form->submit('Change Password', array('class' => 'button fr')); ?>
			<?php echo $this->form->end();?>
		</fieldset>
	<br />

</div>
</div>
<div class="clear"></div>
