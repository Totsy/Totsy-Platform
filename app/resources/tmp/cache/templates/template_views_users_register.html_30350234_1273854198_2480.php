<script type="text/javascript">

window.addEvent('domready', function(){equalHeights('.col');});

$(document).ready(function(){
  $("#registerForm").validate();
});


</script>

<div class="modal-top">
<h1 class="white"><?php echo 'Register with Totsy';?></h1>
</div>
<div class="reg-left logo-bot col">
		<?php echo $this->html->image('register-img.jpg', array('class'=>'reg-img', 'title'=>'Register with Totsy', 'alt'=>'Regsiter with Totsy')); ?>
	<div class="reg-copy">		
		<h2><?php echo ('Join Totsy');?></h2>
		<p>the leading source for today's top kids designers at up to 70% off retail. Gain access to brands for child, boys, girls at insider prices.</p>	
		<p><?php echo ('Exclusive Access. Top Brands. Great Deals.');?><br />
		<span class="red lg"><?php echo ('The savvy mom shops at Totsy.com.');?></span>
		</p>		
		<blockquote>
			"People with a taste for high-end green fashion items at deep discount have said yes to Totsy addiction."<br />
			<strong>The New York Times</strong>
		</blockquote>		
		<blockquote>
			"The online shopping phenomenon."<br />
			<strong>Vogue</strong>
		</blockquote>		
		<a href="#" title="Sign In"><?php echo ('Already a member? Sign in here.');?></a>		
	</div>
</div>
<div class="reg-right col">
	<div class="reg-copy">		
			<fieldset>				
				<?php echo $this->form->create('',array('id'=>'registerForm')); ?>
					<?php echo $this->form->label('fname','First Name',array('class'=>'label')); ?>
					<?php echo $this->form->text('firstname', array('class'=>'required')); ?>
					<?php echo $this->form->label('lname','Last Name',array('class'=>'label')); ?>
					<?php echo $this->form->text('lastname', array('class'=>'required')); ?>
					<?php echo $this->form->label('username','User Name',array('class'=>'label')); ?>
					<?php echo $this->form->text('username', array('class'=>'required')); ?>
					<?php echo $this->form->label('password','Password',array('class'=>'label')); ?>
					<?php echo $this->form->password('password', array('class'=>'required')); ?>
					<?php echo $this->form->label('','',array('class'=>'clear block sm')); ?>
					<?php echo $this->form->checkbox('terms', array('class'=>'required')); ?>
					<span>I agree to the <?php echo $this->html->link('terms of service','#'); ?></span>
					<button type="submit" name="submit" id="submit-btn" class="btn reg-btn"><?php echo 'Register';?></button>				
				<?php echo $this->form->end(); ?>
				<span class="sm">We value your <a href="#" title="Privacy Policy">privacy</a>. Totsy will not sell or rent your email address to third parties.</span>
			</fieldset>	
	</div>
</div>