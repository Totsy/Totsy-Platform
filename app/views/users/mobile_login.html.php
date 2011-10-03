<?php if ($message){ echo $message; } ?>
<h2>Member Sign In</h2>
<?=$this->form->create(null,array('id'=>'loginForm'));?>
<?=$this->form->field('email', array('class'=>"validate['required']  inputbox", 'id'=>"email"));?>
<?=$this->form->field('password', array('class'=>"validate['required'] inputbox",
//'name' => 'password',
'id' => 'password', 'type' => 'password'));?>
<?=$this->form->checkbox('remember_me', array('class' => 'fl checkbox')); ?> Remember Me <br/>
<?=$this->form->submit('Sign In', array('class'=>"button fr"));?> 
<?=$this->form->end();?>
<div style="clear:both;"></div>
<p style='margin-top: 10px'> <?=$this->html->link('Forgot your password?','/reset', array('class'=>"md", 'title'=>"Forgot your password?"))?> </p>