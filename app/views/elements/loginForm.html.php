<h2 style="margin-bottom:20px;">Sign In with Facebook</h2>
	<a href="javascript:;" onclick="fblogin();return false;"><img src="/img/sign_in_fb.png" class="fr"></a>
	<br />
	<h2 style="margin-top:30px;margin-bottom:20px;padding-top:5px;">Sign in with email</h2>
<?php echo $this->form->create(null,array('id'=>'loginForm'));?>


	<div style="width:70px; float:left">
	<?php echo $this->form->label('email', 'Email<span>*</span>', array(
		'escape' => false,
		'class' => 'required loginformlabel'
		));
	?>
	</div>

	<div style="float:right">
	<?php echo $this->form->text('email', array('class' => "validate['required'] inputbox", 'style' => 'width:228px', 'id' => 'email')); ?>
	</div>

	<?php echo $this->form->error('email'); ?>
<div class="clear"></div>

	<div style="width:70px; float:left">
	<?php echo $this->form->label('password', 'Password<span>*</span>', array(
		'escape' => false,
		'class' => 'required loginformlabel'
		));
	?>
	</div>

	<div style="float:right">
	<?php echo $this->form->password('password', array('class' => "validate['required'] inputbox", 'style' => 'width:228px', 'id' => 'password', 'type' => 'password')); ?>
	</div>

	<?php echo $this->form->error('password'); ?>
<div class="clear"></div>
<div style="text-align:center; width:300px;padding:5px;">
	<?php echo $this->form->checkbox('remember_me', array('class' => 'checkbox')); ?> Remember Me &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $this->html->link('Forgot your password?','/reset', array('class'=> 'md', 'title'=> 'Forgot your password?'))?><br/>
</div>

<div class="clear"></div>
	<?php echo $this->form->submit('Sign In', array('class'=>"button fr"));?>
<?php echo $this->form->end();?>
