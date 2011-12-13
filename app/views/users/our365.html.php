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
								<?php echo $this->form->create($user ,array('id'=>'registerForm')); ?>

									<div class="form-row fl form-50-row">
										<?php echo $this->form->label('firstname', 'First Name <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?php echo $this->form->text('firstname', array('class' => 'inputbox')); ?>
									</div>
									<div class="form-row fl form-50-row">
										<?php echo $this->form->label('lastname', 'Last Name <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?php echo $this->form->text('lastname', array('class' => 'inputbox')); ?>
									</div>
									<div class="form-row fl form-50-row">
										<?php echo $this->form->label('email', 'Email <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											)); 
										?>
										<?php echo $this->form->text('email', array('class' => 'inputbox')); ?> 
									</div>
									<div class="form-row fl form-50-row">
										<?php echo $this->form->label('confirmemail', 'Confirm Email <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?php echo $this->form->text('confirmemail', array('class' => 'inputbox')); ?>
									</div>

									<div class="form-row clear">
									<?php echo $this->form->label('password','Password <span>*</span>', array(
										'class'=>'required',
										'escape' => false
										));
									?>
									<?php echo $this->form->password('password', array(
											'class'=>"inputbox",
											'name' => 'password', 
											'id' => 'password'));
									?>
									<?php echo $this->form->error('firstname'); ?>
									<?php echo $this->form->error('lastname'); ?>
									<?php echo $this->form->error('email'); ?>
									<?php echo $this->form->error('confirmemail'); ?>
									<?php echo $this->form->error('emailcheck'); ?>
									<?php echo $this->form->error('password'); ?>
									</div>
									<div class="form-row_">
									<?php echo $this->form->checkbox('terms', array("checked" => "checked", 'style'=>"float:left;margin-right:4px; display: none;"));?>
									</div>
									<span class="sm reg-tos" style="overflow:visible!important;">
											By clicking register you accept our 
											<?php echo $this->html->link('Terms and Conditions','pages/terms')?>.
									</span>
										<?php echo $this->form->submit('Register', array('class' => 'register_button_grn')); ?>
										<?php echo $this->form->error('terms'); ?>
									</div>
								<?php echo $this->form->end(); ?>
									
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
	<?php echo $this->view()->render(array('element' => 'footerNavPublic')); ?>
</div>

<!-- Google Code for Homepage Remarketing List -->
<script type="text/javascript">
/* <![CDATA[ */
	var google_conversion_id = 1019183989;
	var google_conversion_language = "en";
	var google_conversion_format = "3";
	var google_conversion_color = "666666";
	var google_conversion_label = "8xkfCIH8iwIQ9Yb-5QM";
	var google_conversion_value = 0;
/* ]]> */
</script>

<script type="text/javascript" src="https://www.googleadservices.com/pagead/conversion.js"></script>

<noscript>
	<div style="display:inline;">
		<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1019183989/?label=8xkfCIH8iwIQ9Yb-5QM&amp;guid=ON&amp;script=0"/>
	</div>
</noscript>
<!-- END OF Google Code for Homepage Remarketing List -->
