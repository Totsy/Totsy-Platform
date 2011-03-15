<?php $this->title("Change Your Password"); ?>
	<h1 class="p-header">My Account</h1>
	<div id="left">
		<ul class="menu main-nav">
			<li class="firstitem17"><a href="/account" title="Account Dashboard"><span>Account Dashboard</span></a></li>
			<li class="item18"><a href="/account/info" title="Account Information"><span>Account Information</span></a></li>
			<li class="item18 active"><a href="/account/password" title="Change Password"><span>Change Password</span></a></li>
			<li class="item19"><a href="/addresses" title="Address Book"><span>Address Book</span></a></li>
			<li class="item20"><a href="/orders" title="My Orders"><span>My Orders</span></a></li>
			<li class="item20"><a href="/Credits/view" title="My Credits"><span>My Credits</span></a></li>
			<li class="lastitem23"><a href="/Users/invite" title="My Invitations"><span>My Invitations</span></a></li>
			<br />
			<h3 style="color:#999;">Need Help?</h3>
			<hr />
			<li class="first item18"><a href="/tickets/add" title="Contact Us"><span>Help Desk</span></a></li>
			<li class="first item19"><a href="/pages/faq" title="Frequently Asked Questions"><span>FAQ's</span></a></li>
		</ul>
	</div>
<div id="middle" class="noright">
	<div class="tl"></div>
	<div class="tr"></div>
	<div id="page">
		<h2 class="gray mar-b">Change Your Password</h2>
		<hr />
		<fieldset id="" class="">
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
						default:
							echo "Please enter your current and new password below and submit.";
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
			<?=$this->form->submit('Submit', array('class' => 'submit-btn fr')); ?>
			<?=$this->form->end();?>
		</fieldset>

	</div>
	<div class="bl"></div>
	<div class="br"></div>
</div>
