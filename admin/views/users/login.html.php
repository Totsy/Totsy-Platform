<?php if ($message){ echo $message; } ?>
<div class="grid_16"></div>
<div class="clear"></div>
<div class="grid_6 prefix_6">
	<div class="box">
		<h2>
			<a href="#" id="toggle-forms">Totsy Admin Interface</a>
		</h2>
		<div class="block" id="forms">
			<fieldset class="login">
				<legend>Login</legend>
				<?=$this->form->create(null,array('id'=>'loginForm'));?>
				<p>
					<?=$this->form->label('Email:'); ?>
					<?=$this->form->text('email', array('id'=>"email"));?>
				</p>
				<p>
					<?=$this->form->label('Password:'); ?>
					<?=$this->form->password('password', array(
							'name' => 'password', 
							'id' => 'password',
							'type' => 'password'));?>
					<?=$this->form->submit('Login');?>
				</p>
				<?=$this->form->end();?>
			</fieldset>
		</div>
	</div>
</div>