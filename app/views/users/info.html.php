<?php $this->title("My Account Information"); ?>
<h1 class="p-header">My Account</h1>
<?=$this->menu->render('left'); ?>

<div id="middle" class="noright">
<div class="tl"></div>
<div class="tr"></div>
<div id="page">

	<h2 class="gray mar-b">Edit Account Information</h2>
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
						echo "<p>Please enter in your new information below and submit.</p><br>";
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
		<?=$this->form->submit('Submit', array('class' => 'flex-btn fr')); ?>
		<?=$this->form->end();?>	
	</fieldset>
	
</div>
<div class="bl"></div>
<div class="br"></div>