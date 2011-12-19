<?php if ($success == false): ?>
<h2>Reset Password <span style="color:#999; font-size:12px; float:right; margin:5px 0px 0px 0px;"><a href="#" onclick="window.location.href='/login';return false;">Sign In</a></span></h2>
<hr />

<?php if ($message){ ?>
<?php echo $message; ?>
<?php } ?>



											<?=$this->form->create(null, array('id'=>'loginForm')); ?>
										
													
<?=$this->form->label('email', 'Email Address<span>*</span>', array('escape' => false,'class' => 'required')); ?>
<?=$this->form->text('email', array('class' => 'inputbox')); ?>
<?=$this->form->error('email'); ?>
												
													
													<?=$this->form->submit('Reset Password', array('class' => 'button')); ?>
										
											<?=$this->form->end(); ?>

							<?php endif ?>
