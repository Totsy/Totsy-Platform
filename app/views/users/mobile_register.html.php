<h2>Sign Up <span style="color:#999; font-size:12px; float:right; margin:5px 0px 0px 0px;">Already a Totsy member? <a href="#" onclick="window.location.href='/login';return false;">Sign In</a></span></h2>
<hr />
<?php if ($message){ ?>
<?php echo $message; ?>
<?php } ?>
<form id="registerForm" method="post">

									<div>
										<?=$this->form->label('email', 'Email<span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('email', array('class' => 'inputbox')); ?>
										<?=$this->form->error('email'); ?>
									</div>
									<div>
										<?=$this->form->label('confirmemail', 'Confirm Email<span>*</span>', array(
											'escape' => false,
											'class' => 'required'
											));
										?>
										<?=$this->form->text('confirmemail', array('class' => 'inputbox')); ?>
										<?=$this->form->error('confirmemail'); ?>
										<?=$this->form->error('emailcheck'); ?>
									</div>

									<div>
									<?=$this->form->label('password','Password<span>*</span>', array(
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
									<div style="font-size:10px; margin:0px; text-align:center">
											By clicking register you accept our 
											<?=$this->html->link('Terms and Conditions','pages/terms')?>
									</div>
									<br>
									<?=$this->form->submit('Sign Up'); ?>
									<?=$this->form->error('terms'); ?>
									</div>
								<?=$this->form->end(); ?>
				