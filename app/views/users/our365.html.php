<div id="fullscreen" class="our365-login">
	<div id="login-box">
		<div id="login-box-border" class="register-modal">
			<div id="login-box-container">
				<div class="tt">
					<div><!-- --></div>
				</div>
				
				<div class="tm">
					
					<div class="ti">		
						
						<div class="tc login-inner register-inner">
							
							<h1 id="our365-logo">Our365 and Totsy</h1>
							
							<div id="intro-copy" class="red">
								You're one step away from entering this private sale at Totsy, where moms get insider deals on baby and kid stuff.
							</div>
							
							<div class="message">
								<?php if($message){echo "$message"; } ?>
							</div>
							
							<div class="r-container clear our365-promo">
								<div class="tl"></div>
								<div class="tr"></div>
								<div class="r-box lt-gradient-1">
									<p class="promo-copy"><strong>Have you heard the buzz about online private sample sales?</strong><br />Now they're for babies, too. You're invited to a free private club. With private online sales every week of the year, Totsy offers its members sales on high end brands for baby and moms at 40-70% off." Savings of up to 90% off retail.<br />
									<strong>Membership is free</strong>
									</p>
								</div>
								<div class="bl"></div>
								<div class="br"></div>
							</div>

							
							<div class="our365-register-container">
								<?=$this->form->create($user ,array('id'=>'registerForm')); ?>

									<div class="form-row fl form-50-row">
										<?=$this->form->label('firstname', 'First Name <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('firstname', array('class' => 'inputbox')); ?>
									</div>
									<div class="form-row fl form-50-row">
										<?=$this->form->label('lastname', 'Last Name <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('lastname', array('class' => 'inputbox')); ?>
									</div>
									<div class="form-row fl form-50-row">
										<?=$this->form->label('email', 'Email <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											)); 
										?>
										<?=$this->form->text('email', array('class' => 'inputbox')); ?> 
									</div>
									<div class="form-row fl form-50-row">
										<?=$this->form->label('confirmemail', 'Confirm Email <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('confirmemail', array('class' => 'inputbox')); ?>
									</div>

									<div class="form-row clear">
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
									<?=$this->form->error('firstname'); ?>
									<?=$this->form->error('lastname'); ?>
									<?=$this->form->error('email'); ?>
									<?=$this->form->error('confirmemail'); ?>
									<?=$this->form->error('emailcheck'); ?>
									<?=$this->form->error('password'); ?>
									</div>
									<div class="form-row_">
									<?=$this->form->checkbox('terms', array("checked" => "checked", 'style'=>"float:left;margin-right:4px; display: none;"));?>
									</div>
									<span class="sm reg-tos" style="overflow:visible!important;">
											By clicking register you accept our 
											<?=$this->html->link('Terms and Conditions','pages/terms')?>.
									</span>
										<?=$this->form->submit('Register', array('class' => 'register_button_grn')); ?>
										<?=$this->form->error('terms'); ?>
									</div>
								<?=$this->form->end(); ?>
									
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


<div id="footer">

	<ul>
		<li class="first"><a href="/pages/terms" title="Terms of Use">Terms of Use</a></li>
		<li><a href="/pages/privacy" title="Privacy Policy">Privacy Policy</a></li>
		<li><a href="/pages/aboutus" title="About Us">About Us</a></li>
		<li><a href="/blog" title="Blog">Blog</a></li>
		<li><a href="/pages/faq" title="FAQ">FAQ</a></li>
		<?php if (empty($userInfo)){ ?>
		<li><a href="/pages/contact" title="Contact Us">Contact Us</a></li>
		<li class="last"><a href="http://nytm.org/made" title="Made in NYC" target="_blank">Made in NYC</a></li>
		<?php } else { ?>
		<li><a href="/tickets/add" title="Contact Us">Contact Us</a></li>
		<li class="last"><a href="http://nytm.org/made" title="Made in NYC" target="_blank">Made in NYC</a></li>
		<?php } ?>
	</ul>
	
	<span id="copyright">&copy; 2011 Totsy.com. All Rights Reserved.</span>

</div>