<?php $this->title("Change Your Password"); ?>

<div class="grid_16">
	<h2 class="page-title gray">Change Your Password</h2>
	<hr />
</div>

<div class="grid_4 omega">
	<div class="roundy grey_inside">
		<h3 class="gray">My Account</h3>
		<hr />
		<ul class="menu main-nav">
		<li><a href="/account" title="Account Dashboard">Account Dashboard</a></li>
		<li><a href="/account/info" title="Account Information">Account Information</a></li>
		<li><a href="/account/password" title="Change Password">Change Password</a></li>
		<li class="active"><a href="/addresses" title="Address Book">Address Book</a></li>
		<li><a href="/orders" title="My Orders">My Orders</a></li>
		<li><a href="/Credits/view" title="My Credits">My Credits</a></li>
		<li><a href="/Users/invite" title="My Invitations">My Invitations</a></li>
		</ul>
	</div>
	<div class="clear"></div>
	<div class="roundy grey_inside">
		<h3 class="gray">Need Help?</h3>
		<hr />
		<ul class="menu main-nav">
		    <li><a href="/tickets/add" title="Contact Us">Help Desk</a></li>
			<li><a href="/pages/faq" title="Frequently Asked Questions">FAQ's</a></li>
			<li><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
			<li><a href="/pages/terms" title="Terms Of Use">Terms Of Use</a></li>
		</ul>
	</div>
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
			<?=$this->form->create(null, array('class' => "fl") );?>
				<div class="form-row">
				<div class="form-row">
					<?=$this->form->label('password', 'Current Password',array('class' => 'account' )); ?>
					<?=$this->form->password('password', array(
							'class' => 'inputbox',
							'type' => 'password',
							'id' => 'password'
						))
					;?>
				</div>
				<div class="form-row">
					<?=$this->form->label('new_password', 'New Password',array('class' => 'account' )); ?>
					<?=$this->form->password('new_password', array(
							'class' => 'inputbox'
						))
					;?>
				</div>
				<div class="form-row">
					<?=$this->form->label('password_confirm', 'Confirm Password',array('class' => 'account' )); ?>
					<?=$this->form->password('password_confirm', array(
							'class' => 'inputbox'
						))
					;?>
				</div>
			<?=$this->form->submit('Change Password', array('class' => 'button fr')); ?>
			<?=$this->form->end();?>
		</fieldset>
	<br />

</div>
</div>
<div class="clear"></div>
