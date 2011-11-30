<h2 style="margin:0px; font-size:18px;">Member Sign In</h2>
<hr />
<?php if ($message){ ?>
<div style="background:#ed1c24; color:#fff; text-shadow:none; font-size:14px; padding:5px;"> <?php echo $message; ?> </div> 
<?php } ?>
<?=$this->form->create(null,array('id'=>'loginForm'));?>
<?=$this->form->field('email', array('class'=>"validate['required']  inputbox", 'id'=>"email"));?>
<?=$this->form->field('password', array('class'=>"validate['required'] inputbox",
//'name' => 'password',
'id' => 'password', 'type' => 'password'));?>
<?=$this->form->submit('Sign In', array('data-theme' => 'b', 'data-inline' => 'true', 'data-ajax' => 'false'));?> 
<?=$this->form->end();?>
<div style="clear:both;"></div>
<p style='margin-top: 10px'>
 <?=$this->html->link('Forgot your password?','#', array('style' => 'font-size:12px; color:#ed1c24;', 'title'=>"Forgot your password?", 'onclick' => 'window.location.href="/reset";return false;'))?> </p>