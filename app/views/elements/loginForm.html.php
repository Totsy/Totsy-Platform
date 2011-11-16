<h2 style="margin-bottom:20px;">Sign In with Facebook</h2>
	<a href="javascript:;" onclick="fblogin();return false;"><img src="/img/sign_in_fb.png" class="fr"></a>
	<br />
	<h2 style="margin-top:30px;margin-bottom:20px;">Or sign in with email</h2>	
<?=$this->form->create(null,array('id'=>'loginForm'));?>
		
	<?=$this->form->label('email', 'Email <span>*</span>', array(
		'escape' => false,
		'class' => 'required'
		));
	?>

	<?=$this->form->text('email', array('class' => "validate['required'] inputbox", 'style' => 'width:158px', 'id' => 'email')); ?>
	<?=$this->form->error('email'); ?>
	
	<?=$this->form->label('password', 'Password <span>*</span>', array(
		'escape' => false,
		'class' => 'required'
		));
	?>

	<?=$this->form->text('password', array('class' => "validate['required'] inputbox", 'style' => 'width:158px', 'id' => 'password', 'type' => 'password')); ?>
	<?=$this->form->error('password'); ?>

	<?=$this->form->checkbox('remember_me', array('class' => 'checkbox')); ?> Remember Me <?=$this->html->link('Forgot your password?','/reset', array('class'=> 'md', 'title'=> 'Forgot your password?'))?><br/>
	<?=$this->form->submit('Sign In', array('class'=>"button fr"));?> 
<?=$this->form->end();?>