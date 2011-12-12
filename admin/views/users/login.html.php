<?php if ($message){ echo $message; } ?>
<div class="grid_16"></div>
<div class="clear"></div>
<div class="grid_6 prefix_6">
	<div class="box">
		<h2>
			<a href="#" id="toggle-forms">Forms</a>
		</h2>
		<div class="block" id="forms">
			<fieldset class="login">
				<legend>Login</legend>
				<?php echo $this->form->create(null,array('id'=>'loginForm'));?>
				<p>
					<?php echo $this->form->label('Email:'); ?>
					<?php echo $this->form->text('email', array('id'=>"email"));?>
				</p>
				<p>
					<?php echo $this->form->label('Password:'); ?>
					<?php echo $this->form->password('password', array(
							'name' => 'password', 
							'id' => 'password',
							'type' => 'password'));?>
					<?php echo $this->form->submit('Login');?>
				</p>
				<?php echo $this->form->end();?>
			</fieldset>
		</div>
	</div>
</div>