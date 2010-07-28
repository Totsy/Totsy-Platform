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

<h1 id="logo">Totsy</h1>

<div class="sign-in-container">
	<div id="sign-in-box" class="r-container">
		<div class="tl"></div>
		<div class="tr"></div>
		<div class="r-box lt-gradient-1">
			<h2>Admin Sign In</h2>
			
			<?=$this->form->create(null,array('id'=>'loginForm'));?>
			<?=$this->form->field('email', array('class'=>"validate['required']  inputbox", 'id'=>"email"));?>
			<?=$this->form->field('password', array(
					'class'=>"validate['required'] inputbox", 
					'name' => 'password', 
					'id' => 'password',
					'type' => 'password'));?>
			<?=$this->form->field('remember', array('id'=>"remember", 'type'=>"checkbox", 'class'=>"fl checkbox"));?>
			<br />
			<?=$this->form->submit('Login', array('class'=>"flex-btn-2"));?>
			<?=$this->form->end();?>
			
		</div>
		<div class="bl"></div>
		<div class="br"></div>
	</div>
</div>
