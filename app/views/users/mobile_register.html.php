<?=$this->html->link('Already a Member?', '/login');?>
<div class="message">
								<?php if($message){echo "$message"; } ?>
							</div>

                                <h2 style="margin:0px; font-size:18px;">Register</h2>
								<hr />
								 
<?php
	 if (preg_match('/join/',$_SERVER['REQUEST_URI'])) {
print '<form id="registerForm" method="post" onsubmit="_gaq.push([\'_trackPageview\', \'/vpv/join\']);">';
	 } else {
print '<form id="registerForm" method="post" onsubmit="_gaq.push([\'_trackPageview\', \'/vpv/register\']);">';
	 }
?>
	<!-- div class="form-row">
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
									</div -->

									<!-- ************************************************************** -->
									<div>
										<?=$this->form->label('email', 'Email <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('email', array('class' => 'inputbox')); ?>
										<?=$this->form->error('email'); ?>
									</div>
									<div>
										<?=$this->form->label('confirmemail', 'Confirm Email <span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('confirmemail', array('class' => 'inputbox')); ?>
										<?=$this->form->error('confirmemail'); ?>
										<?=$this->form->error('emailcheck'); ?>
									</div>

									<div>
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
									<div>
									<?=$this->form->checkbox('terms', array("checked" => "checked", 'style'=>"float:left;margin-right:4px; display: none;"));?>
									</div>
									<span style="font-size:10px; margin:0px;">
											By clicking register you accept our 
											<?=$this->html->link('Terms and Conditions','pages/terms')?>.
									</span>
									<br>
									<?=$this->form->submit('Register', array('class' => 'button fr')); ?>
									<?=$this->form->error('terms'); ?>
									</div>
								<?=$this->form->end(); ?>
								

									<strong>Why you will love Totsy</strong>
									<ul>
										<li>Exclusive sales for kids, moms and families</li>
										<li>Sales last up to 3 days, plenty of time to shop.</li>
										<li>Savings of up to 90% off retail.</li>
										<li>A tree is planted for your first purchase.</li>
										<li>Membership is free</li>
										<li>We are 100% green.</li>
									</ul>
								</div>
				
							<p>Exclusive access, Top brands. Great deals. The savvy mom shops at Totsy.com</p>