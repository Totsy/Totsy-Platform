<?php use lithium\storage\Session; ?>
<?php $this->title("Account Information"); ?>
<?php
	$brandName = "Totsy";
	$fbConnectBlurb = "Connect your Facebook Account with Totsy";
	
	if(Session::read("layout", array("name"=>"default"))=="mamapedia") {
		$brandName = "Mamasource";
		$fbConnectBlurb = "Connect your Facebook Account";
	} 
?>

<div class="grid_16">
	<h2 class="page-title gray">Account Information</h2>
	<hr />
</div>

<div class="grid_4">
	<?php echo $this->view()->render(array('element' => 'myAccountNav')); ?>
	<?php echo $this->view()->render(array('element' => 'helpNav')); ?>
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
							echo "<div class=\"standard-error-message\">Your current email is incorrect, or already in use. Please try again.</div>";
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

			<?php echo $this->form->create(null, array('class' => "fl") );?>
				<div class="form-row">
					<?php echo $this->form->label('firstname', 'First Name', array('class' => 'account' )); ?>
					<?php echo $this->form->text('firstname', array(
							'type' => 'text',
							'class' => 'inputbox',
							'value' => $user->firstname
						))
					?>
				</div>
				<div class="form-row">
					<?php echo $this->form->label('lastname', 'Last Name',array('class' => 'account' )); ?>
					<?php echo $this->form->text('lastname', array(
							'class' => 'inputbox',
							'value' => $user->lastname
						));
					?>
				</div>
				<div class="form-row">
					<?php echo $this->form->label('eamil', 'E-Mail',array('class' => 'account' )); ?>
					<?php echo $this->form->text('email', array(
							'class' => 'inputbox',
							'value' => $user->email
						))
					;?>
				</div>
				
			<?php echo $this->form->submit('Update', array('class' => 'button fr')); ?>
			<?php echo $this->form->end();?>
		</fieldset>
	</div>
<div style="width:48%; margin-left:10px; float:left;">
	<?php if ($connected): ?>
		<h2 class="gray mar-b">You're Connected With <?php echo $brandName; ?></h2>
		<hr />
		<img src="https://graph.facebook.com/<?php echo $user->facebook_info['id']?>/picture">
		<br /><b><?php echo $user->facebook_info['name']?></b>
	<?php else: ?>
		<h2 class="gray mar-b"><?php echo $fbConnectBlurb; ?></h2>
		<hr />
		<fb:login-button scope="email" size="large" length="long" v="2" style="text-align:center;">Connect With Facebook</fb:login-button>
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
	  oauth: true,      
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
