<?php $this->title("My Account Information"); ?>
	<h1 class="p-header">My Account</h1>
	<div id="left">
		<ul class="menu main-nav">
		<li class="firstitem17"><a href="/account" title="Account Dashboard"><span>Account Dashboard</span></a></li>
	    <li class="item18 active"><a href="/account/info" title="Account Information"><span>Account Information</span></a></li>
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
	
		<h2 class="gray mar-b">Edit Account Information</h2>
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
						default:
							echo "Please enter in your new information below and submit.";
							break;
					}
				?>
			</div>
			<br>
			<?=$this->form->create(null, array('class' => "fl") );?>
				<div class="form-row"> 
					<?=$this->form->label('firstname', 'First Name', array('class' => 'account' )); ?>
					<?=$this->form->text('firstname', array(
							'type' => 'text', 
							'class' => 'inputbox',
							'value' => $user->firstname
						))
					?>
				</div>
				<div class="form-row"> 
					<?=$this->form->label('lastname', 'Last Name',array('class' => 'account' )); ?>
					<?=$this->form->text('lastname', array(
							'class' => 'inputbox',
							'value' => $user->lastname
						));
					?>
				</div>
				<div class="form-row"> 
					<?=$this->form->label('zip', 'Zip Code',array('class' => 'account' )); ?>
					<?=$this->form->text('zip', array(
							'class' => 'inputbox',
							'value' => $user->zip
						));
					?>
				</div>
				<div class="form-row"> 
					<?=$this->form->label('eamil', 'E-Mail',array('class' => 'account' )); ?>
					<?=$this->form->text('email', array(
							'class' => 'inputbox',
							'value' => $user->email
						))
					;?>
				</div>
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
			<?=$this->form->submit('Update Account Information', array('class' => 'button')); ?>
			<?=$this->form->end();?>	
		</fieldset>
		
	</div>
	<div class="bl"></div>
	<div class="br"></div>
</div>
