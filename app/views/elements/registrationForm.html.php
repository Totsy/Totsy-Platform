	<h3 style="color:#999; font-size:18px;">Register</h3>
	<hr />
	<?php if (preg_match('/join/',$_SERVER['REQUEST_URI'])) {
	print '<form id="registerForm" method="post" onsubmit="_gaq.push([\'_trackPageview\', \'/vpv/join\']);">';
		} else {
	print '<form id="registerForm" method="post" onsubmit="_gaq.push([\'_trackPageview\', \'/vpv/register\']);">';
		 }
	?>

	<?=$this->form->label('firstname', 'First Name <span>*</span>', array(
		'escape' => false,
		'class' => 'required'
		));
	?>

	<?=$this->form->text('firstname', array('class' => 'inputbox')); ?>
	<?=$this->form->error('firstname'); ?>

	<?=$this->form->label('lastname', 'Last Name <span>*</span>', array(
		'escape' => false,
		'class' => 'required'
		));
	?>

	<?=$this->form->text('lastname', array('class' => 'inputbox')); ?>
	<?=$this->form->error('lastname'); ?>

	<?=$this->form->label('zip', 'Zip Code <span>*</span>', array(
		'escape' => false,
		'class' => 'required'
		));
	?>

	<?=$this->form->text('zip', array('class' => 'inputbox', 'id' => 'zip')); ?>
	<?=$this->form->error('zip'); ?>

	<?=$this->form->label('email', 'Email <span>*</span>', array(
		'escape' => false,
		'class' => 'required'
		));
	?>

	<?=$this->form->text('email', array('class' => 'inputbox', 'style' => 'width:188px')); ?>
	<?=$this->form->error('email'); ?>
	
	<?=$this->form->label('confirmemail', 'Confirm Email <span>*</span>', array(
		'escape' => false,
		'class' => 'required'
		));
	?>

	<?=$this->form->text('confirmemail', array('class' => 'inputbox', 'style' => 'width:188px')); ?>
	<?=$this->form->error('confirmemail'); ?>
	<?=$this->form->error('emailcheck'); ?>

	<?=$this->form->label('password','Password <span>*</span>', array(
		'class'=>'required',
		'escape' => false
		));
	?>

	<?=$this->form->password('password', array(
		'class'=>"inputbox",
		'name' => 'password',
		'id' => 'password', 'style' => 'width:188px'
		));
	?>

	<?=$this->form->error('password'); ?>
	<?=$this->form->checkbox('terms', array(
		"checked" => "checked", 
		'style'=>"float:left;margin-right:4px; display: none;"
		));
	?>

	<span class="sm reg-tos" style="overflow:visible!important;">
		By clicking register you accept our 
		<?=$this->html->link('Terms and Conditions','pages/terms')?>.
	</span>
	<br>
	<?=$this->form->submit('Register', array(
		'class' => 'button fr'
		));
	?>
	
	<?=$this->form->error('terms'); ?>
	
<?=$this->form->end(); ?>

	<div>
		<h3 style="color:#999; font-size:18px;">Register With Facebook</h3>
		<hr />
		<a href="javascript:;" onclick="fblogin();return false;"><img src="/img/fb_register_btn.png"></a>
	</div>