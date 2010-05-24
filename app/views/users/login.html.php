<?=$this->html->script('mootools-1.2.4-core-nc.js');?>
<?=$this->html->script('mootools-1.2.4.4-more.js');?>
<?=$this->html->script('formcheck.js');?>
<?=$this->html->script('en.js');?>
<?=$this->html->style('formcheck');?>

<script type="text/javascript">
    window.addEvent('domready', function(){
        new FormCheck('loginForm');
    });
</script>

<?php if ($message){ echo $message; } ?>
<?=$this->form->create('',array('id'=>'loginForm'));?>
<?=$this->form->field('email', array('class'=>"validate['required','email']"));?>
<?=$this->form->field('password', array(
		'class'=>"validate['required']", 
		'name' => 'password', 
		'id' => 'password',
		'type' => 'password'));?>
<?=$this->form->submit('Login');?>
<?=$this->form->end();?>
<?=$this->html->link('Request Membership','/register')?>
