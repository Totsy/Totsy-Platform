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
									<p class="promo-copy"><strong>Have you heard the buzz about online private sample sales?</strong><br />
									Now they're for babies, too. You're invited to a free private club. With private online sales every week of the year, Totsy offers its members sales on high end brands for baby and moms at 40-70% off." Savings of up to 70% off retail.<br />
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
										<?=$this->form->error('firstname'); ?>
									</div>
									<div class="form-row fl form-50-row">
										<?=$this->form->label('lastname', 'Last Name <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('lastname', array('class' => 'inputbox')); ?>
										<?=$this->form->error('lastname'); ?>
									</div>
									<div class="form-row fl form-50-row">
										<?=$this->form->label('email', 'Email <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											)); 
										?>
										<?=$this->form->text('email', array('class' => 'inputbox')); ?> 
										<?=$this->form->error('email'); ?>
									</div>
									<div class="form-row fl form-50-row">
										<?=$this->form->label('confirmemail', 'Confirm Email <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('confirmemail', array('class' => 'inputbox')); ?>
										<?=$this->form->error('confirmemail'); ?>
										<?=$this->form->error('emailcheck'); ?>
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
									<?=$this->form->error('password'); ?>
									</div>
									<div class="form-row t-and-c">
										<?=$this->form->checkbox('terms', array('class'=>"", 'style'=>"float:left;margin-right:4px"));?>

										<span class="reg-tos">
												By requesting membership, I accept the 
											<?=$this->html->link('Terms and Conditions','#')?> 
											of Totsy, and accept to receive sale email newsletters. 
											Totsy will never sell or give my email to any outside party.
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
