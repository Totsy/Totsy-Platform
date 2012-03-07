<h2 class="gray mar-b">Personal Information</h2>
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

			<?php echo $this->form->submit('Update Information', array('class' => 'button fr')); ?>
			<?php echo $this->form->end();?>
		</fieldset>
		
<p></p>
<?php echo $this->view()->render(array('element' => 'mobile_headerNav'), array('userInfo' => $userInfo, 'credit' => $credit, 'cartCount' => $cartCount, 'fblogout' => $fblogout)); ?>
