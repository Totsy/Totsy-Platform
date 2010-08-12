<div id="fullscreen" style="background:#FF3300">
	<div id="login-box">
		<div id="login-box-border">
			<div id="login-box-container">
				<div class="tt">
					<div><!-- --></div>
				</div>
				
				<div class="tm">
					
					<div class="ti">		
						
						<div class="tc login-inner register-inner">
							
							<h1 id="logo">Totsy</h1>
							
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
										<li>Savings of up to 70% off retail.</li>
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
								
								<?=$this->form->create(null,array('id'=>'registerForm')); ?>
									<div class="form-row">
										<?=$this->form->label('fname','First Name',array('class'=>'label'));?>&nbsp;
										<?=$this->form->text('firstname', array('class'=>"inputbox",'id'=>"fname"));?>
										<?=$this->form->label('lname','Last Name',array('class'=>'label'));?>&nbsp;
										<?=$this->form->text('lastname', array('class'=>"inputbox",'id'=>"lname"));?>
									</div>

									<div class="form-row">
									<?=$this->form->label('email','Email',array('class'=>'label'));?>&nbsp;
									<?=$this->form->text('email', array('class'=>"inputbox",'id'=>"email"));?>
									<?=$this->form->label('confirmemail','Confirm email',array('class'=>'label'));?>&nbsp;
									<?=$this->form->text('confirmemail', array('class'=>"inputbox",'id'=>"confirmemail"));?>
									</div>

									<div class="form-row">
									<?=$this->form->label('password','Password',array('class'=>'label'));?>
									<?=$this->form->password('password', array(
											'class'=>"inputbox", 
											'name' => 'password', 
											'id' => 'password'));?>
									</div>
									<div class="submit-row">
										<?=$this->form->checkbox('terms', array('class'=>"", 'style'=>"float:left;margin-right:4px"));?>
										<span class="sm reg-tos">
												By requesting membership, I accept the 
											<?=$this->html->link('Terms and Conditions','#')?> 
											of Totsy, and accept to receive sale email newsletters. 
											Totsy will never sell or give my email to any outside party.
										</span>
										<?=$this->form->submit('Register', array('class' => 'flex-btn-2')); ?>
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
