<?php if ($success == false): ?>
<h2 style="margin:0px; font-size:18px;">Forgot your password?</h2>
<hr />
<?php if (!empty($message)): ?>
<div id='message' class="cart-message"><?=$message;?> <a href="/" class="md" title="Return to Totsy">Return to Totsy</a></div>
<?php endif ?>

											<?=$this->form->create(null, array('id'=>'loginForm')); ?>
										
													
<?=$this->form->label('email', 'Email Address <span>*</span>', array('escape' => false,'class' => 'required')); ?>
<?=$this->form->text('email', array('class' => 'inputbox')); ?>
<?=$this->form->error('email'); ?>
												
													
													<?=$this->form->submit('Reset Password', array('class' => 'button')); ?>
										
											<?=$this->form->end(); ?>

							<?php endif ?>
