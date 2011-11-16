<h2 style="margin-bottom:20px;">Sign In with Facebook</h2>
	<a href="javascript:;" onclick="fblogin();return false;"><img src="/img/sign_in_fb.png" class="fr"></a>
	<br />
	<h2 style="margin-top:30px;margin-bottom:20px;border-top:1px solid #cccccc;padding-top:5px;">Or sign in with email</h2>	
<?=$this->form->create(null,array('id'=>'loginForm'));?>
		

	<div style="width:70px; float:left">
	<?=$this->form->label('email', 'Email <span>*</span>', array(
		'escape' => false,
		'class' => 'required loginformlabel'
		));
	?>
	</div>

	<div style="float:right">
	<?=$this->form->text('email', array('class' => "validate['required'] inputbox", 'style' => 'width:228px', 'id' => 'email')); ?>
	</div>

	<?=$this->form->error('email'); ?>
<div class="clear"></div>
	
	<div style="width:70px; float:left">
	<?=$this->form->label('password', 'Password <span>*</span>', array(
		'escape' => false,
		'class' => 'required loginformlabel'
		));
	?>
	</div>

	<div style="float:right">
	<?=$this->form->text('password', array('class' => "validate['required'] inputbox", 'style' => 'width:228px', 'id' => 'password', 'type' => 'password')); ?>
	</div>

	<?=$this->form->error('password'); ?>
<div class="clear"></div>

	<?=$this->form->checkbox('remember_me', array('class' => 'checkbox')); ?> Remember Me &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?=$this->html->link('Forgot your password?','/reset', array('class'=> 'md', 'title'=> 'Forgot your password?'))?><br/>
<div class="clear"></div>
	<?=$this->form->submit('Sign In', array('class'=>"button fr"));?> 
<?=$this->form->end();?>