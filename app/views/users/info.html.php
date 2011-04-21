<?php $this->title("Account Information"); ?>

<div class="grid_16">
	<h2 class="page-title gray">Account Information</h2>
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

	<div style="width:50%; float:left;">
		<h2 class="gray mar-b">Edit Account Information</h2>
		<hr />
		<fieldset id="" class="">
			<?php
					switch ($status) {
						case 'true' :
							echo "<div class=\"standard-message\">Your information has been updated.</div>";
							break;
						case 'email' :
							echo "<div class=\"standard-error-message\">Your current email is incorrect. Please try again.</div>";
							break;
						case 'name' :
							echo "<div class=\"standard-error-message\">Your current first name and last name are incorrect. Please try again.</div>";
							break;
						case 'badfacebook':
							echo "<div class=\"standard-error-message\">Sorry, This facebook account is already connected.</div>";
						// default:
						//	echo "Please enter in your new information below and submit.";
						//	break;
					}
				?>

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
					<?=$this->form->label('eamil', 'E-Mail',array('class' => 'account' )); ?>
					<?=$this->form->text('email', array(
							'class' => 'inputbox',
							'value' => $user->email
						))
					;?>
				</div>

			<?=$this->form->submit('Update', array('class' => 'button fr')); ?>
			<?=$this->form->end();?>
		</fieldset>
	</div>
<div style="width:48%; margin-left:10px; float:left;">
	<?php if ($connected): ?>
		<h2 class="gray mar-b">You're Connected With Totsy</h2>
		<hr />
		<img src="https://graph.facebook.com/<?=$user->facebook_info['id']?>/picture">
		<br /><b><?=$user->facebook_info['name']?></b>
	<?php else: ?>
		<h2 class="gray mar-b">Connect your Facebook Account with Totsy</h2>
		<hr />
		<fb:login-button perms="email,publish_stream, offline_access" size="large" length="long" v="2" style="text-align:center;">Connect With Facebook</fb:login-button>
		<div id="fb-root"></div>
	<?php endif ?>
</div>

	<br />

</div>
</div>
<div class="clear"></div>

<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId   : <?php echo $fbconfig['appId']; ?>,
      session : <?php echo json_encode($fbsession); ?>, // don't refetch the session when PHP already has it
      status  : true, // check login status
      cookie  : true, // enable cookies to allow the server to access the session
      xfbml   : true // parse XFBML
    });
    // whenever the user logs in, we refresh the page
    FB.Event.subscribe('auth.login', function() {
      window.location.reload();
    });
  };
  (function() {
    var e = document.createElement('script');
    e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
    e.async = true;
    document.getElementById('fb-root').appendChild(e);
  }());
</script>
