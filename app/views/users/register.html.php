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
		<?=$this->html->image('register-img.jpg', array('class'=>'reg-img', 'title'=>'Register with Totsy', 'alt'=>'Regsiter with Totsy'));?>
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
		<?=$this->html->link('Already a member? Sign in here', '/login', array('title'=>'Sign In'))?>			
	</div>
</div>
<div class="reg-right col">
	<div class="reg-copy">
	<div class="message">
		<?php if($message){echo "$message"; } ?>
	</div>
			<fieldset>				
				<?=$this->form->create('',array('id'=>'registerForm')); ?>
					<?=$this->form->label('fname','First Name',array('class'=>'label'));?>
					<?=$this->form->text('firstname', array('class'=>'required'));?>
					<?=$this->form->label('lname','Last Name',array('class'=>'label'));?>
					<?=$this->form->text('lastname', array('class'=>'required'));?>
					<?=$this->form->label('email','Email Address',array('class'=>'label'));?>
					<?=$this->form->text('email', array('class'=>'required'));?>
					<?=$this->form->label('password','Password',array('class'=>'label'));?>
					<?=$this->form->password('password', array('class'=>'required'));?>
					<?=$this->form->label('','',array('class'=>'clear block sm'));?>
					<?=$this->form->checkbox('terms', array('class'=>'required'));?>
					<span>I agree to the <?=$this->html->link('terms of service','#')?></span>
					<button type="submit" name="submit" id="submit-btn" class="btn reg-btn"><?php echo 'Register';?></button>				
				<?=$this->form->end(); ?>
				<span class="sm">We value your <a href="#" title="Privacy Policy">privacy</a>. Totsy will not sell or rent your email address to third parties.</span>
			</fieldset>	
	</div>
</div>