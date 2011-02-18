<?=$this->html->script('jquery.maskedinput-1.2.2')?>
<div id="fullscreen">
	<div id="login-box">
		<div id="login-box-border" class="register-modal">
			<div id="login-box-container">
				<div class="tt">
					<div><!-- --></div>
				</div>
				
				<div class="tm">
					
					<div class="ti">		
						
						<div class="tc login-inner register-inner">
							
							<div id='logo'>
                            <h1>
                              <a href='/' title="Totsy.com">
                                Totsy</a>
                              </h1>
                            </div>

							
							<div id="intro-copy">
								<h2 style="margin-top:30px"><span>Become a</span> MEMBER</h2>
							</div>
							
							<div class="message">
								<?php if($message){echo "$message"; } ?>
							</div>
							
							<div class="r-container clear reg-list">
								<div class="tl"></div>
								<div class="tr"></div>
								<div class="r-box lt-gradient-1">
									<strong class="red">Why you will love Totsy</strong>
									<ul class="bugs columns-2">
										<li>Exclusive sales for moms, children &amp; babies.</li>
										<li>Sales last up to 3 days, plenty of time to shop.</li>
										<li>Savings of up to 80% off retail.</li>
										<li>For every purchase, one tree is planted.</li>
										<li>Membership is free</li>
										<li>We are 100% green.</li>
									</ul>
								</div>
								<div class="bl"></div>
								<div class="br"></div>
							</div>

							<div id="" class="r-container clear">
								<div class="tl"></div>
								<div class="tr"></div>
								<div class="r-box lt-gradient-1">
								
								<?=$this->form->create($user ,array('id'=>'registerForm')); ?>

									<div class="form-row">
										<?=$this->form->label('firstname', 'First Name <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('firstname', array('class' => 'inputbox')); ?>
										<?=$this->form->error('firstname'); ?>
									</div>
									<div class="form-row">
										<?=$this->form->label('lastname', 'Last Name <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('lastname', array('class' => 'inputbox')); ?>
										<?=$this->form->error('lastname'); ?>
									</div>
							<div class="form-row">
										<?=$this->form->label('zip', 'Zip Code <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										
										<?=$this->form->text('zip', array('class' => 'inputbox', 'id' => 'zip')); ?>
										<?=$this->form->error('zip'); ?>
									</div>
									<div class="form-row">
										<?=$this->form->label('email', 'Email <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											)); 
										?>
										<?=$this->form->text('email', array('class' => 'inputbox')); ?> 
										<?=$this->form->error('email'); ?>
									</div>
									<div class="form-row">
										<?=$this->form->label('confirmemail', 'Confirm Email <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('confirmemail', array('class' => 'inputbox')); ?>
										<?=$this->form->error('confirmemail'); ?>
										<?=$this->form->error('emailcheck'); ?>
									</div>

									<div class="form-row">
									<?=$this->form->label('password','Password <span>*</span>', array(
										'class'=>'required',
										'escape' => false
										));
									?>
									<?=$this->form->password('password', array(
											'class'=>"inputbox",
											'name' => 'password', 
											'id' => 'password'));
									?>
									<?=$this->form->error('password'); ?>
									</div>
									<div class="form-row">
										<?=$this->form->checkbox('terms', array('class'=>"", 'style'=>"float:left;margin-right:4px"));?>

										<span class="sm reg-tos">
												By requesting membership, I accept the 
											<?=$this->html->link('Terms and Conditions','pages/terms')?> 
											of Totsy, and accept to receive sale email newsletters. 
											Totsy will never sell or give my email to any outside party.
										</span>
										<?=$this->form->submit('Register', array('class' => 'register_button')); ?>
										<?=$this->form->error('terms'); ?>
									</div>
								<?=$this->form->end(); ?>
									
								</div>
								<div class="bl"></div>
								<div class="br"></div>
							</div>
							
						</div>
					</div>
				</div>
				
				<div class="tb">
					<div><!-- --></div>
				</div>
			
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery(function($){
   $("#zip").mask("99999");
});
</script>
<div id="footer">

	<ul>
		<li class="first"><a href="/pages/terms" title="Terms of Use">Terms of Use</a></li>
		<li><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
		<li><a href="/pages/aboutus" title="About Us">About Us</a></li>
		<li><a href="/blog" title="Blog">Blog</a></li>
		<li><a href="/pages/faq" title="FAQ">FAQ</a></li>
		<li class="last"><a href="/pages/contact" title="Contact Us">Contact Us</a></li>
	</ul>
	
	<span id="copyright">&copy; 2010 Totsy.com. All Rights Reserved.</span>

</div>
