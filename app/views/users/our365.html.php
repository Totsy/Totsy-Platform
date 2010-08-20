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
							
							<h1 id="logo-our365">Totsy</h1>
							
							<div id="intro-copy">

							</div>
							
							<div class="message">
								<?php if($message){echo "$message"; } ?>
							</div>
							
							<div class="r-container clear reg-list">
								<div class="tl"></div>
								<div class="tr"></div>
								<div class="r-box lt-gradient-1">

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
											<?=$this->html->link('Terms and Conditions','#')?> 
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
