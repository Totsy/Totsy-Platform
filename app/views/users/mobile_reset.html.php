<?php if ($success == false): ?>
<h2>Reset Password <span style="color:#999; font-size:12px; float:right; margin:5px 0px 0px 0px;"><a href="#" onclick="window.location.href='/login';return false;">Sign In</a></span></h2>
<hr />

<?php if ($message){ ?>
<?php echo $message; ?>
<?php } ?>



											<?php echo $this->form->create(null, array('id'=>'loginForm')); ?>
										
													
<?php echo $this->form->label('email', 'Email Address<span>*</span>', array('escape' => false,'class' => 'required')); ?>
<?php echo $this->form->email('email', array('class' => 'inputbox')); ?>
<?php echo $this->form->error('email'); ?>
												
													
													<?php echo $this->form->submit('Reset Password', array('class' => 'button')); ?>
										
											<?php echo $this->form->end(); ?>

							<?php endif ?>
