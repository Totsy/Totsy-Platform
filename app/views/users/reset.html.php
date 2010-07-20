<?php if (!empty($message)): ?>
	<?=$message;?>
<?php endif ?>
<?=$this->form->create(); ?>
	<div><p>Please enter your email address and a reminder password will be sent to you.</p></div>
	<?=$this->form->text('email', array('label' => 'Email Address')); ?>
	<?=$this->form->submit('Submit'); ?>
<?=$this->form->end(); ?>