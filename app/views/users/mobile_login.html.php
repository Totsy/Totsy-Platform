<h2>Sign In <span style="color:#999; font-size:12px; float:right; margin:5px 0px 0px 0px;">Not a Totsy member? <a href="#" onclick="window.location.href='/register';return false;">Sign Up</a></span></h2>
<hr />
<?php if ($message){ ?>
<?php echo $message; ?>
<?php } ?>
<?=$this->form->create(null,array('id'=>'loginForm'));?>

<p>
<?=$this->form->label('email', 'Email', array('escape' => false,
											'class' => 'validate["required"]  inputbox',
											'style' => 'display:inline-block;'
											));
										?>
<?=$this->form->text('email', array('class' => 'inputbox')); ?>
<?=$this->form->error('email'); ?>
</p>
<p><?=$this->form->label('password', 'Password', array('escape' => false,
											'class' => 'validate["required"]  inputbox',
											'style' => 'display:inline-block; '
											));
										?><?=$this->html->link('Forgot your password?','#', array('style' => 'font-size:9px; margin-left:3px;', 'title'=>"Forgot your password?", 'onclick' => 'window.location.href="/reset";return false;'))?>
<?=$this->form->password('password', array('class'=>"validate['required'] inputbox",
'id' => 'password', 'type' => 'password')); ?>
<?=$this->form->error('password'); ?>
</p>

<p><?=$this->form->checkbox('remember_me', array('class' => 'checkbox', 'data-role' => 'none')); ?> Remember Me </p>

<?=$this->form->submit('Sign In', array('data-theme' => 'b', 'data-ajax' => 'false'));?> 
<?=$this->form->end();?>