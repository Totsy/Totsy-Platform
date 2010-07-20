<?php $this->title("My Account Information"); ?>
<?=$this->menu->render('left', array('div' => array('id' => 'left'), 'ul' => array('class' => 'menu'))); ?>

<h1 class="p-header">My Info</h1>
<div class="tl"></div>
<div class="tr"></div>
<div id="page">

	<h2 class="gray mar-b">Edit Account Information</h2>
	<fieldset id="" class="">
		<div>
			<?php 
				switch ($status) {
					case 'true' :
						echo "Your information has been updated.";
						break;
					case 'false' :
						echo "Your current password is incorrect. Please try again";
						break;
					default:
						echo "<p>Please enter in your new information below and submit.</p><br>";
						break;
				}

				
			?>
		</div>
		<?=$this->form->create(null, array('class' => "fl") );?>
			<div class="form-row"> 
				<?=$this->form->label('firstname', 'First Name'); ?>
				<?=$this->form->text('firstname', array(
						'type' => 'text', 
						'class' => 'inputbox',
						'value' => $user->firstname
					))
				?>
			</div>
			<div class="form-row"> 
				<?=$this->form->label('lastname', 'Last Name'); ?>
				<?=$this->form->text('lastname', array(
						'class' => 'inputbox',
						'value' => $user->lastname
					));
				?>
			</div>
			<div class="form-row"> 
				<?=$this->form->label('eamil', 'E-Mail'); ?>
				<?=$this->form->text('email', array(
						'class' => 'inputbox',
						'value' => $user->email
					))
				;?>
			</div>
			<div class="form-row"> 
				<?=$this->form->label('password', 'Current Password'); ?>
				<?=$this->form->password('password', array(
						'class' => 'inputbox',
						'type' => 'password',
						'id' => 'password'
					))
				;?>
			</div>
			<div class="form-row"> 
				<?=$this->form->label('new_password', 'New Password'); ?>
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