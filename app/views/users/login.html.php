<?php if ($message){ echo $message; } ?>

<div id="fullscreen">
	
	<div id="login-box">
	
		<div id="login-box-border" class="login-modal">
			
			<div id="login-box-container">
				
				<div class="tt">
					<div></div>
				</div>			
				
				<div class="tm">
					<div class="ti">		
					
						<div class="tc login-inner">
						
							<h1 id="logo">Totsy</h1>
							
							<div id="intro-copy">
							
								<p class="red">You have places to be, things to do, and little ones in tow.</p>
								
								<p>At Totsy, moms on the go and moms to be experience the shopping they desire at prices they can't resist.</p>
							
							</div>
							
							<div class="sign-in-container">
								<div id="sign-in-box" class="r-container">
									<div class="tl"></div>
									<div class="tr"></div>
									<div class="r-box lt-gradient-1">
										<h2>Member Sign In</h2>
										
										<?=$this->form->create(null,array('id'=>'loginForm'));?>
										<?=$this->form->field('email', array('class'=>"validate['required']  inputbox", 'id'=>"email"));?>
										<?=$this->form->field('password', array(
												'class'=>"validate['required'] inputbox", 
												//'name' => 'password', 
												'id' => 'password',
												'type' => 'password'));?>
										<!-- <?=$this->form->field('remember', array('id'=>"remember", 'type'=>"checkbox", 'class'=>"fl checkbox"));?> -->
										
										<?=$this->html->link('Forgot your password?','/reset', array('class'=>"md", 'title'=>"Forgot your password?"))?>
										<br />
										<?=$this->form->submit('Login', array('id'=>"login_button"));?>
										<?=$this->form->end();?>
										
									</div>
									<div class="bl"></div>
									<div class="br"></div>
								</div>
							</div>
							
							<div class="register-container">
								<div id="register-box" class="r-container">
									<div class="tl"></div>
									<div class="tr"></div>
									<div class="r-box lt-gradient-1">
										<h2>Become a Member</h2>
										
										<p>Become a member today for access to brand-specific sales, up to 70% off retail, just for you and the kids, ages 0-7. Prenatal care products, baby gear, travel accessories, bedding and bath, children's clothing, toys, and educational materials &mdash; and that's just the start.</p>
										<?=$this->html->link('Request Membership','/register', array('id' => 'register_button')); ?>
									</div>
									<div class="bl"><!-- --></div>
									<div class="br"><!-- --></div>
								
								</div>
							</div>
							
							<p class="login-sig clear">Exclusive access, Top brands. Great deals. <span class="red">The savvy mom shops at Totsy.com</span></p>
							
						</div>
					
					</div>
				</div>

				<div class="tb">
					<div></div>
				</div>	
				
			</div>
			
		</div>
		
	</div>
		
</div>

<div id="footer">

	<ul>
		<li class="first"><a href="/pages/terms" title="Terms of Use">Terms of Use</a></li>
		<li><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
		<li><a href="/pages/aboutus" title="About Us">About Us</a></li>
		<li><a href="/pages/faq" title="FAQ">FAQ</a></li>
		<li class="last"><a href="/pages/contact" title="Contact Us">Contact Us</a></li>
	</ul>
	
	<span id="copyright">&copy; 2010 Totsy.com. All Rights Reserved.</span>

</div>