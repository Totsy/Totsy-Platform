<h2>Sign In <span style="color:#999; font-size:12px; float:right; margin:5px 0px 0px 0px;">Not a Totsy member? <a href="#" onclick="window.location.href='/register';return false;">Sign Up</a></span></h2>
<hr />
<?php if ($message){ ?>
<?php echo $message; ?>
<?php } ?>
<?php echo $this->form->create(null,array('id'=>'loginForm'));?>

<p>
<?php echo $this->form->label('email', 'Email', array('escape' => false,
											'class' => 'validate["required"]  inputbox',
											'style' => 'display:inline-block;'
											));
										?>
<?php echo $this->form->email('email', array('class' => 'inputbox')); ?>
<?php echo $this->form->error('email'); ?>
</p>
<p><?php echo $this->form->label('password', 'Password', array('escape' => false,
											'class' => 'validate["required"]  inputbox',
											'style' => 'display:inline-block; '
											));
										?><?php echo $this->html->link('Forgot your password?','#', array('style' => 'font-size:9px; margin-left:3px;', 'title'=>"Forgot your password?", 'onclick' => 'window.location.href="/reset";return false;'))?>
<?php echo $this->form->password('password', array('class'=>"validate['required'] inputbox",
'id' => 'password', 'type' => 'password')); ?>
<?php echo $this->form->error('password'); ?>
</p>

<p><?php echo $this->form->checkbox('remember_me', array('class' => 'checkbox', 'data-role' => 'none')); ?> Remember Me </p>

<?php echo $this->form->submit('Sign In', array('data-theme' => 'b', 'data-ajax' => 'false', 'class' => 'button_mobile', 'data-inline' => 'true'));?> 
<a href="javascript:;" data-inline="true" onclick="fblogin();return false;"><img src="/img/sign_in_fb.png" class="fr" style="margin-top:7px;"></a>
<?php echo $this->form->end();?>