<?=$this->html->script('mootools-1.2.4-core-nc.js');?>
<?=$this->html->script('mootools-1.2.4.4-more.js');?>

<?=$this->html->script('moosizer.js');?>

<?=$this->html->script('formcheck.js');?>
<?=$this->html->script('en.js');?>
<?=$this->html->style('formcheck');?>

<script type="text/javascript">
    window.addEvent('domready', function(){
        
        new FormCheck('loginForm');
        
        moosizer = new mooSizer({ bgElement:'bg' });
        
    });
    
</script>

<?php if ($message){ echo $message; } ?>


<div id="fullscreen">

	<div id="login-box">
		
		<div id="login-box-border">
		
			<div id="login-box-container">
			
				<h1 id="logo">Totsy</h1>
				
				<div id="intro-copy">
				
					<p class="red">You have places to be, things to do, and little ones in tow.</p>
					
					<p>At Totsy, moms on the go and moms to be experience the shopping they desire at prices they can't resist.</p>
				
				</div>
				
				<div id="sign-in-box">
					
					<h2>Member Sign In</h2>
					
					<?=$this->form->create(null,array('id'=>'loginForm'));?>
					<?=$this->form->field('email', array('class'=>"validate['required','email']"));?>
					<?=$this->form->field('password', array(
							'class'=>"validate['required']", 
							'name' => 'password', 
							'id' => 'password',
							'type' => 'password'));?>
					<?=$this->form->submit('Login');?>
					<?=$this->form->end();?>
					<?=$this->html->link('Request Membership','/register')?>
				
				</div>
				
				<div id="register-box">
				
					<h2>Become a Member</h2>
				
				</div>
			
				<p class="login-sig clear">Exclusive access, Top brands. Great deals. <span class="red">The savvy mom shops at Totsy.com</span></p>
			
			</div>
		
		</div>
		
	</div>
	
	<div id="bg">
		<img id="bg-img" class="activeslide" src="img/home-bg-1.jpg" alt="" />
	</div>
	
</div>

<div id="footer">

	<ul>
		<li class="first"><a href="#" title="Terms of Use">Terms of Use</a></li>
		<li><a href="#" title="Privacy Policy">Privacy Policy</a></li>
		<li><a href="#" title="About Us">About Us</a></li>
		<li><a href="#" title="FAQ">FAQ</a></li>
		<li class="last"><a href="#" title="Contact Us">Contact Us</a></li>
	</ul>
	
	<span id="copyright">&copy; 2010 Totsy.com. All Rights Reserved.</span>

</div>